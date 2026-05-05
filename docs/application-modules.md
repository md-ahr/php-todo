# Application modules

Feature-oriented guide: **what exists**, **which classes participate**, and **how user-visible flows work**.

---

## Authentication (`App\Auth`)

### Session model (`SessionAuth`)

- Session key: **`auth`** (`SessionAuth::SESSION_KEY`).
- Stored fields after login: **`user_id`**, **`email`**, **`name`** (password hash never enters session).
- **`login()`** calls **`session_regenerate_id(true)`** before writing auth payload — mitigates fixation on privilege change.
- **`logout()`** clears **`auth`** and **`intended_url`**, regenerates session ID.

### Helpers (`auth_check()`, `auth_user()`)

Thin wrappers around **`SessionAuth`** for controllers and views.

### Registration (`RegisterController` + `UserRepository`)

1. Validates input via **`RegisterValidator`**.
2. Normalises email (trim/lowercase pattern per repository usage).
3. **`password_hash(... PASSWORD_DEFAULT)`** persists to **`users`**.
4. **`SessionAuth::login()`** signs the user in immediately after registration.

### Login (`LoginController`)

1. Validates via **`LoginValidator`**.
2. **`UserRepository::findForAuthByEmail`** loads credentials.
3. **`password_verify`** on submit.
4. On success **`SessionAuth::login()`** and redirect:
   - If **`$_SESSION['intended_url']`** set (from guard), redirect there and clear it.
   - Else **`/`**.

### Logout (`LogoutController`)

- **POST only** (CSRF token required).
- Redirects to **`/`**.

### Guest guard (`RequireAuthentication::redirectToLoginIfGuest`)

Used by **`/todos`**, **`/subscribe`**, **`/profile`**:

1. If unauthenticated, saves requested path (+ query) into **`$_SESSION['intended_url']`** (sanitised — rejects scheme-relative URLs).
2. Redirects to **`/login`**.

---

## Todos (`TodosController`, `TodoRepository`, `TodoListState`)

### Authorisation

- **`TodosController::index()`** begins with **`RequireAuthentication::redirectToLoginIfGuest('/todos')`**.
- All queries scoped by **`user_id`** — users cannot read other users’ rows via normal flows.

### GET render path

1. Builds **`TodoListState`** from **`$_GET`** (`q`, `status`, `priority`, `page`, `per_page`) via **`TodoListState::fromGlobals()`**.
2. **`TodoRepository::countFiltered`** validates paging bounds; may **303 redirect** if page out of range after normalisation.
3. Loads aggregates for sidebar counts, quota assessment, paginated rows.
4. Optional **`SELECT VERSION()`** ping for footer DB indicator — failures degrade gracefully.

### POST command pattern

Posts include **`_token`** (CSRF), **`_action`** (`create`, `update`, `delete`, `toggle`), and **`state_*`** hidden fields preserving list filters (**`TodoListState::fromPostPrefixes`**).

Typical outcome: **`303`** redirect back to **`/todos?...`** with flash in **`$_SESSION['_todo_notice']`** (cleared next GET).

### Quota enforcement on create

Before **`TodoRepository::create`**:

1. **`TodoRepository::aggregateCounts`** → total todos for user.
2. **`TodoQuota::assess`** with **`UserSubscriptionRepository`** → **`can_create`**.
3. If over free-tier limit and no active subscription row → flash error redirect (no DB insert).

Missing **`user_subscriptions`** table yields a caught **`PDOException`** with actionable migration hint.

### Validation (`TodoValidator`)

Static methods validate **create** and **update** payloads (lengths, allowed priorities).

---

## Subscriptions & quota (`SubscribeController`, `SubscriptionPlans`, `TodoQuota`, `UserSubscriptionRepository`)

### Product catalogue (`SubscriptionPlans::catalog`)

Defines **`monthly`**, **`yearly`**, **`lifetime`** metadata (titles, pricing display copy, bullets). Enum alignment with DB **`plan`** column.

### Active subscription semantics (`UserSubscriptionRepository`)

- **Active** means a row exists for **`user_id`** **and** **`expires_at IS NULL`** (lifetime) **or** **`expires_at > NOW()`**.
- **`activateOrExtend`** implements demo logic:
  - Existing lifetime → **no-op** (cannot downgrade via simulate extend path).
  - **Lifetime plan** → upsert with **`expires_at = NULL`**.
  - Recurring plans → extend from **now** or current period end.

### Checkout behaviour (`SubscribeController`)

There is **no payment processor**:

- **`TodoQuota::allowSimulatedCheckout()`** reads **`APP_SIMULATE_SUBSCRIPTION_CHECKOUT`**.
- When **true**, POST chooses plan → **`activateOrExtend`** → redirect **`/subscribe?ok=1`** with flash.
- When **false**, POST responds with configuration error — UI shows disabled state with explanation.

### Relationship to todos

**`TodoQuota::freeTodoLimit()`** reads **`FREE_TODO_LIMIT`** (minimum enforced **1**, default **10**).

---

## Profile (`ProfileController`)

Authenticated-only (**guard with `/profile`** fallback).

### Sections

1. **Account**: **`UserRepository::findPublicProfileById`** (`name`, `email`, **`created_at`**).
2. **Subscription**: compares **`fetchSubscriptionDetails`** vs **`fetchActiveSubscription`** for active vs expired messaging.
3. **Cancel subscription**: POST **`_action=cancel_subscription`** + **`confirm_cancel=1`** + CSRF → **`UserSubscriptionRepository::deleteByUserId`**.

This removes subscription **benefits** in-app only — **does not** integrate external billing.

---

## Static / marketing pages

- **`HomeController`**, **`AboutController`**, **`ContactController`** — straightforward GET → view patterns.

---

## Not found (`NotFoundController`)

Handles unknown routes — serves appropriate HTTP semantics from controller (verify implementation when extending).

---

## Service provider stub (`App\Providers\AppServiceProvider`)

Placeholder **`register()` / `boot()`** with no bindings yet — hook for future DI or bootstrap tasks.
