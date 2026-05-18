<?php
$page_title = 'My Profile';
require_once '../../includes/header.php';
require_once '../../includes/Student.php';
require_once '../../includes/SavingsGoal.php';

$uid = $_SESSION['user_id'];
$student = Student::find($uid);
if (!$student) {
    header('Location: /EduSpend/login.php');
    exit;
}

// Get quick stats
$totalExpenses = $pdo->prepare("SELECT SUM(Amount) FROM tblExpense WHERE StudentID = ?");
$totalExpenses->execute([$uid]);
$totalExpenses = $totalExpenses->fetchColumn() ?? 0;

$totalIncome = $pdo->prepare("SELECT SUM(Amount) FROM tblIncome WHERE StudentID = ?");
$totalIncome->execute([$uid]);
$totalIncome = $totalIncome->fetchColumn() ?? 0;

$netBalance = $totalIncome - $totalExpenses;

// Get the first active (not completed) savings goal, ordered by target date ascending
$goalQuery = $pdo->prepare("SELECT * FROM tblSavingsGoal WHERE StudentID = ? AND IsCompleted = 0 ORDER BY TargetDate ASC LIMIT 1");
$goalQuery->execute([$uid]);
$goalData = $goalQuery->fetch(PDO::FETCH_ASSOC);

$hasGoal = false;
$goalName = '';
$currentAmount = 0;
$targetAmount = 0;
$percent = 0;

if ($goalData) {
    $hasGoal = true;
    $goalName = htmlspecialchars($goalData['GoalName']);
    $currentAmount = floatval($goalData['CurrentAmount']);
    $targetAmount = floatval($goalData['TargetAmount']);
    $percent = $targetAmount > 0 ? round(($currentAmount / $targetAmount) * 100) : 0;
}
?>

