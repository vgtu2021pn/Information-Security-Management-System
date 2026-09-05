-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 30, 2026 at 08:54 PM
-- Wersja serwera: 10.4.34-MariaDB-cll-lve
-- Wersja PHP: 8.1.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `isms_database`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `affected_controls_a`
--

DROP TABLE IF EXISTS `affected_controls_a`;
CREATE TABLE `affected_controls_a` (
  `un` bigint(18) UNSIGNED NOT NULL,
  `statement_of_applicability_un` bigint(16) UNSIGNED NOT NULL COMMENT 'From the statement_of_applicability table',
  `evidence_un` bigint(16) UNSIGNED NOT NULL COMMENT 'From the evidence table'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='List of affected control measures';

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `ca_evidence_a`
--

DROP TABLE IF EXISTS `ca_evidence_a`;
CREATE TABLE `ca_evidence_a` (
  `un` bigint(18) UNSIGNED NOT NULL,
  `evidence_un` bigint(16) UNSIGNED NOT NULL COMMENT 'From the evidence table',
  `ca_un` bigint(16) UNSIGNED NOT NULL COMMENT 'From the corrective_action table'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Corrective actions of evidence.';

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `ca_procedures_a`
--

DROP TABLE IF EXISTS `ca_procedures_a`;
CREATE TABLE `ca_procedures_a` (
  `un` bigint(18) UNSIGNED NOT NULL,
  `procedures_un` bigint(16) UNSIGNED NOT NULL COMMENT 'From the procedures table',
  `ca_un` bigint(16) UNSIGNED NOT NULL COMMENT 'From the corrective_action table'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='For corrective actions by taking into account procedures.';

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `corrective_action`
--

DROP TABLE IF EXISTS `corrective_action`;
CREATE TABLE `corrective_action` (
  `corrective_action_id` bigint(16) UNSIGNED NOT NULL,
  `found_issues` text NOT NULL,
  `root_cause` text NOT NULL,
  `corrective_action` text NOT NULL,
  `owner_name` char(150) NOT NULL,
  `due_date` date NOT NULL,
  `status` enum('1','2','3') NOT NULL COMMENT '1 - open, 2 - partial, 3 - closed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='It used for dealing with evidence. Links with procedures.';

--
-- Zrzut danych tabeli `corrective_action`
--

INSERT INTO `corrective_action` (`corrective_action_id`, `found_issues`, `root_cause`, `corrective_action`, `owner_name`, `due_date`, `status`) VALUES
(1, 'Company\'s equipment does not meet company\'s inventorization needs.', 'Procedure for inventorization was not been established. No hardware & software support for maintaining inventorization process.', 'Management department must show their lead into proposing inventorization procedure. In the process hardware & software support for implementing inventorization must be established.', 'Management department', '2026-10-30', '1'),
(2, 'Operations department does not have employee representative for briefing other employees.', 'Shortage of knowledgeable employees regarding operations system.', 'Management department must request from the vendor of operations system for additional hours to prepare in briefing a new employee from Operations department.', 'Management department', '2026-11-27', '1'),
(3, '3D printed equipment does not contain unique identification information.', '3D printing technology does not include inventorization f-ty option.', 'To decide for laser logo & inventorization information imbue option .', 'Operations department', '2026-10-30', '1');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `evidence`
--

DROP TABLE IF EXISTS `evidence`;
CREATE TABLE `evidence` (
  `artifact_id` bigint(16) UNSIGNED NOT NULL,
  `artifact_type` varchar(100) NOT NULL COMMENT 'screen capture file, export file, meeting record, vendor report, hash tag of service...',
  `explanation_of_artifact` text NOT NULL,
  `date_of_artifact` date NOT NULL,
  `artifact_owner` char(150) NOT NULL,
  `description_of_artifact_storage` text NOT NULL COMMENT 'cloud medium, non-cloud medium...',
  `integrity_data_of_artifact` char(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Information about artifacts are kept in the evidence table. It gonna create an impact to specific control measures from the statement_of_applicability table.';

--
-- Zrzut danych tabeli `evidence`
--

INSERT INTO `evidence` (`artifact_id`, `artifact_type`, `explanation_of_artifact`, `date_of_artifact`, `artifact_owner`, `description_of_artifact_storage`, `integrity_data_of_artifact`) VALUES
(1, 'operations system software (single archived file)', 'Vendor software', '2026-08-20', 'Management department', 'Original Software is kept in the Management department.\r\nBackup is in use in the Operations department.', 'HASH NEEDED'),
(2, 'operations system software updates', 'Vendor software. Update No. 2026-08-20.', '2026-08-20', 'Operations department', 'Operations department are conducting Software updates according provided Vendor\'s documentation. Backup versions are kept in Cloud Store of Management department.', 'HASH NEEDED'),
(3, '3D print software & hardware', 'Vendor: 3D Print World\r\nSite: www.3dprintworld.hostname.lt\r\nHardware: Version A\r\nIntegrity: Version A-Soft', '2026-08-20', 'Operations department', 'In an Operations department\'s perimeter.', 'HASH NEEDED'),
(4, 'water system pipe at scale', 'Vendor: Old watering solution\r\nAge: 23 Years', '2026-08-20', 'Operations department', 'No reserves. All in-use.', 'not applicable');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `evidence_set_a`
--

DROP TABLE IF EXISTS `evidence_set_a`;
CREATE TABLE `evidence_set_a` (
  `un` bigint(18) UNSIGNED NOT NULL,
  `evidence_un` bigint(16) UNSIGNED NOT NULL COMMENT 'From the evidence table',
  `statement_of_applicability_un` bigint(16) UNSIGNED NOT NULL COMMENT 'From the statement_of_applicability table'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Evidence set of the control measure';

--
-- Zrzut danych tabeli `evidence_set_a`
--

INSERT INTO `evidence_set_a` (`un`, `evidence_un`, `statement_of_applicability_un`) VALUES
(1, 2, 4);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `implemented_controls_a`
--

DROP TABLE IF EXISTS `implemented_controls_a`;
CREATE TABLE `implemented_controls_a` (
  `un` bigint(18) UNSIGNED NOT NULL,
  `risk_register_un` bigint(16) UNSIGNED NOT NULL COMMENT 'From the risk_register table',
  `statement_of_applicability_un` bigint(16) UNSIGNED NOT NULL COMMENT 'From the statement_of_applicability table',
  `risk_change` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='List of implemented control measures for risk reduction';

--
-- Zrzut danych tabeli `implemented_controls_a`
--

INSERT INTO `implemented_controls_a` (`un`, `risk_register_un`, `statement_of_applicability_un`, `risk_change`) VALUES
(1, 1, 1, -3),
(3, 4, 4, -2),
(4, 5, 5, -2),
(5, 1, 6, -2),
(15, 3, 4, -1),
(16, 3, 3, -2);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `procedures`
--

DROP TABLE IF EXISTS `procedures`;
CREATE TABLE `procedures` (
  `document_un` bigint(16) UNSIGNED NOT NULL,
  `document_type` enum('1','2','3','4','5','6','7') NOT NULL DEFAULT '1' COMMENT '1 - incident report, 2 - policy, 3 - procedure, 4 - standard, 5 - guideline, 6 - template, 7 - audit',
  `document_language` enum('en','lt','pl') NOT NULL DEFAULT 'en',
  `document_name` varchar(255) NOT NULL,
  `document_status` enum('1','2','3') NOT NULL DEFAULT '1' COMMENT '1 - draft, 2 - approved, 3 - obsolete',
  `document_owner` char(150) NOT NULL,
  `review_date` date DEFAULT NULL,
  `review_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 - not reviewed, 1 - reviewed',
  `following_review_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='All important procedures that are documented by their owners. Procedures are related to control measures.';

--
-- Zrzut danych tabeli `procedures`
--

INSERT INTO `procedures` (`document_un`, `document_type`, `document_language`, `document_name`, `document_status`, `document_owner`, `review_date`, `review_status`, `following_review_date`) VALUES
(1, '5', 'en', 'Operations Software User Guide', '2', 'Management department', NULL, 0, '2027-08-20'),
(2, '2', 'en', 'Fire prevention ', '2', 'Section of Safety & Health at Work', NULL, 0, '2027-08-20'),
(3, '2', 'en', 'Evacuation Planning, Safety and Communication', '2', 'Section of Safety & Health at Work', NULL, 0, '2027-08-20'),
(4, '2', 'en', 'Disaster Recovery', '2', 'Operations department', NULL, 0, '2027-08-20'),
(5, '2', 'en', 'Supply chain & resource planning', '2', 'Management department', NULL, 0, '2027-08-20'),
(6, '5', 'en', 'Operations Software Safe use of operations system', '2', 'Section of Safety & Health at Work', NULL, 0, '2027-08-20'),
(7, '3', 'en', 'Operations System Maintenance', '2', 'Operations department', NULL, 0, '2027-08-20'),
(8, '3', 'en', 'Worktime Tabel Management', '2', 'Management department', NULL, 0, '2027-08-20');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `procedures_change_log`
--

DROP TABLE IF EXISTS `procedures_change_log`;
CREATE TABLE `procedures_change_log` (
  `un` bigint(18) UNSIGNED NOT NULL,
  `procedures_un` bigint(16) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `version` varchar(10) NOT NULL,
  `main_changes` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Change log of Procedures';

--
-- Zrzut danych tabeli `procedures_change_log`
--

INSERT INTO `procedures_change_log` (`un`, `procedures_un`, `date`, `version`, `main_changes`) VALUES
(1, 1, '2026-08-14', '1.55', 'First version: 1.55.'),
(2, 6, '2026-08-14', '1.55', 'First version: 1.55.');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `related_controls_a`
--

DROP TABLE IF EXISTS `related_controls_a`;
CREATE TABLE `related_controls_a` (
  `un` bigint(18) UNSIGNED NOT NULL,
  `statement_of_applicability_un` bigint(16) UNSIGNED NOT NULL COMMENT 'From the statement_of_applicability table',
  `procedures_un` bigint(16) UNSIGNED NOT NULL COMMENT 'From the procedures table'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='List of related control measures.';

--
-- Zrzut danych tabeli `related_controls_a`
--

INSERT INTO `related_controls_a` (`un`, `statement_of_applicability_un`, `procedures_un`) VALUES
(1, 1, 8),
(2, 2, 2),
(3, 2, 3),
(4, 2, 4),
(5, 3, 1),
(6, 3, 6),
(7, 4, 7),
(8, 8, 3);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `request`
--

DROP TABLE IF EXISTS `request`;
CREATE TABLE `request` (
  `un` bigint(16) UNSIGNED NOT NULL,
  `user_uno` bigint(16) UNSIGNED NOT NULL,
  `code` char(96) NOT NULL,
  `info` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Struktura tabeli dla tabeli `review`
--

DROP TABLE IF EXISTS `review`;
CREATE TABLE `review` (
  `review_un` bigint(16) UNSIGNED NOT NULL,
  `review_name` varchar(200) NOT NULL,
  `review_agenda` text NOT NULL,
  `review_date` date NOT NULL,
  `review_status` tinyint(1) NOT NULL COMMENT '0 - not reviewed, 1 - reviewed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Reviews for the Risk Register, Controls & Procedures.';

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `review_controls_a`
--

DROP TABLE IF EXISTS `review_controls_a`;
CREATE TABLE `review_controls_a` (
  `un` bigint(18) UNSIGNED NOT NULL,
  `statement_of_applicability_un` bigint(16) UNSIGNED NOT NULL COMMENT 'From the statement_of_applicability table',
  `review_un` bigint(16) UNSIGNED NOT NULL COMMENT 'From the review table'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='For reviewing control measures.';

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `review_procedures_a`
--

DROP TABLE IF EXISTS `review_procedures_a`;
CREATE TABLE `review_procedures_a` (
  `un` bigint(18) UNSIGNED NOT NULL,
  `procedures_un` bigint(16) UNSIGNED NOT NULL COMMENT 'From the procedures table',
  `review_un` bigint(16) UNSIGNED NOT NULL COMMENT 'From the review table'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='For reviewing procedures.';

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `review_risks_a`
--

DROP TABLE IF EXISTS `review_risks_a`;
CREATE TABLE `review_risks_a` (
  `un` bigint(18) UNSIGNED NOT NULL,
  `risk_register_un` bigint(16) UNSIGNED NOT NULL COMMENT 'From the risk_register table',
  `review_un` bigint(16) UNSIGNED NOT NULL COMMENT 'From the review table'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='For reviewing risks.';

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `risk_register`
--

DROP TABLE IF EXISTS `risk_register`;
CREATE TABLE `risk_register` (
  `risk_un` bigint(16) UNSIGNED NOT NULL,
  `risk_type` enum('1','2','3') NOT NULL DEFAULT '1' COMMENT '1 - asset, 2 - process, 3 - system',
  `risk_name` char(175) NOT NULL,
  `threat_exp` text NOT NULL,
  `vulnerability_exp` text NOT NULL,
  `impact_v` tinyint(1) NOT NULL DEFAULT 0,
  `likelihood_v` tinyint(1) NOT NULL DEFAULT 0,
  `risk_level_p` enum('+','*') NOT NULL DEFAULT '+' COMMENT '+ for addition, * for multiplication',
  `risk_owner_name` char(150) NOT NULL,
  `treatment_decision` enum('1','2','3','4') NOT NULL DEFAULT '1' COMMENT '1 - fix, 2 - accept, 3 - avoid, 4 - transfer',
  `treatment_plan` varchar(255) NOT NULL,
  `review_date` date DEFAULT NULL,
  `review_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 - not reviewed, 1 - reviewed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='All actual risks that are managed by risk owners. It gonna require for control measures from the statement_of_applicability table.';

--
-- Zrzut danych tabeli `risk_register`
--

INSERT INTO `risk_register` (`risk_un`, `risk_type`, `risk_name`, `threat_exp`, `vulnerability_exp`, `impact_v`, `likelihood_v`, `risk_level_p`, `risk_owner_name`, `treatment_decision`, `treatment_plan`, `review_date`, `review_status`) VALUES
(1, '2', 'Not adequate administration of inventorization system during employee vacation time.', 'Lost or mis-administrated high cost equipment.', 'No inventorization supervisor.', 3, 3, '*', 'Logistics department', '1', 'Recommend a decision to include an additional action for scheduling vacations between employees themselves in regards of inventorization support. ', NULL, 0),
(2, '1', 'Server equipment gets damaged by flood of water, because of bad condition of plumbing system.', 'Water leak out of damaged plumbing system\'s point(s).', 'Old plumbing system of the Operations department\'s building.', 5, 3, '*', 'Operations department', '1', 'At proportional time to start modernization of problematic plumbing system.', NULL, 0),
(3, '2', 'Employees make mistakes, because of not being briefed with latest changes of operations system.', 'Wrong employee actions using operations system.', 'Lack of briefing procedure.', 3, 4, '*', 'Operations department', '1', 'To recommend for inclusion of changelog and annotation of important system changes, that must be taken in account by employees.', NULL, 0),
(4, '3', '3D printed equipment get lost or unmaintained, because of lack of inventorization labels.', '3D printed equipment gonna get lost, it could be left unmaintained after specific period of time.', '3D printed equipment does not produce unique identification information.', 4, 3, '*', 'Logistics department, Operations department', '1', 'To issue unique informative labels for equipment, that gonna be conducted & distributed by logistics department.', NULL, 0),
(5, '2', 'Documentation are not passed to the archive room in time.', 'Broken life-cycle of documentation results in lost documentation assets.', 'Management facilities undergoes maintenance up to 2026 December.', 2, 3, '+', 'Management department', '2', 'No new recommendations for now.', NULL, 0),
(6, '2', 'Sensitive equipment gets damaged during transportation between departments.', 'Damaged sensitive equipment.', 'Rough transportation conditions from operations department.', 5, 3, '*', 'Logistics department, Operations department', '3', 'To recommend a purchase of a premium insurance for all sensitive equipment.', NULL, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `statement_of_applicability`
--

DROP TABLE IF EXISTS `statement_of_applicability`;
CREATE TABLE `statement_of_applicability` (
  `control_un` bigint(16) UNSIGNED NOT NULL,
  `control_name` char(200) NOT NULL,
  `control_description` text NOT NULL,
  `applicability_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - not applicable, 1 - applicable',
  `justification_text` text NOT NULL,
  `implementation_status` enum('1','2','3','4') NOT NULL DEFAULT '1' COMMENT '1 - planned, 2 - implemented, 3 - partial implementation, 4 - not implemented',
  `control_owner_name` char(150) NOT NULL,
  `review_date` date DEFAULT NULL,
  `review_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 - not reviewed, 1 - reviewed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='For managing set of control measures. Requires for evidence set for a proof.';

--
-- Zrzut danych tabeli `statement_of_applicability`
--

INSERT INTO `statement_of_applicability` (`control_un`, `control_name`, `control_description`, `applicability_status`, `justification_text`, `implementation_status`, `control_owner_name`, `review_date`, `review_status`) VALUES
(1, 'Three employee rule (Department)', 'Each function of a department must be backed up by no less than three employee.', 1, 'It provide a chance to keep up with department\'s support for defined organization\'s mission.', '2', 'Each department', NULL, 0),
(2, 'Regulation for protection from fire incidents', 'Local law requires to meet adequate security measures regarding protection from fire incidents. It include planning for actions against plausible disaster situations and preparation of evacuation plans. Company purchase external service for supporting these requirements.', 1, 'Required by law.', '2', 'Section of Safety & Health at Work', NULL, 0),
(3, 'User Guide & Safe use of operations system documentation.', 'Operations system is an external company product, that include adequate documentation too. This documentation are distributed according needs of Operations department by Section of Safety & Health at Work.', 1, 'Operation system is an unique product, that must be operated according its Technical Specification. Safety of operations are necessary, so configuration of the system would not impact into making an incident at Work.  ', '2', 'Section of Safety & Health at Work', NULL, 0),
(4, 'Regular Software & Hardware updates of Operations system.', 'Operations system gets monthly updates & operational tests and yearly check-ups.', 1, 'Company are operating using number of new technological solutions. Regular updates ensure adequate operations of this type of system.', '3', 'Operations department', NULL, 0),
(5, 'Company\'s agreement documentation must be kept for X years at adequate safe place.', 'Management department controls storage & archive rooms for storing sensitive & confidential information of this company. Documentation must be in place for defined period of time, then passed to archives for meeting the demand.', 1, 'A confidentiality, an integrity & an availability could be ensured in a properly build facilities.', '3', 'Management department', NULL, 0),
(6, 'Physical access control at Management department', 'Management department has a Security Guard\'s room for managing physical access at parking lot & Management facilities. ', 1, 'A confidentiality, an integrity & an availability could be ensured in a properly build facilities.', '2', 'Management department', NULL, 0),
(7, 'Technical support of company\'s vehicles', 'Company has an agreement for keeping its vehicles in technical condition.', 1, 'Company\'s operations require for safe & ensured maintenance of transport.', '2', 'Logistics department', NULL, 0),
(8, 'Regulation for safety & health at work', 'Local law requires to meet adequate safety & health measures at work. Equipment must be maintained & configured for safe & sound working attitude. Section of Safety & Health at Work is taking most of the lead on this behalf.', 0, 'Required by law.', '2', 'Section of Safety & Health at Work', NULL, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `uno` bigint(16) UNSIGNED NOT NULL,
  `first_name` char(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` char(90) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` char(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` char(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` char(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `confirmation` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `activated` tinyint(1) UNSIGNED NOT NULL,
  `termsofservice` tinyint(1) UNSIGNED NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Zrzut danych tabeli `user`
--

INSERT INTO `user` (`uno`, `first_name`, `last_name`, `username`, `email`, `password`, `confirmation`, `activated`, `termsofservice`, `created`) VALUES
(60, 'Security', 'Eve', '24555', 'security@hostname.com', '$2y$10$Nge4nMiaswHvxuFJCEZjxOO.KS4nfr3VmcDXz3GLqNw5C5ODfLB7G', 'a14e7b6a7028182c108ff4bf828b597a', 1, 1, '2026-08-20 10:27:09'),
(61, 'Official', 'Pol', 'H125111', 'info@hostname.com', '$2y$10$ZCP9HHdBy0hWCxJSx7hhau3mai7OWvRsb3lMhzBtyUi.hIsSjaKHS', '9238076b2a89c3b2b991997473600e45', 1, 1, '2026-08-20 11:11:12');

--
-- Wyzwalacze `user`
--
DROP TRIGGER IF EXISTS `user_confirmation`;
DELIMITER $$
CREATE TRIGGER `user_confirmation` BEFORE INSERT ON `user` FOR EACH ROW SET NEW.`confirmation` = MD5(CONCAT(NEW.`uno`, NEW.`username`, NEW.`email`, NEW.`password`))
$$
DELIMITER ;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `ca_evidence_a`
--
ALTER TABLE `ca_evidence_a`
  ADD KEY `evidence_un` (`evidence_un`),
  ADD KEY `ca_un` (`ca_un`);

--
-- Indeksy dla tabeli `ca_procedures_a`
--
ALTER TABLE `ca_procedures_a`
  ADD KEY `procedures_un` (`procedures_un`),
  ADD KEY `ca_un` (`ca_un`);

--
-- Indeksy dla tabeli `corrective_action`
--
ALTER TABLE `corrective_action`
  ADD PRIMARY KEY (`corrective_action_id`),
  ADD KEY `corrective_action_id` (`corrective_action_id`);

--
-- Indeksy dla tabeli `evidence`
--
ALTER TABLE `evidence`
  ADD PRIMARY KEY (`artifact_id`),
  ADD KEY `artifact_id` (`artifact_id`);

--
-- Indeksy dla tabeli `evidence_set_a`
--
ALTER TABLE `evidence_set_a`
  ADD PRIMARY KEY (`un`),
  ADD KEY `evidence_un` (`evidence_un`),
  ADD KEY `statement_of_applicability_un` (`statement_of_applicability_un`);

--
-- Indeksy dla tabeli `implemented_controls_a`
--
ALTER TABLE `implemented_controls_a`
  ADD PRIMARY KEY (`un`),
  ADD KEY `risk_register_un` (`risk_register_un`),
  ADD KEY `statement_of_applicability_un` (`statement_of_applicability_un`);

--
-- Indeksy dla tabeli `procedures`
--
ALTER TABLE `procedures`
  ADD PRIMARY KEY (`document_un`),
  ADD KEY `document_un` (`document_un`);

--
-- Indeksy dla tabeli `procedures_change_log`
--
ALTER TABLE `procedures_change_log`
  ADD PRIMARY KEY (`un`),
  ADD KEY `procedures_un` (`procedures_un`);

--
-- Indeksy dla tabeli `related_controls_a`
--
ALTER TABLE `related_controls_a`
  ADD PRIMARY KEY (`un`),
  ADD KEY `statement_of_applicability_un` (`statement_of_applicability_un`),
  ADD KEY `procedures_un` (`procedures_un`);

--
-- Indeksy dla tabeli `request`
--
ALTER TABLE `request`
  ADD PRIMARY KEY (`un`),
  ADD UNIQUE KEY `user_uno` (`user_uno`) USING BTREE;

--
-- Indeksy dla tabeli `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`review_un`),
  ADD KEY `review_un` (`review_un`);

--
-- Indeksy dla tabeli `review_controls_a`
--
ALTER TABLE `review_controls_a`
  ADD PRIMARY KEY (`un`),
  ADD KEY `statement_of_applicability_un` (`statement_of_applicability_un`),
  ADD KEY `review_un` (`review_un`);

--
-- Indeksy dla tabeli `review_procedures_a`
--
ALTER TABLE `review_procedures_a`
  ADD PRIMARY KEY (`un`),
  ADD KEY `review_un` (`review_un`),
  ADD KEY `procedures_un` (`procedures_un`);

--
-- Indeksy dla tabeli `review_risks_a`
--
ALTER TABLE `review_risks_a`
  ADD PRIMARY KEY (`un`),
  ADD KEY `review_un` (`review_un`),
  ADD KEY `risk_register_un` (`risk_register_un`);

--
-- Indeksy dla tabeli `risk_register`
--
ALTER TABLE `risk_register`
  ADD PRIMARY KEY (`risk_un`),
  ADD KEY `risk_un` (`risk_un`);

--
-- Indeksy dla tabeli `statement_of_applicability`
--
ALTER TABLE `statement_of_applicability`
  ADD PRIMARY KEY (`control_un`),
  ADD KEY `control_un` (`control_un`);

--
-- Indeksy dla tabeli `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`uno`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `uno` (`uno`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `corrective_action`
--
ALTER TABLE `corrective_action`
  MODIFY `corrective_action_id` bigint(16) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT dla tabeli `evidence`
--
ALTER TABLE `evidence`
  MODIFY `artifact_id` bigint(16) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT dla tabeli `evidence_set_a`
--
ALTER TABLE `evidence_set_a`
  MODIFY `un` bigint(18) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT dla tabeli `implemented_controls_a`
--
ALTER TABLE `implemented_controls_a`
  MODIFY `un` bigint(18) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT dla tabeli `procedures`
--
ALTER TABLE `procedures`
  MODIFY `document_un` bigint(16) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT dla tabeli `procedures_change_log`
--
ALTER TABLE `procedures_change_log`
  MODIFY `un` bigint(18) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT dla tabeli `related_controls_a`
--
ALTER TABLE `related_controls_a`
  MODIFY `un` bigint(18) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT dla tabeli `request`
--
ALTER TABLE `request`
  MODIFY `un` bigint(16) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT dla tabeli `review`
--
ALTER TABLE `review`
  MODIFY `review_un` bigint(16) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `review_controls_a`
--
ALTER TABLE `review_controls_a`
  MODIFY `un` bigint(18) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `review_procedures_a`
--
ALTER TABLE `review_procedures_a`
  MODIFY `un` bigint(18) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `review_risks_a`
--
ALTER TABLE `review_risks_a`
  MODIFY `un` bigint(18) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `risk_register`
--
ALTER TABLE `risk_register`
  MODIFY `risk_un` bigint(16) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT dla tabeli `statement_of_applicability`
--
ALTER TABLE `statement_of_applicability`
  MODIFY `control_un` bigint(16) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT dla tabeli `user`
--
ALTER TABLE `user`
  MODIFY `uno` bigint(16) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `ca_evidence_a`
--
ALTER TABLE `ca_evidence_a`
  ADD CONSTRAINT `ca_ibfk_1` FOREIGN KEY (`ca_un`) REFERENCES `corrective_action` (`corrective_action_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `evidence_ibfk_3` FOREIGN KEY (`evidence_un`) REFERENCES `evidence` (`artifact_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `ca_procedures_a`
--
ALTER TABLE `ca_procedures_a`
  ADD CONSTRAINT `ca_ibfk_2` FOREIGN KEY (`ca_un`) REFERENCES `corrective_action` (`corrective_action_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `procedures_ibfk_4` FOREIGN KEY (`procedures_un`) REFERENCES `procedures` (`document_un`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `evidence_set_a`
--
ALTER TABLE `evidence_set_a`
  ADD CONSTRAINT `evidence_ibfk_1` FOREIGN KEY (`evidence_un`) REFERENCES `evidence` (`artifact_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `statement_of_applicability_ibfk_1` FOREIGN KEY (`statement_of_applicability_un`) REFERENCES `statement_of_applicability` (`control_un`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `implemented_controls_a`
--
ALTER TABLE `implemented_controls_a`
  ADD CONSTRAINT `risk_register_ibfk_1` FOREIGN KEY (`risk_register_un`) REFERENCES `risk_register` (`risk_un`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `statement_of_applicability_ibfk_3` FOREIGN KEY (`statement_of_applicability_un`) REFERENCES `statement_of_applicability` (`control_un`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `procedures_change_log`
--
ALTER TABLE `procedures_change_log`
  ADD CONSTRAINT `procedures_ibfk_1` FOREIGN KEY (`procedures_un`) REFERENCES `procedures` (`document_un`);

--
-- Ograniczenia dla tabeli `related_controls_a`
--
ALTER TABLE `related_controls_a`
  ADD CONSTRAINT `procedures_ibfk_2` FOREIGN KEY (`procedures_un`) REFERENCES `procedures` (`document_un`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `statement_of_applicability_ibfk_4` FOREIGN KEY (`statement_of_applicability_un`) REFERENCES `statement_of_applicability` (`control_un`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `request`
--
ALTER TABLE `request`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`user_uno`) REFERENCES `user` (`uno`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `review_controls_a`
--
ALTER TABLE `review_controls_a`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`review_un`) REFERENCES `review` (`review_un`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `statement_of_applicability_ibfk_5` FOREIGN KEY (`statement_of_applicability_un`) REFERENCES `statement_of_applicability` (`control_un`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `review_procedures_a`
--
ALTER TABLE `review_procedures_a`
  ADD CONSTRAINT `procedures_ibfk_3` FOREIGN KEY (`procedures_un`) REFERENCES `procedures` (`document_un`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `review_ibfk_3` FOREIGN KEY (`review_un`) REFERENCES `review` (`review_un`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `review_risks_a`
--
ALTER TABLE `review_risks_a`
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`review_un`) REFERENCES `review` (`review_un`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `risk_register_ibfk_2` FOREIGN KEY (`risk_register_un`) REFERENCES `risk_register` (`risk_un`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
