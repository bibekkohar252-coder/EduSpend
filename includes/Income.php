<?php
// includes/Income.php
require_once __DIR__ . '/config.php';

class Income {
    public $incomeId;
    public $amount;
    public $source;
    public $incomeDate;
    public $isScholarship;
    public $studentId;
    public $notes;

    // Constructor – accepts an array (e.g., from database row)
    public function __construct($data = null) {
        if ($data) {
            $this->incomeId = $data['IncomeID'] ?? null;
            $this->amount = $data['Amount'] ?? null;
            $this->source = $data['Source'] ?? null;
            $this->incomeDate = $data['IncomeDate'] ?? null;
            $this->isScholarship = $data['IsScholarship'] ?? 0;
            $this->studentId = $data['StudentID'] ?? null;
            $this->notes = $data['Notes'] ?? null;
        }
    }

    // Save (insert or update)
    public function save() {
        global $pdo;
        if ($this->incomeId) {
            // UPDATE
            $sql = "UPDATE tblIncome 
                    SET Amount = ?, Source = ?, IncomeDate = ?, IsScholarship = ?, Notes = ? 
                    WHERE IncomeID = ?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $this->amount,
                $this->source,
                $this->incomeDate,
                $this->isScholarship,
                $this->notes,
                $this->incomeId
            ]);
        } else {
            // INSERT
            $sql = "INSERT INTO tblIncome (Amount, Source, IncomeDate, IsScholarship, StudentID, Notes) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $this->amount,
                $this->source,
                $this->incomeDate,
                $this->isScholarship,
                $this->studentId,
                $this->notes
            ]);
        }
    }

    // Delete this income record
    public function delete() {
        global $pdo;
        if (!$this->incomeId) return false;
        $stmt = $pdo->prepare("DELETE FROM tblIncome WHERE IncomeID = ?");
        return $stmt->execute([$this->incomeId]);
    }

    // Find one income by ID
    public static function find($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM tblIncome WHERE IncomeID = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new Income($data) : null;
    }

    // Get all income records for a student, optionally filtered by source
    public static function all($studentId, $filterSource = null) {
        global $pdo;
        $sql = "SELECT * FROM tblIncome WHERE StudentID = ?";
        $params = [$studentId];
        if ($filterSource) {
            $sql .= " AND Source LIKE ?";
            $params[] = "%$filterSource%";
        }
        $sql .= " ORDER BY IncomeDate DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $incomes = [];
        foreach ($rows as $row) {
            $incomes[] = new Income($row);
        }
        return $incomes;
    }
}
?>