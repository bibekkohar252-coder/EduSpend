<?php
// includes/SavingsGoal.php
require_once __DIR__ . '/config.php';

class SavingsGoal {
    public $goalId;
    public $goalName;
    public $targetAmount;
    public $currentAmount;
    public $targetDate;
    public $isCompleted;
    public $studentId;

    // Constructor – accepts an array (e.g., from database row)
    public function __construct($data = null) {
        if ($data) {
            $this->goalId = $data['GoalID'] ?? null;
            $this->goalName = $data['GoalName'] ?? null;
            $this->targetAmount = $data['TargetAmount'] ?? null;
            $this->currentAmount = $data['CurrentAmount'] ?? 0;
            $this->targetDate = $data['TargetDate'] ?? null;
            $this->isCompleted = $data['IsCompleted'] ?? 0;
            $this->studentId = $data['StudentID'] ?? null;
        }
    }

    // Save (insert or update)
    public function save() {
        global $pdo;
        if ($this->goalId) {
            // UPDATE
            $sql = "UPDATE tblSavingsGoal 
                    SET GoalName = ?, TargetAmount = ?, CurrentAmount = ?, TargetDate = ?, IsCompleted = ? 
                    WHERE GoalID = ?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $this->goalName,
                $this->targetAmount,
                $this->currentAmount,
                $this->targetDate,
                $this->isCompleted,
                $this->goalId
            ]);
        } else {
            // INSERT
            $sql = "INSERT INTO tblSavingsGoal (GoalName, TargetAmount, CurrentAmount, TargetDate, IsCompleted, StudentID) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $this->goalName,
                $this->targetAmount,
                $this->currentAmount,
                $this->targetDate,
                $this->isCompleted,
                $this->studentId
            ]);
        }
    }

    // Delete this goal
    public function delete() {
        global $pdo;
        if (!$this->goalId) return false;
        $stmt = $pdo->prepare("DELETE FROM tblSavingsGoal WHERE GoalID = ?");
        return $stmt->execute([$this->goalId]);
    }

    // Find one goal by ID
    public static function find($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM tblSavingsGoal WHERE GoalID = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new SavingsGoal($data) : null;
    }

    // Get all savings goals for a student
    public static function all($studentId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM tblSavingsGoal WHERE StudentID = ? ORDER BY TargetDate ASC");
        $stmt->execute([$studentId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $goals = [];
        foreach ($rows as $row) {
            $goals[] = new SavingsGoal($row);
        }
        return $goals;
    }

    // Update progress (increase current amount)
    public function updateProgress($amount) {
        global $pdo;
        if (!$this->goalId) return false;
        $newAmount = $this->currentAmount + $amount;
        if ($newAmount >= $this->targetAmount) {
            $newAmount = $this->targetAmount;
            $this->isCompleted = 1;
        }
        $sql = "UPDATE tblSavingsGoal SET CurrentAmount = ?, IsCompleted = ? WHERE GoalID = ?";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([$newAmount, $this->isCompleted, $this->goalId]);
        if ($success) {
            $this->currentAmount = $newAmount;
        }
        return $success;
    }

    // Calculate progress percentage
    public function getProgressPercent() {
        if ($this->targetAmount <= 0) return 0;
        return round(($this->currentAmount / $this->targetAmount) * 100);
    }
}
?>