# PHP Todo — Technical documentation

Welcome. This folder is the **canonical reference** for how the application works, how to run it safely, and where to change behaviour. Read in order if you are new; jump by topic if you already know the basics.

**Production demo:** [https://php-todo-nfab.onrender.com](https://php-todo-nfab.onrender.com)

---

## Documentation map

| Document | Who it is for | Contents |
|----------|----------------|----------|
| [Onboarding checklist](onboarding-checklist.md) | New engineers (first days) | Ordered tasks to clone, run, explore, ship |
| [Architecture & runtime](architecture-and-runtime.md) | Everyone | Request lifecycle, bootstrap, routing, layers |
| [Application modules](application-modules.md) | Feature work | Auth, todos, subscriptions, profile — classes and flows |
| [Database & migrations](database-and-migrations.md) | Backend work | Schema, repositories, migration script |
| [Configuration & security](configuration-and-security.md) | DevOps / reviewers | Environment variables, sessions, CSRF, passwords |
| [Deployment & troubleshooting](deployment-and-troubleshooting.md) | Ops / on-call | Docker, Render, common failures |
| [HTTP routes reference](http-routes-reference.md) | Quick lookup | Path → controller map |
| [Views & UI conventions](views-and-conventions.md) | Frontend tweaks | Templates, Tailwind, forms, escaping |

---

## What this project is (one paragraph)

PHP Todo is a **minimal MVC-style PHP application** (not Laravel): one front controller, explicit routes, controllers that load views, repositories over PDO, and session-based authentication. Users manage todos with filters and pagination; an optional **simulated subscription** lifts a configurable free-tier cap on todo count. There is **no payment gateway** in the codebase — subscriptions are practice/demo only.

---

## Tech stack (snapshot)

| Layer | Choice |
|-------|--------|
| Language | PHP 8.1+ (Dockerfile targets 8.2) |
| HTTP | Apache + mod_rewrite (production image) or `php -S` locally |
| Database | MySQL 8.x, UTF-8 (`utf8mb4`) |
| Data access | PDO, prepared statements |
| Auth | PHP sessions + password hashing (`password_*`) |
| UI | PHP templates under `views/`, Tailwind via CDN in layout |
| Dependencies | Composer (PSR-4 autoload for `App\`) |

---

## Repository landmarks

```
bootstrap/          # app.php entry wiring, helpers, env loader
config/             # app.php, database.php (reads env)
database/           # schema.sql + migrate.php
docs/               # this documentation
public/             # web root: index.php + .htaccess
routes/web.php      # path → controller dispatch
app/
  Auth/             # session guard + helpers
  Database/         # PDO singleton Connection
  Http/Controllers/ # screen handlers
  Repositories/     # SQL boundaries
  Subscriptions/    # plans + quota rules
  Todos/            # list/filter state value object
  Validation/     # input validators
views/              # templates + components
```

---

## Where to change common things

| Goal | Likely location |
|------|------------------|
| Add a page | New controller method or class + case in `routes/web.php` + view under `views/` |
| Change DB queries | `app/Repositories/*.php` |
| Change free-tier limit | `FREE_TODO_LIMIT` in `.env` (`TodoQuota`) |
| Change subscription plans copy | `app/Subscriptions/SubscriptionPlans.php` + `views/subscribe.view.php` |
| Session / cookie behaviour | `app/Http/Kernel.php` |
| Global helpers | `bootstrap/helpers.php` |

---

## Questions?

If behaviour disagrees with these docs, treat **code + schema** as source of truth and update the relevant markdown file in the same pull request.
