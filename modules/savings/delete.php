<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/SavingsGoal.php';

requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$goal = SavingsGoal::find($id);
if ($goal && $goal->studentId == $_SESSION['user_id']) {
    $goal->delete();
}
header('Location: list.php');
exit;
?>