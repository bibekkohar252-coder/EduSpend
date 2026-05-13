<?php
$page_title = 'Edit Savings Goal';
require_once '../../includes/header.php';
require_once '../../includes/SavingsGoal.php';

$user_id = $_SESSION['user_id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$goal = SavingsGoal::find($id);
if (!$goal || $goal->studentId != $user_id) {
    header('Location: list.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['goal_name']);
    $target = trim($_POST['target_amount']);
    $current = trim($_POST['current_amount']);
    $date = trim($_POST['target_date']);
    $completed = isset($_POST['is_completed']) ? 1 : 0;

    if (empty($name)) {
        $error = 'Goal name is required.';
    } elseif (!is_numeric($target) || $target <= 0) {
        $error = 'Target amount must be greater than zero.';
    } elseif (!is_numeric($current) || $current < 0) {
        $error = 'Current amount cannot be negative.';
    } elseif (empty($date) || strtotime($date) <= time()) {
        $error = 'Target date must be in the future.';
    } else {
        $goal->goalName = $name;
        $goal->targetAmount = $target;
        $goal->currentAmount = $current;
        $goal->targetDate = $date;
        $goal->isCompleted = $completed;

        if ($goal->save()) {
            $success = 'Savings goal updated successfully!';
            // Reload the object to reflect changes (optional)
            $goal = SavingsGoal::find($id);
        } else {
            $error = 'Database error: could not update goal.';
        }
    }
}
?>
<h2>✏️ Edit Savings Goal</h2>
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?> <a href="list.php">Back to list</a></div><?php endif; ?>

<form method="post">
    <div class="form-group">
        <label>Goal Name:</label>
        <input type="text" name="goal_name" value="<?= htmlspecialchars($goal->goalName) ?>" required>
    </div>
    <div class="form-group">
        <label>Target Amount (kr):</label>
        <input type="number" step="0.01" name="target_amount" value="<?= $goal->targetAmount ?>" required>
    </div>
    <div class="form-group">
        <label>Current Amount (kr):</label>
        <input type="number" step="0.01" name="current_amount" value="<?= $goal->currentAmount ?>">
    </div>
    <div class="form-group">
        <label>Target Date:</label>
        <input type="date" name="target_date" value="<?= $goal->targetDate ?>" required>
    </div>
    <div class="form-group">
        <label><input type="checkbox" name="is_completed" <?= $goal->isCompleted ? 'checked' : '' ?>> Mark as completed</label>
    </div>
    <button type="submit" class="btn">Update Goal</button>
    <a href="list.php" class="btn btn-secondary">Cancel</a>
</form>

<?php require_once '../../includes/footer.php'; ?>