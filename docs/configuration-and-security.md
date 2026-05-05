# Configuration & security

---

## Environment loading (`bootstrap/load-env.php`)

- Loads **`.env`** from repository root when **`load_env_file(BASE_PATH.'/.env')`** runs from **`bootstrap/app.php`**.
- **Does not override** variables already present in **`$_ENV`** (allows deployment platforms to inject secrets).
- Supports quoted values and Laravel-style sentinel strings (`true`, `false`, `null`, …) interpreted by **`env()`**.

**Practice:** Never commit **`.env`** with production secrets — commit **`.env.example`** only.

---

## Primary environment variables

Documented fully in **`.env.example`**. Highlights:

| Variable | Purpose |
|----------|---------|
| **`APP_DEBUG`** | Verbose errors when true — **false** in production |
| **`APP_ENV`** | Informative (`local`, `production`, …); consume if you extend telemetry |
| **`DB_*`** | MySQL connection |
| **`FREE_TODO_LIMIT`** | Maximum todos without subscription (**≥ 1**) |
| **`APP_SIMULATE_SUBSCRIPTION_CHECKOUT`** | Enables simulated subscription checkout |

---

## Sessions & cookies

Configured in **`App\Http\Kernel`**:

| Flag | Value | Rationale |
|------|-------|-----------|
| **HttpOnly** | `true` | Reduces XSS cookie theft surface |
| **Secure** | dynamic | Ensures cookie only over HTTPS when appropriate |
| **SameSite** | `Lax` | Balance CSRF resistance vs normal navigation |

Behind proxies (Render, etc.), ensure **`HTTPS`** / **`X-Forwarded-Proto`** reflects TLS so **`Secure`** behaves correctly.

---

## CSRF protection

Implemented in **`bootstrap/helpers.php`**:

- **`csrf_token()`** stores random token in **`$_SESSION['_csrf_token']`** per session.
- Forms POST **`_token`** → **`csrf_validate()`** uses **`hash_equals`** against session value.

**Rules:**

- Every **state-changing POST** should include **`<?= htmlspecialchars(csrf_token()) ?>`** (views already wrap via **`esc()`** helpers).
- **`logout`** uses POST specifically to pair with CSRF.

---

## Authentication security

| Topic | Implementation |
|-------|----------------|
| Password storage | **`password_hash`** default algo (`PASSWORD_DEFAULT`) |
| Verification | **`password_verify`** |
| Session fixation mitigation | **`session_regenerate_id(true)`** on **`SessionAuth::login()`** |

Password hashes live **only** in **`users.password`** — never echoed or logged.

---

## Output escaping

Views must escape dynamic strings:

```php
htmlspecialchars($s, ENT_QUOTES, 'UTF-8')
```

Project templates commonly wrap **`esc()`** helpers — **never trust raw DB strings** in HTML attributes or bodies.

---

## SQL injection posture

Repositories should use **`PDOStatement::prepare`** + bound parameters for **all** user-derived filters (todo list queries already bind **`user_id`** and dynamic **`LIKE`** needles).

Avoid string concatenation for identifiers unless strictly allow-listed.

---

## Subscription / billing realism

Simulated subscriptions **do not prove payment**. Treat **`user_subscriptions`** as **app-local entitlement**, not financial ledger.

---

## Security checklist before merging risky changes

- [ ] No secrets or `.env` committed.
- [ ] `APP_DEBUG` default safe for prod deployments.
- [ ] New POST routes validate CSRF.
- [ ] New queries parameterised.
- [ ] Outputs escaped in HTML contexts.
- [ ] Auth-sensitive redirects validated (`RequireAuthentication` patterns).
