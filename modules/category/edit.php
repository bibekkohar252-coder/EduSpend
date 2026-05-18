<?php
$page_title = 'Edit Category';
require_once '../../includes/header.php';
require_once '../../includes/Category.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$category = Category::find($id);
if (!$category) {
    header('Location: list.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $budget = trim($_POST['budget_limit']);
    $active = isset($_POST['is_active']) ? 1 : 0;
    $desc = trim($_POST['description']);

    if (empty($name)) {
        $error = 'Category name is required.';
    } elseif (!is_numeric($budget) || $budget < 0) {
        $error = 'Budget limit must be a non‑negative number.';
    } else {
        $category->categoryName = $name;
        $category->budgetLimit = $budget;
        $category->isActive = $active;
        $category->description = $desc;

        if ($category->save()) {
            $success = 'Category updated successfully!';
            $category = Category::find($id);
        } else {
            $error = 'Database error: could not update category.';
        }
    }
}
?>

<h2>✏️ Edit Category</h2>

<?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?> <a href="list.php">← Back to list</a></div>
<?php endif; ?>

<form method="post" class="modern-form">
    <div class="form-group">
        <label>Category Name *</label>
        <input type="text" name="name" value="<?= htmlspecialchars($category->categoryName) ?>" required>
    </div>
    <div class="form-group">
        <label>Budget Limit (kr)</label>
        <input type="number" step="0.01" name="budget_limit" value="<?= $category->budgetLimit ?>">
    </div>
    <div class="form-group checkbox-group">
        <label class="checkbox-inline">
            <input type="checkbox" name="is_active" <?= $category->isActive ? 'checked' : '' ?>>
            <span>Active</span>
        </label>
    </div>
    <div class="form-group">
        <label>Description</label>
        <textarea name="description" rows="3"><?= htmlspecialchars($category->description) ?></textarea>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn">💾 Update Category</button>
        <a href="list.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<?php require_once '../../includes/footer.php'; ?>