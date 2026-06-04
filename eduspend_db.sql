-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 04, 2026 at 07:49 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eduspend_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `attempt_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `email`, `attempt_time`) VALUES
(1, 'bibekkohar2525@gmail.com', '2026-05-31 14:42:19'),
(2, 'bibekkohar2525@gmail.com', '2026-05-31 14:42:29'),
(3, 'bibekkohar2525@gmail.com', '2026-05-31 14:42:40'),
(4, 'bibekkohar@gmail.com', '2026-05-31 14:43:21'),
(5, 'bibekkohar@gmail.com', '2026-05-31 14:43:27'),
(6, 'bibekkohar@gmail.com', '2026-05-31 14:43:35');

-- --------------------------------------------------------

--
-- Table structure for table `tblcategory`
--

CREATE TABLE `tblcategory` (
  `CategoryID` int(11) NOT NULL,
  `CategoryName` varchar(50) NOT NULL,
  `BudgetLimit` decimal(10,2) NOT NULL,
  `IsActive` tinyint(1) DEFAULT 1,
  `CreatedDate` date NOT NULL,
  `Description` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcategory`
--

INSERT INTO `tblcategory` (`CategoryID`, `CategoryName`, `BudgetLimit`, `IsActive`, `CreatedDate`, `Description`) VALUES
(15, 'Food & Groceries', 2500.00, 1, '2026-05-13', 'Supermarket, dining hall, restaurants, coffee'),
(16, 'Transport', 800.00, 1, '2026-05-13', 'Bus, train, metro, bike repair, fuel'),
(17, 'Rent', 4500.00, 1, '2026-05-13', 'Dormitory or apartment rent'),
(18, 'Utilities', 800.00, 1, '2026-05-13', 'Electricity, water, heating, internet'),
(20, 'Entertainment', 600.00, 1, '2026-05-13', 'Movies, games, concerts, streaming subscriptions'),
(21, 'Health & Gym', 500.00, 1, '2026-05-13', 'Medicine, doctor visits, gym membership'),
(22, 'Clothing', 800.00, 1, '2026-05-13', 'Clothes, shoes, accessories'),
(23, 'Tuition & Fees', 30000.00, 1, '2026-05-13', 'Course fees, lab fees (per semester)'),
(24, 'Technology', 500.00, 1, '2026-05-13', 'Software licenses, cloud storage, device repair'),
(25, 'Gifts & Donations', 300.00, 1, '2026-05-13', 'Birthday gifts, charity'),
(26, 'Travel', 1000.00, 1, '2026-05-13', 'Weekend trips, flights, hotels'),
(27, 'Miscellaneous', 500.00, 1, '2026-05-13', 'Other unplanned expenses'),
(28, 'Test', 8000.00, 0, '2026-05-14', 'to check');

-- --------------------------------------------------------

--
-- Table structure for table `tblexpense`
--

