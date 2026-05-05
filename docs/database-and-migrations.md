# Database & migrations

---

## Overview

The application targets **MySQL 8** with **InnoDB** and **`utf8mb4`** collation. All persistence goes through **PDO** with **`ERRMODE_EXCEPTION`** and **native prepares disabled emulation** (`ATTR_EMULATE_PREPARES => false`) where configured in **`Connection::get()`**.

---

## Schema (`database/schema.sql`)

Three logical domains:

### `users`

| Column | Notes |
|--------|--------|
| `id` | BIGINT PK, auto-increment |
| `name`, `email` | Email unique (`users_email_unique`) |
| `password` | **`password_hash`** output string |
| `created_at` | Default current timestamp |

### `user_subscriptions`

One row per user at most (**`user_id` PK**).

| Column | Notes |
|--------|--------|
| `plan` | ENUM **`monthly`**, **`yearly`**, **`lifetime`** |
| `expires_at` | **`NULL`** ⇒ lifetime / no expiry datetime |
| `granted_at` | Audit-ish timestamp |

FK **`user_subscriptions.user_id`** → **`users.id`** **`ON DELETE CASCADE`**.

### `todos`

| Column | Notes |
|--------|--------|
| `user_id` | FK → **`users.id`** CASCADE |
| `title` | VARCHAR(500) |
| `notes` | TEXT nullable |
| `priority` | ENUM **`low`**, **`med`**, **`high`** |
| `is_completed` | Tinyint boolean-ish |
| `created_at`, `updated_at` | Automatic timestamps |

Indexes support filtering by user + created/completed patterns (**`KEY todos_user_created`**, **`KEY todos_user_completed`**).

---

## Applying migrations

### Scripted (`database/migrate.php`)

Recommended:

```bash
php database/migrate.php
```

Behaviour:

1. Loads Composer autoload + helpers + `.env`.
2. **`Connection::reset()`** then **`db_retry_once`** executes **`database/schema.sql`** as one **`PDO::exec`** batch.

Safe for **idempotent** DDL (`CREATE TABLE IF NOT EXISTS`). Destructive migrations are **not** modelled — coordinate manual SQL if altering columns.

### Raw MySQL client

```bash
mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" < database/schema.sql
```

---

## Repository map

| Repository | Primary tables |
|------------|------------------|
| **`UserRepository`** | `users` |
| **`UserSubscriptionRepository`** | `user_subscriptions` |
| **`TodoRepository`** | `todos` |

Repositories accept **`PDO`** via constructor injection from controllers (**`db()`** helper).

---

## Connection configuration (`config/database.php`)

Reads **`DB_HOST`**, **`DB_PORT`**, **`DB_DATABASE`**, **`DB_USERNAME`**, **`DB_PASSWORD`** via **`env()`**.

---

## Operational notes

- **Cascade deletes**: dropping a user removes related todos and subscription row automatically — backup/export accordingly.
- **Timezones**: **`DATETIME`** comparisons use MySQL **`NOW()`** in subscription queries — align PHP **`date_default_timezone_set`** with business expectations for display-only formatting.
