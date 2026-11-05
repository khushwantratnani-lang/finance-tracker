<?php
// ─────────────────────────────────────────────────────────────
// includes/auth.php — Session, login checks, flash messages
// ─────────────────────────────────────────────────────────────

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load base URL config (only once)
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/config.php';
}

// Redirect to login if not logged in
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "/login.php");
        exit();
    }
}

// Redirect to dashboard if already logged in
function redirectIfLoggedIn() {
    if (isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "/pages/dashboard.php");
        exit();
    }
}

// Returns true if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get the logged-in user's ID
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Get the logged-in user's username
function getUsername() {
    return $_SESSION['username'] ?? '';
}

// Store a one-time flash message
function setFlash($type, $message) {
    $_SESSION['flash_type']    = $type;
    $_SESSION['flash_message'] = $message;
}

// Retrieve and clear the flash message
function getFlash() {
    if (isset($_SESSION['flash_message'])) {
        $flash = [
            'type'    => $_SESSION['flash_type']    ?? 'success',
            'message' => $_SESSION['flash_message'] ?? '',
        ];
        unset($_SESSION['flash_type'], $_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

// Safely escape output to prevent XSS
function clean($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