CREATE TABLE `tblexpense` (
  `ExpenseID` int(11) NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `CategoryID` int(11) NOT NULL,
  `ExpenseDate` date NOT NULL,
  `IsRecurring` tinyint(1) DEFAULT 0,
  `StudentID` int(11) NOT NULL,
  `Notes` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblexpense`
--

INSERT INTO `tblexpense` (`ExpenseID`, `Amount`, `CategoryID`, `ExpenseDate`, `IsRecurring`, `StudentID`, `Notes`, `deleted_at`) VALUES
(22, 24.00, 16, '2026-05-13', 0, 2, 'Bus ticket to university', NULL),
(24, 89.00, 20, '2026-05-13', 1, 2, 'Netflix subscription', NULL),
(25, 120.00, 18, '2026-05-05', 1, 2, 'Electricity bill', NULL),
(27, 299.00, 24, '2026-05-12', 0, 2, 'Software license', NULL),
(30, 65.00, 21, '2026-05-07', 1, 2, 'Gym membership monthly', NULL),
(31, 450.00, 22, '2026-05-09', 0, 2, 'New hoodie', NULL),
(32, 30.00, 25, '2026-05-11', 0, 2, 'Friend’s birthday gift', NULL),
(42, 560.00, 15, '2026-04-27', 0, 2, 'party', NULL),
(44, 60.00, 27, '2026-05-30', 1, 2, 'norriport', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblincome`
--

CREATE TABLE `tblincome` (
  `IncomeID` int(11) NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `Source` varchar(100) NOT NULL,
  `IncomeDate` date NOT NULL,
  `IsScholarship` tinyint(1) DEFAULT 0,
  `StudentID` int(11) NOT NULL,
  `Notes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblincome`
--

INSERT INTO `tblincome` (`IncomeID`, `Amount`, `Source`, `IncomeDate`, `IsScholarship`, `StudentID`, `Notes`) VALUES
(1, 9000.00, 'Part-time job', '2026-05-13', 0, 2, 'Evening job'),
(2, 2000.00, 'Scholarship', '2026-05-01', 0, 2, 'Scholarship for excellence'),
(3, 15000.00, 'Full-time job', '2026-05-13', 0, 2, 'Summar job');

-- --------------------------------------------------------

--
-- Table structure for table `tblsavingsgoal`
--

CREATE TABLE `tblsavingsgoal` (
  `GoalID` int(11) NOT NULL,
  `GoalName` varchar(100) NOT NULL,
  `TargetAmount` decimal(10,2) NOT NULL,
  `CurrentAmount` decimal(10,2) DEFAULT 0.00,
  `TargetDate` date NOT NULL,
  `IsCompleted` tinyint(1) DEFAULT 0,
  `StudentID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblsavingsgoal`
--

INSERT INTO `tblsavingsgoal` (`GoalID`, `GoalName`, `TargetAmount`, `CurrentAmount`, `TargetDate`, `IsCompleted`, `StudentID`) VALUES
(1, 'Iphone 18', 14000.00, 4000.00, '2026-09-09', 0, 2),
(2, 'feee', 50000.00, 0.00, '2026-12-01', 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tblstudent`
--

CREATE TABLE `tblstudent` (
  `StudentID` int(11) NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `DateOfBirth` date NOT NULL,
  `IsActive` tinyint(1) DEFAULT 1,
  `RegistrationDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblstudent`
--

INSERT INTO `tblstudent` (`StudentID`, `FullName`, `Email`, `PasswordHash`, `DateOfBirth`, `IsActive`, `RegistrationDate`) VALUES
(2, 'Bibek', 'bibekkohar252@gmail.com', '$2y$10$uxs5k7BUmQ2VwMmhBabTKeW39ijXtlcYa0NfYKUo4ZIHiGlGJlJd.', '2202-02-01', 1, '2026-04-29 15:13:20'),
(3, 'Test account', 'bibekkohar2525@gmail.com', '$2y$10$2BHJFU9oADAJJkgrbNGwRO.hq9QwSYYkCwtLfFbyn0ws99Kuoz6f2', '2000-09-12', 1, '2026-05-14 19:23:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email_time` (`email`,`attempt_time`);

--
-- Indexes for table `tblcategory`
--
ALTER TABLE `tblcategory`
  ADD PRIMARY KEY (`CategoryID`);

--
-- Indexes for table `tblexpense`
--
ALTER TABLE `tblexpense`
  ADD PRIMARY KEY (`ExpenseID`),
  ADD KEY `CategoryID` (`CategoryID`),
  ADD KEY `StudentID` (`StudentID`),
  ADD KEY `idx_deleted_at` (`deleted_at`);

--
-- Indexes for table `tblincome`
--
ALTER TABLE `tblincome`
  ADD PRIMARY KEY (`IncomeID`),
  ADD KEY `StudentID` (`StudentID`);

--
-- Indexes for table `tblsavingsgoal`
--
ALTER TABLE `tblsavingsgoal`
  ADD PRIMARY KEY (`GoalID`),
  ADD KEY `StudentID` (`StudentID`);

--
-- Indexes for table `tblstudent`
--
ALTER TABLE `tblstudent`
  ADD PRIMARY KEY (`StudentID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tblcategory`
--
ALTER TABLE `tblcategory`
  MODIFY `CategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `tblexpense`
--
ALTER TABLE `tblexpense`
  MODIFY `ExpenseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `tblincome`
--
ALTER TABLE `tblincome`
  MODIFY `IncomeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblsavingsgoal`
--
ALTER TABLE `tblsavingsgoal`
  MODIFY `GoalID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblstudent`
--
ALTER TABLE `tblstudent`
  MODIFY `StudentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblexpense`
--
ALTER TABLE `tblexpense`
  ADD CONSTRAINT `tblexpense_ibfk_1` FOREIGN KEY (`CategoryID`) REFERENCES `tblcategory` (`CategoryID`),
  ADD CONSTRAINT `tblexpense_ibfk_2` FOREIGN KEY (`StudentID`) REFERENCES `tblstudent` (`StudentID`);

--
-- Constraints for table `tblincome`
--
ALTER TABLE `tblincome`
  ADD CONSTRAINT `tblincome_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `tblstudent` (`StudentID`);

--
-- Constraints for table `tblsavingsgoal`
--
ALTER TABLE `tblsavingsgoal`
  ADD CONSTRAINT `tblsavingsgoal_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `tblstudent` (`StudentID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
