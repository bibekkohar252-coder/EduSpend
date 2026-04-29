<?php
$page_title = 'My Profile';
require_once '../../includes/header.php';
$uid = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM tblStudent WHERE StudentID = :id");
$stmt->execute([':id' => $uid]);
$user = $stmt->fetch();
?>
<h2>My Profile</h2>
<table style="width: auto;">
    <tr><th>Name</th><td><?= htmlspecialchars($user['FullName']) ?></td></tr>
    <tr><th>Email</th><td><?= htmlspecialchars($user['Email']) ?></td></tr>
    <tr><th>Date of Birth</th><td><?= $user['DateOfBirth'] ?></td></tr>
    <tr><th>Member since</th><td><?= $user['RegistrationDate'] ?></td></tr>
</table>
<a href="edit.php" class="btn">Edit Profile</a>
<a href="delete.php" class="btn btn-danger delete-link">Delete Account</a>
<?php require_once '../../includes/footer.php'; ?>