<style>
    /* Profile page extra creativity */
    .profile-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 30px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        color: white;
    }
    .profile-hero::before {
        content: "👤";
        position: absolute;
        font-size: 10rem;
        right: -20px;
        bottom: -30px;
        opacity: 0.1;
        pointer-events: none;
    }
    .profile-hero::after {
        content: "✨";
        position: absolute;
        font-size: 6rem;
        left: -20px;
        top: -20px;
        opacity: 0.08;
        pointer-events: none;
    }
    .profile-hero h1 {
        font-size: 2.2rem;
        margin-bottom: 0.5rem;
        background: none;
        -webkit-text-fill-color: white;
        color: white;
    }
    .profile-badge {
        display: inline-block;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(4px);
        padding: 0.3rem 1rem;
        border-radius: 50px;
        font-size: 0.8rem;
        margin-top: 0.5rem;
    }

    .stats-mini {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .stat-mini-card {
        background: white;
        border-radius: 24px;
        padding: 1rem;
        text-align: center;
        transition: all 0.2s;
        border: 1px solid rgba(102,126,234,0.1);
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .stat-mini-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .stat-mini-card .stat-value {
        font-size: 1.8rem;
        font-weight: 800;
        color: #1e293b;
        font-family: 'Montserrat', sans-serif;
    }
    .stat-mini-card .stat-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 1px;
    }

    .profile-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
    .profile-info-card {
        background: white;
        border-radius: 28px;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .profile-info-card h3 {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.2rem;
        font-size: 1.2rem;
        border-left: 4px solid #667eea;
        padding-left: 0.8rem;
    }
    .info-row {
        display: flex;
        padding: 0.7rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .info-label {
        width: 120px;
        font-weight: 600;
        color: #475569;
    }
    .info-value {
        flex: 1;
        color: #1e293b;
        font-weight: 500;
    }

    .savings-ring {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    .ring-progress {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: conic-gradient(#10b981 0deg, #e2e8f0 0deg);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.8rem;
        position: relative;
    }
    .ring-inner {
        width: 110px;
        height: 110px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: bold;
        color: #10b981;
    }
    .action-buttons {
        display: flex;
        gap: 0.8rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    @media (max-width: 768px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
        .profile-hero h1 {
            font-size: 1.5rem;
        }
        .stats-mini {
            grid-template-columns: 1fr 1fr;
        }
    }
</style>

<div class="profile-hero">
    <h1>Hello, <?= htmlspecialchars(explode(' ', $student->fullName)[0]) ?>! 👋</h1>
    <p>Your financial journey matters. Here's your personal space.</p>
    <div class="profile-badge">
        🏆 Member since <?= date('F Y', strtotime($student->registrationDate)) ?>
    </div>
</div>

<div class="stats-mini">
    <div class="stat-mini-card">
        <div class="stat-value"><?= number_format($totalIncome, 0) ?> kr</div>
        <div class="stat-label">Total Income</div>
    </div>
    <div class="stat-mini-card">
        <!-- Total Expenses in RED -->
        <div class="stat-value" style="color: #ef4444;"><?= number_format($totalExpenses, 0) ?> kr</div>
        <div class="stat-label">Total Expenses</div>
    </div>
    <div class="stat-mini-card">
        <div class="stat-value" style="color: <?= $netBalance >= 0 ? '#10b981' : '#ef4444' ?>">
            <?= number_format($netBalance, 0) ?> kr
        </div>
        <div class="stat-label">Net Balance</div>
    </div>
</div>

<div class="profile-grid">
    <!-- Left: Personal Info -->
    <div class="profile-info-card">
        <h3><i class="fas fa-user-circle"></i> Personal Information</h3>
        <div class="info-row">
            <span class="info-label">Full Name</span>
            <span class="info-value"><?= htmlspecialchars($student->fullName) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Email</span>
            <span class="info-value"><?= htmlspecialchars($student->email) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Date of Birth</span>
            <span class="info-value"><?= date('d F Y', strtotime($student->dateOfBirth)) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Registered</span>
            <span class="info-value"><?= date('d M Y', strtotime($student->registrationDate)) ?></span>
        </div>
        <div class="action-buttons">
            <a href="edit.php" class="btn"><i class="fas fa-edit"></i> Edit Profile</a>
            <a href="delete.php" class="btn btn-danger delete-link"><i class="fas fa-trash-alt"></i> Delete Account</a>
        </div>
    </div>

    <!-- Right: Savings Goal Progress -->
    <div class="profile-info-card">
        <h3><i class="fas fa-piggy-bank"></i> Savings Pulse</h3>
        <?php if ($hasGoal): ?>
            <div class="savings-ring">
                <div class="ring-progress" id="savingsRing" data-percent="<?= $percent ?>">
                    <div class="ring-inner"><?= $percent ?>%</div>
                </div>
                <p style="margin-top: 0.5rem; font-weight: 600;"><?= $goalName ?></p>
                <p style="font-size:0.85rem;">
                    <?= number_format($currentAmount, 0) ?> kr / <?= number_format($targetAmount, 0) ?> kr
                </p>
                <p style="font-size:0.7rem; color:#64748b;">Keep saving! 🎯</p>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 1rem;">
                <i class="fas fa-rocket" style="font-size: 3rem; color: #cbd5e1;"></i>
                <p style="margin-top: 0.5rem;">No savings goals yet.<br>
                <a href="../savings/add.php" class="btn" style="margin-top: 0.5rem;">Create one</a></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Animate the savings ring if present
    const ring = document.getElementById('savingsRing');
    if (ring) {
        let percent = parseFloat(ring.getAttribute('data-percent')) || 0;
        let angle = (percent / 100) * 360;
        ring.style.background = `conic-gradient(#10b981 0deg, #10b981 ${angle}deg, #e2e8f0 ${angle}deg, #e2e8f0 360deg)`;
        let inner = document.querySelector('.ring-inner');
        let current = 0;
        let interval = setInterval(() => {
            if (current >= percent) {
                clearInterval(interval);
            } else {
                current++;
                inner.innerText = current + '%';
            }
        }, 20);
    }
</script>

<?php require_once '../../includes/footer.php'; ?>