<?php
$page_title = 'My Expenses';
require_once '../../includes/header.php';
$user_id = $_SESSION['user_id'];

$categories = $pdo->query("SELECT CategoryID, CategoryName FROM tblCategory WHERE IsActive = 1")->fetchAll();

$category_filter = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT e.*, c.CategoryName FROM tblExpense e JOIN tblCategory c ON e.CategoryID = c.CategoryID WHERE e.StudentID = :uid";
$params = [':uid' => $user_id];

if ($category_filter) {
    $sql .= " AND e.CategoryID = :cat";
    $params[':cat'] = $category_filter;
}
if ($search) {
    $sql .= " AND (e.Amount LIKE :search OR e.Notes LIKE :search)";
    $params[':search'] = "%$search%";
}
$sql .= " ORDER BY e.ExpenseDate DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$expenses = $stmt->fetchAll();
?>
<div class="filter-bar">
    <form method="get" style="display:flex; gap:1rem; flex-wrap:wrap; width:100%;">
        <select name="category" onchange="this.form.submit()">
            <option value="">All categories</option>
            <?php foreach($categories as $c): ?>
                <option value="<?= $c['CategoryID'] ?>" <?= $category_filter == $c['CategoryID'] ? 'selected' : '' ?>><?= htmlspecialchars($c['CategoryName']) ?></option>
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
    <table>
        <thead><tr><th>Date</th><th>Amount (kr)</th><th>Category</th><th>Recurring</th><th>Notes</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($expenses as $e): ?>
            <tr>
                <td><?= htmlspecialchars($e['ExpenseDate']) ?></td>
                <td><?= number_format($e['Amount'],2) ?> kr</td>
                <td><?= htmlspecialchars($e['CategoryName']) ?></td>
                <td><?= $e['IsRecurring'] ? 'Yes' : 'No' ?></td>
                <td><?= htmlspecialchars($e['Notes']) ?></td>
                <td><a href="edit.php?id=<?= $e['ExpenseID'] ?>" class="btn" style="padding:0.2rem 0.6rem;">Edit</a> <a href="delete.php?id=<?= $e['ExpenseID'] ?>" class="btn btn-danger delete-link" style="padding:0.2rem 0.6rem;">Delete</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<a href="add.php" class="btn">+ Add Expense</a>
<?php require_once '../../includes/footer.php'; ?>