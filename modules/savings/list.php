<?php
$page_title = 'Savings Goals';
require_once '../../includes/header.php';
require_once '../../includes/SavingsGoal.php';

$user_id = $_SESSION['user_id'];
$goals = SavingsGoal::all($user_id);
?>
<h2>Savings Goals</h2>

<?php if (empty($goals)): ?>
    <p>No goals yet. <a href="add.php">Create one</a></p>
<?php else: ?>
    <table>
        <thead>
            <tr><th>Goal</th><th>Target (kr)</th><th>Saved (kr)</th><th>Progress</th><th>Target Date</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($goals as $g): 
            $percent = $g->getProgressPercent();
            // Auto-complete if current amount reaches target (optional, but we have updateProgress method)
            if (!$g->isCompleted && $g->currentAmount >= $g->targetAmount) {
                $g->updateProgress(0); // this will set isCompleted to 1
                $g = SavingsGoal::find($g->goalId); // refresh
            }
        ?>
            <tr>
                <td><?= htmlspecialchars($g->goalName) ?></td>
                <td><?= number_format($g->targetAmount, 2) ?> kr</td>
                <td><?= number_format($g->currentAmount, 2) ?> kr</td>
                <td><progress value="<?= $g->currentAmount ?>" max="<?= $g->targetAmount ?>"></progress> <?= $percent ?>%</td>
                <td><?= $g->targetDate ?></td>
                <td><?= $g->isCompleted ? '✅ Completed' : '⏳ In progress' ?></td>
                <td>
                    <a href="edit.php?id=<?= $g->goalId ?>" class="btn">Edit</a>
                    <a href="delete.php?id=<?= $g->goalId ?>" class="btn btn-danger delete-link">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<a href="add.php" class="btn">+ New Goal</a>

<?php require_once '../../includes/footer.php'; ?>