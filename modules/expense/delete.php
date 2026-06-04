<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/Expense.php';

requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$expense = Expense::find($id, false);
if ($expense && $expense->studentId == $_SESSION['user_id']) {
    $expense->softDelete();
}
header('Location: view.php');
exit;
?>