<?php
$page_title = 'My Income';
require_once '../../includes/header.php';
require_once '../../includes/Income.php';

$user_id = $_SESSION['user_id'];
$filter_source = $_GET['source'] ?? '';
$incomes = Income::all($user_id, $filter_source);
?>

<div class="page-header">
    <h2>💰 Income Records</h2>
    <a href="add.php" class="btn">+ Add Income</a>
</div>

<div class="filter-bar">
    <form method="get" style="display:flex; gap:1rem; align-items: flex-end;">
        <div class="form-group" style="margin-bottom:0;">
            <label>Filter by source</label>
            <input type="text" name="source" placeholder="e.g., Job, Scholarship" value="<?= htmlspecialchars($filter_source) ?>">
        </div>
        <button type="submit" class="btn">Filter</button>
        <?php if ($filter_source): ?>
            <a href="list.php" class="btn btn-secondary">Clear</a>
        <?php endif; ?>
    </form>
</div>

<?php if (empty($incomes)): ?>
    <div class="alert alert-info">No income records. <a href="add.php">Add your first income</a></div>
<?php else: ?>
    <div class="expense-grid">
        <?php foreach ($incomes as $inc): ?>
            <div class="expense-card">
                <div class="card-header" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <span class="amount">+<?= number_format($inc->amount, 2) ?> kr</span>
                    <span class="date"><?= date('d M Y', strtotime($inc->incomeDate)) ?></span>
                </div>
                <div class="card-body">
                    <div class="category">
                        📌 Source: <?= htmlspecialchars($inc->source) ?>
                    </div>
                    <?php if ($inc->isScholarship): ?>
                        <div class="notes">
                            🎓 <span style="color:#10b981;">Scholarship</span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($inc->notes)): ?>
                        <div class="notes">
                            📝 <?= htmlspecialchars($inc->notes) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="edit.php?id=<?= $inc->incomeId ?>" class="btn">✏️ Edit</a>
                    <a href="delete.php?id=<?= $inc->incomeId ?>" class="btn btn-danger delete-link">🗑️ Delete</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once '../../includes/footer.php'; ?>