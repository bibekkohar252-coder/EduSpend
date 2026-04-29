<?php
require_once 'config.php';

$categories = $pdo->query("SELECT CategoryID, CategoryName FROM tblCategory WHERE IsActive = 1")->fetchAll();
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = trim($_POST['amount']);
    $categoryId = trim($_POST['category_id']);
    $expenseDate = trim($_POST['expense_date']);
    $isRecurring = isset($_POST['is_recurring']) ? 1 : 0;
    $studentId = 1; // Temporary – replace with session later
    $notes = trim($_POST['notes']);

    // Basic validation
    if ($amount <= 0) {
        $error = "Amount must be greater than 0.";
    } elseif (empty($categoryId)) {
        $error = "Please select a category.";
    } elseif (empty($expenseDate) || strtotime($expenseDate) > time()) {
        $error = "Date cannot be in the future.";
    } else {
        try {
            $sql = "INSERT INTO tblExpense (Amount, CategoryID, ExpenseDate, IsRecurring, StudentID, Notes)
                    VALUES (:amount, :categoryId, :expenseDate, :isRecurring, :studentId, :notes)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':amount' => $amount,
                ':categoryId' => $categoryId,
                ':expenseDate' => $expenseDate,
                ':isRecurring' => $isRecurring,
                ':studentId' => $studentId,
                ':notes' => $notes
            ]);
            $success = "Expense added successfully!";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Expense - EduSpend</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>➕ Add Expense</h2>
        <?php if ($success): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php elseif ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label>Amount (£):</label>
                <input type="number" step="0.01" name="amount" required>
            </div>
            <div class="form-group">
                <label>Category:</label>
                <select name="category_id" required>
                    <option value="">-- Select --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['CategoryID'] ?>"><?= htmlspecialchars($cat['CategoryName']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Date:</label>
                <input type="date" name="expense_date" required>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_recurring"> Recurring expense
                </label>
            </div>
            <div class="form-group">
                <label>Notes (optional):</label>
                <textarea name="notes" rows="3"></textarea>
            </div>
            <button type="submit" class="btn">Save Expense</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>