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
$id          = intval($_POST['id']            ?? 0);
$amount      = floatval($_POST['amount']      ?? 0);
$type        = trim($_POST['type']            ?? '');
$category_id = intval($_POST['category_id']   ?? 0);
$date        = trim($_POST['date']            ?? '');
$description = trim($_POST['description']     ?? '');

// Validate
$errors = [];
if ($amount <= 0)                             $errors[] = 'Enter a valid amount.';
if (!in_array($type, ['income', 'expense']))  $errors[] = 'Invalid transaction type.';
if ($category_id <= 0)                        $errors[] = 'Select a category.';
if (empty($date))                             $errors[] = 'Select a date.';

if (!empty($errors)) {
    setFlash('error', $errors[0]);
    header("Location: edit_transaction.php?id=$id");
    exit();
}

// Verify this transaction belongs to the logged-in user
$check = mysqli_prepare($conn,
    "SELECT transaction_id FROM transactions WHERE transaction_id = ? AND user_id = ?"
);
mysqli_stmt_bind_param($check, "ii", $id, $user_id);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) === 0) {
    setFlash('error', 'Transaction not found.');
    header('Location: dashboard.php');
    exit();
}

$type_safe = mysqli_real_escape_string($conn, $type);
$desc_safe = mysqli_real_escape_string($conn, $description);
$date_safe = mysqli_real_escape_string($conn, $date);

$sql = "UPDATE transactions
        SET amount = $amount,
            type = '$type_safe',
            category_id = $category_id,
            date = '$date_safe',
            description = '$desc_safe'
        WHERE transaction_id = $id AND user_id = $user_id";

if (mysqli_query($conn, $sql)) {
    setFlash('success', 'Transaction updated successfully.');
} else {
    setFlash('error', 'Could not update transaction. Please try again.');
}

header('Location: dashboard.php');
exit();
