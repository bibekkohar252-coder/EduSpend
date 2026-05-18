<?php
// includes/auth.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Student.php';   // Include the Student class

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function login($email, $password) {
    // Use the Student class to find by email
    $student = Student::findByEmail($email);
    if ($student && password_verify($password, $student->passwordHash)) {
        $_SESSION['user_id'] = $student->studentId;
        $_SESSION['user_name'] = $student->fullName;
        $_SESSION['user_email'] = $student->email;
        session_regenerate_id(true);   // ✅ prevents session fixation
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