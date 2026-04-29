<?php
$page_title = 'Savings Goals';
require_once '../../includes/header.php';
$user_id = $_SESSION['user_id'];
$goals = $pdo->prepare("SELECT * FROM tblSavingsGoal WHERE StudentID = :uid ORDER BY TargetDate ASC");
$goals->execute([':uid'=>$user_id]);
$goals = $goals->fetchAll();
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
            $percent = $g['TargetAmount'] > 0 ? round(($g['CurrentAmount'] / $g['TargetAmount']) * 100) : 0;
            $completed = $g['IsCompleted'] || $g['CurrentAmount'] >= $g['TargetAmount'];
            if (!$g['IsCompleted'] && $g['CurrentAmount'] >= $g['TargetAmount']) {
                $pdo->prepare("UPDATE tblSavingsGoal SET IsCompleted = 1 WHERE GoalID = :id")->execute([':id'=>$g['GoalID']]);
                $g['IsCompleted'] = 1;
            }
        ?>
            <tr>
                <td><?= htmlspecialchars($g['GoalName']) ?></td>
                <td><?= number_format($g['TargetAmount'],2) ?> kr</td>
                <td><?= number_format($g['CurrentAmount'],2) ?> kr</td>
                <td><progress value="<?= $g['CurrentAmount'] ?>" max="<?= $g['TargetAmount'] ?>"></progress> <?= $percent ?>%</td>
                <td><?= $g['TargetDate'] ?></td>
                <td><?= $g['IsCompleted'] ? '✅ Completed' : '⏳ In progress' ?></td>
                <td><a href="edit.php?id=<?= $g['GoalID'] ?>" class="btn">Edit</a> <a href="delete.php?id=<?= $g['GoalID'] ?>" class="btn btn-danger delete-link">Delete</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<a href="add.php" class="btn">+ New Goal</a>
<?php require_once '../../includes/footer.php'; ?>