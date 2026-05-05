# Onboarding checklist (new joiners)

Use this as a practical path to productivity. Adjust pacing to your team’s norms.

---

## Day 1 — Run it locally

1. Install **PHP 8.1+**, **Composer**, **Docker** (for MySQL).
2. Clone the repository and run `composer install`.
3. Copy `.env.example` to `.env`. Leave defaults if using Compose MySQL on `127.0.0.1`.
4. Start MySQL: `docker compose up -d`.
5. Apply schema: `php database/migrate.php`.
6. Serve the app from project root: `php -S localhost:8080 -t public`.
7. Register a user, create todos, visit `/subscribe` and simulate a subscription if `APP_SIMULATE_SUBSCRIPTION_CHECKOUT=true`.

**Done when:** You can register, log in, CRUD todos, and see subscription/quota behaviour.

---

## Day 2 — Trace one full request

1. Open `public/index.php` → `bootstrap/app.php`.
2. Follow `App\Http\Kernel::handle()` → `routes/web.php`.
3. Pick `/todos`: open `TodosController::index()` and note POST vs GET branching.
4. Open `TodoRepository` for SQL shapes.
5. Open `views/todos.view.php` for how data reaches HTML.

**Done when:** You can narrate “browser POST → route → controller → repository → redirect → GET render” without guessing.

---

## Day 3 — Data model ownership

1. Read `database/schema.sql` end-to-end.
2. Map tables to repositories (`UserRepository`, `TodoRepository`, `UserSubscriptionRepository`).
3. Understand foreign keys (`ON DELETE CASCADE`) — deleting a user removes todos and subscription rows.

**Done when:** You know which table backs each screen and where migrations are applied.

---

## Week 1 — Ready to contribute

1. Read [Architecture & runtime](architecture-and-runtime.md) and [Application modules](application-modules.md).
2. Read [Configuration & security](configuration-and-security.md) before touching auth or forms.
3. Run through [Deployment & troubleshooting](deployment-and-troubleshooting.md) if you deploy or support staging/production.

**Done when:** You can implement a small change (e.g. new validated field on todos) with CSRF preserved and migrations documented.

---

## Suggested first contributions

- Improve validation messages or accessibility on forms.
- Add an integration test harness (project currently has no PHPUnit suite — discuss with team before adding).
- Harden logging around PDO errors in production (`APP_DEBUG=false` behaviour).
