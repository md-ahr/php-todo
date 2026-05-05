# Deployment & troubleshooting

**Live demo:** [https://php-todo-nfab.onrender.com](https://php-todo-nfab.onrender.com)

---

## Deployment topology (conceptual)

Typical production layout:

```
Internet → TLS terminator / CDN → PHP runtime (Apache + mod_php or php-fpm)
                ↘ Managed MySQL (same VPC / allowed IP / TLS as configured)
```

This codebase assumes **MySQL** — PostgreSQL would require driver + schema translation work.

---

## Docker production image (`Dockerfile`)

Multi-stage:

1. **`composer:2`** installs **`vendor/`** (`composer install --no-dev`).
2. **`php:8.2-apache`** + **`pdo_mysql`** extension.
3. **`APACHE_DOCUMENT_ROOT=/var/www/html/public`** — **only `public/` is web-facing**.
4. **`AllowOverride All`** + **`rewrite`** enabled for **`.htaccess`**.

Run container with **`DB_*`** env vars pointing at reachable MySQL; execute **`php database/migrate.php`** once against that database before serving traffic (job, SSH, one-off container — platform-dependent).

---

## Render.com notes

Common expectations:

1. **Web service** builds/runs Docker or native PHP buildpack — align with Dockerfile’s **`public`** docroot if platform asks for **publish directory**.
2. Provide **`DB_HOST`**, **`DB_PORT`**, **`DB_DATABASE`**, **`DB_USERNAME`**, **`DB_PASSWORD`** from Render MySQL add-on or external provider.
3. Set **`APP_DEBUG=false`** for production services.
4. Run **`php database/migrate.php`** after first deploy or schema change (Render shell / release command).

Trust proxy headers so **`Kernel`** marks cookies **`Secure`** correctly.

---

## Local MySQL via Compose (`docker-compose.yml`)

Provides **`mysql:8.0`** with persistent volume **`mysql_data`**.

- Host connections from laptop: **`127.0.0.1:3306`** (or **`DB_PORT`** override).
- Connections from sibling containers on **`php-todo`** network: host **`mysql`**.

Healthcheck waits until **`mysqladmin ping`** succeeds — wait before migrating.

See comments in **`docker-compose.yml`** for **`exec format error`** / architecture pinning guidance.

---

## PHP built-in server caveat

Command **`php -S localhost:8080 -t public`** serves files under **`public/`**, but **does not implement Apache rewrite rules**. Requests such as **`/todos`** may **404** unless you supply a **router script** that forwards non-existent paths to **`index.php`**.

Practical options:

- Run the **Docker Apache** image locally.
- Add a **`router.php`** (team convention) — document its usage here when introduced.

---

## Migrations on deploy

Always ensure **`database/schema.sql`** has been applied **before** relying on controllers:

```bash
php database/migrate.php
```

Symptoms when skipped:

- **`1146`** table doesn’t exist messages.
- Application hints referencing **`php database/migrate.php`**.

---

## Troubleshooting

### `SQLSTATE[HY000] [2006] MySQL server has gone away`

Meaning: TCP connection dropped between PHP and MySQL mid-request.

Typical causes:

- MySQL restarted / container recycled.
- Idle timeouts (**`wait_timeout`**) with very long-lived PHP workers (less common with PHP-FPM per-request model).
- Docker networking instability during startup — migrate **after** DB healthy.

Mitigations:

- Retry transient failures (`migrate.php` uses **`db_retry_once`**).
- Fix infra stability / timeouts.

See comments on **`App\Database\Connection`**.

### Blank pages / generic “Something went wrong.”

When **`APP_DEBUG=false`**, controllers swallow generic **`Throwable`** — toggle **`APP_DEBUG=true`** **locally only** to surface stack traces.

### CSRF errors (“Session expired — refresh…”)

Session cookie rejected, regenerated session, or missing **`_token`** field — verify form markup and HTTPS/proxy cookie behaviour.

### Subscribe shows disabled messaging

Check **`APP_SIMULATE_SUBSCRIPTION_CHECKOUT`** — must be **`true`** for simulated checkout unless you integrate payments externally.

---

## Backups & retention

Platform-managed MySQL typically provides snapshots — confirm retention policy outside this repo.

Because **`ON DELETE CASCADE`** links todos/subscriptions to users, accidental **`DELETE FROM users`** cascades widely — guard admin tooling accordingly.
