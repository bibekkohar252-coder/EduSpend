<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/Income.php';

requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$income = Income::find($id);
if ($income && $income->studentId == $_SESSION['user_id']) {
    $income->delete();
}
header('Location: list.php');
exit;
?>