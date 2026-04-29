<?php
$page_title = 'Edit Goal';
require_once '../../includes/header.php';
$id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM tblSavingsGoal WHERE GoalID = :id AND StudentID = :uid");
$stmt->execute([':id'=>$id, ':uid'=>$user_id]);
$goal = $stmt->fetch();
if (!$goal) header('Location: list.php');
$error=''; $success='';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['goal_name']);
    $target = trim($_POST['target_amount']);
    $current = trim($_POST['current_amount']);
    $date = trim($_POST['target_date']);
    $completed = isset($_POST['is_completed']) ? 1 : 0;
    if (empty($name)) $error='Name required.';
    elseif (!is_numeric($target) || $target <=0) $error='Target >0.';
    elseif (!is_numeric($current) || $current <0) $error='Current >=0.';
    elseif (empty($date) || strtotime($date) <= time()) $error='Date future.';
    else {
        $sql = "UPDATE tblSavingsGoal SET GoalName=:name, TargetAmount=:target, CurrentAmount=:current, TargetDate=:date, IsCompleted=:comp WHERE GoalID=:id AND StudentID=:uid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':name'=>$name, ':target'=>$target, ':current'=>$current, ':date'=>$date, ':comp'=>$completed, ':id'=>$id, ':uid'=>$user_id]);
        $success='Goal updated.';
        $goal = array_merge($goal, ['GoalName'=>$name, 'TargetAmount'=>$target, 'CurrentAmount'=>$current, 'TargetDate'=>$date, 'IsCompleted'=>$completed]);
    }
}
?>
<h2>Edit Savings Goal</h2>
<?php if($error):?><div class="alert alert-error"><?=$error?></div><?php endif; ?>
<?php if($success):?><div class="alert alert-success"><?=$success?> <a href="list.php">Back to list</a></div><?php endif; ?>
<form method="post">
    <div class="form-group"><label>Goal Name:</label><input type="text" name="goal_name" value="<?=htmlspecialchars($goal['GoalName'])?>" required></div>
    <div class="form-group"><label>Target Amount (£):</label><input type="number" step="0.01" name="target_amount" value="<?=$goal['TargetAmount']?>" required></div>
    <div class="form-group"><label>Current Amount (£):</label><input type="number" step="0.01" name="current_amount" value="<?=$goal['CurrentAmount']?>"></div>
    <div class="form-group"><label>Target Date:</label><input type="date" name="target_date" value="<?=$goal['TargetDate']?>" required></div>
    <div class="form-group"><label><input type="checkbox" name="is_completed" <?=$goal['IsCompleted']?'checked':''?>> Mark as completed</label></div>
    <button type="submit" class="btn">Update Goal</button>
    <a href="list.php" class="btn btn-secondary">Cancel</a>
</form>
<?php require_once '../../includes/footer.php'; ?>