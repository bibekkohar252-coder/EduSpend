<?php
require_once 'config.php';

$studentId = 1; // Temporary – replace with session later

try {
    $sql = "SELECT e.*, c.CategoryName 
            FROM tblExpense e
            JOIN tblCategory c ON e.CategoryID = c.CategoryID
            WHERE e.StudentID = :studentId
            ORDER BY e.ExpenseDate DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':studentId' => $studentId]);
    $expenses = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching expenses: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Expenses - EduSpend</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>📋 My Expenses</h2>
        <?php if (count($expenses) === 0): ?>
            <p>No expenses found. <a href="add_expense.php">Add your first expense</a></p>
        <?php else: ?>
            <table class="expense-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Category</th>
                        <th>Recurring</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($expenses as $exp): ?>
                        <tr>
                            <td><?= htmlspecialchars($exp['ExpenseDate']) ?></td>
                            <td>£<?= number_format($exp['Amount'], 2) ?></td>
                            <td><?= htmlspecialchars($exp['CategoryName']) ?></td>
                            <td><?= $exp['IsRecurring'] ? 'Yes' : 'No' ?></td>
                            <td><?= htmlspecialchars($exp['Notes'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <div class="menu">
            <a href="add_expense.php" class="btn">Add another expense</a>
            <a href="index.php" class="btn btn-secondary">Back to Home</a>
        </div>
    </div>
</body>
</html>