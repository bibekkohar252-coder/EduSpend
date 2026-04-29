<?php
$page_title = 'Register';
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

// If already logged in, go to dashboard
if (isLoggedIn()) {
    header('Location: /EduSpend/index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $dob = $_POST['dob'];

    // Validation
    if (empty($fullname) || empty($email) || empty($password) || empty($dob)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT StudentID FROM tblStudent WHERE Email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $error = 'Email is already registered. Please login.';
        } else {
            // Hash password
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO tblStudent (FullName, Email, PasswordHash, DateOfBirth)
                    VALUES (:name, :email, :hash, :dob)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $fullname,
                ':email' => $email,
                ':hash' => $hashed,
                ':dob' => $dob
            ]);
            $success = 'Registration successful! You can now login.';
            // Clear form (optional)
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register – EduSpend</title>
    <link rel="stylesheet" href="/EduSpend/assets/css/style.css">
</head>
<body class="bg-gradient">
<div class="container" style="max-width: 600px; margin-top: 5vh;">
    <h2>📝 Create an Account</h2>
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?> <a href="/EduSpend/login.php">Login here</a>
        </div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label>Full Name:</label>
            <input type="text" name="fullname" required value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Password (min 6 characters):</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" required>
        </div>
        <div class="form-group">
            <label>Date of Birth:</label>
            <input type="date" name="dob" required value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>">
        </div>
        <button type="submit" class="btn">Register</button>
        <a href="/EduSpend/login.php" class="btn btn-secondary">Already have an account? Login</a>
    </form>
</div>
</body>
</html>