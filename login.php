<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';

redirectIfLoggedIn();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = mysqli_prepare($conn,
            "SELECT user_id, username, password_hash FROM users WHERE email = ?"
        );
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user   = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id']  = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            setFlash('success', 'Welcome back, ' . $user['username'] . '!');
            header("Location: " . BASE_URL . "/pages/dashboard.php");
            exit();
        } else {
            $error = 'Incorrect email or password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Finance Tracker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-brand">
            <div class="auth-brand-icon">&#128181;</div>
            <h2>Finance Tracker</h2>
            <p class="auth-subtitle">Sign in to manage your finances</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error" style="margin-bottom:1.4rem;">
                &#9888;&nbsp;<?= clean($error) ?>
            </div>
        <?php endif; ?>

        <?php
        $flash = getFlash();
        if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] ?>" style="margin-bottom:1.4rem;">
                &#10003;&nbsp;<?= clean($flash['message']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email"
                       value="<?= clean($_POST['email'] ?? '') ?>"
                       placeholder="you@example.com"
                       class="form-control" required autocomplete="email">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Enter your password"
                       class="form-control" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-primary btn-full" style="margin-top:0.5rem;">
                Sign In &nbsp;&#8594;
            </button>

        </form>

        <p class="auth-footer">
            Don't have an account?&nbsp;
            <a href="<?= BASE_URL ?>/pages/register.php"
               style="color:var(--gold);font-weight:600;">
                Create one here
            </a>
        </p>

    </div>
</div>

<script src="<?= BASE_URL ?>/public/js/main.js"></script>
</body>
</html>
