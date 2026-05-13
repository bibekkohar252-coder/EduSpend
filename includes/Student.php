<?php
// includes/Student.php
require_once __DIR__ . '/config.php';

class Student {
    public $studentId;
    public $fullName;
    public $email;
    public $passwordHash;
    public $dateOfBirth;
    public $isActive;
    public $registrationDate;

    // Constructor – accepts an array (e.g., from database row)
    public function __construct($data = null) {
        if ($data) {
            $this->studentId = $data['StudentID'] ?? null;
            $this->fullName = $data['FullName'] ?? null;
            $this->email = $data['Email'] ?? null;
            $this->passwordHash = $data['PasswordHash'] ?? null;
            $this->dateOfBirth = $data['DateOfBirth'] ?? null;
            $this->isActive = $data['IsActive'] ?? 1;
            $this->registrationDate = $data['RegistrationDate'] ?? date('Y-m-d H:i:s');
        }
    }

    // Register a new student
    public function register() {
        global $pdo;
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT StudentID FROM tblStudent WHERE Email = ?");
        $stmt->execute([$this->email]);
        if ($stmt->fetch()) {
            return false; // Email already registered
        }
        $sql = "INSERT INTO tblStudent (FullName, Email, PasswordHash, DateOfBirth, IsActive, RegistrationDate) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([
            $this->fullName,
            $this->email,
            $this->passwordHash,
            $this->dateOfBirth,
            $this->isActive,
            $this->registrationDate
        ]);
        if ($success) {
            $this->studentId = $pdo->lastInsertId();
        }
        return $success;
    }

    // Update profile (excluding password)
    public function updateProfile() {
        global $pdo;
        if (!$this->studentId) return false;
        $sql = "UPDATE tblStudent SET FullName = ?, Email = ?, DateOfBirth = ? WHERE StudentID = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$this->fullName, $this->email, $this->dateOfBirth, $this->studentId]);
    }

    // Update password
    public function updatePassword($newHash) {
        global $pdo;
        if (!$this->studentId) return false;
        $sql = "UPDATE tblStudent SET PasswordHash = ? WHERE StudentID = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$newHash, $this->studentId]);
    }

    // Delete account (cascade will handle related records if foreign keys are set)
    public function deleteAccount() {
        global $pdo;
        if (!$this->studentId) return false;
        // Delete dependent records first (Expense, Income, SavingsGoal) – but your foreign keys may be set to CASCADE.
        // We'll explicitly delete to be safe.
        $pdo->prepare("DELETE FROM tblExpense WHERE StudentID = ?")->execute([$this->studentId]);
        $pdo->prepare("DELETE FROM tblIncome WHERE StudentID = ?")->execute([$this->studentId]);
        $pdo->prepare("DELETE FROM tblSavingsGoal WHERE StudentID = ?")->execute([$this->studentId]);
        $stmt = $pdo->prepare("DELETE FROM tblStudent WHERE StudentID = ?");
        return $stmt->execute([$this->studentId]);
    }

    // Static: find student by ID
    public static function find($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM tblStudent WHERE StudentID = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new Student($data) : null;
    }

    // Static: find student by email (for login)
    public static function findByEmail($email) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM tblStudent WHERE Email = ?");
        $stmt->execute([$email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new Student($data) : null;
    }

    // Static: get all students (admin view)
    public static function all() {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM tblStudent ORDER BY FullName");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $students = [];
        foreach ($rows as $row) {
            $students[] = new Student($row);
        }
        return $students;
    }

    // Get expenses for this student
    public function getExpenses() {
        return Expense::all($this->studentId);
    }

    // Get income records for this student
    public function getIncome() {
        return Income::all($this->studentId);
    }

    // Get savings goals for this student
    public function getSavingsGoals() {
        return SavingsGoal::all($this->studentId);
    }
}
?>