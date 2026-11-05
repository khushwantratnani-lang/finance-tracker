<?php
// ─────────────────────────────────────────────────────────────
// includes/header.php — Shared HTML head + navbar
// ─────────────────────────────────────────────────────────────
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/config.php';
}
$pageTitle = $pageTitle ?? "Finance Tracker";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= clean($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body>

<?php if (isLoggedIn()): ?>
<nav class="navbar">
    <a class="nav-brand" href="<?= BASE_URL ?>/pages/dashboard.php">
        &#128181; Finance <span class="gold">Tracker</span>
    </a>
    <ul class="nav-links">
        <li><a href="<?= BASE_URL ?>/pages/dashboard.php">&#128202; Dashboard</a></li>
        <li><a href="<?= BASE_URL ?>/pages/add_transaction.php">&#43; Add Transaction</a></li>
        <li>
            <a href="<?= BASE_URL ?>/pages/logout.php" class="nav-logout-btn">&#8594; Logout</a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<main class="main-content">

<?php
$flash = getFlash();
if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?>">
    <?= $flash['type'] === 'success' ? '&#10003;' : '&#9888;' ?>
    &nbsp;<?= clean($flash['message']) ?>
</div>
<?php endif; ?>
