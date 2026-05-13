<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/Category.php';

requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$category = Category::find($id);
if ($category) {
    global $pdo;
    // Delete all expenses that use this category
    $stmt = $pdo->prepare("DELETE FROM tblExpense WHERE CategoryID = ?");
    $stmt->execute([$id]);
    // Now delete the category
    $category->delete();
}
header('Location: list.php');
exit;
?>