<?php
$page_title = 'Add Income';
require_once '../../includes/header.php';
require_once '../../includes/Income.php';

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = trim($_POST['amount']);
    $source = trim($_POST['source']);
    $date = trim($_POST['income_date']);
    $scholarship = isset($_POST['is_scholarship']) ? 1 : 0;
    $notes = trim($_POST['notes']);

    // Validation
    if (!is_numeric($amount) || $amount <= 0) {
        $error = 'Amount must be a positive number.';
    } elseif (empty($source)) {
        $error = 'Source is required.';
    } elseif (empty($date)) {
        $error = 'Date is required.';
    } elseif (strtotime($date) > strtotime(date('Y-m-d'))) {
        $error = 'Income date cannot be in the future.';
    } else {
        $income = new Income();
        $income->amount = $amount;
        $income->source = $source;
        $income->incomeDate = $date;
        $income->isScholarship = $scholarship;
        $income->studentId = $user_id;
        $income->notes = $notes;

        if ($income->save()) {
            $success = 'Income added successfully!';
        } else {
            $error = 'Database error: could not save income.';
        }
    }
}
?>
<h2>➕ Add Income</h2>
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?> <a href="list.php">View list</a></div><?php endif; ?>

<form method="post">
    <div class="form-group">
        <label>Amount (kr):</label>
        <input type="number" step="0.01" name="amount" required>
    </div>
    <div class="form-group">
        <label>Source:</label>
        <input type="text" name="source" placeholder="e.g., Part‑time job, Scholarship" required>
    </div>
    <div class="form-group">
        <label>Date:</label>
        <input type="date" name="income_date" value="<?= date('Y-m-d') ?>" required>
    </div>
    <div class="form-group">
        <label><input type="checkbox" name="is_scholarship"> Scholarship income</label>
    </div>
    <div class="form-group">
        <label>Notes:</label>
        <textarea name="notes" rows="2"></textarea>
    </div>
    <button type="submit" class="btn">Save Income</button>
    <a href="list.php" class="btn btn-secondary">Cancel</a>
</form>

<?php require_once '../../includes/footer.php'; ?>