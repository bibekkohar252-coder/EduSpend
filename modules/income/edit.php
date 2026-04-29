<?php
$page_title = 'Edit Income';
require_once '../../includes/header.php';
$id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM tblIncome WHERE IncomeID = :id AND StudentID = :uid");
$stmt->execute([':id'=>$id, ':uid'=>$user_id]);
$inc = $stmt->fetch();
if (!$inc) { header('Location: list.php'); exit; }
$error=''; $success='';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = trim($_POST['amount']);
    $source = trim($_POST['source']);
    $date = trim($_POST['income_date']);
    $scholarship = isset($_POST['is_scholarship'])?1:0;
    $notes = trim($_POST['notes']);
    if (!is_numeric($amount) || $amount <=0) $error='Amount positive.';
    elseif(empty($source)) $error='Source required.';
    elseif(empty($date) || strtotime($date)>time()) $error='Invalid date.';
    else {
        $sql = "UPDATE tblIncome SET Amount=:amt, Source=:src, IncomeDate=:dt, IsScholarship=:sch, Notes=:notes WHERE IncomeID=:id AND StudentID=:uid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':amt'=>$amount, ':src'=>$source, ':dt'=>$date, ':sch'=>$scholarship, ':notes'=>$notes, ':id'=>$id, ':uid'=>$user_id]);
        $success='Updated!';
        $inc = array_merge($inc, ['Amount'=>$amount, 'Source'=>$source, 'IncomeDate'=>$date, 'IsScholarship'=>$scholarship, 'Notes'=>$notes]);
    }
}
?>
<h2>Edit Income</h2>
<?php if($error):?><div class="alert alert-error"><?=$error?></div><?php endif; ?>
<?php if($success):?><div class="alert alert-success"><?=$success?> <a href="list.php">Back</a></div><?php endif; ?>
<form method="post">
    <div class="form-group"><label>Amount:</label><input type="number" step="0.01" name="amount" value="<?=$inc['Amount']?>" required></div>
    <div class="form-group"><label>Source:</label><input type="text" name="source" value="<?=htmlspecialchars($inc['Source'])?>" required></div>
    <div class="form-group"><label>Date:</label><input type="date" name="income_date" value="<?=$inc['IncomeDate']?>" required></div>
    <div class="form-group"><label><input type="checkbox" name="is_scholarship" <?=$inc['IsScholarship']?'checked':''?>> Scholarship</label></div>
    <div class="form-group"><label>Notes:</label><textarea name="notes" rows="2"><?=htmlspecialchars($inc['Notes'])?></textarea></div>
    <button type="submit" class="btn">Update</button>
    <a href="list.php" class="btn btn-secondary">Cancel</a>
</form>
<?php require_once '../../includes/footer.php'; ?>