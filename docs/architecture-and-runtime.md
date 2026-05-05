# Architecture & runtime

This document explains **how HTTP enters the application** and **how code is organised**, without repeating every class name.

---

## High-level architecture

The app follows a **thin front controller** pattern:

1. **Web server** serves only `public/` (Apache docroot or PHP built-in server `-t public`).
2. **`public/index.php`** loads **`bootstrap/app.php`**, which wires environment, autoloading, config, and runs the HTTP kernel.
3. **`App\Http\Kernel`** starts the PHP session and **`require`s `routes/web.php`**.
4. **`routes/web.php`** inspects the path and instantiates the matching **controller**.
5. Controllers call **repositories** (PDO), optional **domain/helpers**, then **`require` views** as PHP templates.

There is **no** Laravel router container, **no** ORM, **no** middleware pipeline beyond session bootstrap — behaviour is explicit and linear.

```
Browser
  → public/index.php
    → bootstrap/app.php (env, autoload, config)
      → Kernel::handle() (session_start)
        → routes/web.php (switch on path)
          → Controller::method()
            → Repository / Subscription logic
            → view_path('*.view.php')
```

---

## Bootstrap (`bootstrap/app.php`)

Responsibilities:

1. Defines **`BASE_PATH`** as the repository root (parent of `bootstrap/`).
2. Loads **`vendor/autoload.php`** when Composer dependencies exist.
3. Loads **`bootstrap/helpers.php`** if helper functions are missing (supports lightweight contexts).
4. Loads **`.env`** via **`bootstrap/load-env.php`** (`load_env_file`) into `$_ENV` / `putenv`.
5. Registers a **fallback autoloader** for classes under `App\` mapping to **`app/`** — unless Composer already loaded `App\Http\Kernel`.
6. Loads **`$GLOBALS['config']`** from **`config/app.php`** (`name`, `env`, `debug`).
7. Runs **`(new App\Http\Kernel())->handle()`**.

Composer’s `composer.json` declares PSR-4 `App\\` → `app/` — in practice both Composer autoload and the fallback resolve `App\*`.

---

## HTTP kernel (`app/Http/Kernel.php`)

The kernel’s job is minimal:

- Ensure **`session_start()`** has run exactly once per request.
- Set session cookie flags:
  - **`cookie_httponly`** `true`
  - **`cookie_secure`** when HTTPS or `X-Forwarded-Proto: https` (important behind TLS terminators such as Render)
  - **`cookie_samesite`** `Lax`

Then it **`require`s `routes/web.php`**. There is no separate router object.

---

## Routing (`routes/web.php`)

Routing is a **`switch`** on **`parse_url(..., PHP_URL_PATH)`**:

- Normalises empty/false paths to **`/`**.
- Maps fixed paths (`/`, `/todos`, `/login`, …) to **`new SomeController()->method()`**.
- Unknown paths → **`NotFoundController`**.

**Implications:**

- Adding a route means adding a **`case`** and a **`use`** import when needed.
- There are **no named routes** — redirects use literal paths (`Location: /todos`).
- **Trailing slashes**: paths are matched exactly as parsed; inconsistent slashes can 404 depending on server normalisation.

---

## Front controller & rewriting (`public/`)

- **`public/index.php`** is the single entry point for dynamic requests.
- **`.htaccess`** (Apache) rewrites non-file paths to **`index.php`** so `/todos` works.

When using **`php -S`**, built-in routing sends unknown paths to `index.php` only if you pass **`-t public`** **and** either run Router script or rely on router — **recommended**: some teams use `router.php`; here the README suggests `-t public` which routes existing files first — verify your PHP version behaviour for clean URLs.

---

## Layering conventions

| Layer | Responsibility | Typical location |
|-------|----------------|----------------|
| Controller | HTTP concerns: guards, validate CSRF, call repos, set flash, redirect, include view | `app/Http/Controllers/` |
| Repository | SQL + PDO only; returns arrays or primitives | `app/Repositories/` |
| Domain / value objects | Pure PHP rules/state without HTTP | `app/Todos/`, `app/Subscriptions/` |
| Validation | Static validators returning field errors | `app/Validation/` |
| Views | HTML presentation; escape output | `views/` |

**Preferred dependency direction:** Controller → Repository / Domain → PDO. Views should not open DB connections.

---

## Configuration access

- **Environment**: **`env()`** in **`bootstrap/helpers.php`** reads `$_ENV`, `$_SERVER`, then `getenv()` with Laravel-style casting for boolean-like strings.
- **Typed config arrays**: **`config/app.php`**, **`config/database.php`** return arrays; database config is consumed by **`App\Database\Connection`**.

---

## Database connection (`App\Database\Connection`)

- **Singleton PDO** per PHP process (`Connection::get()`).
- **`Connection::reset()`** clears the singleton — used by **`db_retry_once()`** helper after MySQL “gone away” errors during migrations or long-lived contexts.

See [Deployment & troubleshooting](deployment-and-troubleshooting.md) for operational notes.

---

## Helpers (`bootstrap/helpers.php`)

Important globals for onboarding:

| Helper | Role |
|--------|------|
| `env()` | Read configuration from environment |
| `base_path()`, `view_path()`, `config_path()` | Resolve filesystem paths |
| `db()` | Returns shared PDO (`Connection::get()`) |
| `db_retry_once(callable)` | One reconnect retry on MySQL 2006 |
| `csrf_token()`, `csrf_validate()` | Session-backed CSRF for POST forms |
| `auth_check()`, `auth_user()` | Delegates to `App\Auth\SessionAuth` |

---

## Error handling philosophy

- Controllers often **`catch (PDOException)`** and render degraded UI or flash messages.
- **`$GLOBALS['config']['debug']`** (from **`APP_DEBUG`**) toggles whether raw exception messages surface to users — **must be false in production**.
