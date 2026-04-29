<?php
$page_title = 'Edit Expense';
require_once '../../includes/header.php';

$user_id = $_SESSION['user_id'];
$expense_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM tblExpense WHERE ExpenseID = :id AND StudentID = :uid");
$stmt->execute([':id' => $expense_id, ':uid' => $user_id]);
$expense = $stmt->fetch();
if (!$expense) {
    header('Location: view.php');
    exit;
}

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
        try {
            $sql = "UPDATE tblExpense SET Amount = :amount, CategoryID = :cat, ExpenseDate = :date, IsRecurring = :recur, Notes = :notes
                    WHERE ExpenseID = :id AND StudentID = :uid";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':amount' => $amount,
                ':cat' => $category_id,
                ':date' => $expense_date,
                ':recur' => $is_recurring,
                ':notes' => $notes,
                ':id' => $expense_id,
                ':uid' => $user_id
            ]);
            $success = 'Expense updated successfully!';
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>

<h2>✏️ Edit Expense</h2>
<?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php elseif ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?> <a href="view.php">Back to list</a></div>
<?php endif; ?>

<form method="post">
    <div class="form-group">
        <label>Amount (kr):</label>
        <input type="number" step="0.01" name="amount" value="<?= htmlspecialchars($expense['Amount']) ?>" required>
    </div>
    <div class="form-group">
        <label>Category:</label>
        <select name="category_id" required>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['CategoryID'] ?>" <?= $expense['CategoryID'] == $cat['CategoryID'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['CategoryName']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Date:</label>
        <input type="date" name="expense_date" value="<?= htmlspecialchars($expense['ExpenseDate']) ?>" required>
    </div>
    <div class="form-group">
        <label><input type="checkbox" name="is_recurring" <?= $expense['IsRecurring'] ? 'checked' : '' ?>> Recurring expense</label>
    </div>
    <div class="form-group">
        <label>Notes:</label>
        <textarea name="notes" rows="3"><?= htmlspecialchars($expense['Notes']) ?></textarea>
    </div>
    <button type="submit" class="btn">Update Expense</button>
    <a href="view.php" class="btn btn-secondary">Cancel</a>
</form>

<?php require_once '../../includes/footer.php'; ?>