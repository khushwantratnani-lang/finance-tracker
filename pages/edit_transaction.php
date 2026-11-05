<?php require_once '../includes/config.php'; ?>
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

requireLogin();

$user_id = getUserId();
$id      = intval($_GET['id'] ?? 0);

// Fetch the transaction — verify it belongs to this user
$stmt = mysqli_prepare($conn,
    "SELECT * FROM transactions WHERE transaction_id = ? AND user_id = ?"
);
mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
mysqli_stmt_execute($stmt);
$result      = mysqli_stmt_get_result($stmt);
$transaction = mysqli_fetch_assoc($result);

if (!$transaction) {
    setFlash('error', 'Transaction not found.');
    header('Location: dashboard.php');
    exit();
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");

$pageTitle = "Edit Transaction — Finance Tracker";
require_once '../includes/header.php';
?>

<div class="page-hero">
    <h2>&#9998; Edit Transaction</h2>
    <p>Update the details for this entry</p>
</div>

<div class="form-page">
    <a href="dashboard.php" class="back-link">&#8592; Back to Dashboard</a>

    <div class="card" style="padding:2rem;">
        <form method="POST" action="update_transaction.php">
            <!-- Pass the transaction ID so update script knows which row to edit -->
            <input type="hidden" name="id" value="<?= $transaction['transaction_id'] ?>">

            <div class="form-grid-2">

                <div class="form-group">
                    <label for="amount">Amount (&pound;)</label>
                    <input type="number" id="amount" name="amount"
                           step="0.01" min="0.01"
                           value="<?= clean($_POST['amount'] ?? $transaction['amount']) ?>"
                           placeholder="0.00" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="type">Transaction Type</label>
                    <?php $currentType = $_POST['type'] ?? $transaction['type']; ?>
                    <select id="type" name="type" class="form-control" required>
                        <option value="income"  <?= $currentType === 'income'  ? 'selected' : '' ?>>&#8593; Income</option>
                        <option value="expense" <?= $currentType === 'expense' ? 'selected' : '' ?>>&#8595; Expense</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="category_id">Category</label>
                    <?php $currentCat = $_POST['category_id'] ?? $transaction['category_id']; ?>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                            <option value="<?= $cat['category_id'] ?>"
                                <?= $currentCat == $cat['category_id'] ? 'selected' : '' ?>>
                                <?= clean($cat['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date"
                           value="<?= clean($_POST['date'] ?? $transaction['date']) ?>"
                           class="form-control" required>
                </div>

                <div class="form-group form-grid-full">
                    <label for="description">Description (optional)</label>
                    <input type="text" id="description" name="description"
                           value="<?= clean($_POST['description'] ?? $transaction['description']) ?>"
                           placeholder="e.g. Weekly grocery shopping"
                           class="form-control">
                </div>

            </div>

            <div style="display:flex;gap:1rem;margin-top:1.5rem;">
                <button type="submit" class="btn btn-gold" style="padding:12px 32px;font-size:1rem;">
                    &#10003;&nbsp; Update Transaction
                </button>
                <a href="dashboard.php" class="btn btn-primary"
                   style="padding:12px 24px;font-size:1rem;opacity:0.75;">
                    Cancel
                </a>
            </div>

        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
