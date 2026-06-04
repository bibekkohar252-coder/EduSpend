<?php
// includes/Category.php
require_once __DIR__ . '/config.php';

class Category {
    public $categoryId;
    public $categoryName;
    public $budgetLimit;
    public $isActive;
    public $createdDate;
    public $description;

    // Constructor – accepts an array (e.g., from database row)
    public function __construct($data = null) {
        if ($data) {
            $this->categoryId = $data['CategoryID'] ?? null;
            $this->categoryName = $data['CategoryName'] ?? null;
            $this->budgetLimit = $data['BudgetLimit'] ?? null;
            $this->isActive = $data['IsActive'] ?? 1;
            $this->createdDate = $data['CreatedDate'] ?? date('Y-m-d');
            $this->description = $data['Description'] ?? null;
        }
    }

    // Save (insert or update)
    public function save() {
        global $pdo;
        if ($this->categoryId) {
            // UPDATE
            $sql = "UPDATE tblCategory 
                    SET CategoryName = ?, BudgetLimit = ?, IsActive = ?, Description = ? 
                    WHERE CategoryID = ?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $this->categoryName,
                $this->budgetLimit,
                $this->isActive,
                $this->description,
                $this->categoryId
            ]);
        } else {
            // INSERT
            $sql = "INSERT INTO tblCategory (CategoryName, BudgetLimit, IsActive, CreatedDate, Description) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $this->categoryName,
                $this->budgetLimit,
                $this->isActive,
                $this->createdDate,
                $this->description
            ]);
        }
    }

    // Delete this category
    public function delete() {
        global $pdo;
        if (!$this->categoryId) return false;
        $stmt = $pdo->prepare("DELETE FROM tblCategory WHERE CategoryID = ?");
        return $stmt->execute([$this->categoryId]);
    }

    // Find one category by ID
    public static function find($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM tblCategory WHERE CategoryID = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new Category($data) : null;
    }

    // Get all categories, optionally filtered by active status
    public static function all($filter = null) {
        global $pdo;
        $sql = "SELECT * FROM tblCategory";
        if ($filter === 'active') {
            $sql .= " WHERE IsActive = 1";
        } elseif ($filter === 'inactive') {
            $sql .= " WHERE IsActive = 0";
        }
        $sql .= " ORDER BY CategoryName";
        $stmt = $pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $categories = [];
        foreach ($rows as $row) {
            $categories[] = new Category($row);
        }
        return $categories;
    }
    // Get total amount spent for a specific category in a given month (YYYY-MM)
public static function getMonthlySpent($categoryId, $studentId, $month)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(Amount), 0) FROM tblExpense WHERE CategoryID = ? AND StudentID = ? AND DATE_FORMAT(ExpenseDate, '%Y-%m') = ?");
    $stmt->execute([$categoryId, $studentId, $month]);
    return (float)$stmt->fetchColumn();
}   
}
?>