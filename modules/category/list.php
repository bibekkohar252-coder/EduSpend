<?php
$page_title = 'Manage Categories';
require_once '../../includes/header.php';
require_once '../../includes/Category.php';

$filter = $_GET['filter'] ?? '';
$categories = Category::all($filter);
?>

<div class="page-header">
    <h2>📂 Categories</h2>
    <a href="add.php" class="btn">+ Add New Category</a>
</div>

<div class="filter-bar">
    <form method="get" style="display:flex; gap:1rem; align-items: flex-end;">
        <div class="form-group" style="margin-bottom:0;">
            <label>Filter by status</label>
            <select name="filter" onchange="this.form.submit()" class="filter-select">
                <option value="">All categories</option>
                <option value="active" <?= $filter == 'active' ? 'selected' : '' ?>>Active only</option>
                <option value="inactive" <?= $filter == 'inactive' ? 'selected' : '' ?>>Inactive only</option>
            </select>
        </div>
        <?php if ($filter): ?>
            <a href="list.php" class="btn btn-secondary" style="margin-top: 0;">Clear</a>
        <?php endif; ?>
    </form>
</div>

<?php if (empty($categories)): ?>
    <div class="alert alert-info">No categories found. <a href="add.php">Create one now</a></div>
<?php else: ?>
    <div class="expense-grid">
        <?php foreach ($categories as $cat): ?>
            <div class="expense-card">
                <div class="card-header" style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                    <span class="amount"><?= htmlspecialchars($cat->categoryName) ?></span>
                    <span class="date">ID: <?= $cat->categoryId ?></span>
                </div>
                <div class="card-body">
                    <div class="category">
                        📊 Budget: <?= number_format($cat->budgetLimit, 2) ?> kr
                    </div>
                    <div class="notes">
                        Status: <?= $cat->isActive ? '<span style="color:#10b981;">✅ Active</span>' : '<span style="color:#ef4444;">❌ Inactive</span>' ?>
                    </div>
                    <?php if (!empty($cat->description)): ?>
                        <div class="notes" style="margin-top: 0.5rem;">
                            📝 <?= htmlspecialchars($cat->description) ?>
                        </div>
                    <?php endif; ?>
                    <div class="notes" style="font-size: 0.7rem; margin-top: 0.5rem;">
                        🗓️ Created: <?= date('d M Y', strtotime($cat->createdDate)) ?>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="edit.php?id=<?= $cat->categoryId ?>" class="btn">✏️ Edit</a>
                    <a href="delete.php?id=<?= $cat->categoryId ?>" class="btn btn-danger delete-link">🗑️ Delete</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once '../../includes/footer.php'; ?>