<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireLogin();
$id = (int)$_GET['id'];
// Optional: check if category is used in expenses – you may want to prevent deletion or set to NULL. We'll just delete for simplicity.
$pdo->prepare("DELETE FROM tblCategory WHERE CategoryID = :id")->execute([':id'=>$id]);
header('Location: list.php');
exit;