# Deploy a Hostinger (hosting compartido)

Esta guía asume un plan Business/Premium de Hostinger (los que dan acceso
SSH y Cron Jobs vía hPanel). No hay Docker ni procesos persistentes en
hosting compartido, así que el scheduler y la cola de jobs se manejan con
**Cron Jobs** en vez de `sail up` / `schedule:work` / `queue:work` corriendo
todo el tiempo como en desarrollo.

La app ya está preparada para esto: `CACHE_STORE`, `SESSION_DRIVER` y
`QUEUE_CONNECTION` usan el driver `database` (no Redis), así que no hace
falta ningún servicio extra más allá de MySQL.

## 1. Subir el código

Hosting compartido no tiene Docker, así que el deploy es más simple que en
local: solo hace falta el código + `composer install` + `npm run build`.

Opción recomendada — Git:

1. Pushear este repo a GitHub (privado o público).
2. En hPanel → **Avanzado → Git**, conectar el repo y apuntar el deploy
   path a una carpeta *fuera* de `public_html` (por ejemplo
   `~/trackerPostulacion`), no directo a `public_html`.
3. Cada `git push` a `main` se puede volver a desplegar con el botón
   "Deploy" de esa sección, o configurando el webhook automático.

Si el plan no tiene esa opción, alternativa por SSH:

```bash
ssh usuario@tu-servidor.hostinger.com
cd ~
git clone <url-del-repo> trackerPostulacion
```

## 2. Apuntar el dominio a `public/`

Laravel sirve todo desde `public/`, nunca desde la raíz del proyecto. En
hPanel, al crear el sitio/dominio, hay que configurar su **document root**
para que apunte a `~/trackerPostulacion/public` en vez de `public_html`.
Si el plan no permite cambiar el document root, la alternativa es symlinkear:

```bash
rm -rf ~/public_html
ln -s ~/trackerPostulacion/public ~/public_html
```

## 3. Variables de entorno

Por SSH, dentro de `~/trackerPostulacion`:

```bash
cp .env.example .env
php artisan key:generate
```

Editar `.env` con los datos reales:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=<db que creaste en hPanel>
DB_USERNAME=<usuario mysql de hPanel>
DB_PASSWORD=<password>

# SMTP real: el que da Hostinger con el dominio, o un servicio externo
# (Mailgun, Resend, etc.) — Mailpit era solo para desarrollo.
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=noreply@tu-dominio.com
MAIL_PASSWORD=<password del correo>
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@tu-dominio.com

# Redis no está disponible en hosting compartido y no hace falta:
# cache/sesión/colas ya usan el driver "database".
```

## 4. Instalar dependencias y preparar la base de datos

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

`--force` es necesario porque `APP_ENV=production` bloquea `migrate` por
seguridad sin esa flag.

## 5. Assets del frontend

El servidor de hosting compartido normalmente no tiene Node instalado (o
no vale la pena instalarlo ahí). Lo más simple es compilar los assets
**en local** y subir la carpeta ya compilada:

```bash
# en tu máquina, dentro del proyecto
npm run build
```

Esto genera `public/build/` (está en `.gitignore`, así que hay que subirlo
aparte, por SFTP o agregándolo puntualmente al deploy — no lo saques del
gitignore para el repo normal).

## 6. Permisos

```bash
chmod -R 775 storage bootstrap/cache
```

## 7. Cron Jobs (reemplazan a `schedule:work` y `queue:work`)

En hPanel → **Avanzado → Cron Jobs**, agregar dos tareas, ambas cada
minuto:

```cron
* * * * * php /home/usuario/trackerPostulacion/artisan schedule:run >> /dev/null 2>&1

* * * * * php /home/usuario/trackerPostulacion/artisan queue:work --stop-when-empty --max-time=55 >> /dev/null 2>&1
```

- La primera es el reemplazo exacto de `schedule:work`: dispara
  `postulaciones:recordatorios` todos los días a las 09:00, tal como está
  configurado en `routes/console.php`.
- La segunda reemplaza a `queue:work` corriendo indefinidamente: cada
  minuto levanta un worker, procesa lo que haya en la cola (los emails de
  recordatorio) y se apaga solo antes de que el próximo cron lo pise.
  Preserva el comportamiento real de colas (reintentos, no bloquear el
  request) sin necesitar un proceso persistente, que hosting compartido no
  permite.

## 8. SSL

En hPanel → **Seguridad → SSL**, activar el certificado gratuito
(Let's Encrypt) para el dominio. Después de eso, `APP_URL` en `.env` debe
quedar en `https://`.

## 9. Verificar

- Entrar al dominio y confirmar que carga el login.
- Loguearse y ver el Kanban.
- Revisar en hPanel → Cron Jobs que las dos tareas corrieron sin error
  (o mirar `storage/logs/laravel.log` por SSH).
