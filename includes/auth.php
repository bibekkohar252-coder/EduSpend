<?php
// includes/auth.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Student.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Clear all failed login attempts for a given email (after successful login)
function clear_login_attempts($email) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE email = ?");
    $stmt->execute([$email]);
}

// Check if an account is locked due to too many failed attempts
function is_account_locked($email) {
    global $pdo;
    $max_attempts = 3;          // 3 failed attempts
    $lockout_minutes = 30;      // block for 30 minutes

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM login_attempts 
                           WHERE email = ? AND attempt_time > DATE_SUB(NOW(), INTERVAL ? MINUTE)");
    $stmt->execute([$email, $lockout_minutes]);
    $attempts = $stmt->fetchColumn();

    return $attempts >= $max_attempts;
}

function login($email, $password) {
    global $pdo;  // needed for inserting failed attempts

    // 1. Check if account is already locked
    if (is_account_locked($email)) {
        $_SESSION['login_error'] = "Too many failed attempts. Your account is locked for 30 minutes. Please try again later.";
        return false;
    }

    // 2. Normal login check
    $student = Student::findByEmail($email);
    if ($student && password_verify($password, $student->passwordHash)) {
        // Success: clear all failed attempts
        clear_login_attempts($email);

        $_SESSION['user_id'] = $student->studentId;
        $_SESSION['user_name'] = $student->fullName;
        $_SESSION['user_email'] = $student->email;
        session_regenerate_id(true);
        return true;
    } else {
        // Failure: record this attempt
        $stmt = $pdo->prepare("INSERT INTO login_attempts (email, attempt_time) VALUES (?, NOW())");
        $stmt->execute([$email]);
        return false;
    }
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