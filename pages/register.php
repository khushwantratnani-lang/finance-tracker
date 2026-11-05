<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';

redirectIfLoggedIn();

$errors = [];
$old    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old      = $_POST;
    $username = trim($_POST['username']        ?? '');
    $email    = trim($_POST['email']           ?? '');
    $password = $_POST['password']             ?? '';
    $confirm  = $_POST['confirm_password']     ?? '';

    if (empty($username))       $errors[] = 'Username is required.';
    if (empty($email))          $errors[] = 'Email is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn,
            "SELECT user_id FROM users WHERE email = ? OR username = ? LIMIT 1"
        );
        mysqli_stmt_bind_param($stmt, "ss", $email, $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = 'That email or username is already registered.';
        }
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = mysqli_prepare($conn,
            "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hash);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['user_id']  = mysqli_insert_id($conn);
            $_SESSION['username'] = $username;
            setFlash('success', 'Account created! Welcome, ' . $username . '.');
            header("Location: " . BASE_URL . "/pages/dashboard.php");
            exit();
        } else {
            $errors[] = 'Something went wrong. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Finance Tracker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-brand">
            <div class="auth-brand-icon">&#128203;</div>
            <h2>Create Account</h2>
            <p class="auth-subtitle">Join Finance Tracker — it's free</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error" style="margin-bottom:1.4rem;display:block;">
                <?php foreach ($errors as $e): ?>
                    <div>&#9888;&nbsp;<?= clean($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username"
                       value="<?= clean($old['username'] ?? '') ?>"
                       placeholder="Choose a username"
                       class="form-control" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email"
                       value="<?= clean($old['email'] ?? '') ?>"
                       placeholder="you@example.com"
                       class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Minimum 6 characters"
                       class="form-control" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password"
                       placeholder="Repeat your password"
                       class="form-control" required>
            </div>

            <button type="submit" class="btn btn-gold btn-full" style="margin-top:0.5rem;">
                Create Account &nbsp;&#8594;
            </button>

        </form>

        <p class="auth-footer">
            Already have an account?&nbsp;
            <a href="<?= BASE_URL ?>/login.php" style="color:var(--blue);font-weight:600;">
                Sign in here
            </a>
        </p>

    </div>
</div>

<script src="<?= BASE_URL ?>/public/js/main.js"></script>
</body>
</html>
