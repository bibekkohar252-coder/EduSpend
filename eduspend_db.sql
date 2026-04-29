-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 29, 2026 at 09:34 AM
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
(1, 'Food', 300.00, 1, '2026-04-29', NULL),
(2, 'Transport', 100.00, 1, '2026-04-29', NULL),
(3, 'Entertainment', 80.00, 1, '2026-04-29', NULL);

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
  `Notes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblexpense`
--

INSERT INTO `tblexpense` (`ExpenseID`, `Amount`, `CategoryID`, `ExpenseDate`, `IsRecurring`, `StudentID`, `Notes`) VALUES
(1, 12.50, 1, '2026-04-29', 0, 1, 'Lunch at canteen'),
(2, 5.00, 2, '2026-04-29', 0, 1, 'Bus ticket');

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
(1, 'Alex Chen', 'alex@university.com', 'hashed_password_123', '2004-05-15', 1, '2026-04-29 09:32:37');

--
-- Indexes for dumped tables
--

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
  ADD KEY `StudentID` (`StudentID`);

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
-- AUTO_INCREMENT for table `tblcategory`
--
ALTER TABLE `tblcategory`
  MODIFY `CategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblexpense`
--
ALTER TABLE `tblexpense`
  MODIFY `ExpenseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblincome`
--
ALTER TABLE `tblincome`
  MODIFY `IncomeID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblsavingsgoal`
--
ALTER TABLE `tblsavingsgoal`
  MODIFY `GoalID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblstudent`
--
ALTER TABLE `tblstudent`
  MODIFY `StudentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
