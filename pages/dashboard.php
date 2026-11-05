<?php require_once '../includes/config.php'; ?>
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

requireLogin();

$user_id  = getUserId();
$username = getUsername();

// ── Handle quick-add form FIRST (before any queries) ───────────────────
// This way the redirect happens before we load data,
// so the next page load shows the updated data.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_transaction'])) {
    $amount      = floatval($_POST['amount']      ?? 0);
    $type        = mysqli_real_escape_string($conn, trim($_POST['type'] ?? ''));
    $category_id = intval($_POST['category_id']   ?? 0);
    $description = mysqli_real_escape_string($conn, trim($_POST['description'] ?? ''));
    $date        = mysqli_real_escape_string($conn, trim($_POST['date'] ?? ''));

    if ($amount <= 0 || empty($type) || empty($date)) {
        setFlash('error', 'Please fill in Amount, Type and Date.');
    } else {
        $sql = "INSERT INTO transactions (user_id, amount, type, category_id, description, date)
                VALUES ($user_id, $amount, '$type', $category_id, '$description', '$date')";
        if (mysqli_query($conn, $sql)) {
            setFlash('success', 'Transaction added successfully.');
        } else {
            setFlash('error', 'Could not save transaction. Please try again.');
        }
    }
    header("Location: dashboard.php");
    exit();
}

// ── Summary totals ──────────────────────────────────────────────────────
$r = mysqli_query($conn, "SELECT SUM(amount) AS total FROM transactions
                           WHERE user_id = $user_id AND type = 'income'");
$total_income = mysqli_fetch_assoc($r)['total'] ?? 0;

$r = mysqli_query($conn, "SELECT SUM(amount) AS total FROM transactions
                           WHERE user_id = $user_id AND type = 'expense'");
$total_expenses = mysqli_fetch_assoc($r)['total'] ?? 0;

$balance = $total_income - $total_expenses;

// ── Categories for the dropdown ─────────────────────────────────────────
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");

// ── All transactions for this user, newest first ────────────────────────
$transactions = mysqli_query($conn,
    "SELECT t.*, c.name AS category_name
     FROM transactions t
     JOIN categories c ON t.category_id = c.category_id
     WHERE t.user_id = $user_id
     ORDER BY t.date DESC, t.created_at DESC"
);

$pageTitle = "Dashboard — Finance Tracker";
require_once '../includes/header.php';
?>

<!-- Dashboard Header -->
<div class="dashboard-header">
    <div>
        <h1>&#128181; My Dashboard</h1>
        <p>Welcome back, <strong><?= clean($username) ?></strong> — here is your financial overview.</p>
    </div>
    <div class="header-pill">&#128197;&nbsp; <?= date('d F Y') ?></div>
</div>

<!-- Summary Cards -->
<div class="summary-cards">

    <div class="summary-card card-income">
        <div class="summary-card-icon ic-i">&#128200;</div>
        <div class="summary-card-label">Total Income</div>
        <div class="summary-card-amount positive">
            &pound;<?= number_format($total_income, 2) ?>
        </div>
    </div>

    <div class="summary-card card-expense">
        <div class="summary-card-icon ic-e">&#128201;</div>
        <div class="summary-card-label">Total Expenses</div>
        <div class="summary-card-amount negative">
            &pound;<?= number_format($total_expenses, 2) ?>
        </div>
    </div>

    <div class="summary-card card-balance">
        <div class="summary-card-icon ic-b">&#9878;</div>
        <div class="summary-card-label">Balance</div>
        <div class="summary-card-amount <?= $balance >= 0 ? 'positive' : 'negative' ?>">
            <?= $balance < 0 ? '-' : '' ?>&pound;<?= number_format(abs($balance), 2) ?>
        </div>
    </div>

</div>

<!-- Quick Add Transaction Form -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <span class="icon">&#43;</span> Quick Add Transaction
        </div>
    </div>

    <form action="dashboard.php" method="POST" style="padding:1.5rem 2rem;">
        <input type="hidden" name="add_transaction" value="1">
        <div class="add-form-row">

            <div class="form-group">
                <label>Amount (&pound;)</label>
                <input type="number" name="amount" step="0.01" min="0.01"
                       placeholder="0.00" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Type</label>
                <select name="type" class="form-control" required>
                    <option value="income">&#8593; Income</option>
                    <option value="expense">&#8595; Expense</option>
                </select>
            </div>

            <div class="form-group">
                <label>Category</label>
                <select name="category_id" class="form-control" required>
                    <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                        <option value="<?= $cat['category_id'] ?>">
                            <?= clean($cat['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" class="form-control"
                       value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-group">
                <label>Description (optional)</label>
                <input type="text" name="description" class="form-control"
                       placeholder="e.g. Grocery shopping">
            </div>

            <div class="form-group" style="display:flex;align-items:flex-end;">
                <button type="submit" class="btn btn-gold" style="width:100%;">
                    &#43; Add
                </button>
            </div>

        </div>
    </form>
</div>

<!-- Transactions Table -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <span class="icon">&#128203;</span> Your Transactions
        </div>
        <a href="add_transaction.php" class="btn btn-primary btn-sm">&#43; New</a>
    </div>

    <?php if (mysqli_num_rows($transactions) === 0): ?>
        <div class="no-data">
            <div class="no-data-icon">&#128202;</div>
            <p>No transactions yet. Add your first one above!</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($t = mysqli_fetch_assoc($transactions)): ?>
                    <tr class="<?= $t['type'] === 'income' ? 'row-income' : 'row-expense' ?>">

                        <td style="color:var(--text-muted);font-size:0.85rem;white-space:nowrap;">
                            <?= date('d M Y', strtotime($t['date'])) ?>
                        </td>

                        <td>
                            <span class="cat-pill"><?= clean($t['category_name']) ?></span>
                        </td>

                        <td style="color:var(--text-muted);">
                            <?= clean($t['description'] ?: '—') ?>
                        </td>

                        <td>
                            <span class="badge badge-<?= $t['type'] ?>">
                                <?= $t['type'] === 'income' ? '&#8593;' : '&#8595;' ?>
                                <?= ucfirst($t['type']) ?>
                            </span>
                        </td>

                        <td class="amount-cell amount-<?= $t['type'] ?>">
                            <?= $t['type'] === 'expense' ? '-' : '+' ?>
                            &pound;<?= number_format($t['amount'], 2) ?>
                        </td>

                        <td>
                            <div class="table-actions">
                                <a href="edit_transaction.php?id=<?= $t['transaction_id'] ?>"
                                   class="btn btn-sm btn-edit">&#9998; Edit</a>

                                <form action="delete_transaction.php" method="POST"
                                      onsubmit="return confirm('Are you sure you want to delete this transaction?')">
                                    <input type="hidden" name="id" value="<?= $t['transaction_id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-delete">
                                        &#10005; Delete
                                    </button>
                                </form>
                            </div>
                        </td>

                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
