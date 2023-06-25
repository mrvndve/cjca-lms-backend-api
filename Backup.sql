/*
SQLyog Community v13.1.9 (64 bit)
MySQL - 10.4.24-MariaDB : Database - cjca_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`cjca_db` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `cjca_db`;

/*Table structure for table `announcement` */

DROP TABLE IF EXISTS `announcement`;

CREATE TABLE `announcement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `author_id` int(11) NOT NULL,
  `content` varchar(255) NOT NULL,
  `attachment` blob DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `author_id` (`author_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

/*Data for the table `announcement` */

/*Table structure for table `audit` */

DROP TABLE IF EXISTS `audit`;

CREATE TABLE `audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `module` varchar(255) NOT NULL,
  `activity` varchar(255) NOT NULL,
  `message` varchar(255) DEFAULT NULL,
  `ip_address` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `audit_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=latin1;

/*Data for the table `audit` */

insert  into `audit`(`id`,`user_id`,`module`,`activity`,`message`,`ip_address`,`created_at`,`updated_at`) values 
(32,1,'School Year','Add','School year has been added.','::1','2022-11-08 00:33:12','2022-11-08 00:33:12'),
(33,1,'School Year','Add','School year has been added.','::1','2022-11-08 00:42:25','2022-11-08 00:42:25'),
(34,1,'Login Form','Login','Login Successful.','::1','2022-11-08 02:16:36','2022-11-08 02:16:36'),
(35,1,'Grade Level','Add','Grade level has been added.','::1','2022-11-08 02:16:52','2022-11-08 02:16:52'),
(36,1,'Grade Level','Update','Grade level has been updated.','::1','2022-11-08 02:16:57','2022-11-08 02:16:57'),
(37,1,'Subject','Add','Subject has been added.','::1','2022-11-08 02:17:56','2022-11-08 02:17:56'),
(38,1,'Subject','Update','Subject has been updated.','::1','2022-11-08 02:20:43','2022-11-08 02:20:43'),
(39,1,'Section','Add','Section has been added.','::1','2022-11-08 02:55:32','2022-11-08 02:55:32'),
(40,1,'Grade Level','Add','Grade level has been added.','::1','2022-11-08 02:55:44','2022-11-08 02:55:44'),
(41,1,'Student','Insert','student@gmail.com has been added to student, with a password of 78979','::1','2022-11-08 02:56:27','2022-11-08 02:56:27'),
(42,39,'Login Form','Login','Login Successful.','::1','2022-11-08 02:57:32','2022-11-08 02:57:32'),
(43,1,'Login Form','Login','Login Successful.','::1','2022-11-08 02:57:38','2022-11-08 02:57:38'),
(44,1,'Subject','Update','Subject has been updated.','::1','2022-11-08 02:57:50','2022-11-08 02:57:50'),
(45,1,'Subject','Update','Subject has been updated.','::1','2022-11-08 02:58:01','2022-11-08 02:58:01'),
(46,1,'Subject','Add Lesson','Add lesson successful.','::1','2022-11-08 02:58:09','2022-11-08 02:58:09'),
(47,1,'Subject','Add Lesson','Add lesson successful.','::1','2022-11-08 03:00:58','2022-11-08 03:00:58'),
(48,39,'Login Form','Login','Login Successful.','::1','2022-11-08 03:01:57','2022-11-08 03:01:57'),
(49,1,'Login Form','Login','Login Successful.','::1','2022-11-08 03:02:19','2022-11-08 03:02:19'),
(50,1,'Subject','Update','Subject has been updated.','::1','2022-11-08 03:02:26','2022-11-08 03:02:26'),
(51,39,'Login Form','Login','Login Successful.','::1','2022-11-08 03:02:37','2022-11-08 03:02:37'),
(52,1,'Login Form','Login','Login Successful.','::1','2022-11-08 03:03:34','2022-11-08 03:03:34'),
(53,1,'Subject','Update','Subject has been updated.','::1','2022-11-08 03:03:41','2022-11-08 03:03:41'),
(54,39,'Login Form','Login','Login Successful.','::1','2022-11-08 03:03:50','2022-11-08 03:03:50'),
(55,1,'Login Form','Login','Login Successful.','::1','2022-11-08 03:04:02','2022-11-08 03:04:02'),
(56,1,'Department','Add','Department has been added.','::1','2022-11-08 03:04:24','2022-11-08 03:04:24'),
(57,1,'Teacher','Insert','teacher@gmail.com has been added to teacher, with a password of 34463','::1','2022-11-08 03:04:38','2022-11-08 03:04:38'),
(58,40,'Login Form','Login','Login Successful.','::1','2022-11-08 03:05:24','2022-11-08 03:05:24'),
(59,1,'Login Form','Login','Login Successful.','::1','2022-11-08 03:05:34','2022-11-08 03:05:34'),
(60,1,'Teacher','Update','Teacher user has been updated.','::1','2022-11-08 03:06:37','2022-11-08 03:06:37'),
(61,1,'Teacher','Update','Teacher user has been updated.','::1','2022-11-08 03:15:47','2022-11-08 03:15:47'),
(62,1,'Subject','Add Teacher','Add teacher successful.','::1','2022-11-08 03:17:30','2022-11-08 03:17:30'),
(63,1,'Teacher','Update','Teacher user has been updated.','::1','2022-11-08 03:52:02','2022-11-08 03:52:02'),
(64,1,'Subject','Add','Subject has been added.','::1','2022-11-08 03:52:34','2022-11-08 03:52:34'),
(65,1,'Teacher','Update','Teacher user has been updated.','::1','2022-11-08 03:52:50','2022-11-08 03:52:50'),
(66,1,'Subject','Delete Teacher','Delete teacher successful.','::1','2022-11-08 03:53:15','2022-11-08 03:53:15'),
(67,1,'Teacher','Update','Teacher user has been updated.','::1','2022-11-08 03:53:29','2022-11-08 03:53:29'),
(68,1,'Teacher','Update','Teacher user has been updated.','::1','2022-11-08 04:02:49','2022-11-08 04:02:49'),
(69,1,'Subject','Add Teacher','Add teacher successful.','::1','2022-11-08 04:03:09','2022-11-08 04:03:09'),
(70,1,'Teacher','Update','Teacher user has been updated.','::1','2022-11-08 04:03:31','2022-11-08 04:03:31'),
(71,1,'Teacher','Update','Teacher user has been updated.','::1','2022-11-08 04:19:41','2022-11-08 04:19:41'),
(72,1,'Subject','Add Teacher','Add teacher successful.','::1','2022-11-08 04:20:04','2022-11-08 04:20:04'),
(73,1,'Teacher','Update','Teacher user has been updated.','::1','2022-11-08 04:20:20','2022-11-08 04:20:20'),
(74,1,'Teacher','Update','Teacher user has been updated.','::1','2022-11-08 04:33:12','2022-11-08 04:33:12'),
(75,1,'Teacher','Update','Teacher user has been updated.','::1','2022-11-08 04:34:17','2022-11-08 04:34:17'),
(76,1,'Teacher','Update','Teacher user has been updated.','::1','2022-11-08 04:42:01','2022-11-08 04:42:01'),
(77,1,'Subject','Add Teacher','Add teacher successful.','::1','2022-11-08 04:42:15','2022-11-08 04:42:15'),
(78,1,'Teacher','Update','Teacher user has been updated.','::1','2022-11-08 04:44:11','2022-11-08 04:44:11'),
(79,1,'Teacher','Update','Teacher user has been updated.','::1','2022-11-08 04:44:19','2022-11-08 04:44:19'),
(80,1,'Teacher','Update','Teacher user has been updated.','::1','2022-11-08 04:47:59','2022-11-08 04:47:59'),
(81,1,'Teacher','Update','Teacher user has been updated.','::1','2022-11-08 04:48:20','2022-11-08 04:48:20'),
(82,1,'Teacher','Update','Teacher user has been updated.','::1','2022-11-08 04:49:23','2022-11-08 04:49:23'),
(83,1,'Teacher','Update','Teacher user has been updated.','::1','2022-11-08 04:50:11','2022-11-08 04:50:11'),
(84,1,'Teacher','Update','Teacher user has been updated.','::1','2022-11-08 04:51:57','2022-11-08 04:51:57'),
(85,1,'Subject','Add Teacher','Add teacher successful.','::1','2022-11-08 04:52:11','2022-11-08 04:52:11'),
(86,1,'Teacher','Update','Teacher user has been updated.','::1','2022-11-08 04:52:22','2022-11-08 04:52:22'),
(87,1,'Subject','Add','Subject has been added.','::1','2022-11-08 04:53:37','2022-11-08 04:53:37'),
(88,1,'Subject','Add Teacher','Add teacher successful.','::1','2022-11-08 04:53:43','2022-11-08 04:53:43'),
(89,1,'Teacher','Update','Teacher user has been updated.','::1','2022-11-08 04:54:14','2022-11-08 04:54:14'),
(90,1,'Teacher','Update','Teacher user has been updated.','::1','2022-11-08 04:54:21','2022-11-08 04:54:21'),
(91,1,'Subject','Add Teacher','Add teacher successful.','::1','2022-11-08 04:55:07','2022-11-08 04:55:07'),
(92,1,'Subject','Add Teacher','Add teacher successful.','::1','2022-11-08 04:55:13','2022-11-08 04:55:13'),
(93,1,'Subject','Update','Subject has been updated.','::1','2022-11-08 05:09:18','2022-11-08 05:09:18'),
(94,1,'Subject','Update','Subject has been updated.','::1','2022-11-08 05:10:04','2022-11-08 05:10:04'),
(95,40,'Login Form','Login','Login Successful.','::1','2022-11-08 05:10:44','2022-11-08 05:10:44'),
(96,1,'Login Form','Login','Login Successful.','::1','2022-11-08 05:11:23','2022-11-08 05:11:23'),
(97,1,'Subject','Add Teacher','Add teacher successful.','::1','2022-11-08 05:12:11','2022-11-08 05:12:11'),
(98,40,'Login Form','Login','Login Successful.','::1','2022-11-08 05:12:30','2022-11-08 05:12:30'),
(99,1,'Login Form','Login','Login Successful.','::1','2022-11-08 05:15:41','2022-11-08 05:15:41'),
(100,40,'Login Form','Login','Login Successful.','::1','2022-11-08 05:20:50','2022-11-08 05:20:50'),
(101,1,'Login Form','Login','Login Successful.','::1','2022-11-08 05:30:31','2022-11-08 05:30:31'),
(102,39,'Login Form','Login','Login Successful.','::1','2022-11-08 14:58:40','2022-11-08 14:58:40'),
(103,1,'Login Form','Login','Login Successful.','::1','2022-11-08 15:00:21','2022-11-08 15:00:21'),
(104,1,'Student','Update','Student user has been updated.','::1','2022-11-08 15:01:03','2022-11-08 15:01:03'),
(105,1,'Student','Update','Student user has been updated.','::1','2022-11-08 15:05:45','2022-11-08 15:05:45'),
(106,1,'Subject','Add Assignment','Add assignment successful.','::1','2022-11-08 15:06:24','2022-11-08 15:06:24'),
(107,1,'Teacher','Insert','teacher1@gmail.com has been added to teacher, with a password of 25568','::1','2022-11-08 15:09:45','2022-11-08 15:09:45'),
(108,1,'Subject','Add Teacher','Add teacher successful.','::1','2022-11-08 15:10:01','2022-11-08 15:10:01'),
(109,39,'Assignment','Submit Assignment','Your assignment has been submitted.','::1','2022-11-08 15:10:10','2022-11-08 15:10:10'),
(110,41,'Login Form','Login','Login Successful.','::1','2022-11-08 15:10:40','2022-11-08 15:10:40'),
(111,41,'Grade Book','Set Score','Score submitted.','::1','2022-11-08 15:13:32','2022-11-08 15:13:32'),
(112,41,'Subject','Add Task Performance','Add task performance successful.','::1','2022-11-08 15:14:07','2022-11-08 15:14:07'),
(113,39,'Task Performance','Submit Task Performance','Your task performance has been submitted.','::1','2022-11-08 15:14:15','2022-11-08 15:14:15'),
(114,41,'Grade Book','Set Score','Score submitted.','::1','2022-11-08 15:14:32','2022-11-08 15:14:32');

