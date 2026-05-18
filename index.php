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

$totalSavings = $pdo->prepare("SELECT SUM(CurrentAmount) FROM tblSavingsGoal WHERE StudentID = ? AND IsCompleted = 0");
$totalSavings->execute([$user_id]);
$totalSavings = $totalSavings->fetchColumn() ?? 0;

$savingsGoal = $pdo->prepare("SELECT SUM(TargetAmount) FROM tblSavingsGoal WHERE StudentID = ? AND IsCompleted = 0");
$savingsGoal->execute([$user_id]);
$savingsGoal = $savingsGoal->fetchColumn() ?? 0;

// Get recent expenses (last 5)
$recentExpenses = $pdo->prepare("
    SELECT e.*, c.CategoryName 
    FROM tblExpense e 
    JOIN tblCategory c ON e.CategoryID = c.CategoryID 
    WHERE e.StudentID = ? 
    ORDER BY e.ExpenseDate DESC 
    LIMIT 5
");
$recentExpenses->execute([$user_id]);
$recentExpenses = $recentExpenses->fetchAll();

// Get recent income (last 5)
$recentIncome = $pdo->prepare("
    SELECT * FROM tblIncome 
    WHERE StudentID = ? 
    ORDER BY IncomeDate DESC 
    LIMIT 5
");
$recentIncome->execute([$user_id]);
$recentIncome = $recentIncome->fetchAll();

// Get active savings goals
$activeGoals = $pdo->prepare("
    SELECT * FROM tblSavingsGoal 
    WHERE StudentID = ? AND IsCompleted = 0 
    ORDER BY TargetDate ASC 
    LIMIT 3
");
$activeGoals->execute([$user_id]);
$activeGoals = $activeGoals->fetchAll();

// Calculate monthly data
$currentMonth = date('Y-m');
$monthlyExpenses = $pdo->prepare("SELECT SUM(Amount) FROM tblExpense WHERE StudentID = ? AND DATE_FORMAT(ExpenseDate, '%Y-%m') = ?");
$monthlyExpenses->execute([$user_id, $currentMonth]);
$monthlyExpenses = $monthlyExpenses->fetchColumn() ?? 0;

$monthlyIncome = $pdo->prepare("SELECT SUM(Amount) FROM tblIncome WHERE StudentID = ? AND DATE_FORMAT(IncomeDate, '%Y-%m') = ?");
$monthlyIncome->execute([$user_id, $currentMonth]);
$monthlyIncome = $monthlyIncome->fetchColumn() ?? 0;

$netBalance = $totalIncome - $totalExpenses;
$monthlyBalance = $monthlyIncome - $monthlyExpenses;

// Category spending breakdown
$categorySpending = $pdo->prepare("
    SELECT c.CategoryName, COALESCE(SUM(e.Amount), 0) as TotalSpent
    FROM tblCategory c
    LEFT JOIN tblExpense e ON c.CategoryID = e.CategoryID AND e.StudentID = ?
    WHERE c.IsActive = 1
    GROUP BY c.CategoryID
    ORDER BY TotalSpent DESC
    LIMIT 5
");
$categorySpending->execute([$user_id]);
$categorySpending = $categorySpending->fetchAll();

// Income breakdown by source
$incomeSources = $pdo->prepare("
    SELECT Source, SUM(Amount) as TotalIncome
    FROM tblIncome
    WHERE StudentID = ?
    GROUP BY Source
    ORDER BY TotalIncome DESC
    LIMIT 5
");
$incomeSources->execute([$user_id]);
$incomeSources = $incomeSources->fetchAll();

// Get quote of the day
$quotes = [
    "The best time to start saving was yesterday. The second best time is today.",
    "Don't tell me where your priorities are. Show me where you spend your money.",
    "A budget is telling your money where to go instead of wondering where it went.",
    "Saving money is a journey, not a destination.",
    "Financial freedom is available to those who learn about it and work for it.",
    "The habit of saving is itself an education.",
    "It's not about how much money you make, it's about how much you keep."
];
$quoteOfDay = $quotes[array_rand($quotes)];
?>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Chart.js for Charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* Dashboard Theme (same as before) */
    .dashboard-module {
        --dashboard-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --dashboard-success: linear-gradient(135deg, #10b981 0%, #059669 100%);
        --dashboard-danger: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        --dashboard-warning: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        --dashboard-info: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }
    
    /* Welcome Hero */
    .dashboard-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        border-radius: 30px;
        padding: 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
    }
    
    .dashboard-hero::before {
        content: "📊";
        position: absolute;
        font-size: 10rem;
        right: -20px;
        bottom: -30px;
        opacity: 0.1;
        pointer-events: none;
    }
    
    .dashboard-hero::after {
        content: "💰";
        position: absolute;
        font-size: 6rem;
        left: -20px;
        top: -20px;
        opacity: 0.08;
        pointer-events: none;
    }
    
    .welcome-text h1 {
        font-family: 'Montserrat', sans-serif;
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        background: linear-gradient(135deg, #fff 0%, #f3e8ff 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .welcome-text p {
        font-family: 'Poppins', sans-serif;
        color: rgba(255,255,255,0.95);
        margin-bottom: 1rem;
    }
    
    .quote-card {
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 1rem;
        margin-top: 1rem;
        border: 1px solid rgba(255,255,255,0.2);
    }
    
    .quote-card i {
        font-size: 1.5rem;
        margin-right: 0.5rem;
        opacity: 0.8;
    }
    
    .quote-card p {
        font-family: 'Poppins', sans-serif;
        font-style: italic;
        margin: 0;
        font-size: 0.9rem;
    }
    
    /* Stats Grid */
    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.2rem;
        margin-bottom: 2rem;
    }
    
    .stat-card-dashboard {
        background: white;
        border-radius: 24px;
        padding: 1.2rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid rgba(102, 126, 234, 0.1);
    }
    
    .stat-card-dashboard:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -12px rgba(102, 126, 234, 0.2);
    }
    
    .stat-card-dashboard::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--dashboard-primary);
    }
    
    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.8rem;
    }
    
    .stat-header h3 {
        font-family: 'Poppins', sans-serif;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #6b7280;
        margin: 0;
    }
    
    .stat-icon-dash {
        font-size: 1.8rem;
        opacity: 0.3;
    }
    
    .stat-number-dash {
        font-family: 'Montserrat', sans-serif;
        font-size: 2rem;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 0.3rem;
    }
    
    .stat-trend {
        font-size: 0.7rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    
    .trend-up { color: #10b981; }
    .trend-down { color: #ef4444; }
    
    /* Charts Row */
    .charts-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .chart-card {
        background: white;
        border-radius: 24px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid rgba(102, 126, 234, 0.1);
        transition: all 0.3s;
    }
    
    .chart-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    }
    
    .chart-card h3 {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    canvas {
        max-height: 250px;
        width: 100%;
    }
    
    /* Recent Activity Section */
    .recent-section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .recent-card {
        background: white;
        border-radius: 24px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid rgba(102, 126, 234, 0.1);
    }
    
    .recent-card h3 {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        justify-content: space-between;
    }
    
    .recent-card h3 a {
        font-size: 0.8rem;
        color: #667eea;
        text-decoration: none;
    }
    
    .activity-list {
        list-style: none;
        padding: 0;
    }
    
    .activity-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.8rem 0;
        border-bottom: 1px solid #f3f4f6;
        transition: all 0.2s;
    }
    
    .activity-item:hover {
        background: #f9fafb;
        transform: translateX(5px);
    }
    
    .activity-info {
        flex: 1;
    }
    
    .activity-title {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        font-size: 0.9rem;
        color: #1f2937;
    }
    
    .activity-date {
        font-size: 0.7rem;
        color: #6b7280;
    }
    
    .activity-amount {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        font-size: 1rem;
    }
    
    .amount-expense {
        color: #ef4444;
    }
    
    .amount-income {
        color: #10b981;
    }
    
    .empty-activity {
        text-align: center;
        padding: 2rem;
        color: #6b7280;
    }
    
    /* Savings Goals Preview */
    .goals-preview {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border-radius: 24px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .goals-preview h3 {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .goal-item {
        background: white;
        border-radius: 16px;
        padding: 1rem;
        margin-bottom: 0.8rem;
        transition: all 0.2s;
    }
    
    .goal-item:hover {
        transform: translateX(5px);
    }
    
    .goal-name-bar {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        font-family: 'Poppins', sans-serif;
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .goal-progress-bar {
        background: #e5e7eb;
        border-radius: 50px;
        height: 8px;
        overflow: hidden;
    }
    
    .goal-progress-fill {
        background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
        border-radius: 50px;
        height: 100%;
        transition: width 0.5s ease;
    }
    
    /* Quick Actions */
    .quick-actions-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        justify-content: center;
        margin-top: 1rem;
    }
    
    .action-btn {
        background: white;
        padding: 0.8rem 1.5rem;
        border-radius: 50px;
        text-decoration: none;
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid #e5e7eb;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    }
    
    .action-btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
    }
    
    @media (max-width: 768px) {
        .welcome-text h1 {
            font-size: 1.5rem;
        }
        
        .charts-row {
            grid-template-columns: 1fr;
        }
        
        .recent-section {
            grid-template-columns: 1fr;
        }
        
        .quick-actions-grid {
            flex-direction: column;
        }
        
        .action-btn {
            justify-content: center;
        }
    }
</style>

<!-- Dashboard Content -->
<div class="dashboard-hero">
    <div class="welcome-text">
        <h1>Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>! 👋</h1>
        <p>Here's your financial snapshot for <?= date('F j, Y') ?></p>
        
        <div class="quote-card">
            <i class="fas fa-quote-left"></i>
            <p>"<?= $quoteOfDay ?>"</p>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="dashboard-stats">
   <div class="stat-card-dashboard" onclick="window.location.href='modules/expense/view.php'">
    <div class="stat-header">
        <h3>Total Expenses</h3>
        <div class="stat-icon-dash">💰</div>
    </div>
    <div class="stat-number-dash" style="color: #ef4444;"><?= number_format($totalExpenses, 0) ?> kr</div>
    <div class="stat-trend">
        <i class="fas fa-calendar-alt"></i> This month: <?= number_format($monthlyExpenses, 0) ?> kr
    </div>
</div>
    
    <div class="stat-card-dashboard" onclick="window.location.href='modules/income/list.php'">
        <div class="stat-header">
            <h3>Total Income</h3>
            <div class="stat-icon-dash">💵</div>
        </div>
        <div class="stat-number-dash"><?= number_format($totalIncome, 0) ?> kr</div>
        <div class="stat-trend trend-up">
            <i class="fas fa-calendar-alt"></i> This month: <?= number_format($monthlyIncome, 0) ?> kr
        </div>
    </div>
    
    <div class="stat-card-dashboard" onclick="window.location.href='modules/savings/list.php'">
        <div class="stat-header">
            <h3>Total Saved</h3>
            <div class="stat-icon-dash">🏦</div>
        </div>
        <div class="stat-number-dash"><?= number_format($totalSavings, 0) ?> kr</div>
        <div class="stat-trend">
            <i class="fas fa-bullseye"></i> Goal: <?= number_format($savingsGoal, 0) ?> kr
        </div>
    </div>
    
    <div class="stat-card-dashboard">
        <div class="stat-header">
            <h3>Net Balance</h3>
            <div class="stat-icon-dash">📊</div>
        </div>
        <div class="stat-number-dash" style="color: <?= $netBalance >= 0 ? '#10b981' : '#ef4444' ?>">
            <?= number_format($netBalance, 0) ?> kr
        </div>
        <div class="stat-trend">
            <i class="fas fa-chart-line"></i> Monthly: <?= number_format($monthlyBalance, 0) ?> kr
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="charts-row">
    <!-- Category Spending Chart (unchanged) -->
    <div class="chart-card">
        <h3><i class="fas fa-chart-pie" style="color: #667eea;"></i> Category Spending Breakdown</h3>
        <canvas id="spendingChart"></canvas>
    </div>
    
    <!-- NEW: Income Breakdown by Source -->
    <div class="chart-card">
        <h3><i class="fas fa-coins" style="color: #10b981;"></i> Income by Source</h3>
        <?php if (empty($incomeSources)): ?>
            <div class="empty-activity" style="padding: 2rem;">
                <i class="fas fa-dollar-sign" style="font-size: 2rem; opacity: 0.3;"></i>
                <p>No income records yet.<br>Add your first income to see breakdown.</p>
                <a href="modules/income/add.php" class="action-btn" style="margin-top: 0.5rem;">Add Income</a>
            </div>
        <?php else: ?>
            <canvas id="incomeChart"></canvas>
        <?php endif; ?>
    </div>
</div>

<!-- Recent Activity Section (same) -->
<div class="recent-section">
    <div class="recent-card">
        <h3>
            <span><i class="fas fa-receipt" style="color: #ef4444;"></i> Recent Expenses</span>
            <a href="modules/expense/view.php">View All →</a>
        </h3>
        <?php if (empty($recentExpenses)): ?>
            <div class="empty-activity">
                <i class="fas fa-coffee" style="font-size: 2rem; opacity: 0.3;"></i>
                <p>No expenses yet. Add your first expense!</p>
                <a href="modules/expense/add.php" class="action-btn" style="margin-top: 0.5rem;">Add Expense</a>
            </div>
        <?php else: ?>
            <div class="activity-list">
                <?php foreach ($recentExpenses as $expense): ?>
                    <div class="activity-item">
                        <div class="activity-info">
                            <div class="activity-title"><?= htmlspecialchars($expense['CategoryName']) ?></div>
                            <div class="activity-date"><?= date('M d, Y', strtotime($expense['ExpenseDate'])) ?></div>
                        </div>
                        <div class="activity-amount amount-expense">
                            -<?= number_format($expense['Amount'], 0) ?> kr
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="recent-card">
        <h3>
            <span><i class="fas fa-coins" style="color: #10b981;"></i> Recent Income</span>
            <a href="modules/income/list.php">View All →</a>
        </h3>
        <?php if (empty($recentIncome)): ?>
            <div class="empty-activity">
                <i class="fas fa-dollar-sign" style="font-size: 2rem; opacity: 0.3;"></i>
                <p>No income records. Add your first income!</p>
                <a href="modules/income/add.php" class="action-btn" style="margin-top: 0.5rem;">Add Income</a>
            </div>
        <?php else: ?>
            <div class="activity-list">
                <?php foreach ($recentIncome as $income): ?>
                    <div class="activity-item">
                        <div class="activity-info">
                            <div class="activity-title"><?= htmlspecialchars($income['Source']) ?></div>
                            <div class="activity-date"><?= date('M d, Y', strtotime($income['IncomeDate'])) ?></div>
                        </div>
                        <div class="activity-amount amount-income">
                            +<?= number_format($income['Amount'], 0) ?> kr
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Active Savings Goals (same) -->
<?php if (!empty($activeGoals)): ?>
<div class="goals-preview">
    <h3><i class="fas fa-piggy-bank"></i> Active Savings Goals</h3>
    <?php foreach ($activeGoals as $goal): 
        $percent = $goal['TargetAmount'] > 0 ? round(($goal['CurrentAmount'] / $goal['TargetAmount']) * 100) : 0;
    ?>
        <div class="goal-item">
            <div class="goal-name-bar">
                <span><?= htmlspecialchars($goal['GoalName']) ?></span>
                <span><?= number_format($goal['CurrentAmount'], 0) ?> / <?= number_format($goal['TargetAmount'], 0) ?> kr</span>
            </div>
            <div class="goal-progress-bar">
                <div class="goal-progress-fill" style="width: <?= min(100, $percent) ?>%"></div>
            </div>
            <div style="font-size: 0.7rem; color: #6b7280; margin-top: 0.3rem;">
                <i class="fas fa-calendar"></i> Target: <?= date('M d, Y', strtotime($goal['TargetDate'])) ?>
            </div>
        </div>
    <?php endforeach; ?>
    <div style="text-align: center; margin-top: 1rem;">
        <a href="modules/savings/list.php" class="action-btn">View All Goals →</a>
    </div>
</div>
<?php endif; ?>

<!-- Quick Actions -->
<div class="quick-actions-grid">
    <a href="modules/expense/add.php" class="action-btn">
        <i class="fas fa-plus-circle" style="color: #ef4444;"></i> Add Expense
    </a>
    <a href="modules/income/add.php" class="action-btn">
        <i class="fas fa-plus-circle" style="color: #10b981;"></i> Add Income
    </a>
    <a href="modules/savings/add.php" class="action-btn">
        <i class="fas fa-plus-circle" style="color: #f59e0b;"></i> New Savings Goal
    </a>
    <a href="modules/expense/view.php" class="action-btn">
        <i class="fas fa-chart-line"></i> View Reports
    </a>
    <a href="modules/student/profile.php" class="action-btn action-btn-primary">
        <i class="fas fa-user"></i> My Profile
    </a>
</div>

<script>
// Category Spending Chart (unchanged)
const ctx1 = document.getElementById('spendingChart').getContext('2d');
const categoryNames = <?= json_encode(array_column($categorySpending, 'CategoryName')) ?>;
const categoryAmounts = <?= json_encode(array_column($categorySpending, 'TotalSpent')) ?>;

if (categoryNames.length > 0) {
    new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: categoryNames,
            datasets: [{
                data: categoryAmounts,
                backgroundColor: ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#10b981'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom', labels: { font: { family: 'Poppins', size: 11 } } }
            }
        }
    });
} else {
    ctx1.canvas.parentElement.innerHTML = '<div class="empty-activity">No expense data yet.</div>';
}

// Income Breakdown Chart (NEW)
<?php if (!empty($incomeSources)): ?>
const ctxIncome = document.getElementById('incomeChart').getContext('2d');
const incomeLabels = <?= json_encode(array_column($incomeSources, 'Source')) ?>;
const incomeValues = <?= json_encode(array_column($incomeSources, 'TotalIncome')) ?>;
new Chart(ctxIncome, {
    type: 'pie',
    data: {
        labels: incomeLabels,
        datasets: [{
            data: incomeValues,
            backgroundColor: ['#10b981', '#34d399', '#6ee7b7', '#a7f3d0', '#059669'],
            borderWidth: 0,
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { position: 'bottom', labels: { font: { family: 'Poppins', size: 11 } } }
        }
    }
});
<?php endif; ?>

// Animate stat numbers (same as before)
document.addEventListener('DOMContentLoaded', function() {
    const statNumbers = document.querySelectorAll('.stat-number-dash');
    statNumbers.forEach(stat => {
        const finalValue = stat.innerText;
        if (parseInt(finalValue) > 0) {
            stat.innerText = '0';
            let current = 0;
            const increment = parseInt(finalValue) / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= parseInt(finalValue)) {
                    stat.innerText = finalValue;
                    clearInterval(timer);
                } else {
                    stat.innerText = Math.floor(current);
                }
            }, 20);
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>