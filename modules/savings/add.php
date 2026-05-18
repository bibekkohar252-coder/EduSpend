<?php
$page_title = 'Add Savings Goal';
require_once '../../includes/header.php';
require_once '../../includes/SavingsGoal.php';

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['goal_name']);
    $target = trim($_POST['target_amount']);
    $date = trim($_POST['target_date']);

    if (empty($name)) {
        $error = 'Goal name is required.';
    } elseif (!is_numeric($target) || $target <= 0) {
        $error = 'Target amount must be greater than zero.';
    } elseif (empty($date) || strtotime($date) <= time()) {
        $error = 'Target date must be in the future.';
    } else {
        $goal = new SavingsGoal();
        $goal->goalName = $name;
        $goal->targetAmount = $target;
        $goal->currentAmount = 0;
        $goal->targetDate = $date;
        $goal->isCompleted = 0;
        $goal->studentId = $user_id;

        if ($goal->save()) {
            $success = 'Savings goal created successfully!';
        } else {
            $error = 'Database error: could not save goal.';
        }
    }
}
?>

<h2>🎯 Add Savings Goal</h2>

<?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php elseif ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?> <a href="list.php">View goals</a></div>
<?php endif; ?>

<form method="post" class="modern-form">
    <div class="form-group">
        <label>Goal Name *</label>
        <input type="text" name="goal_name" placeholder="e.g., New Laptop, Vacation" required>
    </div>
    <div class="form-group">
        <label>Target Amount (kr) *</label>
        <input type="number" step="0.01" name="target_amount" required>
    </div>
    <div class="form-group">
        <label>Target Date *</label>
        <input type="date" name="target_date" required>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn">💾 Create Goal</button>
        <a href="list.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<?php require_once '../../includes/footer.php'; ?>