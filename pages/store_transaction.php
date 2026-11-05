<?php require_once '../includes/config.php'; ?>
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit();
}

$user_id     = getUserId();
$amount      = floatval($_POST['amount']      ?? 0);
$type        = trim($_POST['type']            ?? '');
$category_id = intval($_POST['category_id']   ?? 0);
$date        = trim($_POST['date']            ?? '');
$description = trim($_POST['description']     ?? '');

// Validate
$errors = [];
if ($amount <= 0)                         $errors[] = 'Enter a valid amount greater than zero.';
if (!in_array($type, ['income','expense'])) $errors[] = 'Select a valid transaction type.';
if ($category_id <= 0)                    $errors[] = 'Select a category.';
if (empty($date))                         $errors[] = 'Select a date.';

if (!empty($errors)) {
    setFlash('error', $errors[0]);
    header('Location: add_transaction.php');
    exit();
}

$type_safe = mysqli_real_escape_string($conn, $type);
$desc_safe = mysqli_real_escape_string($conn, $description);
$date_safe = mysqli_real_escape_string($conn, $date);

$sql = "INSERT INTO transactions (user_id, amount, type, category_id, description, date)
        VALUES ($user_id, $amount, '$type_safe', $category_id, '$desc_safe', '$date_safe')";

if (mysqli_query($conn, $sql)) {
    setFlash('success', 'Transaction added successfully.');
} else {
    setFlash('error', 'Could not save transaction. Please try again.');
}

header('Location: dashboard.php');
exit();
