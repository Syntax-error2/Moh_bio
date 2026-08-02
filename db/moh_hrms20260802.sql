-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 02, 2026 at 07:59 AM
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
-- Database: `moh_hrms`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_generate_payroll_snapshot` (IN `p_run_id` INT)   BEGIN
    DECLARE v_snapshot_date DATE;
    DECLARE v_snapshot_id INT;
    
    SET v_snapshot_date = CURDATE();
    
    -- Generate overall snapshot
    INSERT INTO pr_tbl_payroll_snapshots 
        (run_id, snapshot_date, snapshot_type, group_by_value, group_by_label,
         personnel_count, total_gross, total_deductions, total_employer_share, total_net_pay,
         average_gross, average_net_pay, min_net_pay, max_net_pay)
    SELECT 
        p_run_id,
        v_snapshot_date,
        'overall',
        'ALL',
        'All Personnel',
        COUNT(*),
        SUM(gross_pay),
        SUM(total_deductions),
        SUM(total_employer_share),
        SUM(net_pay),
        AVG(gross_pay),
        AVG(net_pay),
        MIN(net_pay),
        MAX(net_pay)
    FROM pr_tbl_payroll_run_details
    WHERE run_id = p_run_id;
    
    SET v_snapshot_id = LAST_INSERT_ID();
    
    -- Generate department snapshots
    INSERT INTO pr_tbl_payroll_snapshots 
        (run_id, snapshot_date, snapshot_type, group_by_value, group_by_label,
         personnel_count, total_gross, total_deductions, total_employer_share, total_net_pay,
         average_gross, average_net_pay, min_net_pay, max_net_pay)
    SELECT 
        p_run_id,
        v_snapshot_date,
        'department',
        d.do_id,
        d.dept_office_name,
        COUNT(*),
        SUM(prd.gross_pay),
        SUM(prd.total_deductions),
        SUM(prd.total_employer_share),
        SUM(prd.net_pay),
        AVG(prd.gross_pay),
        AVG(prd.net_pay),
        MIN(prd.net_pay),
        MAX(prd.net_pay)
    FROM pr_tbl_payroll_run_details prd
    INNER JOIN personnels p ON prd.personnel_id = p.personnel_id
    INNER JOIN dept_offices d ON p.do_id = d.do_id
    WHERE prd.run_id = p_run_id
    GROUP BY d.do_id, d.dept_office_name;
    
    -- Generate income type summaries
    INSERT INTO pr_tbl_payroll_snapshot_items
        (snapshot_id, run_id, item_type, item_id, item_title, item_category,
         total_amount, personnel_count, average_amount, min_amount, max_amount)
    SELECT 
        v_snapshot_id,
        p_run_id,
        'income',
        income_id,
        income_title,
        income_type,
        SUM(amount),
        COUNT(DISTINCT personnel_id),
        AVG(amount),
        MIN(amount),
        MAX(amount)
    FROM pr_tbl_payroll_run_income
    WHERE run_id = p_run_id
    GROUP BY income_id, income_title, income_type;
    
    -- Generate deduction type summaries
    INSERT INTO pr_tbl_payroll_snapshot_items
        (snapshot_id, run_id, item_type, item_id, item_title, item_category,
         total_amount, personnel_count, average_amount, min_amount, max_amount)
    SELECT 
        v_snapshot_id,
        p_run_id,
        'deduction',
        deduction_id,
        deduction_title,
        deduction_type,
        SUM(employee_amount),
        COUNT(DISTINCT personnel_id),
        AVG(employee_amount),
        MIN(employee_amount),
        MAX(employee_amount)
    FROM pr_tbl_payroll_run_deductions
    WHERE run_id = p_run_id
    GROUP BY deduction_id, deduction_title, deduction_type;
    
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `account_signup_audit_logs`
--

CREATE TABLE `account_signup_audit_logs` (
  `audit_id` int(11) NOT NULL,
  `personnel_id_code` varchar(100) DEFAULT NULL,
  `fname` varchar(120) DEFAULT NULL,
  `lname` varchar(120) DEFAULT NULL,
  `matched_personnel_id` int(11) DEFAULT NULL,
  `status` varchar(40) NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `client_ip` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account_signup_audit_logs`
--

INSERT INTO `account_signup_audit_logs` (`audit_id`, `personnel_id_code`, `fname`, `lname`, `matched_personnel_id`, `status`, `remarks`, `client_ip`, `created_at`) VALUES
(1, 'P-JSB-10162024-165', 'JETTER', 'BARROCA', 140, 'SUCCESS', 'Matched personnel record for signup step 2', '::1', '2026-05-19 11:53:09');

-- --------------------------------------------------------

--
-- Table structure for table `activity_calendar`
--

CREATE TABLE `activity_calendar` (
  `activity_id` int(11) NOT NULL,
  `actMM` varchar(2) NOT NULL,
  `actDD` varchar(2) NOT NULL,
  `actYYYY` varchar(4) NOT NULL,
  `completeDate` varchar(10) NOT NULL,
  `event_title` varchar(255) NOT NULL,
  `event_description` text NOT NULL,
  `act_type` varchar(55) NOT NULL,
  `status` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `activity_calendar`
--

INSERT INTO `activity_calendar` (`activity_id`, `actMM`, `actDD`, `actYYYY`, `completeDate`, `event_title`, `event_description`, `act_type`, `status`) VALUES
(1, '11', '03', '2025', '11/03/2025', 'Work suspension', 'typhoon', 'Work Suspension', 'Display to DTR'),
(2, '05', '01', '2026', '05/01/2026', 'LABOR DAY', 'LABOR DAY', 'Regular Holiday', '-');

-- --------------------------------------------------------

--
-- Table structure for table `backup_dbname`
--

CREATE TABLE `backup_dbname` (
  `backup_id` int(11) NOT NULL,
  `ID` varchar(12) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Date` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `client_computer`
--

CREATE TABLE `client_computer` (
  `client_id` int(11) NOT NULL,
  `ipAddress` varchar(20) NOT NULL,
  `compName` varchar(55) NOT NULL,
  `description` varchar(255) NOT NULL,
  `clientNumber` int(11) NOT NULL,
  `display_time` int(11) NOT NULL,
  `RFID_tag` varchar(55) NOT NULL,
  `announcement_img` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `client_computer`
--

INSERT INTO `client_computer` (`client_id`, `ipAddress`, `compName`, `description`, `clientNumber`, `display_time`, `RFID_tag`, `announcement_img`) VALUES
(1, '192.168.56.1', 'MSI', '', 1, 15, '', 50),
(2, '192.168.1.18', 'DESKTOP-KEN0UR6', '', 1, 15, '', 149),
(3, '192.168.1.10', 'DESKTOP-KEN0UR6', '', 1, 15, '', 128),
(4, '192.168.1.22', 'DESKTOP-KEN0UR6', '', 1, 15, '', 71),
(5, '192.168.1.22', 'DESKTOP-KEN0UR6', '', 1, 15, '', 71),
(6, '192.168.1.4', 'DESKTOP-KEN0UR6', '', 1, 15, '', 128),
(7, '192.168.1.4', 'DESKTOP-KEN0UR6', '', 1, 15, '', 128),
(8, '127.0.0.1', 'DESKTOP-KEN0UR6', '', 1, 15, '', 132),
(9, '192.168.1.2', 'DESKTOP-KEN0UR6', '', 1, 15, '', 42),
(10, '192.168.1.18', 'DESKTOP-KEN0UR6', '', 1, 15, '', 149),
(11, '192.168.1.2', 'DESKTOP-KEN0UR6', '', 1, 15, '', 42),
(12, '192.168.1.16', 'DESKTOP-KEN0UR6', '', 1, 15, '', 32),
(13, '192.168.1.18', 'DESKTOP-KEN0UR6', '', 1, 15, '', 149),
(14, '127.0.0.1', 'DESKTOP-KEN0UR6', '', 1, 15, '', 132),
(15, '192.168.1.3', 'DESKTOP-KEN0UR6', '', 1, 15, '', 80),
(16, '192.168.1.5', 'DESKTOP-KEN0UR6', '', 1, 15, '', 25),
(17, '192.168.1.7', 'DESKTOP-KEN0UR6', '', 1, 15, '', 36),
(18, '192.168.1.13', 'DESKTOP-KEN0UR6', '', 1, 15, '', 107),
(19, '172.22.85.95', 'DESKTOP-KEN0UR6', '', 1, 15, '', 81),
(20, '192.168.24.94', 'DESKTOP-KEN0UR6', '', 1, 14, '', 0),
(21, '192.168.24.94', 'DESKTOP-KEN0UR6', '', 1, 14, '', 0),
(22, '192.168.1.4', 'DESKTOP-KEN0UR6', '', 1, 15, '', 128),
(23, '192.168.1.4', 'DESKTOP-KEN0UR6', '', 1, 15, '', 128),
(24, '192.168.1.22', 'DESKTOP-KEN0UR6', '', 1, 15, '', 71),
(25, '192.168.1.22', 'DESKTOP-KEN0UR6', '', 1, 15, '', 71),
(26, '26.218.55.136', 'ri-website-backup.local', '', 1, 5, '', 0),
(27, '10.174.189.58', 'WIN-RO2UQQ0R3FN', '', 1, 15, '', 137),
(28, '192.168.1.11', 'WIN-RO2UQQ0R3FN', '', 1, 15, '', 83);

-- --------------------------------------------------------

--
-- Table structure for table `dept_offices`
--

CREATE TABLE `dept_offices` (
  `do_id` int(11) NOT NULL,
  `dept_office_name` varchar(255) NOT NULL,
  `officeHead_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `dept_offices`
--

INSERT INTO `dept_offices` (`do_id`, `dept_office_name`, `officeHead_id`) VALUES
(1, 'Human Resource Management Office', 14),
(2, 'Office of the Municipal Mayor', 0),
(3, 'Office of the Municipal Engineer', 0),
(4, 'Office of the Municipal Planning & Development Coordinator', 0),
(5, 'Office of the Municipal Budget', 0),
(6, 'Office of the Municipal Accounting', 0),
(7, 'Office of the Municipal Social Welfare and Development', 0),
(8, 'Office of the Municipal Civil Registrar', 0),
(9, 'Office of the Municipal Assessor', 0),
(10, 'Office of the Municipal Treasurer', 0),
(11, 'Office of the Municipal Agriculture', 0),
(12, 'Office of the Municipal Rural Health Unit', 0),
(14, 'Market and Slaughterhouse Section', 95),
(15, 'Special Projects & Shelter Development Section', 0),
(16, 'Municipal Disaster Risk Reduction & Management Office', 0),
(17, 'Heavy Equipment Section', 0),
(19, 'Municipal Nutrition Office', 0),
(20, 'Municipal Tourism Office', 0),
(21, 'Land Tax Section', 0),
(23, 'Office of the Municipal Administrator', 0),
(24, 'Office of the Sangguniang Bayan', 0),
(25, 'Municipal Environment & Natural Resources Office', 0),
(26, 'Job Orders', 0);

-- --------------------------------------------------------

--
-- Table structure for table `designation`
--

CREATE TABLE `designation` (
  `des_id` int(11) NOT NULL,
  `des_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `designation`
--

INSERT INTO `designation` (`des_id`, `des_name`) VALUES
(1, 'Municipal Government Assistant Department Head I (Municipal Treasurer)'),
(2, 'Municipal Mayor'),
(3, 'Private Secretary II'),
(4, 'Security Guard I'),
(5, 'Driver II'),
(7, 'Communication Equipment Operator IV'),
(9, 'Human Resource Management Officer II'),
(12, 'Administrative Aide II'),
(13, 'Administrative Assistant V'),
(16, 'Records Officer I'),
(17, 'Human Resource Management Officer III'),
(18, 'Executive Assistant II'),
(19, 'Executive Assistant II'),
(20, 'Municipal Administrator'),
(21, 'Municipal Vice Mayor'),
(25, 'Sangguniang Bayan Member I'),
(30, 'Sangguniang Bayan Member I(President Liga ng Mga Barangay)'),
(31, 'Sangguniang Bayan Member I (SK Federation President)'),
(32, 'Secretary to the Sanggunian'),
(33, 'Clerk III'),
(34, 'Private Secretary I'),
(37, 'Executive Assistant I'),
(38, 'Local Legislative Staff Employee II'),
(39, 'Local Legislative Staff Assistant II'),
(41, 'Municipal Government Assistant Department Head I(Municipal Health Officer)'),
(42, 'Nurse III'),
(45, 'Sanitation Inspector I'),
(46, 'Nurse II'),
(50, 'Midwife II'),
(56, 'Nursing Attendant'),
(57, 'Medical Technologist I'),
(64, 'LAboratory Aide II'),
(65, 'Administrative Aide IV (Driver II)'),
(66, 'Midwife I'),
(67, 'Nurse I'),
(70, 'Sanitation Inspector III'),
(71, 'Medical Officer III'),
(72, 'Midwife III'),
(73, 'Municipal Government Department I (Municipal Planning & Development Coordinator)'),
(74, 'Administrative Aide IV (Bookbinder II)'),
(75, 'Draftsman III'),
(78, 'Planning Officer II'),
(79, 'Municipal Government Department Head I(Municipal Budget Officer)'),
(82, 'Budgeting Assistant'),
(83, 'Budget Officer II'),
(84, 'Municipal Government Department Head I (Municipal Treasurer)'),
(87, 'Revenue Collection Clerk II'),
(91, '(Messenger) Administrative Aide II'),
(95, 'Municipal  Government Department Head I (Municipal Assessor)'),
(96, 'Tax Mapping Aide'),
(97, 'Assessment Clerk II'),
(99, 'Taxmapper II'),
(100, 'Municipal  Government Department Head I (Municipal Engineer)'),
(101, 'Labor Foreman'),
(106, 'Laborer I'),
(108, 'Carpenter I'),
(109, 'Plumber I'),
(111, 'Welder I'),
(113, 'Engineer II'),
(114, 'Municipal Government Department Head I'),
(115, 'Bookkeeper I'),
(116, 'Accounting Clerk I'),
(118, 'Management & Audit Analyst I'),
(119, 'Administrative Aide III'),
(120, 'Management & Audit Analyst I'),
(121, 'Accountant III'),
(122, 'Senior Bookkeeper'),
(123, 'Municipal Government Department Head I (Municipal Civil Registrar)'),
(124, 'Clerk IV'),
(127, 'Registration Officer III'),
(128, 'Municipal Government Department Head I (Municipal Social Welfare & Development Officer)'),
(129, 'Social Welfare Officer I'),
(131, 'Youth Development Assistant II'),
(132, 'Social Welfare Officer II'),
(133, 'Social Welfare Officer III'),
(134, 'Nutritionist-Dietitian'),
(135, 'Day Care Worker I'),
(136, 'Day Care Worker II'),
(137, 'Government Department Head I (Municipal Agriculturist)'),
(138, 'Agricultural Technologist'),
(139, 'Agriculturist II'),
(140, 'Agricultural Technician I'),
(141, 'Livestock Inspector I'),
(142, 'Market Supervisor III'),
(144, 'Meat Inspector'),
(145, 'Utility Worker I'),
(146, 'Administrative Officer II'),
(147, 'Mechanical Plant Operator II'),
(148, 'Driver I'),
(149, 'Project Development Assistant'),
(150, 'Bookbinder IV'),
(151, 'Project Development Officer III'),
(152, 'Engineer I'),
(153, 'Mechanic II'),
(154, 'Local DRRM Officer III'),
(155, 'Local DRRM Officer II'),
(156, 'Local DRRM Officer I'),
(157, 'Local DRRM Assistant'),
(158, 'Management Specialist'),
(159, 'Environmental Management  Specialist II'),
(160, 'Administrative Aide I');

-- --------------------------------------------------------

--
-- Table structure for table `emp_status`
--

CREATE TABLE `emp_status` (
  `empStat_id` int(11) NOT NULL,
  `emp_stat_name` varchar(255) NOT NULL,
  `position_class` varchar(55) NOT NULL,
  `status` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `emp_status`
--

INSERT INTO `emp_status` (`empStat_id`, `emp_stat_name`, `position_class`, `status`) VALUES
(1, 'Permanent', 'Career Positions', 'Active'),
(2, 'Co-terminous', 'Non-Career Positions', 'Active'),
(3, 'Temporary', 'Non-Career Positions', 'Active'),
(4, 'Job Order', 'Non-Career Positions', 'Active'),
(5, 'Elective', 'Non-Career Positions', 'Active'),
(6, 'End of Contract', '-', 'Separated'),
(7, 'Retired', '-', 'Separated');

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `file_id` int(11) NOT NULL,
  `personnel_id` int(11) NOT NULL,
  `folder_id` int(11) DEFAULT NULL,
  `uploaded_by_personnel_id` int(11) DEFAULT NULL,
  `uploaded_by_access` varchar(100) DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(255) NOT NULL,
  `date_time_uploaded` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gass`
--

CREATE TABLE `gass` (
  `gass_id` int(11) NOT NULL,
  `gass_name` int(11) NOT NULL,
  `level` varchar(55) NOT NULL,
  `step` int(11) NOT NULL,
  `ratePerDay` decimal(11,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `gass`
--

INSERT INTO `gass` (`gass_id`, `gass_name`, `level`, `step`, `ratePerDay`) VALUES
(1, 16, 'Second Level', 1, 1000.00),
(2, 16, 'Second Level', 2, 1000.00),
(3, 16, 'Second Level', 3, 1000.00),
(4, 16, 'Second Level', 4, 1000.00),
(5, 16, 'Second Level', 5, 1000.00),
(6, 16, 'Second Level', 6, 1000.00),
(7, 16, 'Second Level', 7, 1000.00),
(8, 16, 'Second Level', 8, 1000.00);

-- --------------------------------------------------------

--
-- Table structure for table `institution_preferences`
--

CREATE TABLE `institution_preferences` (
  `id` int(11) NOT NULL,
  `zip_code` varchar(12) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `region` varchar(255) NOT NULL,
  `division` varchar(255) NOT NULL,
  `institution_name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `emailAddress` varchar(255) NOT NULL,
  `contactNumber` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `institution_preferences`
--

INSERT INTO `institution_preferences` (`id`, `zip_code`, `logo`, `region`, `division`, `institution_name`, `address`, `emailAddress`, `contactNumber`) VALUES
(1, '6114', '97763-moh-seal.jpg', 'VI', '6', 'Municipal Government of Hinoba-an', 'Hinoba-an, Negros Occidental', 'hinobaanhr@gmail.com', '(034) 467-2540');

-- --------------------------------------------------------

--
-- Table structure for table `lap_dates`
--

CREATE TABLE `lap_dates` (
  `lap_dates_id` int(11) NOT NULL,
  `lap_code` varchar(10) NOT NULL,
  `leave_date_mm` varchar(2) NOT NULL,
  `leave_date_dd` varchar(2) NOT NULL,
  `leave_date_yyyy` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_applicants`
--

CREATE TABLE `leave_applicants` (
  `lap_id` int(11) NOT NULL,
  `leave_code` varchar(55) NOT NULL,
  `leave_date` varchar(10) DEFAULT NULL,
  `leave_type` varchar(255) NOT NULL,
  `leave_type_desc` varchar(255) NOT NULL,
  `substitute_id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `do_id` int(11) NOT NULL,
  `numDays` int(3) NOT NULL,
  `is_special` int(11) NOT NULL,
  `status` varchar(55) NOT NULL DEFAULT 'Pending',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_approved` varchar(10) DEFAULT NULL,
  `approved_by` int(11) NOT NULL,
  `leave_application_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `leave_applicants`
--

INSERT INTO `leave_applicants` (`lap_id`, `leave_code`, `leave_date`, `leave_type`, `leave_type_desc`, `substitute_id`, `applicant_id`, `do_id`, `numDays`, `is_special`, `status`, `date_created`, `date_approved`, `approved_by`, `leave_application_id`) VALUES
(9, 'KFAH5VOPE7', '2025-10-24', 'Vacation Leave - Within Philippines', '', 0, 14, 2, 1, 0, 'Pending', '2025-10-24 10:41:35', NULL, 0, 2),
(10, 'EDBKDN6UQ4', '2025-10-24', 'Vacation Leave', '', 0, 14, 2, 1, 0, 'Pending', '2025-10-24 11:02:50', NULL, 0, 3),
(11, 'MW5DITDZX9', '2025-10-27', 'Solo Parent Leave', '', 0, 14, 2, 1, 1, 'Pending', '2025-10-24 11:04:27', NULL, 0, 4),
(12, 'V2F8O74E9Z', '2025-10-24', 'Vacation Leave - Within Philippines', '', 0, 14, 2, 1, 0, 'Pending', '2025-10-24 11:13:42', NULL, 0, 5),
(13, 'TP40U3H33H', '2025-10-24', 'Vacation Leave', '', 0, 140, 14, 1, 0, 'Pending', '2025-10-24 13:42:52', NULL, 0, 6),
(14, 'VAB87AVHZV', '2025-10-24', 'Solo Parent Leave', '', 0, 140, 14, 9, 1, 'Pending', '2025-10-24 13:44:50', NULL, 0, 7),
(15, 'VAB87AVHZV', '2025-10-25', 'Solo Parent Leave', '', 0, 140, 14, 9, 1, 'Pending', '2025-10-24 13:44:50', NULL, 0, 7),
(16, 'VAB87AVHZV', '2025-10-26', 'Solo Parent Leave', '', 0, 140, 14, 9, 1, 'Pending', '2025-10-24 13:44:50', NULL, 0, 7),
(17, 'VAB87AVHZV', '2025-10-27', 'Solo Parent Leave', '', 0, 140, 14, 9, 1, 'Pending', '2025-10-24 13:44:50', NULL, 0, 7),
(18, 'VAB87AVHZV', '2025-10-28', 'Solo Parent Leave', '', 0, 140, 14, 9, 1, 'Pending', '2025-10-24 13:44:50', NULL, 0, 7),
(19, 'VAB87AVHZV', '2025-10-29', 'Solo Parent Leave', '', 0, 140, 14, 9, 1, 'Pending', '2025-10-24 13:44:50', NULL, 0, 7),
(20, 'VAB87AVHZV', '2025-10-30', 'Solo Parent Leave', '', 0, 140, 14, 9, 1, 'Pending', '2025-10-24 13:44:50', NULL, 0, 7),
(21, 'VAB87AVHZV', '2025-10-31', 'Solo Parent Leave', '', 0, 140, 14, 9, 1, 'Pending', '2025-10-24 13:44:50', NULL, 0, 7),
(22, 'VAB87AVHZV', '2025-11-01', 'Solo Parent Leave', '', 0, 140, 14, 9, 1, 'Pending', '2025-10-24 13:44:50', NULL, 0, 7),
(23, 'VAB87AVHZV', '2025-11-02', 'Solo Parent Leave', '', 0, 140, 14, 9, 1, 'Pending', '2025-10-24 13:44:50', NULL, 0, 7),
(24, 'VAB87AVHZV', '2025-11-03', 'Solo Parent Leave', '', 0, 140, 14, 9, 1, 'Pending', '2025-10-24 13:44:50', NULL, 0, 7),
(25, 'VAB87AVHZV', '2025-11-04', 'Solo Parent Leave', '', 0, 140, 14, 9, 1, 'Pending', '2025-10-24 13:44:50', NULL, 0, 7),
(26, 'VAB87AVHZV', '2025-11-05', 'Solo Parent Leave', '', 0, 140, 14, 9, 1, 'Pending', '2025-10-24 13:44:50', NULL, 0, 7),
(27, 'AK79I1MAXO', '2025-10-24', 'Others', 'Monetized Leave', 0, 140, 14, 10, 0, 'Pending', '2025-10-24 14:12:20', NULL, 0, 8),
(28, 'NI8RHQY70F', '2025-08-24', 'Others', 'Monetized Leave', 0, 140, 14, 5, 0, 'Pending', '2025-10-24 14:15:32', NULL, 0, 9),
(29, 'BG9DPPQ2I8', '2025-11-03', 'Others', 'Monetized Leave', 0, 140, 14, 5, 0, 'Pending', '2025-11-02 18:07:24', NULL, 0, 10),
(30, 'BG9DPPQ2I8', '2025-11-04', 'Others', 'Monetized Leave', 0, 140, 14, 5, 0, 'Pending', '2025-11-02 18:07:24', NULL, 0, 10),
(31, 'BG9DPPQ2I8', '2025-11-05', 'Others', 'Monetized Leave', 0, 140, 14, 5, 0, 'Pending', '2025-11-02 18:07:24', NULL, 0, 10),
(32, 'BG9DPPQ2I8', '2025-11-06', 'Others', 'Monetized Leave', 0, 140, 14, 5, 0, 'Pending', '2025-11-02 18:07:24', NULL, 0, 10),
(33, 'BG9DPPQ2I8', '2025-11-07', 'Others', 'Monetized Leave', 0, 140, 14, 5, 0, 'Pending', '2025-11-02 18:07:24', NULL, 0, 10),
(34, '6D0ZFAH57I', '2025-11-03', 'Others', 'Monetized Leave', 0, 140, 14, 5, 0, 'Pending', '2025-11-02 18:27:19', NULL, 0, 11),
(35, 'XBNR1FN9D6', '2025-11-04', 'Special Privilege Leave', '', 0, 140, 14, 4, 1, 'Pending', '2025-11-03 00:24:49', NULL, 0, 12),
(36, 'XBNR1FN9D6', '2025-11-05', 'Special Privilege Leave', '', 0, 140, 14, 4, 1, 'Pending', '2025-11-03 00:24:49', NULL, 0, 12),
(37, 'XBNR1FN9D6', '2025-11-06', 'Special Privilege Leave', '', 0, 140, 14, 4, 1, 'Pending', '2025-11-03 00:24:49', NULL, 0, 12),
(38, 'XBNR1FN9D6', '2025-11-07', 'Special Privilege Leave', '', 0, 140, 14, 4, 1, 'Pending', '2025-11-03 00:24:49', NULL, 0, 12),
(39, 'PQ1JMQO94C', '2025-11-03', 'Monetized Leave', '', 0, 140, 14, 10, 0, 'Pending', '2025-11-03 00:26:01', NULL, 0, 13),
(40, 'RWVFIXRJ7C', '2025-11-03', 'Monetized Leave', '', 0, 140, 14, 10, 0, 'Pending', '2025-11-03 00:47:40', NULL, 0, 14),
(41, '0GGKLJOGOT', '2025-01-27', 'Mandatory/Forced Leave', '', 0, 140, 14, 4, 0, 'Pending', '2025-11-11 15:17:06', NULL, 0, 15),
(42, '0GGKLJOGOT', '2025-01-28', 'Mandatory/Forced Leave', '', 0, 140, 14, 4, 0, 'Pending', '2025-11-11 15:17:06', NULL, 0, 15),
(43, '0GGKLJOGOT', '2025-01-29', 'Mandatory/Forced Leave', '', 0, 140, 14, 4, 0, 'Pending', '2025-11-11 15:17:06', NULL, 0, 15),
(44, '0GGKLJOGOT', '2025-01-30', 'Mandatory/Forced Leave', '', 0, 140, 14, 4, 0, 'Pending', '2025-11-11 15:17:06', NULL, 0, 15),
(45, '0GGKLJOGOT', '2025-01-31', 'Mandatory/Forced Leave', '', 0, 140, 14, 4, 0, 'Pending', '2025-11-11 15:17:06', NULL, 0, 15),
(46, '1AJOE1HT0I', '2025-11-11', 'Solo Parent Leave', '', 0, 140, 14, 4, 1, 'Pending', '2025-11-11 15:22:47', NULL, 0, 16),
(47, '1AJOE1HT0I', '2025-11-12', 'Solo Parent Leave', '', 0, 140, 14, 4, 1, 'Pending', '2025-11-11 15:22:47', NULL, 0, 16),
(48, '1AJOE1HT0I', '2025-11-13', 'Solo Parent Leave', '', 0, 140, 14, 4, 1, 'Pending', '2025-11-11 15:22:47', NULL, 0, 16),
(49, '1AJOE1HT0I', '2025-11-14', 'Solo Parent Leave', '', 0, 140, 14, 4, 1, 'Pending', '2025-11-11 15:22:47', NULL, 0, 16),
(50, '6QCSJ0RFBN', '2025-10-21', 'Special Privilege Leave', '', 0, 140, 14, 1, 1, 'Pending', '2025-11-12 14:43:09', NULL, 0, 17),
(51, 'QKT8AL3MB1', '2025-11-19', 'Special Privilege Leave', '', 0, 140, 14, 1, 1, 'Pending', '2025-11-21 16:12:12', NULL, 0, 18),
(52, 'PFLGDSQDAL', '2025-11-24', 'Solo Parent Leave', '', 0, 140, 14, 7, 1, 'Pending', '2025-11-21 16:24:18', NULL, 0, 19),
(53, 'PFLGDSQDAL', '2025-11-25', 'Solo Parent Leave', '', 0, 140, 14, 7, 1, 'Pending', '2025-11-21 16:24:18', NULL, 0, 19),
(54, 'PFLGDSQDAL', '2025-11-26', 'Solo Parent Leave', '', 0, 140, 14, 7, 1, 'Pending', '2025-11-21 16:24:18', NULL, 0, 19),
(55, 'PFLGDSQDAL', '2025-11-27', 'Solo Parent Leave', '', 0, 140, 14, 7, 1, 'Pending', '2025-11-21 16:24:18', NULL, 0, 19),
(56, 'SRZRWD1RSE', '2025-11-21', 'Monetized Leave', '', 0, 140, 14, 10, 0, 'Pending', '2025-11-21 16:25:38', NULL, 0, 20),
(57, 'U6BE2MB76A', '2026-06-01', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(58, 'U6BE2MB76A', '2026-06-02', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(59, 'U6BE2MB76A', '2026-06-03', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(60, 'U6BE2MB76A', '2026-06-04', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(61, 'U6BE2MB76A', '2026-06-05', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(62, 'U6BE2MB76A', '2026-06-06', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(63, 'U6BE2MB76A', '2026-06-07', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(64, 'U6BE2MB76A', '2026-06-08', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(65, 'U6BE2MB76A', '2026-06-09', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(66, 'U6BE2MB76A', '2026-06-10', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(67, 'U6BE2MB76A', '2026-06-11', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(68, 'U6BE2MB76A', '2026-06-12', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(69, 'U6BE2MB76A', '2026-06-13', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(70, 'U6BE2MB76A', '2026-06-14', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(71, 'U6BE2MB76A', '2026-06-15', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(72, 'U6BE2MB76A', '2026-06-16', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(73, 'U6BE2MB76A', '2026-06-17', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(74, 'U6BE2MB76A', '2026-06-18', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(75, 'U6BE2MB76A', '2026-06-19', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(76, 'U6BE2MB76A', '2026-06-20', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(77, 'U6BE2MB76A', '2026-06-21', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(78, 'U6BE2MB76A', '2026-06-22', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(79, 'U6BE2MB76A', '2026-06-23', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(80, 'U6BE2MB76A', '2026-06-24', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(81, 'U6BE2MB76A', '2026-06-25', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(82, 'U6BE2MB76A', '2026-06-26', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(83, 'U6BE2MB76A', '2026-06-27', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(84, 'U6BE2MB76A', '2026-06-28', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(85, 'U6BE2MB76A', '2026-06-29', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(86, 'U6BE2MB76A', '2026-06-30', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(87, 'U6BE2MB76A', '2026-07-01', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 11:31:40', NULL, 0, 21),
(88, '91WFM4OI6L', '2026-01-06', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 13:28:23', NULL, 0, 22),
(89, '91WFM4OI6L', '2026-01-07', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 13:28:23', NULL, 0, 22),
(90, 'LS23792EEK', '2026-01-06', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 13:34:56', NULL, 0, 23),
(91, 'LS23792EEK', '2026-01-07', 'Mandatory/Forced Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 13:34:56', NULL, 0, 23),
(92, 'T57T6D0VTQ', '2026-01-08', 'Sick Leave - Out Patient', '', 0, 140, 14, 1, 0, 'Pending', '2026-01-13 13:46:58', NULL, 0, 24),
(93, 'M1ID1SQWIE', '2026-01-13', 'Special Privilege Leave', '', 0, 140, 14, 3, 1, 'Pending', '2026-01-13 14:03:40', NULL, 0, 25),
(94, 'M1ID1SQWIE', '2026-01-14', 'Special Privilege Leave', '', 0, 140, 14, 3, 1, 'Pending', '2026-01-13 14:03:40', NULL, 0, 25),
(95, 'M1ID1SQWIE', '2026-01-15', 'Special Privilege Leave', '', 0, 140, 14, 3, 1, 'Pending', '2026-01-13 14:03:40', NULL, 0, 25),
(96, 'H1VEUYY533', '2026-01-19', 'Paternity Leave', '', 0, 140, 14, 7, 1, 'Pending', '2026-01-13 14:05:55', NULL, 0, 26),
(97, 'H1VEUYY533', '2026-01-20', 'Paternity Leave', '', 0, 140, 14, 7, 1, 'Pending', '2026-01-13 14:05:55', NULL, 0, 26),
(98, 'H1VEUYY533', '2026-01-21', 'Paternity Leave', '', 0, 140, 14, 7, 1, 'Pending', '2026-01-13 14:05:55', NULL, 0, 26),
(99, 'H1VEUYY533', '2026-01-22', 'Paternity Leave', '', 0, 140, 14, 7, 1, 'Pending', '2026-01-13 14:05:55', NULL, 0, 26),
(100, 'H1VEUYY533', '2026-01-23', 'Paternity Leave', '', 0, 140, 14, 7, 1, 'Pending', '2026-01-13 14:05:55', NULL, 0, 26),
(101, 'H1VEUYY533', '2026-01-24', 'Paternity Leave', '', 0, 140, 14, 7, 1, 'Pending', '2026-01-13 14:05:55', NULL, 0, 26),
(102, 'H1VEUYY533', '2026-01-25', 'Paternity Leave', '', 0, 140, 14, 7, 1, 'Pending', '2026-01-13 14:05:55', NULL, 0, 26),
(103, 'H1VEUYY533', '2026-01-26', 'Paternity Leave', '', 0, 140, 14, 7, 1, 'Pending', '2026-01-13 14:05:55', NULL, 0, 26),
(104, 'H1VEUYY533', '2026-01-27', 'Paternity Leave', '', 0, 140, 14, 7, 1, 'Pending', '2026-01-13 14:05:55', NULL, 0, 26),
(105, 'OHO59TS6KK', '2026-01-13', 'Monetized Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 18:11:44', NULL, 0, 27),
(106, 'OHO59TS6KK', '2026-01-14', 'Monetized Leave', '', 0, 140, 14, 2, 0, 'Pending', '2026-01-13 18:11:44', NULL, 0, 27),
(107, '0R0RVVSQKA', '2026-01-30', 'Vacation Leave - Abroad', '', 0, 140, 14, 1, 0, 'Pending', '2026-01-26 16:25:35', NULL, 0, 28);

-- --------------------------------------------------------

--
-- Table structure for table `leave_applications`
--

CREATE TABLE `leave_applications` (
  `id` int(11) NOT NULL,
  `leave_code` varchar(55) DEFAULT NULL,
  `personnel_id` int(11) NOT NULL,
  `office_agency` varchar(255) NOT NULL COMMENT 'Office/Agency/Department',
  `application_date` date NOT NULL COMMENT 'Date of filing',
  `leave_type` varchar(100) NOT NULL COMMENT 'Type of leave (Vacation, Sick, Maternity, etc.)',
  `other_leave_specification` varchar(255) DEFAULT NULL COMMENT 'Specification for "Others" leave type',
  `vacation_details` text DEFAULT NULL COMMENT 'Where vacation will be spent (within/abroad Philippines)',
  `sick_details` text DEFAULT NULL COMMENT 'Illness details or hospital name (in/out patient)',
  `study_details` text DEFAULT NULL COMMENT 'Study leave details (degree, university)',
  `inclusive_date_from` date NOT NULL COMMENT 'Start date of leave',
  `inclusive_date_to` date NOT NULL COMMENT 'End date of leave',
  `inclusive_dates_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'JSON array of date ranges: [{"from": "YYYY-MM-DD", "to": "YYYY-MM-DD"}, ...]' CHECK (json_valid(`inclusive_dates_json`)),
  `number_of_days` decimal(5,2) NOT NULL COMMENT 'Number of working days applied for',
  `commutation` enum('requested','not_requested') DEFAULT 'not_requested' COMMENT 'Commutation request status',
  `as_of_date` date DEFAULT NULL COMMENT 'Date for leave credits certification',
  `total_earned_vl` decimal(10,3) DEFAULT 0.000 COMMENT 'Total Vacation Leave earned',
  `total_earned_sl` decimal(10,3) DEFAULT 0.000 COMMENT 'Total Sick Leave earned',
  `less_application_vl` decimal(10,3) DEFAULT 0.000 COMMENT 'VL deduction for this application',
  `less_application_sl` decimal(10,3) DEFAULT 0.000 COMMENT 'SL deduction for this application',
  `balance_vl` decimal(10,3) DEFAULT 0.000 COMMENT 'VL balance after application',
  `balance_sl` decimal(10,3) DEFAULT 0.000 COMMENT 'SL balance after application',
  `status` enum('pending','approved','disapproved') DEFAULT 'pending' COMMENT 'Application status',
  `recommendation` text DEFAULT NULL COMMENT 'Recommendation or remarks from authorized officer',
  `approved_by` int(11) DEFAULT NULL COMMENT 'User ID who approved/disapproved',
  `approved_date` datetime DEFAULT NULL COMMENT 'Date and time of approval/disapproval',
  `leave_card_entry_id` int(11) DEFAULT NULL COMMENT 'Linked leave_card entry ID (auto-created on approval)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `less_application_vl_without_pay` decimal(10,3) DEFAULT 0.000 COMMENT 'VL without pay deduction for this application',
  `less_application_sl_without_pay` decimal(10,3) DEFAULT 0.000 COMMENT 'SL without pay deduction for this application'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='CS Form No. 6 - Leave Applications';

--
-- Dumping data for table `leave_applications`
--

INSERT INTO `leave_applications` (`id`, `leave_code`, `personnel_id`, `office_agency`, `application_date`, `leave_type`, `other_leave_specification`, `vacation_details`, `sick_details`, `study_details`, `inclusive_date_from`, `inclusive_date_to`, `inclusive_dates_json`, `number_of_days`, `commutation`, `as_of_date`, `total_earned_vl`, `total_earned_sl`, `less_application_vl`, `less_application_sl`, `balance_vl`, `balance_sl`, `status`, `recommendation`, `approved_by`, `approved_date`, `leave_card_entry_id`, `created_at`, `updated_at`, `less_application_vl_without_pay`, `less_application_sl_without_pay`) VALUES
(2, 'KFAH5VOPE7', 14, 'Office of the Municipal Mayor', '2025-10-24', 'Vacation Leave - Within Philippines', '', 'Cebu City', '', '', '2025-10-24', '2025-10-24', '[{\"from\": \"2025-10-24\", \"to\": \"2025-10-24\"}]', 1.00, 'requested', '2025-10-24', 0.000, 0.000, 0.000, 0.000, 0.000, 0.000, 'pending', NULL, NULL, NULL, 58, '2025-10-24 02:41:35', '2026-01-15 07:43:35', 0.000, 0.000),
(3, 'EDBKDN6UQ4', 14, 'Office of the Municipal Mayor', '2025-10-24', 'Vacation Leave', '', 'Cebu City', '', '', '2025-10-24', '2025-10-24', '[{\"from\": \"2025-10-24\", \"to\": \"2025-10-24\"}]', 1.00, 'requested', '2025-10-24', 1.250, 1.250, 1.000, 0.000, 0.250, 1.250, 'pending', NULL, NULL, NULL, 59, '2025-10-24 03:02:50', '2026-01-15 07:43:35', 0.000, 0.000),
(4, 'MW5DITDZX9', 14, 'Office of the Municipal Mayor', '2025-10-24', 'Solo Parent Leave', '', '', '', '', '2025-10-27', '2025-10-27', '[{\"from\": \"2025-10-27\", \"to\": \"2025-10-27\"}]', 1.00, 'requested', '2025-10-24', 1.250, 1.250, 0.000, 0.000, 1.250, 1.250, 'pending', NULL, NULL, NULL, 60, '2025-10-24 03:04:27', '2026-01-15 07:43:35', 0.000, 0.000),
(5, 'V2F8O74E9Z', 14, 'Office of the Municipal Mayor', '2025-10-24', 'Vacation Leave - Within Philippines', '', 'Cebu City', '', '', '2025-10-24', '2025-10-24', '[{\"from\": \"2025-10-24\", \"to\": \"2025-10-24\"}]', 1.00, 'requested', '2025-10-24', 1.250, 1.250, 1.000, 0.000, 0.250, 1.250, 'pending', NULL, NULL, NULL, 61, '2025-10-24 03:13:42', '2026-01-15 07:43:35', 0.000, 0.000),
(18, 'QKT8AL3MB1', 140, 'Market and Slaughterhouse Section', '2025-11-21', 'Special Privilege Leave', '', '', '', '', '2025-11-19', '2025-11-19', '[{\"from\": \"2025-11-19\", \"to\": \"2025-11-19\"}]', 1.00, 'not_requested', '2025-11-21', 11.625, 15.625, 1.000, 0.000, 10.625, 15.625, 'approved', '', NULL, NULL, 755, '2025-11-21 08:12:12', '2026-01-15 09:11:15', 0.000, 0.000),
(23, 'LS23792EEK', 140, 'Market and Slaughterhouse Section', '2026-01-05', 'Mandatory/Forced Leave', '', '', '', '', '2026-01-06', '2026-01-07', '[{\"from\": \"2026-01-06\", \"to\": \"2026-01-07\"}]', 2.00, 'requested', '2026-01-13', 14.125, 18.125, 2.000, 0.000, 12.125, 18.125, 'approved', '', NULL, NULL, 764, '2026-01-13 05:34:56', '2026-01-15 09:11:15', 0.000, 0.000),
(24, 'T57T6D0VTQ', 140, 'Market and Slaughterhouse Section', '2026-01-08', 'Sick Leave - Out Patient', '', '', '', '', '2026-01-08', '2026-01-08', '[{\"from\": \"2026-01-08\", \"to\": \"2026-01-08\"}]', 1.00, 'requested', '2026-01-13', 12.125, 18.125, 0.000, 1.000, 12.125, 17.125, 'approved', '', NULL, NULL, 765, '2026-01-13 05:46:58', '2026-01-15 09:11:15', 0.000, 0.000),
(25, 'M1ID1SQWIE', 140, 'Market and Slaughterhouse Section', '2026-01-13', 'Special Privilege Leave', '', '', '', '', '2026-01-13', '2026-01-15', '[{\"from\": \"2026-01-13\", \"to\": \"2026-01-15\"}]', 3.00, 'requested', '2026-01-13', 12.125, 18.125, 3.000, 0.000, 9.125, 18.125, 'approved', '', NULL, NULL, 766, '2026-01-13 06:03:40', '2026-01-15 09:11:15', 0.000, 0.000),
(26, 'H1VEUYY533', 140, 'Market and Slaughterhouse Section', '2026-01-13', 'Paternity Leave', '', '', '', '', '2026-01-19', '2026-01-27', '[{\"from\": \"2026-01-19\", \"to\": \"2026-01-27\"}]', 7.00, 'requested', '2026-01-13', 12.125, 18.125, 7.000, 0.000, 5.125, 18.125, 'approved', '', NULL, NULL, 767, '2026-01-13 06:05:55', '2026-01-15 09:11:15', 0.000, 0.000),
(27, 'OHO59TS6KK', 140, 'Market and Slaughterhouse Section', '2026-01-13', 'Monetized Leave', '', '', '', '', '2026-01-13', '2026-01-14', '[{\"from\": \"2026-01-13\", \"to\": \"2026-01-14\"}]', 2.00, 'not_requested', '2026-01-13', 12.125, 18.125, 1.000, 1.000, 11.125, 17.125, 'approved', '', NULL, NULL, 768, '2026-01-13 10:11:44', '2026-01-15 09:11:15', 0.000, 0.000),
(28, '0R0RVVSQKA', 140, 'Market and Slaughterhouse Section', '2026-01-26', 'Vacation Leave - Abroad', '', 'US', '', '', '2026-01-30', '2026-01-30', '[{\"from\":\"2026-01-30\",\"to\":\"2026-01-30\"}]', 1.00, 'requested', '2026-01-26', 11.125, 16.125, 1.000, 0.000, 10.125, 16.125, 'approved', '', NULL, NULL, 769, '2026-01-26 08:25:35', '2026-01-26 08:25:47', 0.000, 0.000);

-- --------------------------------------------------------

--
-- Table structure for table `leave_card`
--

CREATE TABLE `leave_card` (
  `id` int(11) NOT NULL,
  `personnel_id` int(11) NOT NULL,
  `period_from` date DEFAULT NULL,
  `period_to` date DEFAULT NULL,
  `particulars` varchar(255) DEFAULT NULL,
  `vl_earned` decimal(13,3) NOT NULL,
  `vl_with_pay` decimal(13,3) NOT NULL,
  `vl_without_pay` decimal(13,3) NOT NULL,
  `sl_earned` decimal(13,3) NOT NULL,
  `sl_with_pay` decimal(13,3) NOT NULL,
  `sl_without_pay` decimal(13,3) NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `is_special_leave` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Special leave indicator - no leave credit deductions',
  `created_from_application` tinyint(1) DEFAULT 0 COMMENT 'Auto-created from approved leave application',
  `date_from` date DEFAULT NULL COMMENT 'Leave start date',
  `date_to` date DEFAULT NULL COMMENT 'Leave end date',
  `number_of_days` decimal(5,2) DEFAULT NULL COMMENT 'Number of leave days'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_card`
--

INSERT INTO `leave_card` (`id`, `personnel_id`, `period_from`, `period_to`, `particulars`, `vl_earned`, `vl_with_pay`, `vl_without_pay`, `sl_earned`, `sl_with_pay`, `sl_without_pay`, `remarks`, `is_special_leave`, `created_from_application`, `date_from`, `date_to`, `number_of_days`) VALUES
(1, 66, '2010-01-01', '2010-01-31', 'januaru 1-31, 2010', 10.350, 0.000, 0.000, 39.390, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(2, 66, '2025-02-02', '2025-02-02', 'monetized leave', 0.000, 5.000, 0.000, 0.000, 5.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(3, 66, '2025-02-03', '2025-02-03', 'monetized leave', 0.000, 5.000, 0.000, 0.000, 5.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(4, 73, '2023-03-16', '2023-09-30', '', 8.125, 0.000, 0.000, 8.125, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(5, 73, '2023-10-31', '2023-11-03', '', 0.000, 2.000, 0.000, 0.000, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(6, 73, '2023-10-01', '2023-12-31', '', 3.750, 0.000, 0.000, 3.750, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(7, 66, '2025-03-31', '2025-03-31', 'Special Leave', 0.000, 0.000, 3.000, 0.000, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(8, 101, '2016-09-26', '2016-09-30', 'Leave Credit', 0.210, 0.000, 0.000, 0.210, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(9, 101, '2016-10-01', '2016-12-31', 'Leave Credit', 3.750, 0.000, 0.000, 3.750, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(10, 101, '2016-12-31', '2016-12-31', 'Mandatory', 0.000, 1.280, 0.000, 0.000, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(11, 101, '2016-11-28', '2016-12-02', 'Sick Leave', 0.000, 0.000, 0.000, 0.000, 5.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(12, 101, '2017-01-01', '2017-03-31', 'Leave Credit', 3.750, 0.000, 0.000, 3.750, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(13, 101, '2017-03-28', '2017-03-29', 'Sick Leave', 0.000, 0.000, 0.000, 0.000, 2.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(14, 101, '2017-04-01', '2017-04-03', 'Sick Leave', 0.000, 0.000, 0.000, 0.000, 3.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(15, 101, '2017-04-01', '2017-04-30', 'Leave Credit', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(16, 117, '2016-10-03', '2016-10-31', 'Leave Credit', 1.167, 0.000, 0.000, 1.167, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(17, 117, '2016-11-01', '2016-12-31', 'Leave Credit', 2.500, 0.000, 0.000, 2.500, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(18, 117, '2016-12-31', '2016-12-31', 'Mandatory Leave', 0.000, 1.250, 0.000, 0.000, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(19, 117, '2017-01-01', '2017-10-31', 'Leave Credit', 12.500, 0.000, 0.000, 12.500, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(20, 117, '2017-11-17', '2017-11-17', 'monetized leave', 0.000, 10.000, 0.000, 0.000, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(21, 117, '2017-11-01', '2017-12-31', 'Leave Credit', 2.500, 0.000, 0.000, 2.500, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(22, 117, '2017-12-31', '2017-12-31', 'Mandatory', 0.000, 5.000, 0.000, 0.000, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(23, 117, '2018-01-01', '2018-02-28', 'Leave Credit', 2.500, 0.000, 0.000, 2.500, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(24, 117, '2018-03-20', '2018-03-22', 'Special Leave', 0.000, 0.000, 3.000, 0.000, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(25, 117, '2018-03-01', '2025-09-30', 'Leave Credit', 10.500, 0.000, 0.000, 10.500, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(26, 117, '2018-11-06', '2018-11-09', 'Mandatory', 0.000, 4.000, 0.000, 0.000, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(27, 117, '2018-11-12', '0000-00-00', 'Mandatory', 0.000, 1.000, 0.000, 0.000, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(28, 117, '2018-10-01', '2018-12-31', 'Leave Credit', 2.500, 0.000, 0.000, 2.500, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(29, 117, '2019-01-01', '2019-01-31', 'Leave Credit', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(30, 117, '2019-02-14', '2019-02-14', 'monetized leave', 0.000, 10.000, 0.000, 0.000, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(31, 117, '2019-02-01', '2019-07-31', 'Leave Credit', 7.500, 0.000, 0.000, 7.500, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(32, 117, '2019-08-20', '2019-08-20', 'monetized leave', 0.000, 5.000, 0.000, 0.000, 5.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(33, 117, '2019-08-01', '2019-08-31', 'Leave Credit', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(34, 117, '2019-09-11', '2019-09-13', 'Mandatory', 0.000, 3.000, 0.000, 0.000, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(35, 117, '2019-09-01', '2019-09-30', 'Leave Credit', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(36, 117, '2019-10-03', '2019-10-04', 'Mandatory', 0.000, 2.000, 0.000, 0.000, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(37, 117, '2019-10-01', '2019-11-30', 'Leave Credit', 2.500, 0.000, 0.000, 2.500, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(38, 117, '2019-12-10', '2019-12-11', 'solo parent', 0.000, 0.000, 2.000, 0.000, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(39, 117, '2019-12-16', '2019-12-20', 'solo parent', 0.000, 0.000, 5.000, 0.000, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(40, 117, '2019-12-01', '2019-12-31', 'Leave Credit', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(41, 117, '2020-01-01', '2020-05-31', 'Leave Credit', 6.250, 0.000, 0.000, 6.250, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(42, 117, '2020-06-09', '2020-06-09', 'monetized leave', 0.000, 10.000, 0.000, 0.000, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(43, 117, '2020-06-24', '2020-06-26', 'Special Leave', 0.000, 0.000, 3.000, 0.000, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(44, 117, '2020-06-01', '2020-10-30', 'Leave Credit', 6.250, 0.000, 0.000, 6.250, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(45, 117, '2020-11-23', '2020-11-27', 'Mandatory', 0.000, 5.000, 0.000, 0.000, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(46, 117, '2020-11-01', '2020-11-30', 'Leave Credit', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(47, 117, '2020-12-17', '2020-12-18', 'solo parent', 0.000, 0.000, 2.000, 0.000, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(48, 117, '2020-12-21', '2020-12-23', 'solo parent', 0.000, 0.000, 3.000, 0.000, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(49, 117, '2020-12-28', '2020-12-29', 'solo parent', 0.000, 0.000, 2.000, 0.000, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(50, 117, '2020-12-01', '2020-12-31', '', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(51, 140, '2024-10-16', '2024-12-31', '', 3.125, 0.000, 0.000, 3.125, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(52, 140, '2025-01-01', '2025-01-31', '', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(53, 140, '2025-01-27', '2025-01-31', 'Mandatory', 0.000, 4.000, 0.000, 0.000, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(54, 140, '2025-02-01', '2025-06-30', '', 6.250, 0.000, 0.000, 6.250, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(56, 101, '2025-10-20', '2025-10-24', 'Test Entry', 0.000, 5.000, 0.000, 0.000, 0.000, 0.000, '', 1, 0, NULL, NULL, NULL),
(57, 14, '2025-09-01', '2025-09-30', '', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(61, 14, '2025-10-24', '2025-10-24', 'Vacation Leave', 0.000, 1.000, 0.000, 0.000, 0.000, 0.000, NULL, 0, 1, '2025-10-24', '2025-10-24', 1.00),
(742, 14, '2025-10-01', '2025-10-31', 'Month of October 2025', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2025-10-01', '2025-10-31', NULL),
(743, 132, '2025-10-01', '2025-10-31', 'Month of October 2025', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2025-10-01', '2025-10-31', NULL),
(754, 140, '2025-07-01', '2025-10-31', '', 5.000, 0.000, 0.000, 5.000, 0.000, 0.000, '', 0, 0, NULL, NULL, NULL),
(755, 140, '2025-11-19', '2025-11-19', 'Special Privilege Leave', 0.000, 1.000, 0.000, 0.000, 0.000, 0.000, NULL, 1, 1, '2025-11-19', '2025-11-19', 1.00),
(758, 140, '2025-12-01', '2025-12-31', 'Month of December 2025', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2025-12-01', '2025-12-31', NULL),
(759, 14, '2025-12-01', '2025-12-31', 'Month of December 2025', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2025-12-01', '2025-12-31', NULL),
(760, 132, '2025-12-01', '2025-12-31', 'Month of December 2025', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2025-12-01', '2025-12-31', NULL),
(762, 140, '2026-01-01', '2026-01-01', 'JANUARY', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Earned', 0, 0, NULL, NULL, NULL),
(764, 140, '2026-01-06', '2026-01-07', 'Mandatory/Forced Leave', 0.000, 2.000, 0.000, 0.000, 0.000, 0.000, NULL, 0, 1, '2026-01-06', '2026-01-07', 2.00),
(765, 140, '2026-01-08', '2026-01-08', 'Sick Leave', 0.000, 0.000, 0.000, 0.000, 1.000, 0.000, NULL, 0, 1, '2026-01-08', '2026-01-08', 1.00),
(766, 140, '2026-01-13', '2026-01-15', 'Special Privilege Leave', 0.000, 3.000, 0.000, 0.000, 0.000, 0.000, NULL, 1, 1, '2026-01-13', '2026-01-15', 3.00),
(767, 140, '2026-01-19', '2026-01-27', 'Paternity Leave', 0.000, 7.000, 0.000, 0.000, 0.000, 0.000, NULL, 1, 1, '2026-01-19', '2026-01-27', 7.00),
(768, 140, '2026-01-01', '2026-01-31', 'Monetized Leave', 0.000, 1.000, 0.000, 0.000, 1.000, 0.000, NULL, 0, 1, '2026-01-13', '2026-01-14', 2.00),
(769, 140, '2026-01-01', '2026-01-31', 'Vacation Leave', 0.000, 1.000, 0.000, 0.000, 0.000, 0.000, NULL, 0, 1, '2026-01-30', '2026-01-30', 1.00),
(770, 140, '2026-02-01', '2026-02-28', 'Month of February 2026', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2026-02-01', '2026-02-28', NULL),
(771, 14, '2026-02-01', '2026-02-28', 'Month of February 2026', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2026-02-01', '2026-02-28', NULL),
(772, 132, '2026-02-01', '2026-02-28', 'Month of February 2026', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2026-02-01', '2026-02-28', NULL),
(773, 140, '2026-03-01', '2026-03-31', 'Month of March 2026', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2026-03-01', '2026-03-31', NULL),
(774, 14, '2026-03-01', '2026-03-31', 'Month of March 2026', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2026-03-01', '2026-03-31', NULL),
(775, 132, '2026-03-01', '2026-03-31', 'Month of March 2026', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2026-03-01', '2026-03-31', NULL),
(776, 677, '2026-03-01', '2026-03-31', 'Month of March 2026', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2026-03-01', '2026-03-31', NULL),
(777, 140, '2026-04-01', '2026-04-30', 'Month of April 2026', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2026-04-01', '2026-04-30', NULL),
(778, 14, '2026-04-01', '2026-04-30', 'Month of April 2026', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2026-04-01', '2026-04-30', NULL),
(779, 132, '2026-04-01', '2026-04-30', 'Month of April 2026', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2026-04-01', '2026-04-30', NULL),
(780, 677, '2026-04-01', '2026-04-30', 'Month of April 2026', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2026-04-01', '2026-04-30', NULL),
(781, 140, '2026-05-01', '2026-05-31', 'Month of May 2026', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2026-05-01', '2026-05-31', NULL),
(782, 14, '2026-05-01', '2026-05-31', 'Month of May 2026', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2026-05-01', '2026-05-31', NULL),
(783, 132, '2026-05-01', '2026-05-31', 'Month of May 2026', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2026-05-01', '2026-05-31', NULL),
(784, 677, '2026-05-01', '2026-05-31', 'Month of May 2026', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2026-05-01', '2026-05-31', NULL),
(785, 140, '2026-06-01', '2026-06-30', 'Month of June 2026', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2026-06-01', '2026-06-30', NULL),
(786, 14, '2026-06-01', '2026-06-30', 'Month of June 2026', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2026-06-01', '2026-06-30', NULL),
(787, 132, '2026-06-01', '2026-06-30', 'Month of June 2026', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2026-06-01', '2026-06-30', NULL),
(788, 677, '2026-06-01', '2026-06-30', 'Month of June 2026', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2026-06-01', '2026-06-30', NULL),
(789, 140, '2026-07-01', '2026-07-31', 'Month of July 2026', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2026-07-01', '2026-07-31', NULL),
(790, 14, '2026-07-01', '2026-07-31', 'Month of July 2026', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2026-07-01', '2026-07-31', NULL),
(791, 132, '2026-07-01', '2026-07-31', 'Month of July 2026', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2026-07-01', '2026-07-31', NULL),
(792, 677, '2026-07-01', '2026-07-31', 'Month of July 2026', 1.250, 0.000, 0.000, 1.250, 0.000, 0.000, 'Monthly Leave Credits', 0, 0, '2026-07-01', '2026-07-31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `monthly_leave_credits_log`
--

CREATE TABLE `monthly_leave_credits_log` (
  `id` int(11) NOT NULL,
  `personnel_id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `vl_earned` decimal(13,3) DEFAULT 1.250,
  `sl_earned` decimal(13,3) DEFAULT 1.250,
  `leave_card_id` int(11) DEFAULT NULL,
  `processed_date` datetime DEFAULT current_timestamp(),
  `processed_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `monthly_leave_credits_log`
--

INSERT INTO `monthly_leave_credits_log` (`id`, `personnel_id`, `year`, `month`, `vl_earned`, `sl_earned`, `leave_card_id`, `processed_date`, `processed_by`) VALUES
(8, 140, 2025, 10, 1.250, 1.250, 741, '2025-11-03 00:20:38', 3),
(9, 14, 2025, 10, 1.250, 1.250, 742, '2025-11-03 00:20:38', 3),
(10, 132, 2025, 10, 1.250, 1.250, 743, '2025-11-03 00:20:38', 3),
(11, 140, 2025, 12, 1.250, 1.250, 758, '2026-01-13 11:13:19', 3),
(12, 14, 2025, 12, 1.250, 1.250, 759, '2026-01-13 11:13:19', 3),
(13, 132, 2025, 12, 1.250, 1.250, 760, '2026-01-13 11:13:19', 3),
(14, 140, 2026, 2, 1.250, 1.250, 770, '2026-03-06 14:33:02', 3),
(15, 14, 2026, 2, 1.250, 1.250, 771, '2026-03-06 14:33:02', 3),
(16, 132, 2026, 2, 1.250, 1.250, 772, '2026-03-06 14:33:02', 3),
(17, 140, 2026, 3, 1.250, 1.250, 773, '2026-04-22 00:20:57', 3),
(18, 14, 2026, 3, 1.250, 1.250, 774, '2026-04-22 00:20:57', 3),
(19, 132, 2026, 3, 1.250, 1.250, 775, '2026-04-22 00:20:57', 3),
(20, 677, 2026, 3, 1.250, 1.250, 776, '2026-04-22 00:20:57', 3),
(21, 140, 2026, 4, 1.250, 1.250, 777, '2026-05-19 11:27:59', 3),
(22, 14, 2026, 4, 1.250, 1.250, 778, '2026-05-19 11:27:59', 3),
(23, 132, 2026, 4, 1.250, 1.250, 779, '2026-05-19 11:27:59', 3),
(24, 677, 2026, 4, 1.250, 1.250, 780, '2026-05-19 11:27:59', 3),
(25, 140, 2026, 5, 1.250, 1.250, 781, '2026-06-23 15:47:49', 3),
(26, 14, 2026, 5, 1.250, 1.250, 782, '2026-06-23 15:47:49', 3),
(27, 132, 2026, 5, 1.250, 1.250, 783, '2026-06-23 15:47:49', 3),
(28, 677, 2026, 5, 1.250, 1.250, 784, '2026-06-23 15:47:49', 3),
(29, 140, 2026, 6, 1.250, 1.250, 785, '2026-07-28 16:49:15', 3),
(30, 14, 2026, 6, 1.250, 1.250, 786, '2026-07-28 16:49:15', 3),
(31, 132, 2026, 6, 1.250, 1.250, 787, '2026-07-28 16:49:15', 3),
(32, 677, 2026, 6, 1.250, 1.250, 788, '2026-07-28 16:49:15', 3),
(33, 140, 2026, 7, 1.250, 1.250, 789, '2026-08-02 13:45:29', 3),
(34, 14, 2026, 7, 1.250, 1.250, 790, '2026-08-02 13:45:29', 3),
(35, 132, 2026, 7, 1.250, 1.250, 791, '2026-08-02 13:45:29', 3),
(36, 677, 2026, 7, 1.250, 1.250, 792, '2026-08-02 13:45:29', 3);

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `news_id` int(11) NOT NULL,
  `news_title` varchar(255) NOT NULL,
  `news_contents` text NOT NULL,
  `dateTime` varchar(255) NOT NULL,
  `posted_by` varchar(255) NOT NULL,
  `ipAddress` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`news_id`, `news_title`, `news_contents`, `dateTime`, `posted_by`, `ipAddress`) VALUES
(3, 'Health Advisory', 'Please wear your ID and follow health safety protocols at all times.', '05/19/2026 | 03:36:07 PM', 'Admin', '10.174.189.58'),
(4, 'System Notice', 'RFID logging is available from 7:00 AM to 6:00 PM.', '05/19/2026 | 03:36:07 PM', 'Admin', '10.174.189.58'),
(5, 'Attendance Reminder', 'Employees are advised to log AM IN, AM OUT, PM IN, and PM OUT accurately.', '05/19/2026 | 03:36:07 PM', 'Admin', '10.174.189.58'),
(6, 'HR Announcement', 'Submit leave applications at least 3 working days before the requested date.', '05/19/2026 | 03:36:07 PM', 'Admin', '10.174.189.58'),
(7, 'Training Update', 'Mandatory orientation for new personnel will be held this Friday at 2:00 PM.', '05/19/2026 | 03:36:07 PM', 'Admin', '10.174.189.58'),
(8, 'Payroll Reminder', 'Kindly verify your attendance records before payroll cut-off every 25th.', '05/19/2026 | 03:36:07 PM', 'Admin', '10.174.189.58'),
(9, 'Security Notice', 'Do not share your RFID card with other personnel. Violations are subject to sanction.', '05/19/2026 | 03:36:07 PM', 'Admin', '10.174.189.58'),
(10, 'Facility Advisory', 'Please keep work areas clean and dispose waste properly.', '05/19/2026 | 03:36:07 PM', 'Admin', '10.174.189.58'),
(11, 'IT Advisory', 'Report kiosk or scanner issues immediately to the IT support desk.', '05/19/2026 | 03:36:07 PM', 'Admin', '10.174.189.58'),
(12, 'General Announcement', 'Thank you for your cooperation in maintaining efficient HRMS operations.', '05/19/2026 | 03:36:07 PM', 'Admin', '10.174.189.58');

-- --------------------------------------------------------

--
-- Table structure for table `personnels`
--

CREATE TABLE `personnels` (
  `personnel_id` int(11) NOT NULL,
  `RFTag_id` varchar(25) NOT NULL,
  `personnel_id_code` varchar(25) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `img` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mname` varchar(255) NOT NULL,
  `suffix` varchar(5) NOT NULL,
  `age` int(11) NOT NULL,
  `sex` varchar(6) NOT NULL,
  `marital_status` varchar(15) NOT NULL,
  `bdMM` varchar(2) NOT NULL,
  `bdDD` varchar(2) NOT NULL,
  `bdYYYY` varchar(4) NOT NULL,
  `birth_place` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `personal_pnum` varchar(15) NOT NULL,
  `emergency_pnum` varchar(15) NOT NULL,
  `conPerson_lname` varchar(55) NOT NULL,
  `conPerson_fname` varchar(55) NOT NULL,
  `conPerson_mname` varchar(55) NOT NULL,
  `conPerson_relationship` varchar(55) NOT NULL,
  `do_id` int(11) NOT NULL,
  `des_id` int(11) NOT NULL,
  `sal_grade` int(11) NOT NULL,
  `sal_step` int(11) NOT NULL,
  `sal_level` int(11) NOT NULL,
  `rate_per_day` decimal(13,2) NOT NULL,
  `gass_id` int(11) NOT NULL,
  `empStat_id` int(11) NOT NULL,
  `eligibility` varchar(255) NOT NULL,
  `plantilla_num` varchar(25) NOT NULL,
  `appointment_date` varchar(10) NOT NULL,
  `separation_date` varchar(10) DEFAULT NULL,
  `num_of_yrs` int(11) NOT NULL,
  `tin_num` varchar(25) NOT NULL,
  `gsis_num` varchar(25) NOT NULL,
  `pagibig_num` varchar(25) NOT NULL,
  `philHealth_num` varchar(25) NOT NULL,
  `monthly_salary` decimal(14,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `personnels`
--

INSERT INTO `personnels` (`personnel_id`, `RFTag_id`, `personnel_id_code`, `shift_id`, `img`, `lname`, `fname`, `mname`, `suffix`, `age`, `sex`, `marital_status`, `bdMM`, `bdDD`, `bdYYYY`, `birth_place`, `address`, `email`, `personal_pnum`, `emergency_pnum`, `conPerson_lname`, `conPerson_fname`, `conPerson_mname`, `conPerson_relationship`, `do_id`, `des_id`, `sal_grade`, `sal_step`, `sal_level`, `rate_per_day`, `gass_id`, `empStat_id`, `eligibility`, `plantilla_num`, `appointment_date`, `separation_date`, `num_of_yrs`, `tin_num`, `gsis_num`, `pagibig_num`, `philHealth_num`, `monthly_salary`) VALUES
(2, 'CPGWPYTB3H', 'E-DAVR-712022-1', 3, 'default_img.jpg', 'RELIQUIAS', 'DAPH ANTHONY', 'VIDAURRAZAGA', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 2, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(3, 'UGYT3P0WEX', 'CT-SOT-732023-2', 3, 'default_img.jpg', 'TIANGA', 'SERAFIN', 'ORENDAIN', 'JR.', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 2, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(4, 'M44PLOGP0U', 'P-JCC-1212019-3A', 3, 'default_img.jpg', 'CASTILLO', 'JOEPET', 'CANILLADA', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 2, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(6, 'P-LBG-312007-6', 'P-LBG-312007-6', 3, 'default_img.jpg', 'GESTOSO', 'LIVIO', 'BAVIERA', 'JR.', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 2, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(7, '0LKJLM2C22', 'P-LMGM-632024-6', 3, 'default_img.jpg', 'MANGILIMUTAN', 'LORRAINE MAE', 'GESTOSO', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 2, 0, 1, 1, 1, 0.01, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(8, 'P-JAP-12212021-7', 'P-JAP-12212021-7', 3, 'default_img.jpg', 'PINONGGAN', 'JOSEPH', 'ALBERIO', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 2, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(9, 'N2CS52WG6G', 'P-GJEG-3162023-8', 3, 'default_img.jpg', 'GIGANAN', 'GRACE JOY', 'ESTRAO', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 2, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(10, 'IO1IY5L4VB', 'P-RAC-872008-10', 3, 'default_img.jpg', 'CORTADO', 'ROGELIO', 'ALFANTA', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 2, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(11, 'D4ZWVFKO1E', 'P-MML-3291999-12', 3, 'default_img.jpg', 'LLAMADO', 'MARISSA', 'MAQUILING', '-', 0, 'Female', 'Single', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 2, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(12, 'UV0LOFMFS3', 'P-JFL-732023-12', 3, 'default_img.jpg', 'LOGRONIO', 'JOESIFIL', 'FAJARDO', '-', 0, 'Male', 'Single', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 2, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(14, 'T1X1EOAO50', 'P-GOH-3162022-16', 3, 'default_img.jpg', 'HERRADURA', 'GINALYN ', 'OBENZA', '-', 50, 'Female', 'Married', '01', '16', '1976', 'Bacolod City', 'Purok 3, Barangay I, Hinoba-an, Negros Occidental', '', '+639386707010', '+639072789861', 'HERRADURA', 'EDMON JR.', 'AREVALO', 'Spouse', 2, 17, 18, 1, 2, 44114.00, 0, 1, 'CS Professional', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(16, '1Z6LX20PVU', 'CT-JMBR-952022-17', 3, 'default_img.jpg', 'RELIQUIAS', 'JOHN MARK', 'BALUNGCAS', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 23, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(17, '55SDU3PJSI', 'E-JLE-812000-20', 3, 'default_img.jpg', 'ENCOY', 'JEFRE', 'LAZALITA', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 24, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(18, 'KE4HXNTQTI', 'E-FJLB-12172007-24', 3, 'default_img.jpg', 'BILBAO', 'FRANCISCO JOSE', 'LOCSIN', 'JR.', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 24, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(19, 'SPRSLW2YQM', 'E-JGO-8252017-23', 3, 'default_img.jpg', 'OCTAVIO', 'JODYBONNE', 'GAYATIN', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 24, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(20, 'E6FJPBZ5BD', 'E-JTT-712019-20A', 3, 'default_img.jpg', 'TUPAS', 'JASON', 'TEMBREVILLA', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 24, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(21, 'WXV3CGLSBO', 'E-JRMG-712016-23', 3, 'default_img.jpg', 'GAYOMALE', 'JOSE ROBERT', 'MILLAN', 'JR.', 0, 'Male', 'Single', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 24, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(22, 'WEYJU63LLE', 'E-MTLB-122024-32', 3, 'default_img.jpg', 'BILBAO', 'MA. TERESA', 'LOCSIN', '-', 0, 'Female', 'Widowed', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 24, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(23, 'KCTG5YC64S', 'E-TCT-712022-25', 3, 'default_img.jpg', 'TUBILLEJA', 'THEODORE', 'DELA CRUZ', 'SR.', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 24, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(24, 'NT36Z4UITB', 'P-EFM-322020-57B', 3, 'default_img.jpg', 'MALAYO', 'EDGARDO', 'FLORES', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 24, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(25, '44SB4W0MF3', 'E-MPV-712023-29', 3, 'default_img.jpg', 'VIDAURRAZAGA', 'MC', 'PEPITO', '-', 0, 'Female', 'Single', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 24, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(26, '6DZNSFDRJG', 'P-TEM-8162021-27', 3, 'default_img.jpg', 'MANOS', 'TEOFILO', 'ENCARGUEZ', 'JR.', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 24, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(27, 'ZGMTZ2Y3BT', 'P-PJLO-732023-32', 3, 'default_img.jpg', 'OCTAVIO', 'PETER JOHN ', 'LAZALITA', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 24, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(28, 'DCORCD1ICP', 'P-LADGT-412024-35', 3, 'default_img.jpg', 'TUMA-OB', 'LESLIE AIKEE DYAN', 'GIGANAN', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 24, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(29, 'BSY3X4CY3E', 'P-JCS-412024-36', 3, 'default_img.jpg', 'SANTES', 'JOCELYN', 'CORONEL', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 24, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(30, 'WQ4X6QJYGH', 'P-JVS-3201997-32', 3, 'default_img.jpg', 'ANLIQUERA', 'SIENA', 'VASQUEZ', '-', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(31, '1QETKAFQWO', 'P-JED-3162022-35', 3, 'default_img.jpg', 'DELOTINA', 'JOFEL', 'EVANGELISTA', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 24, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(32, '2FUFQBHUQM', 'P-LVD-412024-39', 3, 'default_img.jpg', 'DECENA', 'LANNE', 'VILLARETE', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 24, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(33, 'HSDQZ6G6PA', 'P-JMLP-412024-40', 3, 'default_img.jpg', 'PEREZ', 'JOSE MARIA', 'LIM', 'JR.', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(34, 'LUPQ4RIQUJ', 'P-RCT-751994-39', 3, 'default_img.jpg', 'TILOS', 'RIZALIE', 'CELIZ', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(35, 'ZXDNHZFPMW', 'P-NTP-582023-41', 3, 'default_img.jpg', 'PUBLICO', 'NOEMI', 'TOMADO', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(36, 'LVX5C46TAS', 'P-CLS-3162016-42', 3, 'default_img.jpg', 'SILVESTRE', 'CYNTHIA', 'LAMBOT', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(37, 'I03I6VZELI', 'P-MJCG-5162024-44', 3, 'default_img.jpg', 'GALAN', 'MARY JANE', 'CELIS', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(38, 'MS5LRO1IJE', 'P-REV-142021-38', 3, 'default_img.jpg', 'VILLANUEVA', 'ROSALIE', 'ELARDO', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(39, 'HDMCPVKYB1', 'P-MAVR-812000-44', 3, 'default_img.jpg', 'ROXAS', 'MARY ANN', 'VILLAFUERTE', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(40, 'ZV1EXSSS30', 'P-GHB-711991-47', 3, 'default_img.jpg', 'BONGCAWEL', 'GENELYN ', 'HISONA', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(41, 'V2M0IIAYRM', 'P-RVV-531993-49', 3, 'default_img.jpg', 'VILLANUEVA', 'RAYGILDA', 'VERGARA', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(42, 'QESM444ZK0', 'P-RET-531993-50', 3, 'default_img.jpg', 'TEMBREVILLA', 'RONA', 'ESCOSAR', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(43, 'DFR5QGBYHW', 'P-PMB-9211998-52', 3, 'default_img.jpg', 'BARO', 'PHOEBE', 'MANILINGAN', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(44, '6ON2RWCQEM', 'P-MCG-712017-50', 3, 'default_img.jpg', 'GAREZA', 'MELANIE', 'CELIS', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(45, 'RCCO5TPACC', 'P-EMP-11162005-55', 3, 'default_img.jpg', 'PIMENTEL', 'EMY', 'MAGBANUA', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(46, '5UVF66ZPY0', 'P-WRM-712012-56', 3, 'default_img.jpg', 'MAQUILING', 'WARREN', 'RAMIREZ', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '07/01/2012', NULL, 13, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', 0.000),
(47, 'GZ5LO0QDRC', 'P-AAG-1012011-66', 3, 'default_img.jpg', 'GARCIA', 'ADELA', 'ANTOLIN', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(48, 'HEPAB6X3PL', 'P-LBV-10162020-56B', 3, 'default_img.jpg', 'VILLAMATER', 'LORALYN', 'BARROCA', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(49, '2O1JSGHAYX', 'P-RBG-551994-5', 3, 'default_img.jpg', 'GAUAL', 'ROLIN', 'BERGONIO', 'SR.', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(50, 'MHDZI160KN', 'P-CCT-612015-64', 3, 'default_img.jpg', 'TEMBREVILLA', 'CAROLINE', 'CANILLO', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(51, 'ZHXY3CO3KQ', 'P-EODF-612015-65Z', 3, 'default_img.jpg', 'DELA FUENTE', 'EBER', 'ORMEO', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(52, 'WSJIHMXEN6', 'P-MCT-1181994-59', 3, 'default_img.jpg', 'TILOS', 'MARCELINA', 'CELIS', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(53, 'I0FY3RFXM5', 'P-OVT-1162019-65', 3, 'default_img.jpg', 'TILOS', 'OSCAR', 'VILLARETE', '-', 0, 'Male', 'Single', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 4, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(54, '2SOQJG3P6F', 'P-CRS-312001-70', 3, 'default_img.jpg', 'SANTES', 'CINDY', 'REBOLDAL', '-', 0, 'Female', 'Widowed', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 4, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(57, 'LTQHD2BPYJ', 'P-RBG-912015-72', 3, 'default_img.jpg', 'GALON', 'RANDOLF', 'BLANCO', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 4, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(58, 'MF4R0LP0PT', 'P-RAIT-911996-73', 3, 'default_img.jpg', 'TOLEDO', 'RAYMUND ANTHONY', 'INOLINO', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 4, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(59, 'DO2X14YKJR', 'P-APV-1023-2000-74', 3, 'default_img.jpg', 'VILLARUBIA', 'ALTHEA', 'PIA', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 5, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(60, 'N3CFWPHFQX', 'P-CATS-912021-70', 3, 'default_img.jpg', 'SANTES', 'CAROL ANN', 'TILOS', '-', 0, 'Female', 'Single', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 5, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(61, '0RVK3VBCZ1', 'P-APM-812000-76', 3, 'default_img.jpg', 'MANOS', 'ANNABELLE', 'PERIGUA', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 5, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(62, 'APPZCXZCJS', 'P-JMM-711998-77', 3, 'default_img.jpg', 'MIRANDA', 'JAIME', 'MAULIT', '-', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 5, 0, 0, 0, 0, 0.00, 0, 0, '', '', '', NULL, 0, '', '', '', '', NULL),
(63, 'KZFCP2S0HT', 'P-JIG-611990-75', 3, 'default_img.jpg', 'GUINTOS', 'JOSEPHINE', 'INDINO', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 10, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(64, 'CEPHX3JOM0', 'P-IGT-1162019-77', 3, 'default_img.jpg', 'TIBAYDE', 'IRENE', 'GIGANAN', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 10, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(65, 'NZ0IKZI1IR', 'P-MMEM-811988-83', 3, 'default_img.jpg', 'MANGOGTONG', 'MA. MARILOU ', 'ESTRELLA', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 10, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(66, 'OSYDS2VNEW', 'P-BPA-431989-84', 3, 'default_img.jpg', 'ABALLE', 'BETHEL', 'PERFUMA', '-', 62, 'Female', 'Married', '09', '29', '1962', 'Hinoba-an Negros Occidental', 'Brgy. II Poblacion Hinoba-an Negros Occidental', '', '+639         ', '+639         ', '', '', '', '', 10, 0, 7, 6, 1, 823.09, 0, 0, 'Sub', 'MTO-92', '05/02/1988', NULL, 37, '131-335-966', '6209-290-301', '   -   -   -   ', '11-000032438-8', 0.000),
(67, '2KLYB0WHDY', 'P-CBG-982016-85', 3, 'default_img.jpg', 'GESTOSO', 'CARLITO', 'BAVIERA', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 10, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(68, 'ES1MY5CB5N', 'P-RAB-311995-88', 3, 'default_img.jpg', 'BUDACA', 'REVINIA', 'AMACIO', '-', 0, 'Female', 'Widowed', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 10, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(69, 'KVKMFQDZDV', 'P-LNG-1261998-89', 3, 'default_img.jpg', 'GA-AN', 'LENLY', 'NOSAL', '-', 0, 'Female', 'Widowed', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 10, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(70, 'HR1TD1EOO6', 'P-JTN-212021-85', 3, 'default_img.jpg', 'NATALIO', 'JOEFREY', 'TEMBREVILLA', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 9, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(71, 'N0PELGBXHV', 'P-LDG-10162020-86B', 3, 'default_img.jpg', 'GUSTILO', 'LEILANI', 'DECENA', '-', 0, 'Female', 'Single', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 9, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(72, '2XYVUZG44K', 'P-JTR-1012021-88', 3, 'default_img.jpg', 'RELIQUIAS', 'JOERIBEL', 'TORIANO', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 9, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(73, 'C4XW3NQ3CE', 'P-RCA-3162023-97', 3, 'default_img.jpg', 'ACADEMIA', 'RIZALYN', 'CAYANAN', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 9, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(74, 'KDV12JKLNR', 'P-VLT-2162012-96', 3, 'default_img.jpg', 'TRINIO-SANTES', 'VANESSA', 'LIRAZAN', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 3, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(75, 'VUM5AWYEUH', 'P-JTS-712012-11', 3, 'default_img.jpg', 'SUMUGAT', 'JENELYN', 'TELONIO', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 3, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(76, 'K0GBVLRIOM', 'P-MATJ-2162016-104', 3, 'default_img.jpg', 'JORDAN', 'MARK ANTHONY', 'TOMADO', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 3, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(77, 'V4PAJMJ5YO', 'P-OTT-5161993-112', 3, 'default_img.jpg', 'TUPAS', 'OFELIA', 'TEMBREVILLA', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 6, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(78, 'DURSCXCSIM', 'P-AEM-11162011-85', 3, 'default_img.jpg', 'MAYANDIA', 'ANALIE', 'ELARMO', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 6, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(79, 'EAUNV5WVM0', 'P-JPB-816-2016-115', 3, 'default_img.jpg', 'BARROCA', 'JORGIE', 'PERFUMA', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 6, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(80, 'UBFPHM020G', 'P-AVPB-10232000-116', 3, 'default_img.jpg', 'BONILLA', 'ANNI VER', 'PAMLIEGA', '-', 0, 'Female', 'Single', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 6, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(81, 'B42WBNMNSK', 'P-ACL-712012-117', 3, 'default_img.jpg', 'LIMSIACO', 'ARIANE', 'CONSTANTINO', '-', 0, 'Female', 'Single', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 6, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(82, 'EHEYDZ1UYN', 'P-GNA-10242022-116', 3, 'default_img.jpg', 'ALATON', 'GINA', 'NABOR', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 6, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', 0.000),
(83, 'DGPF5FDK53', 'P-JGA-61194-120', 3, 'default_img.jpg', 'ARANETA', 'JOSELITA', 'GENTELIZO', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 6, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', 0.000),
(84, 'N4DZR4BBY0', 'P-MFGR-6151994-121', 3, 'default_img.jpg', 'RELIQUIAS', 'MARIA FE', 'GUINTOS', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 8, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(85, 'CMDUXPZ44I', 'P-SCG-711992', 3, 'default_img.jpg', 'GONZAL', 'SANDRA', 'CASTILLO', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 8, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(86, '5BJ6IAN1FB', 'P-ESO-712012-105', 3, 'default_img.jpg', 'ORBIGOSO', 'ELIZABETH', 'SENIO', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 8, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(87, 'KP6G24TB40', 'P-MVR-10242022-123', 3, 'default_img.jpg', 'RELADO', 'MEDALIA', 'VALENZUELA', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 8, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(88, 'PWU0GP4SHN', 'P-AFS-732023-127', 3, 'default_img.jpg', 'SANTES', 'AZELA', 'FLORES', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 7, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(89, '2SJBOJTXPC', 'P-MEL-612018-119', 3, 'default_img.jpg', 'GELLECANAO', 'MURIELLE', 'LIRAZAN', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 7, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(90, 'Q0BSK1IGMH', 'P-PCB-4162018-118', 3, 'default_img.jpg', 'BARIQUIT', 'PRACEDES', 'CABONILAS', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 7, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(91, 'Q0QB3ZVYNK', 'P-ATV-712001-133', 3, 'default_img.jpg', 'VILLARETE', 'ANGELA', 'TELONIO', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 7, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(92, '1DSSFVBIFF', 'P-EEM-812017-127', 3, 'default_img.jpg', 'MAESTRECAMPO', 'EVELYN', 'ELARMO', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 7, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(93, 'FKX31XYW34', 'P-AOD-912021-130', 3, 'default_img.jpg', 'DELA FUENTE', 'ALVIN', 'ORMEO', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 11, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(94, 'ZJPTEJP5UQ', 'P-LFA-3162023-146', 3, 'default_img.jpg', 'AMANTE', 'LEONIZA', 'FLORES', '-', 0, 'Female', 'Widowed', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 11, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(95, '2M5F54VPSN', 'P-MAA-7222015-142Z', 3, 'default_img.jpg', 'AKOL', 'MARLOU', 'ARTICA', '-', 0, 'Female', '', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 14, 0, 1, 1, 2, 0.04, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', 0.000),
(96, 'FU6VB3OYK4', 'P-WEM-1032016-144', 3, 'default_img.jpg', 'MANGOGTONG', 'WILMAR', 'ESTRELLA', '-', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 14, 0, 0, 0, 0, 0.00, 0, 0, '', '', '', NULL, 0, '', '', '', '', NULL),
(97, 'M1Y5ETAYD6', 'P-LSVN-8162010-150', 3, 'default_img.jpg', 'NAVA', 'LUZ SALOME', 'VILLA', '-', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 14, 0, 0, 0, 0, 0.00, 0, 0, '', '', '', NULL, 0, '', '', '', '', NULL),
(98, 'O6JWSQ5BTR', 'P-IED-1222013-147', 3, 'default_img.jpg', 'DELA CONCEPCION', 'IMELDA', 'ESPENORIO', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 14, 0, 1, 19, 1, 0.02, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(99, 'GSQZ2OCVHQ', 'P-GTR-3162023-154', 3, 'default_img.jpg', 'RELIQUIAS', 'GERALD', 'TORIANO', '-', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 14, 0, 0, 0, 0, 0.00, 0, 0, '', '', '', NULL, 0, '', '', '', '', NULL),
(100, '4RE6HDPT3R', 'P-LTJ-812000-151', 3, 'default_img.jpg', 'JIMENEZ', 'LEO', 'TOMILBA', '-', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 14, 0, 0, 0, 0, 0.00, 0, 0, '', '', '', NULL, 0, '', '', '', '', NULL),
(101, 'EKSUEACJM5', 'P-CGPA-7162024-173', 3, 'default_img.jpg', 'ACIBIDO', 'CHE GENEROSO', 'PARO', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 14, 0, 1, 1, 1, 0.02, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', 0.000),
(102, 'W2DGEUTP62', 'P-MEDL-622003-154', 3, 'default_img.jpg', 'LOREDO', 'MARIA ELENA', 'DELLOSO', '-', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 14, 0, 0, 0, 0, 0.00, 0, 0, '', '', '', NULL, 0, '', '', '', '', NULL),
(103, 'PLCEEHAUGT', 'P-MAGM-812017-148', 3, 'default_img.jpg', 'MARQUEZ', 'MAE ANN', 'GIGANAN', '-', 0, 'Female', 'Single', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 15, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(104, 'J3UWH2Z32E', 'P-RPT-732023-165', 3, 'default_img.jpg', 'TRINIDAD', 'RODOLFO', 'PULGAN', 'JR.', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 15, 0, 0, 0, 0, 0.00, 0, 0, '', '', '', NULL, 0, '', '', '', '', NULL),
(105, 'GRN63LXTVC', 'P-ANG-912021-151', 3, 'default_img.jpg', 'GIGANAN', 'AGNES', 'NAVA', '-', 0, 'Female', 'Widowed', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 15, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(106, '6KR3ZM4BU3', 'P-JAGT-432023-168', 3, 'default_img.jpg', 'TELONIO', 'JAMES ANDREW', 'GUINTOS', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 15, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(107, 'GOYLQLNANM', 'P-DND-912015-169', 3, 'default_img.jpg', 'DELOTINA', 'DANILO', 'NAPIERE', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 17, 0, 1, 1, 2, 0.02, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(108, 'R52SQSFJB4', 'P-JBB-4162018-157', 3, 'default_img.jpg', 'BONILLA', 'JURY', 'BENLOT', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 17, 0, 1, 1, 2, 0.02, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', 0.000),
(109, 'T4BXLLYWTG', 'P-JADD-2162016-160', 3, 'default_img.jpg', 'DERIT', 'JOSE ALAN ', 'DURO', '-', 0, '', '', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 17, 0, 1, 1, 1, 0.02, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(110, 'KUNUEE1ZKH', 'P-JTY-1012000-162', 3, 'default_img.jpg', 'YUSAY', 'JOSE', 'TOGLE', 'III', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 16, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(111, 'POT5DLCLXZ', 'P-TMLG-432017-163', 3, 'default_img.jpg', 'GUINTOS', 'TINA MARIE', 'LUGA', '-', 0, 'Female', 'Single', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 16, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(112, 'F3DUWOJ4SZ', 'P-CETG-722018-164', 3, 'default_img.jpg', 'SIASON', 'CHEZAH ERL', 'GAUAL', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 16, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(113, 'REPYPK1N0K', 'P-EMA-1162019-165', 3, 'default_img.jpg', 'AMBAGAN', 'EDSEL', 'MILLENDEZ', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 16, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', 0.000),
(115, 'MMB6JKH1T3', 'P-JZC-6152022-174', 3, 'default_img.jpg', 'CAMBARIJAN', 'JIMMY', 'ZETA', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 25, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(116, '56EZ3T4YP2', 'P-AMC-6152022-175', 3, 'default_img.jpg', 'CALUMBA', 'ANALYN', 'MANILINGAN', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 25, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(117, '6D0YWDKLPP', 'P-RUA-1032016-143', 3, 'default_img.jpg', 'AGUHAYON', 'RICARDO', 'UBAMOS', '-', 0, '', '', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 14, 0, 1, 1, 1, 0.02, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', 0.000),
(118, 'UUR4C35PLC', 'P-JGA-6192023-134', 3, 'default_img.jpg', 'ALIMANE', 'JINKY', 'GALPO', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 7, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(119, 'EEG0NY2B5U', 'P-JVA-10162017-151', 3, 'default_img.jpg', 'ARBOIZ', 'JOHN', 'VILLARICO', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 15, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(120, 'R0ZJXTVRR4', 'CT-JAA-912023-13', 3, 'default_img.jpg', 'AVELINO', 'JULIEBERT', 'AZUCENA', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 2, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(121, 'BSLAWXCQ2N', 'P-JCB-10172011-103', 3, 'default_img.jpg', 'BAYDO', 'JULITO', 'CAYAS', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 3, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(122, 'Z2TDUSRUV6', 'P-HBB-312007-106', 3, 'default_img.jpg', 'BIACA', 'HERBERT', 'BORNALES', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 3, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', 0.000),
(123, 'OINBKY4BGA', 'P-JSD-9162002-99', 3, 'default_img.jpg', 'DECATORIA', 'JOEBERT', 'SARIL', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 3, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(124, 'YCSOHXN5TA', 'P-EPG-311994-60', 3, 'default_img.jpg', 'GALLARDA', 'ELNOR', 'PACLAONA', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(125, 'LS5IJYTERX', 'P-WNG-3162023-111', 3, 'default_img.jpg', 'GUINTOS', 'WINSTON', 'NAVA', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 3, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(126, 'JT6NRMM2MG', 'P-RTL-2122008-91', 3, 'default_img.jpg', 'LABRADOR', 'REY', 'TRIBUCIO', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 10, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(127, 'FOSBE6FBBP', 'P-FMM-312007-107', 3, 'default_img.jpg', 'MAHINAY', 'FRANCISCO', 'MANINANTAN', 'SR.', 58, 'Male', 'Married', '10', '04', '1966', 'BUUG, ZAMBOANGA SIBUGAY', 'Barangay Pook, Hinoba-an, Negros Occidental', '', '+639305427330', '+639305427330', 'MAHINAY', 'BRENDA', 'DELA PAZ', 'Spouse', 3, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(128, 'SP2WX6WZFC', 'P-VEAP-611991-45', 3, 'default_img.jpg', 'PADA', 'VERONICA EVELYN', 'ALBISO', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(129, '65VQESUT03', 'P-JITP-8162016-109', 3, 'default_img.jpg', 'PANGANTIHON', 'JOHN IRVING', 'TOLEDO', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 3, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(130, 'OIMV5KOLF2', 'P-GMTP-11162021-64', 3, 'default_img.jpg', 'PEROSIA', 'GLORY MAE', 'TUNDA-AN', '-', 0, 'Female', 'Widowed', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(131, 'PMOLWNYZZA', 'P-FDPS-382016-67', 3, 'default_img.jpg', 'SUSANA', 'FREDERICK DAVY', 'PINGCALE', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(132, 'P-ZGT-382010-110', 'P-ZGT-382010-110', 3, 'default_img.jpg', 'TEMBREVILLA', 'ZOSIMO', 'GAVILAGA', '-', 58, 'Male', 'Married', '04', '12', '1968', 'Hinoba-an Negros Occidental', '', '', '+639510897485', '+639510897485', 'TEMBREVILLA', 'LOREDA', 'TALANQUINES', 'Spouse', 3, 111, 0, 0, 0, 0.00, 0, 1, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(133, 'CCEWWUW3P2', 'P-TST-6162017-112', 3, 'default_img.jpg', 'TUBILLEJA', 'THEODORE', 'SIGUEZA', 'JR.', 0, 'Male', 'Single', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 6, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(134, 'E0VZSU0W6G', 'P-MLMA-10162024-22', 3, 'default_img.jpg', 'APLAON', 'MA. LEE', 'MAESTRECAMPO', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 23, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(135, 'PQMSBLBNJ6', 'CT-NVV-10162024-17', 3, 'default_img.jpg', 'VIDAURRAZAGA', 'NOAH', 'VASQUEZ', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 2, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(137, 'F255HSX3FM', 'P-EMG-10162024-162', 3, 'default_img.jpg', 'GUINTOS', 'ERICA', 'MANANGAN', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 14, 0, 1, 1, 1, 0.01, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(138, 'WDB3J415ZP', 'P-SMAA-10162024-96', 3, 'default_img.jpg', 'AKOL', 'SHARRA MAE', 'ARTICA', '-', 0, 'Female', 'Single', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 10, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(139, 'JETERWW23U', 'P-MFAB-3162023-125', 3, 'nrf5ihz6-mae-flor.jpg', 'BARRIOS', 'MAE FLOR', 'ALMAIZ', '-', 0, 'Female', 'Single', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 8, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(140, 'YFUCRYEDIV', 'P-JSB-10162024-165', 3, 'yfucryediv-jetro.png', 'BARROCA', 'JETTER', 'SENIO', '-', 40, 'Male', 'Married', '11', '14', '1984', 'Brgy. Pook Hinoba-an Negros Occidental', 'Manalimsim Brgy. Pook Hinoba-an Negros Occidental', 'jefroxejet38@gmail.com', '+639675203171', '+639         ', 'BARROCA', 'ALCHE', 'DEL ROSARIO', 'Spouse', 14, 160, 1, 1, 1, 0.01, 0, 1, 'none', 'MSS-161', '10/16/2024', NULL, 1, '777-255-218', '    -   -   ', '   -   -   -   ', '  -         - ', 14000.000),
(141, '111LXWXFLY', 'P-MRN-10162024-134', 3, 'default_img.jpg', 'NICOR', 'MICHAEL', 'RAMOS', '-', 0, 'Male', 'Single', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 7, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(142, 'SJLE6GGLD6', 'P-CLCM-4162018-15', 3, 'sjle6ggld6-che.jpg', 'JUAREZ', 'CHERRY LYNN', 'MONTAÑO', '-', 34, 'Female', 'Married', '02', '05', '1992', 'Hinoba-an Negros Occidental', 'Brgy. II Poblacion Hinoba-an Negros Occidental', 'chibichavez@gmail.com', '+6396607288  ', '+639         ', '', '', '', '', 2, 0, 10, 3, 2, 1014.14, 0, 0, 'Professional', 'MO-15', '  /  /    ', NULL, 0, '323-104-947', '2005-379-797', '121-148-974-219', '12-051435724-8', 0.000),
(144, 'GJCC6XY3BR', 'P-AMSA-531993-51', 3, 'default_img.jpg', 'ANTIQUEÑO', 'ANA MARIE', 'SANOY', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', 0.000),
(145, 'XD5JB3HCC0', 'P-LLM-712014-135', 3, 'default_img.jpg', 'MAHINAY', 'LIWAYA', 'LARENO', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 11, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(146, 'E6LFKFXAD0', 'P-AAN-6152022-77', 3, 'default_img.jpg', 'NUÑESCO', 'AIZA', 'ANDO', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 5, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', 0.000),
(147, 'WILZVWBBRI', 'P-GRO-6162017-55', 3, 'default_img.jpg', 'OCCEÑA', 'GEM', 'RELAMPAGOS', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 3, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(148, 'SARR3EM6ZV', 'P-JPP-982016-108', 3, 'default_img.jpg', 'PACURIB', 'JULITO', 'PLAÑA', 'JR.', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 3, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(149, 'G6A2OEKXUH', 'P-MHCM-6162005-46', 3, 'default_img.jpg', 'MANGILIMUTAN', 'MA. HEARTY', 'CAÑA', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 12, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(150, '0M1YGUSYJR', 'P-HMB-10162024-153', 3, 'default_img.jpg', 'BALOYO', 'HANSEL', 'M.', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 11, 0, 1, 1, 1, 0.01, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(151, 'ITZ6XK1TGX', 'NRFJQN0X', 3, 'default_img.jpg', 'LUCENARA', 'ROBIJID', 'Q', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 15, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(152, 'R0VM3TX265', 'NRF0STIA', 3, 'default_img.jpg', 'MONTANO', 'EVALYN', 'C.', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 24, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(153, 'WZZQ44GGKF', 'E-RAY-712022-22', 3, 'default_img.jpg', 'YUSAY', 'ROMEO', 'A.', 'JR.', 0, 'Male', 'Single', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 24, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(154, 'T0SO3GEQZX', 'P-ALT-3162023-147', 3, 'default_img.jpg', 'TUPAS', 'ANTHONY', 'L.', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 11, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(155, 'HOA5M4V2KU', 'P-TGT-7162024-102', 3, 'default_img.jpg', 'TEMBREVILLA', 'THOMY', 'G.', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 3, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(156, 'N0AXWJPKNQ', 'P-NAS-2162016-131', 3, 'default_img.jpg', 'SORONGON', 'NITA', 'A.', '-', 0, 'Female', 'Single', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 7, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL);
INSERT INTO `personnels` (`personnel_id`, `RFTag_id`, `personnel_id_code`, `shift_id`, `img`, `lname`, `fname`, `mname`, `suffix`, `age`, `sex`, `marital_status`, `bdMM`, `bdDD`, `bdYYYY`, `birth_place`, `address`, `email`, `personal_pnum`, `emergency_pnum`, `conPerson_lname`, `conPerson_fname`, `conPerson_mname`, `conPerson_relationship`, `do_id`, `des_id`, `sal_grade`, `sal_step`, `sal_level`, `rate_per_day`, `gass_id`, `empStat_id`, `eligibility`, `plantilla_num`, `appointment_date`, `separation_date`, `num_of_yrs`, `tin_num`, `gsis_num`, `pagibig_num`, `philHealth_num`, `monthly_salary`) VALUES
(157, 'DV6HSR4T31', 'P-AGS-1012015-157-Z', 3, 'default_img.jpg', 'SABOBO', 'ALFREDO', 'G.', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 15, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(158, 'L33XK2MQYL', 'CT-JRLR-4222024-19', 3, 'default_img.jpg', 'RELIQUIAS', 'JOHNNY RAY', 'L.', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 2, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(159, 'O2NINN551F', 'P-MTP-712014-136', 3, 'default_img.jpg', 'PARCON', 'MERCEDITA', 'T.', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 7, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(160, 'QQZIGTSWBI', 'P-ESN-612023-4', 3, 'default_img.jpg', 'NACION', 'ELBRED', 'S.', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 17, 0, 1, 1, 2, 0.02, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(161, 'BLJQQ3XW3K', 'P-NAJ-11162022-141', 3, 'default_img.jpg', 'JOSECO', 'NORVIE', 'A.', '-', 0, 'Male', 'Single', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 11, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(162, 'M1RF4GW0TT', 'P-MENG-712012-149', 3, 'default_img.jpg', 'GUINTOS', 'MA. ELENA', 'N.', '-', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 14, 0, 0, 0, 0, 0.00, 0, 0, '', '', '', NULL, 0, '', '', '', '', NULL),
(163, 'GP4Q0NOWGA', 'P-CMG-1012011-167', 3, 'default_img.jpg', 'GERMINAL', 'CRISTUTO', 'M.', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 17, 0, 1, 1, 1, 0.02, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(164, '36EOSJANR6', 'CT-CJTE732023-33', 3, 'default_img.jpg', 'ENGCOY', 'CANTERLYN JOY', 'T.', '-', 0, 'Female', 'Single', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 24, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(165, '1YJTKOZ61A', 'P-MMD-432023-141', 3, 'default_img.jpg', 'DURAN', 'MICHELLE', 'M.', '-', 0, 'Female', 'Single', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 11, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(166, '5Z5WKCHQGF', 'P-RDD-6162006-141', 3, 'default_img.jpg', 'DOLOR', 'ROLY', 'D.', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 11, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(167, 'VWUQQEPZ5P', 'CT-RSD-522024-20', 3, 'default_img.jpg', 'DIONALDO', 'ROEM', 'S.', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 2, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(168, 'H05GELOMZN', 'P-RBD-212013-164', 3, 'default_img.jpg', 'DEQUINA', 'REYNOLD', 'B.', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 17, 0, 2, 2, 2, 0.02, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(169, '2CKQGJE4K6', 'P-CMD-812023-158', 3, 'default_img.jpg', 'DELLOSO', 'CHRISTOPHER', 'M.', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 14, 0, 1, 1, 1, 0.01, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(170, 'JGSKGE5D2W', 'P-IED-12222013-147', 3, 'default_img.jpg', 'DELA CONCEPCION', 'IMELDA', 'E.', '-', 0, 'Female', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 10, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(171, 'ERKTAZMQZA', 'E-RAC-712022-28', 3, 'default_img.jpg', 'CARDINAL', 'RYAN', 'A.', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 24, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(172, 'RVR5BBU5ZV', 'P-EMGC-11162022-137', 3, 'default_img.jpg', 'CANA', 'EVA MAE', 'G.', '-', 0, 'Female', 'Single', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 11, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(173, 'KHA2LUXNL1', 'P-VMMB-412024-10', 3, 'default_img.jpg', 'BA-AL', 'VON MARVIN', 'M.', '-', 0, 'Male', 'Single', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 2, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(174, 'XFSKZYRVSI', 'P-GPL-7172017-117', 3, 'default_img.jpg', 'LASTRILLA', 'GUIDRALYN', 'P.', '-', 0, 'Female', 'Single', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 7, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(176, 'YBD20JEALC', 'NRF0AZLA', 3, 'default_img.jpg', 'ABRIGANA', 'PAMELA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(177, '03THKL40WG', 'NRFYPH5W', 3, 'default_img.jpg', 'ABUGAN', 'ARMELITO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(178, 'CY3EN2VVKO', 'NRFDYG6K', 3, 'default_img.jpg', 'ACADEMIA', 'MELINDA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(179, 'TXNUYSMIPH', 'NRF1NUT3', 3, 'default_img.jpg', 'ACHA', 'ANGELA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(180, 'PYYVDHBX2B', 'NRFFZ5CB', 3, 'default_img.jpg', 'ACHINOVA', 'RANTE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(181, 'Q1DKJASL5Q', 'NRFHGBYB', 3, 'default_img.jpg', 'ADOLFO', 'JOEY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(182, 'N3UASPXZ1I', 'NRFUOVL4', 3, 'default_img.jpg', 'AGAN', 'MA.THARA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(183, 'YL0IE3CYT5', 'NRF6ZK4W', 3, 'default_img.jpg', 'ALACIO', 'DENNIS', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(184, 'SKFQQYSRUH', 'NRFD6Z4X', 3, 'default_img.jpg', 'ALACIO', 'JOIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(185, '4QWVB2OCKJ', 'NRFK2RP2', 3, 'default_img.jpg', 'ALACIO', 'RANEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(186, '4YOQ2PV6LX', 'NRFADGNF', 3, 'default_img.jpg', 'ALBACITE', 'DIOVEN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(187, '2X2IGRWQ3F', 'NRF1PNNQ', 3, 'default_img.jpg', 'ALBERASTINE', 'HAZEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(188, 'FTBBVTMHBJ', 'NRF454MQ', 3, 'default_img.jpg', 'ALBIO', 'JESUSITO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(189, 'QWV6R0YTL4', 'NRFNAGOD', 3, 'default_img.jpg', 'ALCON', 'MARYLADGIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(190, 'NHTCRGPUNL', 'NRFSDE5E', 3, 'default_img.jpg', 'ALCUBILLA', 'JOEVITH', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(191, 'NMBRF45UGT', 'NRFS1C1L', 3, 'default_img.jpg', 'ALEGADA', 'ROLDAN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(192, 'J2132BQH1R', 'NRFFM2AC', 3, 'default_img.jpg', 'ALEGRE', 'JOHNNY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(193, 'VXUKYT1ZYE', 'NRFJOO3N', 3, 'default_img.jpg', 'ALEGRE', 'LEANAGEENA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(194, 'H50KOG0EZL', 'NRF3G51M', 3, 'default_img.jpg', 'ALGOY', 'RICKY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(195, '6W4E1H3MOJ', 'NRFSSXIT', 3, 'default_img.jpg', 'ALPITCHE', 'JOEMAR', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(196, '122MRHOPFK', 'NRFE4M4W', 3, 'default_img.jpg', 'ALPUERTO', 'JANETH', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(197, 'QF4XRUZUKN', 'NRFQM0VE', 3, 'default_img.jpg', 'ALPUERTO', 'JUNRY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(198, 'DYB5ZASIQX', 'NRFLNYYD', 3, 'default_img.jpg', 'ALVARAN', 'HERBY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(199, 'ALMCWISTDO', 'NRFOZCDS', 3, 'default_img.jpg', 'ALVARAN', 'MA.LYN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(200, 'L6DHHRCG0B', 'NRF3BDUO', 3, 'default_img.jpg', 'ALVAREZ', 'HOPE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(201, 'JSBW36AIXG', 'NRFABKTD', 3, 'default_img.jpg', 'AMANTE', 'WILFREDO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(202, '40UXXAGTXE', 'NRFWWKPI', 3, 'default_img.jpg', 'AMOR', 'JOEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(203, 'J0G0AF1V6G', 'NRF3GYHM', 3, 'default_img.jpg', 'ANDRADE', 'LORIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(204, '3G2DM5IBXZ', 'NRFXU2I3', 3, 'default_img.jpg', 'ANQUILLANO', 'MARYNOVIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(205, 'MNIFJOLRKK', 'NRFYETFV', 3, 'default_img.jpg', 'APAWAN', 'DANTE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(206, 'BEKDMLG3YK', 'NRFQWVIS', 3, 'default_img.jpg', 'APLAON', 'MICHEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(207, '154I61X4QO', 'NRFZAWO4', 3, 'default_img.jpg', 'APOLINARIO', 'JONIVER', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(208, 'ROLHFKBCX0', 'NRF5EWJN', 3, 'default_img.jpg', 'APORTO', 'PEDRITO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(209, '5SRBRLWX50', 'NRFTURIC', 3, 'default_img.jpg', 'ARBOIS', 'JONEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(210, '1SD2XNAJWY', 'NRFHAKWQ', 3, 'default_img.jpg', 'ARQUIZA', 'ROSEMARIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(211, 'YSQGUTBTOK', 'NRFEBQMA', 3, 'default_img.jpg', 'ARROYO', 'CONRADO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(212, '3DNBXO5RHQ', 'NRFXLMN4', 3, 'default_img.jpg', 'ARTICA', 'RIAANN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(213, 'H24D3HJTRK', 'NRFP0O2M', 3, 'default_img.jpg', 'ASTROLOGO', 'LOLITA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(214, 'IYIN3T61FW', 'NRFGFP4C', 3, 'default_img.jpg', 'AWIT', 'ARIEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(215, 'YIVM1PL2SC', 'NRFKM14Y', 3, 'default_img.jpg', 'BA-AL', 'JOHNKENEER', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(216, 'YGTSQYGDGJ', 'NRFAUS52', 3, 'default_img.jpg', 'BABOR', 'MARYGRACE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(217, 'QCAN0BCF1D', 'NRFKLY0F', 3, 'default_img.jpg', 'BACONGALLO', 'VILMA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(218, 'ZUCOSLOM24', 'NRF66DJR', 3, 'default_img.jpg', 'BADAYOS', 'CHRISTINA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(219, 'DUTMC1WL3N', 'NRFEAIUP', 3, 'default_img.jpg', 'BAGUIO', 'VICTOR', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(220, 'ZF23WTX6TQ', 'NRF1210J', 3, 'default_img.jpg', 'BALBUENA', 'JOEMAR', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(221, 'QVHV63NF4F', 'NRFUEXQZ', 3, 'default_img.jpg', 'BALOHABO', 'MAMERTO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(222, 'P4I1OG4OFR', 'NRFIMCW1', 3, 'default_img.jpg', 'BALSADO', 'LEAHMAE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(223, 'FAAK4C4FKN', 'NRFUXXJE', 3, 'default_img.jpg', 'BANTAYAO', 'JIMSON', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(224, 'DF2WD3LS1W', 'NRF0UTXL', 3, 'default_img.jpg', 'BANTILAN', 'AMOROSA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(225, 'OZJBCRYJ15', 'NRF4ZUTV', 3, 'default_img.jpg', 'BARA?AO', 'MARVIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(226, 'RJRBLRFHMA', 'NRFP0GGF', 3, 'default_img.jpg', 'BARGASO', 'ESENCIA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(227, '2TIX145A0G', 'NRFEFSZP', 3, 'default_img.jpg', 'BARING', 'REYNALDO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(228, 'JBMKBEHAC3', 'NRFZJITZ', 3, 'default_img.jpg', 'BAROMIDA', 'BENJIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(229, 'WQSW4JLJPC', 'NRFQFYCS', 3, 'default_img.jpg', 'BARO?A', 'JOHNDIRK', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(230, 'AY60OZPAIX', 'NRFBDYAI', 3, 'default_img.jpg', 'BARROCA', 'BARRY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(231, 'WJRGFLQXDO', 'NRFBQZNT', 3, 'default_img.jpg', 'BARROCA', 'JESBERT', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(232, 'M62NCZZBPX', 'NRFJERTZ', 3, 'default_img.jpg', 'BARTE', 'ALLAN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(233, 'XKVBMG5SN3', 'NRFYUSTQ', 3, 'default_img.jpg', 'BARTOLOME', 'FEBE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(234, '32HKMYC4US', 'NRFZIF1D', 3, 'default_img.jpg', 'BATALLONES', 'BENITO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(235, 'FEZ0UHPVS1', 'NRFUUWDY', 3, 'default_img.jpg', 'BATALLONES', 'JOHNFIL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(236, '6BMGSYG2PF', 'NRFRRIFT', 3, 'default_img.jpg', 'BAUTISTA', 'MAIDA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(237, 'TBHDVDCBDV', 'NRFT23TV', 3, 'default_img.jpg', 'BELLISTA', 'EBI', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(238, 'KO4IBV5IJ1', 'NRF3KBUG', 3, 'default_img.jpg', 'BENDIJO', 'LADISLAO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(239, 'FTR5CJERKV', 'NRFIN4C0', 3, 'default_img.jpg', 'BENGEL', 'HERBERT', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(240, 'ZP56W2EEAM', 'NRFYSFHJ', 3, 'default_img.jpg', 'BERIDO', 'JENNEFER', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(241, 'TRWLVL4NIQ', 'NRFEU41I', 3, 'default_img.jpg', 'BERMUDEZ', 'LOVELL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(242, 'VLR6CMJPY3', 'NRFJK6I5', 3, 'default_img.jpg', 'BERTUCIO', 'RECHEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(243, '5JJKVX2TK4', 'NRFTY655', 3, 'default_img.jpg', 'BESIATA', 'AQUILINORICOIV', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(244, 'B2NVZJ3NLC', 'NRFN3E5W', 3, 'default_img.jpg', 'BIENVENIDO', 'GLOVEN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(245, 'VT26OSYVAE', 'NRF3XSX2', 3, 'default_img.jpg', 'BLANCA', 'JOENALDO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(246, 'DOUHVSLDVU', 'NRFZFY5E', 3, 'default_img.jpg', 'BLANCA', 'REY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(247, 'R05OSG0BS6', 'NRF4KWZ4', 3, 'default_img.jpg', 'BLANCO', 'LOUIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(248, 'KCJXEV30QX', 'NRFODDWL', 3, 'default_img.jpg', 'BLORON', 'GLORYANN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(249, 'SI2NOCQYBQ', 'NRFEGO5P', 3, 'default_img.jpg', 'BLORON', 'JOEMARIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(250, 'JHFFMGRPNW', 'NRFTJGVW', 3, 'default_img.jpg', 'BOLIMA', 'MARLYN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(251, 'XQQ6PNOCPR', 'NRFZR2R6', 3, 'default_img.jpg', 'BONAFE', 'GINA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(252, 'TSQJ5RRJJL', 'NRFDMQHE', 3, 'default_img.jpg', 'BONGCAWEL', 'JURINDO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(253, 'AP02NANENB', 'NRFIZPTH', 3, 'default_img.jpg', 'BONILLA', 'JOSEANTONIO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(254, '0CR3P0OPMT', 'NRFPWCER', 3, 'default_img.jpg', 'BONILLA', 'JULIUS', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(255, '1MYYSBR6Y0', 'NRF3N242', 3, 'default_img.jpg', 'BONILLA', 'ORLANDO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(256, '1153XCBYUX', 'NRFQU3BM', 3, 'default_img.jpg', 'BORNALES', 'RODOLFO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(257, '0XAJ3OI22V', 'NRF3XSPZ', 3, 'default_img.jpg', 'BUENAFLOR', 'LALYNN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(258, 'QSKCYUGWV2', 'NRFLLYI4', 3, 'default_img.jpg', 'BUENAVISTA', 'JESUSA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(259, 'SP14QWVPFL', 'NRFBK2UD', 3, 'default_img.jpg', 'BULFA', 'BOBBY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(260, 'ZTQ00LEDT6', 'NRFE6U1S', 3, 'default_img.jpg', 'CABALDE', 'IDA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(261, '4RZZBME5I4', 'NRFFS6VQ', 3, 'default_img.jpg', 'CABALDE', 'KARTHIECORAZON', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(262, 'ZXCF2BSUVG', 'NRFJTVSJ', 3, 'default_img.jpg', 'CABALEDA', 'CIRILO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(263, 'QPUKF5K0R6', 'NRFP2MRQ', 3, 'default_img.jpg', 'CABALLERO', 'RODEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(264, 'Z6ZZ2VOTSX', 'NRF5KTVE', 3, 'default_img.jpg', 'CABALUNA', 'GERLYN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(265, 'EIJ6CS50NE', 'NRF0JQYD', 3, 'default_img.jpg', 'CABANSAG', 'SAMANTHA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(266, 'C436JLSJ3I', 'NRFMJO1P', 3, 'default_img.jpg', 'CABARLES', 'JEROME', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(267, 'CDUCMWWCKP', 'NRFYUS6Z', 3, 'default_img.jpg', 'CABASE', 'RENE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(268, '6TPCBSBKRI', 'NRFLM0TF', 3, 'default_img.jpg', 'CABRAS', 'RUBEN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(269, 'IQW6GGLNCD', 'NRF1FYQH', 3, 'default_img.jpg', 'CAHAPAY', 'JENELYN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(270, 'I26UHLIXRR', 'NRF3CTE6', 3, 'default_img.jpg', 'CAINAP', 'ARNALDOJR.', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(271, 'DYOJLMZ435', 'NRFF2O4K', 3, 'default_img.jpg', 'CAINAP', 'RENANTE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(272, '42CR4CIUZW', 'NRFK1MOJ', 3, 'default_img.jpg', 'CALAMBA', 'APOLINARIOJR.', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(273, 'P4TDTNYCPC', 'NRFMPFYP', 3, 'default_img.jpg', 'CALAMBA', 'SHIELA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(274, 'GTC5FIJKMQ', 'NRFYCTJL', 3, 'default_img.jpg', 'CALUMBA', 'RICKY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(275, '1CKVTRCM5H', 'NRFRE4CK', 3, 'default_img.jpg', 'CAMORO', 'JOLITO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(276, 'S1WHQNBX6O', 'NRFIRVSB', 3, 'default_img.jpg', 'CA?A', 'CHARLENDAWN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(277, 'XKWUR4NTUR', 'NRFM1Z1O', 3, 'default_img.jpg', 'CA?A', 'JUNRAY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(278, '35QOJQGVQP', 'NRFRZY1D', 3, 'default_img.jpg', 'CANDULIZAS', 'MICHELLEGRACE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(279, 'O5QQEVJQCJ', 'NRFYN2LI', 3, 'default_img.jpg', 'CA?O', 'CHRISTIAN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(280, 'Q3STUYPO2A', 'NRFSZGEM', 3, 'default_img.jpg', 'CANOY', 'RODRIGO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(281, 'PUGKVIVPAK', 'NRF2WLVN', 3, 'default_img.jpg', 'CANUMAY', 'JERSON', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(282, 'I2S30QETFS', 'NRFWRDCO', 3, 'default_img.jpg', 'CARA-AT', 'LORNA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(283, 'XC2PB2NIWF', 'NRF6WAT3', 3, 'default_img.jpg', 'CARDINAL', 'ARAMAE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(284, 'IV2BKBUGXO', 'NRFG5IIZ', 3, 'default_img.jpg', 'CARIAS', 'EVAMAE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(285, 'DEUONKC1OI', 'NRFMLTMH', 3, 'default_img.jpg', 'CAUYAN', 'JOHNREY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(286, 'NK36NYLJZA', 'NRF4G5PK', 3, 'default_img.jpg', 'CAUYAN', 'LOUVEANN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(287, 'TPHMMWIDVU', 'NRFEY1AO', 3, 'default_img.jpg', 'CAYANG', 'CYRIL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(288, 'L3H5AYT2VE', 'NRF3Y3YW', 3, 'default_img.jpg', 'CAYAO', 'HELEN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(289, 'BOJNRQQKU0', 'NRFR5GVR', 3, 'default_img.jpg', 'CELESTIAL', 'JESSAJANE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(290, 'ASKWS2CQDE', 'NRF1O1QH', 3, 'default_img.jpg', 'CELIS', 'ROBERTO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(291, 'VRPX4OSPXV', 'NRF2OSHK', 3, 'default_img.jpg', 'CELIZ', 'LUCIANO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(292, '0OMTNLDEOC', 'NRF42HBZ', 3, 'default_img.jpg', 'CELIZ', 'ROSEPHINE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(293, 'UMFHN24P2I', 'NRFDAF0D', 3, 'default_img.jpg', 'CHAVES', 'RANDY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(294, 'ZHFVBJNGUZ', 'NRF6WOJI', 3, 'default_img.jpg', 'CHAVEZ', 'WENDYJEAN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(295, '6GI1TQDJCB', 'NRFW0ANX', 3, 'default_img.jpg', 'COLANTRO', 'WINNIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(296, 'LK6ZOEV6SU', 'NRFLDQR2', 3, 'default_img.jpg', 'COMBATE', 'CHARRY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(297, 'P0MIEKM6KD', 'NRFVDJ6X', 3, 'default_img.jpg', 'COMPACION', 'REGIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(298, 'MH3YOSA1VF', 'NRFL6AYO', 3, 'default_img.jpg', 'CONSERMAN', 'JESTONI', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(299, '06Y5HIL0OR', 'NRFA6YUR', 3, 'default_img.jpg', 'CORDERO', 'JENNIFER', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(300, '4WUGADU0MF', 'NRFZAACM', 3, 'default_img.jpg', 'CORDOVA', 'GLYZEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(301, 'REFJCQEZ6A', 'NRF5XASL', 3, 'default_img.jpg', 'CORNEL', 'CARMELEE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(302, 'GWNM0JFCVK', 'NRFDJ5GO', 3, 'default_img.jpg', 'CORONEL', 'JOSEPHSMITH', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(303, '62FNTGNOQA', 'NRFI1REQ', 3, 'default_img.jpg', 'CUBID', 'JANEDRIC', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(304, 'HETBCXAPLS', 'NRFLASI2', 3, 'default_img.jpg', 'DA-AN', 'LOIDA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(305, 'VYVHSSW4GC', 'NRFJUF2O', 3, 'default_img.jpg', 'DACALDACAL', 'BEVERLY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(306, 'W0F0AF2RDL', 'NRFFDP5Z', 3, 'default_img.jpg', 'DAULONG', 'CHERRYANN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(307, '2OELZFYOUV', 'NRFIPM0G', 3, 'default_img.jpg', 'DAVID', 'RICHARD', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(308, 'IMDO262TI0', 'NRFYIZDB', 3, 'default_img.jpg', 'DECENA', 'ARMANDO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(309, 'Z1FWHEHVNX', 'NRF0OBEW', 3, 'default_img.jpg', 'DECENA', 'NORA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(310, 'MTGNRJQDY6', 'NRFCST4M', 3, 'default_img.jpg', 'DELA CRUZ', 'MARYCHRISTINE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(311, '0FGPL0RGLF', 'NRFTJO2T', 3, 'default_img.jpg', 'DELA CRUZ', 'SHANNYROSE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(312, 'R6UU1UCFCJ', 'NRFB3AO2', 3, 'default_img.jpg', 'DELA PE?A', 'JHUMAR', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(313, 'T4QWDVP0CC', 'NRFIZP4V', 3, 'default_img.jpg', 'DELA PE?A', 'MAFIRUVIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(314, 'ZJVC5O6H56', 'NRFRK04O', 3, 'default_img.jpg', 'DELGADO', 'JOJO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(315, 'FP4IUIRUFP', 'NRF5VYSN', 3, 'default_img.jpg', 'DEMAFELES', 'JULIANA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(316, 'L26SCKAZDY', 'NRF452AJ', 3, 'default_img.jpg', 'DEMERIN', 'ROWENA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(317, 'K1N51JG1RT', 'NRFGX5T6', 3, 'default_img.jpg', 'DEPOSITARIO', 'RONALD', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(318, 'VA5C36IH10', 'NRFXJIRD', 3, 'default_img.jpg', 'DEQUI?A', 'JERRY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(319, 'VYKAFASKXU', 'NRFPOCEB', 3, 'default_img.jpg', 'DIANOY', 'EMILY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(320, 'W6I6GY0NVK', 'NRFX4JYO', 3, 'default_img.jpg', 'DIAZ', 'JENNERIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(321, 'XSRLIIWWHA', 'NRFJM4IU', 3, 'default_img.jpg', 'DIAZ', 'JOHNRICH', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(322, 'OZCFW0K3F6', 'NRFTKHTJ', 3, 'default_img.jpg', 'DUE?AS', 'JEROLD', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(323, 'LL0BGBJ414', 'NRFCGDMY', 3, 'default_img.jpg', 'ELACO', 'HERNANDO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(324, 'TMNYI5CA1B', 'NRFLEHIX', 3, 'default_img.jpg', 'ELACO', 'MAE-ANN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(325, 'J61K4OUMJK', 'NRF3TUJX', 3, 'default_img.jpg', 'ELACO', 'RONEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(326, 'YF2NUKLHK0', 'NRFXXKIQ', 3, 'default_img.jpg', 'EMIT', 'HAZEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(327, 'ZQD1JL15XF', 'NRFX3JKU', 3, 'default_img.jpg', 'EMIT', 'JOEMAR', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(328, 'M5LMV06J5V', 'NRFN4O60', 3, 'default_img.jpg', 'ENCOY', 'RACKLY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(329, 'KFWYUE2UFJ', 'NRFSSALK', 3, 'default_img.jpg', 'ENGCOY', 'JOHNNOEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(330, 'ZUF1IAVGJ0', 'NRFUUELF', 3, 'default_img.jpg', 'ENGCOY', 'JOHNOLIVER', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(331, '2PSBLQCCJB', 'NRFLSPWU', 3, 'default_img.jpg', 'ENGCOY', 'NI?O', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(332, 'JWZBR1S633', 'NRFWL3YK', 3, 'default_img.jpg', 'ENGCOY', 'ROMEOJR.', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(333, 'SB1B3S4GP1', 'NRFTLTBR', 3, 'default_img.jpg', 'ENGCOY', 'SAMSON', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(334, 'SO1O5BKKJF', 'NRFZKUM5', 3, 'default_img.jpg', 'ERAN', 'HELEN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(335, 'TZ2AKXFPVV', 'NRF1QOXC', 3, 'default_img.jpg', 'EROY', 'AILENE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(336, 'MB4SXKZBX5', 'NRFULBJA', 3, 'default_img.jpg', 'ESPINOSA', 'DOMINADOR', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(337, '1RDGQEY4LJ', 'NRF23M6D', 3, 'default_img.jpg', 'ESPINOSA', 'RAMON', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(338, 'FRK04Q163W', 'NRF5INQV', 3, 'default_img.jpg', 'ESPINOSA', 'VICTORIANOSR.', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(339, 'TN5VF6BK40', 'NRF6FMIR', 3, 'default_img.jpg', 'EVANGELISTA', 'CHYRLL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(340, 'KODANRDVGP', 'NRFXCCC2', 3, 'default_img.jpg', 'EVANGELISTA', 'ERWINJOHANES', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(341, '1HHQNNC6AR', 'NRF2U3XO', 3, 'default_img.jpg', 'FAJARDO', 'MALOU', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(342, 'BPMQPVHYAY', 'NRF6TT40', 3, 'default_img.jpg', 'FAUSTINO', 'JOCELYN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(343, 'ZYODO6M1Y2', 'NRFH2QP4', 3, 'default_img.jpg', 'FERMENDOZA', 'NIDA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(344, 'MMKRCKZ6U6', 'NRFXI5EU', 3, 'default_img.jpg', 'FERNANDEZ', 'EDISON', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(345, 'TALNOHM04G', 'NRFMQBKP', 3, 'default_img.jpg', 'FERNANDEZ', 'MERLINDA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(346, 'V3CH1EWETD', 'NRFP446A', 3, 'default_img.jpg', 'FERNANDEZ', 'SUSAN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(347, '0I220JOLUN', 'NRFQ65OR', 3, 'default_img.jpg', 'FIO-AG', 'PAULAJOY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(348, 'BJGLU2G2AY', 'NRFCJYUN', 3, 'default_img.jpg', 'FLORES', 'JEAN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(349, 'PVTNMPM4QZ', 'NRFVKXOO', 3, 'default_img.jpg', 'FLORES', 'JOSEPHALEXANDERDAVE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(350, 'BOSY3AAFEO', 'NRFQRKOX', 3, 'default_img.jpg', 'FLORES', 'JOSIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(351, 'TI2FMW24LN', 'NRFFPPCU', 3, 'default_img.jpg', 'FLORES', 'JUDY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(352, 'RQ3JGBTFZS', 'NRFHV0RO', 3, 'default_img.jpg', 'FLORES', 'MICHAELJOSHUA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(353, 'NE0MRSBM1M', 'NRFJFLEP', 3, 'default_img.jpg', 'FRANCISCO', 'CHRISTELROSE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(354, 'PWPME2D4O1', 'NRF0CN0L', 3, 'default_img.jpg', 'FUENTEBELLA', 'IRIS', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(355, 'GKWBRSSN2Y', 'NRFLTO6L', 3, 'default_img.jpg', 'FUENTENEGRA', 'PETER', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(356, 'JUPF36WBSZ', 'NRFW6JDM', 3, 'default_img.jpg', 'GADIANA', 'PATRICIOIII', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(357, '6TVMF65PWV', 'NRFPN0K4', 3, 'default_img.jpg', 'GAJARDO', 'GEMMA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(358, 'S5D05LMQNK', 'NRFKVCBN', 3, 'default_img.jpg', 'GALANZA', 'CLAIRE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(359, 'JUHLLW1CRG', 'NRFLQOVH', 3, 'default_img.jpg', 'GALES', 'MAYBELLE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(360, 'YZGTMS1ITX', 'NRF51XRB', 3, 'default_img.jpg', 'GALLARDA', 'ARNOLD', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(361, 'ZTVYWDKDV3', 'NRFGW01L', 3, 'default_img.jpg', 'GALLEGO', 'RAFFY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(362, 'FXGECJ4J3C', 'NRFC3FMG', 3, 'default_img.jpg', 'GALLO', 'MARYANN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(363, 'L6AV6KOH01', 'NRFFZNJJ', 3, 'default_img.jpg', 'GALON', 'ALEX', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(364, 'PRNMVNTW35', 'NRFARSJW', 3, 'default_img.jpg', 'GANANCIAL', 'BALTAZAR', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(365, 'LNAK2EE1LQ', 'NRFREI40', 3, 'default_img.jpg', 'GANANCIAL', 'HAZEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(366, 'LNYNED3QNF', 'NRFG0AF5', 3, 'default_img.jpg', 'GANANCIAL', 'JULEEN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(367, '3DHZXYTMTU', 'NRFT032R', 3, 'default_img.jpg', 'GAPOL', 'ROSELYN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(368, 'JIVLGLWIH4', 'NRFTXU61', 3, 'default_img.jpg', 'GARAYGAY', 'HENRY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(369, 'EN2DBTWSUJ', 'NRFCA4G0', 3, 'default_img.jpg', 'GARCIA', 'JODANE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(370, 'Z5ZLSMM3XI', 'NRFNMHP4', 3, 'default_img.jpg', 'GARLITOS', 'CASILDA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(371, 'SSTNMOAYNQ', 'NRFRDHKY', 3, 'default_img.jpg', 'GARSULA', 'VICKIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(372, 'KFLOQB0RIU', 'NRFCCPZX', 3, 'default_img.jpg', 'GAUAL', 'ROBERTKEITH', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(373, 'G41OM5FPJD', 'NRFUQM2G', 3, 'default_img.jpg', 'GAUAL', 'ROLINJR.', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(374, '0BA6PEVOCK', 'NRFKJXDT', 3, 'default_img.jpg', 'GAUDAN', 'NESTORJR.', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(375, '3I4Q564O4D', 'NRFSFYKA', 3, 'default_img.jpg', 'GAVILAGA', 'DIANAROSE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(376, 'PWZC2W2EOX', 'NRF4LP1C', 3, 'default_img.jpg', 'GAVILAGA', 'VALERIANO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(377, 'KUAYDOJU5T', 'NRFTGIJP', 3, 'default_img.jpg', 'GAYOMALE', 'MA.TERESA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(378, 'QYSNHQJ2EH', 'NRF55WWC', 3, 'default_img.jpg', 'GEDALANGA', 'REGIELOUIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(379, 'GNQGIVI30P', 'NRFBMJJI', 3, 'default_img.jpg', 'GEDRAMA', 'EVAROSE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL);
INSERT INTO `personnels` (`personnel_id`, `RFTag_id`, `personnel_id_code`, `shift_id`, `img`, `lname`, `fname`, `mname`, `suffix`, `age`, `sex`, `marital_status`, `bdMM`, `bdDD`, `bdYYYY`, `birth_place`, `address`, `email`, `personal_pnum`, `emergency_pnum`, `conPerson_lname`, `conPerson_fname`, `conPerson_mname`, `conPerson_relationship`, `do_id`, `des_id`, `sal_grade`, `sal_step`, `sal_level`, `rate_per_day`, `gass_id`, `empStat_id`, `eligibility`, `plantilla_num`, `appointment_date`, `separation_date`, `num_of_yrs`, `tin_num`, `gsis_num`, `pagibig_num`, `philHealth_num`, `monthly_salary`) VALUES
(380, 'HYOABE5X6H', 'NRFOT2ZH', 3, 'default_img.jpg', 'GELLUAGAN', 'DIANA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(381, 'AUW3UYBGYU', 'NRFTQ4P0', 3, 'default_img.jpg', 'GEMONG', 'JIEMARCH', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(382, '66TB6ZHCL2', 'NRF4YJVK', 3, 'default_img.jpg', 'GERMINAL', 'CHRISTINE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(383, 'YIL1C33CBV', 'NRFZHWUA', 3, 'default_img.jpg', 'GERMINAL', 'SONNYJR.', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(384, 'GBK2E3KU2K', 'NRFV6ERU', 3, 'default_img.jpg', 'GIGANAN', 'DARLEEN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(385, 'PG2ACIX2PA', 'NRFAE5DG', 3, 'default_img.jpg', 'GIGANAN', 'VINCENTROME', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(386, 'V2IR21FDNV', 'NRFPFMFV', 3, 'default_img.jpg', 'GONZALES', 'EVELYN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(387, '2G5AHYL4A4', 'NRF0NDFA', 3, 'default_img.jpg', 'GRANDE', 'ROLLY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(388, 'WEG131MYY3', 'NRF6ZWZ1', 3, 'default_img.jpg', 'GUANZON', 'PRECIL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(389, '1R5MBNKXEG', 'NRFRI2J5', 3, 'default_img.jpg', 'GUARDIANO', 'EVELYN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(390, 'I6F0R6VSYT', 'NRFENKO2', 3, 'default_img.jpg', 'GUIMBAL', 'RHONAJOY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(391, 'BJ0G00UVNY', 'NRFGKEDN', 3, 'default_img.jpg', 'GUINTOS', 'DEVEY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(392, 'WIIRQIHPHL', 'NRFCDXMP', 3, 'default_img.jpg', 'GUIWAN', 'RENATO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(393, '5CT3X0FN5H', 'NRFPOF5Z', 3, 'default_img.jpg', 'GUZAREM', 'ANTONIO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(394, 'RIU6UQIRAI', 'NRF4W653', 3, 'default_img.jpg', 'GUZAREM', 'ARDIAN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(395, 'TNWXTMCT0L', 'NRF26B0U', 3, 'default_img.jpg', 'HARRIS', 'DOVY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(396, 'XQ5ZFQO2VV', 'NRFSTI13', 3, 'default_img.jpg', 'HERBULARIO', 'JUNREY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(397, 'W2Z0ITOCC2', 'NRFZSSOV', 3, 'default_img.jpg', 'HERRADURA', 'EDMONJR.', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(398, 'UAOQCTWXR2', 'NRFZ1VPO', 3, 'default_img.jpg', 'HINGOYON', 'ROWENAP.', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(399, 'YUXUYAHIAH', 'NRFN4PUV', 3, 'default_img.jpg', 'HISONA', 'DOROTHYJOY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(400, 'W2PK6TCPYQ', 'NRF0SZ4N', 3, 'default_img.jpg', 'HOBRO', 'CIRIL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(401, '5O5VL4W4LG', 'NRFP5HTC', 3, 'default_img.jpg', 'HULGUIN', 'MENALDJR.', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(402, 'IJK45NIQQ4', 'NRFU3KWF', 3, 'default_img.jpg', 'IBA?EZ', 'JOHN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(403, '22UAFMX4T6', 'NRFMD06U', 3, 'default_img.jpg', 'INAO', 'MYRNA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(404, '60RJS66WV1', 'NRFDVJLH', 3, 'default_img.jpg', 'INFANSO', 'MARGIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(405, 'V1AHEPKLIS', 'NRFTWOGL', 3, 'default_img.jpg', 'JABONITE', 'ELAISA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(406, 'BZW1T1XM6T', 'NRFHPSXO', 3, 'default_img.jpg', 'JAMOYOT', 'RODOLFO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(407, '5VR2OP14RI', 'NRF0JNT6', 3, 'default_img.jpg', 'JAMOYOT', 'ROLITOSR.', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(408, '1YW53UV4GH', 'NRFXSHRA', 3, 'default_img.jpg', 'JANDAYRAN', 'ADRIELBENEDICT', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(409, 'U56IFAC4L5', 'NRF5VHGC', 3, 'default_img.jpg', 'JANEO', 'EDUARDO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(410, 'P5CMI2WL6A', 'NRFYAWUO', 3, 'default_img.jpg', 'JANOY', 'JESSAMAE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(411, 'LLWVKWTE5B', 'NRFPFV5Y', 3, 'default_img.jpg', 'JANOY', 'RICO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(412, 'ZPS01EXQEJ', 'NRFWU50T', 3, 'default_img.jpg', 'JAVELLANA', 'RODERICK', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(413, 'TKJPBZ0EFZ', 'NRF4D5Q4', 3, 'default_img.jpg', 'JIMENEZ', 'JIMMYJR.', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(414, 'PRVPO1CG0Q', 'NRFSOL2R', 3, 'default_img.jpg', 'JORDAN', 'ANTONIO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(415, 'KNGAFHJLCE', 'NRFPOIJC', 3, 'default_img.jpg', 'JUANEZA', 'MAMERTOJR.', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(416, 'LJAHFOW1HQ', 'NRFSJQDU', 3, 'default_img.jpg', 'JUNGCO', 'GIESA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(417, 'TXIW34GH6W', 'NRFHKTQ4', 3, 'default_img.jpg', 'KAMLON', 'JONALYN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(418, '0XKPXY4VFD', 'NRFOBSRX', 3, 'default_img.jpg', 'LABSOG', 'MARVYSTEPHANIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(419, 'LW35TDM1CO', 'NRFZAZQW', 3, 'default_img.jpg', 'LANAO', 'LARRY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(420, '3HBNLOQHGW', 'NRFT5NEW', 3, 'default_img.jpg', 'LAPORE', 'LEONILA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(421, 'N20UKMFO6N', 'NRFLR6SF', 3, 'default_img.jpg', 'LARDIZABAL', 'JOHN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(422, 'E45PYX1C3V', 'NRFQWK5F', 3, 'default_img.jpg', 'LAYDA', 'INNOPAUL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(423, 'DVD5MCH1C6', 'NRFT3G6N', 3, 'default_img.jpg', 'LAZALITA', 'IAN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(424, 'F3JENHXL55', 'NRFYAPLD', 3, 'default_img.jpg', 'LAZALITA', 'JOEM', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(425, 'OAGZCRRJGX', 'NRF6QWVO', 3, 'default_img.jpg', 'LEGASTE', 'TOMANTHONY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(426, '5F2U663G25', 'NRFV2GZU', 3, 'default_img.jpg', 'LENDIO', 'ADIN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(427, 'LZMMB1KEUO', 'NRFC5VA2', 3, 'default_img.jpg', 'LENDIO', 'JOANNEMAY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(428, '6PGST11OLI', 'NRFDUHR6', 3, 'default_img.jpg', 'LIBRADO', 'MERA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(429, 'QLTHSJLAFL', 'NRFGSD3V', 3, 'default_img.jpg', 'LIRAZAN', 'FRANKSTEVEN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(430, '4LUSGNWF64', 'NRFNSNCE', 3, 'default_img.jpg', 'LIRAZAN', 'MAGNO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(431, 'JPCYT2PYMB', 'NRFG2CYA', 3, 'default_img.jpg', 'LOCSIN', 'EDGAR', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(432, 'IJ4JE0KBYV', 'NRFEQA0B', 3, 'default_img.jpg', 'LOQUINIO', 'GLAIZA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(433, 'EUZQYIVKE3', 'NRFDXSIY', 3, 'default_img.jpg', 'LOREDO', 'HARVEY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(434, 'J52THJXYQ2', 'NRFF5Y3L', 3, 'default_img.jpg', 'LUYAS', 'ANALISA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(435, 'ZHDGPOPSRQ', 'NRFTD5PI', 3, 'default_img.jpg', 'LUYAS', 'JEMUEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(436, 'DPNPMSA1PF', 'NRFSW5AN', 3, 'default_img.jpg', 'MACARO', 'JESSECA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(437, '2FBGTZMCCD', 'NRFVBL6C', 3, 'default_img.jpg', 'MACITAS', 'REX', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(438, '64KM6U0TUL', 'NRFKM0WP', 3, 'default_img.jpg', 'MAGALLANES', 'JESSIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(439, 'OO0JW4OALT', 'NRFT4QU1', 3, 'default_img.jpg', 'MAGPAYO', 'ANNELYN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(440, 'ZUTHK5VUSG', 'NRFMYF1O', 3, 'default_img.jpg', 'MAHINAY', 'LARRY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(441, 'ORXQDQYWNI', 'NRFGKJDF', 3, 'default_img.jpg', 'MAHINAY', 'LEO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(442, 'W2VUDV0Y56', 'NRF00KC3', 3, 'default_img.jpg', 'MAHINAY', 'LOMY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(443, 'MLP35JWQQY', 'NRFP6XGP', 3, 'default_img.jpg', 'MALUNES', 'VERGELIOJR.', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(444, 'OFOEKZMZPX', 'NRF531RJ', 3, 'default_img.jpg', 'MAMIGO', 'BETHELYN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(445, '60AO6TX0SJ', 'NRFE6PGX', 3, 'default_img.jpg', 'MANGILIMUTAN', 'KEVIN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(446, 'ES545GDOPJ', 'NRF11SBH', 3, 'default_img.jpg', 'MANGUBA', 'ARNULFO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(447, 'YGDTYAJCW6', 'NRFNIUJI', 3, 'default_img.jpg', 'MANILINGAN', 'CHANDRA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(448, 'RKM4PHID41', 'NRFF2Q3G', 3, 'default_img.jpg', 'MAPUTY', 'ALLAN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(449, 'JVZLAAXQSB', 'NRFLAKIT', 3, 'default_img.jpg', 'MARFIL', 'FRANKIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(450, '4ZEOMY3DO5', 'NRFNS0BN', 3, 'default_img.jpg', 'MARQUEZ', 'GODFREY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(451, 'ZMF2FWKWGU', 'NRFRT154', 3, 'default_img.jpg', 'MATU-OG', 'LEAMAE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(452, 'QKUPF5PEY3', 'NRFDYX5W', 3, 'default_img.jpg', 'MATURAN', 'JOSEPHINE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(453, 'XIOX4EHZI4', 'NRFS6GRR', 3, 'default_img.jpg', 'MAYANDIA', 'FELIX', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(454, 'U0RZJHZSUV', 'NRFBJTOO', 3, 'default_img.jpg', 'MEDEL', 'SHERIAN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(455, 'EKF1Q4LGIF', 'NRFPMQJA', 3, 'default_img.jpg', 'MENAVES', 'TROY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(456, '1QVBHGM3KA', 'NRFNBWQ3', 3, 'default_img.jpg', 'MENIANO', 'DOMINGUITO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(457, 'PND64TQRQF', 'NRFMOTE0', 3, 'default_img.jpg', 'MILAN', 'ANALYN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(458, 'SD62A4IIDO', 'NRF2UYYN', 3, 'default_img.jpg', 'MILLA', 'RENE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(459, 'GZ6RKKJRGJ', 'NRFOPRIO', 3, 'default_img.jpg', 'MILLAREZ', 'DANDY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(460, '5DRZCNTVIJ', 'NRFLB3TC', 3, 'default_img.jpg', 'MINAVES', 'RAY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(461, 'YCCKSK4P3Z', 'NRFCXEZ1', 3, 'default_img.jpg', 'MI?OZA', 'JOEVANIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(462, '3CR4LKHP4Z', 'NRFNKBVI', 3, 'default_img.jpg', 'MI?OZA', 'JOSUE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(463, 'X3RC01CV6L', 'NRF6ZCZH', 3, 'default_img.jpg', 'MIRANDA', 'JAIME', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(464, '0SGMM64OBP', 'NRFM5CKZ', 3, 'default_img.jpg', 'MONCAL', 'GENELYN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(465, 'G1GRUDPCPX', 'NRFE5R0N', 3, 'default_img.jpg', 'MONTEBON', 'JERALD', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(466, 'FDKPBKSVPO', 'NRFTSCQX', 3, 'default_img.jpg', 'MORE?O', 'PATRICKKRISTOFF', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(467, 'N0T0B35GZX', 'NRF1DQBQ', 3, 'default_img.jpg', 'MORE?O', 'RICARDO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(468, 'RDJQBF5IIZ', 'NRFBFHLK', 3, 'default_img.jpg', 'NARRA', 'GELMAR', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(469, 'SG1QPP3BC0', 'NRFPXQWQ', 3, 'default_img.jpg', 'NAVA', 'MAXIMINOJR.', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(470, 'M4660ZMFI2', 'NRFCIK2N', 3, 'default_img.jpg', 'NAVARO', 'ARNEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(471, '3OFKV1E4F2', 'NRFNAOI3', 3, 'default_img.jpg', 'NEGROPRADO', 'ELDIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(472, 'JSWO5ZV1ZQ', 'NRFPYZ4G', 3, 'default_img.jpg', 'NICOLAS', 'MARCIANA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(473, 'BBLIIVVXMD', 'NRFI53OT', 3, 'default_img.jpg', 'NORICO', 'CHRISTIANCHESTER', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(474, 'YQGQG221VM', 'NRFIHP3W', 3, 'default_img.jpg', 'NOSAL', 'FRELYNJOY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(475, '4QR3ILAE33', 'NRFKGECA', 3, 'default_img.jpg', 'NOSAL', 'MARYANN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(476, 'YV3SOLDYHG', 'NRFPWZQZ', 3, 'default_img.jpg', 'NOYNAY', 'DONNA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(477, '3CAQ043TLC', 'NRFEB3C1', 3, 'default_img.jpg', 'OBA?ANA', 'MARNIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(478, 'P1N2P1QY3X', 'NRFBUCXR', 3, 'default_img.jpg', 'OBREGON', 'RACKIEBOY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(479, 'X4UAS5IX12', 'NRFQ2XKG', 3, 'default_img.jpg', 'OCTAVIO', 'SHIELA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(480, 'VDMXHDIJVJ', 'NRFMAPGI', 3, 'default_img.jpg', 'OGHAYON', 'NUVYLYN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(481, 'TAU4V15F1U', 'NRF30OH4', 3, 'default_img.jpg', 'OHOYLAN', 'JIMMY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(482, 'DA34OAXKL3', 'NRF15EPF', 3, 'default_img.jpg', 'OINTINA', 'RENE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(483, 'VBDCJ60QFY', 'NRFVIOKF', 3, 'default_img.jpg', 'OLBOC', 'BASILIOJR.', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(484, '51FMPIIZCO', 'NRFOMBY2', 3, 'default_img.jpg', 'OMBLERO', 'FELIPE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(485, 'M20UOXAAD2', 'NRF0BN51', 3, 'default_img.jpg', 'OMBLERO', 'JESUS', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(486, '1QAGHA1ZPX', 'NRF5TSBD', 3, 'default_img.jpg', 'OMBLERO', 'JIMMY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(487, '3X3FNIOM35', 'NRFLO4HV', 3, 'default_img.jpg', 'OMILIG', 'ANDRIAN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(488, 'YB32J6VM15', 'NRFPCH2N', 3, 'default_img.jpg', 'OMITER', 'MARS', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(489, '2VR1B3XHIC', 'NRFMCE1Q', 3, 'default_img.jpg', 'OROT', 'GENEVEVE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(490, 'ZZDL0Y5OVG', 'NRFT2WXP', 3, 'default_img.jpg', 'ORTEGA', 'ANABELLA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(491, 'FNUBQ6L3OV', 'NRF1TJIP', 3, 'default_img.jpg', 'ORTIAGA', 'BENNY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(492, 'KMZKYJWNV6', 'NRFF3QBP', 3, 'default_img.jpg', 'PABALATE', 'JAMESMARK', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(493, 'DIYVWUU01C', 'NRFHVA3K', 3, 'default_img.jpg', 'PABALATE', 'ROSAN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(494, 'WCKL35PS64', 'NRFC25JE', 3, 'default_img.jpg', 'PABILLARAN', 'MARK', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(495, 'IKOGDDAFWE', 'NRF4NTL3', 3, 'default_img.jpg', 'PACURIB', 'LEVYJUN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(496, 'FPID6M52ZC', 'NRFL0EOJ', 3, 'default_img.jpg', 'PADAYHAG', 'JOSEPHINE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(497, 'OPNUZK2EU5', 'NRFDAVPQ', 3, 'default_img.jpg', 'PADOGA', 'ROMELIN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(498, '2BIFOYKXFD', 'NRFESOGV', 3, 'default_img.jpg', 'PAELDAN', 'REA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(499, 'PLELRB1O0X', 'NRFCZRFI', 3, 'default_img.jpg', 'PALACIOS', 'LILIBETH', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(500, '5DPJXX2LMM', 'NRFMD0J5', 3, 'default_img.jpg', 'PALMA', 'NOEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(501, '2LKFOSZNC4', 'NRFBHJI2', 3, 'default_img.jpg', 'PA?A', 'IVYJOYCE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(502, '5M33SV3UNU', 'NRFGGLHJ', 3, 'default_img.jpg', 'PANCHO', 'KOOLINJOHN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(503, 'MWPSVEN4P4', 'NRFG2HRL', 3, 'default_img.jpg', 'PANIGON', 'JESSA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(504, 'K5Q12YE3ZR', 'NRF5JP3K', 3, 'default_img.jpg', 'PARA-ON', 'GEMMA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(505, 'SU53ZMBWEX', 'NRFN2FRZ', 3, 'default_img.jpg', 'PARA-ON', 'JESSMARK', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(506, 'U5TFK3VP4S', 'NRF2BTFA', 3, 'default_img.jpg', 'PARA-ON', 'MARKJOHN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(507, 'HUBGFFZJVT', 'NRFTTJQB', 3, 'default_img.jpg', 'PARA-ON', 'RODGEN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(508, 'WHKC3H6MXB', 'NRFNNH0Q', 3, 'default_img.jpg', 'PARRE?O', 'DAISY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(509, 'K5QS6CY5HJ', 'NRFHFNTR', 3, 'default_img.jpg', 'PARRE?O', 'JEZEBEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(510, 'LKOQHAKHSA', 'NRFWCACF', 3, 'default_img.jpg', 'PARRE?O', 'MARYMICHELLE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(511, 'UUIOEXMNCX', 'NRF3MLAK', 3, 'default_img.jpg', 'PAT', 'RENE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(512, '6ATM163MKR', 'NRFWVYKU', 3, 'default_img.jpg', 'PAUTAN', 'FILBERT', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(513, 'MG4JWK3C1S', 'NRFBVPXH', 3, 'default_img.jpg', 'PE?AFIEL', 'RANELYN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(514, 'XYD3SZS12D', 'NRFNWLFQ', 3, 'default_img.jpg', 'PEREZ', 'ANNABELLE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(515, 'OZK65BCOHT', 'NRFD1DY6', 3, 'default_img.jpg', 'PEREZ', 'GREG', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(516, 'CCJPCXMLLB', 'NRF4CSVV', 3, 'default_img.jpg', 'PEREZ', 'JHONBERT', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(517, 'OVAZ0K1UU1', 'NRF6K65Z', 3, 'default_img.jpg', 'PEREZ', 'KENNETH', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(518, '0XTWI4LS0Q', 'NRFGTTTO', 3, 'default_img.jpg', 'PERFUMA', 'JUDY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(519, '10QF4QZNTI', 'NRFFCPGM', 3, 'default_img.jpg', 'PIA', 'JOCELYN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(520, 'UK4KEXHRTX', 'NRFDXYXF', 3, 'default_img.jpg', 'PI?ACERADA', 'ROSEMARIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(521, 'VHPWMUBL61', 'NRF6XTCL', 3, 'default_img.jpg', 'PINGCAS', 'CHARLIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(522, 'RR6MPFUJ3J', 'NRFNAOEV', 3, 'default_img.jpg', 'PITA', 'FREGIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(523, 'E42ICTUNAR', 'NRFPZWAG', 3, 'default_img.jpg', 'PLACER', 'SERGIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(524, 'FZS6X4QNI1', 'NRFTVTWH', 3, 'default_img.jpg', 'PLACIDO', 'JUNVER', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(525, '4G2O6G0YUB', 'NRFH116X', 3, 'default_img.jpg', 'POLINES', 'WILMA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(526, '4SQ5QPMINN', 'NRF2ITZ1', 3, 'default_img.jpg', 'POLISTICO', 'THERESA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(527, 'YF4IBM0VGW', 'NRFTR4PZ', 3, 'default_img.jpg', 'PONTIOSO', 'KRESLYJOY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(528, '44HKUWFODQ', 'NRFRZWRS', 3, 'default_img.jpg', 'POPIOCO', 'JUDY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(529, 'QMPDKK1YJ6', 'NRF3R0XU', 3, 'default_img.jpg', 'PUBLICO', 'LORRAINE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(530, 'BIMB245KQ3', 'NRFKIKLK', 3, 'default_img.jpg', 'RALLOS', 'JOHNCARL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(531, 'UGXGFEVF1U', 'NRFDGQVR', 3, 'default_img.jpg', 'RAMIREZ', 'FLORA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(532, 'DXUSA2ZEX6', 'NRF3ENP0', 3, 'default_img.jpg', 'RAMOS', 'RIO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(533, '5SH1OPZME6', 'NRFBHSOH', 3, 'default_img.jpg', 'REBALDE', 'EDDIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(534, 'QSNCYVJRVL', 'NRFUSJD0', 3, 'default_img.jpg', 'REGALA', 'ELVIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(535, 'ASDWDSBCDG', 'NRFQ14CW', 3, 'default_img.jpg', 'RELIQUIAS', 'JEMEROSE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(536, '22EQXNYNZR', 'NRFM6TRV', 3, 'default_img.jpg', 'RELIQUIAS', 'JOGANE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(537, 'WB3WEKQ13V', 'NRFCRJGX', 3, 'default_img.jpg', 'RELIQUIAS', 'JOSEROY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(538, '052PPGV0YO', 'NRFBJ5JN', 3, 'default_img.jpg', 'RELIQUIAS', 'JUANIII', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(539, 'VEGFXO3P21', 'NRFS0EXQ', 3, 'default_img.jpg', 'RELIQUIAS', 'REMAR', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(540, 'Q45RWX2ODD', 'NRFIEJXS', 3, 'default_img.jpg', 'RELOX', 'MANUELJR.', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(541, '260BPNGZOE', 'NRFUJFRP', 3, 'default_img.jpg', 'RENQUIJO', 'CRISTINA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(542, 'FARX201XOP', 'NRFVHC2M', 3, 'default_img.jpg', 'RIVAS', 'MARKLORENZ', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(543, 'CE6WFUD2KL', 'NRFU1K4P', 3, 'default_img.jpg', 'ROBLES', 'DANICAKAYE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(544, '5NCGGBAKQF', 'NRFGFMSH', 3, 'default_img.jpg', 'RONDUBIO', 'CHIDE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(545, 'QT5YNV6ZOH', 'NRFX4IQ4', 3, 'default_img.jpg', 'ROTE', 'ROLAND', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(546, 'CLNVOA3SOJ', 'NRFQWZSG', 3, 'default_img.jpg', 'SABIJON', 'JOHNMARK', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(547, 'C3ALYOEC6V', 'NRFSLW4M', 3, 'default_img.jpg', 'SABIJON', 'RICKY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(548, 'JXQCDZ0EJI', 'NRFXWFJV', 3, 'default_img.jpg', 'SAEL', 'GARY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(549, 'LINTMSFUUE', 'NRFKGISU', 3, 'default_img.jpg', 'SALA', 'MARKIAN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(550, 'LJGEKEJJU3', 'NRFCWCME', 3, 'default_img.jpg', 'SALIG', 'ARIEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(551, 'O2EU5HOJZL', 'NRFUN4L1', 3, 'default_img.jpg', 'SALIG', 'JEANY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(552, '3W1DR6X6OP', 'NRFKQCIR', 3, 'default_img.jpg', 'SALIG', 'LENIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(553, 'FIBOIAU2CP', 'NRFC1UPU', 3, 'default_img.jpg', 'SALIMBOT', 'JONERVIN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(554, 'H3TXC4KKRK', 'NRFEUPQQ', 3, 'default_img.jpg', 'SALMORIN', 'JOEBERT', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(555, 'MMPOZDV25Z', 'NRFMBDEQ', 3, 'default_img.jpg', 'SALMORIN', 'MAGELLAN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(556, 'SI6HGPQRBP', 'NRFI2ZZO', 3, 'default_img.jpg', 'SALVACION', 'MARIALINA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(557, '0VCDRK4QTN', 'NRFN4ABU', 3, 'default_img.jpg', 'SALVADOR', 'GINA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(558, 'Y6Q2Y3FWZ1', 'NRFPEG5Y', 3, 'default_img.jpg', 'SANCHEZ', 'FALCONERY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(559, 'XVW6IXLRLI', 'NRFHWN2R', 3, 'default_img.jpg', 'SANGASINA', 'HARVEY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(560, 'FIGWZOMTRB', 'NRFP2DO4', 3, 'default_img.jpg', 'SANTIAGO', 'RICO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(561, 'RJ6VCJ121D', 'NRFKDWRL', 3, 'default_img.jpg', 'SANTIAGO', 'SARTEJAMES', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(562, 'SP0W2QTWJX', 'NRFIYVHL', 3, 'default_img.jpg', 'SAPATOSE', 'ESPERANZA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(563, 'EMPYRQSA4H', 'NRFNSOTP', 3, 'default_img.jpg', 'SAPENE', 'REGINA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(564, '6IDWW3WBJR', 'NRFWQ3DS', 3, 'default_img.jpg', 'SAPI-AN', 'SHAREWIN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(565, 'LDBLV33OY2', 'NRF5HLZY', 3, 'default_img.jpg', 'SAPIO', 'RODRIGOJR.', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(566, 'RP42WDGCJI', 'NRFEYGVI', 3, 'default_img.jpg', 'SARINO', 'CAMERON', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(567, 'QQRBWJ4GHI', 'NRFP1M5Y', 3, 'default_img.jpg', 'SATINGASIN', 'JOHNREY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(568, 'WUOMRC5TMU', 'NRFM0LJ1', 3, 'default_img.jpg', 'SATOMCACAL', 'MARIO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(569, '3BQVHRDLRA', 'NRFFJP45', 3, 'default_img.jpg', 'SAYLO', 'RALPHMICHAEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(570, 'HC1KUGX355', 'NRFUMBZN', 3, 'default_img.jpg', 'SAYSON', 'RUSSEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(571, 'WQAHHQ510P', 'NRFYPW4E', 3, 'default_img.jpg', 'SECOR', 'ANIELON', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(572, '6QH241UX6S', 'NRF3B3JK', 3, 'default_img.jpg', 'SECOR', 'NI?OALVIN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(573, '2R2MXOEHJ6', 'NRFQLOOT', 3, 'default_img.jpg', 'SEDENIO', 'JAIME', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(574, 'JANFXSL4A2', 'NRFGC0DZ', 3, 'default_img.jpg', 'SEVILLANO', 'CLOIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(575, 'CCTWMVGYFS', 'NRFNNITN', 3, 'default_img.jpg', 'SIBUG', 'RODOLFO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(576, '1C26Q1Q6PQ', 'NRFWKJEG', 3, 'default_img.jpg', 'SOCRATES', 'SORNIDO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(577, 'ME3IYBYJI0', 'NRFWQUYX', 3, 'default_img.jpg', 'SORONGON', 'FLORGEM', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(578, 'LM6KCY0HMK', 'NRFR3UET', 3, 'default_img.jpg', 'SORONGON', 'JOVIEANN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(579, 'D4RDGICJCL', 'NRFSNY01', 3, 'default_img.jpg', 'SUA', 'ELMEN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(580, '43UIJPEOAH', 'NRFQJB1G', 3, 'default_img.jpg', 'SUGINO', 'JANCEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(581, 'KGJI2USRUV', 'NRF1J6HI', 3, 'default_img.jpg', 'TABORADA', 'MARIVIC', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(582, 'WOAK6UGO0E', 'NRFRPZQH', 3, 'default_img.jpg', 'TAIPEN', 'JOEMARY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(583, 'JDWXISBPUZ', 'NRF2YJ5M', 3, 'default_img.jpg', 'TALANQUINES', 'ANGELKIETH', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(584, '5KLQRAKIWT', 'NRFIJ25R', 3, 'default_img.jpg', 'TALANQUINES', 'ANITA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(585, '5I23A0UEQA', 'NRFB0UC0', 3, 'default_img.jpg', 'TALANQUINES', 'LUNA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(586, 'PHY1JKZJXB', 'NRFYEDZY', 3, 'default_img.jpg', 'TALEON', 'ROGELIO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(587, 'QAD1SYG1JA', 'NRFSYJQS', 3, 'default_img.jpg', 'TANJENTE', 'ANTHONY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(588, 'RRZ0A3AAA0', 'NRFOXKE5', 3, 'default_img.jpg', 'TAPADO', 'AGAPITO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(589, 'JJHWFD4K1V', 'NRFJU6YI', 3, 'default_img.jpg', 'TAYOS', 'RONIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(590, 'OQVI2K3XRO', 'NRF61MRG', 3, 'default_img.jpg', 'TEJERO', 'MA.SARAHMAY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(591, 'OOUVPIVBMG', 'NRFARDLS', 3, 'default_img.jpg', 'TELONIO', 'ANALOU', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(592, 'FWMO2GLWHB', 'NRFUFXEQ', 3, 'default_img.jpg', 'TELONIO', 'JUDY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(593, '2CWRBK2MSD', 'NRFJVFZH', 3, 'default_img.jpg', 'TELONIO', 'JUMEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(594, 'FW6U1RM1UZ', 'NRFGI1AZ', 3, 'default_img.jpg', 'TELONIO', 'MANUELJR.', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(595, 'GGFKSHTX5R', 'NRFNE14C', 3, 'default_img.jpg', 'TELONIO', 'SIMPLICIO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(596, 'IGVZAHMNL6', 'NRF6SFK6', 3, 'default_img.jpg', 'TEMBREVILLA', 'ALDRENE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(597, '2H3SZD1RWR', 'NRFNMY4Z', 3, 'default_img.jpg', 'TEMBREVILLA', 'ANNMARGARETTE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(598, 'UK1MELEO0O', 'NRF412KN', 3, 'default_img.jpg', 'TEMBREVILLA', 'ANTHONY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(599, 'JFP4TMR5C3', 'NRF4VX05', 3, 'default_img.jpg', 'TEMBREVILLA', 'ELEANOR', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(600, '1DAQCYBAXU', 'NRFWB0KT', 3, 'default_img.jpg', 'TEMBREVILLA', 'JOAQUINIII', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(601, 'GSZFYAKSU6', 'NRFP56KB', 3, 'default_img.jpg', 'TEMBREVILLA', 'JOFEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(602, '361DO40ONS', 'NRF2I4O6', 3, 'default_img.jpg', 'TEMBREVILLA', 'JULIEMAE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(603, 'IC3N1ICIIT', 'NRFR0DIX', 3, 'default_img.jpg', 'TEMBREVILLA', 'MAELANE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(604, 'HQZKZVQQJY', 'NRFS0U15', 3, 'default_img.jpg', 'TEMBREVILLA', 'MARLON', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(605, 'IA0X5UQ1SR', 'NRFEYHK2', 3, 'default_img.jpg', 'TEMBREVILLA', 'OLIVE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(606, 'PBE3VAI66N', 'NRFSLPEL', 3, 'default_img.jpg', 'TEMBREVILLA', 'RICKY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(607, 'PKAJSDB4IV', 'NRFBBALV', 3, 'default_img.jpg', 'TEMBREVILLA', 'ROBERT', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(608, 'WAZ2CXGXSY', 'NRFWXTXD', 3, 'default_img.jpg', 'TEMBREVILLA', 'RODERICK', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(609, 'ZHCGQDKGKQ', 'NRFZP245', 3, 'default_img.jpg', 'TIANGA', 'ANALIZA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(610, '1W5UNXGB3D', 'NRFQ2VCE', 3, 'default_img.jpg', 'TIBLERO', 'EVA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(611, '6EZM1TTF6T', 'NRF6ECMM', 3, 'default_img.jpg', 'TILANO', 'ROLEMAR', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL);
INSERT INTO `personnels` (`personnel_id`, `RFTag_id`, `personnel_id_code`, `shift_id`, `img`, `lname`, `fname`, `mname`, `suffix`, `age`, `sex`, `marital_status`, `bdMM`, `bdDD`, `bdYYYY`, `birth_place`, `address`, `email`, `personal_pnum`, `emergency_pnum`, `conPerson_lname`, `conPerson_fname`, `conPerson_mname`, `conPerson_relationship`, `do_id`, `des_id`, `sal_grade`, `sal_step`, `sal_level`, `rate_per_day`, `gass_id`, `empStat_id`, `eligibility`, `plantilla_num`, `appointment_date`, `separation_date`, `num_of_yrs`, `tin_num`, `gsis_num`, `pagibig_num`, `philHealth_num`, `monthly_salary`) VALUES
(612, 'JD44P1FT0A', 'NRFAGY1K', 3, 'default_img.jpg', 'TILOS', 'DWIGHT', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(613, 'J4BB6TBZL4', 'NRF3N3WW', 3, 'default_img.jpg', 'TILOS', 'GOLDIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(614, 'ERCNDJUNZ1', 'NRFKVEF3', 3, 'default_img.jpg', 'TILOS', 'SALVADOR', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(615, '4PWJQCRRDZ', 'NRFJXJGX', 3, 'default_img.jpg', 'TIPLES', 'ARNELKIM', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(616, 'K4K6T6PZ6X', 'NRFXH1VO', 3, 'default_img.jpg', 'TIPLES', 'RONIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(617, 'WOU0NJLRGP', 'NRF4ADG0', 3, 'default_img.jpg', 'TITONG', 'CHERYL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(618, 'ZVWPOVQHPM', 'NRFIEB6X', 3, 'default_img.jpg', 'TOBONGBANUA', 'JAZEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(619, '2J21VMAC5O', 'NRFFEAB6', 3, 'default_img.jpg', 'TOLEDO', 'MA.NONNA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(620, 'PAO4DCUOAU', 'NRFVNXGW', 3, 'default_img.jpg', 'TOLEDO', 'RUEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(621, 'WNAAMHTXCX', 'NRFHWSYZ', 3, 'default_img.jpg', 'TOMADO', 'EDUARDOJR.', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(622, '1CQXJBR0MV', 'NRF0P5TC', 3, 'default_img.jpg', 'TOMADO', 'NOVELYN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(623, 'MEJG1T3LKW', 'NRFCPLU2', 3, 'default_img.jpg', 'TOMAS', 'ROSIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(624, 'IUPFEXQZIC', 'NRFHX6CK', 3, 'default_img.jpg', 'TOMO', 'MARVIN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(625, 'RO02WAWJN4', 'NRFJAXJK', 3, 'default_img.jpg', 'TONEL', 'KARYNN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(626, 'D3ONJ32UK5', 'NRFMP4RC', 3, 'default_img.jpg', 'TONOGBANUA', 'NIXONJHON', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(627, 'DYFZXUGTHA', 'NRFYJ4AU', 3, 'default_img.jpg', 'TORNILLA', 'SAIROUS', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(628, '1GQJAHGYVT', 'NRFVEOLR', 3, 'default_img.jpg', 'TORRES', 'EDUARDO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(629, '4MSIK0CBFJ', 'NRFDVJ3J', 3, 'default_img.jpg', 'TORRIBLE', 'JEMIMA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(630, 'K6KWQ0W3IS', 'NRFRSDIH', 3, 'default_img.jpg', 'TOTESORA', 'LANIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(631, 'XYTRIZ6G4T', 'NRFLXNYL', 3, 'default_img.jpg', 'TUBILLEJA', 'TOMMYJOHN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(632, '6F54PXWGYN', 'NRFDAG33', 3, 'default_img.jpg', 'TUBONGBANUA', 'JEANNY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(633, 'K14R6ZHCKU', 'NRFLYXO3', 3, 'default_img.jpg', 'TUBONGBANUA', 'LEOBENA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(634, 'HKYA61TN4K', 'NRFGZRKG', 3, 'default_img.jpg', 'TUGONON', 'ROLANDO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(635, '4PJ1MLTGSB', 'NRF0NGB4', 3, 'default_img.jpg', 'TULAYBA', 'NOVO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(636, 'XCK40QEJHW', 'NRF2S1FZ', 3, 'default_img.jpg', 'TULAYBA', 'TEODOLFO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(637, 'FG250KA6XQ', 'NRFOZ3KD', 3, 'default_img.jpg', 'TUPAS', 'RYANMARS', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(638, 'BNWYPW5GFL', 'NRFYUBDC', 3, 'default_img.jpg', 'UBAMOS', 'MARYJANE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(639, 'T1HLXH55ZN', 'NRFKF3HL', 3, 'default_img.jpg', 'UBAMOS', 'RENIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(640, 'U366XQQ3UD', 'NRFR3SUP', 3, 'default_img.jpg', 'UBAMOS', 'REYNALD', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(641, 'ML3SSL0DEF', 'NRFJ21HV', 3, 'default_img.jpg', 'VALENCIA', 'OSCAR', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(642, 'ITO4SW5GDT', 'NRF4LKPH', 3, 'default_img.jpg', 'VASQUEZ', 'MELCAH', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(643, 'BCO5WKZ41Z', 'NRFZYL5S', 3, 'default_img.jpg', 'VASQUEZ', 'VALLEREEMAE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(644, 'SL1F6NU3AN', 'NRFQN4HI', 3, 'default_img.jpg', 'VEGA', 'JOSEMARIA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(645, 'KOTRGEFFZM', 'NRFVW4KV', 3, 'default_img.jpg', 'VELASCO', 'NIKA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(646, 'TCKJBCH1BQ', 'NRFSEFD6', 3, 'default_img.jpg', 'VELOS', 'MAE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(647, 'F0QS4DRSJT', 'NRFONEZ6', 3, 'default_img.jpg', 'VERANO', 'CHARLIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(648, 'YSR3OLOBHL', 'NRFYHS2B', 3, 'default_img.jpg', 'VERDE', 'RYANMARS', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(649, 'KIACJJHAWK', 'NRFYDSKS', 3, 'default_img.jpg', 'VERGARA', 'JOCELYN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(650, '6KHI5U63OF', 'NRFNUB2G', 3, 'default_img.jpg', 'VERGARA', 'RENELYN', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(651, 'TOZEZ1DHO3', 'NRFZOCFJ', 3, 'default_img.jpg', 'VIDAURRAZAGA', 'MARICON', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(652, 'HTX6EJKMZG', 'NRFVUUP6', 3, 'default_img.jpg', 'VIESCA', 'JEBEE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(653, 'F25NRENPTT', 'NRFWSIGX', 3, 'default_img.jpg', 'VILLADO', 'JENNYMAY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(654, '1NQ4KD3ILO', 'NRF1M5SQ', 3, 'default_img.jpg', 'VILLAFUERTE', 'FLORENCE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(655, 'T6NDKQYNDH', 'NRFX2U6Z', 3, 'default_img.jpg', 'VILLAFUERTE', 'REMY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(656, 'HYZXUB2TLI', 'NRFFNIAK', 3, 'default_img.jpg', 'VILLAHERMOSA', 'JICKIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(657, '4WFAWL0RUN', 'NRFMVGYY', 3, 'default_img.jpg', 'VILLAHERMOSA', 'SANNYBOY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(658, 'L1VHFYPVEQ', 'NRFKXK1B', 3, 'default_img.jpg', 'VILLANUEVA', 'ELDIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(659, 'QYEXOZMAGG', 'NRF1ASC5', 3, 'default_img.jpg', 'VILLANUEVA', 'JIMMY', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(660, 'TZYASNICFP', 'NRFCIHAQ', 3, 'default_img.jpg', 'VILLANUEVA', 'ROSALIA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(661, 'KE34N1PQUM', 'NRFN0ICG', 3, 'default_img.jpg', 'VILLANUEVA', 'VINA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(662, 'KSVCA2TW2C', 'NRFHO3XA', 3, 'default_img.jpg', 'VILLAR', 'ESTELA', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(663, 'CGVTUPE5YX', 'NRFW1YG0', 3, 'default_img.jpg', 'VILLAR', 'LEVI', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(664, '1LWW4O5EHR', 'NRFJETQC', 3, 'default_img.jpg', 'VILLARETE', 'GRINGO', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(665, '6F1VFOQAP6', 'NRF2WYFZ', 3, 'default_img.jpg', 'VISTAR', 'JOHNMARK', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(666, 'ASTBW15KOI', 'NRFVTTCO', 3, 'default_img.jpg', 'VISTAR', 'RONIE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(667, 'KODYMOKYGN', 'NRFE1Z5F', 3, 'default_img.jpg', 'WEE', 'ROMEOJR.', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(668, 'NMG3VXLOJV', 'NRFP36S0', 3, 'default_img.jpg', 'YONTING', 'REAZEL', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(669, 'HRKVS1KO2M', 'NRFGBLKF', 3, 'default_img.jpg', 'YUSAY', 'JASTINE', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(670, 'WIGOR6FCVH', 'NRFHCVVR', 3, 'default_img.jpg', 'YUSAY', 'ROLAND', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 26, 0, 0, 0, 0, 0.00, 0, 4, '', '', '', NULL, 0, '', '', '', '', NULL),
(671, 'CT-RPM-1216-2024-14', 'CT-RPM-1216-2024-14', 0, '', 'MATUGAS', 'RAMIR', 'P.', '-', 48, 'Male', 'Single', '09', '28', '1976', 'Sagay Negros Occidental', 'Sagay Negros Occidental', 'ramirmatugas@gmail.com', '+639273480185', '+639         ', '', '', '', '', 2, 0, 0, 0, 0, 0.00, 0, 0, 'none', 'MO-14', '12/16/2024', NULL, 0, '490-790-181', '    -   -   ', '106-000-569-378', '11-203297443-3', NULL),
(672, 'P-KTT-12162024-136', 'P-KTT-12162024-136', 0, '', 'TUPAS', 'KATHREEN', 'TEMBREVILLA', '-', 26, 'Female', 'Single', '09', '25', '1998', 'Bacolod City', 'Brgy. II Poblacion Hinoba-an Negros Occidental', 'kathreentupas98@gmail.com', '+639696071043', '+639         ', '', '', '', '', 7, 0, 0, 0, 0, 0.00, 0, 0, 'Professional', 'MSWDO-136', '12/16/2024', NULL, 0, '774-037-677', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(673, 'P-MHTT-122025-143', 'P-MHTT-122025-143', 0, '', 'TUMBALE', 'MARY HOPE', 'TEMBREVILLA', '-', 50, 'Female', 'Married', '11', '02', '1974', 'Hinoba-an Negros Occidental', 'Barangay Alim, Hinoba-an Negros Occidental', 'maryhopetumbale@gmail.com', '+639675021027', '+639         ', 'TUMBALE', 'GENARO', 'ORTIZ', 'Spouse', 7, 0, 0, 0, 0, 0.00, 0, 0, 'none', 'MSWDO-143', '01/02/2025', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(674, 'P-JJGE-122025-70', 'P-JJGE-122025-70', 0, '', 'GIMOTEA', 'JERLYN JAY', 'ENGCOY', '-', 0, 'Female', 'Married', '  ', '  ', '    ', 'Hinoba-an Negros Occidental', 'Barangay Alim, Hinoba-an Negros Occidental', 'gimoteajerlynjay@gmail.com', '+639556440190', '+639         ', 'GIMOTEA', 'ACHILLES BEN', 'VALLEGA', 'Spouse', 12, 0, 0, 0, 0, 0.00, 0, 0, 'RA1080(MIDWIFE)', 'RHU-70', '  /  /    ', NULL, 0, '473-492-161', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(675, 'BCUT6421HG', 'P-FTC-7162024-101', 0, '', 'CANDULIZAS', 'FROILAN', 'T.', '-', 43, 'Male', 'Married', '11', '20', '1981', 'Hinoba-an Negros Occidental', 'Brgy. II Poblacion Hinoba-an Negros Occidental', '', '+639478755884', '+639         ', 'GABALES', 'MABEL', 'MONTESCLAROS', 'Spouse', 3, 0, 0, 0, 0, 0.00, 0, 0, 'none', 'MEO-101', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(676, 'CT-MTC-8162023-18', 'CT-MTC-8162023-18', 0, '', 'CANDULIZAS', 'MOLAVE', 'T.', '-', 0, 'Male', 'Married', '  ', '  ', '    ', '', '', '', '+639         ', '+639         ', '', '', '', '', 2, 0, 0, 0, 0, 0.00, 0, 0, '', '', '  /  /    ', NULL, 0, '   -   -   ', '    -   -   ', '   -   -   -   ', '  -         - ', NULL),
(677, 'NRF63DA2', 'test', 3, 'nrf63da2-3rd.png', 'TEST', 'TEST', 'TEST', '-', 33, 'Male', 'Married', '01', '21', '1993', 'Binalbagan', 'Purok Riverside, Paglaum (Pob), Binalbagan, Negros Occidental', 'emiloimagtolis@gmail.com', '+639303546547', '+639301234567', 'MAGTOLIS', 'LORAINE JANE', 'BABANO', 'Spouse', 17, 0, 0, 0, 0, 0.00, 0, 1, 'none', '-', '01/01/2020', NULL, 6, '695-126-198', '5151-616-984', '195-162-116-161', '56-165161651-5', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `personnel_educ_bg`
--

CREATE TABLE `personnel_educ_bg` (
  `eb_id` int(11) NOT NULL,
  `personnel_id` int(11) NOT NULL,
  `degree` varchar(55) NOT NULL,
  `course_details` varchar(255) NOT NULL,
  `units` int(5) NOT NULL,
  `year_grad` varchar(25) NOT NULL,
  `school_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `personnel_educ_bg`
--

INSERT INTO `personnel_educ_bg` (`eb_id`, `personnel_id`, `degree`, `course_details`, `units`, `year_grad`, `school_name`) VALUES
(1, 1, 'Bachelors', 'Bachelor of Arts major in English', 0, '', 'Kabankalan Catholic College'),
(2, 14, 'Bachelors', 'Bachelor of Arts major in English', 0, '', 'Kabankalan Catholic College'),
(3, 14, 'High School', 'NA', 4, '', 'Saint Columbans Academy'),
(4, 14, 'High School', 'NA', 123, '', 'Saint Michaels Academy'),
(5, 14, 'Elementary', 'NA', 0, '', 'Hinoba-an Central Elementary School'),
(6, 14, 'Masters', 'Masters in Public Administration', 15, '', 'University of Negros Occidental-Recoletos'),
(7, 140, 'Bachelors', 'Bachelor of Science in Agriculture Major in Agricultural Extension', 0, '2007-03', 'Negros Oriental State University'),
(8, 82, 'Bachelors', 'Bachelor of Science in Commerce Major in Accounting', 0, '1988-01', 'LA CONSOLATION COLLEGE'),
(9, 142, 'Bachelors', 'Bachelor of Arts Major in Psychology', 0, '2015-03', 'Cebu Normal University');

-- --------------------------------------------------------

--
-- Table structure for table `personnel_fam_bg`
--

CREATE TABLE `personnel_fam_bg` (
  `fm_id` int(11) NOT NULL,
  `personnel_id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL DEFAULT '-',
  `sex` varchar(6) NOT NULL DEFAULT '-',
  `relationship` varchar(55) NOT NULL DEFAULT '-',
  `contact_num` varchar(25) NOT NULL DEFAULT '-'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `personnel_fam_bg`
--

INSERT INTO `personnel_fam_bg` (`fm_id`, `personnel_id`, `fullname`, `sex`, `relationship`, `contact_num`) VALUES
(1, 1, 'Herradura, Maegan Shun, Obenza', 'Female', 'Child', ''),
(2, 1, 'Herradura, Stacey Mignon, Obenza', 'Female', 'Child', ''),
(3, 14, 'Stacey Mignon Obenza Herradura', 'Female', 'Child', '09762723624'),
(4, 14, 'Maegan Shun Obenza Herradura', 'Female', 'Child', '09485725992');

-- --------------------------------------------------------

--
-- Table structure for table `personnel_file_audit_logs`
--

CREATE TABLE `personnel_file_audit_logs` (
  `audit_id` int(11) NOT NULL,
  `action_name` varchar(100) NOT NULL,
  `actor_personnel_id` int(11) DEFAULT NULL,
  `actor_access` varchar(100) DEFAULT NULL,
  `target_personnel_id` int(11) NOT NULL,
  `folder_id` int(11) DEFAULT NULL,
  `file_id` int(11) DEFAULT NULL,
  `action_details` text DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personnel_file_folders`
--

CREATE TABLE `personnel_file_folders` (
  `folder_id` int(11) NOT NULL,
  `personnel_id` int(11) NOT NULL,
  `folder_name` varchar(255) NOT NULL,
  `folder_slug` varchar(255) NOT NULL,
  `is_system_201` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `personnel_file_folders`
--

INSERT INTO `personnel_file_folders` (`folder_id`, `personnel_id`, `folder_name`, `folder_slug`, `is_system_201`, `date_created`) VALUES
(1, 66, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(2, 176, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(3, 177, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(4, 178, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(5, 73, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(6, 179, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(7, 180, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(8, 101, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(9, 181, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(10, 182, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(11, 117, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(12, 95, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(13, 138, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(14, 183, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(15, 184, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(16, 185, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(17, 82, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(18, 186, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(19, 187, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(20, 188, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(21, 189, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(22, 190, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(23, 191, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(24, 192, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(25, 193, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(26, 194, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(27, 118, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(28, 195, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(29, 196, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(30, 197, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(31, 198, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(32, 199, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(33, 200, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(34, 94, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(35, 201, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(36, 113, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(37, 202, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(38, 203, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(39, 30, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(40, 204, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(41, 144, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(42, 205, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(43, 134, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(44, 206, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(45, 207, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(46, 208, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(47, 83, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(48, 209, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(49, 119, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(50, 210, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(51, 211, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(52, 212, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(53, 213, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(54, 120, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(55, 214, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(56, 215, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(57, 173, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(58, 216, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(59, 217, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(60, 218, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(61, 219, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(62, 220, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(63, 221, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(64, 150, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(65, 222, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(66, 223, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(67, 224, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(68, 225, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(69, 226, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(70, 227, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(71, 90, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(72, 43, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(73, 229, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(74, 228, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(75, 139, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(76, 230, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(77, 231, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(78, 140, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(79, 79, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(80, 232, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(81, 233, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(82, 234, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(83, 235, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(84, 236, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(85, 121, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(86, 237, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(87, 238, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(88, 239, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(89, 240, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(90, 241, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(91, 242, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(92, 243, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(93, 122, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(94, 244, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(95, 18, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(96, 22, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(97, 245, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(98, 246, '201-files', '201-files', 1, '2026-05-19 17:37:05'),
(99, 247, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(100, 248, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(101, 249, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(102, 250, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(103, 251, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(104, 40, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(105, 252, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(106, 80, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(107, 253, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(108, 254, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(109, 108, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(110, 255, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(111, 256, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(112, 68, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(113, 257, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(114, 258, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(115, 259, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(116, 276, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(117, 277, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(118, 279, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(119, 260, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(120, 261, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(121, 262, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(122, 263, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(123, 264, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(124, 265, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(125, 266, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(126, 267, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(127, 268, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(128, 269, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(129, 270, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(130, 271, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(131, 272, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(132, 273, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(133, 116, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(134, 274, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(135, 115, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(136, 275, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(137, 172, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(138, 675, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(139, 278, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(140, 676, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(141, 280, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(142, 281, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(143, 282, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(144, 283, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(145, 171, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(146, 284, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(147, 4, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(148, 285, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(149, 286, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(150, 287, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(151, 288, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(152, 289, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(153, 290, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(154, 291, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(155, 292, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(156, 293, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(157, 294, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(158, 295, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(159, 296, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(160, 297, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(161, 298, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(162, 299, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(163, 300, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(164, 301, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(165, 302, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(166, 10, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(167, 303, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(168, 304, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(169, 305, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(170, 306, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(171, 307, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(172, 123, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(173, 308, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(174, 32, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(175, 309, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(176, 98, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(177, 170, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(178, 310, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(179, 311, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(180, 93, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(181, 51, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(182, 312, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(183, 313, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(184, 314, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(185, 169, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(186, 107, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(187, 31, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(188, 315, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(189, 316, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(190, 317, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(191, 318, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(192, 168, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(193, 109, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(194, 319, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(195, 320, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(196, 321, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(197, 167, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(198, 166, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(199, 322, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(200, 165, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(201, 323, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(202, 324, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(203, 325, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(204, 326, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(205, 327, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(206, 17, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(207, 328, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(208, 164, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(209, 329, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(210, 330, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(211, 331, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(212, 332, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(213, 333, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(214, 334, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(215, 335, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(216, 336, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(217, 337, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(218, 338, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(219, 339, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(220, 340, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(221, 341, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(222, 342, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(223, 343, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(224, 344, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(225, 345, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(226, 346, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(227, 347, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(228, 348, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(229, 349, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(230, 350, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(231, 351, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(232, 352, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(233, 353, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(234, 354, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(235, 355, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(236, 69, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(237, 356, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(238, 357, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(239, 37, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(240, 358, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(241, 359, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(242, 360, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(243, 124, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(244, 361, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(245, 362, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(246, 363, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(247, 57, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(248, 364, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(249, 365, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(250, 366, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(251, 367, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(252, 368, '201-files', '201-files', 1, '2026-05-19 17:37:06'),
(253, 47, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(254, 369, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(255, 44, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(256, 370, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(257, 371, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(258, 372, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(259, 49, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(260, 373, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(261, 374, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(262, 375, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(263, 376, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(264, 21, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(265, 377, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(266, 378, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(267, 379, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(268, 89, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(269, 380, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(270, 381, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(271, 382, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(272, 163, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(273, 383, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(274, 67, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(275, 6, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(276, 105, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(277, 384, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(278, 9, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(279, 385, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(280, 674, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(281, 85, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(282, 386, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(283, 387, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(284, 388, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(285, 389, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(286, 390, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(287, 391, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(288, 137, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(289, 63, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(290, 162, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(291, 111, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(292, 125, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(293, 392, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(294, 71, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(295, 393, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(296, 394, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(297, 395, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(298, 396, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(299, 397, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(300, 14, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(301, 398, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(302, 399, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(303, 400, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(304, 401, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(305, 402, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(306, 403, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(307, 404, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(308, 405, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(309, 406, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(310, 407, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(311, 408, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(312, 409, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(313, 410, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(314, 411, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(315, 412, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(316, 413, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(317, 100, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(318, 414, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(319, 76, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(320, 161, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(321, 415, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(322, 142, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(323, 416, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(324, 417, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(325, 126, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(326, 418, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(327, 419, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(328, 420, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(329, 421, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(330, 174, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(331, 422, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(332, 423, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(333, 424, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(334, 425, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(335, 426, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(336, 427, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(337, 428, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(338, 81, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(339, 429, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(340, 430, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(341, 11, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(342, 431, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(343, 12, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(344, 432, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(345, 433, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(346, 102, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(347, 151, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(348, 434, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(349, 435, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(350, 436, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(351, 437, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(352, 92, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(353, 438, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(354, 439, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(355, 127, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(356, 440, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(357, 441, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(358, 145, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(359, 442, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(360, 24, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(361, 443, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(362, 444, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(363, 445, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(364, 7, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(365, 149, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(366, 65, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(367, 96, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(368, 446, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(369, 447, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(370, 61, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(371, 26, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(372, 448, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(373, 46, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(374, 449, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(375, 450, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(376, 103, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(377, 451, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(378, 671, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(379, 452, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(380, 78, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(381, 453, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(382, 454, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(383, 455, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(384, 456, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(385, 461, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(386, 462, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(387, 457, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(388, 458, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(389, 459, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(390, 460, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(391, 62, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(392, 463, '201-files', '201-files', 1, '2026-05-19 17:37:07'),
(393, 464, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(394, 152, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(395, 465, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(396, 466, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(397, 467, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(398, 160, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(399, 468, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(400, 70, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(401, 97, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(402, 469, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(403, 470, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(404, 471, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(405, 472, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(406, 141, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(407, 473, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(408, 474, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(409, 475, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(410, 476, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(411, 146, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(412, 477, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(413, 478, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(414, 147, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(415, 19, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(416, 27, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(417, 479, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(418, 480, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(419, 481, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(420, 482, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(421, 483, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(422, 484, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(423, 485, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(424, 486, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(425, 487, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(426, 488, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(427, 86, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(428, 489, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(429, 490, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(430, 491, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(431, 501, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(432, 492, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(433, 493, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(434, 494, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(435, 148, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(436, 495, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(437, 128, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(438, 496, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(439, 497, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(440, 498, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(441, 499, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(442, 500, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(443, 502, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(444, 129, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(445, 503, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(446, 504, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(447, 505, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(448, 506, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(449, 507, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(450, 159, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(451, 508, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(452, 509, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(453, 510, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(454, 511, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(455, 512, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(456, 513, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(457, 514, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(458, 515, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(459, 516, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(460, 33, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(461, 517, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(462, 518, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(463, 130, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(464, 520, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(465, 519, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(466, 45, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(467, 521, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(468, 8, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(469, 522, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(470, 523, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(471, 524, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(472, 525, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(473, 526, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(474, 527, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(475, 528, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(476, 529, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(477, 35, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(478, 530, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(479, 531, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(480, 532, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(481, 533, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(482, 534, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(483, 87, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(484, 2, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(485, 99, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(486, 535, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(487, 72, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(488, 536, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(489, 16, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(490, 158, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(491, 537, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(492, 538, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(493, 84, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(494, 539, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(495, 540, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(496, 541, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(497, 542, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(498, 543, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(499, 544, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(500, 545, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(501, 39, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(502, 546, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(503, 547, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(504, 157, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(505, 548, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(506, 549, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(507, 550, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(508, 551, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(509, 552, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(510, 553, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(511, 554, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(512, 555, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(513, 556, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(514, 557, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(515, 558, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(516, 559, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(517, 88, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(518, 60, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(519, 54, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(520, 29, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(521, 560, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(522, 561, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(523, 562, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(524, 563, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(525, 564, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(526, 565, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(527, 566, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(528, 567, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(529, 568, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(530, 569, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(531, 570, '201-files', '201-files', 1, '2026-05-19 17:37:08'),
(532, 571, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(533, 572, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(534, 573, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(535, 574, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(536, 112, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(537, 575, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(538, 36, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(539, 576, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(540, 577, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(541, 578, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(542, 156, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(543, 579, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(544, 580, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(545, 75, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(546, 131, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(547, 581, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(548, 582, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(549, 583, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(550, 584, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(551, 585, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(552, 586, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(553, 587, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(554, 588, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(555, 589, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(556, 590, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(557, 591, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(558, 106, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(559, 592, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(560, 593, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(561, 594, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(562, 595, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(563, 596, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(564, 597, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(565, 598, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(566, 50, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(567, 599, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(568, 600, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(569, 601, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(570, 602, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(571, 603, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(572, 604, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(573, 605, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(574, 606, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(575, 607, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(576, 608, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(577, 42, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(578, 155, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(579, 132, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(580, 677, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(581, 609, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(582, 3, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(583, 64, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(584, 610, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(585, 611, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(586, 612, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(587, 613, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(588, 52, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(589, 53, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(590, 34, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(591, 614, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(592, 615, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(593, 616, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(594, 617, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(595, 618, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(596, 619, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(597, 58, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(598, 620, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(599, 621, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(600, 622, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(601, 623, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(602, 624, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(603, 625, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(604, 626, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(605, 627, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(606, 628, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(607, 629, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(608, 630, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(609, 104, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(610, 74, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(611, 23, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(612, 133, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(613, 631, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(614, 632, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(615, 633, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(616, 634, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(617, 635, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(618, 636, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(619, 28, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(620, 673, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(621, 154, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(622, 20, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(623, 672, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(624, 77, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(625, 637, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(626, 638, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(627, 639, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(628, 640, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(629, 641, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(630, 642, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(631, 643, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(632, 644, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(633, 645, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(634, 646, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(635, 647, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(636, 648, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(637, 649, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(638, 650, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(639, 651, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(640, 25, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(641, 135, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(642, 652, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(643, 653, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(644, 654, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(645, 655, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(646, 656, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(647, 657, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(648, 48, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(649, 658, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(650, 659, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(651, 41, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(652, 660, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(653, 38, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(654, 661, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(655, 662, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(656, 663, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(657, 91, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(658, 664, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(659, 59, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(660, 665, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(661, 666, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(662, 667, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(663, 668, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(664, 669, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(665, 110, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(666, 670, '201-files', '201-files', 1, '2026-05-19 17:37:09'),
(667, 153, '201-files', '201-files', 1, '2026-05-19 17:37:09');

-- --------------------------------------------------------

--
-- Table structure for table `personnel_logs`
--

CREATE TABLE `personnel_logs` (
  `log_id` int(11) NOT NULL,
  `RFTag_id` varchar(55) NOT NULL,
  `img` varchar(255) NOT NULL,
  `captured_img` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mname` varchar(255) NOT NULL,
  `suffix` varchar(5) NOT NULL,
  `do_id` int(12) NOT NULL,
  `shift_id` int(12) NOT NULL,
  `logDate` varchar(15) NOT NULL,
  `logTime` varchar(55) NOT NULL,
  `logTime_sec` int(11) NOT NULL,
  `late_status` varchar(3) NOT NULL,
  `logFlow` varchar(25) NOT NULL,
  `client_ip` varchar(25) NOT NULL,
  `remarks` varchar(55) NOT NULL,
  `travel_leave_code` varchar(55) NOT NULL,
  `ref_log_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `personnel_logs`
--

INSERT INTO `personnel_logs` (`log_id`, `RFTag_id`, `img`, `captured_img`, `lname`, `fname`, `mname`, `suffix`, `do_id`, `shift_id`, `logDate`, `logTime`, `logTime_sec`, `late_status`, `logFlow`, `client_ip`, `remarks`, `travel_leave_code`, `ref_log_id`) VALUES
(21, '712022-1', 'personnelImg/default_img.jpg', '6819c5da3bbc9.jpg', 'RELIQUIAS', 'DAPH ANTHONY', 'VIDAURRAZAGA', '', 2, 3, '05/06/2025', '04:18 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(22, '732023-2', 'personnelImg/default_img.jpg', '6819c5eb3097b.jpg', 'TIANGA', 'SERAFIN', 'ORENDAIN', 'JR. ', 2, 3, '05/06/2025', '04:18 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(23, '1212019-3A', 'personnelImg/default_img.jpg', '6819c6042c296.jpg', 'CASTILLO', 'JOEPET', 'CANILLADA', '', 2, 3, '05/06/2025', '04:19 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(24, '632024-6', 'personnelImg/default_img.jpg', '6819c6282f433.jpg', 'MANGILIMUTAN', 'LORRAINE MAE', 'GESTOSO', '', 2, 3, '05/06/2025', '04:19 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(25, '12212021-7', 'personnelImg/default_img.jpg', '6819c6433436e.jpg', 'PINONGGAN', 'JOSEPH', 'ALBERIO', '', 2, 3, '05/06/2025', '04:20 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(26, '3162023-8', 'personnelImg/default_img.jpg', '6819c84d2c392.jpg', 'GIGANAN', 'GRACE JOY', 'ESTRAO', '', 2, 3, '05/06/2025', '04:29 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(27, '872008-10', 'personnelImg/default_img.jpg', '6819c860317e4.jpg', 'CORTADO', 'ROGELIO', 'ALFANTA', '', 2, 3, '05/06/2025', '04:29 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(28, '3291999-12', 'personnelImg/default_img.jpg', '6819c86c2d1b8.jpg', 'LLAMADO', 'MARISSA', 'MAQUILING', '', 2, 3, '05/06/2025', '04:29 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(29, '3291999-12', 'personnelImg/default_img.jpg', '6819cac029983.jpg', 'LLAMADO', 'MARISSA', 'MAQUILING', '', 2, 3, '05/06/2025', '04:39 PM', 0, 'off', 'PM OUT', '192.168.1.18', '', '', 0),
(30, 'NRFLM0TF', 'personnelImg/default_img.jpg', '682592fa26c24.jpg', 'CABRAS', 'RUBEN', '', '', 26, 3, '05/15/2025', '03:08 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(31, 'NRFP2MRQ', 'personnelImg/default_img.jpg', '6825930d17ded.jpg', 'CABALLERO', 'RODEL', '', '', 26, 3, '05/15/2025', '03:09 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(32, 'NRF1FYQH', 'personnelImg/default_img.jpg', '6825931d13f05.jpg', 'CAHAPAY', 'JENELYN', '', '', 26, 3, '05/15/2025', '03:09 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(33, 'NRF2OSHK', 'personnelImg/default_img.jpg', '682594fe15c0d.jpg', 'CELIZ', 'LUCIANO', '', '', 26, 3, '05/15/2025', '03:17 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(34, 'NRF1O1QH', 'personnelImg/default_img.jpg', '6825950a146fc.jpg', 'CELIS', 'ROBERTO', '', '', 26, 3, '05/15/2025', '03:17 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(35, 'NRFR5GVR', 'personnelImg/default_img.jpg', '6825951317eee.jpg', 'CELESTIAL', 'JESSAJANE', '', '', 26, 3, '05/15/2025', '03:17 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(36, 'NRF42HBZ', 'personnelImg/default_img.jpg', '6825951916c47.jpg', 'CELIZ', 'ROSEPHINE', '', '', 26, 3, '05/15/2025', '03:17 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(37, 'NRFWRDCO', 'personnelImg/default_img.jpg', '6825953413a57.jpg', 'CARA-AT', 'LORNA', '', '', 26, 3, '05/15/2025', '03:18 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(38, 'NRF6WOJI', 'personnelImg/default_img.jpg', '682595681bae8.jpg', 'CHAVEZ', 'WENDYJEAN', '', '', 26, 3, '05/15/2025', '03:19 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(39, 'NRFDAF0D', 'personnelImg/default_img.jpg', '6825957716589.jpg', 'CHAVES', 'RANDY', '', '', 26, 3, '05/15/2025', '03:19 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(40, 'NRF2WLVN', 'personnelImg/default_img.jpg', '6825958e19595.jpg', 'CANUMAY', 'JERSON', '', '', 26, 3, '05/15/2025', '03:19 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(41, 'NRFSZGEM', 'personnelImg/default_img.jpg', '682595a215786.jpg', 'CANOY', 'RODRIGO', '', '', 26, 3, '05/15/2025', '03:20 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(42, 'NRFRZY1D', 'personnelImg/default_img.jpg', '682595b318baa.jpg', 'CANDULIZAS', 'MICHELLEGRACE', '', '', 26, 3, '05/15/2025', '03:20 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(43, 'NRFYN2LI', 'personnelImg/default_img.jpg', '682595bc168fa.jpg', 'CA?O', 'CHRISTIAN', '', '', 26, 3, '05/15/2025', '03:20 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(44, 'NRFM1Z1O', 'personnelImg/default_img.jpg', '682595c8113d9.jpg', 'CA?A', 'JUNRAY', '', '', 26, 3, '05/15/2025', '03:20 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(45, 'NRF6WAT3', 'personnelImg/default_img.jpg', '682595d118324.jpg', 'CARDINAL', 'ARAMAE', '', '', 26, 3, '05/15/2025', '03:20 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(46, 'NRFEY1AO', 'personnelImg/default_img.jpg', '682595db186e0.jpg', 'CAYANG', 'CYRIL', '', '', 26, 3, '05/15/2025', '03:20 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(47, 'NRF4G5PK', 'personnelImg/default_img.jpg', '682595e1192f0.jpg', 'CAUYAN', 'LOUVEANN', '', '', 26, 3, '05/15/2025', '03:21 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(48, 'NRFG5IIZ', 'personnelImg/default_img.jpg', '682595ea17b27.jpg', 'CARIAS', 'EVAMAE', '', '', 26, 3, '05/15/2025', '03:21 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(49, 'NRFMLTMH', 'personnelImg/default_img.jpg', '682595f61b618.jpg', 'CAUYAN', 'JOHNREY', '', '', 26, 3, '05/15/2025', '03:21 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(50, 'NRF3Y3YW', 'personnelImg/default_img.jpg', '682595fc159d4.jpg', 'CAYAO', 'HELEN', '', '', 26, 3, '05/15/2025', '03:21 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(51, 'NRFGFP4C', 'personnelImg/default_img.jpg', '68259732157ee.jpg', 'AWIT', 'ARIEL', '', '', 26, 3, '05/15/2025', '03:26 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(52, 'NRFJUF2O', 'personnelImg/default_img.jpg', '682598301f774.jpg', 'DACALDACAL', 'BEVERLY', '', '', 26, 3, '05/15/2025', '03:30 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(53, 'NRFFDP5Z', 'personnelImg/default_img.jpg', '6825984c137af.jpg', 'DAULONG', 'CHERRYANN', '', '', 26, 3, '05/15/2025', '03:31 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(54, 'NRFZAACM', 'personnelImg/default_img.jpg', '6825985718c35.jpg', 'CORDOVA', 'GLYZEL', '', '', 26, 3, '05/15/2025', '03:31 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(55, 'NRFA6YUR', 'personnelImg/default_img.jpg', '682598821438a.jpg', 'CORDERO', 'JENNIFER', '', '', 26, 3, '05/15/2025', '03:32 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(56, 'NRF0OBEW', 'personnelImg/default_img.jpg', '6825988d13582.jpg', 'DECENA', 'NORA', '', '', 26, 3, '05/15/2025', '03:32 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(57, 'NRFYIZDB', 'personnelImg/default_img.jpg', '68259896137ca.jpg', 'DECENA', 'ARMANDO', '', '', 26, 3, '05/15/2025', '03:32 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(58, 'NRFI1REQ', 'personnelImg/default_img.jpg', '682598ec161bf.jpg', 'CUBID', 'JANEDRIC', '', '', 26, 3, '05/15/2025', '03:34 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(59, 'NRFDJ5GO', 'personnelImg/default_img.jpg', '68259911192fa.jpg', 'CORONEL', 'JOSEPHSMITH', '', '', 26, 3, '05/15/2025', '03:34 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(60, 'NRFCST4M', 'personnelImg/default_img.jpg', '6825991d18c19.jpg', 'DELA CRUZ', 'MARYCHRISTINE', '', '', 26, 3, '05/15/2025', '03:34 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(61, 'NRFLDQR2', 'personnelImg/default_img.jpg', '6825994819992.jpg', 'COMBATE', 'CHARRY', '', '', 26, 3, '05/15/2025', '03:35 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(62, 'NRFVDJ6X', 'personnelImg/default_img.jpg', '68259951163c0.jpg', 'COMPACION', 'REGIE', '', '', 26, 3, '05/15/2025', '03:35 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(63, 'NRFB3AO2', 'personnelImg/default_img.jpg', '6825997e1537a.jpg', 'DELA PE?A', 'JHUMAR', '', '', 26, 3, '05/15/2025', '03:36 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(64, 'NRFTJO2T', 'personnelImg/default_img.jpg', '68259989171dc.jpg', 'DELA CRUZ', 'SHANNYROSE', '', '', 26, 3, '05/15/2025', '03:36 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(65, 'NRFXJIRD', 'personnelImg/default_img.jpg', '68259b871522d.jpg', 'DEQUI?A', 'JERRY', '', '', 26, 3, '05/15/2025', '03:45 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(66, 'NRF5VYSN', 'personnelImg/default_img.jpg', '68259b951e4da.jpg', 'DEMAFELES', 'JULIANA', '', '', 26, 3, '05/15/2025', '03:45 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(67, 'NRFTKHTJ', 'personnelImg/default_img.jpg', '68259ba31777e.jpg', 'DUE?AS', 'JEROLD', '', '', 26, 3, '05/15/2025', '03:45 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(68, 'NRFLEHIX', 'personnelImg/default_img.jpg', '68259bae1755f.jpg', 'ELACO', 'MAE-ANN', '', '', 26, 3, '05/15/2025', '03:45 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(69, 'NRFCGDMY', 'personnelImg/default_img.jpg', '68259bb914db1.jpg', 'ELACO', 'HERNANDO', '', '', 26, 3, '05/15/2025', '03:46 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(70, 'NRFGX5T6', 'personnelImg/default_img.jpg', '68259bc815903.jpg', 'DEPOSITARIO', 'RONALD', '', '', 26, 3, '05/15/2025', '03:46 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(71, 'NRFPOCEB', 'personnelImg/default_img.jpg', '68259bda14f91.jpg', 'DIANOY', 'EMILY', '', '', 26, 3, '05/15/2025', '03:46 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(72, 'NRFRK04O', 'personnelImg/default_img.jpg', '68259bea1c559.jpg', 'DELGADO', 'JOJO', '', '', 26, 3, '05/15/2025', '03:46 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(73, 'NRFJM4IU', 'personnelImg/default_img.jpg', '68259c371c9fa.jpg', 'DIAZ', 'JOHNRICH', '', '', 26, 3, '05/15/2025', '03:48 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(74, 'NRFX4JYO', 'personnelImg/default_img.jpg', '68259c4113ebb.jpg', 'DIAZ', 'JENNERIE', '', '', 26, 3, '05/15/2025', '03:48 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(75, 'NRF3TUJX', 'personnelImg/default_img.jpg', '68259c5c151c0.jpg', 'ELACO', 'RONEL', '', '', 26, 3, '05/15/2025', '03:48 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(76, 'NRF452AJ', 'personnelImg/default_img.jpg', '68259c7d17cef.jpg', 'DEMERIN', 'ROWENA', '', '', 26, 3, '05/15/2025', '03:49 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(77, 'NRFN4O60', 'personnelImg/default_img.jpg', '68259c9216aa0.jpg', 'ENCOY', 'RACKLY', '', '', 26, 3, '05/15/2025', '03:49 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(78, 'NRFUUELF', 'personnelImg/default_img.jpg', '68259c9e1ee17.jpg', 'ENGCOY', 'JOHNOLIVER', '', '', 26, 3, '05/15/2025', '03:49 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(79, 'NRFXXKIQ', 'personnelImg/default_img.jpg', '68259cb018f0d.jpg', 'EMIT', 'HAZEL', '', '', 26, 3, '05/15/2025', '03:50 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(80, 'NRFSSALK', 'personnelImg/default_img.jpg', '68259cde175a6.jpg', 'ENGCOY', 'JOHNNOEL', '', '', 26, 3, '05/15/2025', '03:50 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(81, 'NRFX3JKU', 'personnelImg/default_img.jpg', '6825a0971b2d6.jpg', 'EMIT', 'JOEMAR', '', '', 26, 3, '05/15/2025', '04:06 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(82, 'NRFIZP4V', 'personnelImg/default_img.jpg', '6825a0db2442c.jpg', 'DELA PE?A', 'MAFIRUVIE', '', '', 26, 3, '05/15/2025', '04:07 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(83, 'NRFLASI2', 'personnelImg/default_img.jpg', '6825a11117a39.jpg', 'DA-AN', 'LOIDA', '', '', 26, 3, '05/15/2025', '04:08 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(84, '122024-32', 'personnelImg/default_img.jpg', '6825a15a15d30.jpg', 'BILBAO', 'MA. TERESA', 'LOCSIN', '', 24, 3, '05/15/2025', '04:10 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(85, '412024-36', 'personnelImg/default_img.jpg', '6825a1a91866e.jpg', 'SANTES', 'JOCELYN', 'CORONEL', '', 24, 3, '05/15/2025', '04:11 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(86, '531993-49', 'personnelImg/default_img.jpg', '6825a1df1a919.jpg', 'VILLANUEVA', 'RAYGILDA', 'VERGARA', '', 12, 3, '05/15/2025', '04:12 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(87, '732023-2', 'personnelImg/default_img.jpg', '6825a23f152e6.jpg', 'TIANGA', 'SERAFIN', 'ORENDAIN', 'JR. ', 2, 3, '05/15/2025', '04:13 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(88, '711991-47', 'personnelImg/default_img.jpg', '6825a28b169ef.jpg', 'BONGCAWEL', 'GENELYN ', 'HISONA', '', 12, 3, '05/15/2025', '04:15 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(89, 'NRFUUELF', 'personnelImg/default_img.jpg', '6825ae8748153.jpg', 'ENGCOY', 'JOHNOLIVER', '', '', 26, 3, '05/15/2025', '05:06 PM', 0, 'off', 'PM OUT', '192.168.1.10', '', '', 0),
(90, '732023-12', 'personnelImg/default_img.jpg', '682d8252a5678.jpg', 'LOGRONIO', 'JOESIFIL', 'FAJARDO', '', 2, 3, '05/21/2025', '03:35 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(91, '1012011-167', 'personnelImg/default_img.jpg', '682d827c92300.jpg', 'GERMINAL', 'CRISTUTO', 'M.', '', 17, 3, '05/21/2025', '03:36 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(92, '10242022-123', 'personnelImg/default_img.jpg', '682d839a91e3a.jpg', 'RELADO', 'MEDALIA', 'VALENZUELA', '', 8, 3, '05/21/2025', '03:41 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(93, '712022-1', 'personnelImg/default_img.jpg', '682d83cd92d17.jpg', 'RELIQUIAS', 'DAPH ANTHONY', 'VIDAURRAZAGA', '', 2, 3, '05/21/2025', '03:42 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(94, '8162010-150', 'personnelImg/default_img.jpg', '682d83ea9415b.jpg', 'NAVA', 'LUZ SALOME', 'VILLA', '', 14, 3, '05/21/2025', '03:42 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(95, '622003-154', 'personnelImg/default_img.jpg', '682d843693ef1.jpg', 'LOREDO', 'MARIA ELENA', 'DELLOSO', '', 14, 3, '05/21/2025', '03:43 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(96, '812017-148', 'personnelImg/default_img.jpg', '682d847095c68.jpg', 'MARQUEZ', 'MAE ANN', 'GIGANAN', '', 15, 3, '05/21/2025', '03:44 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(97, '3162023-111', 'personnelImg/default_img.jpg', '682d84a6927ad.jpg', 'GUINTOS', 'WINSTON', 'NAVA', '', 3, 3, '05/21/2025', '03:45 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(98, '10172011-103', 'personnelImg/default_img.jpg', '682d84da912dc.jpg', 'BAYDO', 'JULITO', 'CAYAS', '', 3, 3, '05/21/2025', '03:46 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(99, '311994-60', 'personnelImg/default_img.jpg', '682d851b9588f.jpg', 'GALLARDA', 'ELNOR', 'PACLAONA', '', 12, 3, '05/21/2025', '03:47 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(100, '10162024-165', 'personnelImg/nrfc1ohg-jet.jpg', '682d855f960ff.jpg', 'BARROCA', 'JETTER', 'SENIO', '', 14, 3, '05/21/2025', '03:48 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(101, '10162024-134', 'personnelImg/default_img.jpg', '682d85b697d09.jpg', 'NICOR', 'MICHAEL', 'RAMOS', '', 7, 3, '05/21/2025', '03:50 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(102, '10162017-151', 'personnelImg/default_img.jpg', '682d85eb90d1e.jpg', 'ARBOIZ', 'JOHN', 'VILLARICO', '', 15, 3, '05/21/2025', '03:51 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(103, '10162024-96', 'personnelImg/default_img.jpg', '682d861e948c2.jpg', 'AKOL', 'SHARRA MAE', 'ARTICA', '', 10, 3, '05/21/2025', '03:51 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(104, '10162024-162', 'personnelImg/default_img.jpg', '682d865590f1b.jpg', 'GUINTOS', 'ERICA', 'MANANGAN', '', 14, 3, '05/21/2025', '03:52 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(105, '531993-51', 'personnelImg/default_img.jpg', '682d869d9252d.jpg', 'ANTIQUEÑO', 'ANA MARIE', 'SANOY', '', 12, 3, '05/21/2025', '03:54 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(106, 'nrf1210j', 'personnelImg/default_img.jpg', '682d872494342.jpg', 'BALBUENA', 'JOEMAR', '', '', 26, 3, '05/21/2025', '03:56 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(109, 'M44PLOGP0U', 'personnelImg/default_img.jpg', '6847d0d0a4b23.jpg', 'CASTILLO', 'JOEPET', 'CANILLADA', '', 2, 3, '06/10/2025', '02:29 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(110, 'UGYT3P0WEX', 'personnelImg/default_img.jpg', '6847d0eea2646.jpg', 'TIANGA', 'SERAFIN', 'ORENDAIN', 'JR. ', 2, 3, '06/10/2025', '02:30 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(111, 'RK02GLHBHR', 'personnelImg/default_img.jpg', '6847d111a4283.jpg', 'PINONGGAN', 'JOSEPH', 'ALBERIO', '', 2, 3, '06/10/2025', '02:30 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(112, '0LKJLM2C22', 'personnelImg/default_img.jpg', '6847d14da122b.jpg', 'MANGILIMUTAN', 'LORRAINE MAE', 'GESTOSO', '', 2, 3, '06/10/2025', '02:31 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(113, 'IO1IY5L4VB', 'personnelImg/default_img.jpg', '6847d162a9430.jpg', 'CORTADO', 'ROGELIO', 'ALFANTA', '', 2, 3, '06/10/2025', '02:32 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(114, 'D4ZWVFKO1E', 'personnelImg/default_img.jpg', '6847d197a0230.jpg', 'LLAMADO', 'MARISSA', 'MAQUILING', '', 2, 3, '06/10/2025', '02:32 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(115, 'UV0LOFMFS3', 'personnelImg/default_img.jpg', '6847d1a7a07a0.jpg', 'LOGRONIO', 'JOESIFIL', 'FAJARDO', '', 2, 3, '06/10/2025', '02:33 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(116, 'N2CS52WG6G', 'personnelImg/default_img.jpg', '6847d1bba2d1e.jpg', 'GIGANAN', 'GRACE JOY', 'ESTRAO', '', 2, 3, '06/10/2025', '02:33 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(117, '55SDU3PJSI', 'personnelImg/default_img.jpg', '6847d3c59fd6a.jpg', 'ENCOY', 'JEFRE', 'LAZALITA', '', 24, 3, '06/10/2025', '02:42 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(118, '1Z6LX20PVU', 'personnelImg/default_img.jpg', '6847d3e5a1b46.jpg', 'RELIQUIAS', 'JOHN MARK', 'BALUNGCAS', '', 23, 3, '06/10/2025', '02:42 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(119, 'KE4HXNTQTI', 'personnelImg/default_img.jpg', '6847d3f9a2970.jpg', 'BILBAO', 'FRANCISCO JOSE', 'LOCSIN', 'JR. ', 24, 3, '06/10/2025', '02:43 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(120, 'SPRSLW2YQM', 'personnelImg/default_img.jpg', '6847d417a574e.jpg', 'OCTAVIO', 'JODYBONNE', 'GAYATIN', '', 24, 3, '06/10/2025', '02:43 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(121, 'E6FJPBZ5BD', 'personnelImg/default_img.jpg', '6847d423a1bb4.jpg', 'TUPAS', 'JASON', 'TEMBREVILLA', '', 24, 3, '06/10/2025', '02:43 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(122, 'WXV3CGLSBO', 'personnelImg/default_img.jpg', '6847d42aa31bd.jpg', 'GAYOMALE', 'JOSE ROBERT', 'MILLAN', 'JR. ', 24, 3, '06/10/2025', '02:43 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(123, 'WEYJU63LLE', 'personnelImg/default_img.jpg', '6847d43eac461.jpg', 'BILBAO', 'MA. TERESA', 'LOCSIN', '', 24, 3, '06/10/2025', '02:44 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(124, 'KCTG5YC64S', 'personnelImg/default_img.jpg', '6847d4489f2ac.jpg', 'TUBILLEJA', 'THEODORE', 'DELA CRUZ', 'SR. ', 24, 3, '06/10/2025', '02:44 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(125, 'BCUT6421HG', 'personnelImg/default_img.jpg', '6847d497a119c.jpg', 'CANDULIZAS', 'MOLAVE', 'TEMBREVILLA', '', 2, 3, '06/10/2025', '02:45 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(126, 'T1X1EOAO50', 'personnelImg/default_img.jpg', '6847d4efa125f.jpg', 'HERRADURA', 'GINALYN ', 'OBENZA', '', 2, 3, '06/10/2025', '02:47 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(127, 'BSY3X4CY3E', 'personnelImg/default_img.jpg', '6847d5eaa0d7c.jpg', 'SANTES', 'JOCELYN', 'CORONEL', '', 24, 3, '06/10/2025', '02:51 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(128, '6DZNSFDRJG', 'personnelImg/default_img.jpg', '6847d60ca20fa.jpg', 'MANOS', 'TEOFILO', 'ENCARGUEZ', 'JR. ', 24, 3, '06/10/2025', '02:51 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(129, '44SB4W0MF3', 'personnelImg/default_img.jpg', '6847d626a1cba.jpg', 'VIDAURRAZAGA', 'MC', 'PEPITO', '', 24, 3, '06/10/2025', '02:52 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(130, 'NT36Z4UITB', 'personnelImg/default_img.jpg', '6847d636a32ad.jpg', 'MALAYO', 'EDGARDO', 'FLORES', '', 24, 3, '06/10/2025', '02:52 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(131, 'ZGMTZ2Y3BT', 'personnelImg/default_img.jpg', '6847d648a3ac2.jpg', 'OCTAVIO', 'PETER JOHN ', 'LAZALITA', '', 24, 3, '06/10/2025', '02:52 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(132, 'DCORCD1ICP', 'personnelImg/default_img.jpg', '6847d656a21f3.jpg', 'TUMA-OB', 'LESLIE AIKEE DYAN', 'GIGANAN', '', 24, 3, '06/10/2025', '02:53 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(133, 'WQ4X6QJYGH', 'personnelImg/default_img.jpg', '6847d668a1e06.jpg', 'ANLIQUERA', 'SIENA', 'VASQUEZ', '', 26, 3, '06/10/2025', '02:53 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(134, '1QETKAFQWO', 'personnelImg/default_img.jpg', '6847d67ca27d0.jpg', 'DELOTINA', 'JOFEL', 'EVANGELISTA', '', 24, 3, '06/10/2025', '02:53 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(135, '2FUFQBHUQM', 'personnelImg/default_img.jpg', '6847d68d9f5c8.jpg', 'DECENA', 'LANNE', 'VILLARETE', '', 24, 3, '06/10/2025', '02:54 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(136, 'HSDQZ6G6PA', 'personnelImg/default_img.jpg', '6847d6a3a1a10.jpg', 'PEREZ', 'JOSE MARIA', 'LIM', 'JR. ', 12, 3, '06/10/2025', '02:54 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(137, 'DFR5QGBYHW', 'personnelImg/default_img.jpg', '6847d859a7404.jpg', 'BARO', 'PHOEBE', 'MANILINGAN', '', 12, 3, '06/10/2025', '03:01 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(138, 'LVX5C46TAS', 'personnelImg/default_img.jpg', '6847d86da4df4.jpg', 'SILVESTRE', 'CYNTHIA', 'LAMBOT', '', 12, 3, '06/10/2025', '03:02 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(139, 'I03I6VZELI', 'personnelImg/default_img.jpg', '6847d878a586f.jpg', 'GALAN', 'MARY JANE', 'CELIS', '', 12, 3, '06/10/2025', '03:02 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(140, 'QESM444ZK0', 'personnelImg/default_img.jpg', '6847d883a4bbd.jpg', 'TEMBREVILLA', 'RONA', 'ESCOSAR', '', 12, 3, '06/10/2025', '03:02 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(141, 'V2M0IIAYRM', 'personnelImg/default_img.jpg', '6847d8a2a316b.jpg', 'VILLANUEVA', 'RAYGILDA', 'VERGARA', '', 12, 3, '06/10/2025', '03:02 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(142, 'ZV1EXSSS30', 'personnelImg/default_img.jpg', '6847d8b3a24fc.jpg', 'BONGCAWEL', 'GENELYN ', 'HISONA', '', 12, 3, '06/10/2025', '03:03 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(143, 'HDMCPVKYB1', 'personnelImg/default_img.jpg', '6847d8beaadb6.jpg', 'ROXAS', 'MARY ANN', 'VILLAFUERTE', '', 12, 3, '06/10/2025', '03:03 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(144, 'MS5LRO1IJE', 'personnelImg/default_img.jpg', '6847d8c9a7e39.jpg', 'VILLANUEVA', 'ROSALIE', 'ELARDO', '', 12, 3, '06/10/2025', '03:03 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(145, '5UVF66ZPY0', 'personnelImg/default_img.jpg', '6847d9fca67ed.jpg', 'MAQUILING', 'WARREN', 'RAMIREZ', '', 12, 3, '06/10/2025', '03:08 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(146, 'RCCO5TPACC', 'personnelImg/default_img.jpg', '6847da27a4538.jpg', 'PIMENTEL', 'EMY', 'MAGBANUA', '', 12, 3, '06/10/2025', '03:09 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(147, 'HEPAB6X3PL', 'personnelImg/default_img.jpg', '6847da429f708.jpg', 'VILLAMATER', 'LORALYN', 'BARROCA', '', 12, 3, '06/10/2025', '03:09 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(148, '2O1JSGHAYX', 'personnelImg/default_img.jpg', '6847da4ba2f1f.jpg', 'GAUAL', 'ROLIN', 'BERGONIO', 'SR. ', 12, 3, '06/10/2025', '03:10 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(149, 'I0FY3RFXM5', 'personnelImg/default_img.jpg', '6847da91a155e.jpg', 'TILOS', 'OSCAR', 'VILLARETE', '', 4, 3, '06/10/2025', '03:11 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(150, 'MHDZI160KN', 'personnelImg/default_img.jpg', '6847da9ca1eae.jpg', 'TEMBREVILLA', 'CAROLINE', 'CANILLO', '', 12, 3, '06/10/2025', '03:11 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(151, 'ZHXY3CO3KQ', 'personnelImg/default_img.jpg', '6847dab5a2c67.jpg', 'DELA FUENTE', 'EBER', 'ORMEO', '', 12, 3, '06/10/2025', '03:11 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(152, 'WSJIHMXEN6', 'personnelImg/default_img.jpg', '6847dabea0b07.jpg', 'TILOS', 'MARCELINA', 'CELIS', '', 12, 3, '06/10/2025', '03:11 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(153, 'GZ5LO0QDRC', 'personnelImg/default_img.jpg', '6847dacea457e.jpg', 'GARCIA', 'ADELA', 'ANTOLIN', '', 12, 3, '06/10/2025', '03:12 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(154, 'KZFCP2S0HT', 'personnelImg/default_img.jpg', '6847dbfc9f29c.jpg', 'GUINTOS', 'JOSEPHINE', 'INDINO', '', 10, 3, '06/10/2025', '03:17 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(155, 'NZ0IKZI1IR', 'personnelImg/default_img.jpg', '6847dc079e493.jpg', 'MANGOGTONG', 'MA. MARILOU ', 'ESTRELLA', '', 10, 3, '06/10/2025', '03:17 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(156, 'MF4R0LP0PT', 'personnelImg/default_img.jpg', '6847dc10a123b.jpg', 'TOLEDO', 'RAYMUND ANTHONY', 'INOLINO', '', 4, 3, '06/10/2025', '03:17 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(157, 'DO2X14YKJR', 'personnelImg/default_img.jpg', '6847dc17a03a3.jpg', 'VILLARUBIA', 'ALTHEA', 'PIA', '', 5, 3, '06/10/2025', '03:17 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(158, 'APPZCXZCJS', 'personnelImg/default_img.jpg', '6847dc1ea32e0.jpg', 'MIRANDA', 'JAIME', 'MAULIT', '', 5, 3, '06/10/2025', '03:17 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(159, 'N3CFWPHFQX', 'personnelImg/default_img.jpg', '6847dc26a2dad.jpg', 'SANTES', 'CAROL ANN', 'TILOS', '', 5, 3, '06/10/2025', '03:17 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(160, '0RVK3VBCZ1', 'personnelImg/default_img.jpg', '6847dc2da5ae0.jpg', 'MANOS', 'ANNABELLE', 'PERIGUA', '', 5, 3, '06/10/2025', '03:18 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(161, 'LTQHD2BPYJ', 'personnelImg/default_img.jpg', '6847dc369f8f3.jpg', 'GALON', 'RANDOLF', 'BLANCO', '', 4, 3, '06/10/2025', '03:18 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(162, 'VUM5AWYEUH', 'personnelImg/default_img.jpg', '6847dd0fa09ff.jpg', 'SUMUGAT', 'JENELYN', 'TELONIO', '', 3, 3, '06/10/2025', '03:21 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(163, '2KLYB0WHDY', 'personnelImg/default_img.jpg', '6847dd27a51e7.jpg', 'GESTOSO', 'CARLITO', 'BAVIERA', '', 10, 3, '06/10/2025', '03:22 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(164, 'ES1MY5CB5N', 'personnelImg/default_img.jpg', '6847dd2ea3daf.jpg', 'BUDACA', 'REVINIA', 'AMACIO', '', 10, 3, '06/10/2025', '03:22 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(165, 'KVKMFQDZDV', 'personnelImg/default_img.jpg', '6847dd33a3905.jpg', 'GA-AN', 'LENLY', 'NOSAL', '', 10, 3, '06/10/2025', '03:22 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(166, 'HR1TD1EOO6', 'personnelImg/default_img.jpg', '6847dd3d9ed4d.jpg', 'NATALIO', 'JOEFREY', 'TEMBREVILLA', '', 9, 3, '06/10/2025', '03:22 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(167, 'N0PELGBXHV', 'personnelImg/default_img.jpg', '6847dd45a16f0.jpg', 'GUSTILO', 'LEILANI', 'DECENA', '', 9, 3, '06/10/2025', '03:22 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(168, 'OSYDS2VNEW', 'personnelImg/default_img.jpg', '6847dd55a1f83.jpg', 'ABALLE', 'BETHEL', 'PERFUMA', '', 10, 3, '06/10/2025', '03:23 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(169, '2XYVUZG44K', 'personnelImg/default_img.jpg', '6847dd5da0959.jpg', 'RELIQUIAS', 'JOERIBEL', 'TORIANO', '', 9, 3, '06/10/2025', '03:23 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(170, 'C4XW3NQ3CE', 'personnelImg/default_img.jpg', '6847dd64a1b0d.jpg', 'ACADEMIA', 'RIZALYN', 'CAYANAN', '', 9, 3, '06/10/2025', '03:23 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(171, 'KDV12JKLNR', 'personnelImg/default_img.jpg', '6847dd72a1f6d.jpg', 'TRINIO-SANTES', 'VANESSA', 'LIRAZAN', '', 3, 3, '06/10/2025', '03:23 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(172, 'K0GBVLRIOM', 'personnelImg/default_img.jpg', '6847de779e720.jpg', 'JORDAN', 'MARK ANTHONY', 'TOMADO', '', 3, 3, '06/10/2025', '03:27 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(173, 'DURSCXCSIM', 'personnelImg/default_img.jpg', '6847de80a23e1.jpg', 'MAYANDIA', 'ANALIE', 'ELARMO', '', 6, 3, '06/10/2025', '03:27 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(174, 'EAUNV5WVM0', 'personnelImg/default_img.jpg', '6847de8b9fd7c.jpg', 'BARROCA', 'JORGIE', 'PERFUMA', '', 6, 3, '06/10/2025', '03:28 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(175, 'UBFPHM020G', 'personnelImg/default_img.jpg', '6847de95a0dea.jpg', 'BONILLA', 'ANNI VER', 'PAMLIEGA', '', 6, 3, '06/10/2025', '03:28 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(176, 'B42WBNMNSK', 'personnelImg/default_img.jpg', '6847dea49f465.jpg', 'LIMSIACO', 'ARIANE', 'CONSTANTINO', '', 6, 3, '06/10/2025', '03:28 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(177, 'EHEYDZ1UYN', 'personnelImg/default_img.jpg', '6847deaba3fe4.jpg', 'ALATON', 'GINA', 'NABOR', '', 6, 3, '06/10/2025', '03:28 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(178, 'DGPF5FDK53', 'personnelImg/default_img.jpg', '6847deb0a02e1.jpg', 'ARANETA', 'JOSELITA', 'GENTELIZO', '', 6, 3, '06/10/2025', '03:28 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(179, 'N4DZR4BBY0', 'personnelImg/default_img.jpg', '6847deb5a1e21.jpg', 'RELIQUIAS', 'MARIA FE', 'GUINTOS', '', 8, 3, '06/10/2025', '03:28 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(180, 'CMDUXPZ44I', 'personnelImg/default_img.jpg', '6847debaa0680.jpg', 'GONZAL', 'SANDRA', 'CASTILLO', '', 8, 3, '06/10/2025', '03:28 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(181, 'V4PAJMJ5YO', 'personnelImg/default_img.jpg', '6847dec1a014f.jpg', 'TUPAS', 'OFELIA', 'TEMBREVILLA', '', 6, 3, '06/10/2025', '03:29 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(182, 'KP6G24TB40', 'personnelImg/default_img.jpg', '6847df8ba20b3.jpg', 'RELADO', 'MEDALIA', 'VALENZUELA', '', 8, 3, '06/10/2025', '03:32 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(183, 'PWU0GP4SHN', 'personnelImg/default_img.jpg', '6847df9ca3ef0.jpg', 'SANTES', 'AZELA', 'FLORES', '', 7, 3, '06/10/2025', '03:32 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(184, '2SJBOJTXPC', 'personnelImg/default_img.jpg', '6847dfa4a2146.jpg', 'GELLECANAO', 'MURIELLE', 'LIRAZAN', '', 7, 3, '06/10/2025', '03:32 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(185, 'FKX31XYW34', 'personnelImg/default_img.jpg', '6847dfaba0d68.jpg', 'DELA FUENTE', 'ALVIN', 'ORMEO', '', 11, 3, '06/10/2025', '03:32 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(186, 'ZJPTEJP5UQ', 'personnelImg/default_img.jpg', '6847dfb1a067f.jpg', 'AMANTE', 'LEONIZA', 'FLORES', '', 11, 3, '06/10/2025', '03:33 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(187, '1DSSFVBIFF', 'personnelImg/default_img.jpg', '6847e00a9ee57.jpg', 'MAESTRECAMPO', 'EVELYN', 'ELARMO', '', 7, 3, '06/10/2025', '03:34 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(188, 'Q0QB3ZVYNK', 'personnelImg/default_img.jpg', '6847e0109ff92.jpg', 'VILLARETE', 'ANGELA', 'TELONIO', '', 7, 3, '06/10/2025', '03:34 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(189, '2M5F54VPSN', 'personnelImg/default_img.jpg', '6847e01d9f96e.jpg', 'AKOL', 'MARLOU', 'ARTICA', '', 14, 3, '06/10/2025', '03:34 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(190, 'EKSUEACJM5', 'personnelImg/default_img.jpg', '6847e0e69f247.jpg', 'ACIBIDO', 'CHE GENEROSO', 'PARO', '', 14, 3, '06/10/2025', '03:38 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(191, 'PLCEEHAUGT', 'personnelImg/default_img.jpg', '6847e0ef9fd6d.jpg', 'MARQUEZ', 'MAE ANN', 'GIGANAN', '', 15, 3, '06/10/2025', '03:38 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(192, 'W2DGEUTP62', 'personnelImg/default_img.jpg', '6847e0f3a1954.jpg', 'LOREDO', 'MARIA ELENA', 'DELLOSO', '', 14, 3, '06/10/2025', '03:38 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(193, 'M1Y5ETAYD6', 'personnelImg/default_img.jpg', '6847e10b9ffe7.jpg', 'NAVA', 'LUZ SALOME', 'VILLA', '', 14, 3, '06/10/2025', '03:38 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(194, 'GSQZ2OCVHQ', 'personnelImg/default_img.jpg', '6847e142a1c28.jpg', 'RELIQUIAS', 'GERALD', 'TORIANO', '', 14, 3, '06/10/2025', '03:39 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(195, 'J3UWH2Z32E', 'personnelImg/default_img.jpg', '6847e163a1f52.jpg', 'TRINIDAD', 'RODOLFO', 'PULGAN', 'JR. ', 15, 3, '06/10/2025', '03:40 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(196, 'O6JWSQ5BTR', 'personnelImg/default_img.jpg', '6847e16ba2e17.jpg', 'DELA CONCEPTION', 'IMELDA', 'ESPENORIO', '', 14, 3, '06/10/2025', '03:40 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(197, '4RE6HDPT3R', 'personnelImg/default_img.jpg', '6847e179a2880.jpg', 'JIMENEZ', 'LEO', 'TOMILBA', '', 14, 3, '06/10/2025', '03:40 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(198, 'GRN63LXTVC', 'personnelImg/default_img.jpg', '6847e17ea1923.jpg', 'GIGANAN', 'AGNES', 'NAVA', '', 15, 3, '06/10/2025', '03:40 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(199, '6KR3ZM4BU3', 'personnelImg/default_img.jpg', '6847e288a1450.jpg', 'TELONIO', 'JAMES ANDREW', 'GUINTOS', '', 15, 3, '06/10/2025', '03:45 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(200, 'GOYLQLNANM', 'personnelImg/default_img.jpg', '6847e293a18d0.jpg', 'DELOTINA', 'DANILO', 'NAPIERE', '', 17, 3, '06/10/2025', '03:45 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(201, 'F3DUWOJ4SZ', 'personnelImg/default_img.jpg', '6847e29fa22ce.jpg', 'SIASON', 'CHEZAH ERL', 'GAUAL', '', 16, 3, '06/10/2025', '03:45 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(202, 'R52SQSFJB4', 'personnelImg/default_img.jpg', '6847e2a4a23b9.jpg', 'BONILLA', 'JURY', 'BENLOT', '', 17, 3, '06/10/2025', '03:45 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(203, 'T4BXLLYWTG', 'personnelImg/default_img.jpg', '6847e2a7a23c1.jpg', 'DERIT', 'JOSE ALAN ', 'DURO', '', 17, 3, '06/10/2025', '03:45 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(204, 'KUNUEE1ZKH', 'personnelImg/default_img.jpg', '6847e2aaa2886.jpg', 'YUSAY', 'JOSE', 'TOGLE', 'III ', 16, 3, '06/10/2025', '03:45 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(205, 'POT5DLCLXZ', 'personnelImg/default_img.jpg', '6847e2ada3867.jpg', 'GUINTOS', 'TINA MARIE', 'LUGA', '', 16, 3, '06/10/2025', '03:45 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(206, 'MMB6JKH1T3', 'personnelImg/default_img.jpg', '6847e2b0a14ad.jpg', 'CAMBARIJAN', 'JIMMY', 'ZETA', '', 25, 3, '06/10/2025', '03:45 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(207, 'REPYPK1N0K', 'personnelImg/default_img.jpg', '6847e2b49ed5a.jpg', 'AMBAGAN', 'EDSEL', 'MILLENDEZ', '', 16, 3, '06/10/2025', '03:45 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(208, '56EZ3T4YP2', 'personnelImg/default_img.jpg', '6847e2b8a3d70.jpg', 'CALUMBA', 'ANALYN', 'MANILINGAN', '', 25, 3, '06/10/2025', '03:45 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(209, '6D0YWDKLPP', 'personnelImg/default_img.jpg', '6847e3fda46cb.jpg', 'AGUHAYON', 'RICARDO', 'UBAMOS', '', 14, 3, '06/10/2025', '03:51 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(210, 'R0ZJXTVRR4', 'personnelImg/default_img.jpg', '6847e413a08a4.jpg', 'AVELINO', 'JULIEBERT', 'AZUCENA', '', 2, 3, '06/10/2025', '03:51 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(211, 'LS5IJYTERX', 'personnelImg/default_img.jpg', '6847e425a5a91.jpg', 'GUINTOS', 'WINSTON', 'NAVA', '', 3, 3, '06/10/2025', '03:52 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(212, 'EEG0NY2B5U', 'personnelImg/default_img.jpg', '6847e42ba0058.jpg', 'ARBOIZ', 'JOHN', 'VILLARICO', '', 15, 3, '06/10/2025', '03:52 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(213, 'UUR4C35PLC', 'personnelImg/default_img.jpg', '6847e431a159c.jpg', 'ALIMANE', 'JINKY', 'GALPO', '', 7, 3, '06/10/2025', '03:52 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(214, 'YCSOHXN5TA', 'personnelImg/default_img.jpg', '6847e438a2b39.jpg', 'GALLARDA', 'ELNOR', 'PACLAONA', '', 12, 3, '06/10/2025', '03:52 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(215, 'JT6NRMM2MG', 'personnelImg/default_img.jpg', '6847e43ea2c61.jpg', 'LABRADOR', 'REY', 'TRIBUCIO', '', 10, 3, '06/10/2025', '03:52 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(216, 'OINBKY4BGA', 'personnelImg/default_img.jpg', '6847e444a16c0.jpg', 'DECATORIA', 'JOEBERT', 'SARIL', '', 3, 3, '06/10/2025', '03:52 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(217, 'Z2TDUSRUV6', 'personnelImg/default_img.jpg', '6847e44ea105a.jpg', 'BIACA', 'HERBERT', 'BORNALES', '', 3, 3, '06/10/2025', '03:52 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(218, 'CCEWWUW3P2', 'personnelImg/default_img.jpg', '6847e520a295f.jpg', 'TUBILLEJA', 'THEODORE', 'SIGUEZA', 'JR. ', 6, 3, '06/10/2025', '03:56 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(219, 'OIMV5KOLF2', 'personnelImg/default_img.jpg', '6847e52aa2cda.jpg', 'PEROSIA', 'GLORY MAE', 'TUNDA-AN', '', 12, 3, '06/10/2025', '03:56 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(220, 'P-ZGT-382010-110', 'personnelImg/default_img.jpg', '6847e5399f7c2.jpg', 'TEMBREVILLA', 'ZOSIMO', 'GAVILAGA', '', 3, 3, '06/10/2025', '03:56 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(221, '65VQESUT03', 'personnelImg/default_img.jpg', '6847e54da1817.jpg', 'PANGANTIHON', 'JOHN IRVING', 'TOLEDO', '', 3, 3, '06/10/2025', '03:57 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(222, 'SP2WX6WZFC', 'personnelImg/default_img.jpg', '6847e551a0ff7.jpg', 'PADA', 'VERONICA EVELYN', 'ALBISO', '', 12, 3, '06/10/2025', '03:57 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(223, 'PQMSBLBNJ6', 'personnelImg/default_img.jpg', '6847e57fa2b71.jpg', 'VIDAURRAZAGA', 'NOAH', 'VASQUEZ', '', 2, 3, '06/10/2025', '03:57 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(224, 'PMOLWNYZZA', 'personnelImg/default_img.jpg', '6847e587a2137.jpg', 'SUSANA', 'FREDERICK DAVY', 'PINGCALE', '', 12, 3, '06/10/2025', '03:57 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(225, 'F255HSX3FM', 'personnelImg/default_img.jpg', '6847e58ea2eb5.jpg', 'GUINTOS', 'ERICA', 'MANANGAN', '', 14, 3, '06/10/2025', '03:58 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(226, 'E0VZSU0W6G', 'personnelImg/default_img.jpg', '6847e599a1ecc.jpg', 'APLAON', 'MA. LEE', 'MAESTRECAMPO', '', 23, 3, '06/10/2025', '03:58 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(227, 'JETERWW23U', 'personnelImg/nrf5ihz6-mae-flor.jpg', '6847e63ba0ddf.jpg', 'BARRIOS', 'MAE FLOR', 'ALMAIZ', '', 8, 3, '06/10/2025', '04:00 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(228, 'YFUCRYEDIV', 'personnelImg/nrfc1ohg-jet.jpg', '6847e67ca2f4f.jpg', 'BARROCA', 'JETTER', 'SENIO', '', 14, 3, '06/10/2025', '04:02 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(229, '111LXWXFLY', 'personnelImg/default_img.jpg', '6847e687a3506.jpg', 'NICOR', 'MICHAEL', 'RAMOS', '', 7, 3, '06/10/2025', '04:02 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(230, 'SARR3EM6ZV', 'personnelImg/default_img.jpg', '6847e69fa4976.jpg', 'PACURIB', 'JULITO', 'PLAÑA', 'JR. ', 3, 3, '06/10/2025', '04:02 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(231, 'GJCC6XY3BR', 'personnelImg/default_img.jpg', '6847e6aca282f.jpg', 'ANTIQUEÑO', 'ANA MARIE', 'SANOY', '', 12, 3, '06/10/2025', '04:02 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(232, 'E6LFKFXAD0', 'personnelImg/default_img.jpg', '6847e6b2a4ebe.jpg', 'NUÑESCO', 'AIZA', 'ANDO', '', 5, 3, '06/10/2025', '04:02 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(233, 'XD5JB3HCC0', 'personnelImg/default_img.jpg', '6847e6b89f7f9.jpg', 'LAREÑO', 'LIWAYA', 'MAHINAY', '', 11, 3, '06/10/2025', '04:03 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(234, 'WILZVWBBRI', 'personnelImg/default_img.jpg', '6847e6bea0dfd.jpg', 'OCCEÑA', 'GEM', 'RELAMPAGOS', '', 3, 3, '06/10/2025', '04:03 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(235, 'SJLE6GGLD6', 'personnelImg/nrf04z55-che.jpg', '6847e6c9a6366.jpg', 'JUAREZ', 'CHERRY LYNN', 'MONTAÑO', '', 2, 3, '06/10/2025', '04:03 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(236, 'HOA5M4V2KU', 'personnelImg/default_img.jpg', '6848d18590a93.jpg', 'TEMBREVILLA', 'THOMY', 'G.', '', 3, 3, '06/11/2025', '08:44 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(237, 'WZZQ44GGKF', 'personnelImg/default_img.jpg', '6848d19075a88.jpg', 'YUSAY', 'ROMEO', 'A.', 'JR. ', 24, 3, '06/11/2025', '08:45 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(238, 'N0AXWJPKNQ', 'personnelImg/default_img.jpg', '6848d198740e7.jpg', 'SORONGON', 'NITA', 'A.', '', 7, 3, '06/11/2025', '08:45 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(239, 'T0SO3GEQZX', 'personnelImg/default_img.jpg', '6848d1fd76a5b.jpg', 'TUPAS', 'ANTHONY', 'L.', '', 11, 3, '06/11/2025', '08:46 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(240, 'ITZ6XK1TGX', 'personnelImg/default_img.jpg', '6848d20b755ef.jpg', 'LUCENARA', 'ROBIJID', 'Q', '', 15, 3, '06/11/2025', '08:47 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(241, 'R0VM3TX265', 'personnelImg/default_img.jpg', '6848d21d765d0.jpg', 'MONTANO', 'EVALYN', 'C.', '', 24, 3, '06/11/2025', '08:47 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(242, 'L33XK2MQYL', 'personnelImg/default_img.jpg', '6848d22c764a6.jpg', 'RELIQUIAS', 'JOHNNY RAY', 'L.', '', 2, 3, '06/11/2025', '08:47 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(243, 'DV6HSR4T31', 'personnelImg/default_img.jpg', '6848d2357669b.jpg', 'SABOBO', 'ALFREDO', 'G.', '', 15, 3, '06/11/2025', '08:47 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(244, '0M1YGUSYJR', 'personnelImg/default_img.jpg', '6848d24b7558f.jpg', 'BALOYO', 'HANSEL', 'M.', '', 11, 3, '06/11/2025', '08:48 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(245, '5Z5WKCHQGF', 'personnelImg/default_img.jpg', '6848d26273380.jpg', 'DOLOR', 'ROLY', 'D.', '', 11, 3, '06/11/2025', '08:48 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(246, 'QQZIGTSWBI', 'personnelImg/default_img.jpg', '6848d28477be0.jpg', 'NACION', 'ELBRED', 'S.', '', 17, 3, '06/11/2025', '08:49 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(247, 'M1RF4GW0TT', 'personnelImg/default_img.jpg', '6848d28d79da9.jpg', 'GUINTOS', 'MA. ELENA', 'N.', '', 14, 3, '06/11/2025', '08:49 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(248, 'BLJQQ3XW3K', 'personnelImg/default_img.jpg', '6848d2987a54e.jpg', 'NORVIE', 'JOSECO', 'A.', '', 11, 3, '06/11/2025', '08:49 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(249, 'VWUQQEPZ5P', 'personnelImg/default_img.jpg', '6848d2a174922.jpg', 'DIONALDO', 'ROEM', 'S.', '', 2, 3, '06/11/2025', '08:49 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(250, '36EOSJANR6', 'personnelImg/default_img.jpg', '6848d2ac74547.jpg', 'ENGCOY', 'CANTERLYN JOY', 'T.', '', 24, 3, '06/11/2025', '08:49 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(251, 'GP4Q0NOWGA', 'personnelImg/default_img.jpg', '6848d2b67934c.jpg', 'GERMINAL', 'CRISTUTO', 'M.', '', 17, 3, '06/11/2025', '08:49 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(252, '1YJTKOZ61A', 'personnelImg/default_img.jpg', '6848d2c0760f8.jpg', 'DURAN', 'MICHELLE', 'M.', '', 11, 3, '06/11/2025', '08:50 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(253, 'H05GELOMZN', 'personnelImg/default_img.jpg', '6848d2c773c84.jpg', 'DEQUINA', 'REYNOLD', 'B.', '', 17, 3, '06/11/2025', '08:50 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(254, 'G6A2OEKXUH', 'personnelImg/default_img.jpg', '6848d3e479f91.jpg', 'MANGILIMUTAN', 'MA. HEARTY', 'CAÑA', '', 12, 3, '06/11/2025', '08:54 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(255, 'WDB3J415ZP', 'personnelImg/default_img.jpg', '6848d54a77d77.jpg', 'AKOL', 'SHARRA MAE', 'ARTICA', '', 10, 3, '06/11/2025', '09:00 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(256, 'FOSBE6FBBP', 'personnelImg/default_img.jpg', '6848d57a776c6.jpg', 'MAHINAY', 'FRANCISCO', 'MANINANTAN', 'SR. ', 3, 3, '06/11/2025', '09:01 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(257, '6D0YWDKLPP', 'personnelImg/default_img.jpg', '6848d5d376307.jpg', 'AGUHAYON', 'RICARDO', 'UBAMOS', '', 14, 3, '06/11/2025', '09:03 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(258, 'FU6VB3OYK4', 'personnelImg/default_img.jpg', '6848d62175ea7.jpg', 'MANGOGTONG', 'WILMAR', 'ESTRELLA', '', 14, 3, '06/11/2025', '09:04 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(259, '5BJ6IAN1FB', 'personnelImg/default_img.jpg', '6848d90a7a3f9.jpg', 'ORBIGOSO', 'ELIZABETH', 'SENIO', '', 8, 3, '06/11/2025', '09:16 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(260, '2SOQJG3P6F', 'personnelImg/default_img.jpg', '6848d98275d5c.jpg', 'SANTES', 'CINDY', 'REBOLDAL', '', 4, 3, '06/11/2025', '09:18 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(261, '6ON2RWCQEM', 'personnelImg/default_img.jpg', '6848d9b67a26d.jpg', 'GAREZA', 'MELANIE', 'CELIS', '', 12, 3, '06/11/2025', '09:19 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(262, 'LUPQ4RIQUJ', 'personnelImg/default_img.jpg', '6848da07771b3.jpg', 'TILOS', 'RIZALIE', 'CELIZ', '', 12, 3, '06/11/2025', '09:21 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(263, 'ZXDNHZFPMW', 'personnelImg/default_img.jpg', '6848da31747c7.jpg', 'PUBLICO', 'NOEMI', 'TOMADO', '', 12, 3, '06/11/2025', '09:21 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(264, 'CPGWPYTB3H', 'personnelImg/default_img.jpg', '6848da69740ed.jpg', 'RELIQUIAS', 'DAPH ANTHONY', 'VIDAURRAZAGA', '', 2, 3, '06/11/2025', '09:22 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(265, 'P4FPMAJER3', 'personnelImg/default_img.jpg', '6848da9e77b8f.jpg', 'GESTOSO', 'LIVIO', 'BAVIERA', 'JR. ', 2, 3, '06/11/2025', '09:23 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(266, 'TXNUYSMIPH', 'personnelImg/default_img.jpg', '68491bf583b4d.jpg', 'ACHA', 'ANGELA', '', '', 26, 3, '06/11/2025', '02:02 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(267, 'RVR5BBU5ZV', 'personnelImg/default_img.jpg', '68491c017504b.jpg', 'CANA', 'EVA MAE', 'G.', '', 11, 3, '06/11/2025', '02:02 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(268, 'ERKTAZMQZA', 'personnelImg/default_img.jpg', '68491c0d736f3.jpg', 'CARDINAL', 'RYAN', 'A.', '', 24, 3, '06/11/2025', '02:02 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(269, 'JGSKGE5D2W', 'personnelImg/default_img.jpg', '68491c4a75781.jpg', 'DELA CONCEPTION', 'IMELDA', 'E.', '', 10, 3, '06/11/2025', '02:03 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(270, 'KHA2LUXNL1', 'personnelImg/default_img.jpg', '68491c597708f.jpg', 'BA-AL', 'VON MARVIN', 'M.', '', 2, 3, '06/11/2025', '02:04 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(271, 'YBD20JEALC', 'personnelImg/default_img.jpg', '68491c6678c35.jpg', 'ABRIGANA', 'PAMELA', '', '', 26, 3, '06/11/2025', '02:04 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(272, '03THKL40WG', 'personnelImg/default_img.jpg', '68491c727431d.jpg', 'ABUGAN', 'ARMELITO', '', '', 26, 3, '06/11/2025', '02:04 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(273, 'XFSKZYRVSI', 'personnelImg/default_img.jpg', '68491ca676117.jpg', 'LASTRILLA', 'GUIDRALYN', 'P.', '', 7, 3, '06/11/2025', '02:05 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(274, 'CY3EN2VVKO', 'personnelImg/default_img.jpg', '68491cb078451.jpg', 'ACADEMIA', 'MELINDA', '', '', 26, 3, '06/11/2025', '02:05 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(275, '2CKQGJE4K6', 'personnelImg/default_img.jpg', '68491d4c77573.jpg', 'DELLOSO', 'CHRISTOPHER', 'M.', '', 14, 3, '06/11/2025', '02:08 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(276, 'JGSKGE5D2W', 'personnelImg/default_img.jpg', '68491f8a736a1.jpg', 'DELA CONCEPTION', 'IMELDA', 'E.', '', 10, 3, '06/11/2025', '02:17 PM', 0, 'on', 'PM OUT', '192.168.1.2', '', '', 0),
(277, 'ERKTAZMQZA', 'personnelImg/default_img.jpg', '68491fa373496.jpg', 'CARDINAL', 'RYAN', 'A.', '', 24, 3, '06/11/2025', '02:18 PM', 0, 'on', 'PM OUT', '192.168.1.2', '', '', 0),
(278, 'Q1DKJASL5Q', 'personnelImg/default_img.jpg', '6849206779da4.jpg', 'ADOLFO', 'JOEY', '', '', 26, 3, '06/11/2025', '02:21 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(279, 'QWV6R0YTL4', 'personnelImg/default_img.jpg', '6849206f79530.jpg', 'ALCON', 'MARYLADGIE', '', '', 26, 3, '06/11/2025', '02:21 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(280, 'FTBBVTMHBJ', 'personnelImg/default_img.jpg', '68492077796e9.jpg', 'ALBIO', 'JESUSITO', '', '', 26, 3, '06/11/2025', '02:21 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(281, '2X2IGRWQ3F', 'personnelImg/default_img.jpg', '6849208276848.jpg', 'ALBERASTINE', 'HAZEL', '', '', 26, 3, '06/11/2025', '02:21 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(282, '4YOQ2PV6LX', 'personnelImg/default_img.jpg', '6849208a75e5d.jpg', 'ALBACITE', 'DIOVEN', '', '', 26, 3, '06/11/2025', '02:22 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(283, '4QWVB2OCKJ', 'personnelImg/default_img.jpg', '68492090752f2.jpg', 'ALACIO', 'RANEL', '', '', 26, 3, '06/11/2025', '02:22 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(284, 'SKFQQYSRUH', 'personnelImg/default_img.jpg', '6849209677125.jpg', 'ALACIO', 'JOIE', '', '', 26, 3, '06/11/2025', '02:22 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(285, 'YL0IE3CYT5', 'personnelImg/default_img.jpg', '6849209e745d0.jpg', 'ALACIO', 'DENNIS', '', '', 26, 3, '06/11/2025', '02:22 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(286, 'N3UASPXZ1I', 'personnelImg/default_img.jpg', '684920a575fb8.jpg', 'AGAN', 'MA.THARA', '', '', 26, 3, '06/11/2025', '02:22 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(287, 'N2CS52WG6G', 'personnelImg/default_img.jpg', '6849217c7613c.jpg', 'GIGANAN', 'GRACE JOY', 'ESTRAO', '', 2, 3, '06/11/2025', '02:26 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(288, 'IO1IY5L4VB', 'personnelImg/default_img.jpg', '68492185770fe.jpg', 'CORTADO', 'ROGELIO', 'ALFANTA', '', 2, 3, '06/11/2025', '02:26 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(289, 'D4ZWVFKO1E', 'personnelImg/default_img.jpg', '6849219073e43.jpg', 'LLAMADO', 'MARISSA', 'MAQUILING', '', 2, 3, '06/11/2025', '02:26 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(290, 'CPGWPYTB3H', 'personnelImg/default_img.jpg', '6849219d77d20.jpg', 'RELIQUIAS', 'DAPH ANTHONY', 'VIDAURRAZAGA', '', 2, 3, '06/11/2025', '02:26 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(291, 'RK02GLHBHR', 'personnelImg/default_img.jpg', '684921b173b21.jpg', 'PINONGGAN', 'JOSEPH', 'ALBERIO', '', 2, 3, '06/11/2025', '02:26 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(292, 'P4FPMAJER3', 'personnelImg/default_img.jpg', '684921b873660.jpg', 'GESTOSO', 'LIVIO', 'BAVIERA', 'JR. ', 2, 3, '06/11/2025', '02:27 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0);
INSERT INTO `personnel_logs` (`log_id`, `RFTag_id`, `img`, `captured_img`, `lname`, `fname`, `mname`, `suffix`, `do_id`, `shift_id`, `logDate`, `logTime`, `logTime_sec`, `late_status`, `logFlow`, `client_ip`, `remarks`, `travel_leave_code`, `ref_log_id`) VALUES
(293, 'M44PLOGP0U', 'personnelImg/default_img.jpg', '684921c2754b2.jpg', 'CASTILLO', 'JOEPET', 'CANILLADA', '', 2, 3, '06/11/2025', '02:27 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(294, 'UGYT3P0WEX', 'personnelImg/default_img.jpg', '684921c87593c.jpg', 'TIANGA', 'SERAFIN', 'ORENDAIN', 'JR. ', 2, 3, '06/11/2025', '02:27 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(295, '0LKJLM2C22', 'personnelImg/default_img.jpg', '684921cf7618a.jpg', 'MANGILIMUTAN', 'LORRAINE MAE', 'GESTOSO', '', 2, 3, '06/11/2025', '02:27 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(296, 'UV0LOFMFS3', 'personnelImg/default_img.jpg', '684921dc78f54.jpg', 'LOGRONIO', 'JOESIFIL', 'FAJARDO', '', 2, 3, '06/11/2025', '02:27 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(297, 'WXV3CGLSBO', 'personnelImg/default_img.jpg', '684922ab76fcb.jpg', 'GAYOMALE', 'JOSE ROBERT', 'MILLAN', 'JR. ', 24, 3, '06/11/2025', '02:31 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(298, 'E6FJPBZ5BD', 'personnelImg/default_img.jpg', '684922b6758a0.jpg', 'TUPAS', 'JASON', 'TEMBREVILLA', '', 24, 3, '06/11/2025', '02:31 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(299, 'SPRSLW2YQM', 'personnelImg/default_img.jpg', '684922c075933.jpg', 'OCTAVIO', 'JODYBONNE', 'GAYATIN', '', 24, 3, '06/11/2025', '02:31 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(300, 'KE4HXNTQTI', 'personnelImg/default_img.jpg', '684922c676e51.jpg', 'BILBAO', 'FRANCISCO JOSE', 'LOCSIN', 'JR. ', 24, 3, '06/11/2025', '02:31 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(301, 'WEYJU63LLE', 'personnelImg/default_img.jpg', '684922cd770c4.jpg', 'BILBAO', 'MA. TERESA', 'LOCSIN', '', 24, 3, '06/11/2025', '02:31 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(302, 'KCTG5YC64S', 'personnelImg/default_img.jpg', '684922d576a41.jpg', 'TUBILLEJA', 'THEODORE', 'DELA CRUZ', 'SR. ', 24, 3, '06/11/2025', '02:31 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(303, '55SDU3PJSI', 'personnelImg/default_img.jpg', '684922dd77506.jpg', 'ENCOY', 'JEFRE', 'LAZALITA', '', 24, 3, '06/11/2025', '02:31 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(304, '1Z6LX20PVU', 'personnelImg/default_img.jpg', '684922ed74d65.jpg', 'RELIQUIAS', 'JOHN MARK', 'BALUNGCAS', '', 23, 3, '06/11/2025', '02:32 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(305, 'BCUT6421HG', 'personnelImg/default_img.jpg', '684922fd75281.jpg', 'CANDULIZAS', 'MOLAVE', 'TEMBREVILLA', '', 2, 3, '06/11/2025', '02:32 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(306, 'T1X1EOAO50', 'personnelImg/default_img.jpg', '684923277329c.jpg', 'HERRADURA', 'GINALYN ', 'OBENZA', '', 2, 3, '06/11/2025', '02:33 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(307, 'NT36Z4UITB', 'personnelImg/default_img.jpg', '68492da078695.jpg', 'MALAYO', 'EDGARDO', 'FLORES', '', 24, 3, '06/11/2025', '03:17 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(308, '6DZNSFDRJG', 'personnelImg/default_img.jpg', '68492daa75402.jpg', 'MANOS', 'TEOFILO', 'ENCARGUEZ', 'JR. ', 24, 3, '06/11/2025', '03:18 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(309, 'ZGMTZ2Y3BT', 'personnelImg/default_img.jpg', '68492db676926.jpg', 'OCTAVIO', 'PETER JOHN ', 'LAZALITA', '', 24, 3, '06/11/2025', '03:18 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(310, 'DCORCD1ICP', 'personnelImg/default_img.jpg', '68492dc573990.jpg', 'TUMA-OB', 'LESLIE AIKEE DYAN', 'GIGANAN', '', 24, 3, '06/11/2025', '03:18 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(311, 'BSY3X4CY3E', 'personnelImg/default_img.jpg', '68492dcf74a4a.jpg', 'SANTES', 'JOCELYN', 'CORONEL', '', 24, 3, '06/11/2025', '03:18 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(312, 'WQ4X6QJYGH', 'personnelImg/default_img.jpg', '68492ddd755cc.jpg', 'ANLIQUERA', 'SIENA', 'VASQUEZ', '', 26, 3, '06/11/2025', '03:18 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(313, '1QETKAFQWO', 'personnelImg/default_img.jpg', '68492de775da4.jpg', 'DELOTINA', 'JOFEL', 'EVANGELISTA', '', 24, 3, '06/11/2025', '03:19 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(314, '2FUFQBHUQM', 'personnelImg/default_img.jpg', '68492df174247.jpg', 'DECENA', 'LANNE', 'VILLARETE', '', 24, 3, '06/11/2025', '03:19 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(315, 'HSDQZ6G6PA', 'personnelImg/default_img.jpg', '68492df875343.jpg', 'PEREZ', 'JOSE MARIA', 'LIM', 'JR. ', 12, 3, '06/11/2025', '03:19 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(316, 'DFR5QGBYHW', 'personnelImg/default_img.jpg', '68492eb173ded.jpg', 'BARO', 'PHOEBE', 'MANILINGAN', '', 12, 3, '06/11/2025', '03:22 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(317, 'LVX5C46TAS', 'personnelImg/default_img.jpg', '68492eb973e4e.jpg', 'SILVESTRE', 'CYNTHIA', 'LAMBOT', '', 12, 3, '06/11/2025', '03:22 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(318, 'ZXDNHZFPMW', 'personnelImg/default_img.jpg', '68492ec275557.jpg', 'PUBLICO', 'NOEMI', 'TOMADO', '', 12, 3, '06/11/2025', '03:22 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(319, '44SB4W0MF3', 'personnelImg/default_img.jpg', '68492f7c7606c.jpg', 'VIDAURRAZAGA', 'MC', 'PEPITO', '', 24, 3, '06/11/2025', '03:25 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(320, 'QESM444ZK0', 'personnelImg/default_img.jpg', '68492f8976149.jpg', 'TEMBREVILLA', 'RONA', 'ESCOSAR', '', 12, 3, '06/11/2025', '03:26 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(321, 'LUPQ4RIQUJ', 'personnelImg/default_img.jpg', '68492f94747ca.jpg', 'TILOS', 'RIZALIE', 'CELIZ', '', 12, 3, '06/11/2025', '03:26 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(322, 'I03I6VZELI', 'personnelImg/default_img.jpg', '68492fd17429c.jpg', 'GALAN', 'MARY JANE', 'CELIS', '', 12, 3, '06/11/2025', '03:27 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(323, 'MS5LRO1IJE', 'personnelImg/default_img.jpg', '68492fd9747e8.jpg', 'VILLANUEVA', 'ROSALIE', 'ELARDO', '', 12, 3, '06/11/2025', '03:27 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(324, 'HDMCPVKYB1', 'personnelImg/default_img.jpg', '68492fe274034.jpg', 'ROXAS', 'MARY ANN', 'VILLAFUERTE', '', 12, 3, '06/11/2025', '03:27 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(325, 'ZV1EXSSS30', 'personnelImg/default_img.jpg', '68492fec79770.jpg', 'BONGCAWEL', 'GENELYN ', 'HISONA', '', 12, 3, '06/11/2025', '03:27 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(326, 'V2M0IIAYRM', 'personnelImg/default_img.jpg', '68492ff474e79.jpg', 'VILLANUEVA', 'RAYGILDA', 'VERGARA', '', 12, 3, '06/11/2025', '03:27 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(327, 'I0FY3RFXM5', 'personnelImg/default_img.jpg', '6849309e75099.jpg', 'TILOS', 'OSCAR', 'VILLARETE', '', 4, 3, '06/11/2025', '03:30 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(328, '6ON2RWCQEM', 'personnelImg/default_img.jpg', '684930ad7650a.jpg', 'GAREZA', 'MELANIE', 'CELIS', '', 12, 3, '06/11/2025', '03:30 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(329, 'MHDZI160KN', 'personnelImg/default_img.jpg', '684930b572d83.jpg', 'TEMBREVILLA', 'CAROLINE', 'CANILLO', '', 12, 3, '06/11/2025', '03:31 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(330, '2O1JSGHAYX', 'personnelImg/default_img.jpg', '684930bc79a18.jpg', 'GAUAL', 'ROLIN', 'BERGONIO', 'SR. ', 12, 3, '06/11/2025', '03:31 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(331, 'ZHXY3CO3KQ', 'personnelImg/default_img.jpg', '684930c5745af.jpg', 'DELA FUENTE', 'EBER', 'ORMEO', '', 12, 3, '06/11/2025', '03:31 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(332, 'WSJIHMXEN6', 'personnelImg/default_img.jpg', '684930cc75c7b.jpg', 'TILOS', 'MARCELINA', 'CELIS', '', 12, 3, '06/11/2025', '03:31 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(333, 'RCCO5TPACC', 'personnelImg/default_img.jpg', '684930d676bd0.jpg', 'PIMENTEL', 'EMY', 'MAGBANUA', '', 12, 3, '06/11/2025', '03:31 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(334, 'GZ5LO0QDRC', 'personnelImg/default_img.jpg', '684930e277d46.jpg', 'GARCIA', 'ADELA', 'ANTOLIN', '', 12, 3, '06/11/2025', '03:31 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(335, '5UVF66ZPY0', 'personnelImg/default_img.jpg', '684930ee7ab05.jpg', 'MAQUILING', 'WARREN', 'RAMIREZ', '', 12, 3, '06/11/2025', '03:31 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(336, 'HEPAB6X3PL', 'personnelImg/default_img.jpg', '684930fa76ab9.jpg', 'VILLAMATER', 'LORALYN', 'BARROCA', '', 12, 3, '06/11/2025', '03:32 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(337, 'CEPHX3JOM0', 'personnelImg/default_img.jpg', '684931cb758b1.jpg', 'TIBAYDE', 'IRENE', 'GIGANAN', '', 10, 3, '06/11/2025', '03:35 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(338, '2SOQJG3P6F', 'personnelImg/default_img.jpg', '684931d7740c8.jpg', 'SANTES', 'CINDY', 'REBOLDAL', '', 4, 3, '06/11/2025', '03:35 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(339, 'LTQHD2BPYJ', 'personnelImg/default_img.jpg', '684931e7741e9.jpg', 'GALON', 'RANDOLF', 'BLANCO', '', 4, 3, '06/11/2025', '03:36 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(340, 'MF4R0LP0PT', 'personnelImg/default_img.jpg', '684931ef7796b.jpg', 'TOLEDO', 'RAYMUND ANTHONY', 'INOLINO', '', 4, 3, '06/11/2025', '03:36 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(341, 'DO2X14YKJR', 'personnelImg/default_img.jpg', '684931f9757e0.jpg', 'VILLARUBIA', 'ALTHEA', 'PIA', '', 5, 3, '06/11/2025', '03:36 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(342, 'N3CFWPHFQX', 'personnelImg/default_img.jpg', '684931ff74a66.jpg', 'SANTES', 'CAROL ANN', 'TILOS', '', 5, 3, '06/11/2025', '03:36 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(343, 'KZFCP2S0HT', 'personnelImg/default_img.jpg', '68493207756a0.jpg', 'GUINTOS', 'JOSEPHINE', 'INDINO', '', 10, 3, '06/11/2025', '03:36 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(344, '0RVK3VBCZ1', 'personnelImg/default_img.jpg', '6849321977f8e.jpg', 'MANOS', 'ANNABELLE', 'PERIGUA', '', 5, 3, '06/11/2025', '03:36 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(345, 'APPZCXZCJS', 'personnelImg/default_img.jpg', '6849322374d06.jpg', 'MIRANDA', 'JAIME', 'MAULIT', '', 5, 3, '06/11/2025', '03:37 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(346, 'NZ0IKZI1IR', 'personnelImg/default_img.jpg', '6849322e784a8.jpg', 'MANGOGTONG', 'MA. MARILOU ', 'ESTRELLA', '', 10, 3, '06/11/2025', '03:37 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(347, 'OSYDS2VNEW', 'personnelImg/default_img.jpg', '684932d57635d.jpg', 'ABALLE', 'BETHEL', 'PERFUMA', '', 10, 3, '06/11/2025', '03:40 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(348, 'KVKMFQDZDV', 'personnelImg/default_img.jpg', '684932e67a924.jpg', 'GA-AN', 'LENLY', 'NOSAL', '', 10, 3, '06/11/2025', '03:40 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(349, '2XYVUZG44K', 'personnelImg/default_img.jpg', '684932ec73e2a.jpg', 'RELIQUIAS', 'JOERIBEL', 'TORIANO', '', 9, 3, '06/11/2025', '03:40 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(350, 'KDV12JKLNR', 'personnelImg/default_img.jpg', '684932f37763f.jpg', 'TRINIO-SANTES', 'VANESSA', 'LIRAZAN', '', 3, 3, '06/11/2025', '03:40 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(351, 'C4XW3NQ3CE', 'personnelImg/default_img.jpg', '684932fa7706f.jpg', 'ACADEMIA', 'RIZALYN', 'CAYANAN', '', 9, 3, '06/11/2025', '03:40 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(352, 'VUM5AWYEUH', 'personnelImg/default_img.jpg', '6849330479515.jpg', 'SUMUGAT', 'JENELYN', 'TELONIO', '', 3, 3, '06/11/2025', '03:40 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(353, 'ES1MY5CB5N', 'personnelImg/default_img.jpg', '6849330b74250.jpg', 'BUDACA', 'REVINIA', 'AMACIO', '', 10, 3, '06/11/2025', '03:40 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(354, '2KLYB0WHDY', 'personnelImg/default_img.jpg', '6849331175614.jpg', 'GESTOSO', 'CARLITO', 'BAVIERA', '', 10, 3, '06/11/2025', '03:41 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(355, 'HR1TD1EOO6', 'personnelImg/default_img.jpg', '68493319757ec.jpg', 'NATALIO', 'JOEFREY', 'TEMBREVILLA', '', 9, 3, '06/11/2025', '03:41 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(356, 'N0PELGBXHV', 'personnelImg/default_img.jpg', '6849332177399.jpg', 'GUSTILO', 'LEILANI', 'DECENA', '', 9, 3, '06/11/2025', '03:41 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(357, 'K0GBVLRIOM', 'personnelImg/default_img.jpg', '6849346076d11.jpg', 'JORDAN', 'MARK ANTHONY', 'TOMADO', '', 3, 3, '06/11/2025', '03:46 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(358, 'V4PAJMJ5YO', 'personnelImg/default_img.jpg', '68493466770f2.jpg', 'TUPAS', 'OFELIA', 'TEMBREVILLA', '', 6, 3, '06/11/2025', '03:46 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(359, 'DURSCXCSIM', 'personnelImg/default_img.jpg', '6849346c75aa9.jpg', 'MAYANDIA', 'ANALIE', 'ELARMO', '', 6, 3, '06/11/2025', '03:46 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(360, 'EAUNV5WVM0', 'personnelImg/default_img.jpg', '6849347375c10.jpg', 'BARROCA', 'JORGIE', 'PERFUMA', '', 6, 3, '06/11/2025', '03:46 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(361, 'EHEYDZ1UYN', 'personnelImg/default_img.jpg', '684934797655e.jpg', 'ALATON', 'GINA', 'NABOR', '', 6, 3, '06/11/2025', '03:47 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(362, 'UBFPHM020G', 'personnelImg/default_img.jpg', '6849348174f93.jpg', 'BONILLA', 'ANNI VER', 'PAMLIEGA', '', 6, 3, '06/11/2025', '03:47 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(363, 'B42WBNMNSK', 'personnelImg/default_img.jpg', '6849349a73954.jpg', 'LIMSIACO', 'ARIANE', 'CONSTANTINO', '', 6, 3, '06/11/2025', '03:47 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(364, 'DGPF5FDK53', 'personnelImg/default_img.jpg', '684934a277ec5.jpg', 'ARANETA', 'JOSELITA', 'GENTELIZO', '', 6, 3, '06/11/2025', '03:47 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(365, 'N4DZR4BBY0', 'personnelImg/default_img.jpg', '684934a877f36.jpg', 'RELIQUIAS', 'MARIA FE', 'GUINTOS', '', 8, 3, '06/11/2025', '03:47 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(366, 'CMDUXPZ44I', 'personnelImg/default_img.jpg', '684934af74169.jpg', 'GONZAL', 'SANDRA', 'CASTILLO', '', 8, 3, '06/11/2025', '03:47 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(367, 'EAUNV5WVM0', 'personnelImg/default_img.jpg', '684935a974a81.jpg', 'BARROCA', 'JORGIE', 'PERFUMA', '', 6, 3, '06/11/2025', '03:52 PM', 0, 'on', 'PM OUT', '192.168.1.2', '', '', 0),
(368, '5BJ6IAN1FB', 'personnelImg/default_img.jpg', '684937db7514e.jpg', 'ORBIGOSO', 'ELIZABETH', 'SENIO', '', 8, 3, '06/11/2025', '04:01 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(369, 'ZJPTEJP5UQ', 'personnelImg/default_img.jpg', '684937f774aa8.jpg', 'AMANTE', 'LEONIZA', 'FLORES', '', 11, 3, '06/11/2025', '04:01 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(370, '2M5F54VPSN', 'personnelImg/default_img.jpg', '684937ff75f50.jpg', 'AKOL', 'MARLOU', 'ARTICA', '', 14, 3, '06/11/2025', '04:02 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(371, 'KP6G24TB40', 'personnelImg/default_img.jpg', '68493808762c4.jpg', 'RELADO', 'MEDALIA', 'VALENZUELA', '', 8, 3, '06/11/2025', '04:02 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(372, 'PWU0GP4SHN', 'personnelImg/default_img.jpg', '6849381174a89.jpg', 'SANTES', 'AZELA', 'FLORES', '', 7, 3, '06/11/2025', '04:02 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(373, '2SJBOJTXPC', 'personnelImg/default_img.jpg', '6849382074880.jpg', 'GELLECANAO', 'MURIELLE', 'LIRAZAN', '', 7, 3, '06/11/2025', '04:02 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(374, 'Q0BSK1IGMH', 'personnelImg/default_img.jpg', '6849382b73a0e.jpg', 'BARIQUIT', 'PRACEDES', 'CABONILAS', '', 7, 3, '06/11/2025', '04:02 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(375, '1DSSFVBIFF', 'personnelImg/default_img.jpg', '6849384075a18.jpg', 'MAESTRECAMPO', 'EVELYN', 'ELARMO', '', 7, 3, '06/11/2025', '04:03 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(376, 'Q0QB3ZVYNK', 'personnelImg/default_img.jpg', '6849385a7782e.jpg', 'VILLARETE', 'ANGELA', 'TELONIO', '', 7, 3, '06/11/2025', '04:03 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(377, 'FKX31XYW34', 'personnelImg/default_img.jpg', '6849386874736.jpg', 'DELA FUENTE', 'ALVIN', 'ORMEO', '', 11, 3, '06/11/2025', '04:03 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(378, 'FU6VB3OYK4', 'personnelImg/default_img.jpg', '68493b3974266.jpg', 'MANGOGTONG', 'WILMAR', 'ESTRELLA', '', 14, 3, '06/11/2025', '04:15 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(379, 'M1Y5ETAYD6', 'personnelImg/default_img.jpg', '68493b507969d.jpg', 'NAVA', 'LUZ SALOME', 'VILLA', '', 14, 3, '06/11/2025', '04:16 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(380, 'O6JWSQ5BTR', 'personnelImg/default_img.jpg', '68493b69752bc.jpg', 'DELA CONCEPTION', 'IMELDA', 'ESPENORIO', '', 14, 3, '06/11/2025', '04:16 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(381, 'GSQZ2OCVHQ', 'personnelImg/default_img.jpg', '68493b7f75097.jpg', 'RELIQUIAS', 'GERALD', 'TORIANO', '', 14, 3, '06/11/2025', '04:17 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(382, 'EKSUEACJM5', 'personnelImg/default_img.jpg', '68493b9d72d09.jpg', 'ACIBIDO', 'CHE GENEROSO', 'PARO', '', 14, 3, '06/11/2025', '04:17 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(383, 'W2DGEUTP62', 'personnelImg/default_img.jpg', '68493baa7407c.jpg', 'LOREDO', 'MARIA ELENA', 'DELLOSO', '', 14, 3, '06/11/2025', '04:17 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(384, 'J3UWH2Z32E', 'personnelImg/default_img.jpg', '68493bc5774ea.jpg', 'TRINIDAD', 'RODOLFO', 'PULGAN', 'JR. ', 15, 3, '06/11/2025', '04:18 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(385, 'GRN63LXTVC', 'personnelImg/default_img.jpg', '68493bd4745b5.jpg', 'GIGANAN', 'AGNES', 'NAVA', '', 15, 3, '06/11/2025', '04:18 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(386, '6KR3ZM4BU3', 'personnelImg/default_img.jpg', '68493c54763fa.jpg', 'TELONIO', 'JAMES ANDREW', 'GUINTOS', '', 15, 3, '06/11/2025', '04:20 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(387, 'GOYLQLNANM', 'personnelImg/default_img.jpg', '68493c657a1d5.jpg', 'DELOTINA', 'DANILO', 'NAPIERE', '', 17, 3, '06/11/2025', '04:20 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(388, 'R52SQSFJB4', 'personnelImg/default_img.jpg', '68493c7972a64.jpg', 'BONILLA', 'JURY', 'BENLOT', '', 17, 3, '06/11/2025', '04:21 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(389, 'T4BXLLYWTG', 'personnelImg/default_img.jpg', '68493c8e7799d.jpg', 'DERIT', 'JOSE ALAN ', 'DURO', '', 17, 3, '06/11/2025', '04:21 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(390, 'KUNUEE1ZKH', 'personnelImg/default_img.jpg', '68493cac74995.jpg', 'YUSAY', 'JOSE', 'TOGLE', 'III ', 16, 3, '06/11/2025', '04:22 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(391, 'POT5DLCLXZ', 'personnelImg/default_img.jpg', '68493cb8766ba.jpg', 'GUINTOS', 'TINA MARIE', 'LUGA', '', 16, 3, '06/11/2025', '04:22 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(392, 'F3DUWOJ4SZ', 'personnelImg/default_img.jpg', '68493cc775dcd.jpg', 'SIASON', 'CHEZAH ERL', 'GAUAL', '', 16, 3, '06/11/2025', '04:22 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(393, 'MMB6JKH1T3', 'personnelImg/default_img.jpg', '68493ce976da6.jpg', 'CAMBARIJAN', 'JIMMY', 'ZETA', '', 25, 3, '06/11/2025', '04:23 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(394, '56EZ3T4YP2', 'personnelImg/default_img.jpg', '68493cfe73756.jpg', 'CALUMBA', 'ANALYN', 'MANILINGAN', '', 25, 3, '06/11/2025', '04:23 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(395, '6D0YWDKLPP', 'personnelImg/default_img.jpg', '68493d5c757f9.jpg', 'AGUHAYON', 'RICARDO', 'UBAMOS', '', 14, 3, '06/11/2025', '04:24 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(396, 'UUR4C35PLC', 'personnelImg/default_img.jpg', '68493d6d74eeb.jpg', 'ALIMANE', 'JINKY', 'GALPO', '', 7, 3, '06/11/2025', '04:25 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(397, 'EEG0NY2B5U', 'personnelImg/default_img.jpg', '68493d8c76689.jpg', 'ARBOIZ', 'JOHN', 'VILLARICO', '', 15, 3, '06/11/2025', '04:25 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(398, 'R0ZJXTVRR4', 'personnelImg/default_img.jpg', '68493da175679.jpg', 'AVELINO', 'JULIEBERT', 'AZUCENA', '', 2, 3, '06/11/2025', '04:26 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(399, 'BSLAWXCQ2N', 'personnelImg/default_img.jpg', '68493db174f9a.jpg', 'BAYDO', 'JULITO', 'CAYAS', '', 3, 3, '06/11/2025', '04:26 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(400, 'Z2TDUSRUV6', 'personnelImg/default_img.jpg', '68493dc376455.jpg', 'BIACA', 'HERBERT', 'BORNALES', '', 3, 3, '06/11/2025', '04:26 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(401, 'OINBKY4BGA', 'personnelImg/default_img.jpg', '68493dd177ad1.jpg', 'DECATORIA', 'JOEBERT', 'SARIL', '', 3, 3, '06/11/2025', '04:26 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(402, 'YCSOHXN5TA', 'personnelImg/default_img.jpg', '68493de176823.jpg', 'GALLARDA', 'ELNOR', 'PACLAONA', '', 12, 3, '06/11/2025', '04:27 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(403, 'LS5IJYTERX', 'personnelImg/default_img.jpg', '68493df2759df.jpg', 'GUINTOS', 'WINSTON', 'NAVA', '', 3, 3, '06/11/2025', '04:27 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(404, 'JT6NRMM2MG', 'personnelImg/default_img.jpg', '68493e0274746.jpg', 'LABRADOR', 'REY', 'TRIBUCIO', '', 10, 3, '06/11/2025', '04:27 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(405, '1QETKAFQWO', 'personnelImg/default_img.jpg', '6849416e763ef.jpg', 'DELOTINA', 'JOFEL', 'EVANGELISTA', '', 24, 3, '06/11/2025', '04:42 PM', 0, 'off', 'PM OUT', '192.168.1.2', '', '', 0),
(406, '2FUFQBHUQM', 'personnelImg/default_img.jpg', '684941b676ca0.jpg', 'DECENA', 'LANNE', 'VILLARETE', '', 24, 3, '06/11/2025', '04:43 PM', 0, 'off', 'PM OUT', '192.168.1.2', '', '', 0),
(407, 'NT36Z4UITB', 'personnelImg/default_img.jpg', '684941c777432.jpg', 'MALAYO', 'EDGARDO', 'FLORES', '', 24, 3, '06/11/2025', '04:43 PM', 0, 'off', 'PM OUT', '192.168.1.2', '', '', 0),
(408, 'QESM444ZK0', 'personnelImg/default_img.jpg', '684943c52006c.jpg', 'TEMBREVILLA', 'RONA', 'ESCOSAR', '', 12, 3, '06/11/2025', '04:52 PM', 0, 'off', 'PM OUT', '192.168.1.2', '', '', 0),
(409, '65VQESUT03', 'personnelImg/default_img.jpg', '6852650488be6.jpg', 'PANGANTIHON', 'JOHN IRVING', 'TOLEDO', '', 3, 3, '06/18/2025', '03:04 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(410, 'OIMV5KOLF2', 'personnelImg/default_img.jpg', '68526531867c9.jpg', 'PEROSIA', 'GLORY MAE', 'TUNDA-AN', '', 12, 3, '06/18/2025', '03:05 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(411, 'PMOLWNYZZA', 'personnelImg/default_img.jpg', '68526542804ef.jpg', 'SUSANA', 'FREDERICK DAVY', 'PINGCALE', '', 12, 3, '06/18/2025', '03:05 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(412, 'F255HSX3FM', 'personnelImg/default_img.jpg', '685265647d1bf.jpg', 'GUINTOS', 'ERICA', 'MANANGAN', '', 14, 3, '06/18/2025', '03:06 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(413, 'PQMSBLBNJ6', 'personnelImg/default_img.jpg', '6852656e7cba3.jpg', 'VIDAURRAZAGA', 'NOAH', 'VASQUEZ', '', 2, 3, '06/18/2025', '03:06 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(414, 'E0VZSU0W6G', 'personnelImg/default_img.jpg', '685265847d8f3.jpg', 'APLAON', 'MA. LEE', 'MAESTRECAMPO', '', 23, 3, '06/18/2025', '03:06 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(415, 'CCEWWUW3P2', 'personnelImg/default_img.jpg', '6852658f7f8a5.jpg', 'TUBILLEJA', 'THEODORE', 'SIGUEZA', 'JR. ', 6, 3, '06/18/2025', '03:06 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(416, 'P-ZGT-382010-110', 'personnelImg/default_img.jpg', '685265a47d7d0.jpg', 'TEMBREVILLA', 'ZOSIMO', 'GAVILAGA', '', 3, 3, '06/18/2025', '03:07 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(417, 'FOSBE6FBBP', 'personnelImg/default_img.jpg', '685265b57e32f.jpg', 'MAHINAY', 'FRANCISCO', 'MANINANTAN', 'SR. ', 3, 3, '06/18/2025', '03:07 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(418, 'SP2WX6WZFC', 'personnelImg/default_img.jpg', '685265c07e5da.jpg', 'PADA', 'VERONICA EVELYN', 'ALBISO', '', 12, 3, '06/18/2025', '03:07 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(419, 'SARR3EM6ZV', 'personnelImg/default_img.jpg', '6852673589ca3.jpg', 'PACURIB', 'JULITO', 'PLAÑA', 'JR. ', 3, 3, '06/18/2025', '03:13 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(420, '2FUFQBHUQM', 'personnelImg/default_img.jpg', '685267497e880.jpg', 'DECENA', 'LANNE', 'VILLARETE', '', 24, 3, '06/18/2025', '03:14 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(421, 'E6LFKFXAD0', 'personnelImg/default_img.jpg', '685267557dd70.jpg', 'NUÑESCO', 'AIZA', 'ANDO', '', 5, 3, '06/18/2025', '03:14 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(422, 'XD5JB3HCC0', 'personnelImg/default_img.jpg', '685267657f984.jpg', 'LAREÑO', 'LIWAYA', 'MAHINAY', '', 11, 3, '06/18/2025', '03:14 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(423, 'GJCC6XY3BR', 'personnelImg/default_img.jpg', '685267717e9d0.jpg', 'ANTIQUEÑO', 'ANA MARIE', 'SANOY', '', 12, 3, '06/18/2025', '03:14 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(424, 'SJLE6GGLD6', 'personnelImg/nrf04z55-che.jpg', '685267927f897.jpg', 'JUAREZ', 'CHERRY LYNN', 'MONTAÑO', '', 2, 3, '06/18/2025', '03:15 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(425, 'WILZVWBBRI', 'personnelImg/default_img.jpg', '6852679f80699.jpg', 'OCCEÑA', 'GEM', 'RELAMPAGOS', '', 3, 3, '06/18/2025', '03:15 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(426, '111LXWXFLY', 'personnelImg/default_img.jpg', '685267a87eadd.jpg', 'NICOR', 'MICHAEL', 'RAMOS', '', 7, 3, '06/18/2025', '03:15 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(427, 'YFUCRYEDIV', 'personnelImg/nrfc1ohg-jet.jpg', '685267b47c6b6.jpg', 'BARROCA', 'JETTER', 'SENIO', '', 14, 3, '06/18/2025', '03:16 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(428, 'JETERWW23U', 'personnelImg/nrf5ihz6-mae-flor.jpg', '685267c27ba38.jpg', 'BARRIOS', 'MAE FLOR', 'ALMAIZ', '', 8, 3, '06/18/2025', '03:16 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(429, 'WDB3J415ZP', 'personnelImg/default_img.jpg', '685267e47cf7f.jpg', 'AKOL', 'SHARRA MAE', 'ARTICA', '', 10, 3, '06/18/2025', '03:16 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(430, 'DV6HSR4T31', 'personnelImg/default_img.jpg', '685268f67d63d.jpg', 'SABOBO', 'ALFREDO', 'G.', '', 15, 3, '06/18/2025', '03:21 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(431, 'L33XK2MQYL', 'personnelImg/default_img.jpg', '685268ff7eeb2.jpg', 'RELIQUIAS', 'JOHNNY RAY', 'L.', '', 2, 3, '06/18/2025', '03:21 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(432, 'N0AXWJPKNQ', 'personnelImg/default_img.jpg', '685269087e5b3.jpg', 'SORONGON', 'NITA', 'A.', '', 7, 3, '06/18/2025', '03:21 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(433, 'HOA5M4V2KU', 'personnelImg/default_img.jpg', '6852690e7c665.jpg', 'TEMBREVILLA', 'THOMY', 'G.', '', 3, 3, '06/18/2025', '03:21 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(434, 'T0SO3GEQZX', 'personnelImg/default_img.jpg', '685269167b40c.jpg', 'TUPAS', 'ANTHONY', 'L.', '', 11, 3, '06/18/2025', '03:21 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(435, 'WZZQ44GGKF', 'personnelImg/default_img.jpg', '685269217e6aa.jpg', 'YUSAY', 'ROMEO', 'A.', 'JR. ', 24, 3, '06/18/2025', '03:22 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(436, 'R0VM3TX265', 'personnelImg/default_img.jpg', '6852692b7f786.jpg', 'MONTANO', 'EVALYN', 'C.', '', 24, 3, '06/18/2025', '03:22 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(437, 'ITZ6XK1TGX', 'personnelImg/default_img.jpg', '685269317b3a3.jpg', 'LUCENARA', 'ROBIJID', 'Q', '', 15, 3, '06/18/2025', '03:22 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(438, '0M1YGUSYJR', 'personnelImg/default_img.jpg', '685269377dac0.jpg', 'BALOYO', 'HANSEL', 'M.', '', 11, 3, '06/18/2025', '03:22 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(439, 'G6A2OEKXUH', 'personnelImg/default_img.jpg', '6852693f7db99.jpg', 'MANGILIMUTAN', 'MA. HEARTY', 'CAÑA', '', 12, 3, '06/18/2025', '03:22 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(440, 'O2NINN551F', 'personnelImg/default_img.jpg', '685269ef7c6c7.jpg', 'PARCON', 'MERCEDITA', 'T.', '', 7, 3, '06/18/2025', '03:25 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(441, 'QQZIGTSWBI', 'personnelImg/default_img.jpg', '685269f87c506.jpg', 'NACION', 'ELBRED', 'S.', '', 17, 3, '06/18/2025', '03:25 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(442, 'BLJQQ3XW3K', 'personnelImg/default_img.jpg', '68526a027c880.jpg', 'NORVIE', 'JOSECO', 'A.', '', 11, 3, '06/18/2025', '03:25 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(443, 'H05GELOMZN', 'personnelImg/default_img.jpg', '68526a0e7e4cd.jpg', 'DEQUINA', 'REYNOLD', 'B.', '', 17, 3, '06/18/2025', '03:26 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(444, 'M1RF4GW0TT', 'personnelImg/default_img.jpg', '68526a1981d78.jpg', 'GUINTOS', 'MA. ELENA', 'N.', '', 14, 3, '06/18/2025', '03:26 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(445, 'GP4Q0NOWGA', 'personnelImg/default_img.jpg', '68526a237e322.jpg', 'GERMINAL', 'CRISTUTO', 'M.', '', 17, 3, '06/18/2025', '03:26 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(446, '36EOSJANR6', 'personnelImg/default_img.jpg', '68526a2b7e9cc.jpg', 'ENGCOY', 'CANTERLYN JOY', 'T.', '', 24, 3, '06/18/2025', '03:26 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(447, '1YJTKOZ61A', 'personnelImg/default_img.jpg', '68526a357ce3c.jpg', 'DURAN', 'MICHELLE', 'M.', '', 11, 3, '06/18/2025', '03:26 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(448, '5Z5WKCHQGF', 'personnelImg/default_img.jpg', '68526a3d7da47.jpg', 'DOLOR', 'ROLY', 'D.', '', 11, 3, '06/18/2025', '03:26 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(449, 'VWUQQEPZ5P', 'personnelImg/default_img.jpg', '68526a427d9a0.jpg', 'DIONALDO', 'ROEM', 'S.', '', 2, 3, '06/18/2025', '03:26 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(450, 'JGSKGE5D2W', 'personnelImg/default_img.jpg', '68526ae07a163.jpg', 'DELA CONCEPTION', 'IMELDA', 'E.', '', 10, 3, '06/18/2025', '03:29 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(451, '2CKQGJE4K6', 'personnelImg/default_img.jpg', '68526aec7e4c3.jpg', 'DELLOSO', 'CHRISTOPHER', 'M.', '', 14, 3, '06/18/2025', '03:29 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(452, 'ERKTAZMQZA', 'personnelImg/default_img.jpg', '68526af5831d2.jpg', 'CARDINAL', 'RYAN', 'A.', '', 24, 3, '06/18/2025', '03:29 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(453, 'RVR5BBU5ZV', 'personnelImg/default_img.jpg', '68526afc7e3e3.jpg', 'CANA', 'EVA MAE', 'G.', '', 11, 3, '06/18/2025', '03:30 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(454, 'KHA2LUXNL1', 'personnelImg/default_img.jpg', '68526b057d738.jpg', 'BA-AL', 'VON MARVIN', 'M.', '', 2, 3, '06/18/2025', '03:30 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(455, 'XFSKZYRVSI', 'personnelImg/default_img.jpg', '68526b0e7f366.jpg', 'LASTRILLA', 'GUIDRALYN', 'P.', '', 7, 3, '06/18/2025', '03:30 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(456, 'YBD20JEALC', 'personnelImg/default_img.jpg', '68526b1d7ee82.jpg', 'ABRIGANA', 'PAMELA', '', '', 26, 3, '06/18/2025', '03:30 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(457, '03THKL40WG', 'personnelImg/default_img.jpg', '68526b237f102.jpg', 'ABUGAN', 'ARMELITO', '', '', 26, 3, '06/18/2025', '03:30 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(458, 'CY3EN2VVKO', 'personnelImg/default_img.jpg', '68526b327be3d.jpg', 'ACADEMIA', 'MELINDA', '', '', 26, 3, '06/18/2025', '03:30 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(459, 'TXNUYSMIPH', 'personnelImg/default_img.jpg', '68526b477f085.jpg', 'ACHA', 'ANGELA', '', '', 26, 3, '06/18/2025', '03:31 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(460, 'UV0LOFMFS3', 'personnelImg/default_img.jpg', '68526c427c596.jpg', 'LOGRONIO', 'JOESIFIL', 'FAJARDO', '', 2, 3, '06/18/2025', '03:35 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(461, 'D4ZWVFKO1E', 'personnelImg/default_img.jpg', '68526c7d7bdf9.jpg', 'LLAMADO', 'MARISSA', 'MAQUILING', '', 2, 3, '06/18/2025', '03:36 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(462, 'IO1IY5L4VB', 'personnelImg/default_img.jpg', '68526c877c13a.jpg', 'CORTADO', 'ROGELIO', 'ALFANTA', '', 2, 3, '06/18/2025', '03:36 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(463, 'N2CS52WG6G', 'personnelImg/default_img.jpg', '68526c8e7d727.jpg', 'GIGANAN', 'GRACE JOY', 'ESTRAO', '', 2, 3, '06/18/2025', '03:36 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(464, 'RK02GLHBHR', 'personnelImg/default_img.jpg', '68526c9981461.jpg', 'PINONGGAN', 'JOSEPH', 'ALBERIO', '', 2, 3, '06/18/2025', '03:36 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(465, 'RK02GLHBHR', 'personnelImg/default_img.jpg', '68526e9a7cb7c.jpg', 'PINONGGAN', 'JOSEPH', 'ALBERIO', '', 2, 3, '06/18/2025', '03:45 PM', 0, 'on', 'PM OUT', '192.168.1.18', '', '', 0),
(466, 'N2CS52WG6G', 'personnelImg/default_img.jpg', '68526ee97d353.jpg', 'GIGANAN', 'GRACE JOY', 'ESTRAO', '', 2, 3, '06/18/2025', '03:46 PM', 0, 'on', 'PM OUT', '192.168.1.18', '', '', 0),
(467, 'IO1IY5L4VB', 'personnelImg/default_img.jpg', '68526efb7eb43.jpg', 'CORTADO', 'ROGELIO', 'ALFANTA', '', 2, 3, '06/18/2025', '03:47 PM', 0, 'on', 'PM OUT', '192.168.1.18', '', '', 0),
(468, 'D4ZWVFKO1E', 'personnelImg/default_img.jpg', '68526f067ea91.jpg', 'LLAMADO', 'MARISSA', 'MAQUILING', '', 2, 3, '06/18/2025', '03:47 PM', 0, 'on', 'PM OUT', '192.168.1.18', '', '', 0),
(469, 'UV0LOFMFS3', 'personnelImg/default_img.jpg', '68526f0e7ea68.jpg', 'LOGRONIO', 'JOESIFIL', 'FAJARDO', '', 2, 3, '06/18/2025', '03:47 PM', 0, 'on', 'PM OUT', '192.168.1.18', '', '', 0),
(470, 'KCTG5YC64S', 'personnelImg/default_img.jpg', '68526f948010a.jpg', 'TUBILLEJA', 'THEODORE', 'DELA CRUZ', 'SR. ', 24, 3, '06/18/2025', '03:49 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(471, 'WEYJU63LLE', 'personnelImg/default_img.jpg', '68526fa07c5ca.jpg', 'BILBAO', 'MA. TERESA', 'LOCSIN', '', 24, 3, '06/18/2025', '03:49 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(472, 'WXV3CGLSBO', 'personnelImg/default_img.jpg', '68526fa87cad2.jpg', 'GAYOMALE', 'JOSE ROBERT', 'MILLAN', 'JR. ', 24, 3, '06/18/2025', '03:49 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(473, 'E6FJPBZ5BD', 'personnelImg/default_img.jpg', '68526fb47e943.jpg', 'TUPAS', 'JASON', 'TEMBREVILLA', '', 24, 3, '06/18/2025', '03:50 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(474, 'SPRSLW2YQM', 'personnelImg/default_img.jpg', '68526fbf7f80d.jpg', 'OCTAVIO', 'JODYBONNE', 'GAYATIN', '', 24, 3, '06/18/2025', '03:50 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(475, 'KE4HXNTQTI', 'personnelImg/default_img.jpg', '68526fc77fc07.jpg', 'BILBAO', 'FRANCISCO JOSE', 'LOCSIN', 'JR. ', 24, 3, '06/18/2025', '03:50 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(476, '55SDU3PJSI', 'personnelImg/default_img.jpg', '68526fcd7db38.jpg', 'ENCOY', 'JEFRE', 'LAZALITA', '', 24, 3, '06/18/2025', '03:50 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(477, '1Z6LX20PVU', 'personnelImg/default_img.jpg', '68526fd77f690.jpg', 'RELIQUIAS', 'JOHN MARK', 'BALUNGCAS', '', 23, 3, '06/18/2025', '03:50 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(478, 'BCUT6421HG', 'personnelImg/default_img.jpg', '68526fde7c5c8.jpg', 'CANDULIZAS', 'MOLAVE', 'TEMBREVILLA', '', 2, 3, '06/18/2025', '03:50 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(479, 'T1X1EOAO50', 'personnelImg/default_img.jpg', '68526fe57c708.jpg', 'HERRADURA', 'GINALYN ', 'OBENZA', '', 2, 3, '06/18/2025', '03:51 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(480, 'NT36Z4UITB', 'personnelImg/default_img.jpg', '685270bf7d743.jpg', 'MALAYO', 'EDGARDO', 'FLORES', '', 24, 3, '06/18/2025', '03:54 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(481, '44SB4W0MF3', 'personnelImg/default_img.jpg', '685270e2820ca.jpg', 'VIDAURRAZAGA', 'MC', 'PEPITO', '', 24, 3, '06/18/2025', '03:55 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(482, '6DZNSFDRJG', 'personnelImg/default_img.jpg', '685270ef7c384.jpg', 'MANOS', 'TEOFILO', 'ENCARGUEZ', 'JR. ', 24, 3, '06/18/2025', '03:55 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(483, 'ZGMTZ2Y3BT', 'personnelImg/default_img.jpg', '685270f680c0d.jpg', 'OCTAVIO', 'PETER JOHN ', 'LAZALITA', '', 24, 3, '06/18/2025', '03:55 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(484, 'DCORCD1ICP', 'personnelImg/default_img.jpg', '685271257ce8d.jpg', 'TUMA-OB', 'LESLIE AIKEE DYAN', 'GIGANAN', '', 24, 3, '06/18/2025', '03:56 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(485, 'BSY3X4CY3E', 'personnelImg/default_img.jpg', '6852714780751.jpg', 'SANTES', 'JOCELYN', 'CORONEL', '', 24, 3, '06/18/2025', '03:56 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(486, 'WQ4X6QJYGH', 'personnelImg/default_img.jpg', '685271527ed52.jpg', 'ANLIQUERA', 'SIENA', 'VASQUEZ', '', 26, 3, '06/18/2025', '03:57 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(487, 'HSDQZ6G6PA', 'personnelImg/default_img.jpg', '6852716081c6b.jpg', 'PEREZ', 'JOSE MARIA', 'LIM', 'JR. ', 12, 3, '06/18/2025', '03:57 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(488, '1QETKAFQWO', 'personnelImg/default_img.jpg', '6852716c7d8c6.jpg', 'DELOTINA', 'JOFEL', 'EVANGELISTA', '', 24, 3, '06/18/2025', '03:57 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(489, '2FUFQBHUQM', 'personnelImg/default_img.jpg', '685271777d976.jpg', 'DECENA', 'LANNE', 'VILLARETE', '', 24, 3, '06/18/2025', '03:57 PM', 0, 'on', 'PM OUT', '192.168.1.18', '', '', 0),
(490, 'DFR5QGBYHW', 'personnelImg/default_img.jpg', '6852723182979.jpg', 'BARO', 'PHOEBE', 'MANILINGAN', '', 12, 3, '06/18/2025', '04:00 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(491, 'QESM444ZK0', 'personnelImg/default_img.jpg', '6852723c7fae4.jpg', 'TEMBREVILLA', 'RONA', 'ESCOSAR', '', 12, 3, '06/18/2025', '04:00 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(492, 'V2M0IIAYRM', 'personnelImg/default_img.jpg', '685272577e78f.jpg', 'VILLANUEVA', 'RAYGILDA', 'VERGARA', '', 12, 3, '06/18/2025', '04:01 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(493, 'ZV1EXSSS30', 'personnelImg/default_img.jpg', '685272867f5e4.jpg', 'BONGCAWEL', 'GENELYN ', 'HISONA', '', 12, 3, '06/18/2025', '04:02 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(494, 'HDMCPVKYB1', 'personnelImg/default_img.jpg', '685272927e47a.jpg', 'ROXAS', 'MARY ANN', 'VILLAFUERTE', '', 12, 3, '06/18/2025', '04:02 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(495, 'MS5LRO1IJE', 'personnelImg/default_img.jpg', '685272a57ba09.jpg', 'VILLANUEVA', 'ROSALIE', 'ELARDO', '', 12, 3, '06/18/2025', '04:02 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(496, 'I03I6VZELI', 'personnelImg/default_img.jpg', '685272b482f35.jpg', 'GALAN', 'MARY JANE', 'CELIS', '', 12, 3, '06/18/2025', '04:02 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(497, 'LVX5C46TAS', 'personnelImg/default_img.jpg', '685272c47eab4.jpg', 'SILVESTRE', 'CYNTHIA', 'LAMBOT', '', 12, 3, '06/18/2025', '04:03 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(498, 'ZXDNHZFPMW', 'personnelImg/default_img.jpg', '685272d07cde4.jpg', 'PUBLICO', 'NOEMI', 'TOMADO', '', 12, 3, '06/18/2025', '04:03 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(499, 'LUPQ4RIQUJ', 'personnelImg/default_img.jpg', '685272d87f3c7.jpg', 'TILOS', 'RIZALIE', 'CELIZ', '', 12, 3, '06/18/2025', '04:03 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(500, 'I0FY3RFXM5', 'personnelImg/default_img.jpg', '685273eb8229c.jpg', 'TILOS', 'OSCAR', 'VILLARETE', '', 4, 3, '06/18/2025', '04:08 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(501, 'WSJIHMXEN6', 'personnelImg/default_img.jpg', '685273fc7d3a7.jpg', 'TILOS', 'MARCELINA', 'CELIS', '', 12, 3, '06/18/2025', '04:08 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(502, 'ZHXY3CO3KQ', 'personnelImg/default_img.jpg', '685274057d3c4.jpg', 'DELA FUENTE', 'EBER', 'ORMEO', '', 12, 3, '06/18/2025', '04:08 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(503, 'MHDZI160KN', 'personnelImg/default_img.jpg', '6852741280511.jpg', 'TEMBREVILLA', 'CAROLINE', 'CANILLO', '', 12, 3, '06/18/2025', '04:08 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(504, '2O1JSGHAYX', 'personnelImg/default_img.jpg', '6852741a7b0d0.jpg', 'GAUAL', 'ROLIN', 'BERGONIO', 'SR. ', 12, 3, '06/18/2025', '04:08 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(505, 'HEPAB6X3PL', 'personnelImg/default_img.jpg', '6852742582fcf.jpg', 'VILLAMATER', 'LORALYN', 'BARROCA', '', 12, 3, '06/18/2025', '04:09 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(506, 'GZ5LO0QDRC', 'personnelImg/default_img.jpg', '6852743381c30.jpg', 'GARCIA', 'ADELA', 'ANTOLIN', '', 12, 3, '06/18/2025', '04:09 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(507, '5UVF66ZPY0', 'personnelImg/default_img.jpg', '6852743d7af31.jpg', 'MAQUILING', 'WARREN', 'RAMIREZ', '', 12, 3, '06/18/2025', '04:09 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(508, 'RCCO5TPACC', 'personnelImg/default_img.jpg', '685274447db37.jpg', 'PIMENTEL', 'EMY', 'MAGBANUA', '', 12, 3, '06/18/2025', '04:09 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(509, '6ON2RWCQEM', 'personnelImg/default_img.jpg', '6852745782766.jpg', 'GAREZA', 'MELANIE', 'CELIS', '', 12, 3, '06/18/2025', '04:09 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(510, 'NZ0IKZI1IR', 'personnelImg/default_img.jpg', '685274f17e886.jpg', 'MANGOGTONG', 'MA. MARILOU ', 'ESTRELLA', '', 10, 3, '06/18/2025', '04:12 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(511, 'CEPHX3JOM0', 'personnelImg/default_img.jpg', '685274fe7c9ca.jpg', 'TIBAYDE', 'IRENE', 'GIGANAN', '', 10, 3, '06/18/2025', '04:12 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(512, 'KZFCP2S0HT', 'personnelImg/default_img.jpg', '6852750c7f37d.jpg', 'GUINTOS', 'JOSEPHINE', 'INDINO', '', 10, 3, '06/18/2025', '04:12 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(513, 'APPZCXZCJS', 'personnelImg/default_img.jpg', '6852751b7e174.jpg', 'MIRANDA', 'JAIME', 'MAULIT', '', 5, 3, '06/18/2025', '04:13 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(514, '0RVK3VBCZ1', 'personnelImg/default_img.jpg', '6852752d7d39e.jpg', 'MANOS', 'ANNABELLE', 'PERIGUA', '', 5, 3, '06/18/2025', '04:13 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(515, 'N3CFWPHFQX', 'personnelImg/default_img.jpg', '6852753d7c8cd.jpg', 'SANTES', 'CAROL ANN', 'TILOS', '', 5, 3, '06/18/2025', '04:13 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(516, 'DO2X14YKJR', 'personnelImg/default_img.jpg', '685275487b672.jpg', 'VILLARUBIA', 'ALTHEA', 'PIA', '', 5, 3, '06/18/2025', '04:13 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(517, 'MF4R0LP0PT', 'personnelImg/default_img.jpg', '685275547caa3.jpg', 'TOLEDO', 'RAYMUND ANTHONY', 'INOLINO', '', 4, 3, '06/18/2025', '04:14 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(518, 'LTQHD2BPYJ', 'personnelImg/default_img.jpg', '685275697cafd.jpg', 'GALON', 'RANDOLF', 'BLANCO', '', 4, 3, '06/18/2025', '04:14 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(519, '2SOQJG3P6F', 'personnelImg/default_img.jpg', '685275747e651.jpg', 'SANTES', 'CINDY', 'REBOLDAL', '', 4, 3, '06/18/2025', '04:14 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(520, 'VUM5AWYEUH', 'personnelImg/default_img.jpg', '6852763f7de6b.jpg', 'SUMUGAT', 'JENELYN', 'TELONIO', '', 3, 3, '06/18/2025', '04:18 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(521, 'KDV12JKLNR', 'personnelImg/default_img.jpg', '685276547b3ab.jpg', 'TRINIO-SANTES', 'VANESSA', 'LIRAZAN', '', 3, 3, '06/18/2025', '04:18 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(522, 'C4XW3NQ3CE', 'personnelImg/default_img.jpg', '685276697e121.jpg', 'ACADEMIA', 'RIZALYN', 'CAYANAN', '', 9, 3, '06/18/2025', '04:18 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(523, '2XYVUZG44K', 'personnelImg/default_img.jpg', '685276777ee53.jpg', 'RELIQUIAS', 'JOERIBEL', 'TORIANO', '', 9, 3, '06/18/2025', '04:19 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(524, 'N0PELGBXHV', 'personnelImg/default_img.jpg', '6852768e7d965.jpg', 'GUSTILO', 'LEILANI', 'DECENA', '', 9, 3, '06/18/2025', '04:19 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(525, 'HR1TD1EOO6', 'personnelImg/default_img.jpg', '68527696801de.jpg', 'NATALIO', 'JOEFREY', 'TEMBREVILLA', '', 9, 3, '06/18/2025', '04:19 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(526, 'KVKMFQDZDV', 'personnelImg/default_img.jpg', '685276a1830c0.jpg', 'GA-AN', 'LENLY', 'NOSAL', '', 10, 3, '06/18/2025', '04:19 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(527, 'ES1MY5CB5N', 'personnelImg/default_img.jpg', '685276ac7f1c0.jpg', 'BUDACA', 'REVINIA', 'AMACIO', '', 10, 3, '06/18/2025', '04:19 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(528, '2KLYB0WHDY', 'personnelImg/default_img.jpg', '685276b77d01e.jpg', 'GESTOSO', 'CARLITO', 'BAVIERA', '', 10, 3, '06/18/2025', '04:20 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(529, 'OSYDS2VNEW', 'personnelImg/default_img.jpg', '685276d47d897.jpg', 'ABALLE', 'BETHEL', 'PERFUMA', '', 10, 3, '06/18/2025', '04:20 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(530, 'K0GBVLRIOM', 'personnelImg/default_img.jpg', '685277c27f1f5.jpg', 'JORDAN', 'MARK ANTHONY', 'TOMADO', '', 3, 3, '06/18/2025', '04:24 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(531, 'V4PAJMJ5YO', 'personnelImg/default_img.jpg', '685277d07d9cb.jpg', 'TUPAS', 'OFELIA', 'TEMBREVILLA', '', 6, 3, '06/18/2025', '04:24 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(532, 'DURSCXCSIM', 'personnelImg/default_img.jpg', '685277dc8087b.jpg', 'MAYANDIA', 'ANALIE', 'ELARMO', '', 6, 3, '06/18/2025', '04:24 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(533, 'EAUNV5WVM0', 'personnelImg/default_img.jpg', '685277eb7b74e.jpg', 'BARROCA', 'JORGIE', 'PERFUMA', '', 6, 3, '06/18/2025', '04:25 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(534, 'UBFPHM020G', 'personnelImg/default_img.jpg', '685277ff7cd1d.jpg', 'BONILLA', 'ANNI VER', 'PAMLIEGA', '', 6, 3, '06/18/2025', '04:25 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(535, 'B42WBNMNSK', 'personnelImg/default_img.jpg', '6852780e7b5c0.jpg', 'LIMSIACO', 'ARIANE', 'CONSTANTINO', '', 6, 3, '06/18/2025', '04:25 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(536, 'EHEYDZ1UYN', 'personnelImg/default_img.jpg', '6852781b7dc1c.jpg', 'ALATON', 'GINA', 'NABOR', '', 6, 3, '06/18/2025', '04:26 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(537, 'DGPF5FDK53', 'personnelImg/default_img.jpg', '685278287e2b1.jpg', 'ARANETA', 'JOSELITA', 'GENTELIZO', '', 6, 3, '06/18/2025', '04:26 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(538, 'N4DZR4BBY0', 'personnelImg/default_img.jpg', '685278357e14f.jpg', 'RELIQUIAS', 'MARIA FE', 'GUINTOS', '', 8, 3, '06/18/2025', '04:26 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(539, 'CMDUXPZ44I', 'personnelImg/default_img.jpg', '6852783d7e577.jpg', 'GONZAL', 'SANDRA', 'CASTILLO', '', 8, 3, '06/18/2025', '04:26 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(540, 'EAUNV5WVM0', 'personnelImg/default_img.jpg', '68527a038664b.jpg', 'BARROCA', 'JORGIE', 'PERFUMA', '', 6, 3, '06/18/2025', '04:34 PM', 0, 'off', 'PM OUT', '192.168.1.18', '', '', 0),
(541, 'B42WBNMNSK', 'personnelImg/default_img.jpg', '68527cb57bd53.jpg', 'LIMSIACO', 'ARIANE', 'CONSTANTINO', '', 6, 3, '06/18/2025', '04:45 PM', 0, 'off', 'PM OUT', '192.168.1.18', '', '', 0),
(542, 'RK02GLHBHR', 'personnelImg/default_img.jpg', '68535ad0d722c.jpg', 'PINONGGAN', 'JOSEPH', 'ALBERIO', '', 2, 3, '06/19/2025', '08:33 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(543, 'PLCEEHAUGT', 'personnelImg/default_img.jpg', '68535ae3c6305.jpg', 'MARQUEZ', 'MAE ANN', 'GIGANAN', '', 15, 3, '06/19/2025', '08:33 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(544, 'FU6VB3OYK4', 'personnelImg/default_img.jpg', '68535b0dc49cb.jpg', 'MANGOGTONG', 'WILMAR', 'ESTRELLA', '', 14, 3, '06/19/2025', '08:34 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(545, 'GSQZ2OCVHQ', 'personnelImg/default_img.jpg', '68535b1ac56d0.jpg', 'RELIQUIAS', 'GERALD', 'TORIANO', '', 14, 3, '06/19/2025', '08:34 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(546, 'O6JWSQ5BTR', 'personnelImg/default_img.jpg', '68535b7dc4602.jpg', 'DELA CONCEPTION', 'IMELDA', 'ESPENORIO', '', 14, 3, '06/19/2025', '08:36 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(547, '4RE6HDPT3R', 'personnelImg/default_img.jpg', '68535ba7c8432.jpg', 'JIMENEZ', 'LEO', 'TOMILBA', '', 14, 3, '06/19/2025', '08:36 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(548, '2SJBOJTXPC', 'personnelImg/default_img.jpg', '68535bedc6bba.jpg', 'GELLECANAO', 'MURIELLE', 'LIRAZAN', '', 7, 3, '06/19/2025', '08:38 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(549, '1DSSFVBIFF', 'personnelImg/default_img.jpg', '68535c33c56a5.jpg', 'MAESTRECAMPO', 'EVELYN', 'ELARMO', '', 7, 3, '06/19/2025', '08:39 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(550, 'EAUNV5WVM0', 'personnelImg/default_img.jpg', '68535c61c4529.jpg', 'BARROCA', 'JORGIE', 'PERFUMA', '', 6, 3, '06/19/2025', '08:40 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(551, 'MF4R0LP0PT', 'personnelImg/default_img.jpg', '68535c6cc4abe.jpg', 'TOLEDO', 'RAYMUND ANTHONY', 'INOLINO', '', 4, 3, '06/19/2025', '08:40 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(552, 'D4ZWVFKO1E', 'personnelImg/default_img.jpg', '68535c79c2bd2.jpg', 'LLAMADO', 'MARISSA', 'MAQUILING', '', 2, 3, '06/19/2025', '08:40 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(553, 'UV0LOFMFS3', 'personnelImg/default_img.jpg', '68535c81c9397.jpg', 'LOGRONIO', 'JOESIFIL', 'FAJARDO', '', 2, 3, '06/19/2025', '08:40 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(554, 'DO2X14YKJR', 'personnelImg/default_img.jpg', '68535c87c3532.jpg', 'VILLARUBIA', 'ALTHEA', 'PIA', '', 5, 3, '06/19/2025', '08:40 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(555, 'N3CFWPHFQX', 'personnelImg/default_img.jpg', '68535c90c5426.jpg', 'SANTES', 'CAROL ANN', 'TILOS', '', 5, 3, '06/19/2025', '08:40 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(556, '0RVK3VBCZ1', 'personnelImg/default_img.jpg', '68535c97c3138.jpg', 'MANOS', 'ANNABELLE', 'PERIGUA', '', 5, 3, '06/19/2025', '08:40 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(557, 'APPZCXZCJS', 'personnelImg/default_img.jpg', '68535c9ec260d.jpg', 'MIRANDA', 'JAIME', 'MAULIT', '', 5, 3, '06/19/2025', '08:41 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(558, 'KZFCP2S0HT', 'personnelImg/default_img.jpg', '68535ca6c46c7.jpg', 'GUINTOS', 'JOSEPHINE', 'INDINO', '', 10, 3, '06/19/2025', '08:41 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0);
INSERT INTO `personnel_logs` (`log_id`, `RFTag_id`, `img`, `captured_img`, `lname`, `fname`, `mname`, `suffix`, `do_id`, `shift_id`, `logDate`, `logTime`, `logTime_sec`, `late_status`, `logFlow`, `client_ip`, `remarks`, `travel_leave_code`, `ref_log_id`) VALUES
(559, 'CEPHX3JOM0', 'personnelImg/default_img.jpg', '68535cabc377c.jpg', 'TIBAYDE', 'IRENE', 'GIGANAN', '', 10, 3, '06/19/2025', '08:41 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(560, 'NZ0IKZI1IR', 'personnelImg/default_img.jpg', '68535cbac6b91.jpg', 'MANGOGTONG', 'MA. MARILOU ', 'ESTRELLA', '', 10, 3, '06/19/2025', '08:41 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(561, '6ON2RWCQEM', 'personnelImg/default_img.jpg', '68535cc2c2cec.jpg', 'GAREZA', 'MELANIE', 'CELIS', '', 12, 3, '06/19/2025', '08:41 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(562, 'RCCO5TPACC', 'personnelImg/default_img.jpg', '68535cc9c2891.jpg', 'PIMENTEL', 'EMY', 'MAGBANUA', '', 12, 3, '06/19/2025', '08:41 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(563, '5UVF66ZPY0', 'personnelImg/default_img.jpg', '68535cd4c1c99.jpg', 'MAQUILING', 'WARREN', 'RAMIREZ', '', 12, 3, '06/19/2025', '08:41 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(564, 'GZ5LO0QDRC', 'personnelImg/default_img.jpg', '68535cdac32b5.jpg', 'GARCIA', 'ADELA', 'ANTOLIN', '', 12, 3, '06/19/2025', '08:42 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(565, 'HEPAB6X3PL', 'personnelImg/default_img.jpg', '68535ce1c2b93.jpg', 'VILLAMATER', 'LORALYN', 'BARROCA', '', 12, 3, '06/19/2025', '08:42 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(566, '2O1JSGHAYX', 'personnelImg/default_img.jpg', '68535ce7c3026.jpg', 'GAUAL', 'ROLIN', 'BERGONIO', 'SR. ', 12, 3, '06/19/2025', '08:42 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(567, 'MHDZI160KN', 'personnelImg/default_img.jpg', '68535ceec68c8.jpg', 'TEMBREVILLA', 'CAROLINE', 'CANILLO', '', 12, 3, '06/19/2025', '08:42 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(568, 'ZHXY3CO3KQ', 'personnelImg/default_img.jpg', '68535cf6c3900.jpg', 'DELA FUENTE', 'EBER', 'ORMEO', '', 12, 3, '06/19/2025', '08:42 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(569, 'WSJIHMXEN6', 'personnelImg/default_img.jpg', '68535cfcc3f44.jpg', 'TILOS', 'MARCELINA', 'CELIS', '', 12, 3, '06/19/2025', '08:42 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(570, 'I0FY3RFXM5', 'personnelImg/default_img.jpg', '68535d01c58d1.jpg', 'TILOS', 'OSCAR', 'VILLARETE', '', 4, 3, '06/19/2025', '08:42 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(571, 'LUPQ4RIQUJ', 'personnelImg/default_img.jpg', '68535d0ac581f.jpg', 'TILOS', 'RIZALIE', 'CELIZ', '', 12, 3, '06/19/2025', '08:42 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(572, 'ZXDNHZFPMW', 'personnelImg/default_img.jpg', '68535d0ec69b4.jpg', 'PUBLICO', 'NOEMI', 'TOMADO', '', 12, 3, '06/19/2025', '08:42 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(573, 'LVX5C46TAS', 'personnelImg/default_img.jpg', '68535d12c1985.jpg', 'SILVESTRE', 'CYNTHIA', 'LAMBOT', '', 12, 3, '06/19/2025', '08:42 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(574, 'I03I6VZELI', 'personnelImg/default_img.jpg', '68535d19c4078.jpg', 'GALAN', 'MARY JANE', 'CELIS', '', 12, 3, '06/19/2025', '08:43 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(575, 'MS5LRO1IJE', 'personnelImg/default_img.jpg', '68535d1dc5df1.jpg', 'VILLANUEVA', 'ROSALIE', 'ELARDO', '', 12, 3, '06/19/2025', '08:43 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(576, 'HDMCPVKYB1', 'personnelImg/default_img.jpg', '68535d20c25f5.jpg', 'ROXAS', 'MARY ANN', 'VILLAFUERTE', '', 12, 3, '06/19/2025', '08:43 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(577, 'ZV1EXSSS30', 'personnelImg/default_img.jpg', '68535d26c3fcb.jpg', 'BONGCAWEL', 'GENELYN ', 'HISONA', '', 12, 3, '06/19/2025', '08:43 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(578, 'V2M0IIAYRM', 'personnelImg/default_img.jpg', '68535d2cc1f07.jpg', 'VILLANUEVA', 'RAYGILDA', 'VERGARA', '', 12, 3, '06/19/2025', '08:43 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(579, 'QESM444ZK0', 'personnelImg/default_img.jpg', '68535d36c4ebe.jpg', 'TEMBREVILLA', 'RONA', 'ESCOSAR', '', 12, 3, '06/19/2025', '08:43 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(580, 'DFR5QGBYHW', 'personnelImg/default_img.jpg', '68535d3bc1cff.jpg', 'BARO', 'PHOEBE', 'MANILINGAN', '', 12, 3, '06/19/2025', '08:43 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(581, '2FUFQBHUQM', 'personnelImg/default_img.jpg', '68535d3fc60ea.jpg', 'DECENA', 'LANNE', 'VILLARETE', '', 24, 3, '06/19/2025', '08:43 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(582, '1QETKAFQWO', 'personnelImg/default_img.jpg', '68535d46c5cb6.jpg', 'DELOTINA', 'JOFEL', 'EVANGELISTA', '', 24, 3, '06/19/2025', '08:43 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(583, 'HSDQZ6G6PA', 'personnelImg/default_img.jpg', '68535d4cc3b03.jpg', 'PEREZ', 'JOSE MARIA', 'LIM', 'JR. ', 12, 3, '06/19/2025', '08:43 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(584, 'WQ4X6QJYGH', 'personnelImg/default_img.jpg', '68535d51c4a35.jpg', 'ANLIQUERA', 'SIENA', 'VASQUEZ', '', 26, 3, '06/19/2025', '08:44 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(585, 'BSY3X4CY3E', 'personnelImg/default_img.jpg', '68535d5ec564a.jpg', 'SANTES', 'JOCELYN', 'CORONEL', '', 24, 3, '06/19/2025', '08:44 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(586, 'DCORCD1ICP', 'personnelImg/default_img.jpg', '68535d6ac4ef5.jpg', 'TUMA-OB', 'LESLIE AIKEE DYAN', 'GIGANAN', '', 24, 3, '06/19/2025', '08:44 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(587, 'ZGMTZ2Y3BT', 'personnelImg/default_img.jpg', '68535d82c4cc0.jpg', 'OCTAVIO', 'PETER JOHN ', 'LAZALITA', '', 24, 3, '06/19/2025', '08:44 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(588, 'NT36Z4UITB', 'personnelImg/default_img.jpg', '68535d8cc5b9b.jpg', 'MALAYO', 'EDGARDO', 'FLORES', '', 24, 3, '06/19/2025', '08:44 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(589, '44SB4W0MF3', 'personnelImg/default_img.jpg', '68535d95c2c61.jpg', 'VIDAURRAZAGA', 'MC', 'PEPITO', '', 24, 3, '06/19/2025', '08:45 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(590, '6DZNSFDRJG', 'personnelImg/default_img.jpg', '68535e0ac604e.jpg', 'MANOS', 'TEOFILO', 'ENCARGUEZ', 'JR. ', 24, 3, '06/19/2025', '08:47 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(591, 'T1X1EOAO50', 'personnelImg/default_img.jpg', '68535e12c86ce.jpg', 'HERRADURA', 'GINALYN ', 'OBENZA', '', 2, 3, '06/19/2025', '08:47 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(592, 'BCUT6421HG', 'personnelImg/default_img.jpg', '68535e23c4217.jpg', 'CANDULIZAS', 'MOLAVE', 'TEMBREVILLA', '', 2, 3, '06/19/2025', '08:47 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(593, '1Z6LX20PVU', 'personnelImg/default_img.jpg', '68535e2fc5cb9.jpg', 'RELIQUIAS', 'JOHN MARK', 'BALUNGCAS', '', 23, 3, '06/19/2025', '08:47 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(594, '55SDU3PJSI', 'personnelImg/default_img.jpg', '68535e36c85d7.jpg', 'ENCOY', 'JEFRE', 'LAZALITA', '', 24, 3, '06/19/2025', '08:47 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(595, 'KE4HXNTQTI', 'personnelImg/default_img.jpg', '68535e3bc15b8.jpg', 'BILBAO', 'FRANCISCO JOSE', 'LOCSIN', 'JR. ', 24, 3, '06/19/2025', '08:47 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(596, 'SPRSLW2YQM', 'personnelImg/default_img.jpg', '68535e44c541d.jpg', 'OCTAVIO', 'JODYBONNE', 'GAYATIN', '', 24, 3, '06/19/2025', '08:48 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(597, 'E6FJPBZ5BD', 'personnelImg/default_img.jpg', '68535e4bc3518.jpg', 'TUPAS', 'JASON', 'TEMBREVILLA', '', 24, 3, '06/19/2025', '08:48 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(598, 'WXV3CGLSBO', 'personnelImg/default_img.jpg', '68535e4fc5b17.jpg', 'GAYOMALE', 'JOSE ROBERT', 'MILLAN', 'JR. ', 24, 3, '06/19/2025', '08:48 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(599, 'WEYJU63LLE', 'personnelImg/default_img.jpg', '68535e55c53ef.jpg', 'BILBAO', 'MA. TERESA', 'LOCSIN', '', 24, 3, '06/19/2025', '08:48 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(600, 'UV0LOFMFS3', 'personnelImg/default_img.jpg', '68535e5ac35cb.jpg', 'LOGRONIO', 'JOESIFIL', 'FAJARDO', '', 2, 3, '06/19/2025', '08:48 AM', 31705, 'on', 'AM OUT', '192.168.1.2', '', '', 0),
(601, 'KCTG5YC64S', 'personnelImg/default_img.jpg', '68535e5ec5aec.jpg', 'TUBILLEJA', 'THEODORE', 'DELA CRUZ', 'SR. ', 24, 3, '06/19/2025', '08:48 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(602, 'D4ZWVFKO1E', 'personnelImg/default_img.jpg', '68535e66c351c.jpg', 'LLAMADO', 'MARISSA', 'MAQUILING', '', 2, 3, '06/19/2025', '08:48 AM', 31717, 'on', 'AM OUT', '192.168.1.2', '', '', 0),
(603, 'IO1IY5L4VB', 'personnelImg/default_img.jpg', '68535e6ec585d.jpg', 'CORTADO', 'ROGELIO', 'ALFANTA', '', 2, 3, '06/19/2025', '08:48 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(604, 'N2CS52WG6G', 'personnelImg/default_img.jpg', '68535e74c6fe5.jpg', 'GIGANAN', 'GRACE JOY', 'ESTRAO', '', 2, 3, '06/19/2025', '08:48 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(605, 'RK02GLHBHR', 'personnelImg/default_img.jpg', '68535e7bc4dc2.jpg', 'PINONGGAN', 'JOSEPH', 'ALBERIO', '', 2, 3, '06/19/2025', '08:48 AM', 31738, 'on', 'AM OUT', '192.168.1.2', '', '', 0),
(606, 'B42WBNMNSK', 'personnelImg/default_img.jpg', '68535e8ec4382.jpg', 'LIMSIACO', 'ARIANE', 'CONSTANTINO', '', 6, 3, '06/19/2025', '08:49 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(607, 'PQMSBLBNJ6', 'personnelImg/default_img.jpg', '68535ea5c5a95.jpg', 'VIDAURRAZAGA', 'NOAH', 'VASQUEZ', '', 2, 3, '06/19/2025', '08:49 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(608, 'F255HSX3FM', 'personnelImg/default_img.jpg', '68535eb1c32e2.jpg', 'GUINTOS', 'ERICA', 'MANANGAN', '', 14, 3, '06/19/2025', '08:49 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(609, 'PMOLWNYZZA', 'personnelImg/default_img.jpg', '68535ec0c77e3.jpg', 'SUSANA', 'FREDERICK DAVY', 'PINGCALE', '', 12, 3, '06/19/2025', '08:50 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(610, 'OIMV5KOLF2', 'personnelImg/default_img.jpg', '68535ec5c26d4.jpg', 'PEROSIA', 'GLORY MAE', 'TUNDA-AN', '', 12, 3, '06/19/2025', '08:50 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(611, 'UBFPHM020G', 'personnelImg/default_img.jpg', '68535ecdc2e65.jpg', 'BONILLA', 'ANNI VER', 'PAMLIEGA', '', 6, 3, '06/19/2025', '08:50 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(612, 'CMDUXPZ44I', 'personnelImg/default_img.jpg', '68535ed7c3158.jpg', 'GONZAL', 'SANDRA', 'CASTILLO', '', 8, 3, '06/19/2025', '08:50 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(613, 'N4DZR4BBY0', 'personnelImg/default_img.jpg', '68535edbc393c.jpg', 'RELIQUIAS', 'MARIA FE', 'GUINTOS', '', 8, 3, '06/19/2025', '08:50 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(614, 'DGPF5FDK53', 'personnelImg/default_img.jpg', '68535ee1c8a30.jpg', 'ARANETA', 'JOSELITA', 'GENTELIZO', '', 6, 3, '06/19/2025', '08:50 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(615, 'EHEYDZ1UYN', 'personnelImg/default_img.jpg', '68535ee7c9608.jpg', 'ALATON', 'GINA', 'NABOR', '', 6, 3, '06/19/2025', '08:50 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(616, 'DURSCXCSIM', 'personnelImg/default_img.jpg', '68535ef0c8790.jpg', 'MAYANDIA', 'ANALIE', 'ELARMO', '', 6, 3, '06/19/2025', '08:50 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(617, 'V4PAJMJ5YO', 'personnelImg/default_img.jpg', '68535efcc6467.jpg', 'TUPAS', 'OFELIA', 'TEMBREVILLA', '', 6, 3, '06/19/2025', '08:51 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(618, 'K0GBVLRIOM', 'personnelImg/default_img.jpg', '68535f05c3800.jpg', 'JORDAN', 'MARK ANTHONY', 'TOMADO', '', 3, 3, '06/19/2025', '08:51 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(619, 'OSYDS2VNEW', 'personnelImg/default_img.jpg', '68535f0bc41fd.jpg', 'ABALLE', 'BETHEL', 'PERFUMA', '', 10, 3, '06/19/2025', '08:51 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(620, '2KLYB0WHDY', 'personnelImg/default_img.jpg', '68535f12c5add.jpg', 'GESTOSO', 'CARLITO', 'BAVIERA', '', 10, 3, '06/19/2025', '08:51 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(621, 'ES1MY5CB5N', 'personnelImg/default_img.jpg', '68535f18c690d.jpg', 'BUDACA', 'REVINIA', 'AMACIO', '', 10, 3, '06/19/2025', '08:51 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(622, 'KVKMFQDZDV', 'personnelImg/default_img.jpg', '68535f24c43bf.jpg', 'GA-AN', 'LENLY', 'NOSAL', '', 10, 3, '06/19/2025', '08:51 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(623, 'HR1TD1EOO6', 'personnelImg/default_img.jpg', '68535f2ccb346.jpg', 'NATALIO', 'JOEFREY', 'TEMBREVILLA', '', 9, 3, '06/19/2025', '08:51 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(624, 'N0PELGBXHV', 'personnelImg/default_img.jpg', '68535f32c2c60.jpg', 'GUSTILO', 'LEILANI', 'DECENA', '', 9, 3, '06/19/2025', '08:52 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(625, '2XYVUZG44K', 'personnelImg/default_img.jpg', '68535f39c4bb7.jpg', 'RELIQUIAS', 'JOERIBEL', 'TORIANO', '', 9, 3, '06/19/2025', '08:52 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(626, 'C4XW3NQ3CE', 'personnelImg/default_img.jpg', '68535f3fc29be.jpg', 'ACADEMIA', 'RIZALYN', 'CAYANAN', '', 9, 3, '06/19/2025', '08:52 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(627, 'KDV12JKLNR', 'personnelImg/default_img.jpg', '68535f4cc13da.jpg', 'TRINIO-SANTES', 'VANESSA', 'LIRAZAN', '', 3, 3, '06/19/2025', '08:52 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(628, 'VUM5AWYEUH', 'personnelImg/default_img.jpg', '68535f56c3b7d.jpg', 'SUMUGAT', 'JENELYN', 'TELONIO', '', 3, 3, '06/19/2025', '08:52 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(629, '2SOQJG3P6F', 'personnelImg/default_img.jpg', '68535f66c6342.jpg', 'SANTES', 'CINDY', 'REBOLDAL', '', 4, 3, '06/19/2025', '08:52 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(630, 'LTQHD2BPYJ', 'personnelImg/default_img.jpg', '68535f6fc55a4.jpg', 'GALON', 'RANDOLF', 'BLANCO', '', 4, 3, '06/19/2025', '08:53 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(631, 'YBD20JEALC', 'personnelImg/default_img.jpg', '68535f79c38c0.jpg', 'ABRIGANA', 'PAMELA', '', '', 26, 3, '06/19/2025', '08:53 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(632, 'XFSKZYRVSI', 'personnelImg/default_img.jpg', '68535fb3c79e9.jpg', 'LASTRILLA', 'GUIDRALYN', 'P.', '', 7, 3, '06/19/2025', '08:54 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(633, 'KHA2LUXNL1', 'personnelImg/default_img.jpg', '68535fc2c17dd.jpg', 'BA-AL', 'VON MARVIN', 'M.', '', 2, 3, '06/19/2025', '08:54 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(634, 'RVR5BBU5ZV', 'personnelImg/default_img.jpg', '68535fc8c2866.jpg', 'CANA', 'EVA MAE', 'G.', '', 11, 3, '06/19/2025', '08:54 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(635, 'ERKTAZMQZA', 'personnelImg/default_img.jpg', '68535fcec5025.jpg', 'CARDINAL', 'RYAN', 'A.', '', 24, 3, '06/19/2025', '08:54 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(636, '2CKQGJE4K6', 'personnelImg/default_img.jpg', '68535fd4c29fb.jpg', 'DELLOSO', 'CHRISTOPHER', 'M.', '', 14, 3, '06/19/2025', '08:54 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(637, 'JGSKGE5D2W', 'personnelImg/default_img.jpg', '68535fe0c630c.jpg', 'DELA CONCEPTION', 'IMELDA', 'E.', '', 10, 3, '06/19/2025', '08:54 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(638, 'VWUQQEPZ5P', 'personnelImg/default_img.jpg', '68535fe8c58dc.jpg', 'DIONALDO', 'ROEM', 'S.', '', 2, 3, '06/19/2025', '08:55 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(639, '5Z5WKCHQGF', 'personnelImg/default_img.jpg', '68535fefc6f28.jpg', 'DOLOR', 'ROLY', 'D.', '', 11, 3, '06/19/2025', '08:55 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(640, '1YJTKOZ61A', 'personnelImg/default_img.jpg', '68535ff8c5ca6.jpg', 'DURAN', 'MICHELLE', 'M.', '', 11, 3, '06/19/2025', '08:55 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(641, '36EOSJANR6', 'personnelImg/default_img.jpg', '68536004c2c65.jpg', 'ENGCOY', 'CANTERLYN JOY', 'T.', '', 24, 3, '06/19/2025', '08:55 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(642, 'GP4Q0NOWGA', 'personnelImg/default_img.jpg', '68536008c67f3.jpg', 'GERMINAL', 'CRISTUTO', 'M.', '', 17, 3, '06/19/2025', '08:55 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(643, 'M1RF4GW0TT', 'personnelImg/default_img.jpg', '6853600dc615c.jpg', 'GUINTOS', 'MA. ELENA', 'N.', '', 14, 3, '06/19/2025', '08:55 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(644, 'H05GELOMZN', 'personnelImg/default_img.jpg', '68536012c2f66.jpg', 'DEQUINA', 'REYNOLD', 'B.', '', 17, 3, '06/19/2025', '08:55 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(645, 'BLJQQ3XW3K', 'personnelImg/default_img.jpg', '6853601ac6870.jpg', 'NORVIE', 'JOSECO', 'A.', '', 11, 3, '06/19/2025', '08:55 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(646, 'QQZIGTSWBI', 'personnelImg/default_img.jpg', '68536021c5dcb.jpg', 'NACION', 'ELBRED', 'S.', '', 17, 3, '06/19/2025', '08:56 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(647, 'O2NINN551F', 'personnelImg/default_img.jpg', '68536028c46be.jpg', 'PARCON', 'MERCEDITA', 'T.', '', 7, 3, '06/19/2025', '08:56 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(648, '65VQESUT03', 'personnelImg/default_img.jpg', '6853602ec7550.jpg', 'PANGANTIHON', 'JOHN IRVING', 'TOLEDO', '', 3, 3, '06/19/2025', '08:56 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(649, 'G6A2OEKXUH', 'personnelImg/default_img.jpg', '68536034c593b.jpg', 'MANGILIMUTAN', 'MA. HEARTY', 'CAÑA', '', 12, 3, '06/19/2025', '08:56 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(650, 'ITZ6XK1TGX', 'personnelImg/default_img.jpg', '6853603dc4a45.jpg', 'LUCENARA', 'ROBIJID', 'Q', '', 15, 3, '06/19/2025', '08:56 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(651, '0M1YGUSYJR', 'personnelImg/default_img.jpg', '68536042c582d.jpg', 'BALOYO', 'HANSEL', 'M.', '', 11, 3, '06/19/2025', '08:56 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(652, 'R0VM3TX265', 'personnelImg/default_img.jpg', '68536050c7c43.jpg', 'MONTANO', 'EVALYN', 'C.', '', 24, 3, '06/19/2025', '08:56 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(653, 'WZZQ44GGKF', 'personnelImg/default_img.jpg', '68536057c3f3b.jpg', 'YUSAY', 'ROMEO', 'A.', 'JR. ', 24, 3, '06/19/2025', '08:56 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(654, 'T0SO3GEQZX', 'personnelImg/default_img.jpg', '68536067c5f0f.jpg', 'TUPAS', 'ANTHONY', 'L.', '', 11, 3, '06/19/2025', '08:57 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(655, 'HOA5M4V2KU', 'personnelImg/default_img.jpg', '6853606fc6c78.jpg', 'TEMBREVILLA', 'THOMY', 'G.', '', 3, 3, '06/19/2025', '08:57 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(656, 'N0AXWJPKNQ', 'personnelImg/default_img.jpg', '68536075c5af4.jpg', 'SORONGON', 'NITA', 'A.', '', 7, 3, '06/19/2025', '08:57 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(657, 'L33XK2MQYL', 'personnelImg/default_img.jpg', '6853607ac2252.jpg', 'RELIQUIAS', 'JOHNNY RAY', 'L.', '', 2, 3, '06/19/2025', '08:57 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(658, 'DV6HSR4T31', 'personnelImg/default_img.jpg', '6853607fc472d.jpg', 'SABOBO', 'ALFREDO', 'G.', '', 15, 3, '06/19/2025', '08:57 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(659, 'JETERWW23U', 'personnelImg/nrf5ihz6-mae-flor.jpg', '685360c5c5cd9.jpg', 'BARRIOS', 'MAE FLOR', 'ALMAIZ', '', 8, 3, '06/19/2025', '08:58 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(660, 'YFUCRYEDIV', 'personnelImg/nrfc1ohg-jet.jpg', '685360cbc7dd3.jpg', 'BARROCA', 'JETTER', 'SENIO', '', 14, 3, '06/19/2025', '08:58 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(661, '111LXWXFLY', 'personnelImg/default_img.jpg', '685360d6c3656.jpg', 'NICOR', 'MICHAEL', 'RAMOS', '', 7, 3, '06/19/2025', '08:59 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(662, 'WILZVWBBRI', 'personnelImg/default_img.jpg', '685360e2c3307.jpg', 'OCCEÑA', 'GEM', 'RELAMPAGOS', '', 3, 3, '06/19/2025', '08:59 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(663, 'SJLE6GGLD6', 'personnelImg/nrf04z55-che.jpg', '685360e9c8d27.jpg', 'JUAREZ', 'CHERRY LYNN', 'MONTAÑO', '', 2, 3, '06/19/2025', '08:59 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(664, 'GJCC6XY3BR', 'personnelImg/default_img.jpg', '685360f0c2ab7.jpg', 'ANTIQUEÑO', 'ANA MARIE', 'SANOY', '', 12, 3, '06/19/2025', '08:59 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(665, 'XD5JB3HCC0', 'personnelImg/default_img.jpg', '685360f6c2e6e.jpg', 'LAREÑO', 'LIWAYA', 'MAHINAY', '', 11, 3, '06/19/2025', '08:59 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(666, 'E6LFKFXAD0', 'personnelImg/default_img.jpg', '685360fbc53dc.jpg', 'NUÑESCO', 'AIZA', 'ANDO', '', 5, 3, '06/19/2025', '08:59 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(667, '2FUFQBHUQM', 'personnelImg/default_img.jpg', '68536105c9487.jpg', 'DECENA', 'LANNE', 'VILLARETE', '', 24, 3, '06/19/2025', '08:59 AM', 32388, 'on', 'AM OUT', '192.168.1.2', '', '', 0),
(668, 'SARR3EM6ZV', 'personnelImg/default_img.jpg', '6853610bc615a.jpg', 'PACURIB', 'JULITO', 'PLAÑA', 'JR. ', 3, 3, '06/19/2025', '08:59 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(669, 'SP2WX6WZFC', 'personnelImg/default_img.jpg', '68536111c4e72.jpg', 'PADA', 'VERONICA EVELYN', 'ALBISO', '', 12, 3, '06/19/2025', '09:00 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(670, 'FOSBE6FBBP', 'personnelImg/default_img.jpg', '6853611ec79d8.jpg', 'MAHINAY', 'FRANCISCO', 'MANINANTAN', 'SR. ', 3, 3, '06/19/2025', '09:00 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(671, 'P-ZGT-382010-110', 'personnelImg/default_img.jpg', '6853612fc40e6.jpg', 'TEMBREVILLA', 'ZOSIMO', 'GAVILAGA', '', 3, 3, '06/19/2025', '09:00 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(672, 'CCEWWUW3P2', 'personnelImg/default_img.jpg', '68536137c4bc3.jpg', 'TUBILLEJA', 'THEODORE', 'SIGUEZA', 'JR. ', 6, 3, '06/19/2025', '09:00 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(673, 'E0VZSU0W6G', 'personnelImg/default_img.jpg', '6853613fc674d.jpg', 'APLAON', 'MA. LEE', 'MAESTRECAMPO', '', 23, 3, '06/19/2025', '09:00 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(674, '2SJBOJTXPC', 'personnelImg/default_img.jpg', '68536185c581e.jpg', 'GELLECANAO', 'MURIELLE', 'LIRAZAN', '', 7, 3, '06/19/2025', '09:01 AM', 32516, 'on', 'AM OUT', '192.168.1.2', '', '', 0),
(675, '4RE6HDPT3R', 'personnelImg/default_img.jpg', '68536193c5cd9.jpg', 'JIMENEZ', 'LEO', 'TOMILBA', '', 14, 3, '06/19/2025', '09:02 AM', 32530, 'on', 'AM OUT', '192.168.1.2', '', '', 0),
(676, 'O6JWSQ5BTR', 'personnelImg/default_img.jpg', '685361a2c42c9.jpg', 'DELA CONCEPTION', 'IMELDA', 'ESPENORIO', '', 14, 3, '06/19/2025', '09:02 AM', 32545, 'on', 'AM OUT', '192.168.1.2', '', '', 0),
(677, 'GSQZ2OCVHQ', 'personnelImg/default_img.jpg', '685361d6c8afd.jpg', 'RELIQUIAS', 'GERALD', 'TORIANO', '', 14, 3, '06/19/2025', '09:03 AM', 32597, 'on', 'AM OUT', '192.168.1.2', '', '', 0),
(678, 'FU6VB3OYK4', 'personnelImg/default_img.jpg', '685361e8c2fde.jpg', 'MANGOGTONG', 'WILMAR', 'ESTRELLA', '', 14, 3, '06/19/2025', '09:03 AM', 32615, 'on', 'AM OUT', '192.168.1.2', '', '', 0),
(679, 'PLCEEHAUGT', 'personnelImg/default_img.jpg', '685361eec4f08.jpg', 'MARQUEZ', 'MAE ANN', 'GIGANAN', '', 15, 3, '06/19/2025', '09:03 AM', 32621, 'on', 'AM OUT', '192.168.1.2', '', '', 0),
(680, 'WDB3J415ZP', 'personnelImg/default_img.jpg', '6853623ec56f0.jpg', 'AKOL', 'SHARRA MAE', 'ARTICA', '', 10, 3, '06/19/2025', '09:05 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(681, 'ZJPTEJP5UQ', 'personnelImg/default_img.jpg', '68536262c4c21.jpg', 'AMANTE', 'LEONIZA', 'FLORES', '', 11, 3, '06/19/2025', '09:05 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(682, '2M5F54VPSN', 'personnelImg/default_img.jpg', '68536283c670e.jpg', 'AKOL', 'MARLOU', 'ARTICA', '', 14, 3, '06/19/2025', '09:06 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(683, 'FKX31XYW34', 'personnelImg/default_img.jpg', '685362bac3887.jpg', 'DELA FUENTE', 'ALVIN', 'ORMEO', '', 11, 3, '06/19/2025', '09:07 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(684, 'Q0BSK1IGMH', 'personnelImg/default_img.jpg', '685363accf709.jpg', 'BARIQUIT', 'PRACEDES', 'CABONILAS', '', 7, 3, '06/19/2025', '09:11 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(685, 'PWU0GP4SHN', 'personnelImg/default_img.jpg', '685363d1c4c23.jpg', 'SANTES', 'AZELA', 'FLORES', '', 7, 3, '06/19/2025', '09:11 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(686, 'EKSUEACJM5', 'personnelImg/default_img.jpg', '6853652ec9a0b.jpg', 'ACIBIDO', 'CHE GENEROSO', 'PARO', '', 14, 3, '06/19/2025', '09:17 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(687, 'R0ZJXTVRR4', 'personnelImg/default_img.jpg', '68537934cec26.jpg', 'AVELINO', 'JULIEBERT', 'AZUCENA', '', 2, 3, '06/19/2025', '10:42 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(688, 'OINBKY4BGA', 'personnelImg/default_img.jpg', '68537964c6435.jpg', 'DECATORIA', 'JOEBERT', 'SARIL', '', 3, 3, '06/19/2025', '10:43 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(689, 'BSLAWXCQ2N', 'personnelImg/default_img.jpg', '68537986c8bf1.jpg', 'BAYDO', 'JULITO', 'CAYAS', '', 3, 3, '06/19/2025', '10:44 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(690, 'YCSOHXN5TA', 'personnelImg/default_img.jpg', '6853799ac7e58.jpg', 'GALLARDA', 'ELNOR', 'PACLAONA', '', 12, 3, '06/19/2025', '10:44 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(691, 'Z2TDUSRUV6', 'personnelImg/default_img.jpg', '685379b0c59d3.jpg', 'BIACA', 'HERBERT', 'BORNALES', '', 3, 3, '06/19/2025', '10:45 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(692, 'R52SQSFJB4', 'personnelImg/default_img.jpg', '68537a4ec5e04.jpg', 'BONILLA', 'JURY', 'BENLOT', '', 17, 3, '06/19/2025', '10:47 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(693, 'T4BXLLYWTG', 'personnelImg/default_img.jpg', '68537a65c4cd7.jpg', 'DERIT', 'JOSE ALAN ', 'DURO', '', 17, 3, '06/19/2025', '10:48 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(694, 'MMB6JKH1T3', 'personnelImg/default_img.jpg', '68537ac2c587e.jpg', 'CAMBARIJAN', 'JIMMY', 'ZETA', '', 25, 3, '06/19/2025', '10:49 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(695, 'LVX5C46TAS', 'personnelImg/default_img.jpg', '68537cfcc57b1.jpg', 'SILVESTRE', 'CYNTHIA', 'LAMBOT', '', 12, 3, '06/19/2025', '10:59 AM', 39547, 'on', 'AM OUT', '192.168.1.2', '', '', 0),
(696, 'ITZ6XK1TGX', 'personnelImg/default_img.jpg', '68537d2cc3be6.jpg', 'LUCENARA', 'ROBIJID', 'Q', '', 15, 3, '06/19/2025', '10:59 AM', 39595, 'on', 'AM OUT', '192.168.1.2', '', '', 0),
(697, 'CMDUXPZ44I', 'personnelImg/default_img.jpg', '68537e4fc8bbd.jpg', 'GONZAL', 'SANDRA', 'CASTILLO', '', 8, 3, '06/19/2025', '11:04 AM', 39886, 'on', 'AM OUT', '192.168.1.2', '', '', 0),
(698, 'W2DGEUTP62', 'personnelImg/default_img.jpg', '68537e8ec329a.jpg', 'LOREDO', 'MARIA ELENA', 'DELLOSO', '', 14, 3, '06/19/2025', '11:05 AM', 0, 'on', 'AM IN', '192.168.1.2', '', '', 0),
(699, '2M5F54VPSN', 'personnelImg/default_img.jpg', '685393afc2c71.jpg', 'AKOL', 'MARLOU', 'ARTICA', '', 14, 3, '06/19/2025', '12:35 PM', 45358, 'off', 'AM OUT', '192.168.1.2', '', '', 0),
(700, 'ITZ6XK1TGX', 'personnelImg/default_img.jpg', '685393ddc4404.jpg', 'LUCENARA', 'ROBIJID', 'Q', '', 15, 3, '06/19/2025', '12:36 PM', 0, 'off', 'PM IN', '192.168.1.2', '', '', 0),
(701, '0M1YGUSYJR', 'personnelImg/default_img.jpg', '6853942fc3d51.jpg', 'BALOYO', 'HANSEL', 'M.', '', 11, 3, '06/19/2025', '12:38 PM', 45486, 'off', 'AM OUT', '192.168.1.2', '', '', 0),
(702, 'UV0LOFMFS3', 'personnelImg/default_img.jpg', '68539c4bc20a1.jpg', 'LOGRONIO', 'JOESIFIL', 'FAJARDO', '', 2, 3, '06/19/2025', '01:12 PM', 0, 'off', 'PM IN', '192.168.1.2', '', '', 0),
(703, 'G6A2OEKXUH', 'personnelImg/default_img.jpg', '68539c57c2a2c.jpg', 'MANGILIMUTAN', 'MA. HEARTY', 'CAÑA', '', 12, 3, '06/19/2025', '01:12 PM', 47574, 'off', 'AM OUT', '192.168.1.2', '', '', 0),
(704, 'N3CFWPHFQX', 'personnelImg/default_img.jpg', '6853c95d263d3.jpg', 'SANTES', 'CAROL ANN', 'TILOS', '', 5, 3, '06/19/2025', '04:25 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(705, 'MF4R0LP0PT', 'personnelImg/default_img.jpg', '6853c97e1f459.jpg', 'TOLEDO', 'RAYMUND ANTHONY', 'INOLINO', '', 4, 3, '06/19/2025', '04:25 PM', 0, 'on', 'PM IN', '192.168.1.2', '', '', 0),
(706, 'QQZIGTSWBI', 'personnelImg/default_img.jpg', '6854fef29fdc7.jpg', 'NACION', 'ELBRED', 'S.', '', 17, 3, '06/20/2025', '02:25 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(707, 'O2NINN551F', 'personnelImg/default_img.jpg', '6854fefb8beb4.jpg', 'PARCON', 'MERCEDITA', 'T.', '', 7, 3, '06/20/2025', '02:26 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(708, 'BLJQQ3XW3K', 'personnelImg/default_img.jpg', '6854ff038d7c4.jpg', 'NORVIE', 'JOSECO', 'A.', '', 11, 3, '06/20/2025', '02:26 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(709, 'M1RF4GW0TT', 'personnelImg/default_img.jpg', '6854ff0b90a18.jpg', 'GUINTOS', 'MA. ELENA', 'N.', '', 14, 3, '06/20/2025', '02:26 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(710, 'GP4Q0NOWGA', 'personnelImg/default_img.jpg', '6854ff168aebc.jpg', 'GERMINAL', 'CRISTUTO', 'M.', '', 17, 3, '06/20/2025', '02:26 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(711, '36EOSJANR6', 'personnelImg/default_img.jpg', '6854ff218f465.jpg', 'ENGCOY', 'CANTERLYN JOY', 'T.', '', 24, 3, '06/20/2025', '02:26 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(712, '1YJTKOZ61A', 'personnelImg/default_img.jpg', '6854ff278e7d7.jpg', 'DURAN', 'MICHELLE', 'M.', '', 11, 3, '06/20/2025', '02:26 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(713, '5Z5WKCHQGF', 'personnelImg/default_img.jpg', '6854ff2e8e13d.jpg', 'DOLOR', 'ROLY', 'D.', '', 11, 3, '06/20/2025', '02:26 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(714, 'VWUQQEPZ5P', 'personnelImg/default_img.jpg', '6854ff3b8cb6b.jpg', 'DIONALDO', 'ROEM', 'S.', '', 2, 3, '06/20/2025', '02:27 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(715, 'H05GELOMZN', 'personnelImg/default_img.jpg', '6854ff408d400.jpg', 'DEQUINA', 'REYNOLD', 'B.', '', 17, 3, '06/20/2025', '02:27 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(716, 'G6A2OEKXUH', 'personnelImg/default_img.jpg', '6854ffd28c404.jpg', 'MANGILIMUTAN', 'MA. HEARTY', 'CAÑA', '', 12, 3, '06/20/2025', '02:29 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(717, 'L33XK2MQYL', 'personnelImg/default_img.jpg', '6854ffe08ef0a.jpg', 'RELIQUIAS', 'JOHNNY RAY', 'L.', '', 2, 3, '06/20/2025', '02:29 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(718, 'DV6HSR4T31', 'personnelImg/default_img.jpg', '6854ffe890855.jpg', 'SABOBO', 'ALFREDO', 'G.', '', 15, 3, '06/20/2025', '02:29 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(719, '0M1YGUSYJR', 'personnelImg/default_img.jpg', '6854fff08ed01.jpg', 'BALOYO', 'HANSEL', 'M.', '', 11, 3, '06/20/2025', '02:30 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(720, 'ITZ6XK1TGX', 'personnelImg/default_img.jpg', '6854fffa8d098.jpg', 'LUCENARA', 'ROBIJID', 'Q', '', 15, 3, '06/20/2025', '02:30 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(721, 'N0AXWJPKNQ', 'personnelImg/default_img.jpg', '685500018be6a.jpg', 'SORONGON', 'NITA', 'A.', '', 7, 3, '06/20/2025', '02:30 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(722, 'HOA5M4V2KU', 'personnelImg/default_img.jpg', '685500088ea09.jpg', 'TEMBREVILLA', 'THOMY', 'G.', '', 3, 3, '06/20/2025', '02:30 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(723, 'T0SO3GEQZX', 'personnelImg/default_img.jpg', '685500108e4fd.jpg', 'TUPAS', 'ANTHONY', 'L.', '', 11, 3, '06/20/2025', '02:30 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(724, 'WZZQ44GGKF', 'personnelImg/default_img.jpg', '685500168e974.jpg', 'YUSAY', 'ROMEO', 'A.', 'JR. ', 24, 3, '06/20/2025', '02:30 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(725, 'R0VM3TX265', 'personnelImg/default_img.jpg', '6855001b8ed70.jpg', 'MONTANO', 'EVALYN', 'C.', '', 24, 3, '06/20/2025', '02:30 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(726, 'WDB3J415ZP', 'personnelImg/default_img.jpg', '685500928d952.jpg', 'AKOL', 'SHARRA MAE', 'ARTICA', '', 10, 3, '06/20/2025', '02:32 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(727, 'SARR3EM6ZV', 'personnelImg/default_img.jpg', '6855009c8f090.jpg', 'PACURIB', 'JULITO', 'PLAÑA', 'JR. ', 3, 3, '06/20/2025', '02:32 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(728, 'WILZVWBBRI', 'personnelImg/default_img.jpg', '685500a491d1b.jpg', 'OCCEÑA', 'GEM', 'RELAMPAGOS', '', 3, 3, '06/20/2025', '02:33 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(729, 'E6LFKFXAD0', 'personnelImg/default_img.jpg', '685500ae8c6a0.jpg', 'NUÑESCO', 'AIZA', 'ANDO', '', 5, 3, '06/20/2025', '02:33 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(730, 'XD5JB3HCC0', 'personnelImg/default_img.jpg', '685500bc910ab.jpg', 'LAREÑO', 'LIWAYA', 'MAHINAY', '', 11, 3, '06/20/2025', '02:33 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(731, 'GJCC6XY3BR', 'personnelImg/default_img.jpg', '685500c28c24f.jpg', 'ANTIQUEÑO', 'ANA MARIE', 'SANOY', '', 12, 3, '06/20/2025', '02:33 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(732, 'SJLE6GGLD6', 'personnelImg/nrf04z55-che.jpg', '685500ca8c688.jpg', 'JUAREZ', 'CHERRY LYNN', 'MONTAÑO', '', 2, 3, '06/20/2025', '02:33 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(733, '111LXWXFLY', 'personnelImg/default_img.jpg', '685500d390337.jpg', 'NICOR', 'MICHAEL', 'RAMOS', '', 7, 3, '06/20/2025', '02:33 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(734, 'YFUCRYEDIV', 'personnelImg/nrfc1ohg-jet.jpg', '685500de8fda9.jpg', 'BARROCA', 'JETTER', 'SENIO', '', 14, 3, '06/20/2025', '02:34 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(735, 'JETERWW23U', 'personnelImg/nrf5ihz6-mae-flor.jpg', '685500e88c090.jpg', 'BARRIOS', 'MAE FLOR', 'ALMAIZ', '', 8, 3, '06/20/2025', '02:34 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(736, 'CCEWWUW3P2', 'personnelImg/default_img.jpg', '6855018292963.jpg', 'TUBILLEJA', 'THEODORE', 'SIGUEZA', 'JR. ', 6, 3, '06/20/2025', '02:36 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(737, 'F255HSX3FM', 'personnelImg/default_img.jpg', '6855018c8e529.jpg', 'GUINTOS', 'ERICA', 'MANANGAN', '', 14, 3, '06/20/2025', '02:36 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(738, 'PQMSBLBNJ6', 'personnelImg/default_img.jpg', '6855019890a2a.jpg', 'VIDAURRAZAGA', 'NOAH', 'VASQUEZ', '', 2, 3, '06/20/2025', '02:37 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(739, 'E0VZSU0W6G', 'personnelImg/default_img.jpg', '685501a0903ae.jpg', 'APLAON', 'MA. LEE', 'MAESTRECAMPO', '', 23, 3, '06/20/2025', '02:37 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(740, 'PMOLWNYZZA', 'personnelImg/default_img.jpg', '685501a68e2a7.jpg', 'SUSANA', 'FREDERICK DAVY', 'PINGCALE', '', 12, 3, '06/20/2025', '02:37 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(741, 'OIMV5KOLF2', 'personnelImg/default_img.jpg', '685501af91b5a.jpg', 'PEROSIA', 'GLORY MAE', 'TUNDA-AN', '', 12, 3, '06/20/2025', '02:37 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(742, '65VQESUT03', 'personnelImg/default_img.jpg', '685501b68f429.jpg', 'PANGANTIHON', 'JOHN IRVING', 'TOLEDO', '', 3, 3, '06/20/2025', '02:37 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(743, 'SP2WX6WZFC', 'personnelImg/default_img.jpg', '685501bc8e9c2.jpg', 'PADA', 'VERONICA EVELYN', 'ALBISO', '', 12, 3, '06/20/2025', '02:37 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(744, 'FOSBE6FBBP', 'personnelImg/default_img.jpg', '685501c38e6fd.jpg', 'MAHINAY', 'FRANCISCO', 'MANINANTAN', 'SR. ', 3, 3, '06/20/2025', '02:37 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(745, 'P-ZGT-382010-110', 'personnelImg/default_img.jpg', '685501d08ee70.jpg', 'TEMBREVILLA', 'ZOSIMO', 'GAVILAGA', '', 3, 3, '06/20/2025', '02:38 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(746, 'LS5IJYTERX', 'personnelImg/default_img.jpg', '6855024490677.jpg', 'GUINTOS', 'WINSTON', 'NAVA', '', 3, 3, '06/20/2025', '02:40 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(747, 'UUR4C35PLC', 'personnelImg/default_img.jpg', '6855024a8e961.jpg', 'ALIMANE', 'JINKY', 'GALPO', '', 7, 3, '06/20/2025', '02:40 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(748, 'EEG0NY2B5U', 'personnelImg/default_img.jpg', '685502518cd37.jpg', 'ARBOIZ', 'JOHN', 'VILLARICO', '', 15, 3, '06/20/2025', '02:40 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(749, 'R0ZJXTVRR4', 'personnelImg/default_img.jpg', '6855025790cf8.jpg', 'AVELINO', 'JULIEBERT', 'AZUCENA', '', 2, 3, '06/20/2025', '02:40 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(750, 'BSLAWXCQ2N', 'personnelImg/default_img.jpg', '6855025e8cf6f.jpg', 'BAYDO', 'JULITO', 'CAYAS', '', 3, 3, '06/20/2025', '02:40 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(751, 'Z2TDUSRUV6', 'personnelImg/default_img.jpg', '685502638db96.jpg', 'BIACA', 'HERBERT', 'BORNALES', '', 3, 3, '06/20/2025', '02:40 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(752, 'OINBKY4BGA', 'personnelImg/default_img.jpg', '6855026c8c78e.jpg', 'DECATORIA', 'JOEBERT', 'SARIL', '', 3, 3, '06/20/2025', '02:40 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(753, 'YCSOHXN5TA', 'personnelImg/default_img.jpg', '685502798e126.jpg', 'GALLARDA', 'ELNOR', 'PACLAONA', '', 12, 3, '06/20/2025', '02:40 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(754, 'JT6NRMM2MG', 'personnelImg/default_img.jpg', '6855028091a17.jpg', 'LABRADOR', 'REY', 'TRIBUCIO', '', 10, 3, '06/20/2025', '02:41 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(755, '6D0YWDKLPP', 'personnelImg/default_img.jpg', '685502898bab0.jpg', 'AGUHAYON', 'RICARDO', 'UBAMOS', '', 14, 3, '06/20/2025', '02:41 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(756, '56EZ3T4YP2', 'personnelImg/default_img.jpg', '685502fa91a4f.jpg', 'CALUMBA', 'ANALYN', 'MANILINGAN', '', 25, 3, '06/20/2025', '02:43 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(757, 'MMB6JKH1T3', 'personnelImg/default_img.jpg', '685503038d119.jpg', 'CAMBARIJAN', 'JIMMY', 'ZETA', '', 25, 3, '06/20/2025', '02:43 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(758, 'REPYPK1N0K', 'personnelImg/default_img.jpg', '6855030d8f612.jpg', 'AMBAGAN', 'EDSEL', 'MILLENDEZ', '', 16, 3, '06/20/2025', '02:43 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(759, 'KUNUEE1ZKH', 'personnelImg/default_img.jpg', '6855031c916ac.jpg', 'YUSAY', 'JOSE', 'TOGLE', 'III ', 16, 3, '06/20/2025', '02:43 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(760, '6KR3ZM4BU3', 'personnelImg/default_img.jpg', '685503238f40d.jpg', 'TELONIO', 'JAMES ANDREW', 'GUINTOS', '', 15, 3, '06/20/2025', '02:43 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(761, 'F3DUWOJ4SZ', 'personnelImg/default_img.jpg', '685503298daba.jpg', 'SIASON', 'CHEZAH ERL', 'GAUAL', '', 16, 3, '06/20/2025', '02:43 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(762, 'POT5DLCLXZ', 'personnelImg/default_img.jpg', '6855032f8f5a0.jpg', 'GUINTOS', 'TINA MARIE', 'LUGA', '', 16, 3, '06/20/2025', '02:43 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(763, 'T4BXLLYWTG', 'personnelImg/default_img.jpg', '6855033592805.jpg', 'DERIT', 'JOSE ALAN ', 'DURO', '', 17, 3, '06/20/2025', '02:44 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(764, 'R52SQSFJB4', 'personnelImg/default_img.jpg', '685503438f6d0.jpg', 'BONILLA', 'JURY', 'BENLOT', '', 17, 3, '06/20/2025', '02:44 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(765, 'GOYLQLNANM', 'personnelImg/default_img.jpg', '6855034b8e57f.jpg', 'DELOTINA', 'DANILO', 'NAPIERE', '', 17, 3, '06/20/2025', '02:44 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(766, 'FU6VB3OYK4', 'personnelImg/default_img.jpg', '685503b78bccd.jpg', 'MANGOGTONG', 'WILMAR', 'ESTRELLA', '', 14, 3, '06/20/2025', '02:46 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(767, 'W2DGEUTP62', 'personnelImg/default_img.jpg', '685503c28cde3.jpg', 'LOREDO', 'MARIA ELENA', 'DELLOSO', '', 14, 3, '06/20/2025', '02:46 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(768, 'PLCEEHAUGT', 'personnelImg/default_img.jpg', '685503c98c8f9.jpg', 'MARQUEZ', 'MAE ANN', 'GIGANAN', '', 15, 3, '06/20/2025', '02:46 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(769, 'EKSUEACJM5', 'personnelImg/default_img.jpg', '685503cf8d3cd.jpg', 'ACIBIDO', 'CHE GENEROSO', 'PARO', '', 14, 3, '06/20/2025', '02:46 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(770, 'J3UWH2Z32E', 'personnelImg/default_img.jpg', '685503d890d9a.jpg', 'TRINIDAD', 'RODOLFO', 'PULGAN', 'JR. ', 15, 3, '06/20/2025', '02:46 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(771, 'GRN63LXTVC', 'personnelImg/default_img.jpg', '685503df8e7ff.jpg', 'GIGANAN', 'AGNES', 'NAVA', '', 15, 3, '06/20/2025', '02:46 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(772, 'M1Y5ETAYD6', 'personnelImg/default_img.jpg', '685503ec8d1e8.jpg', 'NAVA', 'LUZ SALOME', 'VILLA', '', 14, 3, '06/20/2025', '02:47 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(773, 'O6JWSQ5BTR', 'personnelImg/default_img.jpg', '685503f28d754.jpg', 'DELA CONCEPTION', 'IMELDA', 'ESPENORIO', '', 14, 3, '06/20/2025', '02:47 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(774, 'GSQZ2OCVHQ', 'personnelImg/default_img.jpg', '685503f9931d5.jpg', 'RELIQUIAS', 'GERALD', 'TORIANO', '', 14, 3, '06/20/2025', '02:47 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(775, '4RE6HDPT3R', 'personnelImg/default_img.jpg', '685503ff8e932.jpg', 'JIMENEZ', 'LEO', 'TOMILBA', '', 14, 3, '06/20/2025', '02:47 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(776, '5BJ6IAN1FB', 'personnelImg/default_img.jpg', '6855046d8aa88.jpg', 'ORBIGOSO', 'ELIZABETH', 'SENIO', '', 8, 3, '06/20/2025', '02:49 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(777, 'KP6G24TB40', 'personnelImg/default_img.jpg', '685504749138a.jpg', 'RELADO', 'MEDALIA', 'VALENZUELA', '', 8, 3, '06/20/2025', '02:49 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(778, '2SJBOJTXPC', 'personnelImg/default_img.jpg', '685504798eb8c.jpg', 'GELLECANAO', 'MURIELLE', 'LIRAZAN', '', 7, 3, '06/20/2025', '02:49 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(779, 'PWU0GP4SHN', 'personnelImg/default_img.jpg', '685504818ea0c.jpg', 'SANTES', 'AZELA', 'FLORES', '', 7, 3, '06/20/2025', '02:49 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(780, 'Q0BSK1IGMH', 'personnelImg/default_img.jpg', '685504898d92c.jpg', 'BARIQUIT', 'PRACEDES', 'CABONILAS', '', 7, 3, '06/20/2025', '02:49 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(781, 'Q0QB3ZVYNK', 'personnelImg/default_img.jpg', '6855048f8f337.jpg', 'VILLARETE', 'ANGELA', 'TELONIO', '', 7, 3, '06/20/2025', '02:49 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(782, '1DSSFVBIFF', 'personnelImg/default_img.jpg', '685504958ed75.jpg', 'MAESTRECAMPO', 'EVELYN', 'ELARMO', '', 7, 3, '06/20/2025', '02:49 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(783, 'FKX31XYW34', 'personnelImg/default_img.jpg', '6855049b8e825.jpg', 'DELA FUENTE', 'ALVIN', 'ORMEO', '', 11, 3, '06/20/2025', '02:50 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(784, 'ZJPTEJP5UQ', 'personnelImg/default_img.jpg', '685504a491ffc.jpg', 'AMANTE', 'LEONIZA', 'FLORES', '', 11, 3, '06/20/2025', '02:50 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(785, '2M5F54VPSN', 'personnelImg/default_img.jpg', '685504aa8f234.jpg', 'AKOL', 'MARLOU', 'ARTICA', '', 14, 3, '06/20/2025', '02:50 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(786, 'K0GBVLRIOM', 'personnelImg/default_img.jpg', '6855051b8bb12.jpg', 'JORDAN', 'MARK ANTHONY', 'TOMADO', '', 3, 3, '06/20/2025', '02:52 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(787, 'EHEYDZ1UYN', 'personnelImg/default_img.jpg', '685505288e147.jpg', 'ALATON', 'GINA', 'NABOR', '', 6, 3, '06/20/2025', '02:52 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(788, 'DGPF5FDK53', 'personnelImg/default_img.jpg', '6855052e8e0b3.jpg', 'ARANETA', 'JOSELITA', 'GENTELIZO', '', 6, 3, '06/20/2025', '02:52 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(789, 'N4DZR4BBY0', 'personnelImg/default_img.jpg', '6855053991985.jpg', 'RELIQUIAS', 'MARIA FE', 'GUINTOS', '', 8, 3, '06/20/2025', '02:52 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(790, 'V4PAJMJ5YO', 'personnelImg/default_img.jpg', '6855053e8e0f1.jpg', 'TUPAS', 'OFELIA', 'TEMBREVILLA', '', 6, 3, '06/20/2025', '02:52 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(791, 'DURSCXCSIM', 'personnelImg/default_img.jpg', '6855054692c49.jpg', 'MAYANDIA', 'ANALIE', 'ELARMO', '', 6, 3, '06/20/2025', '02:52 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(792, 'CMDUXPZ44I', 'personnelImg/default_img.jpg', '6855054d8e38d.jpg', 'GONZAL', 'SANDRA', 'CASTILLO', '', 8, 3, '06/20/2025', '02:53 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(793, 'UBFPHM020G', 'personnelImg/default_img.jpg', '6855055291088.jpg', 'BONILLA', 'ANNI VER', 'PAMLIEGA', '', 6, 3, '06/20/2025', '02:53 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(794, 'B42WBNMNSK', 'personnelImg/default_img.jpg', '6855055b8f35a.jpg', 'LIMSIACO', 'ARIANE', 'CONSTANTINO', '', 6, 3, '06/20/2025', '02:53 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(795, 'EAUNV5WVM0', 'personnelImg/default_img.jpg', '685505608c732.jpg', 'BARROCA', 'JORGIE', 'PERFUMA', '', 6, 3, '06/20/2025', '02:53 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(796, 'VUM5AWYEUH', 'personnelImg/default_img.jpg', '685505cb8e88c.jpg', 'SUMUGAT', 'JENELYN', 'TELONIO', '', 3, 3, '06/20/2025', '02:55 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(797, 'KDV12JKLNR', 'personnelImg/default_img.jpg', '685505d48e623.jpg', 'TRINIO-SANTES', 'VANESSA', 'LIRAZAN', '', 3, 3, '06/20/2025', '02:55 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(798, 'C4XW3NQ3CE', 'personnelImg/default_img.jpg', '685505ed8e73f.jpg', 'ACADEMIA', 'RIZALYN', 'CAYANAN', '', 9, 3, '06/20/2025', '02:55 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(799, '2XYVUZG44K', 'personnelImg/default_img.jpg', '685505f590388.jpg', 'RELIQUIAS', 'JOERIBEL', 'TORIANO', '', 9, 3, '06/20/2025', '02:55 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(800, 'N0PELGBXHV', 'personnelImg/default_img.jpg', '685506068d5a2.jpg', 'GUSTILO', 'LEILANI', 'DECENA', '', 9, 3, '06/20/2025', '02:56 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(801, 'VUM5AWYEUH', 'personnelImg/default_img.jpg', '68550a0f901b5.jpg', 'SUMUGAT', 'JENELYN', 'TELONIO', '', 3, 3, '06/20/2025', '03:13 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(802, 'KDV12JKLNR', 'personnelImg/default_img.jpg', '68550a328ecc5.jpg', 'TRINIO-SANTES', 'VANESSA', 'LIRAZAN', '', 3, 3, '06/20/2025', '03:13 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(803, 'C4XW3NQ3CE', 'personnelImg/default_img.jpg', '68550a4a90f89.jpg', 'ACADEMIA', 'RIZALYN', 'CAYANAN', '', 9, 3, '06/20/2025', '03:14 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(804, '2XYVUZG44K', 'personnelImg/default_img.jpg', '68550a6a8e01c.jpg', 'RELIQUIAS', 'JOERIBEL', 'TORIANO', '', 9, 3, '06/20/2025', '03:14 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(805, 'N0PELGBXHV', 'personnelImg/default_img.jpg', '68550a76911e1.jpg', 'GUSTILO', 'LEILANI', 'DECENA', '', 9, 3, '06/20/2025', '03:15 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(806, 'HR1TD1EOO6', 'personnelImg/default_img.jpg', '68550a868c852.jpg', 'NATALIO', 'JOEFREY', 'TEMBREVILLA', '', 9, 3, '06/20/2025', '03:15 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(807, 'DURSCXCSIM', 'personnelImg/default_img.jpg', '68550a998c83a.jpg', 'MAYANDIA', 'ANALIE', 'ELARMO', '', 6, 3, '06/20/2025', '03:15 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(808, 'KVKMFQDZDV', 'personnelImg/default_img.jpg', '68550aa08d225.jpg', 'GA-AN', 'LENLY', 'NOSAL', '', 10, 3, '06/20/2025', '03:15 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(809, 'ES1MY5CB5N', 'personnelImg/default_img.jpg', '68550aa78de47.jpg', 'BUDACA', 'REVINIA', 'AMACIO', '', 10, 3, '06/20/2025', '03:15 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(810, '2KLYB0WHDY', 'personnelImg/default_img.jpg', '68550aad8e9bf.jpg', 'GESTOSO', 'CARLITO', 'BAVIERA', '', 10, 3, '06/20/2025', '03:15 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(811, 'OSYDS2VNEW', 'personnelImg/default_img.jpg', '68550ab691c18.jpg', 'ABALLE', 'BETHEL', 'PERFUMA', '', 10, 3, '06/20/2025', '03:16 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(812, '2SOQJG3P6F', 'personnelImg/default_img.jpg', '68550b1b8d74e.jpg', 'SANTES', 'CINDY', 'REBOLDAL', '', 4, 3, '06/20/2025', '03:17 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(813, 'NZ0IKZI1IR', 'personnelImg/default_img.jpg', '68550b208d9a5.jpg', 'MANGOGTONG', 'MA. MARILOU ', 'ESTRELLA', '', 10, 3, '06/20/2025', '03:17 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(814, 'CEPHX3JOM0', 'personnelImg/default_img.jpg', '68550b258c42d.jpg', 'TIBAYDE', 'IRENE', 'GIGANAN', '', 10, 3, '06/20/2025', '03:17 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(815, 'KZFCP2S0HT', 'personnelImg/default_img.jpg', '68550b298dc64.jpg', 'GUINTOS', 'JOSEPHINE', 'INDINO', '', 10, 3, '06/20/2025', '03:18 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(816, 'APPZCXZCJS', 'personnelImg/default_img.jpg', '68550b2f8bd16.jpg', 'MIRANDA', 'JAIME', 'MAULIT', '', 5, 3, '06/20/2025', '03:18 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(817, '0RVK3VBCZ1', 'personnelImg/default_img.jpg', '68550b358f691.jpg', 'MANOS', 'ANNABELLE', 'PERIGUA', '', 5, 3, '06/20/2025', '03:18 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(818, 'N3CFWPHFQX', 'personnelImg/default_img.jpg', '68550b3a90ca9.jpg', 'SANTES', 'CAROL ANN', 'TILOS', '', 5, 3, '06/20/2025', '03:18 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(819, 'DO2X14YKJR', 'personnelImg/default_img.jpg', '68550b418fc9c.jpg', 'VILLARUBIA', 'ALTHEA', 'PIA', '', 5, 3, '06/20/2025', '03:18 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(820, 'MF4R0LP0PT', 'personnelImg/default_img.jpg', '68550b48917fc.jpg', 'TOLEDO', 'RAYMUND ANTHONY', 'INOLINO', '', 4, 3, '06/20/2025', '03:18 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(821, 'LTQHD2BPYJ', 'personnelImg/default_img.jpg', '68550b4d8e5f8.jpg', 'GALON', 'RANDOLF', 'BLANCO', '', 4, 3, '06/20/2025', '03:18 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(822, 'RCCO5TPACC', 'personnelImg/default_img.jpg', '68550baf8bf13.jpg', 'PIMENTEL', 'EMY', 'MAGBANUA', '', 12, 3, '06/20/2025', '03:20 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(823, '6ON2RWCQEM', 'personnelImg/default_img.jpg', '68550bb48e1eb.jpg', 'GAREZA', 'MELANIE', 'CELIS', '', 12, 3, '06/20/2025', '03:20 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(824, 'WSJIHMXEN6', 'personnelImg/default_img.jpg', '68550bb88e6d0.jpg', 'TILOS', 'MARCELINA', 'CELIS', '', 12, 3, '06/20/2025', '03:20 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(825, 'I0FY3RFXM5', 'personnelImg/default_img.jpg', '68550bbe8e6ea.jpg', 'TILOS', 'OSCAR', 'VILLARETE', '', 4, 3, '06/20/2025', '03:20 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0);
INSERT INTO `personnel_logs` (`log_id`, `RFTag_id`, `img`, `captured_img`, `lname`, `fname`, `mname`, `suffix`, `do_id`, `shift_id`, `logDate`, `logTime`, `logTime_sec`, `late_status`, `logFlow`, `client_ip`, `remarks`, `travel_leave_code`, `ref_log_id`) VALUES
(826, 'ZHXY3CO3KQ', 'personnelImg/default_img.jpg', '68550bc58bb37.jpg', 'DELA FUENTE', 'EBER', 'ORMEO', '', 12, 3, '06/20/2025', '03:20 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(827, 'MHDZI160KN', 'personnelImg/default_img.jpg', '68550bcc9031a.jpg', 'TEMBREVILLA', 'CAROLINE', 'CANILLO', '', 12, 3, '06/20/2025', '03:20 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(828, '2O1JSGHAYX', 'personnelImg/default_img.jpg', '68550bd38f313.jpg', 'GAUAL', 'ROLIN', 'BERGONIO', 'SR. ', 12, 3, '06/20/2025', '03:20 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(829, 'HEPAB6X3PL', 'personnelImg/default_img.jpg', '68550bd98bcf0.jpg', 'VILLAMATER', 'LORALYN', 'BARROCA', '', 12, 3, '06/20/2025', '03:20 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(830, 'GZ5LO0QDRC', 'personnelImg/default_img.jpg', '68550be2901b3.jpg', 'GARCIA', 'ADELA', 'ANTOLIN', '', 12, 3, '06/20/2025', '03:21 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(831, '5UVF66ZPY0', 'personnelImg/default_img.jpg', '68550be78bec2.jpg', 'MAQUILING', 'WARREN', 'RAMIREZ', '', 12, 3, '06/20/2025', '03:21 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(832, 'LUPQ4RIQUJ', 'personnelImg/default_img.jpg', '68550c778c68a.jpg', 'TILOS', 'RIZALIE', 'CELIZ', '', 12, 3, '06/20/2025', '03:23 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(833, 'LVX5C46TAS', 'personnelImg/default_img.jpg', '68550c7e8d390.jpg', 'SILVESTRE', 'CYNTHIA', 'LAMBOT', '', 12, 3, '06/20/2025', '03:23 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(834, 'ZXDNHZFPMW', 'personnelImg/default_img.jpg', '68550c838eac0.jpg', 'PUBLICO', 'NOEMI', 'TOMADO', '', 12, 3, '06/20/2025', '03:23 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(835, 'I03I6VZELI', 'personnelImg/default_img.jpg', '68550c8a92553.jpg', 'GALAN', 'MARY JANE', 'CELIS', '', 12, 3, '06/20/2025', '03:23 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(836, 'MS5LRO1IJE', 'personnelImg/default_img.jpg', '68550c8f8f4d8.jpg', 'VILLANUEVA', 'ROSALIE', 'ELARDO', '', 12, 3, '06/20/2025', '03:23 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(837, 'HDMCPVKYB1', 'personnelImg/default_img.jpg', '68550c978bcf0.jpg', 'ROXAS', 'MARY ANN', 'VILLAFUERTE', '', 12, 3, '06/20/2025', '03:24 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(838, 'ZV1EXSSS30', 'personnelImg/default_img.jpg', '68550c9f8d440.jpg', 'BONGCAWEL', 'GENELYN ', 'HISONA', '', 12, 3, '06/20/2025', '03:24 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(839, 'V2M0IIAYRM', 'personnelImg/default_img.jpg', '68550ca68f1a4.jpg', 'VILLANUEVA', 'RAYGILDA', 'VERGARA', '', 12, 3, '06/20/2025', '03:24 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(840, 'QESM444ZK0', 'personnelImg/default_img.jpg', '68550cad89e7f.jpg', 'TEMBREVILLA', 'RONA', 'ESCOSAR', '', 12, 3, '06/20/2025', '03:24 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(841, 'DFR5QGBYHW', 'personnelImg/default_img.jpg', '68550cb490e83.jpg', 'BARO', 'PHOEBE', 'MANILINGAN', '', 12, 3, '06/20/2025', '03:24 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(842, 'HSDQZ6G6PA', 'personnelImg/default_img.jpg', '68550d808db48.jpg', 'PEREZ', 'JOSE MARIA', 'LIM', 'JR. ', 12, 3, '06/20/2025', '03:27 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(843, '2FUFQBHUQM', 'personnelImg/default_img.jpg', '68550d868e081.jpg', 'DECENA', 'LANNE', 'VILLARETE', '', 24, 3, '06/20/2025', '03:28 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(844, '1QETKAFQWO', 'personnelImg/default_img.jpg', '68550d8d91f6c.jpg', 'DELOTINA', 'JOFEL', 'EVANGELISTA', '', 24, 3, '06/20/2025', '03:28 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(845, '44SB4W0MF3', 'personnelImg/default_img.jpg', '68550d9a90d69.jpg', 'VIDAURRAZAGA', 'MC', 'PEPITO', '', 24, 3, '06/20/2025', '03:28 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(846, '6DZNSFDRJG', 'personnelImg/default_img.jpg', '68550da19273d.jpg', 'MANOS', 'TEOFILO', 'ENCARGUEZ', 'JR. ', 24, 3, '06/20/2025', '03:28 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(847, 'ZGMTZ2Y3BT', 'personnelImg/default_img.jpg', '68550da78e29c.jpg', 'OCTAVIO', 'PETER JOHN ', 'LAZALITA', '', 24, 3, '06/20/2025', '03:28 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(848, 'NT36Z4UITB', 'personnelImg/default_img.jpg', '68550dae8bbc6.jpg', 'MALAYO', 'EDGARDO', 'FLORES', '', 24, 3, '06/20/2025', '03:28 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(849, 'DCORCD1ICP', 'personnelImg/default_img.jpg', '68550db58e853.jpg', 'TUMA-OB', 'LESLIE AIKEE DYAN', 'GIGANAN', '', 24, 3, '06/20/2025', '03:28 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(850, 'WQ4X6QJYGH', 'personnelImg/default_img.jpg', '68550dba8d418.jpg', 'ANLIQUERA', 'SIENA', 'VASQUEZ', '', 26, 3, '06/20/2025', '03:28 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(851, 'BSY3X4CY3E', 'personnelImg/default_img.jpg', '68550dc190bea.jpg', 'SANTES', 'JOCELYN', 'CORONEL', '', 24, 3, '06/20/2025', '03:29 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(852, 'WXV3CGLSBO', 'personnelImg/default_img.jpg', '68550e4190ec1.jpg', 'GAYOMALE', 'JOSE ROBERT', 'MILLAN', 'JR. ', 24, 3, '06/20/2025', '03:31 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(853, 'WEYJU63LLE', 'personnelImg/default_img.jpg', '68550e4c8dda3.jpg', 'BILBAO', 'MA. TERESA', 'LOCSIN', '', 24, 3, '06/20/2025', '03:31 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(854, 'BCUT6421HG', 'personnelImg/default_img.jpg', '68550e558f81d.jpg', 'CANDULIZAS', 'MOLAVE', 'TEMBREVILLA', '', 2, 3, '06/20/2025', '03:31 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(855, '1Z6LX20PVU', 'personnelImg/default_img.jpg', '68550e5d8f511.jpg', 'RELIQUIAS', 'JOHN MARK', 'BALUNGCAS', '', 23, 3, '06/20/2025', '03:31 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(856, '55SDU3PJSI', 'personnelImg/default_img.jpg', '68550e638e2a8.jpg', 'ENCOY', 'JEFRE', 'LAZALITA', '', 24, 3, '06/20/2025', '03:31 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(857, 'T1X1EOAO50', 'personnelImg/default_img.jpg', '68550e6a8c3b1.jpg', 'HERRADURA', 'GINALYN ', 'OBENZA', '', 2, 3, '06/20/2025', '03:31 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(858, 'KE4HXNTQTI', 'personnelImg/default_img.jpg', '68550e718fb6a.jpg', 'BILBAO', 'FRANCISCO JOSE', 'LOCSIN', 'JR. ', 24, 3, '06/20/2025', '03:32 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(859, 'SPRSLW2YQM', 'personnelImg/default_img.jpg', '68550e778ea3f.jpg', 'OCTAVIO', 'JODYBONNE', 'GAYATIN', '', 24, 3, '06/20/2025', '03:32 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(860, 'E6FJPBZ5BD', 'personnelImg/default_img.jpg', '68550e7d8d199.jpg', 'TUPAS', 'JASON', 'TEMBREVILLA', '', 24, 3, '06/20/2025', '03:32 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(861, 'KCTG5YC64S', 'personnelImg/default_img.jpg', '68550e838cdd1.jpg', 'TUBILLEJA', 'THEODORE', 'DELA CRUZ', 'SR. ', 24, 3, '06/20/2025', '03:32 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(862, 'D4ZWVFKO1E', 'personnelImg/default_img.jpg', '68550ed08d91e.jpg', 'LLAMADO', 'MARISSA', 'MAQUILING', '', 2, 3, '06/20/2025', '03:33 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(863, 'UV0LOFMFS3', 'personnelImg/default_img.jpg', '68550efb8e5e2.jpg', 'LOGRONIO', 'JOESIFIL', 'FAJARDO', '', 2, 3, '06/20/2025', '03:34 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(864, '5BJ6IAN1FB', 'personnelImg/default_img.jpg', '68550f2c9085c.jpg', 'ORBIGOSO', 'ELIZABETH', 'SENIO', '', 8, 3, '06/20/2025', '03:35 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(865, 'ERKTAZMQZA', 'personnelImg/default_img.jpg', '68550fbd8cbb0.jpg', 'CARDINAL', 'RYAN', 'A.', '', 24, 3, '06/20/2025', '03:37 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(866, 'TXNUYSMIPH', 'personnelImg/default_img.jpg', '68550fc58c3a5.jpg', 'ACHA', 'ANGELA', '', '', 26, 3, '06/20/2025', '03:37 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(867, 'CY3EN2VVKO', 'personnelImg/default_img.jpg', '68550fce8b278.jpg', 'ACADEMIA', 'MELINDA', '', '', 26, 3, '06/20/2025', '03:37 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(868, '03THKL40WG', 'personnelImg/default_img.jpg', '68550fd696669.jpg', 'ABUGAN', 'ARMELITO', '', '', 26, 3, '06/20/2025', '03:37 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(869, 'YBD20JEALC', 'personnelImg/default_img.jpg', '68550fde8e5a6.jpg', 'ABRIGANA', 'PAMELA', '', '', 26, 3, '06/20/2025', '03:38 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(870, 'XFSKZYRVSI', 'personnelImg/default_img.jpg', '68550fe991687.jpg', 'LASTRILLA', 'GUIDRALYN', 'P.', '', 7, 3, '06/20/2025', '03:38 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(871, 'KHA2LUXNL1', 'personnelImg/default_img.jpg', '68550ff18ce9c.jpg', 'BA-AL', 'VON MARVIN', 'M.', '', 2, 3, '06/20/2025', '03:38 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(872, 'RVR5BBU5ZV', 'personnelImg/default_img.jpg', '68550fff8ea28.jpg', 'CANA', 'EVA MAE', 'G.', '', 11, 3, '06/20/2025', '03:38 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(873, 'JGSKGE5D2W', 'personnelImg/default_img.jpg', '68551008918cd.jpg', 'DELA CONCEPTION', 'IMELDA', 'E.', '', 10, 3, '06/20/2025', '03:38 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(874, '2CKQGJE4K6', 'personnelImg/default_img.jpg', '6855100e8f4ca.jpg', 'DELLOSO', 'CHRISTOPHER', 'M.', '', 14, 3, '06/20/2025', '03:38 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(875, 'QWV6R0YTL4', 'personnelImg/default_img.jpg', '685512088f24e.jpg', 'ALCON', 'MARYLADGIE', '', '', 26, 3, '06/20/2025', '03:47 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(876, 'PYYVDHBX2B', 'personnelImg/default_img.jpg', '685512198d39a.jpg', 'ACHINOVA', 'RANTE', '', '', 26, 3, '06/20/2025', '03:47 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(877, 'N3UASPXZ1I', 'personnelImg/default_img.jpg', '685512228ff09.jpg', 'AGAN', 'MA.THARA', '', '', 26, 3, '06/20/2025', '03:47 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(878, 'Q1DKJASL5Q', 'personnelImg/default_img.jpg', '6855122b8e6b4.jpg', 'ADOLFO', 'JOEY', '', '', 26, 3, '06/20/2025', '03:47 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(879, 'YL0IE3CYT5', 'personnelImg/default_img.jpg', '685512358d3fa.jpg', 'ALACIO', 'DENNIS', '', '', 26, 3, '06/20/2025', '03:48 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(880, 'FTBBVTMHBJ', 'personnelImg/default_img.jpg', '685512428e444.jpg', 'ALBIO', 'JESUSITO', '', '', 26, 3, '06/20/2025', '03:48 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(881, '2X2IGRWQ3F', 'personnelImg/default_img.jpg', '6855124e8b951.jpg', 'ALBERASTINE', 'HAZEL', '', '', 26, 3, '06/20/2025', '03:48 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(882, '4YOQ2PV6LX', 'personnelImg/default_img.jpg', '685512568d62d.jpg', 'ALBACITE', 'DIOVEN', '', '', 26, 3, '06/20/2025', '03:48 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(883, '4QWVB2OCKJ', 'personnelImg/default_img.jpg', '685512628eaae.jpg', 'ALACIO', 'RANEL', '', '', 26, 3, '06/20/2025', '03:48 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(884, 'SKFQQYSRUH', 'personnelImg/default_img.jpg', '6855126d8fbdc.jpg', 'ALACIO', 'JOIE', '', '', 26, 3, '06/20/2025', '03:49 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(885, '4RE6HDPT3R', 'personnelImg/default_img.jpg', '6855127f91c19.jpg', 'JIMENEZ', 'LEO', 'TOMILBA', '', 14, 3, '06/20/2025', '03:49 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(886, 'WDB3J415ZP', 'personnelImg/default_img.jpg', '6855135d8db1c.jpg', 'AKOL', 'SHARRA MAE', 'ARTICA', '', 10, 3, '06/20/2025', '03:53 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(887, '56EZ3T4YP2', 'personnelImg/default_img.jpg', '685513ab8dd18.jpg', 'CALUMBA', 'ANALYN', 'MANILINGAN', '', 25, 3, '06/20/2025', '03:54 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(888, 'N2CS52WG6G', 'personnelImg/default_img.jpg', '685513ef8c692.jpg', 'GIGANAN', 'GRACE JOY', 'ESTRAO', '', 2, 3, '06/20/2025', '03:55 PM', 0, 'on', 'PM IN', '192.168.1.16', '', '', 0),
(889, 'CMDUXPZ44I', 'personnelImg/default_img.jpg', '685514268f3d8.jpg', 'GONZAL', 'SANDRA', 'CASTILLO', '', 8, 3, '06/20/2025', '03:56 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(890, 'UV0LOFMFS3', 'personnelImg/default_img.jpg', '685515d291162.jpg', 'LOGRONIO', 'JOESIFIL', 'FAJARDO', '', 2, 3, '06/20/2025', '04:03 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(891, 'D4ZWVFKO1E', 'personnelImg/default_img.jpg', '685515e48e3d0.jpg', 'LLAMADO', 'MARISSA', 'MAQUILING', '', 2, 3, '06/20/2025', '04:03 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(892, 'WXV3CGLSBO', 'personnelImg/default_img.jpg', '685517238f06d.jpg', 'GAYOMALE', 'JOSE ROBERT', 'MILLAN', 'JR. ', 24, 3, '06/20/2025', '04:09 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(893, 'T1X1EOAO50', 'personnelImg/default_img.jpg', '6855173b8dbd0.jpg', 'HERRADURA', 'GINALYN ', 'OBENZA', '', 2, 3, '06/20/2025', '04:09 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(894, 'KE4HXNTQTI', 'personnelImg/default_img.jpg', '6855175590b7d.jpg', 'BILBAO', 'FRANCISCO JOSE', 'LOCSIN', 'JR. ', 24, 3, '06/20/2025', '04:09 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(895, '55SDU3PJSI', 'personnelImg/default_img.jpg', '68551767902ac.jpg', 'ENCOY', 'JEFRE', 'LAZALITA', '', 24, 3, '06/20/2025', '04:10 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(896, '1Z6LX20PVU', 'personnelImg/default_img.jpg', '685517768be99.jpg', 'RELIQUIAS', 'JOHN MARK', 'BALUNGCAS', '', 23, 3, '06/20/2025', '04:10 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(897, 'BCUT6421HG', 'personnelImg/default_img.jpg', '6855178c90fde.jpg', 'CANDULIZAS', 'MOLAVE', 'TEMBREVILLA', '', 2, 3, '06/20/2025', '04:10 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(898, 'WEYJU63LLE', 'personnelImg/default_img.jpg', '685517a88db9b.jpg', 'BILBAO', 'MA. TERESA', 'LOCSIN', '', 24, 3, '06/20/2025', '04:11 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(899, 'KCTG5YC64S', 'personnelImg/default_img.jpg', '685517bb8d61e.jpg', 'TUBILLEJA', 'THEODORE', 'DELA CRUZ', 'SR. ', 24, 3, '06/20/2025', '04:11 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(900, 'GSQZ2OCVHQ', 'personnelImg/default_img.jpg', '685517ec8dad2.jpg', 'RELIQUIAS', 'GERALD', 'TORIANO', '', 14, 3, '06/20/2025', '04:12 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(901, 'O6JWSQ5BTR', 'personnelImg/default_img.jpg', '685518118bfca.jpg', 'DELA CONCEPTION', 'IMELDA', 'ESPENORIO', '', 14, 3, '06/20/2025', '04:13 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(902, 'M1Y5ETAYD6', 'personnelImg/default_img.jpg', '6855182c8cc0d.jpg', 'NAVA', 'LUZ SALOME', 'VILLA', '', 14, 3, '06/20/2025', '04:13 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(903, 'Q0QB3ZVYNK', 'personnelImg/default_img.jpg', '685518418e042.jpg', 'VILLARETE', 'ANGELA', 'TELONIO', '', 7, 3, '06/20/2025', '04:13 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(904, 'Q0BSK1IGMH', 'personnelImg/default_img.jpg', '685518568c84a.jpg', 'BARIQUIT', 'PRACEDES', 'CABONILAS', '', 7, 3, '06/20/2025', '04:14 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(905, 'PWU0GP4SHN', 'personnelImg/default_img.jpg', '6855186c8c94e.jpg', 'SANTES', 'AZELA', 'FLORES', '', 7, 3, '06/20/2025', '04:14 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(906, '2SJBOJTXPC', 'personnelImg/default_img.jpg', '685518818d3c6.jpg', 'GELLECANAO', 'MURIELLE', 'LIRAZAN', '', 7, 3, '06/20/2025', '04:14 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(907, 'KP6G24TB40', 'personnelImg/default_img.jpg', '685518a68d3d4.jpg', 'RELADO', 'MEDALIA', 'VALENZUELA', '', 8, 3, '06/20/2025', '04:15 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(908, 'POT5DLCLXZ', 'personnelImg/default_img.jpg', '685518d18f8d8.jpg', 'GUINTOS', 'TINA MARIE', 'LUGA', '', 16, 3, '06/20/2025', '04:16 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(909, 'GRN63LXTVC', 'personnelImg/default_img.jpg', '685518e98d75d.jpg', 'GIGANAN', 'AGNES', 'NAVA', '', 15, 3, '06/20/2025', '04:16 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(910, 'J3UWH2Z32E', 'personnelImg/default_img.jpg', '685518fa8dae7.jpg', 'TRINIDAD', 'RODOLFO', 'PULGAN', 'JR. ', 15, 3, '06/20/2025', '04:16 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(911, 'EKSUEACJM5', 'personnelImg/default_img.jpg', '6855191992180.jpg', 'ACIBIDO', 'CHE GENEROSO', 'PARO', '', 14, 3, '06/20/2025', '04:17 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(912, 'PLCEEHAUGT', 'personnelImg/default_img.jpg', '6855192f8d402.jpg', 'MARQUEZ', 'MAE ANN', 'GIGANAN', '', 15, 3, '06/20/2025', '04:17 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(913, 'W2DGEUTP62', 'personnelImg/default_img.jpg', '685519478eab8.jpg', 'LOREDO', 'MARIA ELENA', 'DELLOSO', '', 14, 3, '06/20/2025', '04:18 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(914, 'FU6VB3OYK4', 'personnelImg/default_img.jpg', '6855195e8cb63.jpg', 'MANGOGTONG', 'WILMAR', 'ESTRELLA', '', 14, 3, '06/20/2025', '04:18 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(915, 'GOYLQLNANM', 'personnelImg/default_img.jpg', '6855196f9087e.jpg', 'DELOTINA', 'DANILO', 'NAPIERE', '', 17, 3, '06/20/2025', '04:18 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(916, 'R52SQSFJB4', 'personnelImg/default_img.jpg', '6855198f92f49.jpg', 'BONILLA', 'JURY', 'BENLOT', '', 17, 3, '06/20/2025', '04:19 PM', 0, 'on', 'PM OUT', '192.168.1.16', '', '', 0),
(917, 'KVKMFQDZDV', 'personnelImg/default_img.jpg', '685b9a3a11a42.jpg', 'GA-AN', 'LENLY', 'NOSAL', '', 10, 3, '06/25/2025', '02:42 PM', 0, 'on', 'PM IN', '127.0.0.1', '', '', 0),
(918, '5BJ6IAN1FB', 'personnelImg/default_img.jpg', '685b9a6604806.jpg', 'ORBIGOSO', 'ELIZABETH', 'SENIO', '', 8, 3, '06/25/2025', '02:42 PM', 0, 'on', 'PM IN', '127.0.0.1', '', '', 0),
(919, 'C4XW3NQ3CE', 'personnelImg/default_img.jpg', '685b9a75045e2.jpg', 'ACADEMIA', 'RIZALYN', 'CAYANAN', '', 9, 3, '06/25/2025', '02:42 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(920, '2XYVUZG44K', 'personnelImg/default_img.jpg', '685b9a800786c.jpg', 'RELIQUIAS', 'JOERIBEL', 'TORIANO', '', 9, 3, '06/25/2025', '02:43 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(921, 'VUM5AWYEUH', 'personnelImg/default_img.jpg', '685b9ac50454e.jpg', 'SUMUGAT', 'JENELYN', 'TELONIO', '', 3, 3, '06/25/2025', '02:44 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(922, 'R52SQSFJB4', 'personnelImg/default_img.jpg', '685b9b55054b4.jpg', 'BONILLA', 'JURY', 'BENLOT', '', 17, 3, '06/25/2025', '02:46 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(923, 'GOYLQLNANM', 'personnelImg/default_img.jpg', '685b9b6a052cf.jpg', 'DELOTINA', 'DANILO', 'NAPIERE', '', 17, 3, '06/25/2025', '02:47 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(924, 'FU6VB3OYK4', 'personnelImg/default_img.jpg', '685b9b8b02a2d.jpg', 'MANGOGTONG', 'WILMAR', 'ESTRELLA', '', 14, 3, '06/25/2025', '02:47 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(925, 'W2DGEUTP62', 'personnelImg/default_img.jpg', '685b9b9d02b14.jpg', 'LOREDO', 'MARIA ELENA', 'DELLOSO', '', 14, 3, '06/25/2025', '02:47 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(926, 'PLCEEHAUGT', 'personnelImg/default_img.jpg', '685b9bac04dcb.jpg', 'MARQUEZ', 'MAE ANN', 'GIGANAN', '', 15, 3, '06/25/2025', '02:48 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(927, 'EKSUEACJM5', 'personnelImg/default_img.jpg', '685b9bcf03632.jpg', 'ACIBIDO', 'CHE GENEROSO', 'PARO', '', 14, 3, '06/25/2025', '02:48 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(928, 'J3UWH2Z32E', 'personnelImg/default_img.jpg', '685b9be2035d7.jpg', 'TRINIDAD', 'RODOLFO', 'PULGAN', 'JR. ', 15, 3, '06/25/2025', '02:49 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(929, 'GRN63LXTVC', 'personnelImg/default_img.jpg', '685b9c0205dec.jpg', 'GIGANAN', 'AGNES', 'NAVA', '', 15, 3, '06/25/2025', '02:49 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(930, 'POT5DLCLXZ', 'personnelImg/default_img.jpg', '685b9c1003719.jpg', 'GUINTOS', 'TINA MARIE', 'LUGA', '', 16, 3, '06/25/2025', '02:49 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(931, 'PWU0GP4SHN', 'personnelImg/default_img.jpg', '685b9c1b03494.jpg', 'SANTES', 'AZELA', 'FLORES', '', 7, 3, '06/25/2025', '02:50 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(932, 'KP6G24TB40', 'personnelImg/default_img.jpg', '685b9c2104ac7.jpg', 'RELADO', 'MEDALIA', 'VALENZUELA', '', 8, 3, '06/25/2025', '02:50 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(933, '2SJBOJTXPC', 'personnelImg/default_img.jpg', '685b9c2b025fe.jpg', 'GELLECANAO', 'MURIELLE', 'LIRAZAN', '', 7, 3, '06/25/2025', '02:50 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(934, 'Q0BSK1IGMH', 'personnelImg/default_img.jpg', '685b9c3c07372.jpg', 'BARIQUIT', 'PRACEDES', 'CABONILAS', '', 7, 3, '06/25/2025', '02:50 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(935, 'Q0QB3ZVYNK', 'personnelImg/default_img.jpg', '685b9c4b048b8.jpg', 'VILLARETE', 'ANGELA', 'TELONIO', '', 7, 3, '06/25/2025', '02:50 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(936, 'M1Y5ETAYD6', 'personnelImg/default_img.jpg', '685b9c5103fdb.jpg', 'NAVA', 'LUZ SALOME', 'VILLA', '', 14, 3, '06/25/2025', '02:50 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(937, '4RE6HDPT3R', 'personnelImg/default_img.jpg', '685b9c5803a80.jpg', 'JIMENEZ', 'LEO', 'TOMILBA', '', 14, 3, '06/25/2025', '02:51 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(938, 'O6JWSQ5BTR', 'personnelImg/default_img.jpg', '685b9c6102ab0.jpg', 'DELA CONCEPTION', 'IMELDA', 'ESPENORIO', '', 14, 3, '06/25/2025', '02:51 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(939, 'GSQZ2OCVHQ', 'personnelImg/default_img.jpg', '685b9c740552b.jpg', 'RELIQUIAS', 'GERALD', 'TORIANO', '', 14, 3, '06/25/2025', '02:51 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(940, 'WEYJU63LLE', 'personnelImg/default_img.jpg', '685b9c8702dc3.jpg', 'BILBAO', 'MA. TERESA', 'LOCSIN', '', 24, 3, '06/25/2025', '02:51 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(941, 'KCTG5YC64S', 'personnelImg/default_img.jpg', '685b9c92019db.jpg', 'TUBILLEJA', 'THEODORE', 'DELA CRUZ', 'SR. ', 24, 3, '06/25/2025', '02:52 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(942, '55SDU3PJSI', 'personnelImg/default_img.jpg', '685b9cab035dd.jpg', 'ENCOY', 'JEFRE', 'LAZALITA', '', 24, 3, '06/25/2025', '02:52 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(943, 'BCUT6421HG', 'personnelImg/default_img.jpg', '685b9cae022d2.jpg', 'CANDULIZAS', 'MOLAVE', 'TEMBREVILLA', '', 2, 3, '06/25/2025', '02:52 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(944, '1Z6LX20PVU', 'personnelImg/default_img.jpg', '685b9cbf0752f.jpg', 'RELIQUIAS', 'JOHN MARK', 'BALUNGCAS', '', 23, 3, '06/25/2025', '02:52 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(945, 'KE4HXNTQTI', 'personnelImg/default_img.jpg', '685b9cda03625.jpg', 'BILBAO', 'FRANCISCO JOSE', 'LOCSIN', 'JR. ', 24, 3, '06/25/2025', '02:53 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(946, 'WXV3CGLSBO', 'personnelImg/default_img.jpg', '685b9ce906a44.jpg', 'GAYOMALE', 'JOSE ROBERT', 'MILLAN', 'JR. ', 24, 3, '06/25/2025', '02:53 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(947, 'T1X1EOAO50', 'personnelImg/default_img.jpg', '685b9cf104278.jpg', 'HERRADURA', 'GINALYN ', 'OBENZA', '', 2, 3, '06/25/2025', '02:53 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(948, 'N0PELGBXHV', 'personnelImg/default_img.jpg', '685b9d5b053b9.jpg', 'GUSTILO', 'LEILANI', 'DECENA', '', 9, 3, '06/25/2025', '02:55 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(949, '36EOSJANR6', 'personnelImg/default_img.jpg', '685b9d6904cc3.jpg', 'ENGCOY', 'CANTERLYN JOY', 'T.', '', 24, 3, '06/25/2025', '02:55 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(950, 'GP4Q0NOWGA', 'personnelImg/default_img.jpg', '685b9d81058f0.jpg', 'GERMINAL', 'CRISTUTO', 'M.', '', 17, 3, '06/25/2025', '02:55 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(951, 'M1RF4GW0TT', 'personnelImg/default_img.jpg', '685b9d90047c3.jpg', 'GUINTOS', 'MA. ELENA', 'N.', '', 14, 3, '06/25/2025', '02:56 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(952, 'BLJQQ3XW3K', 'personnelImg/default_img.jpg', '685b9da602c8b.jpg', 'NORVIE', 'JOSECO', 'A.', '', 11, 3, '06/25/2025', '02:56 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(953, 'O2NINN551F', 'personnelImg/default_img.jpg', '685b9dbe077ad.jpg', 'PARCON', 'MERCEDITA', 'T.', '', 7, 3, '06/25/2025', '02:57 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(954, 'QQZIGTSWBI', 'personnelImg/default_img.jpg', '685b9dcd05ee4.jpg', 'NACION', 'ELBRED', 'S.', '', 17, 3, '06/25/2025', '02:57 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(955, '6D0YWDKLPP', 'personnelImg/default_img.jpg', '685b9dda06114.jpg', 'AGUHAYON', 'RICARDO', 'UBAMOS', '', 14, 3, '06/25/2025', '02:57 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(956, 'YCSOHXN5TA', 'personnelImg/default_img.jpg', '685b9df502442.jpg', 'GALLARDA', 'ELNOR', 'PACLAONA', '', 12, 3, '06/25/2025', '02:57 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(957, 'OINBKY4BGA', 'personnelImg/default_img.jpg', '685b9e03060fd.jpg', 'DECATORIA', 'JOEBERT', 'SARIL', '', 3, 3, '06/25/2025', '02:58 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(958, 'F3DUWOJ4SZ', 'personnelImg/default_img.jpg', '685b9e190786b.jpg', 'SIASON', 'CHEZAH ERL', 'GAUAL', '', 16, 3, '06/25/2025', '02:58 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(959, '6KR3ZM4BU3', 'personnelImg/default_img.jpg', '685b9e2e04ca2.jpg', 'TELONIO', 'JAMES ANDREW', 'GUINTOS', '', 15, 3, '06/25/2025', '02:58 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(960, 'KUNUEE1ZKH', 'personnelImg/default_img.jpg', '685b9e4405895.jpg', 'YUSAY', 'JOSE', 'TOGLE', 'III ', 16, 3, '06/25/2025', '02:59 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(961, 'REPYPK1N0K', 'personnelImg/default_img.jpg', '685b9e5402d8f.jpg', 'AMBAGAN', 'EDSEL', 'MILLENDEZ', '', 16, 3, '06/25/2025', '02:59 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(962, 'MMB6JKH1T3', 'personnelImg/default_img.jpg', '685b9e6605ca2.jpg', 'CAMBARIJAN', 'JIMMY', 'ZETA', '', 25, 3, '06/25/2025', '02:59 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(963, '56EZ3T4YP2', 'personnelImg/default_img.jpg', '685b9e7c0558b.jpg', 'CALUMBA', 'ANALYN', 'MANILINGAN', '', 25, 3, '06/25/2025', '03:00 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(964, 'JT6NRMM2MG', 'personnelImg/default_img.jpg', '685b9e91043c2.jpg', 'LABRADOR', 'REY', 'TRIBUCIO', '', 10, 3, '06/25/2025', '03:00 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(965, '2CKQGJE4K6', 'personnelImg/default_img.jpg', '685b9ea205628.jpg', 'DELLOSO', 'CHRISTOPHER', 'M.', '', 14, 3, '06/25/2025', '03:00 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(966, 'JGSKGE5D2W', 'personnelImg/default_img.jpg', '685b9eb403585.jpg', 'DELA CONCEPTION', 'IMELDA', 'E.', '', 10, 3, '06/25/2025', '03:01 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(967, 'RVR5BBU5ZV', 'personnelImg/default_img.jpg', '685b9ec8034d9.jpg', 'CANA', 'EVA MAE', 'G.', '', 11, 3, '06/25/2025', '03:01 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(968, 'KHA2LUXNL1', 'personnelImg/default_img.jpg', '685b9ed902238.jpg', 'BA-AL', 'VON MARVIN', 'M.', '', 2, 3, '06/25/2025', '03:01 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(969, 'XFSKZYRVSI', 'personnelImg/default_img.jpg', '685b9ef604dd0.jpg', 'LASTRILLA', 'GUIDRALYN', 'P.', '', 7, 3, '06/25/2025', '03:02 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(970, 'ERKTAZMQZA', 'personnelImg/default_img.jpg', '685b9f0c090bd.jpg', 'CARDINAL', 'RYAN', 'A.', '', 24, 3, '06/25/2025', '03:02 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(971, '5BJ6IAN1FB', 'personnelImg/default_img.jpg', '685b9f2702763.jpg', 'ORBIGOSO', 'ELIZABETH', 'SENIO', '', 8, 3, '06/25/2025', '03:03 PM', 0, 'on', 'PM OUT', '192.168.1.18', '', '', 0),
(972, 'UV0LOFMFS3', 'personnelImg/default_img.jpg', '685b9f3801d0b.jpg', 'LOGRONIO', 'JOESIFIL', 'FAJARDO', '', 2, 3, '06/25/2025', '03:03 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(973, 'D4ZWVFKO1E', 'personnelImg/default_img.jpg', '685b9f4502607.jpg', 'LLAMADO', 'MARISSA', 'MAQUILING', '', 2, 3, '06/25/2025', '03:03 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(974, 'E6FJPBZ5BD', 'personnelImg/default_img.jpg', '685b9f5506a98.jpg', 'TUPAS', 'JASON', 'TEMBREVILLA', '', 24, 3, '06/25/2025', '03:03 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(975, 'GJCC6XY3BR', 'personnelImg/default_img.jpg', '685b9f6801dab.jpg', 'ANTIQUEÑO', 'ANA MARIE', 'SANOY', '', 12, 3, '06/25/2025', '03:04 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(976, 'XD5JB3HCC0', 'personnelImg/default_img.jpg', '685b9f7d01043.jpg', 'LAREÑO', 'LIWAYA', 'MAHINAY', '', 11, 3, '06/25/2025', '03:04 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(977, 'E6LFKFXAD0', 'personnelImg/default_img.jpg', '685b9f9100d63.jpg', 'NUÑESCO', 'AIZA', 'ANDO', '', 5, 3, '06/25/2025', '03:04 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(978, 'WILZVWBBRI', 'personnelImg/default_img.jpg', '685b9fa802269.jpg', 'OCCEÑA', 'GEM', 'RELAMPAGOS', '', 3, 3, '06/25/2025', '03:05 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(979, 'SARR3EM6ZV', 'personnelImg/default_img.jpg', '685b9fbd01fcd.jpg', 'PACURIB', 'JULITO', 'PLAÑA', 'JR. ', 3, 3, '06/25/2025', '03:05 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(980, 'WDB3J415ZP', 'personnelImg/default_img.jpg', '685b9fd2030cc.jpg', 'AKOL', 'SHARRA MAE', 'ARTICA', '', 10, 3, '06/25/2025', '03:05 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(981, 'R0VM3TX265', 'personnelImg/default_img.jpg', '685b9fec04bd3.jpg', 'MONTANO', 'EVALYN', 'C.', '', 24, 3, '06/25/2025', '03:06 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(982, 'WZZQ44GGKF', 'personnelImg/default_img.jpg', '685ba0010671b.jpg', 'YUSAY', 'ROMEO', 'A.', 'JR. ', 24, 3, '06/25/2025', '03:06 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(983, 'T0SO3GEQZX', 'personnelImg/default_img.jpg', '685ba00f03448.jpg', 'TUPAS', 'ANTHONY', 'L.', '', 11, 3, '06/25/2025', '03:06 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(984, 'HOA5M4V2KU', 'personnelImg/default_img.jpg', '685ba02a02ffd.jpg', 'TEMBREVILLA', 'THOMY', 'G.', '', 3, 3, '06/25/2025', '03:07 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(985, 'OIMV5KOLF2', 'personnelImg/default_img.jpg', '685ba0430483c.jpg', 'PEROSIA', 'GLORY MAE', 'TUNDA-AN', '', 12, 3, '06/25/2025', '03:07 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(986, 'PMOLWNYZZA', 'personnelImg/default_img.jpg', '685ba077044f1.jpg', 'SUSANA', 'FREDERICK DAVY', 'PINGCALE', '', 12, 3, '06/25/2025', '03:08 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(987, 'E0VZSU0W6G', 'personnelImg/default_img.jpg', '685ba09803bc1.jpg', 'APLAON', 'MA. LEE', 'MAESTRECAMPO', '', 23, 3, '06/25/2025', '03:09 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(988, 'PQMSBLBNJ6', 'personnelImg/default_img.jpg', '685ba0ac042f8.jpg', 'VIDAURRAZAGA', 'NOAH', 'VASQUEZ', '', 2, 3, '06/25/2025', '03:09 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(989, 'F255HSX3FM', 'personnelImg/default_img.jpg', '685ba0bf06490.jpg', 'GUINTOS', 'ERICA', 'MANANGAN', '', 14, 3, '06/25/2025', '03:09 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(990, 'CCEWWUW3P2', 'personnelImg/default_img.jpg', '685ba0cd02ffb.jpg', 'TUBILLEJA', 'THEODORE', 'SIGUEZA', 'JR. ', 6, 3, '06/25/2025', '03:10 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(991, 'JETERWW23U', 'personnelImg/nrf5ihz6-mae-flor.jpg', '685ba0e804111.jpg', 'BARRIOS', 'MAE FLOR', 'ALMAIZ', '', 8, 3, '06/25/2025', '03:10 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(992, 'YFUCRYEDIV', 'personnelImg/nrfc1ohg-jet.jpg', '685ba0f5036b4.jpg', 'BARROCA', 'JETTER', 'SENIO', '', 14, 3, '06/25/2025', '03:10 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(993, '111LXWXFLY', 'personnelImg/default_img.jpg', '685ba10306f0a.jpg', 'NICOR', 'MICHAEL', 'RAMOS', '', 7, 3, '06/25/2025', '03:10 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(994, 'SJLE6GGLD6', 'personnelImg/nrf04z55-che.jpg', '685ba177027e1.jpg', 'JUAREZ', 'CHERRY LYNN', 'MONTAÑO', '', 2, 3, '06/25/2025', '03:12 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(995, '65VQESUT03', 'personnelImg/default_img.jpg', '685ba18601105.jpg', 'PANGANTIHON', 'JOHN IRVING', 'TOLEDO', '', 3, 3, '06/25/2025', '03:13 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(996, 'Z2TDUSRUV6', 'personnelImg/default_img.jpg', '685ba19e01615.jpg', 'BIACA', 'HERBERT', 'BORNALES', '', 3, 3, '06/25/2025', '03:13 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(997, 'R0ZJXTVRR4', 'personnelImg/default_img.jpg', '685ba1b1025b9.jpg', 'AVELINO', 'JULIEBERT', 'AZUCENA', '', 2, 3, '06/25/2025', '03:13 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(998, 'BSLAWXCQ2N', 'personnelImg/default_img.jpg', '685ba1c40827c.jpg', 'BAYDO', 'JULITO', 'CAYAS', '', 3, 3, '06/25/2025', '03:14 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(999, 'EEG0NY2B5U', 'personnelImg/default_img.jpg', '685ba1d8024a1.jpg', 'ARBOIZ', 'JOHN', 'VILLARICO', '', 15, 3, '06/25/2025', '03:14 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1000, 'LS5IJYTERX', 'personnelImg/default_img.jpg', '685ba2150297d.jpg', 'GUINTOS', 'WINSTON', 'NAVA', '', 3, 3, '06/25/2025', '03:15 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1001, 'UUR4C35PLC', 'personnelImg/default_img.jpg', '685ba22a038a3.jpg', 'ALIMANE', 'JINKY', 'GALPO', '', 7, 3, '06/25/2025', '03:15 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1002, 'P-ZGT-382010-110', 'personnelImg/default_img.jpg', '685ba2570673a.jpg', 'TEMBREVILLA', 'ZOSIMO', 'GAVILAGA', '', 3, 3, '06/25/2025', '03:16 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1003, 'FOSBE6FBBP', 'personnelImg/default_img.jpg', '685ba26b03307.jpg', 'MAHINAY', 'FRANCISCO', 'MANINANTAN', 'SR. ', 3, 3, '06/25/2025', '03:16 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1004, 'FKX31XYW34', 'personnelImg/default_img.jpg', '685ba27c0281c.jpg', 'DELA FUENTE', 'ALVIN', 'ORMEO', '', 11, 3, '06/25/2025', '03:17 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1005, '2M5F54VPSN', 'personnelImg/default_img.jpg', '685ba29c04d6c.jpg', 'AKOL', 'MARLOU', 'ARTICA', '', 14, 3, '06/25/2025', '03:17 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1006, '4RE6HDPT3R', 'personnelImg/default_img.jpg', '685ba2d902b0b.jpg', 'JIMENEZ', 'LEO', 'TOMILBA', '', 14, 3, '06/25/2025', '03:18 PM', 0, 'on', 'PM OUT', '192.168.1.18', '', '', 0),
(1007, '2XYVUZG44K', 'personnelImg/default_img.jpg', '685ba32303c35.jpg', 'RELIQUIAS', 'JOERIBEL', 'TORIANO', '', 9, 3, '06/25/2025', '03:20 PM', 0, 'on', 'PM OUT', '192.168.1.18', '', '', 0),
(1008, 'C4XW3NQ3CE', 'personnelImg/default_img.jpg', '685ba33706947.jpg', 'ACADEMIA', 'RIZALYN', 'CAYANAN', '', 9, 3, '06/25/2025', '03:20 PM', 0, 'on', 'PM OUT', '192.168.1.18', '', '', 0),
(1009, 'KVKMFQDZDV', 'personnelImg/default_img.jpg', '685ba36e01cb4.jpg', 'GA-AN', 'LENLY', 'NOSAL', '', 10, 3, '06/25/2025', '03:21 PM', 0, 'on', 'PM OUT', '192.168.1.18', '', '', 0),
(1010, 'EAUNV5WVM0', 'personnelImg/default_img.jpg', '685ba37d024bd.jpg', 'BARROCA', 'JORGIE', 'PERFUMA', '', 6, 3, '06/25/2025', '03:21 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1011, 'B42WBNMNSK', 'personnelImg/default_img.jpg', '685ba38b01f52.jpg', 'LIMSIACO', 'ARIANE', 'CONSTANTINO', '', 6, 3, '06/25/2025', '03:21 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1012, 'UBFPHM020G', 'personnelImg/default_img.jpg', '685ba3a002438.jpg', 'BONILLA', 'ANNI VER', 'PAMLIEGA', '', 6, 3, '06/25/2025', '03:22 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1013, 'DO2X14YKJR', 'personnelImg/default_img.jpg', '685ba3af058bc.jpg', 'VILLARUBIA', 'ALTHEA', 'PIA', '', 5, 3, '06/25/2025', '03:22 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1014, 'VUM5AWYEUH', 'personnelImg/default_img.jpg', '685ba3c30299f.jpg', 'SUMUGAT', 'JENELYN', 'TELONIO', '', 3, 3, '06/25/2025', '03:22 PM', 0, 'on', 'PM OUT', '192.168.1.18', '', '', 0),
(1015, '2KLYB0WHDY', 'personnelImg/default_img.jpg', '685ba3d30358e.jpg', 'GESTOSO', 'CARLITO', 'BAVIERA', '', 10, 3, '06/25/2025', '03:22 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1016, 'T4BXLLYWTG', 'personnelImg/default_img.jpg', '685ba3e605f3c.jpg', 'DERIT', 'JOSE ALAN ', 'DURO', '', 17, 3, '06/25/2025', '03:23 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1017, 'ES1MY5CB5N', 'personnelImg/default_img.jpg', '685ba3f500a09.jpg', 'BUDACA', 'REVINIA', 'AMACIO', '', 10, 3, '06/25/2025', '03:23 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1018, 'N3CFWPHFQX', 'personnelImg/default_img.jpg', '685ba40602e3a.jpg', 'SANTES', 'CAROL ANN', 'TILOS', '', 5, 3, '06/25/2025', '03:23 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1019, '0RVK3VBCZ1', 'personnelImg/default_img.jpg', '685ba41203657.jpg', 'MANOS', 'ANNABELLE', 'PERIGUA', '', 5, 3, '06/25/2025', '03:24 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1020, 'APPZCXZCJS', 'personnelImg/default_img.jpg', '685ba41e0237b.jpg', 'MIRANDA', 'JAIME', 'MAULIT', '', 5, 3, '06/25/2025', '03:24 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1021, 'KZFCP2S0HT', 'personnelImg/default_img.jpg', '685ba42e01de3.jpg', 'GUINTOS', 'JOSEPHINE', 'INDINO', '', 10, 3, '06/25/2025', '03:24 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1022, 'CEPHX3JOM0', 'personnelImg/default_img.jpg', '685ba43b05231.jpg', 'TIBAYDE', 'IRENE', 'GIGANAN', '', 10, 3, '06/25/2025', '03:24 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1023, 'NZ0IKZI1IR', 'personnelImg/default_img.jpg', '685ba44a039be.jpg', 'MANGOGTONG', 'MA. MARILOU ', 'ESTRELLA', '', 10, 3, '06/25/2025', '03:24 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1024, '2SOQJG3P6F', 'personnelImg/default_img.jpg', '685ba45d02bc6.jpg', 'SANTES', 'CINDY', 'REBOLDAL', '', 4, 3, '06/25/2025', '03:25 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1025, 'OSYDS2VNEW', 'personnelImg/default_img.jpg', '685ba47002c4e.jpg', 'ABALLE', 'BETHEL', 'PERFUMA', '', 10, 3, '06/25/2025', '03:25 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1026, 'HR1TD1EOO6', 'personnelImg/default_img.jpg', '685ba47d02b0a.jpg', 'NATALIO', 'JOEFREY', 'TEMBREVILLA', '', 9, 3, '06/25/2025', '03:25 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1027, 'GZ5LO0QDRC', 'personnelImg/default_img.jpg', '685ba48c040cd.jpg', 'GARCIA', 'ADELA', 'ANTOLIN', '', 12, 3, '06/25/2025', '03:26 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1028, 'HEPAB6X3PL', 'personnelImg/default_img.jpg', '685ba49a04656.jpg', 'VILLAMATER', 'LORALYN', 'BARROCA', '', 12, 3, '06/25/2025', '03:26 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1029, '2O1JSGHAYX', 'personnelImg/default_img.jpg', '685ba4a801d92.jpg', 'GAUAL', 'ROLIN', 'BERGONIO', 'SR. ', 12, 3, '06/25/2025', '03:26 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1030, 'MHDZI160KN', 'personnelImg/default_img.jpg', '685ba4b70b080.jpg', 'TEMBREVILLA', 'CAROLINE', 'CANILLO', '', 12, 3, '06/25/2025', '03:26 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1031, 'ZHXY3CO3KQ', 'personnelImg/default_img.jpg', '685ba4c80670d.jpg', 'DELA FUENTE', 'EBER', 'ORMEO', '', 12, 3, '06/25/2025', '03:27 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1032, 'I0FY3RFXM5', 'personnelImg/default_img.jpg', '685ba4da0493b.jpg', 'TILOS', 'OSCAR', 'VILLARETE', '', 4, 3, '06/25/2025', '03:27 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1033, 'WSJIHMXEN6', 'personnelImg/default_img.jpg', '685ba4ec01a1e.jpg', 'TILOS', 'MARCELINA', 'CELIS', '', 12, 3, '06/25/2025', '03:27 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1034, '6ON2RWCQEM', 'personnelImg/default_img.jpg', '685ba4fd07eba.jpg', 'GAREZA', 'MELANIE', 'CELIS', '', 12, 3, '06/25/2025', '03:27 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1035, 'RCCO5TPACC', 'personnelImg/default_img.jpg', '685ba50f0459b.jpg', 'PIMENTEL', 'EMY', 'MAGBANUA', '', 12, 3, '06/25/2025', '03:28 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1036, 'LTQHD2BPYJ', 'personnelImg/default_img.jpg', '685ba51d00bd7.jpg', 'GALON', 'RANDOLF', 'BLANCO', '', 4, 3, '06/25/2025', '03:28 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1037, 'MF4R0LP0PT', 'personnelImg/default_img.jpg', '685ba52b039fa.jpg', 'TOLEDO', 'RAYMUND ANTHONY', 'INOLINO', '', 4, 3, '06/25/2025', '03:28 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1038, 'HSDQZ6G6PA', 'personnelImg/default_img.jpg', '685ba53e03e84.jpg', 'PEREZ', 'JOSE MARIA', 'LIM', 'JR. ', 12, 3, '06/25/2025', '03:29 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1039, 'WQ4X6QJYGH', 'personnelImg/default_img.jpg', '685ba55001903.jpg', 'ANLIQUERA', 'SIENA', 'VASQUEZ', '', 26, 3, '06/25/2025', '03:29 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1040, 'NT36Z4UITB', 'personnelImg/default_img.jpg', '685ba565054ef.jpg', 'MALAYO', 'EDGARDO', 'FLORES', '', 24, 3, '06/25/2025', '03:29 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1041, 'DCORCD1ICP', 'personnelImg/default_img.jpg', '685ba57805311.jpg', 'TUMA-OB', 'LESLIE AIKEE DYAN', 'GIGANAN', '', 24, 3, '06/25/2025', '03:29 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1042, 'ZGMTZ2Y3BT', 'personnelImg/default_img.jpg', '685ba58b053e7.jpg', 'OCTAVIO', 'PETER JOHN ', 'LAZALITA', '', 24, 3, '06/25/2025', '03:30 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1043, '6DZNSFDRJG', 'personnelImg/default_img.jpg', '685ba59a02103.jpg', 'MANOS', 'TEOFILO', 'ENCARGUEZ', 'JR. ', 24, 3, '06/25/2025', '03:30 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1044, '44SB4W0MF3', 'personnelImg/default_img.jpg', '685ba5b00611d.jpg', 'VIDAURRAZAGA', 'MC', 'PEPITO', '', 24, 3, '06/25/2025', '03:30 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1045, '1QETKAFQWO', 'personnelImg/default_img.jpg', '685ba5c5031dd.jpg', 'DELOTINA', 'JOFEL', 'EVANGELISTA', '', 24, 3, '06/25/2025', '03:31 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1046, '2FUFQBHUQM', 'personnelImg/default_img.jpg', '685ba5d702435.jpg', 'DECENA', 'LANNE', 'VILLARETE', '', 24, 3, '06/25/2025', '03:31 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1047, 'DFR5QGBYHW', 'personnelImg/default_img.jpg', '685ba61503f73.jpg', 'BARO', 'PHOEBE', 'MANILINGAN', '', 12, 3, '06/25/2025', '03:32 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1048, 'SPRSLW2YQM', 'personnelImg/default_img.jpg', '685ba624017fc.jpg', 'OCTAVIO', 'JODYBONNE', 'GAYATIN', '', 24, 3, '06/25/2025', '03:32 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1049, 'BSY3X4CY3E', 'personnelImg/default_img.jpg', '685ba634011a8.jpg', 'SANTES', 'JOCELYN', 'CORONEL', '', 24, 3, '06/25/2025', '03:33 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1050, 'N0AXWJPKNQ', 'personnelImg/default_img.jpg', '685ba64503e4b.jpg', 'SORONGON', 'NITA', 'A.', '', 7, 3, '06/25/2025', '03:33 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1051, 'ITZ6XK1TGX', 'personnelImg/default_img.jpg', '685ba65500d8b.jpg', 'LUCENARA', 'ROBIJID', 'Q', '', 15, 3, '06/25/2025', '03:33 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1052, '0M1YGUSYJR', 'personnelImg/default_img.jpg', '685ba668055f5.jpg', 'BALOYO', 'HANSEL', 'M.', '', 11, 3, '06/25/2025', '03:33 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1053, 'DV6HSR4T31', 'personnelImg/default_img.jpg', '685ba67903ed8.jpg', 'SABOBO', 'ALFREDO', 'G.', '', 15, 3, '06/25/2025', '03:34 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1054, 'L33XK2MQYL', 'personnelImg/default_img.jpg', '685ba6870456a.jpg', 'RELIQUIAS', 'JOHNNY RAY', 'L.', '', 2, 3, '06/25/2025', '03:34 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1055, 'G6A2OEKXUH', 'personnelImg/default_img.jpg', '685ba69a01e8d.jpg', 'MANGILIMUTAN', 'MA. HEARTY', 'CAÑA', '', 12, 3, '06/25/2025', '03:34 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1056, 'H05GELOMZN', 'personnelImg/default_img.jpg', '685ba6ae01da7.jpg', 'DEQUINA', 'REYNOLD', 'B.', '', 17, 3, '06/25/2025', '03:35 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1057, 'VWUQQEPZ5P', 'personnelImg/default_img.jpg', '685ba6c7070d0.jpg', 'DIONALDO', 'ROEM', 'S.', '', 2, 3, '06/25/2025', '03:35 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1058, '5Z5WKCHQGF', 'personnelImg/default_img.jpg', '685ba6d6033f1.jpg', 'DOLOR', 'ROLY', 'D.', '', 11, 3, '06/25/2025', '03:35 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1059, '1YJTKOZ61A', 'personnelImg/default_img.jpg', '685ba6e601b5b.jpg', 'DURAN', 'MICHELLE', 'M.', '', 11, 3, '06/25/2025', '03:36 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1060, 'ZXDNHZFPMW', 'personnelImg/default_img.jpg', '685ba6f402628.jpg', 'PUBLICO', 'NOEMI', 'TOMADO', '', 12, 3, '06/25/2025', '03:36 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1061, 'LVX5C46TAS', 'personnelImg/default_img.jpg', '685ba70604839.jpg', 'SILVESTRE', 'CYNTHIA', 'LAMBOT', '', 12, 3, '06/25/2025', '03:36 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1062, 'LUPQ4RIQUJ', 'personnelImg/default_img.jpg', '685ba71707d9a.jpg', 'TILOS', 'RIZALIE', 'CELIZ', '', 12, 3, '06/25/2025', '03:36 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1063, '5UVF66ZPY0', 'personnelImg/default_img.jpg', '685ba7240452f.jpg', 'MAQUILING', 'WARREN', 'RAMIREZ', '', 12, 3, '06/25/2025', '03:37 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1064, 'V2M0IIAYRM', 'personnelImg/default_img.jpg', '685ba73504751.jpg', 'VILLANUEVA', 'RAYGILDA', 'VERGARA', '', 12, 3, '06/25/2025', '03:37 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1065, 'QESM444ZK0', 'personnelImg/default_img.jpg', '685ba74403f8c.jpg', 'TEMBREVILLA', 'RONA', 'ESCOSAR', '', 12, 3, '06/25/2025', '03:37 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1066, 'ZV1EXSSS30', 'personnelImg/default_img.jpg', '685ba755034e8.jpg', 'BONGCAWEL', 'GENELYN ', 'HISONA', '', 12, 3, '06/25/2025', '03:37 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1067, 'HDMCPVKYB1', 'personnelImg/default_img.jpg', '685ba7660416b.jpg', 'ROXAS', 'MARY ANN', 'VILLAFUERTE', '', 12, 3, '06/25/2025', '03:38 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1068, 'MS5LRO1IJE', 'personnelImg/default_img.jpg', '685ba7790541b.jpg', 'VILLANUEVA', 'ROSALIE', 'ELARDO', '', 12, 3, '06/25/2025', '03:38 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1069, 'I03I6VZELI', 'personnelImg/default_img.jpg', '685ba78903948.jpg', 'GALAN', 'MARY JANE', 'CELIS', '', 12, 3, '06/25/2025', '03:38 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1070, '1DSSFVBIFF', 'personnelImg/default_img.jpg', '685ba7970250e.jpg', 'MAESTRECAMPO', 'EVELYN', 'ELARMO', '', 7, 3, '06/25/2025', '03:39 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1071, 'CMDUXPZ44I', 'personnelImg/default_img.jpg', '685ba7a703c6a.jpg', 'GONZAL', 'SANDRA', 'CASTILLO', '', 8, 3, '06/25/2025', '03:39 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1072, 'DURSCXCSIM', 'personnelImg/default_img.jpg', '685ba7c206d68.jpg', 'MAYANDIA', 'ANALIE', 'ELARMO', '', 6, 3, '06/25/2025', '03:39 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1073, 'SP2WX6WZFC', 'personnelImg/default_img.jpg', '685ba7d301670.jpg', 'PADA', 'VERONICA EVELYN', 'ALBISO', '', 12, 3, '06/25/2025', '03:40 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1074, 'N4DZR4BBY0', 'personnelImg/default_img.jpg', '685ba7e30477d.jpg', 'RELIQUIAS', 'MARIA FE', 'GUINTOS', '', 8, 3, '06/25/2025', '03:40 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1075, 'DGPF5FDK53', 'personnelImg/default_img.jpg', '685ba7f300e10.jpg', 'ARANETA', 'JOSELITA', 'GENTELIZO', '', 6, 3, '06/25/2025', '03:40 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1076, 'V4PAJMJ5YO', 'personnelImg/default_img.jpg', '685ba80e02a94.jpg', 'TUPAS', 'OFELIA', 'TEMBREVILLA', '', 6, 3, '06/25/2025', '03:41 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1077, 'EHEYDZ1UYN', 'personnelImg/default_img.jpg', '685ba81e02647.jpg', 'ALATON', 'GINA', 'NABOR', '', 6, 3, '06/25/2025', '03:41 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1078, 'K0GBVLRIOM', 'personnelImg/default_img.jpg', '685ba83002ff7.jpg', 'JORDAN', 'MARK ANTHONY', 'TOMADO', '', 3, 3, '06/25/2025', '03:41 PM', 0, 'on', 'PM IN', '192.168.1.18', '', '', 0),
(1079, 'T0SO3GEQZX', 'personnelImg/default_img.jpg', '685baca6058ff.jpg', 'TUPAS', 'ANTHONY', 'L.', '', 11, 3, '06/25/2025', '04:00 PM', 0, 'on', 'PM OUT', '192.168.1.18', '', '', 0),
(1080, 'Q0QB3ZVYNK', 'personnelImg/default_img.jpg', '685bb7ab06521.jpg', 'VILLARETE', 'ANGELA', 'TELONIO', '', 7, 3, '06/25/2025', '04:47 PM', 0, 'off', 'PM OUT', '192.168.1.18', '', '', 0),
(1081, 'DO2X14YKJR', 'personnelImg/default_img.jpg', '685bb7c904d47.jpg', 'VILLARUBIA', 'ALTHEA', 'PIA', '', 5, 3, '06/25/2025', '04:48 PM', 0, 'off', 'PM OUT', '192.168.1.18', '', '', 0),
(1082, 'UV0LOFMFS3', 'personnelImg/default_img.jpg', '685bb7dd0570e.jpg', 'LOGRONIO', 'JOESIFIL', 'FAJARDO', '', 2, 3, '06/25/2025', '04:48 PM', 0, 'off', 'PM OUT', '192.168.1.18', '', '', 0),
(1083, 'ZJPTEJP5UQ', 'personnelImg/default_img.jpg', '685c964f7b295.jpg', 'AMANTE', 'LEONIZA', 'FLORES', '', 11, 3, '06/26/2025', '08:37 AM', 0, 'on', 'AM IN', '127.0.0.1', '', '', 0),
(1084, '2M5F54VPSN', 'personnelImg/default_img.jpg', '685c966072903.jpg', 'AKOL', 'MARLOU', 'ARTICA', '', 14, 3, '06/26/2025', '08:37 AM', 0, 'on', 'AM IN', '127.0.0.1', '', '', 0),
(1085, 'QQZIGTSWBI', 'personnelImg/default_img.jpg', '685c990374a30.jpg', 'NACION', 'ELBRED', 'S.', '', 17, 3, '06/26/2025', '08:49 AM', 0, 'on', 'AM IN', '127.0.0.1', '', '', 0),
(1086, 'BLJQQ3XW3K', 'personnelImg/default_img.jpg', '685c991073f17.jpg', 'NORVIE', 'JOSECO', 'A.', '', 11, 3, '06/26/2025', '08:49 AM', 0, 'on', 'AM IN', '127.0.0.1', '', '', 0),
(1087, 'H05GELOMZN', 'personnelImg/default_img.jpg', '685c99e0754b5.jpg', 'DEQUINA', 'REYNOLD', 'B.', '', 17, 3, '06/26/2025', '08:52 AM', 0, 'on', 'AM IN', '127.0.0.1', '', '', 0),
(1088, 'WDB3J415ZP', 'personnelImg/default_img.jpg', '685ca54ba1431.jpg', 'AKOL', 'SHARRA MAE', 'ARTICA', '', 10, 3, '06/26/2025', '09:41 AM', 0, 'on', 'AM IN', '192.168.1.3', '', '', 0),
(1089, 'ZJPTEJP5UQ', 'personnelImg/default_img.jpg', '685ca56998b00.jpg', 'AMANTE', 'LEONIZA', 'FLORES', '', 11, 3, '06/26/2025', '09:42 AM', 34920, 'on', 'AM OUT', '192.168.1.3', '', '', 0),
(1090, 'WDB3J415ZP', 'personnelImg/default_img.jpg', '685cbd2f9b0d7.jpg', 'AKOL', 'SHARRA MAE', 'ARTICA', '', 10, 3, '06/26/2025', '11:23 AM', 41006, 'on', 'AM OUT', '192.168.1.3', '', '', 0);
INSERT INTO `personnel_logs` (`log_id`, `RFTag_id`, `img`, `captured_img`, `lname`, `fname`, `mname`, `suffix`, `do_id`, `shift_id`, `logDate`, `logTime`, `logTime_sec`, `late_status`, `logFlow`, `client_ip`, `remarks`, `travel_leave_code`, `ref_log_id`) VALUES
(1091, 'FU6VB3OYK4', 'personnelImg/default_img.jpg', '685cbd8697557.jpg', 'MANGOGTONG', 'WILMAR', 'ESTRELLA', '', 14, 3, '06/26/2025', '11:24 AM', 0, 'on', 'AM IN', '192.168.1.3', '', '', 0),
(1092, 'N2CS52WG6G', 'personnelImg/default_img.jpg', '685cbfe29d8f4.jpg', 'GIGANAN', 'GRACE JOY', 'ESTRAO', '', 2, 3, '06/26/2025', '11:34 AM', 0, 'on', 'AM IN', '192.168.1.3', '', '', 0),
(1093, 'N2CS52WG6G', 'personnelImg/default_img.jpg', '685cbfe19b5ef.jpg', 'GIGANAN', 'GRACE JOY', 'ESTRAO', '', 2, 3, '06/26/2025', '11:34 AM', 0, 'off', 'PM IN', '192.168.1.3', '', '', 0),
(1094, 'D4ZWVFKO1E', 'personnelImg/default_img.jpg', '685cc0559956d.jpg', 'LLAMADO', 'MARISSA', 'MAQUILING', '', 2, 3, '06/26/2025', '11:36 AM', 0, 'on', 'AM IN', '192.168.1.3', '', '', 0),
(1095, 'D4ZWVFKO1E', 'personnelImg/default_img.jpg', '685cc0549a423.jpg', 'LLAMADO', 'MARISSA', 'MAQUILING', '', 2, 3, '06/26/2025', '11:36 AM', 0, 'off', 'PM IN', '192.168.1.3', '', '', 0),
(1096, 'N2CS52WG6G', 'personnelImg/default_img.jpg', '685cc112998ca.jpg', 'GIGANAN', 'GRACE JOY', 'ESTRAO', '', 2, 3, '06/26/2025', '11:40 AM', 42000, 'on', 'AM OUT', '192.168.1.3', '', '', 0),
(1097, '', 'personnelImg/default_img.jpg', '685cc1119b9e5.jpg', 'GIGANAN', 'GRACE JOY', 'ESTRAO', '', 2, 3, '06/26/2025', '11:40 AM', 0, 'on', 'PM OUT', '192.168.1.3', '', '', 0),
(1098, 'UV0LOFMFS3', 'personnelImg/default_img.jpg', '685cc16999c6d.jpg', 'LOGRONIO', 'JOESIFIL', 'FAJARDO', '', 2, 3, '06/26/2025', '11:41 AM', 0, 'on', 'AM IN', '192.168.1.3', '', '', 0),
(1099, 'UV0LOFMFS3', 'personnelImg/default_img.jpg', '685cc1689b84c.jpg', 'LOGRONIO', 'JOESIFIL', 'FAJARDO', '', 2, 3, '06/26/2025', '11:41 AM', 0, 'off', 'PM IN', '192.168.1.3', '', '', 0),
(1100, 'IO1IY5L4VB', 'personnelImg/default_img.jpg', '685cc26c9984a.jpg', 'CORTADO', 'ROGELIO', 'ALFANTA', '', 2, 3, '06/26/2025', '11:45 AM', 0, 'on', 'AM IN', '192.168.1.3', '', '', 0),
(1101, 'IO1IY5L4VB', 'personnelImg/default_img.jpg', '685cc26b9a9ba.jpg', 'CORTADO', 'ROGELIO', 'ALFANTA', '', 2, 3, '06/26/2025', '11:45 AM', 0, 'off', 'PM IN', '192.168.1.3', '', '', 0),
(1102, 'Q0QB3ZVYNK', 'personnelImg/default_img.jpg', '685dee8565196.jpg', 'VILLARETE', 'ANGELA', 'TELONIO', '', 7, 3, '06/27/2025', '09:06 AM', 0, 'on', 'AM IN', '192.168.1.5', '', '', 0),
(1103, 'OSYDS2VNEW', 'personnelImg/default_img.jpg', '685e11805aa4a.jpg', 'ABALLE', 'BETHEL', 'PERFUMA', '', 10, 3, '06/27/2025', '11:35 AM', 0, 'on', 'AM IN', '192.168.1.5', '', '', 0),
(1104, 'OSYDS2VNEW', 'personnelImg/default_img.jpg', '685e117f5be77.jpg', 'ABALLE', 'BETHEL', 'PERFUMA', '', 10, 3, '06/27/2025', '11:35 AM', 0, 'off', 'PM IN', '192.168.1.5', '', '', 0),
(1105, 'YFUCRYEDIV', 'personnelImg/nrfc1ohg-jet.jpg', '685e151e5b8fa.jpg', 'BARROCA', 'JETTER', 'SENIO', '', 14, 3, '06/27/2025', '11:50 AM', 0, 'on', 'AM IN', '192.168.1.5', '', '', 0),
(1106, 'YFUCRYEDIV', 'personnelImg/nrfc1ohg-jet.jpg', '685e151d5a3d9.jpg', 'BARROCA', 'JETTER', 'SENIO', '', 14, 3, '06/27/2025', '11:50 AM', 0, 'off', 'PM IN', '192.168.1.5', '', '', 0),
(1107, 'T1X1EOAO50', 'personnelImg/default_img.jpg', '685e194a566f6.jpg', 'HERRADURA', 'GINALYN ', 'OBENZA', '', 2, 3, '06/27/2025', '12:08 PM', 0, 'off', 'PM IN', '192.168.1.5', '', '', 0),
(1108, 'D4ZWVFKO1E', 'personnelImg/default_img.jpg', '685e1a495e4b0.jpg', 'LLAMADO', 'MARISSA', 'MAQUILING', '', 2, 3, '06/27/2025', '12:12 PM', 0, 'off', 'PM IN', '192.168.1.5', '', '', 0),
(1109, '4RE6HDPT3R', 'personnelImg/default_img.jpg', '685e1ad85796a.jpg', 'JIMENEZ', 'LEO', 'TOMILBA', '', 14, 3, '06/27/2025', '12:15 PM', 0, 'off', 'PM IN', '192.168.1.5', '', '', 0),
(1110, 'SJLE6GGLD6', 'personnelImg/nrf04z55-che.jpg', '685e1b025d676.jpg', 'JUAREZ', 'CHERRY LYNN', 'MONTAÑO', '', 2, 3, '06/27/2025', '12:16 PM', 0, 'off', 'PM IN', '192.168.1.5', '', '', 0),
(1111, 'D4ZWVFKO1E', 'personnelImg/default_img.jpg', '685e24cc593ca.jpg', 'LLAMADO', 'MARISSA', 'MAQUILING', '', 2, 3, '06/27/2025', '12:57 PM', 0, 'on', 'PM OUT', '192.168.1.5', '', '', 0),
(1112, 'VUM5AWYEUH', 'personnelImg/default_img.jpg', '685e32fb58764.jpg', 'SUMUGAT', 'JENELYN', 'TELONIO', '', 3, 3, '06/27/2025', '01:58 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1113, 'OINBKY4BGA', 'personnelImg/default_img.jpg', '685e339558a3f.jpg', 'DECATORIA', 'JOEBERT', 'SARIL', '', 3, 3, '06/27/2025', '02:00 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1114, 'BSLAWXCQ2N', 'personnelImg/default_img.jpg', '685e33ea593e4.jpg', 'BAYDO', 'JULITO', 'CAYAS', '', 3, 3, '06/27/2025', '02:02 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1115, 'OINBKY4BGA', 'personnelImg/default_img.jpg', '685e35195819c.jpg', 'DECATORIA', 'JOEBERT', 'SARIL', '', 3, 3, '06/27/2025', '02:07 PM', 0, 'on', 'PM OUT', '192.168.1.5', '', '', 0),
(1116, 'BSLAWXCQ2N', 'personnelImg/default_img.jpg', '685e35885ecaa.jpg', 'BAYDO', 'JULITO', 'CAYAS', '', 3, 3, '06/27/2025', '02:09 PM', 0, 'on', 'PM OUT', '192.168.1.5', '', '', 0),
(1117, 'K0GBVLRIOM', 'personnelImg/default_img.jpg', '685e36245a23b.jpg', 'JORDAN', 'MARK ANTHONY', 'TOMADO', '', 3, 3, '06/27/2025', '02:11 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1118, 'KDV12JKLNR', 'personnelImg/default_img.jpg', '685e37fa583f9.jpg', 'TRINIO-SANTES', 'VANESSA', 'LIRAZAN', '', 3, 3, '06/27/2025', '02:19 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1119, 'HOA5M4V2KU', 'personnelImg/default_img.jpg', '685e38d35727e.jpg', 'TEMBREVILLA', 'THOMY', 'G.', '', 3, 3, '06/27/2025', '02:23 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1120, '2XYVUZG44K', 'personnelImg/default_img.jpg', '685e3b06582c5.jpg', 'RELIQUIAS', 'JOERIBEL', 'TORIANO', '', 9, 3, '06/27/2025', '02:32 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1121, 'C4XW3NQ3CE', 'personnelImg/default_img.jpg', '685e3c0258fa7.jpg', 'ACADEMIA', 'RIZALYN', 'CAYANAN', '', 9, 3, '06/27/2025', '02:36 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1122, 'N0PELGBXHV', 'personnelImg/default_img.jpg', '685e3d4757662.jpg', 'GUSTILO', 'LEILANI', 'DECENA', '', 9, 3, '06/27/2025', '02:42 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1123, 'JT6NRMM2MG', 'personnelImg/default_img.jpg', '685e3e2458d1e.jpg', 'LABRADOR', 'REY', 'TRIBUCIO', '', 10, 3, '06/27/2025', '02:45 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1124, 'KZFCP2S0HT', 'personnelImg/default_img.jpg', '685e3eca5972e.jpg', 'GUINTOS', 'JOSEPHINE', 'INDINO', '', 10, 3, '06/27/2025', '02:48 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1125, 'N3CFWPHFQX', 'personnelImg/default_img.jpg', '685e3f2259625.jpg', 'SANTES', 'CAROL ANN', 'TILOS', '', 5, 3, '06/27/2025', '02:50 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1126, '2SOQJG3P6F', 'personnelImg/default_img.jpg', '685e3f2b57b55.jpg', 'SANTES', 'CINDY', 'REBOLDAL', '', 4, 3, '06/27/2025', '02:50 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1127, '2SOQJG3P6F', 'personnelImg/default_img.jpg', '685e42f3581f1.jpg', 'SANTES', 'CINDY', 'REBOLDAL', '', 4, 3, '06/27/2025', '03:06 PM', 0, 'on', 'PM OUT', '192.168.1.5', '', '', 0),
(1128, 'K0GBVLRIOM', 'personnelImg/default_img.jpg', '685e434a5d2b8.jpg', 'JORDAN', 'MARK ANTHONY', 'TOMADO', '', 3, 3, '06/27/2025', '03:07 PM', 0, 'on', 'PM OUT', '192.168.1.5', '', '', 0),
(1129, 'ZJPTEJP5UQ', 'personnelImg/default_img.jpg', '685e44ba58d4b.jpg', 'AMANTE', 'LEONIZA', 'FLORES', '', 11, 3, '06/27/2025', '03:14 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1130, 'N3CFWPHFQX', 'personnelImg/default_img.jpg', '685e46075a487.jpg', 'SANTES', 'CAROL ANN', 'TILOS', '', 5, 3, '06/27/2025', '03:19 PM', 0, 'on', 'PM OUT', '192.168.1.5', '', '', 0),
(1131, '1Z6LX20PVU', 'personnelImg/default_img.jpg', '685e47045acdc.jpg', 'RELIQUIAS', 'JOHN MARK', 'BALUNGCAS', '', 23, 3, '06/27/2025', '03:23 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1132, 'LUPQ4RIQUJ', 'personnelImg/default_img.jpg', '685e47c557ca2.jpg', 'TILOS', 'RIZALIE', 'CELIZ', '', 12, 3, '06/27/2025', '03:27 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1133, 'ZXDNHZFPMW', 'personnelImg/default_img.jpg', '685e4a8159300.jpg', 'PUBLICO', 'NOEMI', 'TOMADO', '', 12, 3, '06/27/2025', '03:38 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1134, 'VWUQQEPZ5P', 'personnelImg/default_img.jpg', '685e4b2f5b6d0.jpg', 'DIONALDO', 'ROEM', 'S.', '', 2, 3, '06/27/2025', '03:41 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1135, 'BCUT6421HG', 'personnelImg/default_img.jpg', '685e4bcf597f8.jpg', 'CANDULIZAS', 'MOLAVE', 'TEMBREVILLA', '', 2, 3, '06/27/2025', '03:44 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1136, 'KHA2LUXNL1', 'personnelImg/default_img.jpg', '685e4c335661c.jpg', 'BA-AL', 'VON MARVIN', 'M.', '', 2, 3, '06/27/2025', '03:45 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1137, 'QQZIGTSWBI', 'personnelImg/default_img.jpg', '685e4d6e5a323.jpg', 'NACION', 'ELBRED', 'S.', '', 17, 3, '06/27/2025', '03:51 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1138, 'EKSUEACJM5', 'personnelImg/default_img.jpg', '685e4e1358d24.jpg', 'ACIBIDO', 'CHE GENEROSO', 'PARO', '', 14, 3, '06/27/2025', '03:53 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1139, '0LKJLM2C22', 'personnelImg/default_img.jpg', '685e4ec85a130.jpg', 'MANGILIMUTAN', 'LORRAINE MAE', 'GESTOSO', '', 2, 3, '06/27/2025', '03:56 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1140, 'MF4R0LP0PT', 'personnelImg/default_img.jpg', '685e4ecc5bd02.jpg', 'TOLEDO', 'RAYMUND ANTHONY', 'INOLINO', '', 4, 3, '06/27/2025', '03:56 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1141, 'I0FY3RFXM5', 'personnelImg/default_img.jpg', '685e4fbf580e9.jpg', 'TILOS', 'OSCAR', 'VILLARETE', '', 4, 3, '06/27/2025', '04:01 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1142, 'RK02GLHBHR', 'personnelImg/default_img.jpg', '685e50005987a.jpg', 'PINONGGAN', 'JOSEPH', 'ALBERIO', '', 2, 3, '06/27/2025', '04:02 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1143, 'UV0LOFMFS3', 'personnelImg/default_img.jpg', '685e502757017.jpg', 'LOGRONIO', 'JOESIFIL', 'FAJARDO', '', 2, 3, '06/27/2025', '04:02 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1144, '2KLYB0WHDY', 'personnelImg/default_img.jpg', '685e50d65b125.jpg', 'GESTOSO', 'CARLITO', 'BAVIERA', '', 10, 3, '06/27/2025', '04:05 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1145, 'CEPHX3JOM0', 'personnelImg/default_img.jpg', '685e5139580d2.jpg', 'TIBAYDE', 'IRENE', 'GIGANAN', '', 10, 3, '06/27/2025', '04:07 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1146, 'KVKMFQDZDV', 'personnelImg/default_img.jpg', '685e51b759e4a.jpg', 'GA-AN', 'LENLY', 'NOSAL', '', 10, 3, '06/27/2025', '04:09 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1147, 'E6LFKFXAD0', 'personnelImg/default_img.jpg', '685e51fe59d1d.jpg', 'NUÑESCO', 'AIZA', 'ANDO', '', 5, 3, '06/27/2025', '04:10 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1148, 'IO1IY5L4VB', 'personnelImg/default_img.jpg', '685e52625cbb7.jpg', 'CORTADO', 'ROGELIO', 'ALFANTA', '', 2, 3, '06/27/2025', '04:12 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1149, 'C4XW3NQ3CE', 'personnelImg/default_img.jpg', '685e52bb59875.jpg', 'ACADEMIA', 'RIZALYN', 'CAYANAN', '', 9, 3, '06/27/2025', '04:13 PM', 0, 'on', 'PM OUT', '192.168.1.5', '', '', 0),
(1150, 'WDB3J415ZP', 'personnelImg/default_img.jpg', '685e52e5589fc.jpg', 'AKOL', 'SHARRA MAE', 'ARTICA', '', 10, 3, '06/27/2025', '04:14 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1151, 'DO2X14YKJR', 'personnelImg/default_img.jpg', '685e5312579e5.jpg', 'VILLARUBIA', 'ALTHEA', 'PIA', '', 5, 3, '06/27/2025', '04:15 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1152, '0RVK3VBCZ1', 'personnelImg/default_img.jpg', '685e533d57624.jpg', 'MANOS', 'ANNABELLE', 'PERIGUA', '', 5, 3, '06/27/2025', '04:15 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1153, '2XYVUZG44K', 'personnelImg/default_img.jpg', '685e53c459392.jpg', 'RELIQUIAS', 'JOERIBEL', 'TORIANO', '', 9, 3, '06/27/2025', '04:18 PM', 0, 'on', 'PM OUT', '192.168.1.5', '', '', 0),
(1154, 'L33XK2MQYL', 'personnelImg/default_img.jpg', '685e53c957e8d.jpg', 'RELIQUIAS', 'JOHNNY RAY', 'L.', '', 2, 3, '06/27/2025', '04:18 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1155, 'ES1MY5CB5N', 'personnelImg/default_img.jpg', '685e54065aa72.jpg', 'BUDACA', 'REVINIA', 'AMACIO', '', 10, 3, '06/27/2025', '04:19 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1156, 'PLCEEHAUGT', 'personnelImg/default_img.jpg', '685e543c562e6.jpg', 'MARQUEZ', 'MAE ANN', 'GIGANAN', '', 15, 3, '06/27/2025', '04:20 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1157, 'NZ0IKZI1IR', 'personnelImg/default_img.jpg', '685e548759b8a.jpg', 'MANGOGTONG', 'MA. MARILOU ', 'ESTRELLA', '', 10, 3, '06/27/2025', '04:21 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1158, 'POT5DLCLXZ', 'personnelImg/default_img.jpg', '685e550b58750.jpg', 'GUINTOS', 'TINA MARIE', 'LUGA', '', 16, 3, '06/27/2025', '04:23 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1159, 'N4DZR4BBY0', 'personnelImg/default_img.jpg', '685e5589570a7.jpg', 'RELIQUIAS', 'MARIA FE', 'GUINTOS', '', 8, 3, '06/27/2025', '04:25 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1160, 'GSQZ2OCVHQ', 'personnelImg/default_img.jpg', '685e55a25aed0.jpg', 'RELIQUIAS', 'GERALD', 'TORIANO', '', 14, 3, '06/27/2025', '04:26 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1161, 'O6JWSQ5BTR', 'personnelImg/default_img.jpg', '685e55f956571.jpg', 'DELA CONCEPTION', 'IMELDA', 'ESPENORIO', '', 14, 3, '06/27/2025', '04:27 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1162, 'M1RF4GW0TT', 'personnelImg/default_img.jpg', '685e56125656d.jpg', 'GUINTOS', 'MA. ELENA', 'N.', '', 14, 3, '06/27/2025', '04:28 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1163, 'F255HSX3FM', 'personnelImg/default_img.jpg', '685e56535981e.jpg', 'GUINTOS', 'ERICA', 'MANANGAN', '', 14, 3, '06/27/2025', '04:29 PM', 0, 'on', 'PM IN', '192.168.1.5', '', '', 0),
(1164, '0RVK3VBCZ1', 'personnelImg/default_img.jpg', '685e589957293.jpg', 'MANOS', 'ANNABELLE', 'PERIGUA', '', 5, 3, '06/27/2025', '04:38 PM', 0, 'off', 'PM OUT', '192.168.1.5', '', '', 0),
(1165, 'GSQZ2OCVHQ', 'personnelImg/default_img.jpg', '685e58b35c3de.jpg', 'RELIQUIAS', 'GERALD', 'TORIANO', '', 14, 3, '06/27/2025', '04:39 PM', 0, 'off', 'PM OUT', '192.168.1.5', '', '', 0),
(1166, 'ZGMTZ2Y3BT', 'personnelImg/default_img.jpg', '6861db44b3879.jpg', 'OCTAVIO', 'PETER JOHN ', 'LAZALITA', '', 24, 3, '06/30/2025', '08:33 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1167, 'N0AXWJPKNQ', 'personnelImg/default_img.jpg', '6861db85af11d.jpg', 'SORONGON', 'NITA', 'A.', '', 7, 3, '06/30/2025', '08:34 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1168, 'WILZVWBBRI', 'personnelImg/default_img.jpg', '6861e815ace13.jpg', 'OCCEÑA', 'GEM', 'RELAMPAGOS', '', 3, 3, '06/30/2025', '09:27 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1169, 'MS5LRO1IJE', 'personnelImg/default_img.jpg', '6861e936a966e.jpg', 'VILLANUEVA', 'ROSALIE', 'ELARDO', '', 12, 3, '06/30/2025', '09:32 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1170, 'Q0QB3ZVYNK', 'personnelImg/default_img.jpg', '6861e96daa25a.jpg', 'VILLARETE', 'ANGELA', 'TELONIO', '', 7, 3, '06/30/2025', '09:33 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1171, 'E0VZSU0W6G', 'personnelImg/default_img.jpg', '6861e99faed98.jpg', 'APLAON', 'MA. LEE', 'MAESTRECAMPO', '', 23, 3, '06/30/2025', '09:34 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1172, 'M1Y5ETAYD6', 'personnelImg/default_img.jpg', '6861e9eaaba10.jpg', 'NAVA', 'LUZ SALOME', 'VILLA', '', 14, 3, '06/30/2025', '09:35 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1173, '111LXWXFLY', 'personnelImg/default_img.jpg', '6861ea8caba7f.jpg', 'NICOR', 'MICHAEL', 'RAMOS', '', 7, 3, '06/30/2025', '09:38 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1174, '6ON2RWCQEM', 'personnelImg/default_img.jpg', '6861eb03acc5a.jpg', 'GAREZA', 'MELANIE', 'CELIS', '', 12, 3, '06/30/2025', '09:40 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1175, '6D0YWDKLPP', 'personnelImg/default_img.jpg', '6861ec39ac469.jpg', 'AGUHAYON', 'RICARDO', 'UBAMOS', '', 14, 3, '06/30/2025', '09:45 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1176, 'REPYPK1N0K', 'personnelImg/default_img.jpg', '6861ecbdad641.jpg', 'AMBAGAN', 'EDSEL', 'MILLENDEZ', '', 16, 3, '06/30/2025', '09:47 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1177, 'N3CFWPHFQX', 'personnelImg/default_img.jpg', '6861ece3a9267.jpg', 'SANTES', 'CAROL ANN', 'TILOS', '', 5, 3, '06/30/2025', '09:48 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1178, '6KR3ZM4BU3', 'personnelImg/default_img.jpg', '6861ed59a836a.jpg', 'TELONIO', 'JAMES ANDREW', 'GUINTOS', '', 15, 3, '06/30/2025', '09:50 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1179, 'YCSOHXN5TA', 'personnelImg/default_img.jpg', '6861ed99ac89d.jpg', 'GALLARDA', 'ELNOR', 'PACLAONA', '', 12, 3, '06/30/2025', '09:51 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1180, 'MHDZI160KN', 'personnelImg/default_img.jpg', '6861ee38a9d18.jpg', 'TEMBREVILLA', 'CAROLINE', 'CANILLO', '', 12, 3, '06/30/2025', '09:53 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1181, 'SARR3EM6ZV', 'personnelImg/default_img.jpg', '6861ef1eb2e24.jpg', 'PACURIB', 'JULITO', 'PLAÑA', 'JR. ', 3, 3, '06/30/2025', '09:57 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1182, '0M1YGUSYJR', 'personnelImg/default_img.jpg', '6861f048ab6c1.jpg', 'BALOYO', 'HANSEL', 'M.', '', 11, 3, '06/30/2025', '10:02 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1183, '1YJTKOZ61A', 'personnelImg/default_img.jpg', '6861f06dac909.jpg', 'DURAN', 'MICHELLE', 'M.', '', 11, 3, '06/30/2025', '10:03 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1184, '5Z5WKCHQGF', 'personnelImg/default_img.jpg', '6861f0a3abf63.jpg', 'DOLOR', 'ROLY', 'D.', '', 11, 3, '06/30/2025', '10:04 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1185, 'EHEYDZ1UYN', 'personnelImg/default_img.jpg', '6861f10ca9dbd.jpg', 'ALATON', 'GINA', 'NABOR', '', 6, 3, '06/30/2025', '10:06 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1186, 'P-ZGT-382010-110', 'personnelImg/default_img.jpg', '6861f19aaa70a.jpg', 'TEMBREVILLA', 'ZOSIMO', 'GAVILAGA', '', 3, 3, '06/30/2025', '10:08 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1187, 'Q0BSK1IGMH', 'personnelImg/default_img.jpg', '6861f1ffaaa2e.jpg', 'BARIQUIT', 'PRACEDES', 'CABONILAS', '', 7, 3, '06/30/2025', '10:10 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1188, 'R0ZJXTVRR4', 'personnelImg/default_img.jpg', '6861f2a9abc75.jpg', 'AVELINO', 'JULIEBERT', 'AZUCENA', '', 2, 3, '06/30/2025', '10:12 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1189, 'XFSKZYRVSI', 'personnelImg/default_img.jpg', '6861f32fa87ac.jpg', 'LASTRILLA', 'GUIDRALYN', 'P.', '', 7, 3, '06/30/2025', '10:15 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1190, 'XD5JB3HCC0', 'personnelImg/default_img.jpg', '6861f5e6ad4b2.jpg', 'LAREÑO', 'LIWAYA', 'MAHINAY', '', 11, 3, '06/30/2025', '10:26 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1191, 'PMOLWNYZZA', 'personnelImg/default_img.jpg', '6861f628a849c.jpg', 'SUSANA', 'FREDERICK DAVY', 'PINGCALE', '', 12, 3, '06/30/2025', '10:27 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1192, 'WSJIHMXEN6', 'personnelImg/default_img.jpg', '6861f688a94bc.jpg', 'TILOS', 'MARCELINA', 'CELIS', '', 12, 3, '06/30/2025', '10:29 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1193, 'QESM444ZK0', 'personnelImg/default_img.jpg', '6861f6f7ab179.jpg', 'TEMBREVILLA', 'RONA', 'ESCOSAR', '', 12, 3, '06/30/2025', '10:31 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1194, 'DGPF5FDK53', 'personnelImg/default_img.jpg', '6861f75eaf60d.jpg', 'ARANETA', 'JOSELITA', 'GENTELIZO', '', 6, 3, '06/30/2025', '10:33 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1195, 'GJCC6XY3BR', 'personnelImg/default_img.jpg', '6861f781aa551.jpg', 'ANTIQUEÑO', 'ANA MARIE', 'SANOY', '', 12, 3, '06/30/2025', '10:33 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1196, 'FU6VB3OYK4', 'personnelImg/default_img.jpg', '6861f79bacc3d.jpg', 'MANGOGTONG', 'WILMAR', 'ESTRELLA', '', 14, 3, '06/30/2025', '10:34 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1197, 'ZV1EXSSS30', 'personnelImg/default_img.jpg', '6861f7c7a9873.jpg', 'BONGCAWEL', 'GENELYN ', 'HISONA', '', 12, 3, '06/30/2025', '10:34 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1198, 'Z2TDUSRUV6', 'personnelImg/default_img.jpg', '6861f7e1ab569.jpg', 'BIACA', 'HERBERT', 'BORNALES', '', 3, 3, '06/30/2025', '10:35 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1199, 'UUR4C35PLC', 'personnelImg/default_img.jpg', '6861f85da8711.jpg', 'ALIMANE', 'JINKY', 'GALPO', '', 7, 3, '06/30/2025', '10:37 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1200, 'JETERWW23U', 'personnelImg/nrf5ihz6-mae-flor.jpg', '6861f8c9acf3f.jpg', 'BARRIOS', 'MAE FLOR', 'ALMAIZ', '', 8, 3, '06/30/2025', '10:39 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1201, 'CCEWWUW3P2', 'personnelImg/default_img.jpg', '6861f938abecd.jpg', 'TUBILLEJA', 'THEODORE', 'SIGUEZA', 'JR. ', 6, 3, '06/30/2025', '10:40 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1202, 'ZJPTEJP5UQ', 'personnelImg/default_img.jpg', '6861f98dac349.jpg', 'AMANTE', 'LEONIZA', 'FLORES', '', 11, 3, '06/30/2025', '10:42 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1203, 'BSY3X4CY3E', 'personnelImg/default_img.jpg', '6861f9c2abe9a.jpg', 'SANTES', 'JOCELYN', 'CORONEL', '', 24, 3, '06/30/2025', '10:43 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1204, 'F3DUWOJ4SZ', 'personnelImg/default_img.jpg', '6861fa5fabdf7.jpg', 'SIASON', 'CHEZAH ERL', 'GAUAL', '', 16, 3, '06/30/2025', '10:45 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1205, 'GZ5LO0QDRC', 'personnelImg/default_img.jpg', '6861fab9ac8ca.jpg', 'GARCIA', 'ADELA', 'ANTOLIN', '', 12, 3, '06/30/2025', '10:47 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1206, 'LVX5C46TAS', 'personnelImg/default_img.jpg', '6861fae3a9cc8.jpg', 'SILVESTRE', 'CYNTHIA', 'LAMBOT', '', 12, 3, '06/30/2025', '10:48 AM', 0, 'on', 'AM IN', '192.168.1.7', '', '', 0),
(1207, 'FOSBE6FBBP', 'personnelImg/default_img.jpg', '6862107ae0eab.jpg', 'MAHINAY', 'FRANCISCO', 'MANINANTAN', 'SR. ', 3, 3, '06/30/2025', '12:20 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1208, 'UBFPHM020G', 'personnelImg/default_img.jpg', '686210bbe1bd2.jpg', 'BONILLA', 'ANNI VER', 'PAMLIEGA', '', 6, 3, '06/30/2025', '12:21 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1209, 'H05GELOMZN', 'personnelImg/default_img.jpg', '686210e5e5b11.jpg', 'DEQUINA', 'REYNOLD', 'B.', '', 17, 3, '06/30/2025', '12:21 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1210, 'T0SO3GEQZX', 'personnelImg/default_img.jpg', '68621103e3809.jpg', 'TUPAS', 'ANTHONY', 'L.', '', 11, 3, '06/30/2025', '12:22 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1211, 'PQMSBLBNJ6', 'personnelImg/default_img.jpg', '6862113ee33e3.jpg', 'VIDAURRAZAGA', 'NOAH', 'VASQUEZ', '', 2, 3, '06/30/2025', '12:23 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1212, 'LTQHD2BPYJ', 'personnelImg/default_img.jpg', '68621173e0d81.jpg', 'GALON', 'RANDOLF', 'BLANCO', '', 4, 3, '06/30/2025', '12:24 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1213, 'OIMV5KOLF2', 'personnelImg/default_img.jpg', '6862119ae2a5a.jpg', 'PEROSIA', 'GLORY MAE', 'TUNDA-AN', '', 12, 3, '06/30/2025', '12:24 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1214, 'BLJQQ3XW3K', 'personnelImg/default_img.jpg', '686211e0e357f.jpg', 'NORVIE', 'JOSECO', 'A.', '', 11, 3, '06/30/2025', '12:26 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1215, 'HR1TD1EOO6', 'personnelImg/default_img.jpg', '68621216e2476.jpg', 'NATALIO', 'JOEFREY', 'TEMBREVILLA', '', 9, 3, '06/30/2025', '12:27 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1216, '6DZNSFDRJG', 'personnelImg/default_img.jpg', '686212b2e34fb.jpg', 'MANOS', 'TEOFILO', 'ENCARGUEZ', 'JR. ', 24, 3, '06/30/2025', '12:29 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1217, 'FKX31XYW34', 'personnelImg/default_img.jpg', '686212ece1b71.jpg', 'DELA FUENTE', 'ALVIN', 'ORMEO', '', 11, 3, '06/30/2025', '12:30 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1218, 'DCORCD1ICP', 'personnelImg/default_img.jpg', '6862131be2c5e.jpg', 'TUMA-OB', 'LESLIE AIKEE DYAN', 'GIGANAN', '', 24, 3, '06/30/2025', '12:31 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1219, 'V4PAJMJ5YO', 'personnelImg/default_img.jpg', '6862134de21c3.jpg', 'TUPAS', 'OFELIA', 'TEMBREVILLA', '', 6, 3, '06/30/2025', '12:32 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1220, 'RVR5BBU5ZV', 'personnelImg/default_img.jpg', '6862136fe0c89.jpg', 'CANA', 'EVA MAE', 'G.', '', 11, 3, '06/30/2025', '12:32 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1221, 'MMB6JKH1T3', 'personnelImg/default_img.jpg', '6862138fe6059.jpg', 'CAMBARIJAN', 'JIMMY', 'ZETA', '', 25, 3, '06/30/2025', '12:33 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1222, '2CKQGJE4K6', 'personnelImg/default_img.jpg', '686213a7e6f1d.jpg', 'DELLOSO', 'CHRISTOPHER', 'M.', '', 14, 3, '06/30/2025', '12:33 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1223, '56EZ3T4YP2', 'personnelImg/default_img.jpg', '686213cbdf3e6.jpg', 'CALUMBA', 'ANALYN', 'MANILINGAN', '', 25, 3, '06/30/2025', '12:34 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1224, 'T4BXLLYWTG', 'personnelImg/default_img.jpg', '68621428e219e.jpg', 'DERIT', 'JOSE ALAN ', 'DURO', '', 17, 3, '06/30/2025', '12:35 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1225, 'GOYLQLNANM', 'personnelImg/default_img.jpg', '6862144fe19d5.jpg', 'DELOTINA', 'DANILO', 'NAPIERE', '', 17, 3, '06/30/2025', '12:36 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1226, 'ITZ6XK1TGX', 'personnelImg/default_img.jpg', '68621469e03ec.jpg', 'LUCENARA', 'ROBIJID', 'Q', '', 15, 3, '06/30/2025', '12:36 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1227, 'HDMCPVKYB1', 'personnelImg/default_img.jpg', '6862149ae08ab.jpg', 'ROXAS', 'MARY ANN', 'VILLAFUERTE', '', 12, 3, '06/30/2025', '12:37 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1228, 'B42WBNMNSK', 'personnelImg/default_img.jpg', '686214c9e1f73.jpg', 'LIMSIACO', 'ARIANE', 'CONSTANTINO', '', 6, 3, '06/30/2025', '12:38 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1229, 'DV6HSR4T31', 'personnelImg/default_img.jpg', '68621507e0e7a.jpg', 'SABOBO', 'ALFREDO', 'G.', '', 15, 3, '06/30/2025', '12:39 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1230, 'ZHXY3CO3KQ', 'personnelImg/default_img.jpg', '68621580e1e74.jpg', 'DELA FUENTE', 'EBER', 'ORMEO', '', 12, 3, '06/30/2025', '12:41 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1231, 'W2DGEUTP62', 'personnelImg/default_img.jpg', '686215cfe3232.jpg', 'LOREDO', 'MARIA ELENA', 'DELLOSO', '', 14, 3, '06/30/2025', '12:42 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1232, 'PWU0GP4SHN', 'personnelImg/default_img.jpg', '6862161bdfc73.jpg', 'SANTES', 'AZELA', 'FLORES', '', 7, 3, '06/30/2025', '12:44 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1233, 'CMDUXPZ44I', 'personnelImg/default_img.jpg', '6862164ae1a2c.jpg', 'GONZAL', 'SANDRA', 'CASTILLO', '', 8, 3, '06/30/2025', '12:44 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1234, 'EAUNV5WVM0', 'personnelImg/default_img.jpg', '68621664e21c8.jpg', 'BARROCA', 'JORGIE', 'PERFUMA', '', 6, 3, '06/30/2025', '12:45 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1235, '2SJBOJTXPC', 'personnelImg/default_img.jpg', '6862168be2482.jpg', 'GELLECANAO', 'MURIELLE', 'LIRAZAN', '', 7, 3, '06/30/2025', '12:46 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1236, '1QETKAFQWO', 'personnelImg/default_img.jpg', '686216aae10ce.jpg', 'DELOTINA', 'JOFEL', 'EVANGELISTA', '', 24, 3, '06/30/2025', '12:46 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1237, 'R52SQSFJB4', 'personnelImg/default_img.jpg', '686216cbdfa49.jpg', 'BONILLA', 'JURY', 'BENLOT', '', 17, 3, '06/30/2025', '12:47 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1238, '2SOQJG3P6F', 'personnelImg/default_img.jpg', '68621b20e1364.jpg', 'SANTES', 'CINDY', 'REBOLDAL', '', 4, 3, '06/30/2025', '01:05 PM', 0, 'off', 'PM IN', '192.168.1.7', '', '', 0),
(1239, 'KP6G24TB40', 'personnelImg/default_img.jpg', '6862254ae00ef.jpg', 'RELADO', 'MEDALIA', 'VALENZUELA', '', 8, 3, '06/30/2025', '01:48 PM', 0, 'on', 'PM IN', '192.168.1.7', '', '', 0),
(1240, 'GP4Q0NOWGA', 'personnelImg/default_img.jpg', '6862259de0307.jpg', 'GERMINAL', 'CRISTUTO', 'M.', '', 17, 3, '06/30/2025', '01:50 PM', 0, 'on', 'PM IN', '192.168.1.7', '', '', 0),
(1241, 'KP6G24TB40', 'personnelImg/default_img.jpg', '68622c1aef3b9.jpg', 'RELADO', 'MEDALIA', 'VALENZUELA', '', 8, 3, '06/30/2025', '02:18 PM', 0, 'on', 'PM OUT', '127.0.0.1', '', '', 0),
(1242, 'HOA5M4V2KU', 'personnelImg/default_img.jpg', '68622d02ee890.jpg', 'TEMBREVILLA', 'THOMY', 'G.', '', 3, 3, '06/30/2025', '02:21 PM', 0, 'on', 'PM IN', '127.0.0.1', '', '', 0),
(1243, 'GP4Q0NOWGA', 'personnelImg/default_img.jpg', '68622de7ed34d.jpg', 'GERMINAL', 'CRISTUTO', 'M.', '', 17, 3, '06/30/2025', '02:25 PM', 0, 'on', 'PM OUT', '127.0.0.1', '', '', 0),
(1244, '2SOQJG3P6F', 'personnelImg/default_img.jpg', '68622e72bc8e6.jpg', 'SANTES', 'CINDY', 'REBOLDAL', '', 4, 3, '06/30/2025', '02:28 PM', 0, 'on', 'PM OUT', '127.0.0.1', '', '', 0),
(1245, 'MF4R0LP0PT', 'personnelImg/default_img.jpg', '68624529aabf2.jpg', 'TOLEDO', 'RAYMUND ANTHONY', 'INOLINO', '', 4, 3, '06/30/2025', '04:04 PM', 0, 'on', 'PM IN', '127.0.0.1', '', '', 0),
(1246, 'R0VM3TX265', 'personnelImg/default_img.jpg', '68633862dd3a2.jpg', 'MONTANO', 'EVALYN', 'C.', '', 24, 3, '07/01/2025', '09:22 AM', 0, 'on', 'AM IN', '192.168.1.13', '', '', 0),
(1247, 'HSDQZ6G6PA', 'personnelImg/default_img.jpg', '68633878d33c7.jpg', 'PEREZ', 'JOSE MARIA', 'LIM', 'JR. ', 12, 3, '07/01/2025', '09:23 AM', 0, 'on', 'AM IN', '192.168.1.13', '', '', 0),
(1248, 'GRN63LXTVC', 'personnelImg/default_img.jpg', '68633d77cef30.jpg', 'GIGANAN', 'AGNES', 'NAVA', '', 15, 3, '07/01/2025', '09:44 AM', 0, 'on', 'AM IN', '192.168.1.13', '', '', 0),
(1249, 'DURSCXCSIM', 'personnelImg/default_img.jpg', '68633da9cff0e.jpg', 'MAYANDIA', 'ANALIE', 'ELARMO', '', 6, 3, '07/01/2025', '09:45 AM', 0, 'on', 'AM IN', '192.168.1.13', '', '', 0),
(1250, '1DSSFVBIFF', 'personnelImg/default_img.jpg', '68633df6d014b.jpg', 'MAESTRECAMPO', 'EVELYN', 'ELARMO', '', 7, 3, '07/01/2025', '09:46 AM', 0, 'on', 'AM IN', '192.168.1.13', '', '', 0),
(1251, 'LS5IJYTERX', 'personnelImg/default_img.jpg', '68633e36cf1b8.jpg', 'GUINTOS', 'WINSTON', 'NAVA', '', 3, 3, '07/01/2025', '09:47 AM', 0, 'on', 'AM IN', '192.168.1.13', '', '', 0),
(1252, 'I03I6VZELI', 'personnelImg/default_img.jpg', '68633e70d30a0.jpg', 'GALAN', 'MARY JANE', 'CELIS', '', 12, 3, '07/01/2025', '09:48 AM', 0, 'on', 'AM IN', '192.168.1.13', '', '', 0),
(1253, 'G6A2OEKXUH', 'personnelImg/default_img.jpg', '68633e94cf2e4.jpg', 'MANGILIMUTAN', 'MA. HEARTY', 'CAÑA', '', 12, 3, '07/01/2025', '09:49 AM', 0, 'on', 'AM IN', '192.168.1.13', '', '', 0),
(1254, '5UVF66ZPY0', 'personnelImg/default_img.jpg', '68633ee2d0ad2.jpg', 'MAQUILING', 'WARREN', 'RAMIREZ', '', 12, 3, '07/01/2025', '09:50 AM', 0, 'on', 'AM IN', '192.168.1.13', '', '', 0),
(1255, 'EEG0NY2B5U', 'personnelImg/default_img.jpg', '68633f20cf5d4.jpg', 'ARBOIZ', 'JOHN', 'VILLARICO', '', 15, 3, '07/01/2025', '09:51 AM', 0, 'on', 'AM IN', '192.168.1.13', '', '', 0),
(1256, '2O1JSGHAYX', 'personnelImg/default_img.jpg', '68633f3cd021f.jpg', 'GAUAL', 'ROLIN', 'BERGONIO', 'SR. ', 12, 3, '07/01/2025', '09:51 AM', 0, 'on', 'AM IN', '192.168.1.13', '', '', 0),
(1257, '65VQESUT03', 'personnelImg/default_img.jpg', '68633f5ed128c.jpg', 'PANGANTIHON', 'JOHN IRVING', 'TOLEDO', '', 3, 3, '07/01/2025', '09:52 AM', 0, 'on', 'AM IN', '192.168.1.13', '', '', 0),
(1258, 'RCCO5TPACC', 'personnelImg/default_img.jpg', '68633f9ad2382.jpg', 'PIMENTEL', 'EMY', 'MAGBANUA', '', 12, 3, '07/01/2025', '09:53 AM', 0, 'on', 'AM IN', '192.168.1.13', '', '', 0),
(1259, '5BJ6IAN1FB', 'personnelImg/default_img.jpg', '68633fb7d0705.jpg', 'ORBIGOSO', 'ELIZABETH', 'SENIO', '', 8, 3, '07/01/2025', '09:53 AM', 0, 'on', 'AM IN', '192.168.1.13', '', '', 0),
(1260, '2FUFQBHUQM', 'personnelImg/default_img.jpg', '68633fe4d02bc.jpg', 'DECENA', 'LANNE', 'VILLARETE', '', 24, 3, '07/01/2025', '09:54 AM', 0, 'on', 'AM IN', '192.168.1.13', '', '', 0),
(1261, 'HEPAB6X3PL', 'personnelImg/default_img.jpg', '68634067d0e47.jpg', 'VILLAMATER', 'LORALYN', 'BARROCA', '', 12, 3, '07/01/2025', '09:56 AM', 0, 'on', 'AM IN', '192.168.1.13', '', '', 0),
(1262, '1DSSFVBIFF', 'personnelImg/default_img.jpg', '686351dfacd5e.jpg', 'MAESTRECAMPO', 'EVELYN', 'ELARMO', '', 7, 3, '07/01/2025', '11:11 AM', 40286, 'on', 'AM OUT', '192.168.1.13', '', '', 0),
(1263, 'OSYDS2VNEW', 'personnelImg/default_img.jpg', '6867453d19576.jpg', 'ABALLE', 'BETHEL', 'PERFUMA', '', 10, 3, '07/04/2025', '11:06 AM', 0, 'on', 'AM IN', '172.22.85.95', '', '', 0),
(1264, 'OSYDS2VNEW', 'personnelImg/default_img.jpg', '68674f952dbe6.jpg', 'ABALLE', 'BETHEL', 'PERFUMA', '', 10, 3, '07/04/2025', '11:50 AM', 42644, 'on', 'AM OUT', '172.22.85.95', '', '', 0),
(1265, 'N2CS52WG6G', 'personnelImg/default_img.jpg', '686751003182b.jpg', 'GIGANAN', 'GRACE JOY', 'ESTRAO', '', 2, 3, '07/04/2025', '11:56 AM', 0, 'on', 'AM IN', '172.22.85.95', '', '', 0),
(1266, 'N2CS52WG6G', 'personnelImg/default_img.jpg', '686750ff29aef.jpg', 'GIGANAN', 'GRACE JOY', 'ESTRAO', '', 2, 3, '07/04/2025', '11:56 AM', 0, 'off', 'PM IN', '172.22.85.95', '', '', 0),
(1267, 'KDV12JKLNR', 'personnelImg/default_img.jpg', '68675156282be.jpg', 'TRINIO-SANTES', 'VANESSA', 'LIRAZAN', '', 3, 3, '07/04/2025', '11:58 AM', 0, 'on', 'AM IN', '172.22.85.95', '', '', 0),
(1268, 'KDV12JKLNR', 'personnelImg/default_img.jpg', '686751552ad1d.jpg', 'TRINIO-SANTES', 'VANESSA', 'LIRAZAN', '', 3, 3, '07/04/2025', '11:58 AM', 0, 'off', 'PM IN', '172.22.85.95', '', '', 0),
(1269, 'KUNUEE1ZKH', 'personnelImg/default_img.jpg', '686752c22676a.jpg', 'YUSAY', 'JOSE', 'TOGLE', 'III ', 16, 3, '07/04/2025', '12:04 PM', 0, 'off', 'PM IN', '172.22.85.95', '', '', 0),
(1270, 'D4ZWVFKO1E', 'personnelImg/default_img.jpg', '686753ea29f24.jpg', 'LLAMADO', 'MARISSA', 'MAQUILING', '', 2, 3, '07/04/2025', '12:09 PM', 0, 'off', 'PM IN', '172.22.85.95', '', '', 0),
(1271, 'SJLE6GGLD6', 'personnelImg/nrf04z55-che.jpg', '6867552a291cc.jpg', 'JUAREZ', 'CHERRY LYNN', 'MONTAÑO', '', 2, 3, '07/04/2025', '12:14 PM', 0, 'off', 'PM IN', '172.22.85.95', '', '', 0),
(1272, 'T1X1EOAO50', 'personnelImg/default_img.jpg', '686759a12bba6.jpg', 'HERRADURA', 'GINALYN ', 'OBENZA', '', 2, 3, '07/04/2025', '12:33 PM', 0, 'off', 'PM IN', '172.22.85.95', '', '', 0),
(1273, 'NZ0IKZI1IR', 'personnelImg/default_img.jpg', '68675c257200d.jpg', 'MANGOGTONG', 'MA. MARILOU ', 'ESTRELLA', '', 10, 3, '07/04/2025', '12:44 PM', 0, 'off', 'PM IN', '172.22.85.95', '', '', 0),
(1274, '4RE6HDPT3R', 'personnelImg/default_img.jpg', '68675d092bbf7.jpg', 'JIMENEZ', 'LEO', 'TOMILBA', '', 14, 3, '07/04/2025', '12:48 PM', 0, 'off', 'PM IN', '172.22.85.95', '', '', 0),
(1275, 'DFR5QGBYHW', 'personnelImg/default_img.jpg', '68675dd82999f.jpg', 'BARO', 'PHOEBE', 'MANILINGAN', '', 12, 3, '07/04/2025', '12:51 PM', 0, 'off', 'PM IN', '172.22.85.95', '', '', 0),
(1276, 'V2M0IIAYRM', 'personnelImg/default_img.jpg', '68675e28284fb.jpg', 'VILLANUEVA', 'RAYGILDA', 'VERGARA', '', 12, 3, '07/04/2025', '12:52 PM', 0, 'off', 'PM IN', '172.22.85.95', '', '', 0),
(1277, 'YFUCRYEDIV', 'personnelImg/yfucryediv-jetro.png', '68675fb227da8.jpg', 'BARROCA', 'JETTER', 'SENIO', '', 14, 3, '07/04/2025', '12:59 PM', 0, 'off', 'PM IN', '172.22.85.95', '', '', 0),
(1278, '0LKJLM2C22', 'personnelImg/default_img.jpg', '686769761fc0b.jpg', 'MANGILIMUTAN', 'LORRAINE MAE', 'GESTOSO', '', 2, 3, '07/04/2025', '01:41 PM', 0, 'on', 'PM IN', '172.22.85.95', '', '', 0),
(1279, 'RK02GLHBHR', 'personnelImg/default_img.jpg', '686769c222175.jpg', 'PINONGGAN', 'JOSEPH', 'ALBERIO', '', 2, 3, '07/04/2025', '01:42 PM', 0, 'on', 'PM IN', '172.22.85.95', '', '', 0),
(1280, 'N2CS52WG6G', 'personnelImg/default_img.jpg', '686769fe1eb3e.jpg', 'GIGANAN', 'GRACE JOY', 'ESTRAO', '', 2, 3, '07/04/2025', '01:43 PM', 0, 'on', 'PM OUT', '172.22.85.95', '', '', 0),
(1281, 'IO1IY5L4VB', 'personnelImg/default_img.jpg', '68676a141f627.jpg', 'CORTADO', 'ROGELIO', 'ALFANTA', '', 2, 3, '07/04/2025', '01:43 PM', 0, 'on', 'PM IN', '172.22.85.95', '', '', 0),
(1282, 'D4ZWVFKO1E', 'personnelImg/default_img.jpg', '68676a261dffa.jpg', 'LLAMADO', 'MARISSA', 'MAQUILING', '', 2, 3, '07/04/2025', '01:44 PM', 0, 'on', 'PM OUT', '172.22.85.95', '', '', 0),
(1283, 'UV0LOFMFS3', 'personnelImg/default_img.jpg', '68676a441ee2c.jpg', 'LOGRONIO', 'JOESIFIL', 'FAJARDO', '', 2, 3, '07/04/2025', '01:44 PM', 0, 'on', 'PM IN', '172.22.85.95', '', '', 0),
(1284, '0LKJLM2C22', 'personnelImg/default_img.jpg', '686795901e8d0.jpg', 'MANGILIMUTAN', 'LORRAINE MAE', 'GESTOSO', '', 2, 3, '07/04/2025', '04:49 PM', 0, 'off', 'PM OUT', '172.22.85.95', '', '', 0),
(1285, 'RK02GLHBHR', 'personnelImg/default_img.jpg', '686796a33bc74.jpg', 'PINONGGAN', 'JOSEPH', 'ALBERIO', '', 2, 3, '07/04/2025', '04:53 PM', 0, 'off', 'PM OUT', '192.168.24.94', '', '', 0),
(1286, 'IO1IY5L4VB', 'personnelImg/default_img.jpg', '686796b33b402.jpg', 'CORTADO', 'ROGELIO', 'ALFANTA', '', 2, 3, '07/04/2025', '04:54 PM', 0, 'off', 'PM OUT', '192.168.24.94', '', '', 0),
(1287, 'UV0LOFMFS3', 'personnelImg/default_img.jpg', '68679bb53bf10.jpg', 'LOGRONIO', 'JOESIFIL', 'FAJARDO', '', 2, 3, '07/04/2025', '05:15 PM', 0, 'off', 'PM OUT', '192.168.24.94', '', '', 0),
(1288, '0LKJLM2C22', 'personnelImg/default_img.jpg', '6868897b18af1.jpg', 'MANGILIMUTAN', 'LORRAINE MAE', 'GESTOSO', '', 2, 3, '07/05/2025', '10:10 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1289, 'E0VZSU0W6G', 'personnelImg/default_img.jpg', '6868898b088ae.jpg', 'APLAON', 'MA. LEE', 'MAESTRECAMPO', '', 23, 3, '07/05/2025', '10:10 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1290, 'EKSUEACJM5', 'personnelImg/default_img.jpg', '686889920e8ea.jpg', 'ACIBIDO', 'CHE GENEROSO', 'PARO', '', 14, 3, '07/05/2025', '10:10 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1291, 'R0ZJXTVRR4', 'personnelImg/default_img.jpg', '6868899709ee0.jpg', 'AVELINO', 'JULIEBERT', 'AZUCENA', '', 2, 3, '07/05/2025', '10:10 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1292, 'IO1IY5L4VB', 'personnelImg/default_img.jpg', '6868899f0bde9.jpg', 'CORTADO', 'ROGELIO', 'ALFANTA', '', 2, 3, '07/05/2025', '10:10 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1293, 'L33XK2MQYL', 'personnelImg/default_img.jpg', '686889a3089da.jpg', 'RELIQUIAS', 'JOHNNY RAY', 'L.', '', 2, 3, '07/05/2025', '10:10 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1294, 'PLCEEHAUGT', 'personnelImg/default_img.jpg', '686889a7095f6.jpg', 'MARQUEZ', 'MAE ANN', 'GIGANAN', '', 15, 3, '07/05/2025', '10:10 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1295, '1Z6LX20PVU', 'personnelImg/default_img.jpg', '686889ab0982b.jpg', 'RELIQUIAS', 'JOHN MARK', 'BALUNGCAS', '', 23, 3, '07/05/2025', '10:10 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1296, 'VWUQQEPZ5P', 'personnelImg/default_img.jpg', '686889af0afa4.jpg', 'DIONALDO', 'ROEM', 'S.', '', 2, 3, '07/05/2025', '10:10 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1297, 'R0VM3TX265', 'personnelImg/default_img.jpg', '686889ca0950e.jpg', 'MONTANO', 'EVALYN', 'C.', '', 24, 3, '07/05/2025', '10:11 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1298, '2O1JSGHAYX', 'personnelImg/default_img.jpg', '686889d20a692.jpg', 'GAUAL', 'ROLIN', 'BERGONIO', 'SR. ', 12, 3, '07/05/2025', '10:11 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1299, 'PQMSBLBNJ6', 'personnelImg/default_img.jpg', '686889d608b59.jpg', 'VIDAURRAZAGA', 'NOAH', 'VASQUEZ', '', 2, 3, '07/05/2025', '10:11 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1300, 'VUM5AWYEUH', 'personnelImg/default_img.jpg', '686889fd09655.jpg', 'SUMUGAT', 'JENELYN', 'TELONIO', '', 3, 3, '07/05/2025', '10:12 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1301, 'BSLAWXCQ2N', 'personnelImg/default_img.jpg', '68688a050ad81.jpg', 'BAYDO', 'JULITO', 'CAYAS', '', 3, 3, '07/05/2025', '10:12 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1302, 'P-ZGT-382010-110', 'personnelImg/default_img.jpg', '68688a0b0a5a8.jpg', 'TEMBREVILLA', 'ZOSIMO', 'GAVILAGA', '', 3, 3, '07/05/2025', '10:12 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1303, 'SARR3EM6ZV', 'personnelImg/default_img.jpg', '68688a120b318.jpg', 'PACURIB', 'JULITO', 'PLAÑA', 'JR. ', 3, 3, '07/05/2025', '10:12 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1304, 'FOSBE6FBBP', 'personnelImg/default_img.jpg', '68688a1a0d9ef.jpg', 'MAHINAY', 'FRANCISCO', 'MANINANTAN', 'SR. ', 3, 3, '07/05/2025', '10:12 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1305, '6KR3ZM4BU3', 'personnelImg/default_img.jpg', '68688a1f0fbb7.jpg', 'TELONIO', 'JAMES ANDREW', 'GUINTOS', '', 15, 3, '07/05/2025', '10:12 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1306, 'JT6NRMM2MG', 'personnelImg/default_img.jpg', '68688a24093ef.jpg', 'LABRADOR', 'REY', 'TRIBUCIO', '', 10, 3, '07/05/2025', '10:12 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1307, 'Z2TDUSRUV6', 'personnelImg/default_img.jpg', '68688a290c156.jpg', 'BIACA', 'HERBERT', 'BORNALES', '', 3, 3, '07/05/2025', '10:12 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1308, 'KZFCP2S0HT', 'personnelImg/default_img.jpg', '68688a2e08798.jpg', 'GUINTOS', 'JOSEPHINE', 'INDINO', '', 10, 3, '07/05/2025', '10:13 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1309, 'UV0LOFMFS3', 'personnelImg/default_img.jpg', '68688a330f3ea.jpg', 'LOGRONIO', 'JOESIFIL', 'FAJARDO', '', 2, 3, '07/05/2025', '10:13 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1310, 'HOA5M4V2KU', 'personnelImg/default_img.jpg', '68688a38096d8.jpg', 'TEMBREVILLA', 'THOMY', 'G.', '', 3, 3, '07/05/2025', '10:13 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1311, '65VQESUT03', 'personnelImg/default_img.jpg', '68688a4008829.jpg', 'PANGANTIHON', 'JOHN IRVING', 'TOLEDO', '', 3, 3, '07/05/2025', '10:13 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1312, 'LS5IJYTERX', 'personnelImg/default_img.jpg', '68688a460a74a.jpg', 'GUINTOS', 'WINSTON', 'NAVA', '', 3, 3, '07/05/2025', '10:13 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1313, 'KDV12JKLNR', 'personnelImg/default_img.jpg', '68688a4a0a266.jpg', 'TRINIO-SANTES', 'VANESSA', 'LIRAZAN', '', 3, 3, '07/05/2025', '10:13 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1314, 'GOYLQLNANM', 'personnelImg/default_img.jpg', '68688a6e08a00.jpg', 'DELOTINA', 'DANILO', 'NAPIERE', '', 17, 3, '07/05/2025', '10:14 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1315, 'T4BXLLYWTG', 'personnelImg/default_img.jpg', '68688a730a41b.jpg', 'DERIT', 'JOSE ALAN ', 'DURO', '', 17, 3, '07/05/2025', '10:14 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1316, 'H05GELOMZN', 'personnelImg/default_img.jpg', '68688a770ba70.jpg', 'DEQUINA', 'REYNOLD', 'B.', '', 17, 3, '07/05/2025', '10:14 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1317, 'QQZIGTSWBI', 'personnelImg/default_img.jpg', '68688a7c12268.jpg', 'NACION', 'ELBRED', 'S.', '', 17, 3, '07/05/2025', '10:14 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1318, 'K0GBVLRIOM', 'personnelImg/default_img.jpg', '68688a8407cbe.jpg', 'JORDAN', 'MARK ANTHONY', 'TOMADO', '', 3, 3, '07/05/2025', '10:14 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1319, 'R52SQSFJB4', 'personnelImg/default_img.jpg', '68688a89085b5.jpg', 'BONILLA', 'JURY', 'BENLOT', '', 17, 3, '07/05/2025', '10:14 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1320, 'HR1TD1EOO6', 'personnelImg/default_img.jpg', '68688aa10a9a4.jpg', 'NATALIO', 'JOEFREY', 'TEMBREVILLA', '', 9, 3, '07/05/2025', '10:14 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1321, 'N0PELGBXHV', 'personnelImg/default_img.jpg', '68688aa50939e.jpg', 'GUSTILO', 'LEILANI', 'DECENA', '', 9, 3, '07/05/2025', '10:14 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1322, '2XYVUZG44K', 'personnelImg/default_img.jpg', '68688aaa07d63.jpg', 'RELIQUIAS', 'JOERIBEL', 'TORIANO', '', 9, 3, '07/05/2025', '10:15 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1323, 'C4XW3NQ3CE', 'personnelImg/default_img.jpg', '68688ab009b2a.jpg', 'ACADEMIA', 'RIZALYN', 'CAYANAN', '', 9, 3, '07/05/2025', '10:15 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1324, '2KLYB0WHDY', 'personnelImg/default_img.jpg', '68688acd08a9b.jpg', 'GESTOSO', 'CARLITO', 'BAVIERA', '', 10, 3, '07/05/2025', '10:15 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1325, '0RVK3VBCZ1', 'personnelImg/default_img.jpg', '68688ad107209.jpg', 'MANOS', 'ANNABELLE', 'PERIGUA', '', 5, 3, '07/05/2025', '10:15 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1326, 'F255HSX3FM', 'personnelImg/default_img.jpg', '68688ad50b6c7.jpg', 'GUINTOS', 'ERICA', 'MANANGAN', '', 14, 3, '07/05/2025', '10:15 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1327, 'O6JWSQ5BTR', 'personnelImg/default_img.jpg', '68688adc098b3.jpg', 'DELA CONCEPTION', 'IMELDA', 'ESPENORIO', '', 14, 3, '07/05/2025', '10:15 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1328, 'CEPHX3JOM0', 'personnelImg/default_img.jpg', '68688ae108dbe.jpg', 'TIBAYDE', 'IRENE', 'GIGANAN', '', 10, 3, '07/05/2025', '10:15 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1329, 'ES1MY5CB5N', 'personnelImg/default_img.jpg', '68688ae509e51.jpg', 'BUDACA', 'REVINIA', 'AMACIO', '', 10, 3, '07/05/2025', '10:16 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1330, 'KVKMFQDZDV', 'personnelImg/default_img.jpg', '68688ae9078dc.jpg', 'GA-AN', 'LENLY', 'NOSAL', '', 10, 3, '07/05/2025', '10:16 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1331, 'NZ0IKZI1IR', 'personnelImg/default_img.jpg', '68688aed08e10.jpg', 'MANGOGTONG', 'MA. MARILOU ', 'ESTRELLA', '', 10, 3, '07/05/2025', '10:16 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1332, 'OSYDS2VNEW', 'personnelImg/default_img.jpg', '68688af10780b.jpg', 'ABALLE', 'BETHEL', 'PERFUMA', '', 10, 3, '07/05/2025', '10:16 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1333, 'T0SO3GEQZX', 'personnelImg/default_img.jpg', '68688b000a9a4.jpg', 'TUPAS', 'ANTHONY', 'L.', '', 11, 3, '07/05/2025', '10:16 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1334, '0M1YGUSYJR', 'personnelImg/default_img.jpg', '68688b0509b70.jpg', 'BALOYO', 'HANSEL', 'M.', '', 11, 3, '07/05/2025', '10:16 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1335, '5Z5WKCHQGF', 'personnelImg/default_img.jpg', '68688b090c57f.jpg', 'DOLOR', 'ROLY', 'D.', '', 11, 3, '07/05/2025', '10:16 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1336, 'XD5JB3HCC0', 'personnelImg/default_img.jpg', '68688b0e0b318.jpg', 'LAREÑO', 'LIWAYA', 'MAHINAY', '', 11, 3, '07/05/2025', '10:16 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1337, '1YJTKOZ61A', 'personnelImg/default_img.jpg', '68688b1608b8d.jpg', 'DURAN', 'MICHELLE', 'M.', '', 11, 3, '07/05/2025', '10:16 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1338, 'ZJPTEJP5UQ', 'personnelImg/default_img.jpg', '68688b200f0b8.jpg', 'AMANTE', 'LEONIZA', 'FLORES', '', 11, 3, '07/05/2025', '10:17 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1339, 'FKX31XYW34', 'personnelImg/default_img.jpg', '68688b270ca42.jpg', 'DELA FUENTE', 'ALVIN', 'ORMEO', '', 11, 3, '07/05/2025', '10:17 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1340, 'BLJQQ3XW3K', 'personnelImg/default_img.jpg', '68688b320c272.jpg', 'NORVIE', 'JOSECO', 'A.', '', 11, 3, '07/05/2025', '10:17 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1341, 'RVR5BBU5ZV', 'personnelImg/default_img.jpg', '68688b370c30f.jpg', 'CANA', 'EVA MAE', 'G.', '', 11, 3, '07/05/2025', '10:17 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1342, 'I0FY3RFXM5', 'personnelImg/default_img.jpg', '68688b460d739.jpg', 'TILOS', 'OSCAR', 'VILLARETE', '', 4, 3, '07/05/2025', '10:17 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1343, 'CCEWWUW3P2', 'personnelImg/default_img.jpg', '68688b4a0888f.jpg', 'TUBILLEJA', 'THEODORE', 'SIGUEZA', 'JR. ', 6, 3, '07/05/2025', '10:17 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1344, 'M1RF4GW0TT', 'personnelImg/default_img.jpg', '68688b5408c2e.jpg', 'GUINTOS', 'MA. ELENA', 'N.', '', 14, 3, '07/05/2025', '10:17 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1345, 'T1X1EOAO50', 'personnelImg/default_img.jpg', '68688b5f0b024.jpg', 'HERRADURA', 'GINALYN ', 'OBENZA', '', 2, 3, '07/05/2025', '10:18 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1346, 'YFUCRYEDIV', 'personnelImg/yfucryediv-jetro.png', '68688b640a45a.jpg', 'BARROCA', 'JETTER', 'SENIO', '', 14, 3, '07/05/2025', '10:18 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1347, 'SJLE6GGLD6', 'personnelImg/nrf04z55-che.jpg', '68688b68090da.jpg', 'JUAREZ', 'CHERRY LYNN', 'MONTAÑO', '', 2, 3, '07/05/2025', '10:18 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1348, 'MF4R0LP0PT', 'personnelImg/default_img.jpg', '68688b7f0d2ef.jpg', 'TOLEDO', 'RAYMUND ANTHONY', 'INOLINO', '', 4, 3, '07/05/2025', '10:18 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1349, '2SOQJG3P6F', 'personnelImg/default_img.jpg', '68688b840d0fe.jpg', 'SANTES', 'CINDY', 'REBOLDAL', '', 4, 3, '07/05/2025', '10:18 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1350, 'LTQHD2BPYJ', 'personnelImg/default_img.jpg', '68688b870bb65.jpg', 'GALON', 'RANDOLF', 'BLANCO', '', 4, 3, '07/05/2025', '10:18 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1351, '111LXWXFLY', 'personnelImg/default_img.jpg', '68688b9509a9d.jpg', 'NICOR', 'MICHAEL', 'RAMOS', '', 7, 3, '07/05/2025', '10:18 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1352, '2SJBOJTXPC', 'personnelImg/default_img.jpg', '68688b9b08dda.jpg', 'GELLECANAO', 'MURIELLE', 'LIRAZAN', '', 7, 3, '07/05/2025', '10:19 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1353, 'PWU0GP4SHN', 'personnelImg/default_img.jpg', '68688b9f0b017.jpg', 'SANTES', 'AZELA', 'FLORES', '', 7, 3, '07/05/2025', '10:19 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1354, 'Q0QB3ZVYNK', 'personnelImg/default_img.jpg', '68688ba30a3e6.jpg', 'VILLARETE', 'ANGELA', 'TELONIO', '', 7, 3, '07/05/2025', '10:19 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0);
INSERT INTO `personnel_logs` (`log_id`, `RFTag_id`, `img`, `captured_img`, `lname`, `fname`, `mname`, `suffix`, `do_id`, `shift_id`, `logDate`, `logTime`, `logTime_sec`, `late_status`, `logFlow`, `client_ip`, `remarks`, `travel_leave_code`, `ref_log_id`) VALUES
(1355, 'XFSKZYRVSI', 'personnelImg/default_img.jpg', '68688ba70acb2.jpg', 'LASTRILLA', 'GUIDRALYN', 'P.', '', 7, 3, '07/05/2025', '10:19 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1356, 'Q0BSK1IGMH', 'personnelImg/default_img.jpg', '68688baa09a05.jpg', 'BARIQUIT', 'PRACEDES', 'CABONILAS', '', 7, 3, '07/05/2025', '10:19 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1357, 'UUR4C35PLC', 'personnelImg/default_img.jpg', '68688baf0aa18.jpg', 'ALIMANE', 'JINKY', 'GALPO', '', 7, 3, '07/05/2025', '10:19 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1358, 'N0AXWJPKNQ', 'personnelImg/default_img.jpg', '68688bb20b048.jpg', 'SORONGON', 'NITA', 'A.', '', 7, 3, '07/05/2025', '10:19 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1359, '1DSSFVBIFF', 'personnelImg/default_img.jpg', '68688bb60cd82.jpg', 'MAESTRECAMPO', 'EVELYN', 'ELARMO', '', 7, 3, '07/05/2025', '10:19 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1360, 'CMDUXPZ44I', 'personnelImg/default_img.jpg', '68688bc7092a8.jpg', 'GONZAL', 'SANDRA', 'CASTILLO', '', 8, 3, '07/05/2025', '10:19 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1361, 'N4DZR4BBY0', 'personnelImg/default_img.jpg', '68688bca09d81.jpg', 'RELIQUIAS', 'MARIA FE', 'GUINTOS', '', 8, 3, '07/05/2025', '10:19 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1362, 'JETERWW23U', 'personnelImg/nrf5ihz6-mae-flor.jpg', '68688bd008a08.jpg', 'BARRIOS', 'MAE FLOR', 'ALMAIZ', '', 8, 3, '07/05/2025', '10:19 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1363, '5BJ6IAN1FB', 'personnelImg/default_img.jpg', '68688bd508ab1.jpg', 'ORBIGOSO', 'ELIZABETH', 'SENIO', '', 8, 3, '07/05/2025', '10:20 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1364, '6DZNSFDRJG', 'personnelImg/default_img.jpg', '68688be509a3f.jpg', 'MANOS', 'TEOFILO', 'ENCARGUEZ', 'JR. ', 24, 3, '07/05/2025', '10:20 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1365, '1QETKAFQWO', 'personnelImg/default_img.jpg', '68688bea0bc7a.jpg', 'DELOTINA', 'JOFEL', 'EVANGELISTA', '', 24, 3, '07/05/2025', '10:20 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1366, 'BSY3X4CY3E', 'personnelImg/default_img.jpg', '68688bef08e0a.jpg', 'SANTES', 'JOCELYN', 'CORONEL', '', 24, 3, '07/05/2025', '10:20 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1367, 'ZGMTZ2Y3BT', 'personnelImg/default_img.jpg', '68688bf30766d.jpg', 'OCTAVIO', 'PETER JOHN ', 'LAZALITA', '', 24, 3, '07/05/2025', '10:20 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1368, '2FUFQBHUQM', 'personnelImg/default_img.jpg', '68688bf70b15d.jpg', 'DECENA', 'LANNE', 'VILLARETE', '', 24, 3, '07/05/2025', '10:20 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1369, 'DCORCD1ICP', 'personnelImg/default_img.jpg', '68688bfb095bb.jpg', 'TUMA-OB', 'LESLIE AIKEE DYAN', 'GIGANAN', '', 24, 3, '07/05/2025', '10:20 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1370, 'N3CFWPHFQX', 'personnelImg/default_img.jpg', '68688c0a0bf34.jpg', 'SANTES', 'CAROL ANN', 'TILOS', '', 5, 3, '07/05/2025', '10:20 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1371, 'DO2X14YKJR', 'personnelImg/default_img.jpg', '68688c12079cb.jpg', 'VILLARUBIA', 'ALTHEA', 'PIA', '', 5, 3, '07/05/2025', '10:21 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1372, 'E6LFKFXAD0', 'personnelImg/default_img.jpg', '68688c160819e.jpg', 'NUÑESCO', 'AIZA', 'ANDO', '', 5, 3, '07/05/2025', '10:21 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1373, 'RK02GLHBHR', 'personnelImg/default_img.jpg', '68688c260a400.jpg', 'PINONGGAN', 'JOSEPH', 'ALBERIO', '', 2, 3, '07/05/2025', '10:21 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1374, 'D4ZWVFKO1E', 'personnelImg/default_img.jpg', '68688c2b0e111.jpg', 'LLAMADO', 'MARISSA', 'MAQUILING', '', 2, 3, '07/05/2025', '10:21 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1375, 'KHA2LUXNL1', 'personnelImg/default_img.jpg', '68688c3209452.jpg', 'BA-AL', 'VON MARVIN', 'M.', '', 2, 3, '07/05/2025', '10:21 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1376, 'N2CS52WG6G', 'personnelImg/default_img.jpg', '68688c3a0c756.jpg', 'GIGANAN', 'GRACE JOY', 'ESTRAO', '', 2, 3, '07/05/2025', '10:21 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1377, 'KP6G24TB40', 'personnelImg/default_img.jpg', '68688c3f0967a.jpg', 'RELADO', 'MEDALIA', 'VALENZUELA', '', 8, 3, '07/05/2025', '10:21 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1378, 'M1Y5ETAYD6', 'personnelImg/default_img.jpg', '68688c5209287.jpg', 'NAVA', 'LUZ SALOME', 'VILLA', '', 14, 3, '07/05/2025', '10:22 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1379, '6D0YWDKLPP', 'personnelImg/default_img.jpg', '68688c590b304.jpg', 'AGUHAYON', 'RICARDO', 'UBAMOS', '', 14, 3, '07/05/2025', '10:22 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1380, 'OINBKY4BGA', 'personnelImg/default_img.jpg', '68688c620ce7b.jpg', 'DECATORIA', 'JOEBERT', 'SARIL', '', 3, 3, '07/05/2025', '10:22 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1381, 'WDB3J415ZP', 'personnelImg/default_img.jpg', '68688c6b0d011.jpg', 'AKOL', 'SHARRA MAE', 'ARTICA', '', 10, 3, '07/05/2025', '10:22 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1382, '4RE6HDPT3R', 'personnelImg/default_img.jpg', '68688c6f0e5d1.jpg', 'JIMENEZ', 'LEO', 'TOMILBA', '', 14, 3, '07/05/2025', '10:22 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1383, 'GSQZ2OCVHQ', 'personnelImg/default_img.jpg', '68688c750b4d0.jpg', 'RELIQUIAS', 'GERALD', 'TORIANO', '', 14, 3, '07/05/2025', '10:22 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1384, 'NZ0IKZI1IR', 'personnelImg/default_img.jpg', '68688c7b094d6.jpg', 'MANGOGTONG', 'MA. MARILOU ', 'ESTRELLA', '', 10, 3, '07/05/2025', '10:22 AM', 37369, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1385, 'FU6VB3OYK4', 'personnelImg/default_img.jpg', '68688c800a6ac.jpg', 'MANGOGTONG', 'WILMAR', 'ESTRELLA', '', 14, 3, '07/05/2025', '10:22 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1386, '2CKQGJE4K6', 'personnelImg/default_img.jpg', '68688c870af41.jpg', 'DELLOSO', 'CHRISTOPHER', 'M.', '', 14, 3, '07/05/2025', '10:23 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1387, 'YCSOHXN5TA', 'personnelImg/default_img.jpg', '68688c970ad65.jpg', 'GALLARDA', 'ELNOR', 'PACLAONA', '', 12, 3, '07/05/2025', '10:23 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1388, 'W2DGEUTP62', 'personnelImg/default_img.jpg', '68688c9b0a1d4.jpg', 'LOREDO', 'MARIA ELENA', 'DELLOSO', '', 14, 3, '07/05/2025', '10:23 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1389, 'HDMCPVKYB1', 'personnelImg/default_img.jpg', '68688ca50957e.jpg', 'ROXAS', 'MARY ANN', 'VILLAFUERTE', '', 12, 3, '07/05/2025', '10:23 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1390, 'ZHXY3CO3KQ', 'personnelImg/default_img.jpg', '68688cad0bd54.jpg', 'DELA FUENTE', 'EBER', 'ORMEO', '', 12, 3, '07/05/2025', '10:23 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1391, 'MS5LRO1IJE', 'personnelImg/default_img.jpg', '68688cb30d9b5.jpg', 'VILLANUEVA', 'ROSALIE', 'ELARDO', '', 12, 3, '07/05/2025', '10:23 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1392, 'WSJIHMXEN6', 'personnelImg/default_img.jpg', '68688cb90a2ea.jpg', 'TILOS', 'MARCELINA', 'CELIS', '', 12, 3, '07/05/2025', '10:23 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1393, '6ON2RWCQEM', 'personnelImg/default_img.jpg', '68688cbd085a0.jpg', 'GAREZA', 'MELANIE', 'CELIS', '', 12, 3, '07/05/2025', '10:23 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1394, 'PMOLWNYZZA', 'personnelImg/default_img.jpg', '68688cc10a62c.jpg', 'SUSANA', 'FREDERICK DAVY', 'PINGCALE', '', 12, 3, '07/05/2025', '10:23 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1395, 'LUPQ4RIQUJ', 'personnelImg/default_img.jpg', '68688cc808c88.jpg', 'TILOS', 'RIZALIE', 'CELIZ', '', 12, 3, '07/05/2025', '10:24 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1396, 'ZXDNHZFPMW', 'personnelImg/default_img.jpg', '68688ccd0c140.jpg', 'PUBLICO', 'NOEMI', 'TOMADO', '', 12, 3, '07/05/2025', '10:24 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1397, 'V2M0IIAYRM', 'personnelImg/default_img.jpg', '68688cd40866f.jpg', 'VILLANUEVA', 'RAYGILDA', 'VERGARA', '', 12, 3, '07/05/2025', '10:24 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1398, 'DFR5QGBYHW', 'personnelImg/default_img.jpg', '68688cd80b8a6.jpg', 'BARO', 'PHOEBE', 'MANILINGAN', '', 12, 3, '07/05/2025', '10:24 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1399, 'MHDZI160KN', 'personnelImg/default_img.jpg', '68688cdc0c679.jpg', 'TEMBREVILLA', 'CAROLINE', 'CANILLO', '', 12, 3, '07/05/2025', '10:24 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1400, 'QESM444ZK0', 'personnelImg/default_img.jpg', '68688ce10be23.jpg', 'TEMBREVILLA', 'RONA', 'ESCOSAR', '', 12, 3, '07/05/2025', '10:24 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1401, 'ZV1EXSSS30', 'personnelImg/default_img.jpg', '68688ce707227.jpg', 'BONGCAWEL', 'GENELYN ', 'HISONA', '', 12, 3, '07/05/2025', '10:24 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1402, 'GJCC6XY3BR', 'personnelImg/default_img.jpg', '68688cee09464.jpg', 'ANTIQUEÑO', 'ANA MARIE', 'SANOY', '', 12, 3, '07/05/2025', '10:24 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1403, 'GZ5LO0QDRC', 'personnelImg/default_img.jpg', '68688cf60a4d1.jpg', 'GARCIA', 'ADELA', 'ANTOLIN', '', 12, 3, '07/05/2025', '10:24 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1404, 'LVX5C46TAS', 'personnelImg/default_img.jpg', '68688cfc09f2d.jpg', 'SILVESTRE', 'CYNTHIA', 'LAMBOT', '', 12, 3, '07/05/2025', '10:24 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1405, 'GP4Q0NOWGA', 'personnelImg/default_img.jpg', '68688d010be61.jpg', 'GERMINAL', 'CRISTUTO', 'M.', '', 17, 3, '07/05/2025', '10:25 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1406, 'HSDQZ6G6PA', 'personnelImg/default_img.jpg', '68688d06087a2.jpg', 'PEREZ', 'JOSE MARIA', 'LIM', 'JR. ', 12, 3, '07/05/2025', '10:25 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1407, 'HEPAB6X3PL', 'personnelImg/default_img.jpg', '68688d0c086e2.jpg', 'VILLAMATER', 'LORALYN', 'BARROCA', '', 12, 3, '07/05/2025', '10:25 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1408, 'RCCO5TPACC', 'personnelImg/default_img.jpg', '68688d190d34b.jpg', 'PIMENTEL', 'EMY', 'MAGBANUA', '', 12, 3, '07/05/2025', '10:25 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1409, '5UVF66ZPY0', 'personnelImg/default_img.jpg', '68688d1f0b03f.jpg', 'MAQUILING', 'WARREN', 'RAMIREZ', '', 12, 3, '07/05/2025', '10:25 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1410, 'I03I6VZELI', 'personnelImg/default_img.jpg', '68688d250c617.jpg', 'GALAN', 'MARY JANE', 'CELIS', '', 12, 3, '07/05/2025', '10:25 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1411, 'G6A2OEKXUH', 'personnelImg/default_img.jpg', '68688d2d0a32f.jpg', 'MANGILIMUTAN', 'MA. HEARTY', 'CAÑA', '', 12, 3, '07/05/2025', '10:25 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1412, 'OIMV5KOLF2', 'personnelImg/default_img.jpg', '68688d320883f.jpg', 'PEROSIA', 'GLORY MAE', 'TUNDA-AN', '', 12, 3, '07/05/2025', '10:25 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1413, 'ITZ6XK1TGX', 'personnelImg/default_img.jpg', '68688d4a076b3.jpg', 'LUCENARA', 'ROBIJID', 'Q', '', 15, 3, '07/05/2025', '10:26 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1414, 'WILZVWBBRI', 'personnelImg/default_img.jpg', '68688d4f0b728.jpg', 'OCCEÑA', 'GEM', 'RELAMPAGOS', '', 3, 3, '07/05/2025', '10:26 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1415, 'DV6HSR4T31', 'personnelImg/default_img.jpg', '68688d540a4c2.jpg', 'SABOBO', 'ALFREDO', 'G.', '', 15, 3, '07/05/2025', '10:26 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1416, 'REPYPK1N0K', 'personnelImg/default_img.jpg', '68688d590e578.jpg', 'AMBAGAN', 'EDSEL', 'MILLENDEZ', '', 16, 3, '07/05/2025', '10:26 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1417, 'GRN63LXTVC', 'personnelImg/default_img.jpg', '68688d5e0ad94.jpg', 'GIGANAN', 'AGNES', 'NAVA', '', 15, 3, '07/05/2025', '10:26 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1418, 'KUNUEE1ZKH', 'personnelImg/default_img.jpg', '68688d6c0e204.jpg', 'YUSAY', 'JOSE', 'TOGLE', 'III ', 16, 3, '07/05/2025', '10:26 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1419, 'POT5DLCLXZ', 'personnelImg/default_img.jpg', '68688d70089e9.jpg', 'GUINTOS', 'TINA MARIE', 'LUGA', '', 16, 3, '07/05/2025', '10:26 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1420, 'F3DUWOJ4SZ', 'personnelImg/default_img.jpg', '68688d740aec6.jpg', 'SIASON', 'CHEZAH ERL', 'GAUAL', '', 16, 3, '07/05/2025', '10:26 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1421, 'B42WBNMNSK', 'personnelImg/default_img.jpg', '68688d8309c4e.jpg', 'LIMSIACO', 'ARIANE', 'CONSTANTINO', '', 6, 3, '07/05/2025', '10:27 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1422, 'V4PAJMJ5YO', 'personnelImg/default_img.jpg', '68688d870c382.jpg', 'TUPAS', 'OFELIA', 'TEMBREVILLA', '', 6, 3, '07/05/2025', '10:27 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1423, 'UBFPHM020G', 'personnelImg/default_img.jpg', '68688d8d0fdb4.jpg', 'BONILLA', 'ANNI VER', 'PAMLIEGA', '', 6, 3, '07/05/2025', '10:27 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1424, 'EAUNV5WVM0', 'personnelImg/default_img.jpg', '68688d910cdae.jpg', 'BARROCA', 'JORGIE', 'PERFUMA', '', 6, 3, '07/05/2025', '10:27 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1425, 'EHEYDZ1UYN', 'personnelImg/default_img.jpg', '68688d970a783.jpg', 'ALATON', 'GINA', 'NABOR', '', 6, 3, '07/05/2025', '10:27 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1426, 'DGPF5FDK53', 'personnelImg/default_img.jpg', '68688d9c0aa44.jpg', 'ARANETA', 'JOSELITA', 'GENTELIZO', '', 6, 3, '07/05/2025', '10:27 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1427, 'DURSCXCSIM', 'personnelImg/default_img.jpg', '68688da208fea.jpg', 'MAYANDIA', 'ANALIE', 'ELARMO', '', 6, 3, '07/05/2025', '10:27 AM', 0, 'on', 'AM IN', '192.168.24.94', '', '', 0),
(1428, '0LKJLM2C22', 'personnelImg/default_img.jpg', '68688dd709f49.jpg', 'MANGILIMUTAN', 'LORRAINE MAE', 'GESTOSO', '', 2, 3, '07/05/2025', '10:28 AM', 37717, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1429, 'E0VZSU0W6G', 'personnelImg/default_img.jpg', '68688de40b184.jpg', 'APLAON', 'MA. LEE', 'MAESTRECAMPO', '', 23, 3, '07/05/2025', '10:28 AM', 37730, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1430, 'EKSUEACJM5', 'personnelImg/default_img.jpg', '68688dee09243.jpg', 'ACIBIDO', 'CHE GENEROSO', 'PARO', '', 14, 3, '07/05/2025', '10:29 AM', 37740, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1431, 'R0ZJXTVRR4', 'personnelImg/default_img.jpg', '68688df20c4a4.jpg', 'AVELINO', 'JULIEBERT', 'AZUCENA', '', 2, 3, '07/05/2025', '10:29 AM', 37744, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1432, 'IO1IY5L4VB', 'personnelImg/default_img.jpg', '68688df8089a7.jpg', 'CORTADO', 'ROGELIO', 'ALFANTA', '', 2, 3, '07/05/2025', '10:29 AM', 37750, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1433, 'L33XK2MQYL', 'personnelImg/default_img.jpg', '68688dfc09c52.jpg', 'RELIQUIAS', 'JOHNNY RAY', 'L.', '', 2, 3, '07/05/2025', '10:29 AM', 37754, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1434, 'PLCEEHAUGT', 'personnelImg/default_img.jpg', '68688e010a97f.jpg', 'MARQUEZ', 'MAE ANN', 'GIGANAN', '', 15, 3, '07/05/2025', '10:29 AM', 37759, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1435, '1Z6LX20PVU', 'personnelImg/default_img.jpg', '68688e050a471.jpg', 'RELIQUIAS', 'JOHN MARK', 'BALUNGCAS', '', 23, 3, '07/05/2025', '10:29 AM', 37763, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1436, 'VWUQQEPZ5P', 'personnelImg/default_img.jpg', '68688e0909993.jpg', 'DIONALDO', 'ROEM', 'S.', '', 2, 3, '07/05/2025', '10:29 AM', 37767, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1437, 'R0VM3TX265', 'personnelImg/default_img.jpg', '68688e0e0da07.jpg', 'MONTANO', 'EVALYN', 'C.', '', 24, 3, '07/05/2025', '10:29 AM', 37772, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1438, '2O1JSGHAYX', 'personnelImg/default_img.jpg', '68688e160acf0.jpg', 'GAUAL', 'ROLIN', 'BERGONIO', 'SR. ', 12, 3, '07/05/2025', '10:29 AM', 37780, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1439, 'PQMSBLBNJ6', 'personnelImg/default_img.jpg', '68688e1d0e124.jpg', 'VIDAURRAZAGA', 'NOAH', 'VASQUEZ', '', 2, 3, '07/05/2025', '10:29 AM', 37787, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1440, 'VUM5AWYEUH', 'personnelImg/default_img.jpg', '68688e3e0ba43.jpg', 'SUMUGAT', 'JENELYN', 'TELONIO', '', 3, 3, '07/05/2025', '10:30 AM', 37820, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1441, 'BSLAWXCQ2N', 'personnelImg/default_img.jpg', '68688e450a954.jpg', 'BAYDO', 'JULITO', 'CAYAS', '', 3, 3, '07/05/2025', '10:30 AM', 37827, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1442, 'P-ZGT-382010-110', 'personnelImg/default_img.jpg', '68688e530a00e.jpg', 'TEMBREVILLA', 'ZOSIMO', 'GAVILAGA', '', 3, 3, '07/05/2025', '10:30 AM', 37842, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1443, 'SARR3EM6ZV', 'personnelImg/default_img.jpg', '68688e58079de.jpg', 'PACURIB', 'JULITO', 'PLAÑA', 'JR. ', 3, 3, '07/05/2025', '10:30 AM', 37846, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1444, 'FOSBE6FBBP', 'personnelImg/default_img.jpg', '68688e5f09806.jpg', 'MAHINAY', 'FRANCISCO', 'MANINANTAN', 'SR. ', 3, 3, '07/05/2025', '10:30 AM', 37853, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1445, '6KR3ZM4BU3', 'personnelImg/default_img.jpg', '68688e640a615.jpg', 'TELONIO', 'JAMES ANDREW', 'GUINTOS', '', 15, 3, '07/05/2025', '10:30 AM', 37858, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1446, 'JT6NRMM2MG', 'personnelImg/default_img.jpg', '68688e680b651.jpg', 'LABRADOR', 'REY', 'TRIBUCIO', '', 10, 3, '07/05/2025', '10:31 AM', 37862, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1447, 'Z2TDUSRUV6', 'personnelImg/default_img.jpg', '68688e6d078d2.jpg', 'BIACA', 'HERBERT', 'BORNALES', '', 3, 3, '07/05/2025', '10:31 AM', 37867, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1448, 'KZFCP2S0HT', 'personnelImg/default_img.jpg', '68688e700958f.jpg', 'GUINTOS', 'JOSEPHINE', 'INDINO', '', 10, 3, '07/05/2025', '10:31 AM', 37870, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1449, 'UV0LOFMFS3', 'personnelImg/default_img.jpg', '68688e74083b8.jpg', 'LOGRONIO', 'JOESIFIL', 'FAJARDO', '', 2, 3, '07/05/2025', '10:31 AM', 37874, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1450, 'HOA5M4V2KU', 'personnelImg/default_img.jpg', '68688e790a16b.jpg', 'TEMBREVILLA', 'THOMY', 'G.', '', 3, 3, '07/05/2025', '10:31 AM', 37880, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1451, '65VQESUT03', 'personnelImg/default_img.jpg', '68688e7e0cdf4.jpg', 'PANGANTIHON', 'JOHN IRVING', 'TOLEDO', '', 3, 3, '07/05/2025', '10:31 AM', 37884, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1452, 'LS5IJYTERX', 'personnelImg/default_img.jpg', '68688e820bd6e.jpg', 'GUINTOS', 'WINSTON', 'NAVA', '', 3, 3, '07/05/2025', '10:31 AM', 37888, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1453, 'KDV12JKLNR', 'personnelImg/default_img.jpg', '68688e8a09458.jpg', 'TRINIO-SANTES', 'VANESSA', 'LIRAZAN', '', 3, 3, '07/05/2025', '10:31 AM', 37896, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1454, 'GOYLQLNANM', 'personnelImg/default_img.jpg', '68688ea20928f.jpg', 'DELOTINA', 'DANILO', 'NAPIERE', '', 17, 3, '07/05/2025', '10:32 AM', 37920, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1455, 'T4BXLLYWTG', 'personnelImg/default_img.jpg', '68688ea708908.jpg', 'DERIT', 'JOSE ALAN ', 'DURO', '', 17, 3, '07/05/2025', '10:32 AM', 37925, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1456, 'H05GELOMZN', 'personnelImg/default_img.jpg', '68688eaa08306.jpg', 'DEQUINA', 'REYNOLD', 'B.', '', 17, 3, '07/05/2025', '10:32 AM', 37928, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1457, 'QQZIGTSWBI', 'personnelImg/default_img.jpg', '68688eb10d47d.jpg', 'NACION', 'ELBRED', 'S.', '', 17, 3, '07/05/2025', '10:32 AM', 37935, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1458, 'K0GBVLRIOM', 'personnelImg/default_img.jpg', '68688eb70cccf.jpg', 'JORDAN', 'MARK ANTHONY', 'TOMADO', '', 3, 3, '07/05/2025', '10:32 AM', 37941, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1459, 'R52SQSFJB4', 'personnelImg/default_img.jpg', '68688ebc0da3b.jpg', 'BONILLA', 'JURY', 'BENLOT', '', 17, 3, '07/05/2025', '10:32 AM', 37946, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1460, 'HR1TD1EOO6', 'personnelImg/default_img.jpg', '68688ecb096b5.jpg', 'NATALIO', 'JOEFREY', 'TEMBREVILLA', '', 9, 3, '07/05/2025', '10:32 AM', 37961, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1461, 'N0PELGBXHV', 'personnelImg/default_img.jpg', '68688ed00b000.jpg', 'GUSTILO', 'LEILANI', 'DECENA', '', 9, 3, '07/05/2025', '10:32 AM', 37966, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1462, '2XYVUZG44K', 'personnelImg/default_img.jpg', '68688ed708155.jpg', 'RELIQUIAS', 'JOERIBEL', 'TORIANO', '', 9, 3, '07/05/2025', '10:32 AM', 37973, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1463, 'C4XW3NQ3CE', 'personnelImg/default_img.jpg', '68688edb0e32b.jpg', 'ACADEMIA', 'RIZALYN', 'CAYANAN', '', 9, 3, '07/05/2025', '10:32 AM', 37977, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1464, '2KLYB0WHDY', 'personnelImg/default_img.jpg', '68688eea09226.jpg', 'GESTOSO', 'CARLITO', 'BAVIERA', '', 10, 3, '07/05/2025', '10:33 AM', 37992, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1465, '0RVK3VBCZ1', 'personnelImg/default_img.jpg', '68688ef006bb4.jpg', 'MANOS', 'ANNABELLE', 'PERIGUA', '', 5, 3, '07/05/2025', '10:33 AM', 37998, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1466, 'F255HSX3FM', 'personnelImg/default_img.jpg', '68688ef7085ab.jpg', 'GUINTOS', 'ERICA', 'MANANGAN', '', 14, 3, '07/05/2025', '10:33 AM', 38005, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1467, 'O6JWSQ5BTR', 'personnelImg/default_img.jpg', '68688eff08da1.jpg', 'DELA CONCEPTION', 'IMELDA', 'ESPENORIO', '', 14, 3, '07/05/2025', '10:33 AM', 38013, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1468, 'CEPHX3JOM0', 'personnelImg/default_img.jpg', '68688f04079c0.jpg', 'TIBAYDE', 'IRENE', 'GIGANAN', '', 10, 3, '07/05/2025', '10:33 AM', 38018, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1469, 'ES1MY5CB5N', 'personnelImg/default_img.jpg', '68688f0d0c47e.jpg', 'BUDACA', 'REVINIA', 'AMACIO', '', 10, 3, '07/05/2025', '10:33 AM', 38027, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1470, 'KVKMFQDZDV', 'personnelImg/default_img.jpg', '68688f120948c.jpg', 'GA-AN', 'LENLY', 'NOSAL', '', 10, 3, '07/05/2025', '10:33 AM', 38032, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1471, 'OSYDS2VNEW', 'personnelImg/default_img.jpg', '68688f1d0b8aa.jpg', 'ABALLE', 'BETHEL', 'PERFUMA', '', 10, 3, '07/05/2025', '10:34 AM', 38043, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1472, 'T0SO3GEQZX', 'personnelImg/default_img.jpg', '68688f2e0b5c1.jpg', 'TUPAS', 'ANTHONY', 'L.', '', 11, 3, '07/05/2025', '10:34 AM', 38060, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1473, '0M1YGUSYJR', 'personnelImg/default_img.jpg', '68688f3409b73.jpg', 'BALOYO', 'HANSEL', 'M.', '', 11, 3, '07/05/2025', '10:34 AM', 38066, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1474, '5Z5WKCHQGF', 'personnelImg/default_img.jpg', '68688f3c08106.jpg', 'DOLOR', 'ROLY', 'D.', '', 11, 3, '07/05/2025', '10:34 AM', 38074, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1475, 'XD5JB3HCC0', 'personnelImg/default_img.jpg', '68688f4008af2.jpg', 'LAREÑO', 'LIWAYA', 'MAHINAY', '', 11, 3, '07/05/2025', '10:34 AM', 38078, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1476, '1YJTKOZ61A', 'personnelImg/default_img.jpg', '68688f45096b9.jpg', 'DURAN', 'MICHELLE', 'M.', '', 11, 3, '07/05/2025', '10:34 AM', 38083, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1477, 'ZJPTEJP5UQ', 'personnelImg/default_img.jpg', '68688f4a08a57.jpg', 'AMANTE', 'LEONIZA', 'FLORES', '', 11, 3, '07/05/2025', '10:34 AM', 38088, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1478, 'FKX31XYW34', 'personnelImg/default_img.jpg', '68688f4f0acd6.jpg', 'DELA FUENTE', 'ALVIN', 'ORMEO', '', 11, 3, '07/05/2025', '10:34 AM', 38093, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1479, 'BLJQQ3XW3K', 'personnelImg/default_img.jpg', '68688f560a8d9.jpg', 'NORVIE', 'JOSECO', 'A.', '', 11, 3, '07/05/2025', '10:35 AM', 38100, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1480, 'RVR5BBU5ZV', 'personnelImg/default_img.jpg', '68688f5e096cc.jpg', 'CANA', 'EVA MAE', 'G.', '', 11, 3, '07/05/2025', '10:35 AM', 38108, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1481, 'I0FY3RFXM5', 'personnelImg/default_img.jpg', '68688f6c06e6f.jpg', 'TILOS', 'OSCAR', 'VILLARETE', '', 4, 3, '07/05/2025', '10:35 AM', 38122, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1482, 'CCEWWUW3P2', 'personnelImg/default_img.jpg', '68688f730a552.jpg', 'TUBILLEJA', 'THEODORE', 'SIGUEZA', 'JR. ', 6, 3, '07/05/2025', '10:35 AM', 38129, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1483, 'M1RF4GW0TT', 'personnelImg/default_img.jpg', '68688f760a649.jpg', 'GUINTOS', 'MA. ELENA', 'N.', '', 14, 3, '07/05/2025', '10:35 AM', 38132, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1484, 'T1X1EOAO50', 'personnelImg/default_img.jpg', '68688f7c06fc0.jpg', 'HERRADURA', 'GINALYN ', 'OBENZA', '', 2, 3, '07/05/2025', '10:35 AM', 38138, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1485, 'YFUCRYEDIV', 'personnelImg/yfucryediv-jetro.png', '68688f800a0fa.jpg', 'BARROCA', 'JETTER', 'SENIO', '', 14, 3, '07/05/2025', '10:35 AM', 38143, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1486, 'SJLE6GGLD6', 'personnelImg/nrf04z55-che.jpg', '68688f8809d59.jpg', 'JUAREZ', 'CHERRY LYNN', 'MONTAÑO', '', 2, 3, '07/05/2025', '10:35 AM', 38150, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1487, 'MF4R0LP0PT', 'personnelImg/default_img.jpg', '68688fb0087a2.jpg', 'TOLEDO', 'RAYMUND ANTHONY', 'INOLINO', '', 4, 3, '07/05/2025', '10:36 AM', 38190, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1488, '2SOQJG3P6F', 'personnelImg/default_img.jpg', '68688fb408fa9.jpg', 'SANTES', 'CINDY', 'REBOLDAL', '', 4, 3, '07/05/2025', '10:36 AM', 38194, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1489, 'LTQHD2BPYJ', 'personnelImg/default_img.jpg', '68688fb808140.jpg', 'GALON', 'RANDOLF', 'BLANCO', '', 4, 3, '07/05/2025', '10:36 AM', 38198, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1490, '111LXWXFLY', 'personnelImg/default_img.jpg', '68688fc90b3d8.jpg', 'NICOR', 'MICHAEL', 'RAMOS', '', 7, 3, '07/05/2025', '10:36 AM', 38215, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1491, '2SJBOJTXPC', 'personnelImg/default_img.jpg', '68688fd00c42b.jpg', 'GELLECANAO', 'MURIELLE', 'LIRAZAN', '', 7, 3, '07/05/2025', '10:37 AM', 38222, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1492, 'PWU0GP4SHN', 'personnelImg/default_img.jpg', '68688fdb0922e.jpg', 'SANTES', 'AZELA', 'FLORES', '', 7, 3, '07/05/2025', '10:37 AM', 38233, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1493, 'Q0QB3ZVYNK', 'personnelImg/default_img.jpg', '68688ff409040.jpg', 'VILLARETE', 'ANGELA', 'TELONIO', '', 7, 3, '07/05/2025', '10:37 AM', 38258, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1494, 'XFSKZYRVSI', 'personnelImg/default_img.jpg', '68688ff908cdf.jpg', 'LASTRILLA', 'GUIDRALYN', 'P.', '', 7, 3, '07/05/2025', '10:37 AM', 38263, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1495, 'Q0BSK1IGMH', 'personnelImg/default_img.jpg', '68688fff09493.jpg', 'BARIQUIT', 'PRACEDES', 'CABONILAS', '', 7, 3, '07/05/2025', '10:37 AM', 38269, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1496, 'UUR4C35PLC', 'personnelImg/default_img.jpg', '686890070ad9d.jpg', 'ALIMANE', 'JINKY', 'GALPO', '', 7, 3, '07/05/2025', '10:37 AM', 38277, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1497, 'N0AXWJPKNQ', 'personnelImg/default_img.jpg', '6868900b0ac91.jpg', 'SORONGON', 'NITA', 'A.', '', 7, 3, '07/05/2025', '10:38 AM', 38281, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1498, '1DSSFVBIFF', 'personnelImg/default_img.jpg', '6868900e0d840.jpg', 'MAESTRECAMPO', 'EVELYN', 'ELARMO', '', 7, 3, '07/05/2025', '10:38 AM', 38284, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1499, 'CMDUXPZ44I', 'personnelImg/default_img.jpg', '686890200b808.jpg', 'GONZAL', 'SANDRA', 'CASTILLO', '', 8, 3, '07/05/2025', '10:38 AM', 38302, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1500, 'N4DZR4BBY0', 'personnelImg/default_img.jpg', '686890250a38e.jpg', 'RELIQUIAS', 'MARIA FE', 'GUINTOS', '', 8, 3, '07/05/2025', '10:38 AM', 38307, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1501, 'JETERWW23U', 'personnelImg/nrf5ihz6-mae-flor.jpg', '6868902909482.jpg', 'BARRIOS', 'MAE FLOR', 'ALMAIZ', '', 8, 3, '07/05/2025', '10:38 AM', 38311, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1502, '5BJ6IAN1FB', 'personnelImg/default_img.jpg', '686890300b976.jpg', 'ORBIGOSO', 'ELIZABETH', 'SENIO', '', 8, 3, '07/05/2025', '10:38 AM', 38318, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1503, '6DZNSFDRJG', 'personnelImg/default_img.jpg', '6868903f09abc.jpg', 'MANOS', 'TEOFILO', 'ENCARGUEZ', 'JR. ', 24, 3, '07/05/2025', '10:38 AM', 38333, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1504, '1QETKAFQWO', 'personnelImg/default_img.jpg', '6868904509da8.jpg', 'DELOTINA', 'JOFEL', 'EVANGELISTA', '', 24, 3, '07/05/2025', '10:38 AM', 38339, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1505, 'BSY3X4CY3E', 'personnelImg/default_img.jpg', '6868905309093.jpg', 'SANTES', 'JOCELYN', 'CORONEL', '', 24, 3, '07/05/2025', '10:39 AM', 38353, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1506, 'ZGMTZ2Y3BT', 'personnelImg/default_img.jpg', '686890660734b.jpg', 'OCTAVIO', 'PETER JOHN ', 'LAZALITA', '', 24, 3, '07/05/2025', '10:39 AM', 38372, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1507, '2FUFQBHUQM', 'personnelImg/default_img.jpg', '686890720ac56.jpg', 'DECENA', 'LANNE', 'VILLARETE', '', 24, 3, '07/05/2025', '10:39 AM', 38384, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1508, 'DCORCD1ICP', 'personnelImg/default_img.jpg', '686890770a6ed.jpg', 'TUMA-OB', 'LESLIE AIKEE DYAN', 'GIGANAN', '', 24, 3, '07/05/2025', '10:39 AM', 38389, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1509, 'N3CFWPHFQX', 'personnelImg/default_img.jpg', '686890900a052.jpg', 'SANTES', 'CAROL ANN', 'TILOS', '', 5, 3, '07/05/2025', '10:40 AM', 38414, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1510, 'DO2X14YKJR', 'personnelImg/default_img.jpg', '686890950a46f.jpg', 'VILLARUBIA', 'ALTHEA', 'PIA', '', 5, 3, '07/05/2025', '10:40 AM', 38419, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1511, 'RK02GLHBHR', 'personnelImg/default_img.jpg', '686890a509d2d.jpg', 'PINONGGAN', 'JOSEPH', 'ALBERIO', '', 2, 3, '07/05/2025', '10:40 AM', 38435, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1512, 'D4ZWVFKO1E', 'personnelImg/default_img.jpg', '686890aa0a8f6.jpg', 'LLAMADO', 'MARISSA', 'MAQUILING', '', 2, 3, '07/05/2025', '10:40 AM', 38440, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1513, 'KHA2LUXNL1', 'personnelImg/default_img.jpg', '686890b10f1b5.jpg', 'BA-AL', 'VON MARVIN', 'M.', '', 2, 3, '07/05/2025', '10:40 AM', 38447, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1514, 'N2CS52WG6G', 'personnelImg/default_img.jpg', '686890b409b95.jpg', 'GIGANAN', 'GRACE JOY', 'ESTRAO', '', 2, 3, '07/05/2025', '10:40 AM', 38450, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1515, 'KP6G24TB40', 'personnelImg/default_img.jpg', '686890b90a974.jpg', 'RELADO', 'MEDALIA', 'VALENZUELA', '', 8, 3, '07/05/2025', '10:40 AM', 38455, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1516, 'M1Y5ETAYD6', 'personnelImg/default_img.jpg', '686890fb0cef6.jpg', 'NAVA', 'LUZ SALOME', 'VILLA', '', 14, 3, '07/05/2025', '10:42 AM', 38521, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1517, '6D0YWDKLPP', 'personnelImg/default_img.jpg', '686890ff09f3c.jpg', 'AGUHAYON', 'RICARDO', 'UBAMOS', '', 14, 3, '07/05/2025', '10:42 AM', 38525, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1518, 'OINBKY4BGA', 'personnelImg/default_img.jpg', '68689107094dd.jpg', 'DECATORIA', 'JOEBERT', 'SARIL', '', 3, 3, '07/05/2025', '10:42 AM', 38533, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1519, 'WDB3J415ZP', 'personnelImg/default_img.jpg', '6868910b0ad8a.jpg', 'AKOL', 'SHARRA MAE', 'ARTICA', '', 10, 3, '07/05/2025', '10:42 AM', 38537, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1520, '4RE6HDPT3R', 'personnelImg/default_img.jpg', '686891100b0da.jpg', 'JIMENEZ', 'LEO', 'TOMILBA', '', 14, 3, '07/05/2025', '10:42 AM', 38542, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1521, 'GSQZ2OCVHQ', 'personnelImg/default_img.jpg', '686891150e06d.jpg', 'RELIQUIAS', 'GERALD', 'TORIANO', '', 14, 3, '07/05/2025', '10:42 AM', 38547, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1522, 'FU6VB3OYK4', 'personnelImg/default_img.jpg', '686891250a3e2.jpg', 'MANGOGTONG', 'WILMAR', 'ESTRELLA', '', 14, 3, '07/05/2025', '10:42 AM', 38563, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1523, '2CKQGJE4K6', 'personnelImg/default_img.jpg', '686891290804c.jpg', 'DELLOSO', 'CHRISTOPHER', 'M.', '', 14, 3, '07/05/2025', '10:42 AM', 38567, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1524, 'YCSOHXN5TA', 'personnelImg/default_img.jpg', '6868913c0b6cd.jpg', 'GALLARDA', 'ELNOR', 'PACLAONA', '', 12, 3, '07/05/2025', '10:43 AM', 38586, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1525, 'W2DGEUTP62', 'personnelImg/default_img.jpg', '686891420d265.jpg', 'LOREDO', 'MARIA ELENA', 'DELLOSO', '', 14, 3, '07/05/2025', '10:43 AM', 38593, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1526, 'HDMCPVKYB1', 'personnelImg/default_img.jpg', '686891480865e.jpg', 'ROXAS', 'MARY ANN', 'VILLAFUERTE', '', 12, 3, '07/05/2025', '10:43 AM', 38598, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1527, 'MS5LRO1IJE', 'personnelImg/default_img.jpg', '686891580c597.jpg', 'VILLANUEVA', 'ROSALIE', 'ELARDO', '', 12, 3, '07/05/2025', '10:43 AM', 38614, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1528, 'WSJIHMXEN6', 'personnelImg/default_img.jpg', '6868915e09c36.jpg', 'TILOS', 'MARCELINA', 'CELIS', '', 12, 3, '07/05/2025', '10:43 AM', 38620, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1529, '6ON2RWCQEM', 'personnelImg/default_img.jpg', '686891630cc04.jpg', 'GAREZA', 'MELANIE', 'CELIS', '', 12, 3, '07/05/2025', '10:43 AM', 38625, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1530, 'PMOLWNYZZA', 'personnelImg/default_img.jpg', '686891680c1c8.jpg', 'SUSANA', 'FREDERICK DAVY', 'PINGCALE', '', 12, 3, '07/05/2025', '10:43 AM', 38630, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1531, 'LUPQ4RIQUJ', 'personnelImg/default_img.jpg', '6868916c0c2b9.jpg', 'TILOS', 'RIZALIE', 'CELIZ', '', 12, 3, '07/05/2025', '10:43 AM', 38634, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1532, 'ZXDNHZFPMW', 'personnelImg/default_img.jpg', '686891720a4c5.jpg', 'PUBLICO', 'NOEMI', 'TOMADO', '', 12, 3, '07/05/2025', '10:44 AM', 38640, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1533, 'V2M0IIAYRM', 'personnelImg/default_img.jpg', '6868917608018.jpg', 'VILLANUEVA', 'RAYGILDA', 'VERGARA', '', 12, 3, '07/05/2025', '10:44 AM', 38644, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1534, 'DFR5QGBYHW', 'personnelImg/default_img.jpg', '6868917b07fe6.jpg', 'BARO', 'PHOEBE', 'MANILINGAN', '', 12, 3, '07/05/2025', '10:44 AM', 38649, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1535, 'MHDZI160KN', 'personnelImg/default_img.jpg', '6868918009d1a.jpg', 'TEMBREVILLA', 'CAROLINE', 'CANILLO', '', 12, 3, '07/05/2025', '10:44 AM', 38654, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1536, 'QESM444ZK0', 'personnelImg/default_img.jpg', '686891850b5eb.jpg', 'TEMBREVILLA', 'RONA', 'ESCOSAR', '', 12, 3, '07/05/2025', '10:44 AM', 38659, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1537, 'ZV1EXSSS30', 'personnelImg/default_img.jpg', '6868918c0808c.jpg', 'BONGCAWEL', 'GENELYN ', 'HISONA', '', 12, 3, '07/05/2025', '10:44 AM', 38666, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1538, 'GJCC6XY3BR', 'personnelImg/default_img.jpg', '68689192080f2.jpg', 'ANTIQUEÑO', 'ANA MARIE', 'SANOY', '', 12, 3, '07/05/2025', '10:44 AM', 38672, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1539, 'GZ5LO0QDRC', 'personnelImg/default_img.jpg', '686891990a66e.jpg', 'GARCIA', 'ADELA', 'ANTOLIN', '', 12, 3, '07/05/2025', '10:44 AM', 38680, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1540, 'LVX5C46TAS', 'personnelImg/default_img.jpg', '6868919d099f1.jpg', 'SILVESTRE', 'CYNTHIA', 'LAMBOT', '', 12, 3, '07/05/2025', '10:44 AM', 38683, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1541, 'GP4Q0NOWGA', 'personnelImg/default_img.jpg', '686891a20cae9.jpg', 'GERMINAL', 'CRISTUTO', 'M.', '', 17, 3, '07/05/2025', '10:44 AM', 38688, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1542, 'HSDQZ6G6PA', 'personnelImg/default_img.jpg', '686891a50cb94.jpg', 'PEREZ', 'JOSE MARIA', 'LIM', 'JR. ', 12, 3, '07/05/2025', '10:44 AM', 38691, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1543, 'HEPAB6X3PL', 'personnelImg/default_img.jpg', '686891ab0c395.jpg', 'VILLAMATER', 'LORALYN', 'BARROCA', '', 12, 3, '07/05/2025', '10:44 AM', 38697, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1544, 'RCCO5TPACC', 'personnelImg/default_img.jpg', '686891b009110.jpg', 'PIMENTEL', 'EMY', 'MAGBANUA', '', 12, 3, '07/05/2025', '10:45 AM', 38702, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1545, '5UVF66ZPY0', 'personnelImg/default_img.jpg', '686891b60aa26.jpg', 'MAQUILING', 'WARREN', 'RAMIREZ', '', 12, 3, '07/05/2025', '10:45 AM', 38708, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1546, 'I03I6VZELI', 'personnelImg/default_img.jpg', '686891ba07fe0.jpg', 'GALAN', 'MARY JANE', 'CELIS', '', 12, 3, '07/05/2025', '10:45 AM', 38712, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1547, 'G6A2OEKXUH', 'personnelImg/default_img.jpg', '686891bd09743.jpg', 'MANGILIMUTAN', 'MA. HEARTY', 'CAÑA', '', 12, 3, '07/05/2025', '10:45 AM', 38715, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1548, 'OIMV5KOLF2', 'personnelImg/default_img.jpg', '686891c20975c.jpg', 'PEROSIA', 'GLORY MAE', 'TUNDA-AN', '', 12, 3, '07/05/2025', '10:45 AM', 38721, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1549, 'ITZ6XK1TGX', 'personnelImg/default_img.jpg', '686891d507732.jpg', 'LUCENARA', 'ROBIJID', 'Q', '', 15, 3, '07/05/2025', '10:45 AM', 38739, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1550, 'WILZVWBBRI', 'personnelImg/default_img.jpg', '686891da07f63.jpg', 'OCCEÑA', 'GEM', 'RELAMPAGOS', '', 3, 3, '07/05/2025', '10:45 AM', 38744, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1551, 'DV6HSR4T31', 'personnelImg/default_img.jpg', '686891de079c7.jpg', 'SABOBO', 'ALFREDO', 'G.', '', 15, 3, '07/05/2025', '10:45 AM', 38748, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1552, 'REPYPK1N0K', 'personnelImg/default_img.jpg', '686891e30d082.jpg', 'AMBAGAN', 'EDSEL', 'MILLENDEZ', '', 16, 3, '07/05/2025', '10:45 AM', 38753, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1553, 'GRN63LXTVC', 'personnelImg/default_img.jpg', '686891e80f573.jpg', 'GIGANAN', 'AGNES', 'NAVA', '', 15, 3, '07/05/2025', '10:45 AM', 38758, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1554, 'KUNUEE1ZKH', 'personnelImg/default_img.jpg', '686891fb09268.jpg', 'YUSAY', 'JOSE', 'TOGLE', 'III ', 16, 3, '07/05/2025', '10:46 AM', 38777, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1555, 'POT5DLCLXZ', 'personnelImg/default_img.jpg', '686892010ad95.jpg', 'GUINTOS', 'TINA MARIE', 'LUGA', '', 16, 3, '07/05/2025', '10:46 AM', 38783, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1556, 'F3DUWOJ4SZ', 'personnelImg/default_img.jpg', '68689205076b8.jpg', 'SIASON', 'CHEZAH ERL', 'GAUAL', '', 16, 3, '07/05/2025', '10:46 AM', 38787, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1557, 'B42WBNMNSK', 'personnelImg/default_img.jpg', '686892170a104.jpg', 'LIMSIACO', 'ARIANE', 'CONSTANTINO', '', 6, 3, '07/05/2025', '10:46 AM', 38805, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1558, 'V4PAJMJ5YO', 'personnelImg/default_img.jpg', '6868921e09116.jpg', 'TUPAS', 'OFELIA', 'TEMBREVILLA', '', 6, 3, '07/05/2025', '10:46 AM', 38812, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1559, 'UBFPHM020G', 'personnelImg/default_img.jpg', '686892230b27f.jpg', 'BONILLA', 'ANNI VER', 'PAMLIEGA', '', 6, 3, '07/05/2025', '10:46 AM', 38817, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1560, 'EAUNV5WVM0', 'personnelImg/default_img.jpg', '6868922a0e066.jpg', 'BARROCA', 'JORGIE', 'PERFUMA', '', 6, 3, '07/05/2025', '10:47 AM', 38824, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1561, 'EHEYDZ1UYN', 'personnelImg/default_img.jpg', '6868922f0b6c1.jpg', 'ALATON', 'GINA', 'NABOR', '', 6, 3, '07/05/2025', '10:47 AM', 38829, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1562, 'DGPF5FDK53', 'personnelImg/default_img.jpg', '686892330ae87.jpg', 'ARANETA', 'JOSELITA', 'GENTELIZO', '', 6, 3, '07/05/2025', '10:47 AM', 38833, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1563, 'DURSCXCSIM', 'personnelImg/default_img.jpg', '686892370d7e8.jpg', 'MAYANDIA', 'ANALIE', 'ELARMO', '', 6, 3, '07/05/2025', '10:47 AM', 38837, 'on', 'AM OUT', '192.168.24.94', '', '', 0),
(1566, 'CMDUXPZ44I', 'personnelImg/default_img.jpg', '6868ea47939bc.jpg', 'GONZAL', 'SANDRA', 'CASTILLO', '', 8, 3, '07/05/2025', '05:03 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1567, 'EAUNV5WVM0', 'personnelImg/default_img.jpg', '6868ea7d8cd29.jpg', 'BARROCA', 'JORGIE', 'PERFUMA', '', 6, 3, '07/05/2025', '05:03 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1568, 'N0PELGBXHV', 'personnelImg/default_img.jpg', '6868ea878dbb9.jpg', 'GUSTILO', 'LEILANI', 'DECENA', '', 9, 3, '07/05/2025', '05:04 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1569, '6D0YWDKLPP', 'personnelImg/default_img.jpg', '6868eab68e8ea.jpg', 'AGUHAYON', 'RICARDO', 'UBAMOS', '', 14, 3, '07/05/2025', '05:04 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1570, 'OSYDS2VNEW', 'personnelImg/default_img.jpg', '6868eaea8ee0b.jpg', 'ABALLE', 'BETHEL', 'PERFUMA', '', 10, 3, '07/05/2025', '05:05 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1571, 'KVKMFQDZDV', 'personnelImg/default_img.jpg', '6868eaf28fd35.jpg', 'GA-AN', 'LENLY', 'NOSAL', '', 10, 3, '07/05/2025', '05:05 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1572, 'CEPHX3JOM0', 'personnelImg/default_img.jpg', '6868eaf696053.jpg', 'TIBAYDE', 'IRENE', 'GIGANAN', '', 10, 3, '07/05/2025', '05:05 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1573, 'O6JWSQ5BTR', 'personnelImg/default_img.jpg', '6868eafd8bfb1.jpg', 'DELA CONCEPTION', 'IMELDA', 'ESPENORIO', '', 14, 3, '07/05/2025', '05:06 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1574, 'DGPF5FDK53', 'personnelImg/default_img.jpg', '6868eb258fc9c.jpg', 'ARANETA', 'JOSELITA', 'GENTELIZO', '', 6, 3, '07/05/2025', '05:06 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1575, 'Q0QB3ZVYNK', 'personnelImg/default_img.jpg', '6868eb438c88a.jpg', 'VILLARETE', 'ANGELA', 'TELONIO', '', 7, 3, '07/05/2025', '05:07 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1576, 'FU6VB3OYK4', 'personnelImg/default_img.jpg', '6868ebaf8bf3b.jpg', 'MANGOGTONG', 'WILMAR', 'ESTRELLA', '', 14, 3, '07/05/2025', '05:09 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1577, '1DSSFVBIFF', 'personnelImg/default_img.jpg', '6868ec0e8e06d.jpg', 'MAESTRECAMPO', 'EVELYN', 'ELARMO', '', 7, 3, '07/05/2025', '05:10 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1578, 'N2CS52WG6G', 'personnelImg/default_img.jpg', '6868ec8e8ea1e.jpg', 'GIGANAN', 'GRACE JOY', 'ESTRAO', '', 2, 3, '07/05/2025', '05:12 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1579, '0RVK3VBCZ1', 'personnelImg/default_img.jpg', '6868ecc48efdb.jpg', 'MANOS', 'ANNABELLE', 'PERIGUA', '', 5, 3, '07/05/2025', '05:13 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1580, 'UUR4C35PLC', 'personnelImg/default_img.jpg', '6868ed048c223.jpg', 'ALIMANE', 'JINKY', 'GALPO', '', 7, 3, '07/05/2025', '05:14 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1581, 'MS5LRO1IJE', 'personnelImg/default_img.jpg', '6868ed398d5d2.jpg', 'VILLANUEVA', 'ROSALIE', 'ELARDO', '', 12, 3, '07/05/2025', '05:15 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1582, 'HR1TD1EOO6', 'personnelImg/default_img.jpg', '6868ed718e9c1.jpg', 'NATALIO', 'JOEFREY', 'TEMBREVILLA', '', 9, 3, '07/05/2025', '05:16 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1583, '6KR3ZM4BU3', 'personnelImg/default_img.jpg', '6868ed908b7f3.jpg', 'TELONIO', 'JAMES ANDREW', 'GUINTOS', '', 15, 3, '07/05/2025', '05:17 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1584, 'WILZVWBBRI', 'personnelImg/default_img.jpg', '6868edbb8bc6b.jpg', 'OCCEÑA', 'GEM', 'RELAMPAGOS', '', 3, 3, '07/05/2025', '05:17 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1585, '0LKJLM2C22', 'personnelImg/default_img.jpg', '6868edcb8f862.jpg', 'MANGILIMUTAN', 'LORRAINE MAE', 'GESTOSO', '', 2, 3, '07/05/2025', '05:18 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1586, '65VQESUT03', 'personnelImg/default_img.jpg', '6868ee058cd6c.jpg', 'PANGANTIHON', 'JOHN IRVING', 'TOLEDO', '', 3, 3, '07/05/2025', '05:19 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1587, 'XFSKZYRVSI', 'personnelImg/default_img.jpg', '6868ee218b496.jpg', 'LASTRILLA', 'GUIDRALYN', 'P.', '', 7, 3, '07/05/2025', '05:19 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1588, 'KUNUEE1ZKH', 'personnelImg/default_img.jpg', '6868ee698e251.jpg', 'YUSAY', 'JOSE', 'TOGLE', 'III ', 16, 3, '07/05/2025', '05:20 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1589, 'SJLE6GGLD6', 'personnelImg/nrf04z55-che.jpg', '6868ee878e323.jpg', 'JUAREZ', 'CHERRY LYNN', 'MONTAÑO', '', 2, 3, '07/05/2025', '05:21 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1590, 'T1X1EOAO50', 'personnelImg/default_img.jpg', '6868ee928e96e.jpg', 'HERRADURA', 'GINALYN ', 'OBENZA', '', 2, 3, '07/05/2025', '05:21 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1591, 'D4ZWVFKO1E', 'personnelImg/default_img.jpg', '6868eef18f364.jpg', 'LLAMADO', 'MARISSA', 'MAQUILING', '', 2, 3, '07/05/2025', '05:22 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1592, 'JETERWW23U', 'personnelImg/nrf5ihz6-mae-flor.jpg', '6868eef78d8fd.jpg', 'BARRIOS', 'MAE FLOR', 'ALMAIZ', '', 8, 3, '07/05/2025', '05:23 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1593, 'VUM5AWYEUH', 'personnelImg/default_img.jpg', '6868ef7e8c7fd.jpg', 'SUMUGAT', 'JENELYN', 'TELONIO', '', 3, 3, '07/05/2025', '05:25 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1594, 'ES1MY5CB5N', 'personnelImg/default_img.jpg', '6868efc594375.jpg', 'BUDACA', 'REVINIA', 'AMACIO', '', 10, 3, '07/05/2025', '05:26 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1595, '2KLYB0WHDY', 'personnelImg/default_img.jpg', '6868efce8f263.jpg', 'GESTOSO', 'CARLITO', 'BAVIERA', '', 10, 3, '07/05/2025', '05:26 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1596, 'Q0BSK1IGMH', 'personnelImg/default_img.jpg', '6868f0278eed0.jpg', 'BARIQUIT', 'PRACEDES', 'CABONILAS', '', 7, 3, '07/05/2025', '05:28 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1597, 'PWU0GP4SHN', 'personnelImg/default_img.jpg', '6868f0348e4e7.jpg', 'SANTES', 'AZELA', 'FLORES', '', 7, 3, '07/05/2025', '05:28 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1598, '2CKQGJE4K6', 'personnelImg/default_img.jpg', '6868f0518b4c2.jpg', 'DELLOSO', 'CHRISTOPHER', 'M.', '', 14, 3, '07/05/2025', '05:28 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1599, 'GSQZ2OCVHQ', 'personnelImg/default_img.jpg', '6868f05b8eb72.jpg', 'RELIQUIAS', 'GERALD', 'TORIANO', '', 14, 3, '07/05/2025', '05:28 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1600, 'M1RF4GW0TT', 'personnelImg/default_img.jpg', '6868f0bb8e75b.jpg', 'GUINTOS', 'MA. ELENA', 'N.', '', 14, 3, '07/05/2025', '05:30 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1601, 'N0AXWJPKNQ', 'personnelImg/default_img.jpg', '6868f12c8e52f.jpg', 'SORONGON', 'NITA', 'A.', '', 7, 3, '07/05/2025', '05:32 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1602, 'F255HSX3FM', 'personnelImg/default_img.jpg', '6868f1e38ee83.jpg', 'GUINTOS', 'ERICA', 'MANANGAN', '', 14, 3, '07/05/2025', '05:35 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1603, 'F3DUWOJ4SZ', 'personnelImg/default_img.jpg', '6868f26f8ff73.jpg', 'SIASON', 'CHEZAH ERL', 'GAUAL', '', 16, 3, '07/05/2025', '05:37 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1604, 'POT5DLCLXZ', 'personnelImg/default_img.jpg', '6868f2798df87.jpg', 'GUINTOS', 'TINA MARIE', 'LUGA', '', 16, 3, '07/05/2025', '05:38 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1605, 'ZHXY3CO3KQ', 'personnelImg/default_img.jpg', '6868f2838cc2c.jpg', 'DELA FUENTE', 'EBER', 'ORMEO', '', 12, 3, '07/05/2025', '05:38 PM', 63490, 'off', 'AM OUT', '192.168.24.94', '', '', 0),
(1606, 'WDB3J415ZP', 'personnelImg/default_img.jpg', '6868f2ae8c487.jpg', 'AKOL', 'SHARRA MAE', 'ARTICA', '', 10, 3, '07/05/2025', '05:38 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1607, 'T0SO3GEQZX', 'personnelImg/default_img.jpg', '6868f4348f184.jpg', 'TUPAS', 'ANTHONY', 'L.', '', 11, 3, '07/05/2025', '05:45 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1608, 'MHDZI160KN', 'personnelImg/default_img.jpg', '6868f48a8b8da.jpg', 'TEMBREVILLA', 'CAROLINE', 'CANILLO', '', 12, 3, '07/05/2025', '05:46 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1609, 'UBFPHM020G', 'personnelImg/default_img.jpg', '6868f55e8d58e.jpg', 'BONILLA', 'ANNI VER', 'PAMLIEGA', '', 6, 3, '07/05/2025', '05:50 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1610, 'XD5JB3HCC0', 'personnelImg/default_img.jpg', '6868f7a79209b.jpg', 'LAREÑO', 'LIWAYA', 'MAHINAY', '', 11, 3, '07/05/2025', '06:00 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1611, '0M1YGUSYJR', 'personnelImg/default_img.jpg', '6868f8478f27d.jpg', 'BALOYO', 'HANSEL', 'M.', '', 11, 3, '07/05/2025', '06:02 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1612, 'BLJQQ3XW3K', 'personnelImg/default_img.jpg', '6868f8558db23.jpg', 'NORVIE', 'JOSECO', 'A.', '', 11, 3, '07/05/2025', '06:03 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1613, 'ZJPTEJP5UQ', 'personnelImg/default_img.jpg', '6868f9b58fb2f.jpg', 'AMANTE', 'LEONIZA', 'FLORES', '', 11, 3, '07/05/2025', '06:08 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1614, 'RVR5BBU5ZV', 'personnelImg/default_img.jpg', '6868f9bf8be81.jpg', 'CANA', 'EVA MAE', 'G.', '', 11, 3, '07/05/2025', '06:09 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1615, 'R0VM3TX265', 'personnelImg/default_img.jpg', '6868fa798f0b7.jpg', 'MONTANO', 'EVALYN', 'C.', '', 24, 3, '07/05/2025', '06:12 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0);
INSERT INTO `personnel_logs` (`log_id`, `RFTag_id`, `img`, `captured_img`, `lname`, `fname`, `mname`, `suffix`, `do_id`, `shift_id`, `logDate`, `logTime`, `logTime_sec`, `late_status`, `logFlow`, `client_ip`, `remarks`, `travel_leave_code`, `ref_log_id`) VALUES
(1616, 'BSLAWXCQ2N', 'personnelImg/default_img.jpg', '6868fb4c8b9c1.jpg', 'BAYDO', 'JULITO', 'CAYAS', '', 3, 3, '07/05/2025', '06:15 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1617, 'IO1IY5L4VB', 'personnelImg/default_img.jpg', '6868fbcc8c58f.jpg', 'CORTADO', 'ROGELIO', 'ALFANTA', '', 2, 3, '07/05/2025', '06:17 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1618, 'UV0LOFMFS3', 'personnelImg/default_img.jpg', '6868fe288dbc7.jpg', 'LOGRONIO', 'JOESIFIL', 'FAJARDO', '', 2, 3, '07/05/2025', '06:27 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1619, 'EHEYDZ1UYN', 'personnelImg/default_img.jpg', '6868ffd287294.jpg', 'ALATON', 'GINA', 'NABOR', '', 6, 3, '07/05/2025', '06:34 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1620, 'LTQHD2BPYJ', 'personnelImg/default_img.jpg', '6868ffdd773b5.jpg', 'GALON', 'RANDOLF', 'BLANCO', '', 4, 3, '07/05/2025', '06:35 PM', 0, 'off', 'PM IN', '192.168.24.94', '', '', 0),
(1621, 'O2NINN551F', 'personnelImg/default_img.jpg', '686b164eb6f22.jpg', 'PARCON', 'MERCEDITA', 'T.', '', 7, 3, '07/07/2025', '08:35 AM', 0, 'on', 'AM IN', '192.168.1.4', '', '', 0),
(1622, 'O2NINN551F', 'personnelImg/default_img.jpg', '686b280909105.jpg', 'PARCON', 'MERCEDITA', 'T.', '', 7, 3, '07/07/2025', '09:51 AM', 35463, 'on', 'AM OUT', '192.168.1.4', '', '', 0),
(1623, 'EEG0NY2B5U', 'personnelImg/default_img.jpg', '686b289602546.jpg', 'ARBOIZ', 'JOHN', 'VILLARICO', '', 15, 3, '07/07/2025', '09:53 AM', 0, 'on', 'AM IN', '192.168.1.4', '', '', 0),
(1624, 'YFUCRYEDIV', 'personnelImg/yfucryediv-jetro.png', '686b40ff10579.jpg', 'BARROCA', 'JETTER', 'SENIO', '', 14, 3, '07/07/2025', '11:37 AM', 0, 'on', 'AM IN', '192.168.1.4', '', '', 0),
(1625, 'YFUCRYEDIV', 'personnelImg/yfucryediv-jetro.png', '686b40fe0b66e.jpg', 'BARROCA', 'JETTER', 'SENIO', '', 14, 3, '07/07/2025', '11:37 AM', 0, 'off', 'PM IN', '192.168.1.4', '', '', 0),
(1626, 'REPYPK1N0K', 'personnelImg/default_img.jpg', '686b712203301.jpg', 'AMBAGAN', 'EDSEL', 'MILLENDEZ', '', 16, 3, '07/07/2025', '03:02 PM', 0, 'on', 'PM IN', '192.168.1.4', '', '', 0),
(1627, 'YFUCRYEDIV', 'personnelImg/yfucryediv-jetro.png', '686ca12b55881.jpg', 'BARROCA', 'JETTER', 'SENIO', '', 14, 3, '07/08/2025', '12:40 PM', 0, 'off', 'PM IN', '192.168.1.4', '', '', 0),
(1628, 'YFUCRYEDIV', 'personnelImg/yfucryediv-jetro.png', '686ca4ac4ef08.jpg', 'BARROCA', 'JETTER', 'SENIO', '', 14, 3, '07/08/2025', '12:55 PM', 0, 'on', 'PM OUT', '192.168.1.4', '', '', 0),
(1629, 'T1X1EOAO50', 'personnelImg/default_img.jpg', '686ca734467e1.jpg', 'HERRADURA', 'GINALYN ', 'OBENZA', '', 2, 3, '07/08/2025', '01:05 PM', 0, 'off', 'PM IN', '192.168.1.4', '', '', 0),
(1630, 'YFUCRYEDIV', 'personnelImg/yfucryediv-jetro.png', '6870bacd268dc.jpg', 'BARROCA', 'JETTER', 'SENIO', '', 14, 3, '07/11/2025', '03:18 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(1631, 'SJLE6GGLD6', 'personnelImg/nrf04z55-che.jpg', '6870bcd5f3317.jpg', 'JUAREZ', 'CHERRY LYNN', 'MONTAÑO', '', 2, 3, '07/11/2025', '03:27 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(1632, 'SJLE6GGLD6', 'personnelImg/sjle6ggld6-che.jpg', '6870be34f2718.jpg', 'JUAREZ', 'CHERRY LYNN', 'MONTAÑO', '', 2, 3, '07/11/2025', '03:33 PM', 0, 'on', 'PM OUT', '192.168.1.22', '', '', 0),
(1633, 'T1X1EOAO50', 'personnelImg/default_img.jpg', '6870bf0eeeed8.jpg', 'HERRADURA', 'GINALYN ', 'OBENZA', '', 2, 3, '07/11/2025', '03:36 PM', 0, 'on', 'PM IN', '192.168.1.22', '', '', 0),
(1634, 'SJLE6GGLD6', 'personnelImg/sjle6ggld6-che.jpg', '687452ea598de.jpg', 'JUAREZ', 'CHERRY LYNN', 'MONTAÑO', '', 2, 3, '07/14/2025', '08:44 AM', 0, 'on', 'AM IN', '192.168.1.22', '', '', 0),
(1635, 'GP4Q0NOWGA', 'personnelImg/default_img.jpg', '687453ec3c1b1.jpg', 'GERMINAL', 'CRISTUTO', 'M.', '', 17, 3, '07/14/2025', '08:48 AM', 0, 'on', 'AM IN', '192.168.1.22', '', '', 0),
(1636, 'GP4Q0NOWGA', 'personnelImg/default_img.jpg', '687471013f034.jpg', 'GERMINAL', 'CRISTUTO', 'M.', '', 17, 3, '07/14/2025', '10:52 AM', 39168, 'on', 'AM OUT', '192.168.1.22', '', '', 0),
(1637, 'W2DGEUTP62', 'personnelImg/default_img.jpg', '687472bb3db13.jpg', 'LOREDO', 'MARIA ELENA', 'DELLOSO', '', 14, 3, '07/14/2025', '11:00 AM', 0, 'on', 'AM IN', '192.168.1.22', '', '', 0),
(1638, 'T1X1EOAO50', 'personnelImg/default_img.jpg', '68f9e3431c820.jpg', 'HERRADURA', 'GINALYN ', 'OBENZA', '', 2, 3, '10/23/2025', '04:11 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(1639, 'GP4Q0NOWGA', 'personnelImg/default_img.jpg', '68f9e72769e71.jpg', 'GERMINAL', 'CRISTUTO', 'M.', '', 17, 3, '10/23/2025', '04:28 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(1640, 'W2DGEUTP62', 'personnelImg/default_img.jpg', '68f9e72f65349.jpg', 'LOREDO', 'MARIA ELENA', 'DELLOSO', '', 14, 3, '10/23/2025', '04:28 PM', 0, 'on', 'PM IN', '192.168.1.10', '', '', 0),
(1641, 'YFUCRYEDIV', 'personnelImg/yfucryediv-jetro.png', '69081f24ef1c7.jpg', 'BARROCA', 'JETTER', 'SENIO', '', 14, 3, '11/03/2025', '11:18 AM', 0, 'on', 'AM IN', '26.218.55.136', '', '', 0),
(1642, 'YFUCRYEDIV', 'personnelImg/yfucryediv-jetro.png', '69082902892ed.jpg', 'BARROCA', 'JETTER', 'SENIO', '', 14, 3, '11/03/2025', '12:01 PM', 43263, 'off', 'AM OUT', '26.218.55.136', '', '', 0),
(1643, 'YFUCRYEDIV', 'personnelImg/yfucryediv-jetro.png', '6912be9b26739.jpg', 'BARROCA', 'JETTER', 'SENIO', '', 14, 3, '11/11/2025', '12:42 PM', 0, 'off', 'PM IN', '192.168.1.22', '', '', 0),
(1644, 'YFUCRYEDIV', 'personnelImg/yfucryediv-jetro.png', '6a0beba15265d.jpg', 'BARROCA', 'JETTER', 'SENIO', '', 14, 3, '05/19/2026', '12:48 PM', 0, 'off', 'PM IN', '10.174.189.58', '', '', 0),
(1645, 'YFUCRYEDIV', 'personnelImg/yfucryediv-jetro.png', '6a0bf20674103.jpg', 'BARROCA', 'JETTER', 'SENIO', '', 14, 3, '05/19/2026', '01:15 PM', 0, 'on', 'PM OUT', '10.174.189.58', '', '', 0),
(1646, 'T1X1EOAO50', 'personnelImg/default_img.jpg', '6a0bf383c3ea0.jpg', 'HERRADURA', 'GINALYN ', 'OBENZA', '', 2, 3, '05/19/2026', '01:22 PM', 0, 'on', 'PM IN', '10.174.189.58', '', '', 0),
(1647, 'T1X1EOAO50', 'personnelImg/default_img.jpg', '6a0bf8204f9a1.jpg', 'HERRADURA', 'GINALYN ', 'OBENZA', '', 2, 3, '05/19/2026', '01:41 PM', 0, 'on', 'PM OUT', '10.174.189.58', '', '', 0),
(1648, 'M44PLOGP0U', 'personnelImg/default_img.jpg', '6a0c13a062afb.jpg', 'CASTILLO', 'JOEPET', 'CANILLADA', '', 2, 3, '05/19/2026', '03:39 PM', 0, 'on', 'PM IN', '10.174.189.58', '', '', 0),
(1649, 'P-JAP-12212021-7', 'personnelImg/default_img.jpg', '6a0c14775e1b9.jpg', 'PINONGGAN', 'JOSEPH', 'ALBERIO', '', 2, 3, '05/19/2026', '03:42 PM', 0, 'on', 'PM IN', '10.174.189.58', '', '', 0),
(1650, 'R0ZJXTVRR4', 'personnelImg/default_img.jpg', '6a0c1495a64a2.jpg', 'AVELINO', 'JULIEBERT', 'AZUCENA', '', 2, 3, '05/19/2026', '03:43 PM', 0, 'on', 'PM IN', '10.174.189.58', '', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `personnel_official_travel_logs`
--

CREATE TABLE `personnel_official_travel_logs` (
  `travel_log_id` int(11) NOT NULL,
  `personnel_id` int(11) NOT NULL,
  `travel_code` varchar(55) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `travel_date` varchar(55) NOT NULL,
  `travel_type` varchar(55) NOT NULL,
  `numDays` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personnel_seminars`
--

CREATE TABLE `personnel_seminars` (
  `ps_id` int(11) NOT NULL,
  `personnel_id` int(11) NOT NULL,
  `seminar_title` varchar(255) NOT NULL,
  `seminar_desc` varchar(255) NOT NULL,
  `seminar_venue` varchar(255) NOT NULL,
  `event_date` varchar(10) NOT NULL,
  `entry_type` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `personnel_seminars`
--

INSERT INTO `personnel_seminars` (`ps_id`, `personnel_id`, `seminar_title`, `seminar_desc`, `seminar_venue`, `event_date`, `entry_type`) VALUES
(1, 142, '2022 Regional HR Summit', 'A Continuing Professional Education for HR Managers and Leaders', 'Grand Xing Imperial Hotel, Iloilo City', '2022-04-27', 'Manual Encode'),
(2, 142, 'Performance Management Teams', 'Performance Management Teams', 'Planta Centro Bacolod Hotel and Residences, Corner Roxas and Araneta Streets, Bacolod City', '2019-10-29', 'Manual Encode'),
(3, 142, 'Legal Accountability', 'Regulation, Protection and Advice for Civil Servants Handling and Processing Government Records', 'EON Centennial Resort Hotel and Waterpark located in Alta Tierra Village, Jaro, Iloilo City', '2019-10-23', 'Manual Encode'),
(4, 142, 'Human Resource Records Management Seminar', 'Human Resource  Records Management Seminar', 'Iloilo Grand Hotel, Iloilo City', '2019-08-01', 'Manual Encode'),
(5, 142, 'CHRMP-NO LGU', 'Capacity Enhancement Program and Mid-year Conference', 'Palmas del Mar Resort, Bacolod City', '2019-07-18', 'Manual Encode'),
(6, 142, '7th Regional Congress of Human Resource Management Practitioners', '', 'Diversion 21 Hotel, Mandurriao, Iloilo City', '2018-10-04', 'Manual Encode'),
(7, 140, 'Test Seminar', 'Test Seminar', 'Test Seminar', '2025-11-03', 'Manual Encode');

-- --------------------------------------------------------

--
-- Table structure for table `pr_tbl_deductions`
--

CREATE TABLE `pr_tbl_deductions` (
  `deduction_id` int(11) NOT NULL,
  `deduction_type` varchar(55) DEFAULT NULL,
  `deduction_title` varchar(55) DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pr_tbl_income`
--

CREATE TABLE `pr_tbl_income` (
  `income_id` int(11) NOT NULL,
  `income_type` varchar(55) DEFAULT NULL,
  `income_title` varchar(55) DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pr_tbl_payroll_audit_log`
--

CREATE TABLE `pr_tbl_payroll_audit_log` (
  `audit_id` int(11) NOT NULL,
  `run_id` int(11) DEFAULT NULL,
  `detail_id` int(11) DEFAULT NULL,
  `action_type` enum('create','update','delete','approve','cancel','complete') NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `record_id` int(11) DEFAULT NULL,
  `field_name` varchar(100) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `performed_by` int(11) DEFAULT NULL COMMENT 'User who performed the action',
  `performed_at` datetime NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Audit trail for all payroll changes';

-- --------------------------------------------------------

--
-- Table structure for table `pr_tbl_payroll_profiles`
--

CREATE TABLE `pr_tbl_payroll_profiles` (
  `profile_id` int(11) NOT NULL,
  `profile_name` varchar(100) NOT NULL COMMENT 'Template name (e.g., "Regular Monthly Payroll", "13th Month Pay")',
  `profile_description` text DEFAULT NULL COMMENT 'Detailed description of this template',
  `profile_type` enum('regular','special','13th_month','bonus','custom') NOT NULL DEFAULT 'regular',
  `pay_frequency` enum('monthly','semi-monthly','bi-weekly','weekly','one-time') NOT NULL DEFAULT 'monthly',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Is this the default profile for regular payroll?',
  `created_by` int(11) DEFAULT NULL COMMENT 'User who created this profile',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Payroll templates/profiles for easy cloning and reuse';

-- --------------------------------------------------------

--
-- Table structure for table `pr_tbl_payroll_profile_deductions`
--

CREATE TABLE `pr_tbl_payroll_profile_deductions` (
  `profile_deduction_id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL COMMENT 'References pr_tbl_payroll_profiles.profile_id',
  `deduction_id` int(11) NOT NULL COMMENT 'References pr_tbl_deductions.deduction_id',
  `default_employee_amt` decimal(10,2) DEFAULT NULL COMMENT 'Default employee amount',
  `default_employer_amt` decimal(10,2) DEFAULT NULL COMMENT 'Default employer amount',
  `amount_calculation` enum('fixed','percentage','formula','personnel_specific') NOT NULL DEFAULT 'personnel_specific',
  `calculation_base` varchar(50) DEFAULT NULL COMMENT 'For percentage: what to base on',
  `calculation_value` decimal(10,4) DEFAULT NULL COMMENT 'For percentage: the percentage value',
  `is_mandatory` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Deduction items included in payroll profiles';

-- --------------------------------------------------------

--
-- Table structure for table `pr_tbl_payroll_profile_filters`
--

CREATE TABLE `pr_tbl_payroll_profile_filters` (
  `filter_id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `filter_type` enum('department','designation','emp_status','personnel','all') NOT NULL,
  `filter_value` varchar(50) NOT NULL COMMENT 'ID or "all"',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Define which personnel are included in profile';

-- --------------------------------------------------------

--
-- Table structure for table `pr_tbl_payroll_profile_income`
--

CREATE TABLE `pr_tbl_payroll_profile_income` (
  `profile_income_id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL COMMENT 'References pr_tbl_payroll_profiles.profile_id',
  `income_id` int(11) NOT NULL COMMENT 'References pr_tbl_income.income_id',
  `default_amount` decimal(10,2) DEFAULT NULL COMMENT 'Default amount (NULL = use personnel-specific amount)',
  `amount_calculation` enum('fixed','percentage','formula','personnel_specific') NOT NULL DEFAULT 'personnel_specific',
  `calculation_base` varchar(50) DEFAULT NULL COMMENT 'For percentage: what to base on (e.g., "basic_salary")',
  `calculation_value` decimal(10,4) DEFAULT NULL COMMENT 'For percentage: the percentage value',
  `is_mandatory` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Must this income be included?',
  `display_order` int(11) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Income items included in payroll profiles';

-- --------------------------------------------------------

--
-- Table structure for table `pr_tbl_payroll_runs`
--

CREATE TABLE `pr_tbl_payroll_runs` (
  `run_id` int(11) NOT NULL,
  `profile_id` int(11) DEFAULT NULL COMMENT 'Profile used to generate this run',
  `run_name` varchar(150) NOT NULL COMMENT 'Payroll run name (e.g., "October 2025 Regular Payroll")',
  `run_type` enum('regular','special','13th_month','bonus','adjustment','custom') NOT NULL DEFAULT 'regular',
  `pay_period_start` date NOT NULL COMMENT 'Start of pay period',
  `pay_period_end` date NOT NULL COMMENT 'End of pay period',
  `payment_date` date DEFAULT NULL COMMENT 'Actual payment date',
  `run_status` enum('draft','pending','approved','processing','completed','cancelled') NOT NULL DEFAULT 'draft',
  `total_personnel` int(11) NOT NULL DEFAULT 0 COMMENT 'Total number of personnel in this run',
  `total_gross` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total gross pay for all personnel',
  `total_deductions` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total deductions (employee portion)',
  `total_employer_share` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total employer contributions',
  `total_net_pay` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total net pay for all personnel',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL COMMENT 'User who created this run',
  `approved_by` int(11) DEFAULT NULL COMMENT 'User who approved this run',
  `approved_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Payroll execution history - each row is one payroll run';

-- --------------------------------------------------------

--
-- Table structure for table `pr_tbl_payroll_run_deductions`
--

CREATE TABLE `pr_tbl_payroll_run_deductions` (
  `run_deduction_id` int(11) NOT NULL,
  `detail_id` int(11) NOT NULL COMMENT 'References pr_tbl_payroll_run_details.detail_id',
  `run_id` int(11) NOT NULL COMMENT 'References pr_tbl_payroll_runs.run_id',
  `personnel_id` varchar(50) NOT NULL,
  `deduction_id` int(11) NOT NULL COMMENT 'References pr_tbl_deductions.deduction_id',
  `deduction_title` varchar(100) NOT NULL COMMENT 'Snapshot of deduction name at time of run',
  `deduction_type` varchar(50) NOT NULL,
  `employee_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `employer_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Deduction breakdown snapshot for each payroll run';

-- --------------------------------------------------------

--
-- Table structure for table `pr_tbl_payroll_run_details`
--

CREATE TABLE `pr_tbl_payroll_run_details` (
  `detail_id` int(11) NOT NULL,
  `run_id` int(11) NOT NULL COMMENT 'References pr_tbl_payroll_runs.run_id',
  `personnel_id` varchar(50) NOT NULL COMMENT 'References personnels.personnel_id',
  `gross_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_deductions` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_employer_share` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('pending','paid','hold','cancelled') NOT NULL DEFAULT 'pending',
  `payment_method` enum('bank_transfer','check','cash','other') DEFAULT NULL,
  `payment_reference` varchar(100) DEFAULT NULL COMMENT 'Check number, transaction ID, etc.',
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Individual personnel records within each payroll run';

-- --------------------------------------------------------

--
-- Table structure for table `pr_tbl_payroll_run_income`
--

CREATE TABLE `pr_tbl_payroll_run_income` (
  `run_income_id` int(11) NOT NULL,
  `detail_id` int(11) NOT NULL COMMENT 'References pr_tbl_payroll_run_details.detail_id',
  `run_id` int(11) NOT NULL COMMENT 'References pr_tbl_payroll_runs.run_id',
  `personnel_id` varchar(50) NOT NULL,
  `income_id` int(11) NOT NULL COMMENT 'References pr_tbl_income.income_id',
  `income_title` varchar(100) NOT NULL COMMENT 'Snapshot of income name at time of run',
  `income_type` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Income breakdown snapshot for each payroll run';

-- --------------------------------------------------------

--
-- Table structure for table `pr_tbl_payroll_snapshots`
--

CREATE TABLE `pr_tbl_payroll_snapshots` (
  `snapshot_id` int(11) NOT NULL,
  `run_id` int(11) NOT NULL COMMENT 'References pr_tbl_payroll_runs.run_id',
  `snapshot_date` date NOT NULL COMMENT 'Date snapshot was generated',
  `snapshot_type` enum('department','designation','emp_status','income_type','deduction_type','overall') NOT NULL,
  `group_by_value` varchar(100) DEFAULT NULL COMMENT 'Department ID, Designation ID, etc.',
  `group_by_label` varchar(150) DEFAULT NULL COMMENT 'Department Name, Designation Name, etc.',
  `personnel_count` int(11) NOT NULL DEFAULT 0,
  `total_gross` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_deductions` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_employer_share` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_net_pay` decimal(15,2) NOT NULL DEFAULT 0.00,
  `average_gross` decimal(10,2) NOT NULL DEFAULT 0.00,
  `average_net_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `min_net_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_net_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Aggregate payroll statistics for reporting and analysis';

-- --------------------------------------------------------

--
-- Table structure for table `pr_tbl_payroll_snapshot_items`
--

CREATE TABLE `pr_tbl_payroll_snapshot_items` (
  `snapshot_item_id` int(11) NOT NULL,
  `snapshot_id` int(11) NOT NULL COMMENT 'References pr_tbl_payroll_snapshots.snapshot_id',
  `run_id` int(11) NOT NULL,
  `item_type` enum('income','deduction') NOT NULL,
  `item_id` int(11) NOT NULL COMMENT 'income_id or deduction_id',
  `item_title` varchar(100) NOT NULL,
  `item_category` varchar(50) NOT NULL COMMENT 'income_type or deduction_type',
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `personnel_count` int(11) NOT NULL DEFAULT 0 COMMENT 'How many personnel have this item',
  `average_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `min_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Detailed breakdown of income/deductions in snapshots';

-- --------------------------------------------------------

--
-- Table structure for table `pr_tbl_pay_pro_personnels`
--

CREATE TABLE `pr_tbl_pay_pro_personnels` (
  `ppp_id` int(11) NOT NULL,
  `personnel_id` int(11) NOT NULL,
  `payprofile_id` int(11) NOT NULL,
  `status` varchar(55) NOT NULL DEFAULT 'Active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pr_tbl_personnel_deductions`
--

CREATE TABLE `pr_tbl_personnel_deductions` (
  `personnel_deduction_id` int(11) NOT NULL,
  `personnel_id` varchar(50) NOT NULL COMMENT 'References personnels.personnel_id',
  `deduction_id` int(11) NOT NULL COMMENT 'References pr_tbl_deductions.deduction_id',
  `employer_amt_per_pay` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Amount paid by employer per pay period',
  `employee_amt_per_pay` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Amount deducted from employee per pay period',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL COMMENT 'User who created this record'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pr_tbl_personnel_income`
--

CREATE TABLE `pr_tbl_personnel_income` (
  `personnel_income_id` int(11) NOT NULL,
  `personnel_id` varchar(50) NOT NULL COMMENT 'References personnels.personnel_id',
  `income_id` int(11) NOT NULL COMMENT 'References pr_tbl_income.income_id',
  `amount_per_pay` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Amount paid per pay period',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL COMMENT 'User who created this record'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Junction table: Links personnel to income types with amounts';

-- --------------------------------------------------------

--
-- Table structure for table `pr_tbl_signatory_items`
--

CREATE TABLE `pr_tbl_signatory_items` (
  `item_id` int(11) NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  `role_title` varchar(150) DEFAULT NULL,
  `person_name` varchar(150) DEFAULT NULL,
  `display_order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pr_tbl_signatory_items`
--

INSERT INTO `pr_tbl_signatory_items` (`item_id`, `template_id`, `role_title`, `person_name`, `display_order`) VALUES
(8, 2, 'Prepared By', 'HR Officer', 1),
(9, 2, 'Certified: Service duly rendered as stated', 'Municipal Mayor', 2),
(10, 2, 'Approved for Payment', 'Municipal Mayor', 3),
(11, 2, 'Certified: Each employee whose name appears on the payroll has been paid the amount as indicated', '', 4),
(12, 2, 'Accounting Entries', '', 5),
(13, 2, 'Certified: Cash available for the purpose', 'Municipal Accountant', 6),
(14, 2, 'Certified Correct:', 'Head of Treasury Division/Unit', 7);

-- --------------------------------------------------------

--
-- Table structure for table `pr_tbl_signatory_templates`
--

CREATE TABLE `pr_tbl_signatory_templates` (
  `template_id` int(11) NOT NULL,
  `template_name` varchar(100) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pr_tbl_signatory_templates`
--

INSERT INTO `pr_tbl_signatory_templates` (`template_id`, `template_name`, `is_default`, `created_at`) VALUES
(2, 'Standard Official Registry', 1, '2026-08-02 13:20:59');

-- --------------------------------------------------------

--
-- Table structure for table `service_record`
--

CREATE TABLE `service_record` (
  `sr_id` int(11) NOT NULL,
  `personnel_id` int(11) NOT NULL,
  `maid_lname` varchar(55) NOT NULL,
  `maid_fname` varchar(55) NOT NULL,
  `maid_mname` varchar(55) NOT NULL,
  `appointDate_status` varchar(55) NOT NULL,
  `serv_date_from` varchar(10) NOT NULL,
  `serv_date_to` varchar(10) NOT NULL,
  `roa_designation` varchar(255) NOT NULL,
  `roa_status` varchar(255) NOT NULL,
  `monthly_salary` decimal(14,3) NOT NULL,
  `annual_salary` decimal(14,3) NOT NULL,
  `office_appointment` varchar(255) NOT NULL,
  `separate_date` varchar(10) NOT NULL,
  `separate_cause` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `service_record`
--

INSERT INTO `service_record` (`sr_id`, `personnel_id`, `maid_lname`, `maid_fname`, `maid_mname`, `appointDate_status`, `serv_date_from`, `serv_date_to`, `roa_designation`, `roa_status`, `monthly_salary`, `annual_salary`, `office_appointment`, `separate_date`, `separate_cause`) VALUES
(1, 46, '', '', '', 'Active', '2012-07-01', '2012-12-31', 'Utility Worker I', 'Permanent', 0.000, 90.000, 'Office of the Municipal Rural Health Unit', '', ''),
(2, 46, '', '', '', '', '2013-01-01', '2013-12-31', 'Utility Worker I', 'Permanent', 0.000, 89.000, 'Office of the Municipal Rural Health Unit', '', ''),
(3, 140, 'Barroca', 'Jetter', 'Senio', 'Active', '2024-10-16', '2024-11-30', 'Administrative Aide I', 'Permanent', 14000.000, 168000.000, 'Market and Slaughterhouse Section', '', ''),
(4, 66, 'Aballe', 'Bethel', 'Perfuma', '', '1989-03-04', '1989-03-12', 'Clerk ', 'Temporary', 0.000, 9288.000, 'Office of the Municipal Treasurer', '', ''),
(5, 66, '', '', '', '', '1990-01-01', '1990-03-04', 'Clerk ', 'Temporary', 0.000, 9288.000, 'Office of the Municipal Treasurer', '', ''),
(6, 66, 'Aballe', 'Bethel', 'Perfuma', '', '1990-04-04', '1990-12-31', 'Laborer I ', 'Permanent', 0.000, 18.000, 'Office of the Municipal Treasurer', '', ''),
(7, 66, 'Aballe', 'Bethel', 'Perfuma', '', '1991-01-01', '1991-12-31', 'Laborer I', 'Permanent', 0.000, 18.000, 'Office of the Municipal Treasurer', '', ''),
(9, 66, '', '', '', '', '1996-01-01', '1996-12-31', 'Laborer I', 'Permanent', 0.000, 39.000, 'Office of the Municipal Treasurer', '', ''),
(11, 66, '', '', '', '', '1999-01-01', '1999-12-31', 'Laborer I', 'Permanent', 0.000, 42.000, 'Office of the Municipal Treasurer', '', ''),
(12, 66, '', '', '', '', '2000-01-01', '2000-12-31', 'Laborer I', 'Permanent', 0.000, 48.000, 'Office of the Municipal Treasurer', '', ''),
(13, 66, '', '', '', '', '2001-01-01', '2001-06-30', 'Laborer I', 'Permanent', 0.000, 51.000, 'Office of the Municipal Treasurer', '', ''),
(14, 66, '', '', '', '', '2001-01-07', '2001-12-31', 'Laborer I', 'Permanent', 0.000, 54.000, 'Office of the Municipal Treasurer', '', ''),
(15, 66, '', '', '', '', '2002-01-01', '2002-12-31', 'Laborer I', 'Permanent', 0.000, 57.000, 'Office of the Municipal Treasurer', '', ''),
(19, 66, '', '', '', '', '2010-01-01', '2010-12-31', 'RC CLERK II', 'Permanent', 0.000, 117.000, 'Office of the Municipal Treasurer', '', ''),
(20, 66, '', '', '', '', '2011-01-01', '2011-01-17', 'RC CLERK II', 'Permanent', 0.000, 128.000, 'Office of the Municipal Treasurer', '', ''),
(21, 66, '', '', '', '', '2011-01-18', '2011-04-04', 'Laborer I', 'Permanent (LEAVE OF ABSENCE WITHOUT PAY)', 0.000, 128.000, 'Office of the Municipal Treasurer', '', ''),
(22, 66, '', '', '', '', '2011-05-04', '', 'RC CLERK II', 'Permanent (RE-INSTATE)', 0.000, 0.000, '', '', ''),
(23, 66, '', '', '', '', '2011-05-04', '2011-12-31', 'RC CLERK II', 'Permanent', 0.000, 128.000, 'Office of the Municipal Treasurer', '', ''),
(24, 66, '', '', '', '', '2012-01-01', '2012-12-31', 'RC CLERK II', 'Permanent', 0.000, 139.000, 'Office of the Municipal Treasurer', '', ''),
(25, 66, '', '', '', '', '2013-01-01', '2013-12-31', 'RC CLERK II', 'Permanent', 0.000, 142.000, 'Office of the Municipal Treasurer', '', ''),
(26, 66, '', '', '', '', '2014-01-01', '2014-12-31', 'RCC II', 'Permanent', 0.000, 142.000, 'Office of the Municipal Treasurer', '', ''),
(27, 66, '', '', '', '', '2015-01-01', '2015-12-31', 'RC CLERK II', 'Permanent', 0.000, 151.000, 'Office of the Municipal Treasurer', '', ''),
(28, 66, '', '', '', '', '2016-01-01', '2016-09-30', 'RC CLERK II', 'Permanent', 0.000, 153.000, 'Office of the Municipal Treasurer', '', ''),
(29, 66, '', '', '', '', '2016-01-10', '2016-12-31', 'RC CLERK II', 'Permanent', 0.000, 157.000, 'Office of the Municipal Treasurer', '', ''),
(30, 66, '', '', '', '', '2017-01-01', '2017-08-31', 'RC CLERK II', 'Permanent', 0.000, 157.000, 'Office of the Municipal Treasurer', '', ''),
(31, 66, '', '', '', '', '2017-01-09', '2017-12-31', 'RC CLERK II', 'Permanent', 0.000, 162.000, 'Office of the Municipal Treasurer', '', ''),
(32, 66, '', '', '', '', '2018-01-01', '', 'RC CLERK II', 'Permanent', 0.000, 163.000, 'Office of the Municipal Treasurer', '', ''),
(34, 66, '', '', '', '', '1992-01-01', '1992-12-31', 'Laborer I', 'Permanent', 0.000, 18.000, 'Office of the Municipal Treasurer', '', ''),
(35, 66, '', '', '', '', '1993-01-01', '1993-12-31', 'Laborer I', 'Permanent', 0.000, 18.000, 'Office of the Municipal Treasurer', '', ''),
(36, 66, '', '', '', '', '1994-01-01', '1994-12-31', 'Laborer I', 'Permanent', 0.000, 18.000, 'Office of the Municipal Treasurer', '', ''),
(37, 66, '', '', '', '', '1997-01-01', '1997-12-31', 'Laborer I', 'Permanent', 0.000, 39.000, 'Office of the Municipal Treasurer', '', ''),
(38, 66, '', '', '', '', '1998-01-01', '1998-12-31', 'Laborer I', 'Permanent', 0.000, 39.000, 'Office of the Municipal Treasurer', '', ''),
(39, 66, '', '', '', '', '2003-01-01', '2003-12-31', 'Laborer I', 'Permanent', 0.000, 57.000, 'Office of the Municipal Treasurer', '', ''),
(40, 66, '', '', '', '', '2004-01-01', '2004-12-31', 'Laborer I ', 'Permanent', 0.000, 57.000, 'Office of the Municipal Treasurer', '', ''),
(41, 66, '', '', '', '', '2005-01-01', '2005-12-31', 'Laborer I', 'Permanent', 0.000, 57.000, 'Office of the Municipal Treasurer', '', ''),
(42, 66, '', '', '', '', '2006-01-01', '2006-12-31', 'Laborer I', 'Permanent', 0.000, 57.000, 'Office of the Municipal Treasurer', '', ''),
(43, 66, '', '', '', '', '2007-01-01', '2007-06-30', 'Laborer I', 'Permanent', 0.000, 57.000, 'Office of the Municipal Treasurer', '', ''),
(44, 66, '', '', '', '', '2007-01-07', '2007-12-31', 'Laborer I', 'Permanent', 0.000, 63.000, 'Office of the Municipal Treasurer', '', ''),
(48, 66, '', '', '', '', '1995-01-01', '1995-12-31', 'Laborer I', 'Permanent', 0.000, 39.000, 'Office of the Municipal Treasurer', '', ''),
(55, 66, '', '', '', '', '2009-01-01', '2009-12-31', 'RC CLERK II', 'Permanent', 0.000, 106.000, 'Office of the Municipal Treasurer', '', ''),
(57, 66, '', '', '', '', '2008-01-01', '2008-11-02', 'Laborer I', 'Permanent', 0.000, 63.000, 'Office of the Municipal Treasurer', '', ''),
(61, 66, '', '', '', '', '2008-01-07', '2008-12-31', 'RC CLERK II', 'Permanent', 0.000, 96.000, 'Office of the Municipal Treasurer', '', ''),
(62, 66, '', '', '', '', '2008-12-02', '2008-06-30', 'RC CLERK II', 'Permanent', 0.000, 96696.000, 'Office of the Municipal Treasurer', '', ''),
(63, 101, 'Acibido', 'Che Generoso', 'Paro', '', '2016-09-26', '2016-09-30', 'Statistician Aide', 'Temporary', 0.000, 120.000, 'Office of the Municipal Planning & Development Coordinator', '', ''),
(64, 101, 'Acibido', 'Che Generoso', 'Paro', '', '2016-10-01', '2016-12-31', 'Statistician Aide', 'Temporary', 0.000, 125.000, 'Office of the Municipal Planning & Development Coordinator', '', ''),
(65, 101, 'Acibido', 'Che Generoso', 'Paro', '', '2017-01-01', '2017-08-31', 'Statistician Aide', 'Temporary', 0.000, 125.000, 'Office of the Municipal Planning & Development Coordinator', '', ''),
(66, 101, 'Acibido', 'Che Generoso', 'Paro', '', '2017-09-01', '2017-09-25', 'Statistician Aide', 'Temporary', 0.000, 131.000, 'Office of the Municipal Planning & Development Coordinator', '', ''),
(67, 101, 'Acibido', 'Jetter', 'Paro', '', '2017-09-25', '2017-09-25', 'End of Temporary', '', 0.000, 131.000, 'Office of the Municipal Planning & Development Coordinator', '', ''),
(68, 101, 'Acibido', 'Che Generoso', 'Paro', '', '2017-10-23', '2017-12-31', 'Administrative Aide I', 'Permanent', 0.000, 107.000, 'Market and Slaughterhouse Section', '', ''),
(69, 101, 'Acibido', 'Che Generoso', 'Paro', '', '2018-01-01', '2018-12-31', 'Administrative Aide I', 'Permanent', 0.000, 107.000, 'Market and Slaughterhouse Section', '', ''),
(70, 117, 'Aguhayon', 'Ricardo', 'U.', '', '2016-10-03', '2016-12-31', 'Revenue Collection Clerk II', 'Permanent', 0.000, 154.000, 'Market and Slaughterhouse Section', '', ''),
(71, 117, 'Aguhayon', 'Ricardo', 'U.', '', '2017-01-01', '2017-08-31', 'Revenue Collection Clerk II', 'Permanent', 0.000, 154.000, 'Market and Slaughterhouse Section', '', ''),
(72, 117, 'Aguhayon', 'Ricardo', 'U.', '', '2017-09-01', '2017-12-31', 'Revenue Collection Clerk II', 'Permanent', 0.000, 159.000, 'Market and Slaughterhouse Section', '', ''),
(73, 117, 'Aguhayon', 'Ricardo', 'U.', '', '2018-01-01', '2018-12-31', 'Revenue Collection Clerk II', 'Permanent', 0.000, 159.000, 'Market and Slaughterhouse Section', '', ''),
(74, 95, 'Akol', 'Marlou', 'A.', '', '2007-07-09', '2008-06-30', 'Private Secretary II', 'Co-Terminous', 0.000, 175.000, 'Office of the Municipal Mayor-LGU Cauayan', '', ''),
(75, 95, 'Akol', 'Marlou', 'A.', '', '2008-07-01', '2009-12-31', 'Private Secretary II', 'Co-Terminous', 0.000, 193.000, 'Office of the Municipal Mayor-LGU Cauayan', '', ''),
(76, 95, 'Akol', 'Marlou', 'A.', '', '2010-01-01', '2010-12-31', 'Private Secretary II', 'Co-Terminous', 0.000, 197.000, 'Office of the Municipal Mayor-LGU Cauayan', '', ''),
(77, 95, 'Akol', 'Marlou', 'A.', '', '2011-01-01', '2011-06-30', 'Private Secretary II', 'Co-Terminous', 0.000, 221.000, 'Office of the Municipal Mayor-LGU Cauayan', '', ''),
(78, 95, 'Akol', 'Marlou', 'A.', '', '2011-07-01', '2011-09-30', 'Private Secretary II', 'Co-Terminous', 0.000, 224.000, 'Office of the Municipal Mayor-LGU Cauayan', '', ''),
(79, 95, '', '', '', '', '2011-10-01', '2012-12-31', 'Public Services Officer III', 'Permanent', 0.000, 272.000, 'Office of the Municipal Mayor-LGU Cauayan', '', ''),
(80, 95, '', '', '', '', '2013-01-01', '2013-12-31', 'Public Services Officer III', 'Permanent', 0.000, 305.000, 'Office of the Municipal Mayor-LGU Cauayan', '', ''),
(81, 95, '', '', '', '', '2014-01-01', '2014-01-12', 'Public Services Officer III', 'Permanent', 0.000, 338.000, 'Office of the Municipal Mayor-LGU Cauayan', '', ''),
(82, 95, '', '', '', '', '', '2014-01-13', 'RESIGNED', '', 0.000, 0.000, '', '', ''),
(83, 95, '', '', '', '', '2014-02-03', '2015-07-21', 'Executive Assistant I', 'Co-Terminous', 0.000, 236.000, 'Office of the Municipal Mayor-LGU Hinoba-an', '', ''),
(84, 95, '', '', '', '', '2015-07-22', '2015-12-31', 'Mrket Supervisor III', 'Permanent', 0.000, 338.000, 'Market and Slaughterhouse Section', '', ''),
(85, 95, '', '', '', '', '2016-01-01', '2016-09-30', 'Market Supervisor III', 'Permanent', 0.000, 338.000, 'Market and Slaughterhouse Section', '', ''),
(86, 95, '', '', '', '', '2016-10-01', '2016-12-31', 'Market Supervisor III', 'Permanent', 0.000, 361.000, 'Market and Slaughterhouse Section', '', ''),
(87, 95, '', '', '', '', '2017-01-01', '2017-08-31', 'Market Supervisor III', 'Permanent', 0.000, 361.000, 'Market and Slaughterhouse Section', '', ''),
(88, 95, '', '', '', '', '2017-09-01', '2017-12-31', 'Market Supervisor III', 'Permanent', 0.000, 385.000, 'Market and Slaughterhouse Section', '', ''),
(89, 95, '', '', '', '', '2018-01-01', '2018-07-21', 'Market Supervisor III', 'Permanent', 0.000, 385.000, 'Market and Slaughterhouse Section', '', ''),
(90, 95, '', '', '', '', '2018-07-22', '2018-12-31', 'Market Supervisor III', 'Permanent', 0.000, 390.000, 'Market and Slaughterhouse Section', '', ''),
(91, 82, 'Alagton', 'Gina', 'N.', '', '2012-07-01', '2012-12-31', 'Project Development Assistant', 'Permanent', 0.000, 149.000, 'Special Projects & Shelter Development Section', '', ''),
(92, 82, '', '', '', '', '2013-01-01', '2013-12-31', 'Project Development Assistant', 'Permanent', 0.000, 153.000, 'Special Projects & Shelter Development Section', '', ''),
(93, 82, '', '', '', '', '2014-01-01', '2014-12-31', 'Project Development Assistant', 'Permanent', 0.000, 153.000, 'Special Projects & Shelter Development Section', '', ''),
(94, 82, '', '', '', '', '2015-01-01', '2015-06-30', 'Project Development Assistant', 'Permanent', 0.000, 153.000, 'Special Projects & Shelter Development Section', '', ''),
(95, 82, '', '', '', '', '2015-07-01', '2015-12-31', 'Project Development Assistant', 'Permanent', 0.000, 162.000, 'Special Projects & Shelter Development Section', '', ''),
(96, 82, '', '', '', '', '2016-01-01', '2016-09-30', 'Project Development Assistant', 'Permanent', 0.000, 162.000, 'Special Projects & Shelter Development Section', '', ''),
(97, 82, '', '', '', '', '2016-10-01', '2016-12-31', 'Project Development Assistant', 'Permanent', 0.000, 167.000, 'Special Projects & Shelter Development Section', '', ''),
(98, 82, '', '', '', '', '2017-01-01', '2017-08-31', 'Project Development Assistant', 'Permanent', 0.000, 167.000, 'Special Projects & Shelter Development Section', '', ''),
(99, 82, '', '', '', '', '2017-09-01', '2017-12-31', 'Project Development Assistant', 'Permanent', 0.000, 172.000, 'Special Projects & Shelter Development Section', '', ''),
(100, 82, '', '', '', '', '2018-01-01', '2018-06-30', 'Project Development Assistant', 'Permanent', 0.000, 172.000, 'Special Projects & Shelter Development Section', '', ''),
(101, 82, '', '', '', '', '2018-07-01', '2018-12-31', 'Project Development Assistant', 'Permanent', 0.000, 174.000, 'Special Projects & Shelter Development Section', '', ''),
(102, 146, '', '', '', '', '2012-07-01', '2012-12-31', 'Accounting Clerk I', 'Permanent', 0.000, 111.000, 'Office of the Municipal Accounting', '', ''),
(103, 146, '', '', '', '', '2013-01-01', '2013-12-31', 'Accounting Clerk I', 'Permanent', 0.000, 111.000, 'Office of the Municipal Accounting', '', ''),
(104, 146, '', '', '', '', '2014-01-01', '2014-12-31', 'Accounting Clerk I', 'Permanent', 0.000, 114.000, 'Office of the Municipal Accounting', '', ''),
(105, 146, '', '', '', '', '2015-01-01', '2015-06-30', 'Accounting Clerk I', 'Permanent', 0.000, 114.000, 'Office of the Municipal Accounting', '', ''),
(106, 146, '', '', '', '', '2015-07-01', '2015-12-31', 'Accounting Clerk I', 'Permanent', 0.000, 121.000, 'Office of the Municipal Accounting', '', ''),
(107, 146, '', '', '', '', '2016-01-01', '2016-03-15', 'Accounting Clerk I', 'Permanent', 0.000, 121.000, 'Office of the Municipal Accounting', '', ''),
(108, 146, '', '', '', '', '2016-03-16', '2016-09-30', 'Management & Audit Analyst I', 'Permanent', 0.000, 200.000, 'Office of the Municipal Accounting', '', ''),
(109, 146, '', '', '', '', '2016-10-01', '2016-12-31', 'Management & Audit Analyst I', 'Permanent', 0.000, 206.000, 'Office of the Municipal Accounting', '', ''),
(110, 146, '', '', '', '', '2017-01-01', '2017-08-31', 'Accounting Clerk I', 'Permanent', 0.000, 206.000, 'Office of the Municipal Accounting', '', ''),
(111, 146, '', '', '', '', '2017-09-01', '2017-12-31', 'Management & Audit Analyst I', 'Permanent', 0.000, 211.000, 'Office of the Municipal Accounting', '', ''),
(112, 146, '', '', '', '', '2018-01-01', '2018-12-31', 'Management & Audit Analyst I', 'Permanent', 0.000, 211.000, 'Office of the Municipal Accounting', '', ''),
(113, 83, '', '', '', '', '1994-06-01', '1994-12-31', 'Utility Worker I', 'Permanent', 0.000, 24.000, 'Office of the Municipal Accounting', '', ''),
(114, 83, '', '', '', '', '1995-01-01', '1995-12-31', 'Utility Worker I', 'Permanent', 0.000, 39.000, 'Office of the Municipal Accounting', '', ''),
(115, 83, '', '', '', '', '1996-01-01', '1996-12-31', 'Utility Worker I', 'Permanent', 0.000, 39.000, 'Office of the Municipal Accounting', '', ''),
(116, 83, '', '', '', '', '1997-01-01', '1997-12-31', 'Utility Worker I', 'Permanent', 0.000, 39.000, 'Office of the Municipal Accounting', '', ''),
(117, 83, '', '', '', '', '1998-01-01', '1998-12-31', 'Utility Worker I', 'Permanent', 0.000, 39.000, 'Office of the Municipal Accounting', '', ''),
(118, 83, '', '', '', '', '1999-01-01', '1999-12-31', 'Utility Worker I', 'Permanent', 0.000, 42.000, 'Office of the Municipal Accounting', '', ''),
(119, 83, '', '', '', '', '2000-01-01', '2000-07-31', 'Utility Worker I', 'Permanent', 0.000, 46.000, 'Office of the Municipal Accounting', '', ''),
(120, 83, '', '', '', '', '2000-08-01', '2000-12-31', 'Utility Worker II', 'Permanent', 0.000, 50.000, 'Office of the Municipal Accounting', '', ''),
(121, 83, '', '', '', '', '2001-01-01', '2001-06-30', 'Utility Worker II', 'Permanent', 0.000, 53.000, 'Office of the Municipal Accounting', '', ''),
(122, 83, '', '', '', '', '2001-07-01', '2001-12-31', 'Utility Worker II', 'Permanent', 0.000, 56.000, 'Office of the Municipal Accounting', '', ''),
(123, 83, '', '', '', '', '2002-01-01', '2002-12-31', 'Utility Worker II', 'Permanent', 0.000, 59.000, 'Office of the Municipal Accounting', '', ''),
(124, 83, '', '', '', '', '2003-01-01', '2003-12-31', 'Utility Worker II', 'Permanent', 0.000, 59.000, 'Office of the Municipal Accounting', '', ''),
(125, 83, '', '', '', '', '2004-01-01', '2004-12-31', 'Utility Worker II', 'Permanent', 0.000, 59.000, 'Office of the Municipal Accounting', '', ''),
(126, 83, '', '', '', '', '2005-01-01', '2005-12-31', 'Utility Worker II', 'Permanent', 0.000, 59.000, 'Office of the Municipal Accounting', '', ''),
(127, 83, '', '', '', '', '2006-01-01', '2006-12-31', 'Utility Worker II', 'Permanent', 0.000, 59.000, 'Office of the Municipal Accounting', '', ''),
(128, 83, '', '', '', '', '2007-01-01', '2007-06-30', 'Utility Worker II', 'Permanent', 0.000, 59.000, 'Office of the Municipal Accounting', '', ''),
(129, 83, '', '', '', '', '2007-07-01', '2008-06-30', 'Utility Worker II', 'Permanent', 0.000, 71.000, 'Office of the Municipal Accounting', '', ''),
(130, 83, '', '', '', '', '2008-07-01', '2009-12-31', 'Utility Worker II', 'Permanent', 0.000, 72.000, 'Office of the Municipal Accounting', '', ''),
(131, 83, '', '', '', '', '2010-01-01', '2010-10-25', 'Utility Worker II', 'Permanent', 0.000, 80.000, 'Office of the Municipal Accounting', '', ''),
(132, 83, '', '', '', '', '2010-10-26', '2011-09-30', 'Accounting Clerk I', 'Permanent', 0.000, 102.000, 'Office of the Municipal Accounting', '', ''),
(133, 83, '', '', '', '', '2011-10-01', '2011-12-31', 'Senior Bookkeeper', 'Permanent', 0.000, 147.000, 'Office of the Municipal Accounting', '', ''),
(134, 83, '', '', '', '', '2012-01-01', '2012-12-31', 'Senior Bookkeeper', 'Permanent', 0.000, 160.000, 'Office of the Municipal Accounting', '', ''),
(135, 83, '', '', '', '', '2013-01-01', '2013-12-31', 'Senior Bookkeeper', 'Permanent', 0.000, 164.000, 'Office of the Municipal Accounting', '', ''),
(136, 83, '', '', '', '', '2014-01-01', '2014-12-31', 'Senior Bookkeeper', 'Permanent', 0.000, 164.000, 'Office of the Municipal Accounting', '', ''),
(137, 83, '', '', '', '', '2015-01-01', '2015-09-30', 'Senior Bookkeeper', 'Permanent', 0.000, 164.000, 'Office of the Municipal Accounting', '', ''),
(138, 83, '', '', '', '', '2015-10-01', '2015-12-31', 'Senior Bookkeeper', 'Permanent', 0.000, 175.000, 'Office of the Municipal Accounting', '', ''),
(139, 83, '', '', '', '', '2016-01-01', '2016-09-30', 'Senior Bookkeeper', 'Permanent', 0.000, 175.000, 'Office of the Municipal Accounting', '', ''),
(140, 83, '', '', '', '', '2016-10-01', '2016-12-31', 'Senior Bookkeeper', 'Permanent', 0.000, 180.000, 'Office of the Municipal Accounting', '', ''),
(141, 83, '', '', '', '', '2017-01-01', '2017-08-31', 'Senior Bookkeeper', 'Permanent', 0.000, 180.000, 'Office of the Municipal Accounting', '', ''),
(142, 83, '', '', '', '', '2017-09-01', '2017-12-31', 'Senior Bookkeeper', 'Permanent', 0.000, 185.000, 'Office of the Municipal Accounting', '', ''),
(143, 83, '', '', '', '', '2018-01-01', '2018-12-31', 'Senior Bookkeeper', 'Permanent', 0.000, 185.000, 'Office of the Municipal Accounting', '', ''),
(144, 144, '', '', '', '', '1993-05-03', '1993-12-31', 'Midwife I', 'Permanent', 0.000, 24.000, 'Office of the Municipal Rural Health Unit', '', ''),
(145, 144, '', '', '', '', '1994-01-01', '1994-12-31', 'Midwife I', 'Permanent', 0.000, 34.000, 'Office of the Municipal Rural Health Unit', '', ''),
(146, 144, '', '', '', '', '1995-01-01', '1995-12-31', 'Midwife II', 'Permanent', 0.000, 46.000, 'Office of the Municipal Rural Health Unit', '', ''),
(147, 144, '', '', '', '', '1996-01-01', '1996-12-31', 'Midwife II', 'Permanent', 0.000, 49.000, 'Office of the Municipal Rural Health Unit', '', ''),
(148, 144, '', '', '', '', '1997-01-01', '1997-10-31', 'Midwife II', 'Permanent', 0.000, 58.000, 'Office of the Municipal Rural Health Unit', '', ''),
(149, 144, '', '', '', '', '1997-11-01', '1997-12-31', 'Midwife II', 'Permanent', 0.000, 67.000, 'Office of the Municipal Rural Health Unit', '', ''),
(150, 144, '', '', '', '', '1998-01-01', '1998-12-31', 'Midwife II', 'Permanent', 0.000, 67.000, 'Office of the Municipal Rural Health Unit', '', ''),
(151, 144, '', '', '', '', '1999-01-01', '1999-12-31', 'Midwife II', 'Permanent', 0.000, 72.000, 'Office of the Municipal Rural Health Unit', '', ''),
(152, 144, '', '', '', '', '2000-01-01', '2000-12-31', 'Midwife II', 'Permanent', 0.000, 81.000, 'Office of the Municipal Rural Health Unit', '', ''),
(153, 144, '', '', '', '', '2001-01-01', '2001-06-30', 'Midwife II', 'Permanent', 0.000, 86.000, 'Office of the Municipal Rural Health Unit', '', ''),
(154, 144, '', '', '', '', '2001-07-01', '2001-12-31', 'Midwife II', 'Permanent', 0.000, 91.000, 'Office of the Municipal Rural Health Unit', '', ''),
(155, 144, '', '', '', '', '2002-01-01', '2007-06-30', 'Midwife II', 'Permanent', 0.000, 96.000, 'Office of the Municipal Rural Health Unit', '', ''),
(156, 144, '', '', '', '', '2007-07-01', '2008-06-30', 'Midwife II', 'Permanent', 0.000, 106.000, 'Office of the Municipal Rural Health Unit', '', ''),
(157, 144, '', '', '', '', '2008-07-01', '0009-12-31', 'Midwife II', 'Permanent', 0.000, 116.000, 'Office of the Municipal Rural Health Unit', '', ''),
(158, 144, '', '', '', '', '2010-01-01', '2010-12-31', 'Midwife II', 'Permanent', 0.000, 128.000, 'Office of the Municipal Rural Health Unit', '', ''),
(159, 144, '', '', '', '', '2011-01-01', '2011-12-31', 'Midwife II', 'Permanent', 0.000, 139.000, 'Office of the Municipal Rural Health Unit', '', ''),
(160, 144, '', '', '', '', '2012-01-01', '2012-12-31', 'Midwife II', 'Permanent', 0.000, 151.000, 'Office of the Municipal Rural Health Unit', '', ''),
(161, 144, '', '', '', '', '2013-01-01', '2013-12-31', 'Midwife II', 'Permanent', 0.000, 154.000, 'Office of the Municipal Rural Health Unit', '', ''),
(162, 144, '', '', '', '', '2014-01-01', '2014-12-31', 'Midwife II', 'Permanent', 0.000, 156.000, 'Office of the Municipal Rural Health Unit', '', ''),
(163, 144, '', '', '', '', '2015-01-01', '2015-12-31', 'Midwife II', 'Permanent', 0.000, 164.000, 'Office of the Municipal Rural Health Unit', '', ''),
(164, 144, '', '', '', '', '2016-01-01', '2016-09-30', 'Midwife II', 'Permanent', 0.000, 169.000, 'Office of the Municipal Rural Health Unit', '', ''),
(165, 144, '', '', '', '', '2016-10-01', '2016-12-31', 'Midwife II', 'Permanent', 0.000, 174.000, 'Office of the Municipal Rural Health Unit', '', ''),
(166, 144, '', '', '', '', '2017-01-01', '2017-05-02', 'Midwife II', 'Permanent', 0.000, 174.000, 'Office of the Municipal Rural Health Unit', '', ''),
(167, 144, '', '', '', '', '2017-05-03', '2017-08-31', 'Midwife II', 'Permanent', 0.000, 175.000, 'Office of the Municipal Rural Health Unit', '', ''),
(168, 144, '', '', '', '', '2017-09-01', '2017-12-31', 'Midwife II', 'Permanent', 0.000, 200.000, 'Office of the Municipal Rural Health Unit', '', ''),
(169, 144, '', '', '', '', '2018-01-01', '2018-12-31', 'Midwife II', 'Permanent', 0.000, 202.000, 'Office of the Municipal Rural Health Unit', '', ''),
(182, 113, '', '', '', '', '2016-06-13', '2016-12-31', 'Teacher I', 'Probationary', 0.000, 228.000, 'Bilbao-Uybico NHS', '', ''),
(183, 113, '', '', '', '', '2017-01-01', '2017-12-31', 'Teacher I', 'Probationary', 0.000, 235.000, 'Bilbao-Uybico NHS', '', ''),
(184, 113, '', '', '', '', '2018-01-01', '2018-06-03', 'Teacher I', 'Probationary', 0.000, 242.000, 'Bilbao-Uybico NHS', '', ''),
(185, 113, '', '', '', '', '2018-06-22', '2018-12-31', 'Teacher I', 'Probationary', 0.000, 242.000, 'Municipal Disaster Risk Reduction & Management Office', '', ''),
(186, 113, '', '', '', '', '2019-01-01', '2019-01-14', 'Teacher I', 'Probationary', 0.000, 249.000, 'Municipal Disaster Risk Reduction & Management Office', '', ''),
(187, 113, '', '', '', '', '2019-01-15', '2019-01-15', 'Teacher I', 'Probationary', 0.000, 0.000, '', '2019-01-15', 'Resigned'),
(188, 113, '', '', '', '', '2019-01-16', '2019-12-31', 'Local DRRM Assistant', 'Permanent', 0.000, 175.000, 'Municipal Disaster Risk Reduction & Management Office', '', ''),
(189, 142, '', '', '', '', '2018-04-16', '2018-07-31', 'RECORDS OFFICER I', 'Permanent', 0.000, 196.000, 'Office of the Municipal Mayor', '', ''),
(194, 142, '', '', '', '', '2018-08-01', '2018-12-31', 'Records Officer I', 'Permanent', 0.000, 202.000, 'Office of the Municipal Mayor', '', ''),
(195, 142, '', '', '', '', '2019-01-01', '2019-12-31', 'Records Officer I', 'Permanent', 0.000, 207.000, 'Office of the Municipal Mayor', '', ''),
(196, 142, '', '', '', '', '2020-01-01', '2020-12-31', 'Records Officer I', 'Permanent', 0.000, 218.000, 'Office of the Municipal Mayor', '', ''),
(197, 142, '', '', '', '', '2021-01-01', '2021-04-15', 'Records Officer I', 'Permanent', 0.000, 229.000, 'Office of the Municipal Mayor', '', ''),
(198, 142, '', '', '', '', '2021-04-16', '2021-12-31', 'Records Officer I', 'Permanent', 0.000, 230.000, 'Office of the Municipal Mayor', '', ''),
(199, 142, '', '', '', '', '2022-01-01', '2022-12-31', 'Records Officer I', 'Permanent', 0.000, 241.000, 'Office of the Municipal Mayor', '', ''),
(200, 142, '', '', '', '', '2023-01-01', '2023-12-31', 'Records Officer I', 'Permanent', 0.000, 252.000, 'Office of the Municipal Mayor', '', ''),
(201, 142, '', '', '', '', '2024-01-01', '2024-04-15', 'Records Officer I', 'Permanent', 0.000, 252.000, 'Office of the Municipal Mayor', '', ''),
(202, 142, '', '', '', '', '2024-04-16', '2024-08-01', 'Records Officer I', 'Permanent', 0.000, 254.000, 'Office of the Municipal Mayor', '', ''),
(203, 142, '', '', '', '', '2024-08-02', '2024-12-31', 'Records Officer I', 'Permanent', 0.000, 267.000, 'Office of the Municipal Mayor', '', ''),
(204, 142, '', '', '', '', '2025-01-01', '', 'Records Officer I', 'Permanent', 0.000, 267.000, 'Office of the Municipal Mayor', '', ''),
(206, 142, '', '', '', '', '1989-08-19', '1989-09-08', '', '', 0.000, 0.000, '', '', ''),
(207, 108, '', '', '', '', '2018-04-16', '2018-07-31', '(Utility Worker I) Administrative Aide I', 'Permanent', 0.000, 107.000, 'EED-Heavy Equipment Section', '', ''),
(209, 108, '', '', '', '', '2018-08-01', '2018-12-31', '(Utility Worker I) Administrative Aide I', 'Permanent', 0.000, 113.000, 'EED-Heavy Equipment Section', '', ''),
(210, 108, '', '', '', '', '2019-01-01', '2019-12-31', '(Utility Worker I) Administrative Aide I', 'Permanent', 0.000, 119.000, 'EED-Heavy Equipment Section', '', ''),
(211, 108, '', '', '', '', '2020-01-01', '2020-12-31', '(Utility Worker I) Administrative Aide I', 'Permanent', 0.000, 124.000, 'EED-Heavy Equipment Section', '', ''),
(212, 108, '', '', '', '', '2021-01-01', '2021-04-15', '(Utility Worker I) Administrative Aide I', 'Permanent', 0.000, 129.000, 'EED-Heavy Equipment Section', '', ''),
(213, 108, '', '', '', '', '2021-04-16', '2021-12-31', '(Utility Worker I) Administrative Aide I', 'Permanent', 0.000, 131.000, 'EED-Heavy Equipment Section', '', ''),
(214, 108, '', '', '', '', '2022-01-01', '2022-12-31', '(Utility Worker I) Administrative Aide I', 'Permanent', 0.000, 136.000, 'EED-Heavy Equipment Section', '', ''),
(215, 108, '', '', '', '', '2023-01-01', '2023-12-31', '(Utility Worker I) Administrative Aide I', 'Permanent', 0.000, 141.000, 'EED-Heavy Equipment Section', '', ''),
(216, 108, '', '', '', '', '2024-01-01', '2024-08-01', '(Utility Worker I) Administrative Aide I', 'Permanent', 0.000, 141.000, 'EED-Heavy Equipment Section', '', ''),
(217, 108, '', '', '', '', '2024-08-02', '2024-12-31', '(Utility Worker I) Administrative Aide I', 'Permanent', 0.000, 148.000, 'EED-Heavy Equipment Section', '', ''),
(218, 108, '', '', '', '', '2025-01-01', '', '(Utility Worker I) Administrative Aide I', 'Permanent', 0.000, 148.000, 'EED-Heavy Equipment Section', '', ''),
(219, 122, '', '', '', '', '2007-03-01', '2007-06-30', 'Carpenter I', 'Temporary', 0.000, 65.000, 'Office of the Municipal Engineer', '', ''),
(220, 122, '', '', '', '', '2007-07-01', '2008-06-30', 'Carpenter I', 'Temporary', 0.000, 71.000, 'Office of the Municipal Engineer', '', ''),
(221, 122, '', '', '', '', '2008-07-01', '2009-12-31', 'Carpenter I', 'Temporary', 0.000, 78.000, 'Office of the Municipal Engineer', '', ''),
(222, 122, '', '', '', '', '2010-01-01', '2010-12-31', 'Carpenter I', 'Temporary', 0.000, 87.000, 'Office of the Municipal Engineer', '', ''),
(223, 122, '', '', '', '', '2011-01-01', '2011-12-31', 'Carpenter I', 'Temporary', 0.000, 95.000, 'Office of the Municipal Engineer', '', ''),
(224, 122, '', '', '', '', '2012-01-01', '2012-03-15', 'Carpenter', 'Temporary', 0.000, 103.000, 'Office of the Municipal Engineer', '', ''),
(225, 122, '', '', '', '', '2012-03-16', '2012-12-31', 'Carpenter I', 'Permanent', 0.000, 103.000, 'Office of the Municipal Engineer', '', ''),
(226, 122, '', '', '', '', '2013-01-01', '2013-12-31', 'Carpenter I', 'Permanent', 0.000, 106.000, 'Office of the Municipal Engineer', '', ''),
(227, 122, '', '', '', '', '2014-01-01', '2014-12-31', 'Carpenter I', 'Permanent', 0.000, 106.000, 'Office of the Municipal Engineer', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `shift_id` int(11) NOT NULL,
  `do_id` int(11) NOT NULL,
  `shift_name` varchar(255) NOT NULL,
  `type` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`shift_id`, `do_id`, `shift_name`, `type`) VALUES
(3, 0, 'Regular', 'Regular Shift'),
(5, 0, 'Flexi Time 9AM-6PM', 'Regular Shift');

-- --------------------------------------------------------

--
-- Table structure for table `signatories_settings`
--

CREATE TABLE `signatories_settings` (
  `id` int(11) NOT NULL,
  `hrmo_name` varchar(255) DEFAULT NULL,
  `hrmo_position` varchar(255) DEFAULT 'Human Resource Management Officer',
  `recommending_name` varchar(255) DEFAULT NULL,
  `recommending_position` varchar(255) DEFAULT 'Immediate Supervisor',
  `approving_name` varchar(255) DEFAULT NULL,
  `approving_position` varchar(255) DEFAULT 'Regional Director',
  `monetization_constant` decimal(10,7) DEFAULT 0.0481927,
  `budget_officer_name` varchar(255) DEFAULT NULL,
  `budget_officer_position` varchar(255) DEFAULT 'Municipal Budget Officer',
  `treasurer_name` varchar(255) DEFAULT NULL,
  `treasurer_position` varchar(255) DEFAULT 'Acting Municipal Treasurer',
  `accountant_name` varchar(255) DEFAULT NULL,
  `accountant_position` varchar(255) DEFAULT 'Municipal Accountant',
  `mayor_name` varchar(255) DEFAULT NULL,
  `mayor_position` varchar(255) DEFAULT 'Municipal Mayor',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `signatories_settings`
--

INSERT INTO `signatories_settings` (`id`, `hrmo_name`, `hrmo_position`, `recommending_name`, `recommending_position`, `approving_name`, `approving_position`, `monetization_constant`, `budget_officer_name`, `budget_officer_position`, `treasurer_name`, `treasurer_position`, `accountant_name`, `accountant_position`, `mayor_name`, `mayor_position`, `created_at`, `updated_at`) VALUES
(1, 'CHERRY LYNN M. JUAREZ', 'Human Resource Management Officer', 'GINALYN O. HERRADURA', 'HRMO III', 'JOHN MARK B. RELIQUIAS', 'Municipal Administrator', 0.0481927, '', 'Municipal Budget Officer', 'JOSEPHINE I. GUINTOS', 'Municipal Treasurer', 'OFELIA T. TUPAS', 'Municipal Accountant', 'DAPH ANTHONY V. RELIQUIAS', 'Municipal Mayor', '2025-11-02 08:47:15', '2026-05-19 11:12:25'),
(2, NULL, 'Human Resource Management Officer', NULL, 'Immediate Supervisor', NULL, 'Regional Director', 0.0481927, NULL, 'Municipal Budget Officer', NULL, 'Acting Municipal Treasurer', NULL, 'Municipal Accountant', NULL, 'Municipal Mayor', '2025-11-02 08:50:03', '2025-11-02 08:50:03');

-- --------------------------------------------------------

--
-- Table structure for table `slides`
--

CREATE TABLE `slides` (
  `slide_id` int(11) NOT NULL,
  `img` varchar(255) NOT NULL,
  `sequence` int(11) NOT NULL,
  `status` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `slides`
--

INSERT INTO `slides` (`slide_id`, `img`, `sequence`, `status`) VALUES
(1, '4645-3879-for-the-lord-take-delight-in-his-people-he-crowns-the-humble-with-salvation-bible-quotes.jpg', 1, ''),
(2, '28966-7504-7.jpg', 2, ''),
(3, '89734-11700-13631-5.jpg', 3, ''),
(4, '27105-29176-1.jpg', 4, ''),
(5, '16958-72042-6.jpg', 5, ''),
(6, '96560-83175-3.jpg', 6, ''),
(7, '84030-80175-2.jpg', 7, ''),
(8, '77333-7504-7.jpg', 8, ''),
(9, '83077-29176-1.jpg', 9, ''),
(10, '99217-72042-6.jpg', 10, '');

-- --------------------------------------------------------

--
-- Table structure for table `time_schedules`
--

CREATE TABLE `time_schedules` (
  `schedule_id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `day` varchar(15) NOT NULL,
  `am_IN` varchar(15) NOT NULL,
  `am_IN_co` varchar(15) NOT NULL,
  `am_OUT` varchar(15) NOT NULL,
  `am_OUT_co` varchar(15) NOT NULL,
  `pm_IN` varchar(15) NOT NULL,
  `pm_IN_co` varchar(15) NOT NULL,
  `pm_OUT` varchar(15) NOT NULL,
  `pm_OUT_co` varchar(15) NOT NULL,
  `do_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `type` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `time_schedules`
--

INSERT INTO `time_schedules` (`schedule_id`, `school_id`, `day`, `am_IN`, `am_IN_co`, `am_OUT`, `am_OUT_co`, `pm_IN`, `pm_IN_co`, `pm_OUT`, `pm_OUT_co`, `do_id`, `shift_id`, `type`) VALUES
(1, 1, 'Monday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 17, 3, 'Regular Shift'),
(2, 1, 'Monday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 1, 3, 'Regular Shift'),
(3, 1, 'Monday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 21, 3, 'Regular Shift'),
(4, 1, 'Monday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 14, 3, 'Regular Shift'),
(5, 1, 'Monday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 16, 3, 'Regular Shift'),
(6, 1, 'Monday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 25, 3, 'Regular Shift'),
(7, 1, 'Monday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 19, 3, 'Regular Shift'),
(8, 1, 'Monday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 20, 3, 'Regular Shift'),
(9, 1, 'Monday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 6, 3, 'Regular Shift'),
(10, 1, 'Monday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 23, 3, 'Regular Shift'),
(11, 1, 'Monday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 11, 3, 'Regular Shift'),
(12, 1, 'Monday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 9, 3, 'Regular Shift'),
(13, 1, 'Monday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 5, 3, 'Regular Shift'),
(14, 1, 'Monday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 8, 3, 'Regular Shift'),
(15, 1, 'Monday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 3, 3, 'Regular Shift'),
(16, 1, 'Monday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 2, 3, 'Regular Shift'),
(17, 1, 'Monday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 4, 3, 'Regular Shift'),
(18, 1, 'Monday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 12, 3, 'Regular Shift'),
(19, 1, 'Monday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 7, 3, 'Regular Shift'),
(20, 1, 'Monday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 10, 3, 'Regular Shift'),
(21, 1, 'Monday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 24, 3, 'Regular Shift'),
(22, 1, 'Monday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 15, 3, 'Regular Shift'),
(23, 1, 'Tuesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 17, 3, 'Regular Shift'),
(24, 1, 'Tuesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 1, 3, 'Regular Shift'),
(25, 1, 'Tuesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 21, 3, 'Regular Shift'),
(26, 1, 'Tuesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 14, 3, 'Regular Shift'),
(27, 1, 'Tuesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 16, 3, 'Regular Shift'),
(28, 1, 'Tuesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 25, 3, 'Regular Shift'),
(29, 1, 'Tuesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 19, 3, 'Regular Shift'),
(30, 1, 'Tuesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 20, 3, 'Regular Shift'),
(31, 1, 'Tuesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 6, 3, 'Regular Shift'),
(32, 1, 'Tuesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 23, 3, 'Regular Shift'),
(33, 1, 'Tuesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 11, 3, 'Regular Shift'),
(34, 1, 'Tuesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 9, 3, 'Regular Shift'),
(35, 1, 'Tuesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 5, 3, 'Regular Shift'),
(36, 1, 'Tuesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 8, 3, 'Regular Shift'),
(37, 1, 'Tuesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 3, 3, 'Regular Shift'),
(38, 1, 'Tuesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 2, 3, 'Regular Shift'),
(39, 1, 'Tuesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 4, 3, 'Regular Shift'),
(40, 1, 'Tuesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 12, 3, 'Regular Shift'),
(41, 1, 'Tuesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 7, 3, 'Regular Shift'),
(42, 1, 'Tuesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 10, 3, 'Regular Shift'),
(43, 1, 'Tuesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 24, 3, 'Regular Shift'),
(44, 1, 'Tuesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 15, 3, 'Regular Shift'),
(45, 1, 'Wednesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 17, 3, 'Regular Shift'),
(46, 1, 'Wednesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 1, 3, 'Regular Shift'),
(47, 1, 'Wednesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 21, 3, 'Regular Shift'),
(48, 1, 'Wednesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 14, 3, 'Regular Shift'),
(49, 1, 'Wednesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 16, 3, 'Regular Shift'),
(50, 1, 'Wednesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 25, 3, 'Regular Shift'),
(51, 1, 'Wednesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 19, 3, 'Regular Shift'),
(52, 1, 'Wednesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 20, 3, 'Regular Shift'),
(53, 1, 'Wednesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 6, 3, 'Regular Shift'),
(54, 1, 'Wednesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 23, 3, 'Regular Shift'),
(55, 1, 'Wednesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 11, 3, 'Regular Shift'),
(56, 1, 'Wednesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 9, 3, 'Regular Shift'),
(57, 1, 'Wednesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 5, 3, 'Regular Shift'),
(58, 1, 'Wednesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 8, 3, 'Regular Shift'),
(59, 1, 'Wednesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 3, 3, 'Regular Shift'),
(60, 1, 'Wednesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 2, 3, 'Regular Shift'),
(61, 1, 'Wednesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 4, 3, 'Regular Shift'),
(62, 1, 'Wednesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 12, 3, 'Regular Shift'),
(63, 1, 'Wednesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 7, 3, 'Regular Shift'),
(64, 1, 'Wednesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 10, 3, 'Regular Shift'),
(65, 1, 'Wednesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 24, 3, 'Regular Shift'),
(66, 1, 'Wednesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 15, 3, 'Regular Shift'),
(67, 1, 'Thursday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 17, 3, 'Regular Shift'),
(68, 1, 'Thursday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 1, 3, 'Regular Shift'),
(69, 1, 'Thursday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 21, 3, 'Regular Shift'),
(70, 1, 'Thursday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 14, 3, 'Regular Shift'),
(71, 1, 'Thursday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 16, 3, 'Regular Shift'),
(72, 1, 'Thursday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 25, 3, 'Regular Shift'),
(73, 1, 'Thursday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 19, 3, 'Regular Shift'),
(74, 1, 'Thursday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 20, 3, 'Regular Shift'),
(75, 1, 'Thursday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 6, 3, 'Regular Shift'),
(76, 1, 'Thursday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 23, 3, 'Regular Shift'),
(77, 1, 'Thursday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 11, 3, 'Regular Shift'),
(78, 1, 'Thursday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 9, 3, 'Regular Shift'),
(79, 1, 'Thursday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 5, 3, 'Regular Shift'),
(80, 1, 'Thursday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 8, 3, 'Regular Shift'),
(81, 1, 'Thursday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 3, 3, 'Regular Shift'),
(82, 1, 'Thursday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 2, 3, 'Regular Shift'),
(83, 1, 'Thursday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 4, 3, 'Regular Shift'),
(84, 1, 'Thursday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 12, 3, 'Regular Shift'),
(85, 1, 'Thursday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 7, 3, 'Regular Shift'),
(86, 1, 'Thursday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 10, 3, 'Regular Shift'),
(87, 1, 'Thursday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 24, 3, 'Regular Shift'),
(88, 1, 'Thursday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 15, 3, 'Regular Shift'),
(89, 1, 'Friday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 17, 3, 'Regular Shift'),
(90, 1, 'Friday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 1, 3, 'Regular Shift'),
(91, 1, 'Friday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 21, 3, 'Regular Shift'),
(92, 1, 'Friday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 14, 3, 'Regular Shift'),
(93, 1, 'Friday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 16, 3, 'Regular Shift'),
(94, 1, 'Friday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 25, 3, 'Regular Shift'),
(95, 1, 'Friday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 19, 3, 'Regular Shift'),
(96, 1, 'Friday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 20, 3, 'Regular Shift'),
(97, 1, 'Friday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 6, 3, 'Regular Shift'),
(98, 1, 'Friday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 23, 3, 'Regular Shift'),
(99, 1, 'Friday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 11, 3, 'Regular Shift'),
(100, 1, 'Friday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 9, 3, 'Regular Shift'),
(101, 1, 'Friday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 5, 3, 'Regular Shift'),
(102, 1, 'Friday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 8, 3, 'Regular Shift'),
(103, 1, 'Friday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 3, 3, 'Regular Shift'),
(104, 1, 'Friday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 2, 3, 'Regular Shift'),
(105, 1, 'Friday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 4, 3, 'Regular Shift'),
(106, 1, 'Friday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 12, 3, 'Regular Shift'),
(107, 1, 'Friday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 7, 3, 'Regular Shift'),
(108, 1, 'Friday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 10, 3, 'Regular Shift'),
(109, 1, 'Friday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 24, 3, 'Regular Shift'),
(110, 1, 'Friday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 15, 3, 'Regular Shift'),
(111, 1, 'Monday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 26, 3, 'Regular Shift'),
(112, 1, 'Tuesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 26, 3, 'Regular Shift'),
(113, 1, 'Wednesday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 26, 3, 'Regular Shift'),
(114, 1, 'Thursday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 26, 3, 'Regular Shift'),
(115, 1, 'Friday', '05:30 AM', '08:16 AM', '12:00 PM', '', '11:30 AM', '01:16 PM', '04:30 PM', '', 26, 3, 'Regular Shift'),
(116, 1, 'Saturday', '05:30 AM', '07:30 AM', '11:30 AM', '', '04:00 PM', '07:15 PM', '09:00 PM', '', 17, 3, 'Regular Shift'),
(117, 1, 'Saturday', '05:30 AM', '07:30 AM', '11:30 AM', '', '04:00 PM', '07:15 PM', '09:00 PM', '', 1, 3, 'Regular Shift'),
(118, 1, 'Saturday', '05:30 AM', '07:30 AM', '11:30 AM', '', '04:00 PM', '07:15 PM', '09:00 PM', '', 26, 3, 'Regular Shift'),
(119, 1, 'Saturday', '05:30 AM', '07:30 AM', '11:30 AM', '', '04:00 PM', '07:15 PM', '09:00 PM', '', 21, 3, 'Regular Shift'),
(120, 1, 'Saturday', '05:30 AM', '07:30 AM', '11:30 AM', '', '04:00 PM', '07:15 PM', '09:00 PM', '', 14, 3, 'Regular Shift'),
(121, 1, 'Saturday', '05:30 AM', '07:30 AM', '11:30 AM', '', '04:00 PM', '07:15 PM', '09:00 PM', '', 16, 3, 'Regular Shift'),
(122, 1, 'Saturday', '05:30 AM', '07:30 AM', '11:30 AM', '', '04:00 PM', '07:15 PM', '09:00 PM', '', 25, 3, 'Regular Shift'),
(123, 1, 'Saturday', '05:30 AM', '07:30 AM', '11:30 AM', '', '04:00 PM', '07:15 PM', '09:00 PM', '', 19, 3, 'Regular Shift'),
(124, 1, 'Saturday', '05:30 AM', '07:30 AM', '11:30 AM', '', '04:00 PM', '07:15 PM', '09:00 PM', '', 20, 3, 'Regular Shift'),
(125, 1, 'Saturday', '05:30 AM', '07:30 AM', '11:30 AM', '', '04:00 PM', '07:15 PM', '09:00 PM', '', 6, 3, 'Regular Shift'),
(126, 1, 'Saturday', '05:30 AM', '07:30 AM', '11:30 AM', '', '04:00 PM', '07:15 PM', '09:00 PM', '', 23, 3, 'Regular Shift'),
(127, 1, 'Saturday', '05:30 AM', '07:30 AM', '11:30 AM', '', '04:00 PM', '07:15 PM', '09:00 PM', '', 11, 3, 'Regular Shift'),
(128, 1, 'Saturday', '05:30 AM', '07:30 AM', '11:30 AM', '', '04:00 PM', '07:15 PM', '09:00 PM', '', 9, 3, 'Regular Shift'),
(129, 1, 'Saturday', '05:30 AM', '07:30 AM', '11:30 AM', '', '04:00 PM', '07:15 PM', '09:00 PM', '', 5, 3, 'Regular Shift'),
(130, 1, 'Saturday', '05:30 AM', '07:30 AM', '11:30 AM', '', '04:00 PM', '07:15 PM', '09:00 PM', '', 8, 3, 'Regular Shift'),
(131, 1, 'Saturday', '05:30 AM', '07:30 AM', '11:30 AM', '', '04:00 PM', '07:15 PM', '09:00 PM', '', 3, 3, 'Regular Shift'),
(132, 1, 'Saturday', '05:30 AM', '07:30 AM', '11:30 AM', '', '04:00 PM', '07:15 PM', '09:00 PM', '', 2, 3, 'Regular Shift'),
(133, 1, 'Saturday', '05:30 AM', '07:30 AM', '11:30 AM', '', '04:00 PM', '07:15 PM', '09:00 PM', '', 4, 3, 'Regular Shift'),
(134, 1, 'Saturday', '05:30 AM', '07:30 AM', '11:30 AM', '', '04:00 PM', '07:15 PM', '09:00 PM', '', 12, 3, 'Regular Shift'),
(135, 1, 'Saturday', '05:30 AM', '07:30 AM', '11:30 AM', '', '04:00 PM', '07:15 PM', '09:00 PM', '', 7, 3, 'Regular Shift'),
(136, 1, 'Saturday', '05:30 AM', '07:30 AM', '11:30 AM', '', '04:00 PM', '07:15 PM', '09:00 PM', '', 10, 3, 'Regular Shift'),
(137, 1, 'Saturday', '05:30 AM', '07:30 AM', '11:30 AM', '', '04:00 PM', '07:15 PM', '09:00 PM', '', 24, 3, 'Regular Shift'),
(138, 1, 'Saturday', '05:30 AM', '07:30 AM', '11:30 AM', '', '04:00 PM', '07:15 PM', '09:00 PM', '', 15, 3, 'Regular Shift');

-- --------------------------------------------------------

--
-- Table structure for table `travel_num_generator`
--

CREATE TABLE `travel_num_generator` (
  `pot_id` int(11) NOT NULL,
  `mm` varchar(2) NOT NULL,
  `sequence` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `travel_num_generator`
--

INSERT INTO `travel_num_generator` (`pot_id`, `mm`, `sequence`) VALUES
(1, '06', 92);

-- --------------------------------------------------------

--
-- Table structure for table `useraccount`
--

CREATE TABLE `useraccount` (
  `user_id` int(11) NOT NULL,
  `school_id` varchar(11) NOT NULL,
  `personnel_id` int(11) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `access` varchar(255) NOT NULL,
  `do_id` int(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `useraccount`
--

INSERT INTO `useraccount` (`user_id`, `school_id`, `personnel_id`, `fname`, `lname`, `email`, `username`, `password`, `access`, `do_id`) VALUES
(3, '1', 145, 'JING', 'HERRADURA', '', 'admin', 'a1Bz20ydqelm8m1wql21232f297a57a5a743894a0e4a801fc3', 'Admin', 1),
(4, '1', 140, 'JETTER', 'BARROCA', 'jefroxejet38@gmail.com', 'jetter', 'a1Bz20ydqelm8m1wql78828819ccbe6de15fe33440f497764a', 'User', 14);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_payroll_personnel_details`
-- (See below for the actual view)
--
CREATE TABLE `vw_payroll_personnel_details` (
`detail_id` int(11)
,`run_id` int(11)
,`run_name` varchar(150)
,`pay_period_start` date
,`pay_period_end` date
,`personnel_id` varchar(50)
,`personnel_name` text
,`dept_office_name` varchar(255)
,`designation_name` varchar(255)
,`gross_pay` decimal(10,2)
,`total_deductions` decimal(10,2)
,`total_employer_share` decimal(10,2)
,`net_pay` decimal(10,2)
,`payment_status` enum('pending','paid','hold','cancelled')
,`payment_method` enum('bank_transfer','check','cash','other')
,`payment_reference` varchar(100)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_payroll_run_summary`
-- (See below for the actual view)
--
CREATE TABLE `vw_payroll_run_summary` (
`run_id` int(11)
,`run_name` varchar(150)
,`run_type` enum('regular','special','13th_month','bonus','adjustment','custom')
,`pay_period_start` date
,`pay_period_end` date
,`payment_date` date
,`run_status` enum('draft','pending','approved','processing','completed','cancelled')
,`total_personnel` int(11)
,`total_gross` decimal(15,2)
,`total_deductions` decimal(15,2)
,`total_employer_share` decimal(15,2)
,`total_net_pay` decimal(15,2)
,`profile_name` varchar(100)
,`created_by_name` varchar(511)
,`approved_by_name` varchar(511)
,`approved_at` datetime
,`completed_at` datetime
,`created_at` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `yearly_dtr_summary`
--

CREATE TABLE `yearly_dtr_summary` (
  `yDTRs_id` int(11) NOT NULL,
  `personnel_id` int(11) NOT NULL,
  `ys_month` varchar(2) NOT NULL,
  `ys_year` varchar(4) NOT NULL,
  `day_present_AM` int(11) NOT NULL,
  `day_present_PM` int(11) NOT NULL,
  `day_present_Total` decimal(11,1) NOT NULL,
  `late_AM` int(11) NOT NULL,
  `late_PM` int(11) NOT NULL,
  `late_Total_num` int(11) NOT NULL,
  `late_Total_mins` int(11) NOT NULL,
  `late_Total_time` varchar(5) NOT NULL,
  `uTime_AM` int(11) NOT NULL,
  `uTime_PM` int(11) NOT NULL,
  `uTime_Total_num` int(11) NOT NULL,
  `uTime_Total_mins` int(11) NOT NULL,
  `uTime_Total_time` varchar(5) NOT NULL,
  `day_absent_AM` int(11) NOT NULL,
  `day_absent_PM` int(11) NOT NULL,
  `day_absent_Total` decimal(11,1) NOT NULL,
  `total_num_leave` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `yearly_dtr_summary`
--

INSERT INTO `yearly_dtr_summary` (`yDTRs_id`, `personnel_id`, `ys_month`, `ys_year`, `day_present_AM`, `day_present_PM`, `day_present_Total`, `late_AM`, `late_PM`, `late_Total_num`, `late_Total_mins`, `late_Total_time`, `uTime_AM`, `uTime_PM`, `uTime_Total_num`, `uTime_Total_mins`, `uTime_Total_time`, `day_absent_AM`, `day_absent_PM`, `day_absent_Total`, `total_num_leave`) VALUES
(1, 140, '05', '2026', 0, 1, 0.5, 0, 0, 0, 0, '00:00', 0, 1, 1, 195, '00:00', 20, 19, 19.5, 0),
(2, 145, '08', '2026', 0, 0, 0.0, 0, 0, 0, 0, '00:00', 0, 0, 0, 0, '00:00', 21, 21, 21.0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_signup_audit_logs`
--
ALTER TABLE `account_signup_audit_logs`
  ADD PRIMARY KEY (`audit_id`);

--
-- Indexes for table `activity_calendar`
--
ALTER TABLE `activity_calendar`
  ADD PRIMARY KEY (`activity_id`);

--
-- Indexes for table `backup_dbname`
--
ALTER TABLE `backup_dbname`
  ADD PRIMARY KEY (`backup_id`);

--
-- Indexes for table `client_computer`
--
ALTER TABLE `client_computer`
  ADD PRIMARY KEY (`client_id`);

--
-- Indexes for table `dept_offices`
--
ALTER TABLE `dept_offices`
  ADD PRIMARY KEY (`do_id`);

--
-- Indexes for table `designation`
--
ALTER TABLE `designation`
  ADD PRIMARY KEY (`des_id`);

--
-- Indexes for table `emp_status`
--
ALTER TABLE `emp_status`
  ADD PRIMARY KEY (`empStat_id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`file_id`);

--
-- Indexes for table `gass`
--
ALTER TABLE `gass`
  ADD PRIMARY KEY (`gass_id`);

--
-- Indexes for table `institution_preferences`
--
ALTER TABLE `institution_preferences`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lap_dates`
--
ALTER TABLE `lap_dates`
  ADD PRIMARY KEY (`lap_dates_id`);

--
-- Indexes for table `leave_applicants`
--
ALTER TABLE `leave_applicants`
  ADD PRIMARY KEY (`lap_id`);

--
-- Indexes for table `leave_applications`
--
ALTER TABLE `leave_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_personnel_id` (`personnel_id`),
  ADD KEY `idx_application_date` (`application_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_leave_card_entry` (`leave_card_entry_id`);

--
-- Indexes for table `leave_card`
--
ALTER TABLE `leave_card`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_created_from_app` (`created_from_application`);

--
-- Indexes for table `monthly_leave_credits_log`
--
ALTER TABLE `monthly_leave_credits_log`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_personnel_month` (`personnel_id`,`year`,`month`),
  ADD KEY `idx_year_month` (`year`,`month`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`news_id`);

--
-- Indexes for table `personnels`
--
ALTER TABLE `personnels`
  ADD PRIMARY KEY (`personnel_id`);

--
-- Indexes for table `personnel_educ_bg`
--
ALTER TABLE `personnel_educ_bg`
  ADD PRIMARY KEY (`eb_id`);

--
-- Indexes for table `personnel_fam_bg`
--
ALTER TABLE `personnel_fam_bg`
  ADD PRIMARY KEY (`fm_id`);

--
-- Indexes for table `personnel_file_audit_logs`
--
ALTER TABLE `personnel_file_audit_logs`
  ADD PRIMARY KEY (`audit_id`),
  ADD KEY `idx_pfa_target_personnel` (`target_personnel_id`),
  ADD KEY `idx_pfa_action_name` (`action_name`);

--
-- Indexes for table `personnel_file_folders`
--
ALTER TABLE `personnel_file_folders`
  ADD PRIMARY KEY (`folder_id`),
  ADD UNIQUE KEY `uq_personnel_folder_slug` (`personnel_id`,`folder_slug`);

--
-- Indexes for table `personnel_logs`
--
ALTER TABLE `personnel_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `personnel_official_travel_logs`
--
ALTER TABLE `personnel_official_travel_logs`
  ADD PRIMARY KEY (`travel_log_id`);

--
-- Indexes for table `personnel_seminars`
--
ALTER TABLE `personnel_seminars`
  ADD PRIMARY KEY (`ps_id`);

--
-- Indexes for table `pr_tbl_deductions`
--
ALTER TABLE `pr_tbl_deductions`
  ADD PRIMARY KEY (`deduction_id`);

--
-- Indexes for table `pr_tbl_income`
--
ALTER TABLE `pr_tbl_income`
  ADD PRIMARY KEY (`income_id`);

--
-- Indexes for table `pr_tbl_payroll_audit_log`
--
ALTER TABLE `pr_tbl_payroll_audit_log`
  ADD PRIMARY KEY (`audit_id`),
  ADD KEY `idx_run_id` (`run_id`),
  ADD KEY `idx_detail_id` (`detail_id`),
  ADD KEY `idx_action_type` (`action_type`),
  ADD KEY `idx_table_name` (`table_name`),
  ADD KEY `idx_performed_at` (`performed_at`);

--
-- Indexes for table `pr_tbl_payroll_profiles`
--
ALTER TABLE `pr_tbl_payroll_profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD UNIQUE KEY `unique_profile_name` (`profile_name`),
  ADD KEY `idx_profile_type` (`profile_type`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_is_default` (`is_default`);

--
-- Indexes for table `pr_tbl_payroll_profile_deductions`
--
ALTER TABLE `pr_tbl_payroll_profile_deductions`
  ADD PRIMARY KEY (`profile_deduction_id`),
  ADD UNIQUE KEY `unique_profile_deduction` (`profile_id`,`deduction_id`),
  ADD KEY `idx_profile_id` (`profile_id`),
  ADD KEY `idx_deduction_id` (`deduction_id`),
  ADD KEY `idx_display_order` (`display_order`);

--
-- Indexes for table `pr_tbl_payroll_profile_filters`
--
ALTER TABLE `pr_tbl_payroll_profile_filters`
  ADD PRIMARY KEY (`filter_id`),
  ADD KEY `idx_profile_id` (`profile_id`),
  ADD KEY `idx_filter_type` (`filter_type`);

--
-- Indexes for table `pr_tbl_payroll_profile_income`
--
ALTER TABLE `pr_tbl_payroll_profile_income`
  ADD PRIMARY KEY (`profile_income_id`),
  ADD UNIQUE KEY `unique_profile_income` (`profile_id`,`income_id`),
  ADD KEY `idx_profile_id` (`profile_id`),
  ADD KEY `idx_income_id` (`income_id`),
  ADD KEY `idx_display_order` (`display_order`);

--
-- Indexes for table `pr_tbl_payroll_runs`
--
ALTER TABLE `pr_tbl_payroll_runs`
  ADD PRIMARY KEY (`run_id`),
  ADD KEY `idx_profile_id` (`profile_id`),
  ADD KEY `idx_run_status` (`run_status`),
  ADD KEY `idx_run_type` (`run_type`),
  ADD KEY `idx_pay_period` (`pay_period_start`,`pay_period_end`),
  ADD KEY `idx_payment_date` (`payment_date`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_run_status_date` (`run_status`,`pay_period_start`,`pay_period_end`);

--
-- Indexes for table `pr_tbl_payroll_run_deductions`
--
ALTER TABLE `pr_tbl_payroll_run_deductions`
  ADD PRIMARY KEY (`run_deduction_id`),
  ADD KEY `idx_detail_id` (`detail_id`),
  ADD KEY `idx_run_id` (`run_id`),
  ADD KEY `idx_personnel_id` (`personnel_id`),
  ADD KEY `idx_deduction_id` (`deduction_id`);

--
-- Indexes for table `pr_tbl_payroll_run_details`
--
ALTER TABLE `pr_tbl_payroll_run_details`
  ADD PRIMARY KEY (`detail_id`),
  ADD UNIQUE KEY `unique_run_personnel` (`run_id`,`personnel_id`),
  ADD KEY `idx_run_id` (`run_id`),
  ADD KEY `idx_personnel_id` (`personnel_id`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_detail_status` (`payment_status`,`run_id`);

--
-- Indexes for table `pr_tbl_payroll_run_income`
--
ALTER TABLE `pr_tbl_payroll_run_income`
  ADD PRIMARY KEY (`run_income_id`),
  ADD KEY `idx_detail_id` (`detail_id`),
  ADD KEY `idx_run_id` (`run_id`),
  ADD KEY `idx_personnel_id` (`personnel_id`),
  ADD KEY `idx_income_id` (`income_id`);

--
-- Indexes for table `pr_tbl_payroll_snapshots`
--
ALTER TABLE `pr_tbl_payroll_snapshots`
  ADD PRIMARY KEY (`snapshot_id`),
  ADD KEY `idx_run_id` (`run_id`),
  ADD KEY `idx_snapshot_type` (`snapshot_type`),
  ADD KEY `idx_snapshot_date` (`snapshot_date`),
  ADD KEY `idx_group_by` (`snapshot_type`,`group_by_value`),
  ADD KEY `idx_snapshot_run_type` (`run_id`,`snapshot_type`);

--
-- Indexes for table `pr_tbl_payroll_snapshot_items`
--
ALTER TABLE `pr_tbl_payroll_snapshot_items`
  ADD PRIMARY KEY (`snapshot_item_id`),
  ADD KEY `idx_snapshot_id` (`snapshot_id`),
  ADD KEY `idx_run_id` (`run_id`),
  ADD KEY `idx_item_type` (`item_type`),
  ADD KEY `idx_item_id` (`item_id`);

--
-- Indexes for table `pr_tbl_pay_pro_personnels`
--
ALTER TABLE `pr_tbl_pay_pro_personnels`
  ADD PRIMARY KEY (`ppp_id`);

--
-- Indexes for table `pr_tbl_personnel_deductions`
--
ALTER TABLE `pr_tbl_personnel_deductions`
  ADD PRIMARY KEY (`personnel_deduction_id`),
  ADD UNIQUE KEY `unique_personnel_deduction` (`personnel_id`,`deduction_id`),
  ADD KEY `idx_personnel_id` (`personnel_id`),
  ADD KEY `idx_deduction_id` (`deduction_id`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `pr_tbl_personnel_income`
--
ALTER TABLE `pr_tbl_personnel_income`
  ADD PRIMARY KEY (`personnel_income_id`),
  ADD UNIQUE KEY `unique_personnel_income` (`personnel_id`,`income_id`),
  ADD KEY `idx_personnel_id` (`personnel_id`),
  ADD KEY `idx_income_id` (`income_id`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `pr_tbl_signatory_items`
--
ALTER TABLE `pr_tbl_signatory_items`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `pr_tbl_signatory_templates`
--
ALTER TABLE `pr_tbl_signatory_templates`
  ADD PRIMARY KEY (`template_id`);

--
-- Indexes for table `service_record`
--
ALTER TABLE `service_record`
  ADD PRIMARY KEY (`sr_id`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`shift_id`);

--
-- Indexes for table `signatories_settings`
--
ALTER TABLE `signatories_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `slides`
--
ALTER TABLE `slides`
  ADD PRIMARY KEY (`slide_id`);

--
-- Indexes for table `time_schedules`
--
ALTER TABLE `time_schedules`
  ADD PRIMARY KEY (`schedule_id`);

--
-- Indexes for table `travel_num_generator`
--
ALTER TABLE `travel_num_generator`
  ADD PRIMARY KEY (`pot_id`);

--
-- Indexes for table `useraccount`
--
ALTER TABLE `useraccount`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `yearly_dtr_summary`
--
ALTER TABLE `yearly_dtr_summary`
  ADD PRIMARY KEY (`yDTRs_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account_signup_audit_logs`
--
ALTER TABLE `account_signup_audit_logs`
  MODIFY `audit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `activity_calendar`
--
ALTER TABLE `activity_calendar`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `backup_dbname`
--
ALTER TABLE `backup_dbname`
  MODIFY `backup_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `client_computer`
--
ALTER TABLE `client_computer`
  MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `dept_offices`
--
ALTER TABLE `dept_offices`
  MODIFY `do_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `designation`
--
ALTER TABLE `designation`
  MODIFY `des_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT for table `emp_status`
--
ALTER TABLE `emp_status`
  MODIFY `empStat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gass`
--
ALTER TABLE `gass`
  MODIFY `gass_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `institution_preferences`
--
ALTER TABLE `institution_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lap_dates`
--
ALTER TABLE `lap_dates`
  MODIFY `lap_dates_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_applicants`
--
ALTER TABLE `leave_applicants`
  MODIFY `lap_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `leave_applications`
--
ALTER TABLE `leave_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `leave_card`
--
ALTER TABLE `leave_card`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=793;

--
-- AUTO_INCREMENT for table `monthly_leave_credits_log`
--
ALTER TABLE `monthly_leave_credits_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `news_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `personnels`
--
ALTER TABLE `personnels`
  MODIFY `personnel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=678;

--
-- AUTO_INCREMENT for table `personnel_educ_bg`
--
ALTER TABLE `personnel_educ_bg`
  MODIFY `eb_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `personnel_fam_bg`
--
ALTER TABLE `personnel_fam_bg`
  MODIFY `fm_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `personnel_file_audit_logs`
--
ALTER TABLE `personnel_file_audit_logs`
  MODIFY `audit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personnel_file_folders`
--
ALTER TABLE `personnel_file_folders`
  MODIFY `folder_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=668;

--
-- AUTO_INCREMENT for table `personnel_logs`
--
ALTER TABLE `personnel_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1651;

--
-- AUTO_INCREMENT for table `personnel_official_travel_logs`
--
ALTER TABLE `personnel_official_travel_logs`
  MODIFY `travel_log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personnel_seminars`
--
ALTER TABLE `personnel_seminars`
  MODIFY `ps_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pr_tbl_deductions`
--
ALTER TABLE `pr_tbl_deductions`
  MODIFY `deduction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pr_tbl_income`
--
ALTER TABLE `pr_tbl_income`
  MODIFY `income_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pr_tbl_payroll_audit_log`
--
ALTER TABLE `pr_tbl_payroll_audit_log`
  MODIFY `audit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `pr_tbl_payroll_profiles`
--
ALTER TABLE `pr_tbl_payroll_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pr_tbl_payroll_profile_deductions`
--
ALTER TABLE `pr_tbl_payroll_profile_deductions`
  MODIFY `profile_deduction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pr_tbl_payroll_profile_filters`
--
ALTER TABLE `pr_tbl_payroll_profile_filters`
  MODIFY `filter_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pr_tbl_payroll_profile_income`
--
ALTER TABLE `pr_tbl_payroll_profile_income`
  MODIFY `profile_income_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pr_tbl_payroll_runs`
--
ALTER TABLE `pr_tbl_payroll_runs`
  MODIFY `run_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pr_tbl_payroll_run_deductions`
--
ALTER TABLE `pr_tbl_payroll_run_deductions`
  MODIFY `run_deduction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6817;

--
-- AUTO_INCREMENT for table `pr_tbl_payroll_run_details`
--
ALTER TABLE `pr_tbl_payroll_run_details`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6321;

--
-- AUTO_INCREMENT for table `pr_tbl_payroll_run_income`
--
ALTER TABLE `pr_tbl_payroll_run_income`
  MODIFY `run_income_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8319;

--
-- AUTO_INCREMENT for table `pr_tbl_payroll_snapshots`
--
ALTER TABLE `pr_tbl_payroll_snapshots`
  MODIFY `snapshot_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=237;

--
-- AUTO_INCREMENT for table `pr_tbl_payroll_snapshot_items`
--
ALTER TABLE `pr_tbl_payroll_snapshot_items`
  MODIFY `snapshot_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `pr_tbl_pay_pro_personnels`
--
ALTER TABLE `pr_tbl_pay_pro_personnels`
  MODIFY `ppp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pr_tbl_personnel_deductions`
--
ALTER TABLE `pr_tbl_personnel_deductions`
  MODIFY `personnel_deduction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pr_tbl_personnel_income`
--
ALTER TABLE `pr_tbl_personnel_income`
  MODIFY `personnel_income_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pr_tbl_signatory_items`
--
ALTER TABLE `pr_tbl_signatory_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `pr_tbl_signatory_templates`
--
ALTER TABLE `pr_tbl_signatory_templates`
  MODIFY `template_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `service_record`
--
ALTER TABLE `service_record`
  MODIFY `sr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=229;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `shift_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `signatories_settings`
--
ALTER TABLE `signatories_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `slides`
--
ALTER TABLE `slides`
  MODIFY `slide_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `time_schedules`
--
ALTER TABLE `time_schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- AUTO_INCREMENT for table `travel_num_generator`
--
ALTER TABLE `travel_num_generator`
  MODIFY `pot_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `useraccount`
--
ALTER TABLE `useraccount`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `yearly_dtr_summary`
--
ALTER TABLE `yearly_dtr_summary`
  MODIFY `yDTRs_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

-- --------------------------------------------------------

--
-- Structure for view `vw_payroll_personnel_details`
--
DROP TABLE IF EXISTS `vw_payroll_personnel_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_payroll_personnel_details`  AS SELECT `prd`.`detail_id` AS `detail_id`, `prd`.`run_id` AS `run_id`, `pr`.`run_name` AS `run_name`, `pr`.`pay_period_start` AS `pay_period_start`, `pr`.`pay_period_end` AS `pay_period_end`, `prd`.`personnel_id` AS `personnel_id`, concat(`p`.`fname`,' ',ifnull(concat(substr(`p`.`mname`,1,1),'. '),''),`p`.`lname`) AS `personnel_name`, `d`.`dept_office_name` AS `dept_office_name`, `des`.`des_name` AS `designation_name`, `prd`.`gross_pay` AS `gross_pay`, `prd`.`total_deductions` AS `total_deductions`, `prd`.`total_employer_share` AS `total_employer_share`, `prd`.`net_pay` AS `net_pay`, `prd`.`payment_status` AS `payment_status`, `prd`.`payment_method` AS `payment_method`, `prd`.`payment_reference` AS `payment_reference` FROM ((((`pr_tbl_payroll_run_details` `prd` join `pr_tbl_payroll_runs` `pr` on(`prd`.`run_id` = `pr`.`run_id`)) join `personnels` `p` on(`prd`.`personnel_id` = `p`.`personnel_id`)) left join `dept_offices` `d` on(`p`.`do_id` = `d`.`do_id`)) left join `designation` `des` on(`p`.`des_id` = `des`.`des_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `vw_payroll_run_summary`
--
DROP TABLE IF EXISTS `vw_payroll_run_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_payroll_run_summary`  AS SELECT `pr`.`run_id` AS `run_id`, `pr`.`run_name` AS `run_name`, `pr`.`run_type` AS `run_type`, `pr`.`pay_period_start` AS `pay_period_start`, `pr`.`pay_period_end` AS `pay_period_end`, `pr`.`payment_date` AS `payment_date`, `pr`.`run_status` AS `run_status`, `pr`.`total_personnel` AS `total_personnel`, `pr`.`total_gross` AS `total_gross`, `pr`.`total_deductions` AS `total_deductions`, `pr`.`total_employer_share` AS `total_employer_share`, `pr`.`total_net_pay` AS `total_net_pay`, `pp`.`profile_name` AS `profile_name`, concat(`u1`.`fname`,' ',`u1`.`lname`) AS `created_by_name`, concat(`u2`.`fname`,' ',`u2`.`lname`) AS `approved_by_name`, `pr`.`approved_at` AS `approved_at`, `pr`.`completed_at` AS `completed_at`, `pr`.`created_at` AS `created_at` FROM (((`pr_tbl_payroll_runs` `pr` left join `pr_tbl_payroll_profiles` `pp` on(`pr`.`profile_id` = `pp`.`profile_id`)) left join `useraccount` `u1` on(`pr`.`created_by` = `u1`.`user_id`)) left join `useraccount` `u2` on(`pr`.`approved_by` = `u2`.`user_id`)) ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `leave_applications`
--
ALTER TABLE `leave_applications`
  ADD CONSTRAINT `fk_leave_app_personnel` FOREIGN KEY (`personnel_id`) REFERENCES `personnels` (`personnel_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
