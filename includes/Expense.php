<?php
// includes/Expense.php
require_once __DIR__ . '/config.php';

class Expense {
    public $expenseId;
    public $amount;
    public $categoryId;
    public $expenseDate;
    public $isRecurring;
    public $studentId;
    public $notes;
    public $categoryName;
    public $deletedAt;

    public function __construct($data = null) {
        if ($data) {
            $this->expenseId = $data['ExpenseID'] ?? null;
            $this->amount = $data['Amount'] ?? null;
            $this->categoryId = $data['CategoryID'] ?? null;
            $this->expenseDate = $data['ExpenseDate'] ?? null;
            $this->isRecurring = $data['IsRecurring'] ?? 0;
            $this->studentId = $data['StudentID'] ?? null;
            $this->notes = $data['Notes'] ?? null;
            $this->deletedAt = $data['deleted_at'] ?? null;
        }
    }

    public function softDelete() {
        global $pdo;
        if (!$this->expenseId) return false;
        $stmt = $pdo->prepare("UPDATE tblExpense SET deleted_at = NOW() WHERE ExpenseID = ?");
        return $stmt->execute([$this->expenseId]);
    }

    public function restore() {
        global $pdo;
        if (!$this->expenseId) return false;
        $stmt = $pdo->prepare("UPDATE tblExpense SET deleted_at = NULL WHERE ExpenseID = ?");
        return $stmt->execute([$this->expenseId]);
    }

    public function hardDelete() {
        global $pdo;
        if (!$this->expenseId) return false;
        $stmt = $pdo->prepare("DELETE FROM tblExpense WHERE ExpenseID = ?");
        return $stmt->execute([$this->expenseId]);
    }

    public function save() {
        global $pdo;
        if ($this->expenseId) {
            $sql = "UPDATE tblExpense SET Amount = ?, CategoryID = ?, ExpenseDate = ?, IsRecurring = ?, Notes = ? WHERE ExpenseID = ?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$this->amount, $this->categoryId, $this->expenseDate, $this->isRecurring, $this->notes, $this->expenseId]);
        } else {
            $sql = "INSERT INTO tblExpense (Amount, CategoryID, ExpenseDate, IsRecurring, StudentID, Notes) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$this->amount, $this->categoryId, $this->expenseDate, $this->isRecurring, $this->studentId, $this->notes]);
        }
    }

    public static function find($id, $withTrashed = false) {
        global $pdo;
        $sql = "SELECT * FROM tblExpense WHERE ExpenseID = ?";
        if (!$withTrashed) $sql .= " AND deleted_at IS NULL";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new Expense($data) : null;
    }

    public static function all($studentId, $categoryFilter = null, $search = null, $withTrashed = false) {
        global $pdo;
        $sql = "SELECT e.*, c.CategoryName FROM tblExpense e 
                INNER JOIN tblCategory c ON e.CategoryID = c.CategoryID 
                WHERE e.StudentID = ?";
        if (!$withTrashed) $sql .= " AND e.deleted_at IS NULL";
        $params = [$studentId];
        if ($categoryFilter) {
            $sql .= " AND e.CategoryID = ?";
            $params[] = $categoryFilter;
        }
        if ($search) {
            $sql .= " AND (e.Amount LIKE ? OR e.Notes LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        $sql .= " ORDER BY e.ExpenseDate DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $expenses = [];
        foreach ($rows as $row) {
            $expense = new Expense($row);
            $expense->categoryName = $row['CategoryName'];
            $expenses[] = $expense;
        }
        return $expenses;
    }
}
?>