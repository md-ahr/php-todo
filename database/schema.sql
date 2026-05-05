-- Run once against your todo database (matches DB_DATABASE in .env):
-- mysql -u todo -p todo < database/schema.sql

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY users_email_unique (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_subscriptions (
    user_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
    plan ENUM('monthly', 'yearly', 'lifetime') NOT NULL,
    expires_at DATETIME NULL COMMENT 'NULL = lifetime (no expiry)',
    granted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT user_subscriptions_user_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS todos (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(500) NOT NULL,
    notes TEXT NULL,
    priority ENUM('low','med','high') NOT NULL DEFAULT 'med',
    is_completed TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT todos_user_id_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    KEY todos_user_created (user_id, created_at),
    KEY todos_user_completed (user_id, is_completed)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
