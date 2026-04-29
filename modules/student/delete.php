<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireLogin();
$uid = $_SESSION['user_id'];
// Delete all dependent records first (expenses, income, savings) – optional, for simplicity we can just delete student if foreign keys are CASCADE. If not, you need to delete related records.
// We assume foreign keys are set to CASCADE in your SQL. If not, add:
$pdo->prepare("DELETE FROM tblExpense WHERE StudentID = ?")->execute([$uid]);
$pdo->prepare("DELETE FROM tblIncome WHERE StudentID = ?")->execute([$uid]);
$pdo->prepare("DELETE FROM tblSavingsGoal WHERE StudentID = ?")->execute([$uid]);
$pdo->prepare("DELETE FROM tblStudent WHERE StudentID = ?")->execute([$uid]);
logout();
header('Location: /EduSpend/login.php');
exit;
?>