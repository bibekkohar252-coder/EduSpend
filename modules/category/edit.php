<?php
$page_title = 'Edit Category';
require_once '../../includes/header.php';
$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM tblCategory WHERE CategoryID = :id");
$stmt->execute([':id' => $id]);
$cat = $stmt->fetch();
if (!$cat) { header('Location: list.php'); exit; }
$error = ''; $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $budget = trim($_POST['budget_limit']);
    $active = isset($_POST['is_active']) ? 1 : 0;
    $desc = trim($_POST['description']);
    if (empty($name)) $error = 'Name required.';
    elseif (!is_numeric($budget) || $budget < 0) $error = 'Budget must be >=0.';
    else {
        $sql = "UPDATE tblCategory SET CategoryName=:name, BudgetLimit=:budget, IsActive=:active, Description=:desc WHERE CategoryID=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':name'=>$name, ':budget'=>$budget, ':active'=>$active, ':desc'=>$desc, ':id'=>$id]);
        $success = 'Updated.';
        $cat = array_merge($cat, ['CategoryName'=>$name, 'BudgetLimit'=>$budget, 'IsActive'=>$active, 'Description'=>$desc]);
    }
}
?>
<h2>Edit Category</h2>
<?php if($error):?><div class="alert alert-error"><?=$error?></div><?php endif; ?>
<?php if($success):?><div class="alert alert-success"><?=$success?> <a href="list.php">Back</a></div><?php endif; ?>
<form method="post">
    <div class="form-group"><label>Name:</label><input type="text" name="name" value="<?=htmlspecialchars($cat['CategoryName'])?>" required></div>
    <div class="form-group"><label>Budget Limit:</label><input type="number" step="0.01" name="budget_limit" value="<?=$cat['BudgetLimit']?>"></div>
    <div class="form-group"><label><input type="checkbox" name="is_active" <?=$cat['IsActive']?'checked':''?>> Active</label></div>
    <div class="form-group"><label>Description:</label><textarea name="description" rows="2"><?=htmlspecialchars($cat['Description'])?></textarea></div>
    <button type="submit" class="btn">Update</button>
    <a href="list.php" class="btn btn-secondary">Cancel</a>
</form>
<?php require_once '../../includes/footer.php'; ?>