/*Table structure for table `db_backup` */

DROP TABLE IF EXISTS `db_backup`;

CREATE TABLE `db_backup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

/*Data for the table `db_backup` */

/*Table structure for table `department` */

DROP TABLE IF EXISTS `department`;

CREATE TABLE `department` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `person_in_charge` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

/*Data for the table `department` */

insert  into `department`(`id`,`code`,`name`,`person_in_charge`,`created_at`,`updated_at`) values 
(7,'Dept-39115','Information & Technology','test','2022-11-08 03:04:24','2022-11-08 03:04:24');

/*Table structure for table `grade_level` */

DROP TABLE IF EXISTS `grade_level`;

CREATE TABLE `grade_level` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_senior_high` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

/*Data for the table `grade_level` */

insert  into `grade_level`(`id`,`code`,`name`,`is_senior_high`,`created_at`,`updated_at`) values 
(20,'GL-62769','Grade 1',0,'2022-11-08 02:55:44','2022-11-08 02:55:44');

/*Table structure for table `notifications` */

DROP TABLE IF EXISTS `notifications`;

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `is_read` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=266 DEFAULT CHARSET=latin1;

/*Data for the table `notifications` */

insert  into `notifications`(`id`,`user_id`,`title`,`message`,`type`,`is_read`,`created_at`,`updated_at`) values 
(249,39,'New Lesson','A new lesson has been added to your student account.','lesson',1,'2022-11-08 03:00:58','2022-11-08 15:06:35'),
(250,40,'New Subject','A new subject has been added to your teacher account.','teacher new subject',1,'2022-11-08 03:17:30','2022-11-08 14:57:54'),
(251,39,'New Subject','A new subject has been added to your student account.','subject',1,'2022-11-08 03:52:34','2022-11-08 15:06:35'),
(252,40,'New Subject','A new subject has been added to your teacher account.','teacher new subject',1,'2022-11-08 04:03:09','2022-11-08 14:57:54'),
(253,40,'New Subject','A new subject has been added to your teacher account.','teacher new subject',1,'2022-11-08 04:20:04','2022-11-08 14:57:54'),
(254,40,'New Subject','A new subject has been added to your teacher account.','teacher new subject',1,'2022-11-08 04:42:15','2022-11-08 14:57:54'),
(255,40,'New Subject','A new subject has been added to your teacher account.','teacher new subject',1,'2022-11-08 04:52:11','2022-11-08 14:57:54'),
(256,39,'New Subject','A new subject has been added to your student account.','subject',1,'2022-11-08 04:53:37','2022-11-08 15:06:35'),
(257,40,'New Subject','A new subject has been added to your teacher account.','teacher new subject',1,'2022-11-08 04:53:43','2022-11-08 14:57:54'),
(258,40,'New Subject','A new subject has been added to your teacher account.','teacher new subject',1,'2022-11-08 04:55:07','2022-11-08 14:57:54'),
(259,40,'New Subject','A new subject has been added to your teacher account.','teacher new subject',1,'2022-11-08 04:55:13','2022-11-08 14:57:54'),
(260,40,'New Subject','A new subject has been added to your teacher account.','teacher new subject',1,'2022-11-08 05:12:11','2022-11-08 14:57:54'),
(261,39,'New Assignment','A new assignment has been added to your student account.','assignment',1,'2022-11-08 15:06:24','2022-11-08 15:06:35'),
(262,41,'New Subject','A new subject has been added to your teacher account.','teacher new subject',0,'2022-11-08 15:10:01','2022-11-08 15:10:01'),
(263,41,'Assignment','Student Test  submitted an assignment.','teacher assignment',0,'2022-11-08 15:10:10','2022-11-08 15:10:10'),
(264,39,'New Task Performance','A new task performance has been added to your student account.','task performance',0,'2022-11-08 15:14:07','2022-11-08 15:14:07'),
(265,41,'Task Performance','Student Test  submitted a task performance.','teacher task performance',0,'2022-11-08 15:14:15','2022-11-08 15:14:15');

