<?php
require_once __DIR__ . '/config.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function login($email, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT StudentID, FullName, Email, PasswordHash FROM tblStudent WHERE Email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['PasswordHash'])) {
        $_SESSION['user_id'] = $user['StudentID'];
        $_SESSION['user_name'] = $user['FullName'];
        $_SESSION['user_email'] = $user['Email'];
        return true;
    }
    return false;
}

function logout() {
    $_SESSION = [];
    session_destroy();
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /EduSpend/login.php');
        exit;
    }
}
?>