-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: localhost    Database: mahanaim_db
-- ------------------------------------------------------
-- Server version	8.0.45-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `academy_enrollments`
--

DROP TABLE IF EXISTS `academy_enrollments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academy_enrollments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `education` varchar(50) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `why_join` text,
  `financial_goals` text,
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `transaction_ref` varchar(255) DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT '50000.00',
  `cohort` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_ref` (`transaction_ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academy_enrollments`
--

LOCK TABLES `academy_enrollments` WRITE;
/*!40000 ALTER TABLE `academy_enrollments` DISABLE KEYS */;
/*!40000 ALTER TABLE `academy_enrollments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'The Board','$2y$10$3nyq4yZoVdBQ0cKMrTVb6u59bj4R081u.JR4Esmu1iShVSwLM8R0u','info@mahanaimimpact.com','2026-02-27 15:00:33');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `donations`
--

DROP TABLE IF EXISTS `donations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `donations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `message` text,
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `transaction_ref` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_ref` (`transaction_ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `donations`
--

LOCK TABLES `donations` WRITE;
/*!40000 ALTER TABLE `donations` DISABLE KEYS */;
/*!40000 ALTER TABLE `donations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `early_bird_deadline` date NOT NULL,
  `early_bird_price` decimal(10,2) NOT NULL,
  `regular_price` decimal(10,2) NOT NULL,
  `description` text,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (1,'Mahanaim Trade Fair 2025','Lagos Convention Center','2025-12-26','2025-11-03',15000.00,25000.00,'Join us for the biggest youth-focused trade fair of the year. Network, showcase, and grow your business.','inactive','2026-02-27 15:00:33'),(2,'Financial Literacy Summit','Abuja Business Hub','2025-03-26','2025-02-03',10000.00,15000.00,'Learn wealth creation, investment strategies, and financial freedom principles.','inactive','2026-02-27 15:00:33'),(4,'21 Times Better: A Global Gateway to Purpose, Planet, and Prosperity','Online Webinar','2026-04-04','2026-04-03',0.00,0.00,'Join us for an inspiring webinar featuring Engr Lawal Ayoola (Environment Engineer & Climate Change Advocate), Isesele Janet (Global Impact Enthusiast), and hosted by Ajileye Toluwalase. Date: April 3, 2026 at 8:00 PM - 9:30 PM WAT.','active','2026-03-27 14:23:12');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gallery`
--

DROP TABLE IF EXISTS `gallery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gallery` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `category` enum('trade_fair','outreach','mentorship','academy','community') NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `description` text,
  `upload_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','inactive') DEFAULT 'active',
  `display_order` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gallery`
--

LOCK TABLES `gallery` WRITE;
/*!40000 ALTER TABLE `gallery` DISABLE KEYS */;
/*!40000 ALTER TABLE `gallery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mentors`
--

DROP TABLE IF EXISTS `mentors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mentors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `expertise` varchar(255) NOT NULL,
  `experience` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mentors`
--

LOCK TABLES `mentors` WRITE;
/*!40000 ALTER TABLE `mentors` DISABLE KEYS */;
/*!40000 ALTER TABLE `mentors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registrations`
--

DROP TABLE IF EXISTS `registrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `registrations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `business_name` varchar(255) DEFAULT NULL,
  `ticket_type` enum('vendor','attendee','sponsor') NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `transaction_ref` varchar(255) DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_ref` (`transaction_ref`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registrations`
--

LOCK TABLES `registrations` WRITE;
/*!40000 ALTER TABLE `registrations` DISABLE KEYS */;
INSERT INTO `registrations` VALUES (4,4,'Ajileye Taiwo','Ajileyetoluwalase@gmail.com','07088916615','MIN','attendee',0.00,'pending','MIN-1775075728-9715',NULL,'2026-04-01 20:35:28'),(5,4,'Peace  Ibiyemi  Erewunmi','erewunmipeaceibiyemi@gmail.com','09120813061','Purwash LTD','attendee',0.00,'pending','MIN-1775082051-1447',NULL,'2026-04-01 22:20:51'),(6,4,'Olawale Divine-Goodness Ayowale','divineolawalejohn@gmail.com','08140831075','None','attendee',0.00,'pending','MIN-1775082128-7237',NULL,'2026-04-01 22:22:08'),(7,4,'Olawale Divine-Goodness Ayowale','divineolawalejohn@gmail.com','08140831075','None','attendee',0.00,'pending','MIN-1775082170-7237',NULL,'2026-04-01 22:22:50'),(8,4,'Peace  Ibiyemi  Erewunmi','erewunmipeaceibiyemi@gmail.com','09120813061','Purwash LTD','attendee',0.00,'pending','MIN-1775082175-7853',NULL,'2026-04-01 22:22:55'),(9,4,'Olawale Divine-Goodness Ayowale','divineolawalejohn@gmail.com','08140831075','None','attendee',0.00,'pending','MIN-1775082205-7732',NULL,'2026-04-01 22:23:25'),(10,4,'Olawale Divine-Goodness Ayowale','divineolawalejohn@gmail.com','08140831075','None','attendee',0.00,'pending','MIN-1775082297-2061',NULL,'2026-04-01 22:24:57'),(11,4,'Ibukunoluwa Praise','praiseamogbon@gmail.com','07031484629','','attendee',0.00,'pending','MIN-1775082732-9542',NULL,'2026-04-01 22:32:12'),(12,4,'Ibukunoluwa Praise','praiseamogbon@gmail.com','07031484629','','attendee',0.00,'pending','MIN-1775082805-5552',NULL,'2026-04-01 22:33:25'),(13,4,'Atobiloye Eniola','eniolaatobiloye2020@gmail.com','08138433760','Words Beyond Moments','attendee',0.00,'pending','MIN-1775083107-8036',NULL,'2026-04-01 22:38:27'),(14,4,'Atobiloye Eniola Timileyin','eniolaatobiloye2020@gmail.com','08138433760','Words Beyond Moments','attendee',0.00,'pending','MIN-1775083145-5392',NULL,'2026-04-01 22:39:05'),(15,4,'Marv','thatgirlmarvie@gmail.com','08111972094','Social media management','attendee',0.00,'pending','MIN-1775103587-9526',NULL,'2026-04-02 04:19:47'),(16,4,'Marv','thatgirlmarvie@gmail.com','08111972094','Social media management','attendee',0.00,'pending','MIN-1775103613-5572',NULL,'2026-04-02 04:20:13'),(17,4,'ABOSEDE Inioluwa OLADOKUN','abosedeoladokun001@gmail.com','08144631547','Dicta luxury collection','attendee',0.00,'pending','MIN-1775108202-8798',NULL,'2026-04-02 05:36:42'),(18,4,'ABOSEDE Inioluwa OLADOKUN','abosedeoladokun001@gmail.com','08144631547','Dicta luxury collection','attendee',0.00,'pending','MIN-1775108248-3021',NULL,'2026-04-02 05:37:28'),(19,4,'ABOSEDE Inioluwa OLADOKUN','abosedeoladokun001@gmail.com','08144631547','Dicta luxury collection','attendee',0.00,'pending','MIN-1775108293-2207',NULL,'2026-04-02 05:38:13'),(20,4,'ABOSEDE Inioluwa OLADOKUN','abosedeoladokun001@gmail.com','08144631547','Dicta luxury collection','attendee',0.00,'pending','MIN-1775108316-2191',NULL,'2026-04-02 05:38:36'),(21,4,'ABOSEDE Inioluwa OLADOKUN','abosedeoladokun001@gmail.com','08144631547','','attendee',0.00,'pending','MIN-1775108343-3612',NULL,'2026-04-02 05:39:03');
/*!40000 ALTER TABLE `registrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sponsors`
--

DROP TABLE IF EXISTS `sponsors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sponsors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `student_preference` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sponsors`
--

LOCK TABLES `sponsors` WRITE;
/*!40000 ALTER TABLE `sponsors` DISABLE KEYS */;
/*!40000 ALTER TABLE `sponsors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `uploads`
--

DROP TABLE IF EXISTS `uploads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `uploads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_size` int DEFAULT NULL,
  `uploaded_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `uploads`
--

LOCK TABLES `uploads` WRITE;
/*!40000 ALTER TABLE `uploads` DISABLE KEYS */;
/*!40000 ALTER TABLE `uploads` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-02 11:08:18
