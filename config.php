<?php
session_start();

// Site settings
const SITE_NAME = 'Greenfield Academy';
const SITE_TAGLINE = 'A complete learning journey from Early Years to University';
const SITE_ADDRESS = 'Westlands, Nairobi, Kenya';
const SITE_PHONE = '+254 707 566 279';
const SITE_EMAIL = 'info@greenfield.sc.ke';

// Database connection settings (local XAMPP defaults)
const DB_HOST = 'localhost';
const DB_NAME = 'greenfield_academy';
const DB_USER = 'root';
const DB_PASS = '';

function db()
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_NAME);
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    return $pdo;
}

function sanitize($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function isLoggedIn()
{
    return !empty($_SESSION['user_id']);
}

function currentUser()
{
    if (!isLoggedIn()) {
        return null;
    }

    static $user = null;
    if ($user === null) {
        $stmt = db()->prepare('SELECT id, full_name, email, role, level, student_id, avatar FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch() ?: null;
    }

    return $user;
}

function loginUser($email, $password)
{
    $stmt = db()->prepare('SELECT id, password FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if (!$user) {
        return false;
    }

    if (!empty($user['password']) && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        return true;
    }

    return false;
}

function logoutUser()
{
    unset($_SESSION['user_id']);
    session_destroy();
}
