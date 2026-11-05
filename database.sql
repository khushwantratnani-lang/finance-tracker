-- Finance Tracker database setup
-- Run this in phpMyAdmin (SQL tab). It creates the database, tables and categories.

CREATE DATABASE IF NOT EXISTS finance_tracker
    CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE finance_tracker;

CREATE TABLE IF NOT EXISTS users (
    user_id       INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(100) NOT NULL UNIQUE,
    email         VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id        INT NOT NULL,
    amount         DECIMAL(10,2) NOT NULL,
    type           ENUM('income','expense') NOT NULL,
    category_id    INT NOT NULL,
    description    TEXT,
    date           DATE NOT NULL,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)     REFERENCES users(user_id)          ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
);

INSERT INTO categories (name) VALUES
    ('Food'),
    ('Rent'),
    ('Transport'),
    ('Salary'),
    ('Entertainment'),
    ('Utilities');