/*Table structure for table `school_year` */

DROP TABLE IF EXISTS `school_year`;

CREATE TABLE `school_year` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

/*Data for the table `school_year` */

insert  into `school_year`(`id`,`name`,`created_at`,`updated_at`) values 
(7,'2021 - 2022','2022-11-08 00:33:12','2022-11-08 00:33:12'),
(8,'2022 - 2023','2022-11-08 00:42:25','2022-11-08 00:42:25');

/*Table structure for table `section` */

DROP TABLE IF EXISTS `section`;

CREATE TABLE `section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

/*Data for the table `section` */

insert  into `section`(`id`,`code`,`name`,`created_at`,`updated_at`) values 
(16,'Sect-94894','test','2022-11-08 02:55:32','2022-11-08 02:55:32');

/*Table structure for table `strand_course` */

DROP TABLE IF EXISTS `strand_course`;

CREATE TABLE `strand_course` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `strand_course` */

/*Table structure for table `subject` */

DROP TABLE IF EXISTS `subject`;

CREATE TABLE `subject` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `grade_level_id` int(11) NOT NULL,
  `strand_course_id` int(11) DEFAULT NULL,
  `school_year_id` int(11) NOT NULL,
  `semester` varchar(255) DEFAULT NULL,
  `grading` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

/*Data for the table `subject` */

insert  into `subject`(`id`,`code`,`name`,`description`,`grade_level_id`,`strand_course_id`,`school_year_id`,`semester`,`grading`,`created_at`,`updated_at`) values 
(15,'Subj-80857','Grade 1','test',20,NULL,8,NULL,'1st Grading','2022-11-08 02:17:56','2022-11-08 03:03:41'),
(16,'Subj-47329','Grade 1','test',20,NULL,7,NULL,'1st Grading','2022-11-08 03:52:34','2022-11-08 03:52:34'),
(17,'Subj-28082','Subject Test','test',20,NULL,7,NULL,'1st Grading','2022-11-08 04:53:37','2022-11-08 05:10:04');

/*Table structure for table `subject_assignments` */

DROP TABLE IF EXISTS `subject_assignments`;

CREATE TABLE `subject_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) NOT NULL,
  `due_date` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `total_points` int(11) NOT NULL,
  `passing_score` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject_id` (`subject_id`),
  CONSTRAINT `subject_assignments_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=latin1;

