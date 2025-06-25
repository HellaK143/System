-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 25, 2025 at 06:18 PM
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
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `program` varchar(100) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `status` enum('Submitted','Under Review','Shortlisted','Rejected','Accepted') DEFAULT 'Submitted',
  `feedback` text DEFAULT NULL,
  `assigned_mentor` int(11) DEFAULT NULL,
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
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `program`, `full_name`, `category`, `status`, `feedback`, `assigned_mentor`, `campus`, `student_number`, `course`, `year_of_study`, `graduation_year`, `current_job`, `employer`, `staff_number`, `faculty`, `years_at_umu`, `national_id`, `occupation`, `marital_status`, `num_beneficiaries`, `nationality`, `phone`, `email`, `street`, `village`, `subcounty`, `country`, `district`, `refugee`, `age_range`, `disability`, `disability_text`, `gender`, `business_idea_name`, `sector`, `program_attended`, `initial_capital`, `cohort`, `year_of_inception`, `interested_in`, `submitted_at`) VALUES
(1, NULL, 'isaac', 'student', 'Submitted', NULL, NULL, 'main', 'uyyyu', 'hh', '3', '2025', '', '', '', '', '', '', '', '', '', 'ug', '9', 'i@gmail.com', 'hh', 'n', 'mj', 'Uganda', 'Kampala', 'no', '19-25', 'no', '', NULL, 'yt', 'agriculture', 'unesco', '64', 'h', '2021', 'funding', '2025-06-25 12:41:05'),
(2, 'science', 'e', 'staff', 'Submitted', NULL, NULL, '', '', '', '', '2025', '', '', '3', 'f', '3', '', '', '', '', 'u', '434', 'i@gmail.com', 'ew', 'we', 'we', 'Uganda', 'Kampala', 'yes', '19-25', 'no', 'none', NULL, 'wee', 'agriculture', 'ai', '3', 'w', '2013', 'funding', '2025-06-25 12:44:56'),
(3, NULL, 'isaacs', 'student', 'Submitted', NULL, NULL, 'main', 'uyyyu', 'hh', '3', '2025', '', '', '', '', '', '', '', '', '', 'ug', '9', 'i@gmail.com', 'hh', 'n', 'mj', 'Uganda', 'Kampala', 'no', '19-25', 'no', '', NULL, 'yt', 'agriculture', 'unesco', '64', 'h', '2021', 'funding', '2025-06-25 12:41:05'),
(4, NULL, 'isaac', 'student', 'Submitted', NULL, NULL, 'mains', 'uyyyu', 'hh', '3', '2025', '', '', '', '', '', '', '', '', '', 'ug', '9', 'i@gmail.com', 'hh', 'n', 'mj', 'Uganda', 'Kampala', 'no', '19-25', 'no', '', NULL, 'yt', 'agriculture', 'unesco', '64', 'h', '2021', 'funding', '2025-06-25 12:41:05'),
(5, NULL, 'kk', 'student', 'Submitted', NULL, NULL, 'main', '23', 'ff', '1', '2025', '', '', '', '', '', '', '', '', '', 'u', '343', 'd@gmail.com', 'rere', 're', 'ere', 'Uganda', 'Kaabong', 'no', '19-25', 'no', '', NULL, 'ee', 'agriculture', 'suesca', '45', 'e', '2017', 'mentorship', '2025-06-25 14:47:42'),
(6, NULL, 'q', 'staff', 'Shortlisted', '', NULL, '', '', '', '', '2025', '', '', 'q', 'q', '2', '', '', '', '', 'u', '3', 'e@gmail.com', 'w', 'w', 'w', 'Uganda', 'Kampala', 'no', '31-35', 'no', '', NULL, 'we', 'agriculture', 'tagdev', '3', 'e', '2016', 'funding', '2025-06-25 14:52:43'),
(7, NULL, 'w', 'student', 'Under Review', 'nice', 2, 'main', '3', 'ds', '5', '2025', '', '', '', '', '', '', '', '', '', 'r', '2', 'd@gmail.com', 'e', 'w', 'w', 'Uganda', 'Kaabong', 'yes', '36-40', 'no', '', NULL, 'w', 'agriculture', 'unesco', '2', 'w', '2015', 'funding', '2025-06-25 14:54:27');

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
(1, 'alkclkac', 'kjkahkla', 'adlkandlk@gmail.com', '077777777', 'hhdklan', 'kjsjk', 'jhgjhgjk', 3, 'male', '1750847006_jogendra-singh-VrW_EgqwOUo-unsplash.jpg', '2025-06-25 13:23:26', '', '', '', '');

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
(2, 'isaac', 'isaac@gmail.com', 'Tech', '077777777', 'scie');

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
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
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
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','mentor','entrepreneur','evaluator') DEFAULT 'entrepreneur',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'isaac', 'ocayaisaac55@gmail.com', 'kalipso55', 'evaluator', '2025-06-23 11:50:24'),
(3, 'isaac2', 'ocayaisaac5566@gmail.com', 'kalipso5566', 'admin', '2025-06-23 11:50:24');

--
-- Indexes for dumped tables
--

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
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_id` (`application_id`),
  ADD KEY `created_by` (`created_by`);

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
-- AUTO_INCREMENT for table `mentors`
--
ALTER TABLE `mentors`
  MODIFY `mentor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`);

--
-- Constraints for table `evaluation_scores`
--
ALTER TABLE `evaluation_scores`
  ADD CONSTRAINT `evaluation_scores_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`),
  ADD CONSTRAINT `evaluation_scores_ibfk_2` FOREIGN KEY (`evaluator_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `evaluation_scores_ibfk_3` FOREIGN KEY (`criteria_id`) REFERENCES `evaluation_criteria` (`id`);

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
