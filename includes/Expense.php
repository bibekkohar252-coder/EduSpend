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
    public $categoryName; // extra property for JOIN result

    public function __construct($data = null) {
        if ($data) {
            $this->expenseId = $data['ExpenseID'] ?? null;
            $this->amount = $data['Amount'] ?? null;
            $this->categoryId = $data['CategoryID'] ?? null;
            $this->expenseDate = $data['ExpenseDate'] ?? null;
            $this->isRecurring = $data['IsRecurring'] ?? 0;
            $this->studentId = $data['StudentID'] ?? null;
            $this->notes = $data['Notes'] ?? null;
        }
    }

    // Save (insert or update)
    public function save() {
        global $pdo;
        if ($this->expenseId) {
            $sql = "UPDATE tblExpense 
                    SET Amount = ?, CategoryID = ?, ExpenseDate = ?, IsRecurring = ?, Notes = ? 
                    WHERE ExpenseID = ?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $this->amount,
                $this->categoryId,
                $this->expenseDate,
                $this->isRecurring,
                $this->notes,
                $this->expenseId
            ]);
        } else {
            $sql = "INSERT INTO tblExpense (Amount, CategoryID, ExpenseDate, IsRecurring, StudentID, Notes) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $this->amount,
                $this->categoryId,
                $this->expenseDate,
                $this->isRecurring,
                $this->studentId,
                $this->notes
            ]);
        }
    }

    // Delete this expense
    public function delete() {
        global $pdo;
        if (!$this->expenseId) return false;
        $stmt = $pdo->prepare("DELETE FROM tblExpense WHERE ExpenseID = ?");
        return $stmt->execute([$this->expenseId]);
    }

    // Find one expense by ID
    public static function find($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM tblExpense WHERE ExpenseID = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new Expense($data) : null;
    }

    // Get all expenses for a student, with category name, filter, search
    public static function all($studentId, $categoryFilter = null, $search = null) {
        global $pdo;
        $sql = "SELECT e.*, c.CategoryName 
                FROM tblExpense e 
                INNER JOIN tblCategory c ON e.CategoryID = c.CategoryID 
                WHERE e.StudentID = ?";
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