/*Data for the table `subject_assignments` */

insert  into `subject_assignments`(`id`,`subject_id`,`due_date`,`title`,`description`,`total_points`,`passing_score`,`file_name`,`created_at`,`updated_at`) values 
(45,15,'2022-11-09 15:06:00','test','tes',20,10,'homework - 1667891184 - Science Lesson 4.pdf','2022-11-08 15:06:24','2022-11-08 15:06:24');

/*Table structure for table `subject_exams` */

DROP TABLE IF EXISTS `subject_exams`;

CREATE TABLE `subject_exams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `due_date` datetime NOT NULL,
  `questions` blob NOT NULL,
  `minutes` int(11) NOT NULL,
  `total_points` int(11) NOT NULL,
  `passing_score` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject_id` (`subject_id`),
  CONSTRAINT `subject_exams_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

/*Data for the table `subject_exams` */

/*Table structure for table `subject_lessons` */

DROP TABLE IF EXISTS `subject_lessons`;

CREATE TABLE `subject_lessons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject_id` (`subject_id`),
  CONSTRAINT `subject_lessons_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=latin1;

/*Data for the table `subject_lessons` */

insert  into `subject_lessons`(`id`,`subject_id`,`title`,`description`,`file_name`,`created_at`,`updated_at`) values 
(83,15,'test','test','lesson - 1667847489 - Math Lesson 3.pdf','2022-11-08 02:58:09','2022-11-08 02:58:09'),
(84,15,'test','test','lesson - 1667847657 - Science Lesson 4.pdf','2022-11-08 03:00:58','2022-11-08 03:00:58');

