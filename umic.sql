-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 26, 2025 at 06:14 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `umic`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `role` varchar(30) DEFAULT NULL,
  `activity` varchar(255) DEFAULT NULL,
  `activity_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `username`, `role`, `activity`, `activity_time`, `details`) VALUES
(1, 3, 'isaac2', 'admin', 'Message Sent', '2025-06-26 16:01:27', 'Message sent to ocayaisaac55@gmail.com for application 7'),
(2, 3, 'isaac2', 'admin', 'Mentor Assigned', '2025-06-26 16:01:35', 'Mentor ID 2 assigned to application 7'),
(3, 3, 'isaac2', 'admin', 'Feedback Sent', '2025-06-26 16:02:21', 'Feedback sent for application 7'),
(4, 3, 'isaac2', 'admin', 'Mentor Assigned', '2025-06-26 16:10:56', 'Mentor ID 2 assigned to application 7');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `program_id` int(11) DEFAULT NULL,
  `program` varchar(100) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `status` enum('Submitted','Under Review','Shortlisted','Rejected','Accepted') DEFAULT 'Submitted',
  `feedback` text DEFAULT NULL,
  `assigned_mentor` int(11) DEFAULT NULL,
  `assigned_mentor_email` varchar(100) DEFAULT NULL,
  `campus` varchar(100) DEFAULT NULL,
  `student_number` varchar(100) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `year_of_study` varchar(10) DEFAULT NULL,
  `graduation_year` varchar(10) DEFAULT NULL,
  `current_job` varchar(100) DEFAULT NULL,
  `employer` varchar(100) DEFAULT NULL,
  `staff_number` varchar(100) DEFAULT NULL,
  `faculty` varchar(100) DEFAULT NULL,
  `years_at_umu` varchar(10) DEFAULT NULL,
  `national_id` varchar(100) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `marital_status` varchar(50) DEFAULT NULL,
  `num_beneficiaries` varchar(10) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `street` varchar(100) DEFAULT NULL,
  `village` varchar(100) DEFAULT NULL,
  `subcounty` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `refugee` varchar(10) DEFAULT NULL,
  `age_range` varchar(20) DEFAULT NULL,
  `disability` varchar(10) DEFAULT NULL,
  `disability_text` varchar(300) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `business_idea_name` varchar(255) DEFAULT NULL,
  `sector` varchar(100) DEFAULT NULL,
  `program_attended` varchar(100) DEFAULT NULL,
  `initial_capital` varchar(50) DEFAULT NULL,
  `cohort` varchar(50) DEFAULT NULL,
  `year_of_inception` varchar(10) DEFAULT NULL,
  `interested_in` varchar(100) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `assigned_evaluator` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `program_id`, `program`, `full_name`, `category`, `status`, `feedback`, `assigned_mentor`, `assigned_mentor_email`, `campus`, `student_number`, `course`, `year_of_study`, `graduation_year`, `current_job`, `employer`, `staff_number`, `faculty`, `years_at_umu`, `national_id`, `occupation`, `marital_status`, `num_beneficiaries`, `nationality`, `phone`, `email`, `street`, `village`, `subcounty`, `country`, `district`, `refugee`, `age_range`, `disability`, `disability_text`, `gender`, `business_idea_name`, `sector`, `program_attended`, `initial_capital`, `cohort`, `year_of_inception`, `interested_in`, `submitted_at`, `assigned_evaluator`) VALUES
(1, 1, NULL, 'isaac', 'student', 'Submitted', NULL, NULL, NULL, 'main', 'uyyyu', 'hh', '3', '2025', '', '', '', '', '', '', '', '', '', 'ug', '9', 'i@gmail.com', 'hh', 'n', 'mj', 'Uganda', 'Kampala', 'no', '19-25', 'no', '', NULL, 'yt', 'agriculture', 'unesco', '64', 'h', '2021', 'funding', '2025-06-25 12:41:05', NULL),
(3, 1, NULL, 'isaacs', 'student', 'Submitted', NULL, NULL, NULL, 'main', 'uyyyu', 'hh', '3', '2025', '', '', '', '', '', '', '', '', '', 'ug', '9', 'i@gmail.com', 'hh', 'n', 'mj', 'Uganda', 'Kampala', 'no', '19-25', 'no', '', NULL, 'yt', 'agriculture', 'unesco', '64', 'h', '2021', 'funding', '2025-06-25 12:41:05', NULL),
(4, 1, NULL, 'isaac', 'student', 'Submitted', NULL, NULL, NULL, 'mains', 'uyyyu', 'hh', '3', '2025', '', '', '', '', '', '', '', '', '', 'ug', '9', 'i@gmail.com', 'hh', 'n', 'mj', 'Uganda', 'Kampala', 'no', '19-25', 'no', '', NULL, 'yt', 'agriculture', 'unesco', '64', 'h', '2021', 'funding', '2025-06-25 12:41:05', NULL),
(5, 1, NULL, 'kk', 'student', 'Submitted', NULL, NULL, NULL, 'main', '23', 'ff', '1', '2025', '', '', '', '', '', '', '', '', '', 'u', '343', 'd@gmail.com', 'rere', 're', 'ere', 'Uganda', 'Kaabong', 'no', '19-25', 'no', '', NULL, 'ee', 'agriculture', 'suesca', '45', 'e', '2017', 'mentorship', '2025-06-25 14:47:42', NULL),
(6, 1, NULL, 'q', 'staff', 'Shortlisted', '', NULL, NULL, '', '', '', '2025', '', '', 'q', 'q', '2', '', '', '', '', 'u', '3', 'e@gmail.com', 'w', 'w', 'w', 'Uganda', 'Kampala', 'no', '31-35', 'no', '', NULL, 'we', 'agriculture', 'tagdev', '3', 'e', '2016', 'funding', '2025-06-25 14:52:43', NULL),
(7, 1, NULL, 'w', 'student', 'Under Review', 'nicelyyyyyyy', 2, NULL, 'main', '3', 'ds', '5', '2025', '', '', '', '', '', '', '', '', '', 'r', '2', 'd@gmail.com', 'e', 'w', 'w', 'Uganda', 'Kaabong', 'yes', '36-40', 'no', '', NULL, 'w', 'agriculture', 'unesco', '2', 'w', '2015', 'funding', '2025-06-25 14:54:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `id` int(11) NOT NULL,
  `application_id` int(11) DEFAULT NULL,
  `file_type` enum('proposal','video','pitch_deck','business_plan') DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attachments`
