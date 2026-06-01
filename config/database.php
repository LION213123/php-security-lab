<?php
// ============================================================
// config/database.php
// Database Configuration - PHP Security Lab
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // default XAMPP/Laragon: kosong
define('DB_NAME', 'security_lab');
define('DB_PORT', 3306);

// ============================================================
// MySQLi connection (used by unsecure demos)
// ============================================================
function getConnection(): mysqli {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    if ($conn->connect_error) {
        die('<div class="error-box">❌ Koneksi database gagal: ' . $conn->connect_error . '<br>Pastikan MySQL sudah berjalan dan database <strong>security_lab</strong> sudah diimport.</div>');
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}

// ============================================================
// PDO connection (used by secure demos)
// ============================================================
function getPDO(): PDO {
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    try {
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        die('<div class="error-box">❌ Koneksi PDO gagal: ' . $e->getMessage() . '<br>Pastikan MySQL sudah berjalan dan database <strong>security_lab</strong> sudah diimport.</div>');
    }
}
