<?php
$page_title = 'Edit Income';
require_once '../../includes/header.php';
require_once '../../includes/Income.php';

$user_id = $_SESSION['user_id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$income = Income::find($id);
if (!$income || $income->studentId != $user_id) {
    header('Location: list.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = trim($_POST['amount']);
    $source = trim($_POST['source']);
    $date = trim($_POST['income_date']);
    $scholarship = isset($_POST['is_scholarship']) ? 1 : 0;
    $notes = trim($_POST['notes']);

    if (!is_numeric($amount) || $amount <= 0) {
        $error = 'Amount must be a positive number.';
    } elseif (empty($source)) {
        $error = 'Source is required.';
    } elseif (empty($date)) {
        $error = 'Date is required.';
    } elseif (strtotime($date) > strtotime(date('Y-m-d'))) {
        $error = 'Income date cannot be in the future.';
    } else {
        $income->amount = $amount;
        $income->source = $source;
        $income->incomeDate = $date;
        $income->isScholarship = $scholarship;
        $income->notes = $notes;

        if ($income->save()) {
            $success = 'Income updated successfully!';
            // Reload the object to reflect changes (optional)
            $income = Income::find($id);
        } else {
            $error = 'Database error: could not update income.';
        }
    }
}
?>
<h2>✏️ Edit Income</h2>
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?> <a href="list.php">Back to list</a></div><?php endif; ?>

<form method="post">
    <div class="form-group">
        <label>Amount (kr):</label>
        <input type="number" step="0.01" name="amount" value="<?= $income->amount ?>" required>
    </div>
    <div class="form-group">
        <label>Source:</label>
        <input type="text" name="source" value="<?= htmlspecialchars($income->source) ?>" required>
    </div>
    <div class="form-group">
        <label>Date:</label>
        <input type="date" name="income_date" value="<?= $income->incomeDate ?>" required>
    </div>
    <div class="form-group">
        <label><input type="checkbox" name="is_scholarship" <?= $income->isScholarship ? 'checked' : '' ?>> Scholarship income</label>
    </div>
    <div class="form-group">
        <label>Notes:</label>
        <textarea name="notes" rows="2"><?= htmlspecialchars($income->notes) ?></textarea>
    </div>
    <button type="submit" class="btn">Update Income</button>
    <a href="list.php" class="btn btn-secondary">Cancel</a>
</form>

<?php require_once '../../includes/footer.php'; ?>