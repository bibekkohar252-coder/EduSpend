<?php
$page_title = 'My Expenses';
require_once '../../includes/header.php';
require_once '../../includes/Expense.php';

$user_id = $_SESSION['user_id'];

// Month filter
$month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
$category_filter = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

// Fetch expenses
$expenses = Expense::all($user_id, $category_filter, $search);

// Calculate total for selected month
$total = $pdo->prepare("SELECT SUM(Amount) FROM tblExpense WHERE StudentID = ? AND DATE_FORMAT(ExpenseDate, '%Y-%m') = ?");
$total->execute([$user_id, $month]);
$totalAmount = $total->fetchColumn() ?? 0;

// Get category totals for the small pie chart (same month)
$catTotals = $pdo->prepare("
    SELECT c.CategoryName, SUM(e.Amount) as total
    FROM tblExpense e
    JOIN tblCategory c ON e.CategoryID = c.CategoryID
    WHERE e.StudentID = ? AND DATE_FORMAT(e.ExpenseDate, '%Y-%m') = ?
    GROUP BY e.CategoryID
");
$catTotals->execute([$user_id, $month]);
$categoryData = $catTotals->fetchAll();
$labels = [];
$values = [];
foreach ($categoryData as $row) {
    $labels[] = $row['CategoryName'];
    $values[] = $row['total'];
}

// Export CSV
if (isset($_GET['export']) && $_GET['export'] == 1) {
    $expensesForExport = Expense::all($user_id, $category_filter, $search);
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="expenses_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Date', 'Amount (kr)', 'Category', 'Recurring', 'Notes']);
    foreach ($expensesForExport as $e) {
        fputcsv($out, [$e->expenseDate, $e->amount, $e->categoryName, $e->isRecurring ? 'Yes' : 'No', $e->notes]);
    }
    fclose($out);
    exit;
}
?>

<style>
    .stats-row {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
        align-items: stretch;
        margin-bottom: 1.5rem;
    }
    .summary-card {
        flex: 2;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }
    .summary-card h3 {
        margin: 0;
        font-size: 1.1rem;
    }
    .total-amount {
        font-size: 2rem;
        font-weight: bold;
        margin: 0;
    }
    .pie-card {
        flex: 1;
        background: white;
        border-radius: 20px;
        padding: 0.75rem;
        border: 1px solid #eef2f6;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 180px;
    }
    .expense-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }
    .expense-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: transform 0.2s, box-shadow 0.2s;
        overflow: hidden;
        border: 1px solid #eef2f6;
    }
    .expense-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    }
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 0.75rem 1rem;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .card-header .amount {
        font-size: 1.4rem;
        font-weight: bold;
    }
    .card-header .date {
        font-size: 0.85rem;
        opacity: 0.9;
    }
    .card-body {
        padding: 1rem;
    }
    .card-body .category {
        font-weight: 600;
        color: #4f46e5;
        margin-bottom: 0.5rem;
    }
    .card-body .notes {
        color: #64748b;
        font-size: 0.9rem;
        margin: 0.5rem 0;
    }
    .card-footer {
        padding: 0.75rem 1rem;
        border-top: 1px solid #eef2f6;
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
        background: #fafcff;
    }
    .recurring-badge {
        display: inline-block;
        background: #f1f5f9;
        border-radius: 20px;
        padding: 0.2rem 0.6rem;
        font-size: 0.7rem;
        font-weight: 500;
        color: #1e293b;
    }

    /* Cool page header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .page-header h2 {
        font-size: 1.8rem;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .page-subtitle {
        font-size: 0.85rem;
        color: #64748b;
        margin-top: 0.2rem;
    }

    @media (max-width: 768px) {
        .expense-grid {
            grid-template-columns: 1fr;
        }
        .stats-row {
            flex-direction: column;
        }
        .pie-card {
            min-width: auto;
        }
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<!-- ===== EXPENSES PAGE HEADER ===== -->
<div class="page-header">
    <div>
        <h2>💸 Expenses</h2>
        <p class="page-subtitle">Track where your money goes — every krone matters.</p>
    </div>
    <a href="add.php" class="btn">+ Add New Expense</a>
</div>

<!-- Filter bar -->
<div class="filter-bar">
    <form method="get" style="display:flex; gap:1rem; flex-wrap:wrap; width:100%; align-items:flex-end;">
        <div class="form-group" style="margin-bottom:0;">
            <label>Month</label>
            <input type="month" name="month" value="<?= htmlspecialchars($month) ?>" onchange="this.form.submit()">
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label>Category</label>
            <select name="category" onchange="this.form.submit()">
                <option value="">All categories</option>
                <?php
                $cats = $pdo->query("SELECT CategoryID, CategoryName FROM tblCategory WHERE IsActive = 1")->fetchAll();
                foreach ($cats as $c): ?>
                    <option value="<?= $c['CategoryID'] ?>" <?= $category_filter == $c['CategoryID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['CategoryName']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label>Search</label>
            <input type="text" name="search" placeholder="Amount or notes" value="<?= htmlspecialchars($search) ?>">
        </div>
        <button type="submit" class="btn">Filter</button>
        <a href="?export=1&<?= http_build_query($_GET) ?>" class="btn btn-secondary">📥 Export CSV</a>
        <?php if ($category_filter || $search || $month != date('Y-m')): ?>
            <a href="view.php" class="btn btn-secondary">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Statistics row: summary card + small pie chart -->
<div class="stats-row">
    <div class="summary-card">
        <h3>Total for <?= htmlspecialchars(date('F Y', strtotime($month . '-01'))) ?></h3>
        <p class="total-amount"><?= number_format($totalAmount, 2) ?> kr</p>
    </div>
    <?php if (!empty($labels)): ?>
    <div class="pie-card">
        <canvas id="miniExpenseChart" width="180" height="180" style="max-width:180px; max-height:180px;"></canvas>
    </div>
    <?php endif; ?>
</div>

<!-- Expense cards grid -->
<?php if (empty($expenses)): ?>
    <div class="alert alert-info">✨ No expenses found. <a href="add.php">Add your first expense</a></div>
<?php else: ?>
    <div class="expense-grid">
        <?php foreach ($expenses as $e): ?>
            <div class="expense-card">
                <div class="card-header">
                    <span class="amount"><?= number_format($e->amount, 2) ?> kr</span>
                    <span class="date"><?= htmlspecialchars($e->expenseDate) ?></span>
                </div>
                <div class="card-body">
                    <div class="category">📂 <?= htmlspecialchars($e->categoryName) ?></div>
                    <?php if ($e->isRecurring): ?>
                        <span class="recurring-badge">🔄 Recurring</span>
                    <?php endif; ?>
                    <?php if (!empty($e->notes)): ?>
                        <div class="notes">📝 <?= htmlspecialchars($e->notes) ?></div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="edit.php?id=<?= $e->expenseId ?>" class="btn">✏️ Edit</a>
                    <a href="delete.php?id=<?= $e->expenseId ?>" class="btn btn-danger delete-link">🗑️ Delete</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Chart.js library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<?php if (!empty($labels)): ?>
<script>
    const pieCtx = document.getElementById('miniExpenseChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                data: <?= json_encode($values) ?>,
                backgroundColor: ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec489a', '#06b6d4', '#f97316', '#14b8a6', '#6366f1']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } },
                tooltip: { callbacks: { label: (ctx) => `${ctx.label}: ${ctx.parsed.toFixed(2)} kr` } }
            }
        }
    });
</script>
<?php endif; ?>

<?php require_once '../../includes/footer.php'; ?>