<?php
$page_title = 'Savings Goals';
require_once '../../includes/header.php';
require_once '../../includes/SavingsGoal.php';

$user_id = $_SESSION['user_id'];
$goals = SavingsGoal::all($user_id);
?>

<div class="page-header">
    <h2>🎯 Savings Goals</h2>
    <a href="add.php" class="btn">+ New Goal</a>
</div>

<?php if (empty($goals)): ?>
    <div class="alert alert-info">No goals yet. <a href="add.php">Create one</a></div>
<?php else: ?>
    <div class="expense-grid">
        <?php foreach ($goals as $g): 
            $percent = $g->getProgressPercent();
            // Auto-complete if reached
            if (!$g->isCompleted && $g->currentAmount >= $g->targetAmount) {
                $g->updateProgress(0);
                $g = SavingsGoal::find($g->goalId);
                $percent = 100;
            }
        ?>
            <div class="expense-card">
                <div class="card-header" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <span class="amount"><?= htmlspecialchars($g->goalName) ?></span>
                    <span class="date"><?= $g->isCompleted ? '✅ Completed' : '⏳ In progress' ?></span>
                </div>
                <div class="card-body">
                    <div class="category">
                        🎯 Target: <?= number_format($g->targetAmount, 2) ?> kr
                    </div>
                    <div class="notes" style="margin: 0.5rem 0;">
                        💰 Saved: <?= number_format($g->currentAmount, 2) ?> kr
                    </div>
                    <div class="goal-progress-container" style="margin: 0.5rem 0;">
                        <div class="goal-progress-bar" style="background:#e2e8f0; border-radius:20px; height:10px;">
                            <div class="goal-progress-fill" style="width: <?= min(100, $percent) ?>%; background: linear-gradient(90deg, #f59e0b, #d97706); height:10px; border-radius:20px;"></div>
                        </div>
                        <div style="text-align:right; font-size:0.7rem; margin-top:0.2rem;"><?= $percent ?>%</div>
                    </div>
                    <div class="notes" style="font-size:0.75rem;">
                        📅 Target date: <?= date('d M Y', strtotime($g->targetDate)) ?>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="edit.php?id=<?= $g->goalId ?>" class="btn">✏️ Edit</a>
                    <a href="delete.php?id=<?= $g->goalId ?>" class="btn btn-danger delete-link">🗑️ Delete</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once '../../includes/footer.php'; ?>