-- Student Management System Database Export
SET FOREIGN_KEY_CHECKS=0;



CREATE TABLE `attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('Present','Absent') NOT NULL,
  `marked_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `course_id` (`course_id`),
  KEY `marked_by` (`marked_by`),
  CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendance_ibfk_3` FOREIGN KEY (`marked_by`) REFERENCES `teachers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_name` varchar(100) NOT NULL,
  `course_code` varchar(50) NOT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `course_code` (`course_code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO courses (id, course_name, course_code, duration, created_at) VALUES ('3', 'Bca', 'BCA2026', '3 Year', '2026-02-25 15:57:03');


CREATE TABLE `exams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exam_name` varchar(100) NOT NULL,
  `course_id` int(11) NOT NULL,
  `exam_date` date DEFAULT NULL,
  `total_marks` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `fee_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_mode` enum('Cash','UPI','Card','Bank Transfer') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `fee_payments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `fee_structure` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `total_fee` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `fee_structure_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `marks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `marks_obtained` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `exam_id` (`exam_id`),
  CONSTRAINT `marks_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `marks_ibfk_2` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enrollment_no` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `admission_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `enrollment_no` (`enrollment_no`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `students_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO students (id, enrollment_no, name, email, phone, gender, dob, address, photo, course_id, admission_date, created_at) VALUES ('5', '2405112110108', 'PRUTHVIRAJ', 'pruthvirathod468@gmail.com', '7459485491', 'Male', '2002-04-26', 'i am a bca student', '', '3', '2026-02-25', '2026-02-25 15:59:48');
INSERT INTO students (id, enrollment_no, name, email, phone, gender, dob, address, photo, course_id, admission_date, created_at) VALUES ('6', '240511211012', 'utsav', 'utsav12@gmail.com', '7878900987', 'Male', '2026-02-25', 'this is a bechloar student', '', '3', '2026-02-25', '2026-02-25 22:44:05');
INSERT INTO students (id, enrollment_no, name, email, phone, gender, dob, address, photo, course_id, admission_date, created_at) VALUES ('8', 'AUTO-11d583', 'PRUTHVIRAJ', 'pruthvirathod467@gmail.com', '', '', '', '', '', '3', '', '2026-03-09 22:21:14');
INSERT INTO students (id, enrollment_no, name, email, phone, gender, dob, address, photo, course_id, admission_date, created_at) VALUES ('9', 'AUTO-652a10', 'Test Student', 'student_test@example.com', '', '', '', '', '', '3', '', '2026-03-09 22:21:14');
INSERT INTO students (id, enrollment_no, name, email, phone, gender, dob, address, photo, course_id, admission_date, created_at) VALUES ('10', 'ENR00011', 'sagar', 'sagar123@gmail.com', '', '', '', '', '', '', '2026-03-09', '2026-03-09 23:15:28');
INSERT INTO students (id, enrollment_no, name, email, phone, gender, dob, address, photo, course_id, admission_date, created_at) VALUES ('11', '240511210111', 'keyur', 'keyurrathod12@gmail.com', '9867563423', 'Male', '2002-04-26', 'thisis  bca student', '', '3', '2026-03-11', '2026-03-11 11:49:07');


CREATE TABLE `teachers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teacher','student') DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO users (id, name, email, password, role, created_at) VALUES ('6', 'admin', 'admin123@gmail.com', '$2y$10$TBgrRnLlbmjkbfPQzDqcI.jtB3gqJvTv6zDuKBsK0MrylZTeRTQNq', 'admin', '2026-02-25 15:44:33');
INSERT INTO users (id, name, email, password, role, created_at) VALUES ('7', 'PRUTHVIRAJ', 'pruthvirathod467@gmail.com', '$2y$10$bwb.kmDSrzz74.k8ixiW.ecBu7RkYY/Hf64XlPnvPf/LO9tOLDkMO', 'admin', '2026-02-25 15:46:36');
INSERT INTO users (id, name, email, password, role, created_at) VALUES ('8', 'PRUTHVIRAJ', 'pruthvirathod468@gmail.com', '$2y$10$slOsXckTXgZ.f0YDUWb3JOES3L.sQd973CtT1VIOHynzzu2b0ZYJe', 'admin', '2026-02-25 15:59:48');
INSERT INTO users (id, name, email, password, role, created_at) VALUES ('9', 'utsav', 'utsav12@gmail.com', '$2y$10$go88P3Gbt/vzyW6HReDJEe.m8r8JThgH0ly9LbLzQCH0YNvvWMTMW', 'student', '2026-02-25 22:44:05');
INSERT INTO users (id, name, email, password, role, created_at) VALUES ('10', 'Test Student', 'student_test@example.com', '$2y$10$GHGd2QSjiTCobM.fpIQvvOaNi.5E/hCTUAsoAvF2ZuEHoWqH9nwp2', 'student', '2026-03-09 22:10:24');
INSERT INTO users (id, name, email, password, role, created_at) VALUES ('11', 'sagar', 'sagar123@gmail.com', 'sagar123', 'student', '0000-00-00 00:00:00');
INSERT INTO users (id, name, email, password, role, created_at) VALUES ('12', 'Admin User', 'admin@example.com', '$2y$10$3SARcfi6Hv/HUTDQAwfKBu1FJIO/TX2ZHwV.COdyr9304lwFvHGAW', 'admin', '2026-03-10 00:35:11');
INSERT INTO users (id, name, email, password, role, created_at) VALUES ('13', 'preet', 'preetrathod@gmail.com', '$2y$10$F3DosuId3BZuxO572OWc6.KnfSXIqwUSDNFoL6a/y/0kXt9ugtP2O', 'admin', '2026-03-11 11:19:01');
INSERT INTO users (id, name, email, password, role, created_at) VALUES ('14', 'keyur', 'keyurrathod12@gmail.com', '$2y$10$qgKZapjsgG1omc84U2t5euedS4sOklSTLjYS69mfaKSKbQouMVUV2', 'student', '2026-03-11 11:49:07');

SET FOREIGN_KEY_CHECKS=1;