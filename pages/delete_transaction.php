<?php require_once '../includes/config.php'; ?>
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit();
}

$user_id = getUserId();
$id      = intval($_POST['id'] ?? 0);

// Verify the transaction belongs to this user before deleting
$check = mysqli_prepare($conn,
    "SELECT transaction_id FROM transactions WHERE transaction_id = ? AND user_id = ?"
);
mysqli_stmt_bind_param($check, "ii", $id, $user_id);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) > 0) {
    $del = mysqli_prepare($conn,
        "DELETE FROM transactions WHERE transaction_id = ? AND user_id = ?"
    );
    mysqli_stmt_bind_param($del, "ii", $id, $user_id);
    mysqli_stmt_execute($del);
    setFlash('success', 'Transaction deleted successfully.');
} else {
    setFlash('error', 'Transaction not found.');
}

header('Location: dashboard.php');
exit();
