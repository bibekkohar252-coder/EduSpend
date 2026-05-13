<?php
$page_title = 'Manage Categories';
require_once '../../includes/header.php';
require_once '../../includes/Category.php';

$filter = $_GET['filter'] ?? '';
$categories = Category::all($filter);
?>
<div class="filter-bar">
    <form method="get" style="display:flex; gap:1rem;">
        <select name="filter" onchange="this.form.submit()">
            <option value="">All</option>
            <option value="active" <?= $filter == 'active' ? 'selected' : '' ?>>Active only</option>
            <option value="inactive" <?= $filter == 'inactive' ? 'selected' : '' ?>>Inactive only</option>
        </select>
        <?php if ($filter): ?>
            <a href="list.php" class="btn btn-secondary">Clear</a>
        <?php endif; ?>
    </form>
</div>

<?php if (empty($categories)): ?>
    <div class="alert alert-info">No categories found. <a href="add.php">Add one</a></div>
<?php else: ?>
    <table class="expense-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Budget (kr)</th>
                <th>Active</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($categories as $cat): ?>
            <tr>
                <td><?= $cat->categoryId ?></td>
                <td><?= htmlspecialchars($cat->categoryName) ?></td>
                <td><?= number_format($cat->budgetLimit, 2) ?> kr</td>
                <td><?= $cat->isActive ? '✅ Active' : '❌ Inactive' ?></td>
                <td><?= htmlspecialchars($cat->createdDate) ?></td>
                <td>
                    <a href="edit.php?id=<?= $cat->categoryId ?>" class="btn">Edit</a>
                    <a href="delete.php?id=<?= $cat->categoryId ?>" class="btn btn-danger delete-link">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<a href="add.php" class="btn">+ Add Category</a>

<?php require_once '../../includes/footer.php'; ?>