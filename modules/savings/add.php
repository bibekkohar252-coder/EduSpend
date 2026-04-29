<?php
$page_title = 'Add Savings Goal';
require_once '../../includes/header.php';
$user_id = $_SESSION['user_id'];
$error=''; $success='';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['goal_name']);
    $target = trim($_POST['target_amount']);
    $current = trim($_POST['current_amount']);
    $date = trim($_POST['target_date']);
    if (empty($name)) $error='Goal name required.';
    elseif (!is_numeric($target) || $target <=0) $error='Target must be >0.';
    elseif (!is_numeric($current) || $current <0) $error='Current amount cannot be negative.';
    elseif (empty($date) || strtotime($date) <= time()) $error='Target date must be in future.';
    else {
        $sql = "INSERT INTO tblSavingsGoal (GoalName, TargetAmount, CurrentAmount, TargetDate, StudentID)
                VALUES (:name, :target, :current, :date, :uid)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':name'=>$name, ':target'=>$target, ':current'=>$current, ':date'=>$date, ':uid'=>$user_id]);
        $success = 'Goal created!';
    }
}
?>
<h2>🎯 Add Savings Goal</h2>
<?php if($error):?><div class="alert alert-error"><?=$error?></div><?php endif; ?>
<?php if($success):?><div class="alert alert-success"><?=$success?> <a href="list.php">View goals</a></div><?php endif; ?>
<form method="post">
    <div class="form-group"><label>Goal Name:</label><input type="text" name="goal_name" required></div>
    <div class="form-group"><label>Target Amount (£):</label><input type="number" step="0.01" name="target_amount" required></div>
    <div class="form-group"><label>Current Amount (£):</label><input type="number" step="0.01" name="current_amount" value="0"></div>
    <div class="form-group"><label>Target Date:</label><input type="date" name="target_date" required></div>
    <button type="submit" class="btn">Create Goal</button>
    <a href="list.php" class="btn btn-secondary">Cancel</a>
</form>
<?php require_once '../../includes/footer.php'; ?>