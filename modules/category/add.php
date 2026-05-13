<?php
$page_title = 'Add Category';
require_once '../../includes/header.php';
require_once '../../includes/Category.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $budget = trim($_POST['budget_limit']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $desc = trim($_POST['description']);

    // Validation
    if (empty($name)) {
        $error = 'Category name is required.';
    } elseif (!is_numeric($budget) || $budget < 0) {
        $error = 'Budget limit must be a non‑negative number.';
    } else {
        $category = new Category();
        $category->categoryName = $name;
        $category->budgetLimit = $budget;
        $category->isActive = $is_active;
        $category->createdDate = date('Y-m-d');
        $category->description = $desc;

        if ($category->save()) {
            $success = 'Category added successfully!';
        } else {
            $error = 'Database error: could not save category.';
        }
    }
}
?>
<h2>➕ Add Category</h2>
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?> <a href="list.php">View all</a></div><?php endif; ?>

<form method="post">
    <div class="form-group">
        <label>Category Name:</label>
        <input type="text" name="name" required>
    </div>
    <div class="form-group">
        <label>Budget Limit (kr):</label>
        <input type="number" step="0.01" name="budget_limit" value="0">
    </div>
    <div class="form-group">
        <label><input type="checkbox" name="is_active" checked> Active</label>
    </div>
    <div class="form-group">
        <label>Description:</label>
        <textarea name="description" rows="2"></textarea>
    </div>
    <button type="submit" class="btn">Save Category</button>
    <a href="list.php" class="btn btn-secondary">Cancel</a>
</form>

<?php require_once '../../includes/footer.php'; ?>