<?php
$page_title = 'Trash - Deleted Expenses';
require_once '../../includes/header.php';
require_once '../../includes/Expense.php';

$user_id = $_SESSION['user_id'];
$expenses = Expense::all($user_id, null, null, true);
$trashed = array_filter($expenses, function($e) { return $e->deletedAt !== null; });
?>

<style>
    .trash-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .empty-trash {
        text-align: center;
        padding: 3rem;
        background: white;
        border-radius: 24px;
        color: #64748b;
    }
    .btn-restore {
        background: linear-gradient(135deg, #10b981, #059669);
    }
    .btn-restore:hover {
        background: linear-gradient(135deg, #059669, #047857);
    }
</style>

<div class="trash-header">
    <h2>🗑️ Trash</h2>
    <a href="view.php" class="btn btn-secondary">← Back to Expenses</a>
</div>

<?php if (empty($trashed)): ?>
    <div class="empty-trash">No deleted expenses.</div>
<?php else: ?>
    <div class="expense-grid">
        <?php foreach ($trashed as $e): ?>
            <div class="expense-card">
                <div class="card-header">
                    <span class="amount"><?= number_format($e->amount, 2) ?> kr</span>
                    <span class="date"><?= htmlspecialchars($e->expenseDate) ?></span>
                </div>
                <div class="card-body">
                    <div class="category">📂 <?= htmlspecialchars($e->categoryName) ?></div>
                    <?php if ($e->isRecurring): ?>
                        <span class="recurring-badge">🔄 Recurring</span>
                    <?php endif; ?>
                    <?php if (!empty($e->notes)): ?>
                        <div class="notes">📝 <?= htmlspecialchars($e->notes) ?></div>
                    <?php endif; ?>
                    <div class="notes" style="color:#dc2626;">🗑️ Deleted: <?= date('d M Y H:i', strtotime($e->deletedAt)) ?></div>
                </div>
                <div class="card-footer">
                    <a href="restore.php?id=<?= $e->expenseId ?>" class="btn btn-restore restore-link">↩️ Restore</a>
                    <a href="hard_delete.php?id=<?= $e->expenseId ?>" class="btn btn-danger permanent-delete-link">💀 Permanent Delete</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once '../../includes/footer.php'; ?>