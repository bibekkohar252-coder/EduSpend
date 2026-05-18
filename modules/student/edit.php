<?php
$page_title = 'Edit Profile';
require_once '../../includes/header.php';
require_once '../../includes/Student.php';

$uid = $_SESSION['user_id'];
$student = Student::find($uid);
if (!$student) {
    header('Location: /EduSpend/login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $dob = $_POST['dob'];

    if (empty($name) || empty($email) || empty($dob)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $student->fullName = $name;
        $student->email = $email;
        $student->dateOfBirth = $dob;
        if ($student->updateProfile()) {
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $success = 'Profile updated successfully!';
        } else {
            $error = 'Database error: could not update profile.';
        }
    }
}
?>

<h2>✏️ Edit Profile</h2>

<?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?> <a href="profile.php">← Back to profile</a></div>
<?php endif; ?>

<form method="post" class="modern-form">
    <div class="form-group">
        <label>Full Name *</label>
        <input type="text" name="fullname" value="<?= htmlspecialchars($student->fullName) ?>" required>
    </div>
    <div class="form-group">
        <label>Email Address *</label>
        <input type="email" name="email" value="<?= htmlspecialchars($student->email) ?>" required>
    </div>
    <div class="form-group">
        <label>Date of Birth *</label>
        <input type="date" name="dob" value="<?= $student->dateOfBirth ?>" required>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn">💾 Save Changes</button>
        <a href="profile.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<?php require_once '../../includes/footer.php'; ?>