/*Table structure for table `subject_quizzes` */

DROP TABLE IF EXISTS `subject_quizzes`;

CREATE TABLE `subject_quizzes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `due_date` datetime NOT NULL,
  `questions` blob NOT NULL,
  `minutes` int(11) NOT NULL,
  `total_points` int(11) NOT NULL,
  `passing_score` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject_id` (`subject_id`),
  CONSTRAINT `subject_quizzes_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

/*Data for the table `subject_quizzes` */

/*Table structure for table `subject_task_performances` */

DROP TABLE IF EXISTS `subject_task_performances`;

CREATE TABLE `subject_task_performances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) NOT NULL,
  `due_date` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `total_points` int(11) NOT NULL,
  `passing_score` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject_id` (`subject_id`),
  CONSTRAINT `subject_task_performances_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

/*Data for the table `subject_task_performances` */

insert  into `subject_task_performances`(`id`,`subject_id`,`due_date`,`title`,`description`,`total_points`,`passing_score`,`file_name`,`created_at`,`updated_at`) values 
(11,15,'2022-11-09 15:13:00','test','test',20,10,'taskperformance - 1667891646 - Math Lesson 3.pdf','2022-11-08 15:14:07','2022-11-08 15:14:07');

/*Table structure for table `subject_teachers` */

