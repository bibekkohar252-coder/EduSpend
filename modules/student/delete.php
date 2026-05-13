<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/Student.php';

requireLogin();

$uid = $_SESSION['user_id'];
$student = Student::find($uid);
if ($student) {
    $student->deleteAccount();
}
logout();
header('Location: /EduSpend/login.php');
exit;
?>