--

INSERT INTO `attachments` (`id`, `application_id`, `file_type`, `file_path`, `uploaded_at`) VALUES
(1, 4, '', 'att_685c0af976c28.docx', '2025-06-25 14:43:05'),
(2, 7, '', 'att_685c0da3ca92d.docx', '2025-06-25 14:54:27');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `entrepreneur`
--

CREATE TABLE `entrepreneur` (
  `entrepreneur_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `student_id` varchar(20) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `year_of_study` int(11) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `profile_picture` varchar(100) DEFAULT NULL,
  `registration_date` datetime DEFAULT current_timestamp(),
  `interests` text NOT NULL,
  `sector` varchar(30) NOT NULL,
  `role` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `entrepreneur`
--

INSERT INTO `entrepreneur` (`entrepreneur_id`, `first_name`, `last_name`, `email`, `phone`, `student_id`, `department`, `course`, `year_of_study`, `gender`, `profile_picture`, `registration_date`, `interests`, `sector`, `role`, `password`) VALUES
(1, 'alkclkac', 'kjkahkla', 'adlkandlk@gmail.com', '077777777', 'hhdklan', 'kjsjk', 'jhgjhgjk', 3, 'female', '1750847006_jogendra-singh-VrW_EgqwOUo-unsplash.jpg', '2025-06-25 13:23:26', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_criteria`
--

CREATE TABLE `evaluation_criteria` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation_criteria`
--

INSERT INTO `evaluation_criteria` (`id`, `name`, `description`) VALUES
(1, 'Innovation', 'How novel or original is the idea?'),
(2, 'Feasibility', 'How practical and achievable is the idea?'),
(3, 'Market Potential', 'What is the potential market size and demand?');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_scores`
--

CREATE TABLE `evaluation_scores` (
  `id` int(11) NOT NULL,
  `application_id` int(11) DEFAULT NULL,
  `evaluator_id` int(11) DEFAULT NULL,
  `criteria_id` int(11) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `evaluated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_type` enum('workshop','training','mentoring') NOT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `event_type`, `start_datetime`, `end_datetime`, `location`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'tree', 'mnaklnclask', 'training', '2025-06-28 12:13:00', '2025-06-28 12:13:00', 'smss', 3, '2025-06-26 12:20:29', '2025-06-26 18:09:10');

-- --------------------------------------------------------

--
-- Table structure for table `mentors`
--

CREATE TABLE `mentors` (
  `mentor_id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `expertise_area` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `assigned_department` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mentors`
--

INSERT INTO `mentors` (`mentor_id`, `full_name`, `email`, `expertise_area`, `phone`, `assigned_department`) VALUES
(1, 'kasozi', 'k@gmail.com', 'Web', '07777777', 'Science'),
(2, 'isaac', 'isaac@gmail.com', 'Tech', '077777777', 'scie'),
(3, 'sfs sdf', 'a@gmail.com', 'wwww', '2222222', 'aa');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `application_id` int(11) DEFAULT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `recipient_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sender` varchar(255) DEFAULT NULL,
  `recipient` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `application_id`, `sender_id`, `recipient_id`, `message`, `sent_at`, `sender`, `recipient`) VALUES
(1, 1, 1, 1, 'iuhcapihdapihcn', '2025-06-26 09:35:38', 'admin', 'isaac'),
(2, NULL, 3, 7, 'hey', '2025-06-26 09:40:09', NULL, NULL),
(3, NULL, 3, 1, 'hey', '2025-06-26 09:40:27', NULL, NULL),
(4, NULL, 3, 6, 'hey', '2025-06-26 09:40:27', NULL, NULL),
(5, NULL, 3, 7, 'hey', '2025-06-26 09:40:27', NULL, NULL),
(6, NULL, 3, 7, 'hey', '2025-06-26 09:44:32', NULL, NULL),
(7, NULL, 3, 6, 'hey', '2025-06-26 09:44:34', NULL, NULL),
(8, NULL, 3, 1, 'hey', '2025-06-26 09:44:36', NULL, NULL),
(9, NULL, 7, 3, 'Re: hey', '2025-06-26 10:43:42', NULL, NULL),
(10, NULL, 7, 3, 'Re: hey', '2025-06-26 10:43:50', NULL, NULL),
(11, NULL, 7, 3, 'Re: hey', '2025-06-26 10:45:39', NULL, NULL),
(12, NULL, 7, 3, 'ok', '2025-06-26 10:45:47', NULL, NULL),
(13, NULL, 7, 3, 'ok', '2025-06-26 10:48:25', NULL, NULL),
(14, NULL, 7, 3, 'ok', '2025-06-26 11:02:32', NULL, NULL),
(15, NULL, 3, 1, 'true', '2025-06-26 11:03:18', NULL, NULL),
(16, NULL, 3, 1, 'true', '2025-06-26 11:05:43', NULL, NULL),
(17, 7, 7, NULL, 'heeeeeyyyyyyy', '2025-06-26 12:43:33', NULL, NULL),
(18, 7, NULL, NULL, 'thois', '2025-06-26 16:01:27', 'ocayaisaac5566@gmail.com', 'ocayaisaac55@gmail.com'),
(19, 7, NULL, NULL, 'Dear isaac,\n\nYou have been assigned to mentor the following application:\n\nApplication ID: 7\nApplicant Name: w\nApplicant Email: d@gmail.com\n\nPlease log in to your dashboard to view more details.\n\nRegards,\nUMU Innovation Office', '2025-06-26 16:10:56', 'ocayaisaac5566@gmail.com', 'isaac@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `name`, `description`) VALUES
(1, 'Default Program', 'Auto-created for dashboard test');

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `application_id` int(11) DEFAULT NULL,
  `event_type` enum('interview','pitch') DEFAULT NULL,
  `scheduled_at` datetime DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `session_type` enum('interview','pitch') NOT NULL,
  `scheduled_by` int(11) DEFAULT NULL,
  `scheduled_for` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `location` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','mentor','entrepreneur','evaluator') DEFAULT 'entrepreneur',
  `created_at` datetime DEFAULT current_timestamp(),
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `role`, `created_at`, `profile_picture`) VALUES
(1, 'isaac', 'ocayaisaac55@gmail.com', 'kalipso55', 'evaluator', '2025-06-23 11:50:24', NULL),
(3, 'isaac2', 'ocayaisaac5566@gmail.com', 'kalipso5566', 'admin', '2025-06-23 11:50:24', NULL),
(6, 'isaac44', 'ocayaisaac5544@gmail.com', 'kalipso5544', 'entrepreneur', '2025-06-23 11:50:24', NULL),
(7, 'isaac8', 'kww@gmail.com', 'kalipso5577', 'mentor', '2025-06-23 11:50:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `type` enum('info','warning','success','error') DEFAULT 'info',
  `category` varchar(50) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_id` (`application_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `resource_id` (`resource_id`);

--
-- Indexes for table `entrepreneur`
--
ALTER TABLE `entrepreneur`
  ADD PRIMARY KEY (`entrepreneur_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- Indexes for table `evaluation_criteria`
--
ALTER TABLE `evaluation_criteria`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `evaluation_scores`
--
ALTER TABLE `evaluation_scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_id` (`application_id`),
  ADD KEY `evaluator_id` (`evaluator_id`),
  ADD KEY `criteria_id` (`criteria_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_type` (`event_type`),
  ADD KEY `idx_start` (`start_datetime`);

--
-- Indexes for table `mentors`
--
ALTER TABLE `mentors`
  ADD PRIMARY KEY (`mentor_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_id` (`application_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `recipient_id` (`recipient_id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_id` (`application_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `scheduled_by` (`scheduled_by`),
  ADD KEY `idx_app` (`application_id`),
  ADD KEY `idx_for` (`scheduled_for`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `entrepreneur`
--
ALTER TABLE `entrepreneur`
  MODIFY `entrepreneur_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `evaluation_criteria`
--
ALTER TABLE `evaluation_criteria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `evaluation_scores`
--
ALTER TABLE `evaluation_scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mentors`
--
ALTER TABLE `mentors`
  MODIFY `mentor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`);

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`);

--
-- Constraints for table `evaluation_scores`
--
ALTER TABLE `evaluation_scores`
  ADD CONSTRAINT `evaluation_scores_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`),
  ADD CONSTRAINT `evaluation_scores_ibfk_2` FOREIGN KEY (`evaluator_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `evaluation_scores_ibfk_3` FOREIGN KEY (`criteria_id`) REFERENCES `evaluation_criteria` (`id`);

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`),
  ADD CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sessions_ibfk_2` FOREIGN KEY (`scheduled_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sessions_ibfk_3` FOREIGN KEY (`scheduled_for`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
