-- ============================================================
-- PHP Security Lab - Database Setup
-- For Educational Purposes Only (Local Use)
-- ============================================================

CREATE DATABASE IF NOT EXISTS security_lab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE security_lab;

-- ============================================================
-- Table: users
-- ============================================================
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dummy user data (plain text for demo purposes)
-- NOTE: In production, ALWAYS use password_hash() !!
INSERT INTO users (username, password) VALUES
('admin', 'admin123'),
('mahasiswa', 'mahasiswa123');

-- ============================================================
-- Table: comments
-- ============================================================
DROP TABLE IF EXISTS comments;
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample comments
INSERT INTO comments (name, comment) VALUES
('Alice', 'Halo semua! Ini komentar pertama.'),
('Bob', 'Selamat belajar keamanan web!'),
('Charlie', 'XSS dan SQL Injection itu berbahaya kalau tidak dimitigasi.');
