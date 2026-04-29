<?php
$page_title = 'Dashboard';
require_once 'includes/header.php';

$user_id = $_SESSION['user_id'];

// Get quick stats for dashboard
$totalExpenses = $pdo->prepare("SELECT SUM(Amount) FROM tblExpense WHERE StudentID = ?");
$totalExpenses->execute([$user_id]);
$totalExpenses = $totalExpenses->fetchColumn() ?? 0;

$totalIncome = $pdo->prepare("SELECT SUM(Amount) FROM tblIncome WHERE StudentID = ?");
$totalIncome->execute([$user_id]);
$totalIncome = $totalIncome->fetchColumn() ?? 0;

$savingsGoal = $pdo->prepare("SELECT SUM(TargetAmount) FROM tblSavingsGoal WHERE StudentID = ? AND IsCompleted = 0");
$savingsGoal->execute([$user_id]);
$savingsGoal = $savingsGoal->fetchColumn() ?? 0;
?>
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .stat-card {
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        border-radius: 24px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05);
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-4px);
    }
    .stat-card h3 {
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--gray);
        margin-bottom: 0.5rem;
    }
    .stat-card .amount {
        font-size: 2rem;
        font-weight: bold;
        color: var(--dark);
    }
    .dashboard-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-top: 1rem;
    }
</style>

<h1>📘 Dashboard</h1>
<p>Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>! Here's your financial snapshot.</p>

<div class="stats-grid">
    <div class="stat-card">
        <h3>💰 Total Expenses</h3>
        <div class="amount"><?= number_format($totalExpenses, 2) ?> kr</div>
    </div>
    <div class="stat-card">
        <h3>💵 Total Income</h3>
        <div class="amount"><?= number_format($totalIncome, 2) ?> kr</div>
    </div>
    <div class="stat-card">
        <h3>🎯 Active Savings Goal</h3>
        <div class="amount"><?= number_format($savingsGoal, 2) ?> kr</div>
    </div>
</div>

<div class="dashboard-actions">
    <a href="modules/expense/add.php" class="btn">➕ Add Expense</a>
    <a href="modules/expense/view.php" class="btn btn-secondary">📋 View Expenses</a>
    <a href="modules/income/add.php" class="btn btn-secondary">💰 Add Income</a>
    <a href="modules/savings/add.php" class="btn btn-secondary">🎯 New Savings Goal</a>
    <a href="modules/student/profile.php" class="btn btn-secondary">👤 My Profile</a>
</div>

<?php require_once 'includes/footer.php'; ?>