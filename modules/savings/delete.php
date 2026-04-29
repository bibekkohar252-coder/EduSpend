<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireLogin();
$id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];
$pdo->prepare("DELETE FROM tblSavingsGoal WHERE GoalID = :id AND StudentID = :uid")->execute([':id'=>$id, ':uid'=>$user_id]);
header('Location: list.php');
exit;