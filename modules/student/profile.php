<?php
$page_title = 'My Profile';
require_once '../../includes/header.php';
require_once '../../includes/Student.php';

$uid = $_SESSION['user_id'];
$student = Student::find($uid);
if (!$student) {
    header('Location: /EduSpend/login.php');
    exit;
}
?>
<h2>My Profile</h2>
<table style="width: auto;">
    <tr><th>Name</th><td><?= htmlspecialchars($student->fullName) ?></td></tr>
    <tr><th>Email</th><td><?= htmlspecialchars($student->email) ?></td></tr>
    <tr><th>Date of Birth</th><td><?= $student->dateOfBirth ?></td></tr>
    <tr><th>Member since</th><td><?= $student->registrationDate ?></td></tr>
</table>
<a href="edit.php" class="btn">Edit Profile</a>
<a href="delete.php" class="btn btn-danger delete-link">Delete Account</a>
<?php require_once '../../includes/footer.php'; ?>