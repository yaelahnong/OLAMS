-- MySQL dump 10.13  Distrib 8.0.34, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: olams
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `attendances`
--

DROP TABLE IF EXISTS `attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendances` (
  `attendance_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` tinyint(3) unsigned NOT NULL,
  `division_id` tinyint(3) unsigned NOT NULL,
  `reason` text NOT NULL,
  `type` enum('Sick','National') NOT NULL,
  `start_date` timestamp NULL DEFAULT NULL,
  `finish_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` tinyint(3) unsigned NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`attendance_id`),
  KEY `user_id` (`user_id`),
  KEY `division_id` (`division_id`),
  CONSTRAINT `attendances_ibfk_2` FOREIGN KEY (`division_id`) REFERENCES `m_divisions` (`division_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `attendances_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendances`
--

LOCK TABLES `attendances` WRITE;
/*!40000 ALTER TABLE `attendances` DISABLE KEYS */;
INSERT INTO `attendances` VALUES (3,1,1,'','National','2023-10-18 06:16:33','2023-10-21 06:16:33','2023-10-18 06:16:17',1,NULL,NULL),(4,2,4,'','National','2023-10-18 06:17:01','2023-10-21 06:17:01','2023-10-18 06:18:02',1,NULL,NULL),(5,3,1,'','National','2023-10-18 06:17:01','2023-10-21 06:17:01','2023-10-18 06:18:02',1,NULL,NULL),(8,4,1,'','National','2023-10-18 06:19:47','2023-10-21 06:19:47','2023-10-18 06:20:04',1,NULL,NULL),(9,5,1,'','National','2023-10-18 06:20:18','2023-10-21 06:20:18','2023-10-18 06:20:43',1,NULL,NULL),(10,6,1,'','National','2023-10-18 06:21:35','2023-10-21 06:21:35','2023-10-18 06:22:09',1,NULL,NULL),(11,7,1,'','National','2023-10-18 06:21:35','2023-10-21 06:21:35','2023-10-18 06:22:09',1,NULL,NULL),(12,8,1,'','National','2023-10-18 06:22:20','2023-10-21 06:22:20','2023-10-18 06:22:58',1,NULL,NULL),(13,9,1,'','National','2023-10-18 06:22:20','2023-10-21 06:22:20','2023-10-18 06:22:58',0,NULL,NULL),(16,10,1,'','National','2023-10-18 06:23:58','2023-10-21 06:23:58','2023-10-18 06:24:40',1,NULL,NULL),(17,11,1,'','National','2023-10-18 06:23:58','2023-10-21 06:23:58','2023-10-18 06:24:40',1,NULL,NULL),(18,12,5,'','Sick','2023-10-18 06:25:05','2023-10-20 06:25:05','2023-10-18 06:27:31',1,NULL,NULL),(19,13,3,'','Sick','2023-10-18 06:25:05','2023-10-20 06:25:05','2023-10-18 06:27:31',1,NULL,NULL),(20,14,2,'','Sick','2023-10-18 06:25:05','2023-10-20 06:25:05','2023-10-18 06:27:31',1,NULL,NULL),(21,15,2,'','Sick','2023-10-18 06:25:05','2023-10-20 06:25:05','2023-10-18 06:27:31',1,NULL,NULL),(22,16,1,'','Sick','2023-10-18 06:25:05','2023-10-20 06:25:05','2023-10-18 06:27:31',1,NULL,NULL),(23,16,6,'Sakit','Sick','2023-10-18 18:00:00','2023-10-20 17:00:00','2023-10-20 07:28:47',2,'2023-10-20 10:51:40',2),(24,20,6,'lebaran','National','2023-10-19 11:01:00','2023-10-20 22:01:00','2023-10-20 07:59:17',2,'2023-10-20 10:39:08',2);
/*!40000 ALTER TABLE `attendances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `duty_overtimes`
--

DROP TABLE IF EXISTS `duty_overtimes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `duty_overtimes` (
  `duty_overtime_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` tinyint(3) unsigned NOT NULL,
  `project_id` tinyint(3) unsigned NOT NULL,
  `division_id` tinyint(3) unsigned NOT NULL,
  `lead_count` tinyint(3) unsigned NOT NULL,
  `customer_count` tinyint(3) unsigned NOT NULL,
  `note` text DEFAULT NULL,
  `approved_by` tinyint(3) unsigned NOT NULL,
  `created_by` tinyint(3) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`duty_overtime_id`),
  KEY `user_id` (`user_id`),
  KEY `project_id` (`project_id`),
  KEY `division_id` (`division_id`),
  CONSTRAINT `duty_overtimes_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `m_projects` (`project_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `duty_overtimes_ibfk_3` FOREIGN KEY (`division_id`) REFERENCES `m_divisions` (`division_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `duty_overtimes_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `duty_overtimes`
--

LOCK TABLES `duty_overtimes` WRITE;
/*!40000 ALTER TABLE `duty_overtimes` DISABLE KEYS */;
INSERT INTO `duty_overtimes` VALUES (1,17,1,6,0,2,NULL,1,17,'2023-10-18 07:10:44',NULL,NULL),(2,18,2,6,2,5,NULL,1,18,'2023-10-18 07:10:44',NULL,NULL),(3,19,3,6,1,3,NULL,1,19,'2023-10-18 07:10:44',NULL,NULL),(4,20,4,6,0,1,NULL,1,20,'2023-10-18 07:10:44',NULL,NULL),(5,17,3,6,2,4,NULL,1,17,'2023-10-18 07:12:44',NULL,NULL),(6,18,3,6,2,4,NULL,1,18,'2023-10-18 07:12:44',NULL,NULL),(7,19,2,6,2,2,NULL,1,19,'2023-10-18 07:15:37',NULL,NULL),(8,20,2,6,2,2,NULL,1,20,'2023-10-18 07:15:37',NULL,NULL),(9,17,4,6,4,4,NULL,1,17,'2023-10-18 07:15:37',NULL,NULL),(10,18,4,6,4,4,NULL,1,18,'2023-10-18 07:15:37',NULL,NULL),(11,19,4,6,4,4,NULL,1,19,'2023-10-18 07:15:37',NULL,NULL),(12,18,2,6,1,1,NULL,1,18,'2023-10-18 07:18:00',NULL,NULL),(13,18,1,6,1,2,NULL,1,18,'2023-10-16 07:16:07',NULL,NULL),(14,18,3,6,2,3,NULL,1,18,'2023-10-10 07:16:07',NULL,NULL),(15,18,4,6,1,1,NULL,1,18,'2023-10-20 07:16:07',NULL,NULL);
/*!40000 ALTER TABLE `duty_overtimes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leaves`
--

DROP TABLE IF EXISTS `leaves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leaves` (
  `leaves_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` tinyint(3) unsigned NOT NULL,
  `division_id` tinyint(3) unsigned NOT NULL,
  `reason` text NOT NULL,
  `category` enum('Annual','Pregnancy','Important Reason','Extended') NOT NULL,
  `start_date` timestamp NULL DEFAULT NULL,
  `finish_date` timestamp NULL DEFAULT NULL,
  `status` enum('Approved','Pending','Reject') NOT NULL,
  `status_updated_at` timestamp NULL DEFAULT NULL,
  `status_updated_by` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` tinyint(3) unsigned NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`leaves_id`),
  KEY `user_id` (`user_id`),
  KEY `division_id` (`division_id`),
  CONSTRAINT `leaves_ibfk_2` FOREIGN KEY (`division_id`) REFERENCES `m_divisions` (`division_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `leaves_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leaves`
--

LOCK TABLES `leaves` WRITE;
/*!40000 ALTER TABLE `leaves` DISABLE KEYS */;
INSERT INTO `leaves` VALUES (1,2,4,'','Important Reason',NULL,NULL,'Approved',NULL,NULL,'2023-10-18 06:41:00',1,NULL,NULL),(2,3,1,'','Important Reason',NULL,NULL,'Approved',NULL,NULL,'2023-10-18 06:41:00',1,NULL,NULL),(3,4,1,'','Important Reason',NULL,NULL,'Pending',NULL,NULL,'2023-10-18 06:41:29',1,NULL,NULL),(4,5,1,'','Important Reason',NULL,NULL,'Approved',NULL,NULL,'2023-10-18 06:42:26',1,NULL,NULL),(5,6,1,'','Important Reason',NULL,NULL,'Pending',NULL,NULL,'2023-10-18 06:42:26',1,NULL,NULL),(6,7,1,'','Important Reason',NULL,NULL,'Approved',NULL,NULL,'2023-10-18 06:43:35',1,NULL,NULL),(7,8,1,'','Extended',NULL,NULL,'Reject',NULL,NULL,'2023-10-18 06:43:35',1,NULL,NULL),(8,9,1,'','Extended',NULL,NULL,'Reject',NULL,NULL,'2023-10-18 06:44:31',1,NULL,NULL),(9,10,1,'','Extended',NULL,NULL,'Reject',NULL,NULL,'2023-10-18 06:44:31',1,NULL,NULL),(10,11,1,'','Annual',NULL,NULL,'Pending',NULL,NULL,'2023-10-18 06:45:48',1,NULL,NULL),(11,12,5,'','Annual',NULL,NULL,'Pending',NULL,NULL,'2023-10-18 06:45:48',1,NULL,NULL),(12,13,3,'','Annual',NULL,NULL,'Approved',NULL,NULL,'2023-10-18 06:46:59',1,NULL,NULL),(13,14,2,'','Extended',NULL,NULL,'Approved',NULL,NULL,'2023-10-18 06:46:59',1,NULL,NULL),(14,15,2,'','Important Reason',NULL,NULL,'Approved',NULL,NULL,'2023-10-18 06:47:47',1,NULL,NULL),(15,16,1,'','Pregnancy',NULL,NULL,'Approved',NULL,NULL,'2023-10-18 06:47:47',1,NULL,NULL);
/*!40000 ALTER TABLE `leaves` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `m_basic_salaries`
--

DROP TABLE IF EXISTS `m_basic_salaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `m_basic_salaries` (
  `basic_salary_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` tinyint(3) unsigned NOT NULL,
  `total_basic_salary` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` tinyint(3) unsigned NOT NULL,
  `update_by` tinyint(3) unsigned DEFAULT NULL,
  `update_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`basic_salary_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `m_basic_salaries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `m_basic_salaries`
--

LOCK TABLES `m_basic_salaries` WRITE;
/*!40000 ALTER TABLE `m_basic_salaries` DISABLE KEYS */;
INSERT INTO `m_basic_salaries` VALUES (1,2,6000000,'2023-10-18 07:23:15',1,NULL,NULL),(2,3,10000000,'2023-10-18 07:23:15',1,NULL,NULL),(3,4,5000000,'2023-10-18 07:27:32',1,NULL,NULL),(4,5,5000000,'2023-10-18 07:27:32',1,NULL,NULL),(5,6,5000000,'2023-10-18 07:27:32',1,NULL,NULL),(6,7,5000000,'2023-10-18 07:27:32',1,NULL,NULL),(7,8,5000000,'2023-10-18 07:27:32',1,NULL,NULL),(8,9,5000000,'2023-10-18 07:27:32',1,NULL,NULL),(9,10,5000000,'2023-10-18 07:27:32',1,NULL,NULL),(10,11,5000000,'2023-10-18 07:27:32',1,NULL,NULL),(11,12,5000000,'2023-10-18 07:27:32',1,NULL,NULL),(12,13,5000000,'2023-10-18 07:27:32',1,NULL,NULL),(13,14,6000000,'2023-10-18 07:27:32',1,NULL,NULL),(14,15,5000000,'2023-10-18 07:27:32',1,NULL,NULL),(15,16,7000000,'2023-10-18 07:27:32',1,NULL,NULL);
/*!40000 ALTER TABLE `m_basic_salaries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `m_divisions`
--

DROP TABLE IF EXISTS `m_divisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `m_divisions` (
  `division_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `division_name` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` tinyint(3) unsigned NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`division_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `m_divisions`
--

LOCK TABLES `m_divisions` WRITE;
/*!40000 ALTER TABLE `m_divisions` DISABLE KEYS */;
INSERT INTO `m_divisions` VALUES (1,'Software Development','2023-10-18 05:39:14',1,NULL,NULL),(2,'General Affair	','2023-10-18 05:39:14',1,NULL,NULL),(3,'Manage Service Provider','2023-10-18 05:39:38',1,NULL,NULL),(4,'Office Administration','2023-10-18 05:39:38',1,NULL,NULL),(5,'Digital Marketing','2023-10-18 05:41:19',1,NULL,NULL),(6,'Rancang Mebel','2023-10-18 07:00:41',1,NULL,NULL);
/*!40000 ALTER TABLE `m_divisions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `m_projects`
--

DROP TABLE IF EXISTS `m_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `m_projects` (
  `project_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `project_name` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` tinyint(3) unsigned NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` tinyint(3) unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` tinyint(3) unsigned DEFAULT NULL,
  `is_deleted` enum('Y','N') DEFAULT 'N',
  PRIMARY KEY (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `m_projects`
--

LOCK TABLES `m_projects` WRITE;
/*!40000 ALTER TABLE `m_projects` DISABLE KEYS */;
INSERT INTO `m_projects` VALUES (1,'Rumah Sakit Harapan','2023-10-18 05:43:50',1,'2023-10-19 11:07:26',NULL,NULL,NULL,'N'),(2,'Gopal','2023-10-18 05:43:50',1,'2023-10-19 11:07:30',NULL,NULL,NULL,'N'),(3,'EFS','2023-10-18 05:44:09',1,'2023-10-19 13:29:52',2,NULL,NULL,'N'),(4,'MAP','2023-10-18 05:44:09',1,'2023-10-19 11:07:42',NULL,NULL,NULL,'N'),(9,'olams','2023-10-19 09:15:45',2,'2023-10-19 11:20:47',2,'2023-10-19 06:20:47',2,'Y'),(10,'OlamS','2023-10-19 11:23:52',2,'2023-10-19 13:32:47',2,'2023-10-19 08:32:47',2,'Y'),(11,'BLOB','2023-10-19 11:46:47',2,'2023-10-19 13:33:23',NULL,'2023-10-19 08:33:23',2,'Y'),(15,'Olams','2023-10-19 14:32:04',2,'2023-10-19 14:32:48',NULL,'2023-10-19 09:32:48',2,'Y'),(16,'Olams','2023-10-19 14:32:54',2,NULL,NULL,NULL,NULL,'N'),(17,'Olams','2023-10-20 02:07:08',2,'2023-10-20 02:07:15',2,NULL,NULL,'N'),(18,'RSU','2023-10-20 02:47:38',2,'2023-10-20 08:34:07',2,NULL,NULL,'N');
/*!40000 ALTER TABLE `m_projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `m_roles`
--

DROP TABLE IF EXISTS `m_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `m_roles` (
  `role_id` tinyint(3) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` tinyint(3) unsigned NOT NULL,
  `updated_by` tinyint(3) unsigned DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `m_roles`
--

LOCK TABLES `m_roles` WRITE;
/*!40000 ALTER TABLE `m_roles` DISABLE KEYS */;
INSERT INTO `m_roles` VALUES (1,'User','2023-10-18 05:04:24',1,NULL,NULL),(2,'Leader','2023-10-18 05:05:08',1,NULL,NULL),(3,'Admin','2023-10-18 05:05:08',1,NULL,NULL),(4,'Supervisor','2023-10-18 05:05:08',1,NULL,NULL);
/*!40000 ALTER TABLE `m_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `overtimes`
--

DROP TABLE IF EXISTS `overtimes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `overtimes` (
  `overtime_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` tinyint(3) unsigned NOT NULL,
  `project_id` tinyint(3) unsigned NOT NULL,
  `divisi_id` tinyint(3) unsigned NOT NULL,
  `type` enum('Normal','Urgent','Business Trip') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `finish_date` timestamp NULL DEFAULT NULL,
  `category` enum('Weekday','Weekend') NOT NULL,
  `effective_time` tinyint(3) unsigned DEFAULT NULL,
  `reason` text NOT NULL,
  `submitted_by_admin` tinyint(3) unsigned DEFAULT NULL,
  `sent_by_admin` timestamp NULL DEFAULT NULL,
  `checked_by_leader` tinyint(3) unsigned DEFAULT NULL,
  `checked_by_leader_at` timestamp NULL DEFAULT NULL,
  `status` enum('Approved','Pending','Reject') NOT NULL,
  `status_updated_at` timestamp NULL DEFAULT NULL,
  `status_updated_by` tinyint(3) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` tinyint(3) unsigned NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`overtime_id`),
  KEY `user_id` (`user_id`),
  KEY `project_id` (`project_id`),
  KEY `divisi_id` (`divisi_id`),
  CONSTRAINT `overtimes_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `m_projects` (`project_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `overtimes_ibfk_3` FOREIGN KEY (`divisi_id`) REFERENCES `m_divisions` (`division_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `overtimes_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `overtimes`
--

LOCK TABLES `overtimes` WRITE;
/*!40000 ALTER TABLE `overtimes` DISABLE KEYS */;
INSERT INTO `overtimes` VALUES (1,3,2,1,'Normal','2023-10-18 07:34:32',NULL,'Weekday',NULL,'Debbuging',NULL,NULL,NULL,NULL,'Approved',NULL,NULL,'2023-10-18 07:34:32',3,NULL,NULL),(2,4,3,1,'Normal','2023-10-18 07:35:38',NULL,'Weekday',NULL,'Refisi Tampilan Dashboard',NULL,NULL,NULL,NULL,'Approved',NULL,NULL,'2023-10-18 07:35:38',4,'2023-10-18 07:36:53',NULL),(3,5,1,1,'Normal','2023-10-18 07:36:28',NULL,'Weekday',NULL,'Debbuging',NULL,NULL,NULL,NULL,'Pending',NULL,NULL,'2023-10-18 07:36:28',5,NULL,NULL),(4,6,2,1,'Normal','2023-10-18 07:38:43',NULL,'Weekend',NULL,'Refisi Tampilan Profile',NULL,NULL,NULL,NULL,'Reject',NULL,NULL,'2023-10-18 07:38:43',6,NULL,NULL),(5,9,4,1,'Urgent','2023-10-18 07:38:43','2023-10-18 16:54:59','Weekday',NULL,'Deployment',NULL,NULL,NULL,NULL,'Approved',NULL,NULL,'2023-10-18 07:38:43',9,NULL,NULL);
/*!40000 ALTER TABLE `overtimes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `overtimes_histories`
--

DROP TABLE IF EXISTS `overtimes_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `overtimes_histories` (
  `overtime_history_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `overtime_id` tinyint(3) unsigned NOT NULL,
  `user_id` tinyint(3) unsigned NOT NULL,
  `status` enum('Approved','Pending','Reject') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` tinyint(3) unsigned NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`overtime_history_id`),
  KEY `overtime_id` (`overtime_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `overtimes_histories_ibfk_1` FOREIGN KEY (`overtime_id`) REFERENCES `overtimes` (`overtime_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `overtimes_histories_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `overtimes_histories`
--

LOCK TABLES `overtimes_histories` WRITE;
/*!40000 ALTER TABLE `overtimes_histories` DISABLE KEYS */;
INSERT INTO `overtimes_histories` VALUES (1,1,3,'Approved','2023-10-18 07:45:21',1,NULL,NULL),(2,2,4,'Approved','2023-10-18 07:45:21',1,NULL,NULL),(3,3,5,'Pending','2023-10-18 07:46:22',1,NULL,NULL),(4,4,6,'Reject','2023-10-18 07:46:22',1,NULL,NULL),(5,5,9,'Approved','2023-10-18 07:47:12',3,NULL,NULL);
/*!40000 ALTER TABLE `overtimes_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` tinyint(3) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(254) NOT NULL,
  `email` varchar(64) NOT NULL,
  `created_by` tinyint(3) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` tinyint(3) unsigned DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `m_roles` (`role_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,4,'Ahmad Bahtiarsyah','bahtiar','$2y$12$5xQWIJEIM1Zk/tEL.CYbKedaHUYbbHQrRJ6Y2m49Oo4coca/ervvG','bahtiar@example.com',1,'2023-10-18 05:18:30',NULL,NULL),(2,3,'Karin Oktiara','karin','$2y$12$/yh7xWYcapJ46NkxpctodesMcLe9nRIpAHLlMWpO0.d7neKIJxofa','karin@example.com',1,'2023-10-18 05:18:30',NULL,NULL),(3,2,'Iyu Priatna','iyu','$2y$12$APcX4fwyVMlT2HoCzVJhPu.rE3VsM2r11cgnkFYfFBcEs5b39v3cq','iyu@example.com',0,'2023-10-18 05:19:46',NULL,NULL),(4,1,'Nur Fauzi','fauzi','$2y$12$qLrE9ZAty1zJeCqSyzyq0eKGdm64AtxfLkJLG3gwgVGGcAtXnbwUq','fauzi@example.com',1,'2023-10-18 05:25:18',NULL,NULL),(5,1,'Yosi Yuniar','yosi','$2y$12$vo3Z1aTf.IbEJc.CiiRiNOiZi9vwuizJITV2bx6dFG3yC5i/WpTmK','yosi@example.com',1,'2023-10-18 05:24:13',NULL,NULL),(6,1,'Tita Aprilianti','tita','$2y$12$TlgdUfYsyFW/x3nr22qL.e8.uvh3cCzW6xPoVQVdGQ8DBA87UwHe2','tita@example.com',1,'2023-10-18 05:24:13',NULL,NULL),(7,1,'Tito Shadam','tito','$2y$12$VpF/OTQySJknm7uSq/BtNOfIlKpjB1Cvktswwyr8uyuHV5xpnctRi','tito@example.com',1,'2023-10-18 05:26:56',NULL,NULL),(8,1,'Virgianto Eko','eko','$2y$12$AQSypZrdplpm5hvJSSAY/edksAw8Zf2r/Im0TLmrtMougNe/6jyZG','eko@example.com',1,'2023-10-18 05:26:56',NULL,NULL),(9,1,'Muhammad Bara Aksayeth','bara','$2y$12$8kvYfRZdiPuVTghGgLv5euRuCJ9zHQPcLxsYfNJf9DVe/Hcu1W1LG','bara@example.com',1,'2023-10-18 05:29:05',NULL,NULL),(10,1,'Marino Imola','marino','$2y$12$PtvMvymnX88ZEL6ECf5R8.OBfKIcYtxHlucF6F06BbvjK1ZZy.EZm','marino@example.com',1,'2023-10-18 05:29:05',NULL,NULL),(11,1,'Cryan Fajri','cryan','$2y$12$9Elzi2qCQIWmGXMYPnZzQegpYgb0t6XGBhQdbYRCA4AxHUdmqM.qm','cryan@example.com',1,'2023-10-18 05:32:17',NULL,NULL),(12,1,'Mega Murdiana','mega','$2y$12$QRgTU3k2heEBW4PBQH4rvOYgDJ72u81tQ/cDxkXRWRHA8gHXUV8na','mega@example.com',1,'2023-10-18 05:32:17',NULL,NULL),(13,1,'Subhan Abdullah','subhan','$2y$12$QxiZEcHwXPh71r2cNXQe4uAMvs9fEH5ERHnn3upSNBv3bFtdnIYzO','subhan@example.com',1,'2023-10-18 05:34:17',NULL,NULL),(14,1,'Andi Baskoro','andi','$2y$12$cpYHKk1ZPPIhJlrHgGah2.5vx7uCfXwr/YmTcXoiEBAByhyxpMt0m','andi@example.com',1,'2023-10-18 05:34:17',NULL,NULL),(15,1,'Heri Gunawan','heri','$2y$12$d1SWg0JiVCgMxNZ2vfRkjeolPG1JoUhj0PZYBqWxmmcyrhEmNZDs6','heri@example.com',1,'2023-10-18 05:35:38',NULL,NULL),(16,1,'Anggraeni','eni','$2y$12$yeDcmktrYOC0p8PknUEFie52jAouV5TbEpWP5T8nfn8NEHZ762Obq','eni@example.com',1,'2023-10-18 05:35:38',NULL,NULL),(17,1,'Hafiz ','hafiz','$2y$12$Ikz0Mij1xW5f3oE2K/zT1u.Xsv33K8pMcLYtlU6862iNf7KByCgny','hafiz@example.com',1,'2023-10-18 06:53:39',NULL,NULL),(18,1,'Edo','edo','$2y$12$UXqupYQx6Eu6Tg1koDUmnO5II9wpj0zfEx658i61ZT8XMp4A/yZt6','edo@example.com',1,'2023-10-18 07:02:51',NULL,NULL),(19,1,'Rendy','rendy','$2y$12$rSTQq2rQe/UwNtPGPlpmj.X5EM.CFI6izcu6txRNhL1arTscOFn62','rendy@example.com',1,'2023-10-18 07:02:51',NULL,NULL),(20,1,'Puspa','puspa','$2y$12$MioQTTjBRC7DSP5Swm1a7.P8hr3AagILvMgPBQy95AfVhqWFOv2Z6','puspa@example.com',1,'2023-10-18 07:03:54',NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-10-23 15:46:36
