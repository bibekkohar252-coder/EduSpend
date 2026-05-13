<?php
$page_title = 'Add Expense';
require_once '../../includes/header.php';
require_once '../../includes/Expense.php';

$user_id = $_SESSION['user_id'];
$categories = $pdo->query("SELECT CategoryID, CategoryName FROM tblCategory WHERE IsActive = 1")->fetchAll();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = trim($_POST['amount']);
    $category_id = trim($_POST['category_id']);
    $expense_date = trim($_POST['expense_date']);
    $is_recurring = isset($_POST['is_recurring']) ? 1 : 0;
    $notes = trim($_POST['notes']);

    if (!is_numeric($amount) || $amount <= 0) {
        $error = 'Amount must be a positive number.';
    } elseif (empty($category_id)) {
        $error = 'Please select a category.';
    } elseif (empty($expense_date)) {
        $error = 'Please select a date.';
    } elseif (strtotime($expense_date) > strtotime(date('Y-m-d'))) {
        $error = 'Expense date cannot be in the future.';
    } else {
        $expense = new Expense();
        $expense->amount = $amount;
        $expense->categoryId = $category_id;
        $expense->expenseDate = $expense_date;
        $expense->isRecurring = $is_recurring;
        $expense->studentId = $user_id;
        $expense->notes = $notes;

        if ($expense->save()) {
            $success = 'Expense added successfully!';
        } else {
            $error = 'Database error: could not save expense.';
        }
    }
}
?>

<h2>➕ Add New Expense</h2>
<?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php elseif ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?> <a href="view.php">View all expenses</a></div>
<?php endif; ?>

<form method="post">
    <div class="form-group">
        <label>Amount (kr):</label>
        <input type="number" step="0.01" name="amount" value="<?= htmlspecialchars($_POST['amount'] ?? '') ?>" required>
    </div>
    <div class="form-group">
        <label>Category:</label>
        <select name="category_id" required>
            <option value="">-- Select --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['CategoryID'] ?>" <?= (isset($_POST['category_id']) && $_POST['category_id'] == $cat['CategoryID']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['CategoryName']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Date:</label>
        <input type="date" name="expense_date" value="<?= htmlspecialchars($_POST['expense_date'] ?? date('Y-m-d')) ?>" required>
    </div>
    <div class="form-group">
        <label><input type="checkbox" name="is_recurring" <?= isset($_POST['is_recurring']) ? 'checked' : '' ?>> Recurring expense</label>
    </div>
    <div class="form-group">
        <label>Notes (optional):</label>
        <textarea name="notes" rows="3"><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
    </div>
    <button type="submit" class="btn">Save Expense</button>
    <a href="view.php" class="btn btn-secondary">Cancel</a>
</form>

<?php require_once '../../includes/footer.php'; ?>