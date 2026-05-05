# HTTP routes reference

All routes are defined in **`routes/web.php`** as exact path matches (no trailing-slash normalisation). Method handling is **inside controllers** unless noted.

| Path | Controller | Auth | Notes |
|------|------------|------|--------|
| **`/`** | `HomeController::index()` | Optional | Landing |
| **`/about`** | `AboutController::index()` | Optional | |
| **`/contact`** | `ContactController::index()` | Optional | |
| **`/register`** | `RegisterController::index()` | Public | GET form / POST creates user + logs in |
| **`/login`** | `LoginController::index()` | Guest | Supports return to `intended_url` after guard |
| **`/logout`** | `LogoutController::logout()` | **POST + CSRF** | |
| **`/todos`** | `TodosController::index()` | **Required** | GET list; POST creates/updates/deletes/toggles via `_action` |
| **`/subscribe`** | `SubscribeController::index()` | **Required** | Simulated subscription when enabled in env |
| **`/profile`** | `ProfileController::index()` | **Required** | Account + subscription summary; POST cancel |
| **(other)** | `NotFoundController::index()` | — | Renders `404.view.php` |

### Query strings & filters

- **`/todos`**: **`q`**, **`status`** (`all|active|done`), **`priority`**, **`page`**, **`per_page`** — see **`TodoListState`**.
- **`/subscribe`**: **`ok=1`** triggers success flash consumption from session.

### Adding a route

1. **`use` import** at top of **`routes/web.php`** (if new controller namespace).
2. New **`case '/path':`** with **`break;`** (prevents accidental fall-through bugs).
3. Implement controller + view; protect with **`RequireAuthentication`** when needed.
4. Document here in the same PR.

### 404 behaviour

`NotFoundController` includes the **`404.view.php`** template. If you require strict HTTP semantics for APIs or SEO, confirm whether the response should call **`http_response_code(404)`** (currently verify in controller — may default to **200** unless set).
