<?php
$page_title = 'My Income';
require_once '../../includes/header.php';
$user_id = $_SESSION['user_id'];
$filter_source = $_GET['source'] ?? '';
$sql = "SELECT * FROM tblIncome WHERE StudentID = :uid";
$params = [':uid' => $user_id];
if ($filter_source) {
    $sql .= " AND Source LIKE :src";
    $params[':src'] = "%$filter_source%";
}
$sql .= " ORDER BY IncomeDate DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$incomes = $stmt->fetchAll();
?>
<h2>💰 Income Records</h2>
<form method="get" class="filter-bar">
    <input type="text" name="source" placeholder="Filter by source" value="<?= htmlspecialchars($filter_source) ?>">
    <button type="submit" class="btn">Filter</button>
    <?php if ($filter_source): ?><a href="list.php" class="btn btn-secondary">Clear</a><?php endif; ?>
</form>
<?php if (empty($incomes)): ?>
    <p>No income records. <a href="add.php">Add your first income</a></p>
<?php else: ?>
    <table>
        <thead>
            <tr><th>Date</th><th>Amount (kr)</th><th>Source</th><th>Scholarship</th><th>Notes</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($incomes as $inc): ?>
            <tr>
                <td><?= $inc['IncomeDate'] ?></td>
                <td><?= number_format($inc['Amount'],2) ?> kr</td>
                <td><?= htmlspecialchars($inc['Source']) ?></td>
                <td><?= $inc['IsScholarship'] ? 'Yes' : 'No' ?></td>
                <td><?= htmlspecialchars($inc['Notes']) ?></td>
                <td><a href="edit.php?id=<?= $inc['IncomeID'] ?>" class="btn">Edit</a> <a href="delete.php?id=<?= $inc['IncomeID'] ?>" class="btn btn-danger delete-link">Delete</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<a href="add.php" class="btn">+ Add Income</a>
<?php require_once '../../includes/footer.php'; ?>