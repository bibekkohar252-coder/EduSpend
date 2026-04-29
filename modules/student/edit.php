<?php
$page_title = 'Edit Profile';
require_once '../../includes/header.php';
$uid = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM tblStudent WHERE StudentID = :id");
$stmt->execute([':id' => $uid]);
$user = $stmt->fetch();
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $dob = $_POST['dob'];
    if (empty($name) || empty($email) || empty($dob)) {
        $error = 'All fields required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email.';
    } else {
        $pdo->prepare("UPDATE tblStudent SET FullName = :name, Email = :email, DateOfBirth = :dob WHERE StudentID = :id")
            ->execute([':name'=>$name, ':email'=>$email, ':dob'=>$dob, ':id'=>$uid]);
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $success = 'Profile updated.';
        $user['FullName'] = $name; $user['Email'] = $email; $user['DateOfBirth'] = $dob;
    }
}
?>
<h2>Edit Profile</h2>
<?php if($error):?><div class="alert alert-error"><?=$error?></div><?php endif; ?>
<?php if($success):?><div class="alert alert-success"><?=$success?> <a href="profile.php">View</a></div><?php endif; ?>
<form method="post">
    <div class="form-group"><label>Full Name:</label><input type="text" name="fullname" value="<?=htmlspecialchars($user['FullName'])?>" required></div>
    <div class="form-group"><label>Email:</label><input type="email" name="email" value="<?=htmlspecialchars($user['Email'])?>" required></div>
    <div class="form-group"><label>Date of Birth:</label><input type="date" name="dob" value="<?=$user['DateOfBirth']?>" required></div>
    <button type="submit" class="btn">Save Changes</button>
    <a href="profile.php" class="btn btn-secondary">Cancel</a>
</form>
<?php require_once '../../includes/footer.php'; ?>