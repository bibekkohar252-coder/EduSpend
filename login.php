<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$lockout_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    if (empty($email) || empty($password)) {
        $error = 'Please fill in both fields.';
    } elseif (login($email, $password)) {
        header('Location: index.php');
        exit;
    } else {
        // login() already sets $_SESSION['login_error'] if account is locked
        // otherwise it just returns false, so we set a generic error
        if (!isset($_SESSION['login_error'])) {
            $error = 'Invalid email or password.';
        }
    }
}
// capture lockout error from session if present
if (isset($_SESSION['login_error'])) {
    $lockout_error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduSpend – Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* extra centering for login page only */
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 1rem;
        }
        .login-card {
            max-width: 450px;
            width: 100%;
            background: white;
            border-radius: 32px;
            padding: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            animation: fadeInUp 0.5s ease;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .login-logo {
            text-align: center;
            margin-bottom: 1rem;
        }
        .login-logo img {
            height: 50px;
            width: auto;
        }
        .login-title {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .login-title h2 {
            margin: 0;
            color: var(--dark);
        }
        .login-title p {
            color: var(--gray);
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-logo">
        <img src="assets/images/logo.jpeg" alt="EduSpend Logo">
    </div>
    <div class="login-title">
        <h2>Welcome back</h2>
        <p>Log in to manage your finances</p>
    </div>
    <?php if ($lockout_error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($lockout_error) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label>Email address</label>
            <input type="email" name="email" required autofocus>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn" style="width:100%; background: var(--primary);">Log in</button>
        <div style="text-align: center; margin-top: 1rem;">
            <a href="modules/student/register.php" style="color: var(--primary);">Create an account</a>
        </div>
    </form>
</div>
</body>
</html>