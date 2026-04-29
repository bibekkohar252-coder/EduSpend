<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$expense_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($expense_id) {
    // Delete only if it belongs to the logged-in user
    $stmt = $pdo->prepare("DELETE FROM tblExpense WHERE ExpenseID = :id AND StudentID = :uid");
    $stmt->execute([':id' => $expense_id, ':uid' => $user_id]);
}

header('Location: view.php');
exit;
?>