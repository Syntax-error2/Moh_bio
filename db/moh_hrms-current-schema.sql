-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 26, 2026 at 07:41 AM
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

-- --------------------------------------------------------

--
-- Table structure for table `dept_offices`
--

CREATE TABLE `dept_offices` (
  `do_id` int(11) NOT NULL,
  `dept_office_name` varchar(255) NOT NULL,
  `officeHead_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `designation`
--

CREATE TABLE `designation` (
  `des_id` int(11) NOT NULL,
  `des_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `file_id` int(11) NOT NULL,
  `personnel_id` int(11) NOT NULL,
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
  `separation_date` varchar(10) NOT NULL,
  `num_of_yrs` int(11) NOT NULL,
  `tin_num` varchar(25) NOT NULL,
  `gsis_num` varchar(25) NOT NULL,
  `pagibig_num` varchar(25) NOT NULL,
  `philHealth_num` varchar(25) NOT NULL,
  `monthly_salary` decimal(14,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `travel_num_generator`
--

CREATE TABLE `travel_num_generator` (
  `pot_id` int(11) NOT NULL,
  `mm` varchar(2) NOT NULL,
  `sequence` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `activity_calendar`
--
ALTER TABLE `activity_calendar`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `backup_dbname`
--
ALTER TABLE `backup_dbname`
  MODIFY `backup_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `client_computer`
--
ALTER TABLE `client_computer`
  MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dept_offices`
--
ALTER TABLE `dept_offices`
  MODIFY `do_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `designation`
--
ALTER TABLE `designation`
  MODIFY `des_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emp_status`
--
ALTER TABLE `emp_status`
  MODIFY `empStat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gass`
--
ALTER TABLE `gass`
  MODIFY `gass_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `institution_preferences`
--
ALTER TABLE `institution_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lap_dates`
--
ALTER TABLE `lap_dates`
  MODIFY `lap_dates_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_applicants`
--
ALTER TABLE `leave_applicants`
  MODIFY `lap_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_applications`
--
ALTER TABLE `leave_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_card`
--
ALTER TABLE `leave_card`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `monthly_leave_credits_log`
--
ALTER TABLE `monthly_leave_credits_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `news_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personnels`
--
ALTER TABLE `personnels`
  MODIFY `personnel_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personnel_educ_bg`
--
ALTER TABLE `personnel_educ_bg`
  MODIFY `eb_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personnel_fam_bg`
--
ALTER TABLE `personnel_fam_bg`
  MODIFY `fm_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personnel_logs`
--
ALTER TABLE `personnel_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personnel_official_travel_logs`
--
ALTER TABLE `personnel_official_travel_logs`
  MODIFY `travel_log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personnel_seminars`
--
ALTER TABLE `personnel_seminars`
  MODIFY `ps_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pr_tbl_deductions`
--
ALTER TABLE `pr_tbl_deductions`
  MODIFY `deduction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pr_tbl_income`
--
ALTER TABLE `pr_tbl_income`
  MODIFY `income_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pr_tbl_payroll_audit_log`
--
ALTER TABLE `pr_tbl_payroll_audit_log`
  MODIFY `audit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pr_tbl_payroll_profiles`
--
ALTER TABLE `pr_tbl_payroll_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pr_tbl_payroll_profile_deductions`
--
ALTER TABLE `pr_tbl_payroll_profile_deductions`
  MODIFY `profile_deduction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pr_tbl_payroll_profile_filters`
--
ALTER TABLE `pr_tbl_payroll_profile_filters`
  MODIFY `filter_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pr_tbl_payroll_profile_income`
--
ALTER TABLE `pr_tbl_payroll_profile_income`
  MODIFY `profile_income_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pr_tbl_payroll_runs`
--
ALTER TABLE `pr_tbl_payroll_runs`
  MODIFY `run_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pr_tbl_payroll_run_deductions`
--
ALTER TABLE `pr_tbl_payroll_run_deductions`
  MODIFY `run_deduction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pr_tbl_payroll_run_details`
--
ALTER TABLE `pr_tbl_payroll_run_details`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pr_tbl_payroll_run_income`
--
ALTER TABLE `pr_tbl_payroll_run_income`
  MODIFY `run_income_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pr_tbl_payroll_snapshots`
--
ALTER TABLE `pr_tbl_payroll_snapshots`
  MODIFY `snapshot_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pr_tbl_payroll_snapshot_items`
--
ALTER TABLE `pr_tbl_payroll_snapshot_items`
  MODIFY `snapshot_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pr_tbl_pay_pro_personnels`
--
ALTER TABLE `pr_tbl_pay_pro_personnels`
  MODIFY `ppp_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pr_tbl_personnel_deductions`
--
ALTER TABLE `pr_tbl_personnel_deductions`
  MODIFY `personnel_deduction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pr_tbl_personnel_income`
--
ALTER TABLE `pr_tbl_personnel_income`
  MODIFY `personnel_income_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_record`
--
ALTER TABLE `service_record`
  MODIFY `sr_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `shift_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `signatories_settings`
--
ALTER TABLE `signatories_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `slides`
--
ALTER TABLE `slides`
  MODIFY `slide_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `time_schedules`
--
ALTER TABLE `time_schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `travel_num_generator`
--
ALTER TABLE `travel_num_generator`
  MODIFY `pot_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `useraccount`
--
ALTER TABLE `useraccount`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `yearly_dtr_summary`
--
ALTER TABLE `yearly_dtr_summary`
  MODIFY `yDTRs_id` int(11) NOT NULL AUTO_INCREMENT;

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
