# PHP Todo

A small practice application: register, sign in, manage todos with filters and pagination, optional subscription tiers (simulated checkout), and a profile area. Built as a minimal PHP 8 skeleton with Composer autoloading, PDO/MySQL, session auth, and Tailwind-style UI via the Tailwind browser CDN.

**Live demo:** [https://php-todo-nfab.onrender.com](https://php-todo-nfab.onrender.com)

## Documentation

Full onboarding and technical documentation for the team: **[docs/README.md](docs/README.md)**.

## Features

- Session-based authentication (register / login / logout)
- Todo list with priorities, completion toggle, search/filter, and paging
- Free-tier todo limit configurable via environment; “subscription” unlocks unlimited todos (practice mode — no real payments)
- Profile page with account details and optional cancellation of subscription access (demo behaviour)
- Apache-friendly front controller under `public/` with URL rewriting

## Requirements

- PHP **8.1+** (Docker image uses **8.2**)
- Composer **2.x**
- MySQL **8** (local Docker Compose file included)

## Quick start (local)

1. **Install PHP dependencies**

   ```bash
   cd php-todo
   composer install
   ```

   (Clone or copy the project first so this directory exists.)

2. **Environment**

   ```bash
   cp .env.example .env
   ```

   Adjust `DB_*` if needed. With Docker Compose MySQL below, defaults in `.env.example` usually work when PHP runs on the host (`DB_HOST=127.0.0.1`).

3. **Start MySQL**

   ```bash
   docker compose up -d
   ```

   Wait until the container is healthy, then apply the schema:

   ```bash
   php database/migrate.php
   ```

4. **Run the app**

   From the project root, serve the `public` directory:

   ```bash
   php -S localhost:8080 -t public
   ```

   Open [http://localhost:8080](http://localhost:8080).

### Docker image (Apache + PHP)

Build and run (supply database reachable from the container, e.g. hosted MySQL):

```bash
docker build -t php-todo .
docker run -p 8080:80 \
  -e DB_HOST=... -e DB_PORT=3306 -e DB_DATABASE=... \
  -e DB_USERNAME=... -e DB_PASSWORD=... \
  php-todo
```

Run migrations once against that database (from a shell with PHP and network access to MySQL), or execute `php database/migrate.php` in a one-off container with the same env.

## Configuration

| Variable | Purpose |
|----------|---------|
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | PDO MySQL connection |
| `APP_DEBUG` | Verbose errors when `true` |
| `FREE_TODO_LIMIT` | Max todos per user without an active subscription row |
| `APP_SIMULATE_SUBSCRIPTION_CHECKOUT` | When `true`, choosing a plan on `/subscribe` grants access immediately (no payment gateway) |

See `.env.example` for defaults and comments.

## Project layout

| Path | Role |
|------|------|
| `public/index.php` | Front controller |
| `bootstrap/app.php` | Autoload, env, config, HTTP kernel |
| `routes/web.php` | Path → controller dispatch |
| `app/Http/Controllers/` | Request handlers |
| `app/Repositories/` | Database access |
| `database/schema.sql` | Applied by `database/migrate.php` |
| `views/` | PHP templates |

## Deployment notes (Render)

The live instance matches the Docker-style deployment: provision **MySQL**, set `DB_*` and other env vars on the host, run `php database/migrate.php` once after deploy, and keep `APP_DEBUG=false` in production.

## License

MIT (see `composer.json`).
