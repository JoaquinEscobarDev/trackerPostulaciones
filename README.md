# Tracker de Postulaciones

Herramienta personal para ordenar postulaciones laborales: vista Kanban con
drag & drop, recordatorios automáticos de seguimiento y exportación a
CSV/PDF. Hecha con Laravel + Livewire, corriendo 100% en Docker (Laravel
Sail) para tener un entorno de desarrollo reproducible, igual al de un
equipo real.

## Stack

- **Laravel 13** (PHP 8.5)
- **Livewire 3** (+ Volt para las páginas de auth de Breeze) — interactividad
  sin escribir una SPA aparte
- **Laravel Breeze** — auth (login, registro, recuperación de contraseña)
- **MySQL 8.4**, **Redis** y **Mailpit** vía Docker Compose (Laravel Sail)
- **Tailwind CSS**
- **SortableJS** (+ Alpine.js) para el drag & drop del Kanban
- **barryvdh/laravel-dompdf** para exportar a PDF

## Por qué estas decisiones

- **Livewire para el Kanban, no Alpine puro**: el estado (qué postulación
  está en qué columna) vive en el servidor y hay que persistirlo en la BD
  en cada movimiento. Livewire maneja ese round-trip y el re-render sin
  escribir una API REST aparte. Alpine se usa igual, pero solo para el
  detalle de UI que no necesita servidor: inicializar SortableJS y togglear
  el modal.
- **Jobs en vez de enviar el email directo desde el comando**: el comando
  Artisan solo decide *qué* postulaciones necesitan recordatorio y encola
  un job por cada una. El envío real (que depende de una red externa, SMTP)
  queda en un `Job` que corre en el worker de colas, con reintentos
  automáticos si falla, sin bloquear al comando ni al scheduler.
- **`recordatorio_enviado_en` en vez de reusar `updated_at`**: si solo
  mirara `updated_at`, el recordatorio se reenviaría todos los días una vez
  que la postulación quedara "vieja". Se guarda cuándo se mandó el último
  recordatorio y se compara contra `updated_at`: si el usuario edita la
  postulación después de eso, `updated_at` vuelve a ser más reciente y el
  recordatorio puede dispararse de nuevo más adelante.
- **Policy en vez de chequear `user_id` a mano en cada controller/Livewire
  component**: centraliza la regla "cada usuario solo ve/edita sus propias
  postulaciones" en un solo lugar (`PostulacionPolicy`), reutilizable desde
  el Kanban, el form y el job.

## Requisitos

Solo [Docker Desktop](https://www.docker.com/products/docker-desktop/). No
hace falta tener PHP, Composer ni MySQL instalados en la máquina — todo
corre en contenedores.

## Setup

```bash
git clone <repo>
cd trackerPostulacion
cp .env.example .env
docker compose build
docker compose up -d
```

Después, adentro del contenedor de la app:

```bash
./vendor/bin/sail composer install
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

La app queda en **http://localhost**. Mailpit (para ver los emails que
manda la app en desarrollo, incluidos los recordatorios) queda en
**http://localhost:8025**.

Usuario de prueba creado por el seeder: `test@example.com` / `password`.

## Correr el scheduler y el worker de colas en desarrollo

Los recordatorios automáticos dependen de dos procesos corriendo en
paralelo (además de `sail up`):

```bash
# Terminal 1: procesa los jobs encolados (envío de emails)
./vendor/bin/sail artisan queue:work

# Terminal 2: dispara las tareas programadas (revisa el reloj cada minuto)
./vendor/bin/sail artisan schedule:work
```

`schedule:work` es el equivalente en desarrollo del cron de producción:
simula que el cron llama a `schedule:run` cada minuto, así no hace falta
configurar nada en el SO para probar el scheduler localmente.

Para probar el recordatorio sin esperar al horario programado (todos los
días 09:00), se puede correr el comando a mano:

```bash
./vendor/bin/sail artisan postulaciones:recordatorios
```

El umbral de días sin seguimiento es configurable con la variable de
entorno `POSTULACIONES_DIAS_RECORDATORIO` (default: 7).

## Comandos útiles

```bash
./vendor/bin/sail artisan test        # tests
./vendor/bin/sail artisan tinker      # REPL
./vendor/bin/sail npm run dev         # Vite en modo watch
```
