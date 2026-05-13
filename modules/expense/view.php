<?php
$page_title = 'My Expenses';
require_once '../../includes/header.php';
require_once '../../includes/Expense.php';

$user_id = $_SESSION['user_id'];
$categories = $pdo->query("SELECT CategoryID, CategoryName FROM tblCategory WHERE IsActive = 1")->fetchAll();
$category_filter = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
$expenses = Expense::all($user_id, $category_filter, $search);
?>
<div class="filter-bar">
    <form method="get" style="display:flex; gap:1rem; flex-wrap:wrap; width:100%;">
        <select name="category" onchange="this.form.submit()">
            <option value="">All categories</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c['CategoryID'] ?>" <?= $category_filter == $c['CategoryID'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['CategoryName']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="search" placeholder="Search by amount or notes" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn">Search</button>
        <?php if ($category_filter || $search): ?>
            <a href="view.php" class="btn btn-secondary">Clear</a>
        <?php endif; ?>
    </form>
</div>

<?php if (empty($expenses)): ?>
    <div class="alert alert-info">No expenses found. <a href="add.php">Add one</a></div>
<?php else: ?>
    <table class="expense-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Amount (kr)</th>
                <th>Category</th>
                <th>Recurring</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($expenses as $e): ?>
            <tr>
                <td><?= htmlspecialchars($e->expenseDate) ?></td>
                <td><?= number_format($e->amount, 2) ?> kr</td>
                <td><?= htmlspecialchars($e->categoryName) ?></td>
                <td><?= $e->isRecurring ? 'Yes' : 'No' ?></td>
                <td><?= htmlspecialchars($e->notes) ?></td>
                <td>
                    <a href="edit.php?id=<?= $e->expenseId ?>" class="btn">Edit</a>
                    <a href="delete.php?id=<?= $e->expenseId ?>" class="btn btn-danger delete-link">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<a href="add.php" class="btn">+ Add Expense</a>
<?php require_once '../../includes/footer.php'; ?>