DROP TABLE IF EXISTS `subject_teachers`;

CREATE TABLE `subject_teachers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `teacher_id` (`user_id`),
  KEY `subject_id` (`subject_id`),
  CONSTRAINT `subject_teachers_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subject_teachers_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;

/*Data for the table `subject_teachers` */

insert  into `subject_teachers`(`id`,`subject_id`,`user_id`,`created_at`,`updated_at`) values 
(40,16,40,'2022-11-08 04:55:13','2022-11-08 04:55:13'),
(41,17,40,'2022-11-08 05:12:11','2022-11-08 05:12:11'),
(42,15,41,'2022-11-08 15:10:01','2022-11-08 15:10:01');

/*Table structure for table `submitted_assignments` */

DROP TABLE IF EXISTS `submitted_assignments`;

CREATE TABLE `submitted_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assignment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `score` int(11) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `answer` varchar(255) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assignment_id` (`assignment_id`),
  CONSTRAINT `submitted_assignments_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `subject_assignments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;

/*Data for the table `submitted_assignments` */

insert  into `submitted_assignments`(`id`,`assignment_id`,`user_id`,`score`,`status`,`answer`,`file_name`,`created_at`,`updated_at`) values 
(23,45,39,20,'Passed','test','','2022-11-08 15:10:10','2022-11-08 15:13:32');

/*Table structure for table `submitted_exams` */

DROP TABLE IF EXISTS `submitted_exams`;

CREATE TABLE `submitted_exams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exam_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exam_id` (`exam_id`),
  CONSTRAINT `submitted_exams_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `subject_exams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=latin1;

/*Data for the table `submitted_exams` */

