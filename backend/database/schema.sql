CREATE DATABASE IF NOT EXISTS spendwise
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE spendwise;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS budgets;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS user_settings;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(254) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email),
    CONSTRAINT chk_users_email CHECK (email LIKE '%_@_%._%')
) ENGINE=InnoDB;

CREATE TABLE user_settings (
    user_id BIGINT UNSIGNED NOT NULL,
    preferred_currency CHAR(3) NOT NULL DEFAULT 'BAM',
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id),
    CONSTRAINT fk_settings_user FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT chk_settings_currency CHECK (preferred_currency REGEXP '^[A-Z]{3}$')
) ENGINE=InnoDB;

CREATE TABLE categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(50) NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_categories_user_name_type (user_id, name, type),
    UNIQUE KEY uq_categories_id_user (id, user_id),
    CONSTRAINT fk_categories_user FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT chk_categories_name CHECK (CHAR_LENGTH(TRIM(name)) > 0)
) ENGINE=InnoDB;

CREATE TABLE transactions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    description VARCHAR(255) NOT NULL,
    transaction_date DATE NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY ix_transactions_user_date (user_id, transaction_date),
    CONSTRAINT fk_transactions_user FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_transactions_category_user FOREIGN KEY (category_id, user_id)
        REFERENCES categories(id, user_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT chk_transactions_amount CHECK (amount > 0),
    CONSTRAINT chk_transactions_description CHECK (CHAR_LENGTH(TRIM(description)) > 0)
) ENGINE=InnoDB;

CREATE TABLE budgets (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    amount_limit DECIMAL(12,2) NOT NULL,
    month DATE NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_budgets_user_category_month (user_id, category_id, month),
    CONSTRAINT fk_budgets_user FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_budgets_category_user FOREIGN KEY (category_id, user_id)
        REFERENCES categories(id, user_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT chk_budgets_amount CHECK (amount_limit > 0),
    CONSTRAINT chk_budgets_month_start CHECK (DAYOFMONTH(month) = 1)
) ENGINE=InnoDB;

INSERT INTO users (id, full_name, email, password_hash) VALUES
    (1, 'Sample SpendWise User', 'sample@example.invalid', '!sample-user-has-no-login-password!');

INSERT INTO user_settings (user_id, preferred_currency) VALUES (1, 'BAM');

INSERT INTO categories (id, user_id, name, type) VALUES
    (1, 1, 'Food', 'expense'),
    (2, 1, 'Transport', 'expense'),
    (3, 1, 'Shopping', 'expense'),
    (4, 1, 'Bills', 'expense'),
    (5, 1, 'Salary', 'income');

INSERT INTO transactions (id, user_id, category_id, amount, type, description, transaction_date) VALUES
    (1, 1, 5, 3200.00, 'income', 'Monthly salary', '2026-10-01'),
    (2, 1, 1, 300.00, 'expense', 'Groceries', '2026-10-05'),
    (3, 1, 2, 180.00, 'expense', 'Bus and taxi', '2026-10-08'),
    (4, 1, 3, 140.00, 'expense', 'New clothes', '2026-10-12'),
    (5, 1, 4, 130.00, 'expense', 'Electricity bill', '2026-10-15');

INSERT INTO budgets (id, user_id, category_id, amount_limit, month) VALUES
    (1, 1, 1, 400.00, '2026-10-01'),
    (2, 1, 2, 300.00, '2026-10-01'),
    (3, 1, 3, 200.00, '2026-10-01'),
    (4, 1, 4, 300.00, '2026-10-01');
