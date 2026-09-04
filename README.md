<div align="center">

# 📋 Tracker de Postulaciones

**Kanban de postulaciones laborales con recordatorios automáticos de seguimiento.**

[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-%5E8.3-777BB4?logo=php&logoColor=white)](https://php.net)
[![Livewire](https://img.shields.io/badge/Livewire-3-4E56A6?logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.4-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com)
[![Docker](https://img.shields.io/badge/Docker-Sail-2496ED?logo=docker&logoColor=white)](https://laravel.com/docs/sail)
[![Tests](https://img.shields.io/badge/tests-33%20passing-30C755?logo=pest&logoColor=white)](#comandos-útiles)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](https://opensource.org/licenses/MIT)

[🔗 Demo en vivo](https://trackerpostulaciones.com) · [Setup](#setup) · [Por qué estas decisiones](#por-qué-estas-decisiones) · [Deploy](#deploy)

</div>

---

Herramienta personal para ordenar postulaciones laborales: vista Kanban con
drag & drop, recordatorios automáticos de seguimiento y exportación a
CSV/PDF. Hecha con Laravel + Livewire, corriendo 100% en Docker (Laravel
Sail) para tener un entorno de desarrollo reproducible, igual al de un
equipo real.

## Funcionalidades

- 🔐 **Auth completa** — registro, login, recuperación de contraseña (Laravel Breeze + Livewire/Volt)
- 🗂️ **Kanban con drag & drop** — 4 columnas (Postulado, Entrevista, Oferta, Rechazado), arrastrar una tarjeta actualiza el estado en la base de datos al instante
- ✏️ **CRUD completo** — crear, editar, ver detalle y eliminar postulaciones, todo desde un modal
- 🔒 **Cada usuario ve solo lo suyo** — reforzado con una Policy, no solo con filtros en las consultas
- 📧 **Recordatorios automáticos** — si una postulación queda "Postulado" sin novedades por más de N días (configurable), se encola un email de seguimiento
- 📤 **Exportación** — historial completo a CSV y PDF
- ✅ **33 tests** cubriendo auth, CRUD y los límites de autorización entre usuarios

## Stack

| | |
|---|---|
| **Backend** | Laravel 13 (PHP 8.3+) |
| **Frontend interactivo** | Livewire 3 (+ Volt para las páginas de auth de Breeze) |
| **Auth** | Laravel Breeze |
| **Base de datos** | MySQL 8.4 |
| **Entorno dev** | Docker Compose vía Laravel Sail (MySQL, Redis, Mailpit) |
| **Estilos** | Tailwind CSS |
| **Drag & drop** | SortableJS + Alpine.js |
| **Export PDF** | barryvdh/laravel-dompdf |

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
git clone https://github.com/JoaquinEscobarDev/trackerPostulaciones.git
cd trackerPostulaciones
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

## Deploy

Corriendo en producción en [trackerpostulaciones.com](https://trackerpostulaciones.com),
en un hosting compartido (sin Docker). Ver [DEPLOY.md](DEPLOY.md) para el
paso a paso completo: document root, variables de entorno, y el scheduler
más la cola de jobs reemplazados por Cron Jobs cada minuto.