/*Table structure for table `submitted_quizzes` */

DROP TABLE IF EXISTS `submitted_quizzes`;

CREATE TABLE `submitted_quizzes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quiz_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_id` (`quiz_id`),
  CONSTRAINT `submitted_quizzes_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `subject_quizzes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

/*Data for the table `submitted_quizzes` */

/*Table structure for table `submitted_task_performances` */

DROP TABLE IF EXISTS `submitted_task_performances`;

CREATE TABLE `submitted_task_performances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_performance_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `score` int(11) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `answer` varchar(255) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_performance_id` (`task_performance_id`),
  CONSTRAINT `submitted_task_performances_ibfk_1` FOREIGN KEY (`task_performance_id`) REFERENCES `subject_task_performances` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

/*Data for the table `submitted_task_performances` */

insert  into `submitted_task_performances`(`id`,`task_performance_id`,`user_id`,`score`,`status`,`answer`,`file_name`,`created_at`,`updated_at`) values 
(6,11,39,8,'Failed','asfasf','','2022-11-08 15:14:15','2022-11-08 15:14:32');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `suffix` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `role` enum('ADMIN','TEACHER','STUDENT') NOT NULL DEFAULT 'ADMIN',
  `school_year_id` int(11) DEFAULT NULL,
  `grade_level_id` int(11) DEFAULT NULL,
  `strand_course_id` int(11) DEFAULT NULL,
  `semester` varchar(255) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `sidebar_bg_color` varchar(255) DEFAULT '#212121',
  `sidebar_txt_color` varchar(255) DEFAULT '#FFFFFF',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `section_id` (`section_id`),
  KEY `department_id` (`department_id`),
  KEY `grade_level_id` (`grade_level_id`),
  KEY `strand_course_id` (`strand_course_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_ibfk_3` FOREIGN KEY (`grade_level_id`) REFERENCES `grade_level` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_ibfk_4` FOREIGN KEY (`strand_course_id`) REFERENCES `strand_course` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=latin1;

/*Data for the table `users` */

insert  into `users`(`id`,`code`,`email`,`password`,`first_name`,`middle_name`,`last_name`,`suffix`,`full_name`,`gender`,`contact`,`address`,`image`,`is_active`,`role`,`school_year_id`,`grade_level_id`,`strand_course_id`,`semester`,`section_id`,`department_id`,`sidebar_bg_color`,`sidebar_txt_color`,`created_at`,`updated_at`) values 
(1,'A-001','chavezfritzsti@gmail.com','0192023a7bbd73250516f069df18b500','Chavez',NULL,'Fritz',NULL,'Chavez Fritz','Male','09758070122','Batasan Hills',NULL,1,'ADMIN',NULL,NULL,NULL,NULL,NULL,NULL,'#212121','#FFFFFF','2022-11-07 22:08:51',NULL),
(39,'S-44503','student@gmail.com','ad6a280417a0f533d8b670c61667e1a0','Student','','Test','','Student Test ','Male','09758070122','test',NULL,1,'STUDENT',8,20,NULL,NULL,16,NULL,'#212121','#FFFFFF','2022-11-08 02:56:27','2022-11-08 15:05:45'),
(40,'T-27389','teacher@gmail.com','a426dcf72ba25d046591f81a5495eab7','Teacher','','Test','','Teacher Test ','Male','09758070122','test',NULL,1,'TEACHER',7,NULL,NULL,NULL,NULL,7,'#212121','#FFFFFF','2022-11-08 03:04:38','2022-11-08 04:54:21'),
(41,'T-12138','teacher1@gmail.com','a426dcf72ba25d046591f81a5495eab7','Teacher 1','','Test','','Teacher 1 Test ','Male','09758070122','test',NULL,1,'TEACHER',8,NULL,NULL,NULL,NULL,7,'#212121','#FFFFFF','2022-11-08 15:09:45','2022-11-08 15:09:45');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
