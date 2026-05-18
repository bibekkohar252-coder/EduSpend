<?php
require_once __DIR__ . '/auth.php';
requireLogin();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduSpend – <?= $page_title ?? 'Student Finance' ?></title>
    <link rel="stylesheet" href="/EduSpend/assets/css/style.css">
    <script src="/EduSpend/assets/js/main.js" defer></script>
</head>
<body>
<nav class="navbar glass">
    <a href="/EduSpend/index.php" class="logo-link">
        <div class="logo">
            <img src="/EduSpend/assets/images/logo.jpeg" alt="EduSpend Logo" class="logo-img">
            <div class="logo-text">
                <span class="logo-main">EduSpend</span>
                <span class="logo-tagline">TRACK·PLAN·SAVE·GROW</span>
            </div>
        </div>
    </a>
    <ul class="nav-links">
        <li><a href="/EduSpend/index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">Dashboard</a></li>
        <li><a href="/EduSpend/modules/expense/view.php">Expenses</a></li>
        <li><a href="/EduSpend/modules/category/list.php">Categories</a></li>
        <li><a href="/EduSpend/modules/student/profile.php">Profile</a></li>
        <li><a href="/EduSpend/modules/income/list.php">Income</a></li>
        <li><a href="/EduSpend/modules/savings/list.php">Savings</a></li>
    </ul>
    <div class="user-info">
        👤 <?= htmlspecialchars($_SESSION['user_name']) ?>
        <a href="/EduSpend/logout.php" class="logout-btn">Logout</a>
    </div>
</nav>
<main class="container">