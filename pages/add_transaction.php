<?php require_once '../includes/config.php'; ?>
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

requireLogin();

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");

$pageTitle = "Add Transaction — Finance Tracker";
require_once '../includes/header.php';
?>

<div class="page-hero">
    <h2>&#43; Add New Transaction</h2>
    <p>Record a new income or expense entry</p>
</div>

<div class="form-page">
    <a href="dashboard.php" class="back-link">&#8592; Back to Dashboard</a>

    <div class="card" style="padding:2rem;">
        <form method="POST" action="store_transaction.php">

            <div class="form-grid-2">

                <div class="form-group">
                    <label for="amount">Amount (&pound;)</label>
                    <input type="number" id="amount" name="amount"
                           step="0.01" min="0.01"
                           value="<?= clean($_POST['amount'] ?? '') ?>"
                           placeholder="0.00" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="type">Transaction Type</label>
                    <select id="type" name="type" class="form-control" required>
                        <option value="income"  <?= ($_POST['type'] ?? '') === 'income'  ? 'selected' : '' ?>>&#8593; Income</option>
                        <option value="expense" <?= ($_POST['type'] ?? '') === 'expense' ? 'selected' : '' ?>>&#8595; Expense</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                            <option value="<?= $cat['category_id'] ?>"
                                <?= ($_POST['category_id'] ?? '') == $cat['category_id'] ? 'selected' : '' ?>>
                                <?= clean($cat['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date"
                           value="<?= clean($_POST['date'] ?? date('Y-m-d')) ?>"
                           class="form-control" required>
                </div>

                <div class="form-group form-grid-full">
                    <label for="description">Description (optional)</label>
                    <input type="text" id="description" name="description"
                           value="<?= clean($_POST['description'] ?? '') ?>"
                           placeholder="e.g. Weekly grocery shopping"
                           class="form-control">
                </div>

            </div>

            <div style="display:flex;gap:1rem;margin-top:1.5rem;">
                <button type="submit" class="btn btn-gold" style="padding:12px 32px;font-size:1rem;">
                    &#10003;&nbsp; Save Transaction
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
