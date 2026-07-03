-- MySQL dump 10.13  Distrib 8.0.40, for macos12.7 (arm64)
--
-- Host: 127.0.0.1    Database: recepsionis_db
-- ------------------------------------------------------
-- Server version	8.0.40

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
-- Table structure for table `admin_category_routing`
--

DROP TABLE IF EXISTS `admin_category_routing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_category_routing` (
  `user_id` int NOT NULL,
  `category_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`category_id`),
  KEY `fk_acr_cat` (`category_id`),
  CONSTRAINT `fk_acr_cat` FOREIGN KEY (`category_id`) REFERENCES `complaint_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_acr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_category_routing`
--

LOCK TABLES `admin_category_routing` WRITE;
/*!40000 ALTER TABLE `admin_category_routing` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_category_routing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `appointments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `visitor_id` int DEFAULT NULL,
  `host_id` int NOT NULL,
  `nama_visitor` varchar(100) NOT NULL,
  `email_visitor` varchar(100) DEFAULT NULL,
  `no_telp_visitor` varchar(20) DEFAULT NULL,
  `perusahaan_visitor` varchar(200) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `waktu_mulai` time NOT NULL,
  `waktu_selesai` time NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `deskripsi` text,
  `reminder_sent` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `visitor_id` (`visitor_id`),
  KEY `idx_host` (`host_id`),
  KEY `idx_tanggal` (`tanggal`),
  KEY `idx_status` (`status`),
  CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE SET NULL,
  CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`host_id`) REFERENCES `hosts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appointments`
--

LOCK TABLES `appointments` WRITE;
/*!40000 ALTER TABLE `appointments` DISABLE KEYS */;
/*!40000 ALTER TABLE `appointments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `complaint_categories`
--

DROP TABLE IF EXISTS `complaint_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `complaint_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text,
  `icon` varchar(50) DEFAULT 'bi-tag',
  `warna` varchar(20) DEFAULT '#667eea',
  `status_aktif` tinyint(1) DEFAULT '1',
  `urutan` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `complaint_categories`
--

LOCK TABLES `complaint_categories` WRITE;
/*!40000 ALTER TABLE `complaint_categories` DISABLE KEYS */;
INSERT INTO `complaint_categories` VALUES (1,'Program','Pengaduan terkait program studi, pendaftaran, atau informasi akademik','bi-mortarboard','#667eea',1,1,'2026-02-18 15:44:55','2026-02-18 15:44:55'),(2,'Help Desk','Bantuan teknis, informasi umum, atau pertanyaan lainnya','bi-headset','#10b981',1,2,'2026-02-18 15:44:55','2026-02-18 15:44:55'),(3,'Lainnya','Pengaduan atau pertanyaan lainnya yang tidak termasuk kategori di atas','bi-question-circle','#f59e0b',1,3,'2026-02-18 15:44:55','2026-02-18 15:44:55');
/*!40000 ALTER TABLE `complaint_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hosts`
--

DROP TABLE IF EXISTS `hosts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hosts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `departemen` varchar(100) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `status_aktif` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hosts`
--

LOCK TABLES `hosts` WRITE;
/*!40000 ALTER TABLE `hosts` DISABLE KEYS */;
INSERT INTO `hosts` VALUES (1,'Dr. Ahmad Hidayat','ahmad.hidayat@example.com','081234567890','Teknik Informatika','Dosen',1,'2025-12-12 02:37:58','2025-12-12 02:37:58'),(2,'Siti Nurhaliza','siti.nurhaliza@example.com','081234567891','Manajemen','Kepala Departemen',1,'2025-12-12 02:37:58','2025-12-12 02:37:58'),(3,'Budi Santoso','budi.santoso@example.com','081234567892','Akuntansi','Dosen',1,'2025-12-12 02:37:58','2025-12-12 02:37:58');
/*!40000 ALTER TABLE `hosts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `live_chat_admin_state`
--

DROP TABLE IF EXISTS `live_chat_admin_state`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `live_chat_admin_state` (
  `live_session_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_user_id` int NOT NULL,
  `last_read_message_id` int NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`live_session_id`,`admin_user_id`),
  KEY `idx_lcas_admin` (`admin_user_id`),
  CONSTRAINT `fk_lcas_admin_user` FOREIGN KEY (`admin_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `live_chat_admin_state`
--

LOCK TABLES `live_chat_admin_state` WRITE;
/*!40000 ALTER TABLE `live_chat_admin_state` DISABLE KEYS */;
INSERT INTO `live_chat_admin_state` VALUES ('012112de-0398-436b-99aa-619eaa4faf08',1,0,'2026-04-08 10:24:44','2026-04-08 10:24:44'),('0560d2cd-b5ee-4f9d-94e8-09747d8d6abe',1,6,'2026-04-08 07:11:23','2026-04-08 07:11:23'),('0861a0b3-bbc3-40a1-826e-3e3c9df3c167',1,0,'2026-04-08 11:15:21','2026-04-08 11:15:21'),('0d138f0f-7b7c-4b2b-932f-06e2ed60f6c5',1,0,'2026-04-08 07:11:25','2026-04-08 07:11:25'),('2d8d04a3-17c4-416b-9337-be59bef3393a',1,0,'2026-04-08 11:15:22','2026-04-08 11:15:22'),('3321674c-3649-4be5-bf45-162ce03c2ec4',1,0,'2026-04-08 11:15:16','2026-04-08 11:15:16'),('47d691a6-fd05-48da-92f2-e5f16c8d15a1',1,0,'2026-04-08 10:24:40','2026-04-08 10:24:40'),('6ebbf40e-85b7-4239-847c-a963520c1b44',1,0,'2026-04-08 10:57:55','2026-04-08 10:57:55'),('75879164-0a7a-4a5a-9fbf-64897726b0cd',1,0,NULL,'2026-04-09 07:32:19'),('79b8564d-6f35-4500-b895-ca234b8bf0dc',1,0,'2026-04-08 08:56:49','2026-04-08 08:56:49'),('868ca0e7-520e-4ae6-879a-21aff6a1920f',1,10,'2026-04-08 10:24:36','2026-04-08 10:24:36'),('8b838c3d-df50-4132-ad08-3726e7e18e1f',1,0,'2026-04-08 08:56:54','2026-04-08 08:56:54'),('942d3698-74f3-4d12-a289-295aaa84d47e',1,0,'2026-04-08 10:24:43','2026-04-08 10:24:43'),('9440f101-60e8-40c0-a9a2-581d45d0b3d5',1,9,'2026-04-08 07:11:21','2026-04-08 07:11:21'),('9c1e0cba-0c6a-45ed-bb12-a1a60be2169b',1,15,'2026-04-08 15:36:54','2026-04-08 15:36:54'),('a524d13f-e63d-4ce8-b8a9-0f82a5c142b7',1,17,NULL,'2026-04-09 07:35:08'),('a9a3ad23-3007-420e-a859-a58d6b23c337',1,0,'2026-04-08 10:57:56','2026-04-08 10:57:56'),('ae30de4c-1b57-45c1-88a6-cfde08576963',1,0,'2026-04-08 11:15:26','2026-04-08 11:15:26'),('b46bfb2d-79f9-429a-a184-c64b45e24c83',1,0,'2026-04-08 08:56:51','2026-04-08 08:56:51'),('b8dbc4b0-f721-4132-9711-a133961b0586',1,12,'2026-04-08 10:24:34','2026-04-08 10:24:34'),('d137cb6e-1f9a-4f8d-b786-36b9aad43ba3',1,0,'2026-04-08 11:15:24','2026-04-08 11:15:24'),('ddbce97d-1bb9-4673-b49e-809794a94442',1,0,'2026-04-08 08:56:53','2026-04-08 08:56:53'),('f2410d61-4eee-49f0-a03a-6f22e8a61cfb',1,0,'2026-04-08 11:15:19','2026-04-08 11:15:19');
/*!40000 ALTER TABLE `live_chat_admin_state` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `live_chat_messages`
--

DROP TABLE IF EXISTS `live_chat_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `live_chat_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `live_session_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `staff_call_id` int DEFAULT NULL,
  `sender` enum('guest','admin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_user_id` int DEFAULT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_session` (`live_session_id`),
  KEY `idx_staff_call` (`staff_call_id`),
  KEY `fk_lcm_admin_user` (`admin_user_id`),
  CONSTRAINT `fk_lcm_admin_user` FOREIGN KEY (`admin_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_lcm_staff_call` FOREIGN KEY (`staff_call_id`) REFERENCES `staff_calls` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `live_chat_messages`
--

LOCK TABLES `live_chat_messages` WRITE;
/*!40000 ALTER TABLE `live_chat_messages` DISABLE KEYS */;
INSERT INTO `live_chat_messages` VALUES (1,'0d138f0f-7b7c-4b2b-932f-06e2ed60f6c5',33,'admin',1,'hai','2026-04-08 03:32:29'),(2,'0d138f0f-7b7c-4b2b-932f-06e2ed60f6c5',33,'guest',NULL,'ini aku mau nanya program kak','2026-04-08 03:32:40'),(3,'0d138f0f-7b7c-4b2b-932f-06e2ed60f6c5',33,'admin',1,'oh iya kak berminat di program apa?','2026-04-08 03:32:52'),(4,'0560d2cd-b5ee-4f9d-94e8-09747d8d6abe',37,'admin',1,'hai wahyu ada yang bisa di bantu?','2026-04-08 06:51:29'),(5,'0560d2cd-b5ee-4f9d-94e8-09747d8d6abe',37,'guest',NULL,'mau nanya program kak','2026-04-08 06:51:39'),(6,'0560d2cd-b5ee-4f9d-94e8-09747d8d6abe',37,'admin',1,'oh iya kk minat di program apa?','2026-04-08 06:52:01'),(7,'9440f101-60e8-40c0-a9a2-581d45d0b3d5',40,'admin',1,'ada yang bisa di bantu?','2026-04-08 06:59:38'),(8,'9440f101-60e8-40c0-a9a2-581d45d0b3d5',40,'guest',NULL,'ini kak','2026-04-08 06:59:50'),(9,'9440f101-60e8-40c0-a9a2-581d45d0b3d5',40,'guest',NULL,'anu kak','2026-04-08 06:59:52'),(10,'868ca0e7-520e-4ae6-879a-21aff6a1920f',42,'admin',1,'jon','2026-04-08 07:11:09'),(11,'b8dbc4b0-f721-4132-9711-a133961b0586',43,'admin',1,'hi','2026-04-08 10:24:14'),(12,'b8dbc4b0-f721-4132-9711-a133961b0586',43,'guest',NULL,'kamu ganteng deh','2026-04-08 10:24:20'),(13,'9c1e0cba-0c6a-45ed-bb12-a1a60be2169b',55,'admin',1,'halo halo','2026-04-08 15:36:13'),(14,'9c1e0cba-0c6a-45ed-bb12-a1a60be2169b',55,'admin',1,'ada yang bisa di bantu','2026-04-08 15:36:15'),(15,'9c1e0cba-0c6a-45ed-bb12-a1a60be2169b',55,'guest',NULL,'aku mau mie mas 1 ya','2026-04-08 15:36:23'),(16,'9c1e0cba-0c6a-45ed-bb12-a1a60be2169b',55,'admin',1,'oke di tunggu','2026-04-08 15:36:29'),(17,'a524d13f-e63d-4ce8-b8a9-0f82a5c142b7',56,'admin',1,'hallo kak','2026-04-09 07:32:23');
/*!40000 ALTER TABLE `live_chat_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `host_id` int DEFAULT NULL,
  `visitor_id` int DEFAULT NULL,
  `type` enum('checkin','appointment','queue','checkout','system') DEFAULT 'checkin',
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read') DEFAULT 'unread',
  `email_sent` tinyint(1) DEFAULT '0',
  `sms_sent` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `visitor_id` (`visitor_id`),
  KEY `idx_host` (`host_id`),
  KEY `idx_status` (`status`),
  KEY `idx_type` (`type`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`host_id`) REFERENCES `hosts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,NULL,NULL,'system','Panggilan dari: wahyu','Panggilan dari wahyu (085885529352). Keperluan: Ingin bertanya mengenai Program','read',0,0,'2025-12-12 02:55:48'),(2,NULL,NULL,'system','Panggilan dari: wahyu','Panggilan dari wahyu (085885529352). Keperluan: Ingin bertanya mengenai Program','read',0,0,'2025-12-12 02:56:01'),(3,2,1,'checkin','Tamu Baru: wahyu','wahyu telah check-in untuk bertemu dengan Anda. Tujuan: Bimbingan dengan pak yos','read',0,0,'2025-12-12 02:57:31'),(4,NULL,NULL,'system','Panggilan dari: wahyu','Panggilan dari wahyu (213131313). Keperluan: awdwdwdww','read',0,0,'2025-12-12 03:03:35'),(5,NULL,NULL,'system','Panggilan dari: aji','Panggilan dari aji (090808080). Keperluan: daftar kuliah','read',0,0,'2025-12-12 03:08:46'),(6,NULL,NULL,'system','Panggilan dari: aji','Panggilan dari aji (090808080). Keperluan: daftar kuliah','read',0,0,'2025-12-12 03:09:04'),(7,NULL,NULL,'system','Panggilan dari: wahyu','Panggilan dari wahyu (03492302). Keperluan: nanya admisison','read',0,0,'2025-12-12 03:18:44'),(8,2,NULL,'checkin','Tamu Baru: and','and telah check-in untuk bertemu dengan Anda. Tujuan: bimbingan','read',0,0,'2025-12-12 03:19:48'),(9,NULL,NULL,'system','Panggilan dari: wanjay','Panggilan dari wanjay (09103291). Keperluan: gabut','read',0,0,'2025-12-12 03:55:12'),(10,NULL,NULL,'system','Panggilan dari: joan','Panggilan dari joan (21213123). Keperluan: admission','read',0,0,'2025-12-12 06:19:27'),(11,NULL,NULL,'system','Panggilan dari: wahyu','Panggilan dari wahyu (12312312). Keperluan: admission','read',0,0,'2025-12-12 06:20:21'),(12,NULL,NULL,'system','Panggilan dari: anjay','Panggilan dari anjay (09090909090). Keperluan: admission','read',0,0,'2025-12-12 06:25:38'),(13,NULL,NULL,'system','Panggilan dari: wahyu','Panggilan dari wahyu (213123). Keperluan: mau tanya program','read',0,0,'2025-12-12 08:08:18'),(14,NULL,NULL,'system','Panggilan dari: wahyu','Panggilan dari wahyu (12313123). Keperluan: jwdjwqdad','read',0,0,'2025-12-12 09:13:55'),(15,NULL,NULL,'system','Panggilan dari: wahyu','Panggilan dari wahyu (123213). Keperluan: ketemu admission','read',0,0,'2025-12-12 11:59:43'),(16,NULL,NULL,'system','Panggilan dari: andi','Panggilan dari andi (123123123131). Keperluan: butuh it di ruang medco','read',0,0,'2026-02-05 02:14:01'),(17,NULL,NULL,'system','Panggilan dari: jony','Panggilan dari jony (08232123345). Keperluan: testing testing','read',0,0,'2026-02-05 02:16:03'),(18,NULL,NULL,'system','Panggilan dari: hallo','Panggilan dari hallo (1232133). Keperluan: testing','read',0,0,'2026-02-05 02:27:42'),(19,NULL,NULL,'system','Panggilan dari: dasada','Panggilan dari dasada (31231). Keperluan: testing','read',0,0,'2026-02-05 02:30:25'),(20,NULL,NULL,'system','Panggilan dari: fgfgfg','Panggilan dari fgfgfg (1214367). Keperluan: efhgjhjgf','read',0,0,'2026-02-05 02:35:14'),(21,NULL,NULL,'system','Panggilan dari: hai','Panggilan dari hai (8723237823). Keperluan: testing apps','read',0,0,'2026-02-05 02:47:58'),(22,NULL,NULL,'system','Panggilan dari: testing','Panggilan dari testing (testing). Keperluan: hallo','read',0,0,'2026-02-05 03:02:40'),(23,NULL,NULL,'system','Panggilan dari: wahyu adi saputro','Panggilan dari wahyu adi saputro (085885529352). Keperluan: it support dong','read',0,0,'2026-02-05 03:08:21'),(24,NULL,NULL,'system','Panggilan dari: wahyu nih','Panggilan dari wahyu nih (085711970888). Keperluan: testing API whatsapps','read',0,0,'2026-02-05 03:09:34'),(25,NULL,NULL,'system','Panggilan dari: wahyu','Panggilan dari wahyu (0823082103). Keperluan: infoo','read',0,0,'2026-02-05 03:17:12'),(26,NULL,NULL,'system','Panggilan dari: test','Panggilan dari test (6285885529352). Keperluan: test','read',0,0,'2026-02-05 03:27:52'),(27,NULL,NULL,'system','Panggilan dari: UJI COBA TEST E-Recesionis','Panggilan dari UJI COBA TEST E-Recesionis (6281232893316). Keperluan: Testing auto respon E-Recepsionis','read',0,0,'2026-02-05 03:29:09'),(28,NULL,NULL,'system','Panggilan dari: wahyu adi saputro (TESTING)','Panggilan dari wahyu adi saputro (TESTING) (085885529352). Keperluan: (Testing) Selamat siang saya minta bantuan untuk admision dong','read',0,0,'2026-02-05 03:30:58'),(29,NULL,NULL,'system','Panggilan dari: TESTING (andi sudrajat diningrat mangubroto)','Panggilan dari TESTING (andi sudrajat diningrat mangubroto) (080808080). Keperluan: TESTING - saya butuh admission dong,mau nanya soal program','read',0,0,'2026-02-05 03:32:55'),(30,NULL,NULL,'system','Panggilan dari: TESTING','Panggilan dari TESTING (080808080). Keperluan: bisakah saya bertemu dengan admission untuk menanyakan programm, saya di recepsionis ya \r\nTerima kasih','read',0,0,'2026-02-05 03:36:11'),(31,NULL,NULL,'system','Panggilan dari: testing','Panggilan dari testing (012930139). Keperluan: testing\nRuangan: Ruang Meeting','read',0,0,'2026-02-06 03:43:21'),(32,NULL,NULL,'system','Panggilan dari: wahyu','Panggilan dari wahyu (92382103). Keperluan: WOY MAUL\nRuangan: Ruang Lab Komputer 1','read',0,0,'2026-02-06 07:35:26'),(33,NULL,NULL,'system','Panggilan dari: Wahyu adi saputro','Panggilan dari Wahyu adi saputro (080808080). Keperluan: Ingin bertemu dengan Team Operasional\nRuangan: Ruang Meeting','read',0,0,'2026-02-08 15:48:43'),(34,NULL,NULL,'system','Panggilan dari: testing','Panggilan dari testing (098090809). Keperluan: Admission\nRuangan: Ruang Seminar','read',0,0,'2026-02-08 15:57:36'),(35,NULL,NULL,'system','Panggilan dari: wdwd','Panggilan dari wdwd (1231231). Keperluan: asafadwad\nRuangan: Ruang Lab Komputer 1','read',0,0,'2026-02-11 06:45:20'),(36,NULL,NULL,'system','Panggilan dari: sdsd','Panggilan dari sdsd (1231). Keperluan: esadasd\nRuangan: Ruang Lab Komputer 1','read',0,0,'2026-02-11 06:51:39'),(37,NULL,NULL,'system','Panggilan dari: wd','Panggilan dari wd (323123). Keperluan: sdqwdawd\nRuangan: Ruang Lab Komputer 1','read',0,0,'2026-02-11 06:56:23'),(38,NULL,NULL,'system','Panggilan dari: dwwdw','Panggilan dari dwwdw (21321). Keperluan: 123213\nRuangan: Ruang Lab Komputer 1','read',0,0,'2026-02-11 06:59:55'),(39,NULL,NULL,'system','Panggilan dari: wahyu','Panggilan dari wahyu (085885529352). Keperluan: s\nRuangan: Ruang Kelas 301','read',0,0,'2026-02-11 15:05:38'),(40,NULL,NULL,'system','Panggilan dari: wahyu adi saputro','Panggilan dari wahyu adi saputro (08021312083). Keperluan: tanya program','read',0,0,'2026-02-18 15:47:51'),(41,NULL,NULL,'system','Panggilan dari: wahyu','Panggilan dari wahyu (213891238)\nKategori: Program\nKeperluan: saya ingin tanya program','read',0,0,'2026-02-18 15:51:16'),(42,NULL,NULL,'system','Panggilan dari: wahyu adi saputro','Panggilan dari wahyu adi saputro (085885529352)\nKategori: Help Desk\nKeperluan: help desk','read',0,0,'2026-04-07 05:40:15'),(43,NULL,NULL,'system','Panggilan dari: wahyu','Panggilan dari wahyu (087718838762)\nKategori: Help Desk\nKeperluan: kelas','read',0,0,'2026-04-07 07:21:09'),(44,NULL,NULL,'system','Panggilan dari: andi','Panggilan dari andi (123123131)\nKategori: Help Desk\nKeperluan: mau nanya program','read',0,0,'2026-04-07 07:23:18'),(45,NULL,NULL,'system','Panggilan dari: wahyu','Panggilan dari wahyu (8493843948)\nKategori: Program\nKeperluan: nanya','read',0,0,'2026-04-07 14:49:51'),(46,NULL,NULL,'system','Panggilan dari: jon','Panggilan dari jon (131312)\nKategori: Help Desk\nKeperluan: awdadw','read',0,0,'2026-04-07 14:55:49'),(47,NULL,NULL,'system','Panggilan dari: adawdad','Panggilan dari adawdad (123131)\nKategori: Program\nKeperluan: aeadaw','read',0,0,'2026-04-07 15:35:05'),(48,NULL,NULL,'system','Live chat: wahyu','wahyu (085885529352)\nKategori: Program\ndwdawd','read',0,0,'2026-04-08 02:59:57'),(49,NULL,NULL,'system','Live chat: wahyu','wahyu (085885529352)\nKategori: Program\ndwdawd','read',0,0,'2026-04-08 03:02:00'),(50,NULL,NULL,'system','Live chat: aead','aead (085885529352)\nKategori: Program\nprogram','read',0,0,'2026-04-08 03:32:20'),(51,NULL,NULL,'system','Live chat: jdjawdja','jdjawdja (02193012)\nKategori: Program\nadawd','read',0,0,'2026-04-08 03:35:46'),(52,NULL,NULL,'system','Live chat: wahyu','wahyu (085885529352)\nKategori: Help Desk\nruang medco batrai mic abis','read',0,0,'2026-04-08 03:57:29'),(53,NULL,NULL,'system','Live chat: jon','jon (898989)\nKategori: Help Desk\nprogram','read',0,0,'2026-04-08 04:09:55'),(54,NULL,NULL,'system','Live chat: wahyu','wahyu (21092310)\nKategori: Program\nsdsd','read',0,0,'2026-04-08 06:51:05'),(55,NULL,NULL,'system','Live chat: andi','andi (2131)\nKategori: Help Desk\ndsd','read',0,0,'2026-04-08 06:52:25'),(56,NULL,NULL,'system','Live chat: sdsd','sdsd (1231)\nKategori: Help Desk\nsdada','read',0,0,'2026-04-08 06:56:35'),(57,NULL,NULL,'system','Live chat: dsdsd','dsdsd (12323)\nKategori: Help Desk\nasdada','read',0,0,'2026-04-08 06:59:17'),(58,NULL,NULL,'system','Live chat: dsdsd','dsdsd (12323)\nKategori: Help Desk\nasdada','read',0,0,'2026-04-08 07:08:27'),(59,NULL,NULL,'system','Live chat: jhon','jhon (085885529352)\nKategori: Help Desk\nsdsdsd','read',0,0,'2026-04-08 07:10:49'),(60,NULL,NULL,'system','Live chat: wadawd','wadawd (085885529352)\nKategori: Program\nadsada','read',0,0,'2026-04-08 10:24:01'),(61,NULL,NULL,'system','Live chat: joesep','joesep (085885529352)\nKategori: Program\nsaya mau nanya tentang program','read',0,0,'2026-04-08 10:53:31'),(62,NULL,NULL,'system','Live chat: wahyu','wahyu (085885529352)\nKategori: Program\nsaya mau tanya dong','read',0,0,'2026-04-08 10:56:44'),(63,NULL,NULL,'system','Live chat: wahyu','wahyu (085885529352)\nKategori: Program\ndadsdada','read',0,0,'2026-04-08 10:58:23'),(64,NULL,NULL,'system','Live chat: wadwad','wadwad (081381525044)\nKategori: Program\nHAI manis','read',0,0,'2026-04-08 11:00:44'),(65,NULL,NULL,'system','Panggilan dari: Tes WA','Panggilan dari Tes WA (08123456789)\nKategori: Program\nKeperluan: uji wa','read',0,0,'2026-04-08 11:01:53'),(66,NULL,NULL,'system','Panggilan dari: Tes WA2','Panggilan dari Tes WA2 (08123456789)\nKategori: Program\nKeperluan: uji wa2','read',0,0,'2026-04-08 11:02:12'),(67,NULL,NULL,'system','Live chat: JENNY BLACK PINK','JENNY BLACK PINK (085885529352)\nKategori: Help Desk\ntolongggg','read',0,0,'2026-04-08 11:03:13'),(68,NULL,NULL,'system','Live chat: sadad','sadad (12313)\nKategori: Help Desk\nwedwdw','read',0,0,'2026-04-08 11:06:21'),(69,NULL,NULL,'system','Live chat: jenny','jenny (085885529352)\nKategori: Help Desk\njonnn','read',0,0,'2026-04-08 11:08:39'),(70,NULL,NULL,'system','Live chat: sdsds','sdsds (1231)\nKategori: Program\nsdasda','read',0,0,'2026-04-08 11:11:10'),(71,NULL,NULL,'system','Panggilan dari: Tes Flow WA','Panggilan dari Tes Flow WA (08111111111)\nKategori: Program\nKeperluan: tes notifikasi admin','read',0,0,'2026-04-08 11:13:10'),(72,NULL,NULL,'system','Live chat: adawd','adawd (12312313)\nKategori: Program\nqdadawd','unread',0,0,'2026-04-08 15:36:00'),(73,NULL,NULL,'system','Live chat: wahyuadi','wahyuadi (08080)\nKategori: Help Desk\nwedadawd','unread',0,0,'2026-04-09 07:32:16');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prodi`
--

DROP TABLE IF EXISTS `prodi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `prodi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_prodi` varchar(200) NOT NULL,
  `kode_prodi` varchar(50) DEFAULT NULL,
  `penjelasan` text,
  `kontak_person` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `direct_link` varchar(500) DEFAULT NULL,
  `fakultas` varchar(100) DEFAULT NULL,
  `jenjang` enum('D3','S1','S2','S3') DEFAULT 'S1',
  `foto` varchar(255) DEFAULT NULL,
  `status_aktif` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_prodi` (`kode_prodi`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prodi`
--

LOCK TABLES `prodi` WRITE;
/*!40000 ALTER TABLE `prodi` DISABLE KEYS */;
INSERT INTO `prodi` VALUES (1,'Magister Teknik Fisika','FTI','Fakultas Teknologi Industri, Institut Teknologi Bandung (FTI ITB) diresmikan pada tahun 1972. Namun sebagian kegiatan akademik di departemen yang berada di bawah naungan FTI ITB telah dilaksanakan sebelumnya. Hingga Desember 2005, departemen yang berada di bawah naungan FTI ITB adalah Departemen: Teknik Kimia, Teknik Mesin, Teknik Elektro, Teknik Fisika, Teknik Industri, Teknik Informatika, dan Teknik Penerbangan.','','','','https://fti.itb.ac.id/en/','Fakultas Teknologi Industri (FTI)','S2',NULL,1,'2025-12-13 10:48:08','2026-04-07 14:42:31'),(2,'Sistem Informasi','SI','Program studi yang menggabungkan ilmu komputer dengan bisnis. Mempelajari pengembangan sistem informasi untuk mendukung proses bisnis organisasi.','Prof. Siti Nurhaliza','siti.nurhaliza@kampus.ac.id','081234567891','https://www.kampus.ac.id/sistem-informasi','Fakultas Teknik','S1',NULL,1,'2025-12-13 10:48:08','2026-02-11 06:31:51'),(3,'Magister Farmasi','SF','fakultas ini merupakan bagian dari Univertitas Indonesia. Pada tanggal 1 Februari 1949, fakultas ini diubah menjadi Fakultas Ilmu Pengetahuan dan Ilmu Alam (FIPIA), namun tetap berada di bawah Universitas Indonesia. Struktur organisasi Departemen Farmasi sangat sederhana, hanya satu orang yang bertanggung jawab untuk mengatur departemen, namun sejak 1959, organisasi berkembang dan seorang sekretaris diangkat untuk membantu ketua departemen.','','','','https://fa.itb.ac.id/','Sekolah Farmasi (SF)','S2',NULL,1,'2025-12-13 10:48:08','2026-04-07 14:35:42'),(4,'Manajemen Informatika','MI','Program studi D3 yang mempelajari aplikasi teknologi informasi dalam manajemen bisnis. Fokus pada praktik dan implementasi langsung.','Dewi Sartika, S.Kom., M.Kom.','dewi.sartika@kampus.ac.id','081234567893','https://www.kampus.ac.id/manajemen-informatika','Fakultas Teknik','D3','698bf8056184c.jpg',1,'2025-12-13 10:48:08','2026-02-11 06:31:51'),(5,'Sekolah Bisnis dan Manajemen (SBM)','SBM','The Industrial Engineering Department at ITB recognized the importance of business and management education as early as the 1970s. This vision was realized in 1990 when Prof. Mathias A’roef introduced the MBA program, which later became the foundation for the establishment of SBM ITB.','','','','https://www.sbm.itb.ac.id/','Sekolah Bisnis dan Manajemen (SBM)','S2','693d49a098299.jpg',1,'2025-12-13 10:48:08','2026-04-07 14:37:04');
/*!40000 ALTER TABLE `prodi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `programs`
--

DROP TABLE IF EXISTS `programs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `programs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) NOT NULL,
  `deskripsi` text,
  `kategori` enum('Perkuliahan','Seminar','Workshop','Event','Lainnya') DEFAULT 'Perkuliahan',
  `tanggal` date DEFAULT NULL,
  `waktu_mulai` time DEFAULT NULL,
  `waktu_selesai` time DEFAULT NULL,
  `lokasi` varchar(200) DEFAULT NULL,
  `pengajar` varchar(100) DEFAULT NULL,
  `kontak` varchar(100) DEFAULT NULL,
  `status_aktif` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `programs`
--

LOCK TABLES `programs` WRITE;
/*!40000 ALTER TABLE `programs` DISABLE KEYS */;
INSERT INTO `programs` VALUES (1,'Workshop Web Development','Workshop membuat website modern dengan HTML, CSS, dan JavaScript','Workshop','2025-12-12','09:00:00','12:00:00','Lab Komputer 1','Dr. Ahmad Hidayat','ahmad@example.com',1,'2025-12-12 02:50:06','2025-12-12 02:50:06'),(2,'Seminar Teknologi AI','Seminar tentang Artificial Intelligence dan Machine Learning','Seminar','2025-12-13','13:00:00','16:00:00','Ruang Seminar','Prof. Siti Nurhaliza','siti@example.com',1,'2025-12-12 02:50:06','2025-12-12 02:50:06'),(3,'Kuliah Algoritma & Struktur Data','Perkuliahan reguler mata kuliah Algoritma','Perkuliahan','2025-12-12','08:00:00','10:00:00','Ruang Kelas 301','Budi Santoso','budi@example.com',1,'2025-12-12 02:50:06','2025-12-12 02:50:06');
/*!40000 ALTER TABLE `programs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `queue`
--

DROP TABLE IF EXISTS `queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `queue` (
  `id` int NOT NULL AUTO_INCREMENT,
  `visitor_id` int NOT NULL,
  `host_id` int NOT NULL,
  `nomor_antrian` varchar(20) NOT NULL,
  `status` enum('waiting','in-progress','completed','cancelled') DEFAULT 'waiting',
  `waktu_masuk` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `waktu_dipanggil` timestamp NULL DEFAULT NULL,
  `waktu_selesai` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `visitor_id` (`visitor_id`),
  KEY `idx_host` (`host_id`),
  KEY `idx_status` (`status`),
  KEY `idx_nomor` (`nomor_antrian`),
  CONSTRAINT `queue_ibfk_1` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `queue_ibfk_2` FOREIGN KEY (`host_id`) REFERENCES `hosts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `queue`
--

LOCK TABLES `queue` WRITE;
/*!40000 ALTER TABLE `queue` DISABLE KEYS */;
INSERT INTO `queue` VALUES (1,1,2,'A001','completed','2025-12-12 02:57:31','2025-12-12 03:55:31','2025-12-12 03:55:42','2025-12-12 02:57:31');
/*!40000 ALTER TABLE `queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rooms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_ruangan` varchar(100) NOT NULL,
  `kode_ruangan` varchar(50) NOT NULL,
  `lokasi` varchar(200) DEFAULT NULL,
  `lantai` varchar(50) DEFAULT NULL,
  `gedung` varchar(100) DEFAULT NULL,
  `kapasitas` int DEFAULT '0',
  `deskripsi` text,
  `foto` varchar(255) DEFAULT NULL,
  `status_aktif` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `perangkat` text,
  `mode_ruangan` varchar(100) DEFAULT NULL,
  `images` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_ruangan` (`kode_ruangan`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
INSERT INTO `rooms` VALUES (1,'Kirana Megantara 1','KM-1','Gedung A Lantai 12','12','Gedung A',25,'Ruang kelas',NULL,1,'2025-12-12 02:50:06','2026-04-07 07:49:30','Support Hybrid\\r\\nMicrophone\\r\\nKamera Konferensi\\r\\nSmartboard Interactiv','Hybrid','uploads/rooms/room_1_1775548163_6830.jpg'),(2,'Kirana Megantara 2','KM-02','Gedung B Lantai 12','12','Gedung B',25,'Ruang kelas',NULL,1,'2025-12-12 02:50:06','2026-04-07 07:45:33','Smartboard\nMicrophone\nKamera','Hybrid','uploads/rooms/room_2_1775547921_6619.jpg,uploads/rooms/room_2_1775547933_7693.jpg'),(3,'Medco Amphitheater','MA-1','Gedung B Lantai 12','12','Gedung B',66,'Ruang Amphitheater',NULL,1,'2025-12-12 02:50:06','2026-04-07 07:47:14','Smartboard\nMicrophone\nKamera\nProyektor','Hybrid','uploads/rooms/room_3_1775548016_6919.jpg,uploads/rooms/room_3_1775548034_7841.jpg'),(4,'Henk Uno Amphitheater','HUA-1','Gedung A Lantai 12','12','Gedung A',66,'Ruang kelas amphitheater',NULL,1,'2025-12-12 02:50:06','2026-04-07 07:35:34','Smartboard\nMicrophone\nKamera\nProyektor','Hybrid','uploads/rooms/room_4_1775546939_5561.jpg,uploads/rooms/room_4_1770347144_9855.jpg,uploads/rooms/room_4_1770347144_9754.jpg,uploads/rooms/room_4_1770347144_6722.jpg'),(11,'Noni Purnomo Amphitheater','NPA-1','Gedung B Lantai 12','12','Gedung B',66,'Ruang Amphitheater',NULL,1,'2026-04-07 07:44:18','2026-04-07 08:01:56','Smartboard\nMicrophone\nKamera\nProyektor','Hybrid','uploads/rooms/room_11_1775548916_5563.jpg');
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `setting_type` varchar(50) DEFAULT 'string',
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=211 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'site_name','E-Recepsionis System','2025-12-12 02:37:58','string',NULL,'2026-02-06 03:16:03'),(2,'site_email','noreply@recepsionis.local','2025-12-12 02:37:58','string',NULL,'2026-02-06 03:16:03'),(3,'queue_enabled','1','2025-12-12 02:37:58','string',NULL,'2026-02-06 03:16:03'),(4,'badge_enabled','1','2025-12-12 02:37:58','string',NULL,'2026-02-06 03:16:03'),(5,'email_notification','1','2025-12-12 02:37:58','string',NULL,'2026-02-06 03:16:03'),(6,'sms_notification','1','2025-12-12 02:39:38','string',NULL,'2026-02-06 03:16:03'),(7,'auto_checkout_hours','8','2025-12-12 02:37:58','string',NULL,'2026-02-06 03:16:03'),(15,'wa_enabled','1','2026-02-05 02:29:56','string',NULL,'2026-02-06 03:16:03'),(16,'wa_api_url','https://api.fonnte.com/send','2026-02-05 02:29:56','string',NULL,'2026-02-06 03:16:03'),(17,'wa_api_token','ZcNwfGwNMqRXuHEmFiak','2026-02-05 02:29:56','string',NULL,'2026-02-06 03:16:03'),(18,'wa_admin_phones','6285885529352','2026-02-11 06:51:29','string',NULL,'2026-02-06 03:16:03'),(137,'thumbnail_height','180','2026-02-06 03:16:03','number','Tinggi thumbnail preview (px)','2026-02-06 03:16:03'),(138,'thumbnail_border_radius','12','2026-02-06 03:16:03','number','Border radius thumbnail (px)','2026-02-06 03:16:03'),(139,'thumbnail_bg_color','#e2e8f0','2026-02-06 03:16:03','color','Warna background placeholder thumbnail','2026-02-06 03:16:03'),(140,'thumbnail_margin_bottom','15','2026-02-06 03:16:03','number','Margin bawah thumbnail (px)','2026-02-06 03:16:03');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff_calls`
--

DROP TABLE IF EXISTS `staff_calls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff_calls` (
  `id` int NOT NULL AUTO_INCREMENT,
  `visitor_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `visitor_phone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `host_id` int DEFAULT NULL,
  `call_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `message` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','answered','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `answered_by` int DEFAULT NULL,
  `answered_at` timestamp NULL DEFAULT NULL,
  `whatsapp_sent` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `wa_http_code` int DEFAULT NULL,
  `wa_response` mediumtext COLLATE utf8mb4_unicode_ci,
  `room_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `room_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visitor_id` int DEFAULT NULL,
  `live_session_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `live_status` enum('waiting','active','ended') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `host_id` (`host_id`),
  KEY `visitor_id` (`visitor_id`),
  KEY `category_id` (`category_id`),
  KEY `idx_staff_calls_live_session` (`live_session_id`),
  CONSTRAINT `staff_calls_ibfk_1` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE SET NULL,
  CONSTRAINT `staff_calls_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `complaint_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_calls`
--

LOCK TABLES `staff_calls` WRITE;
/*!40000 ALTER TABLE `staff_calls` DISABLE KEYS */;
INSERT INTO `staff_calls` VALUES (1,'hallo','1232133',NULL,'general','testing','answered',1,'2026-02-05 03:38:50',0,'2026-02-05 02:27:42','2026-02-05 03:38:50',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,'dasada','31231',NULL,'general','testing','answered',1,'2026-02-05 03:38:50',1,'2026-02-05 02:30:25','2026-02-05 03:38:50',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,'fgfgfg','1214367',NULL,'general','efhgjhjgf','answered',1,'2026-02-05 03:38:49',1,'2026-02-05 02:35:14','2026-02-05 03:38:49',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,'hai','8723237823',NULL,'general','testing apps','answered',1,'2026-02-05 03:38:47',1,'2026-02-05 02:47:58','2026-02-05 03:38:47',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,'testing','testing',NULL,'general','hallo','answered',1,'2026-02-05 03:38:47',1,'2026-02-05 03:02:40','2026-02-05 03:38:47',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(6,'wahyu adi saputro','085885529352',NULL,'general','it support dong','answered',1,'2026-02-05 03:38:47',1,'2026-02-05 03:08:21','2026-02-05 03:38:47',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,'wahyu nih','085711970888',NULL,'general','testing API whatsapps','answered',1,'2026-02-05 03:38:46',1,'2026-02-05 03:09:34','2026-02-05 03:38:46',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,'wahyu','0823082103',NULL,'general','infoo','answered',1,'2026-02-05 03:38:45',1,'2026-02-05 03:17:12','2026-02-05 03:38:45',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"reason\\\":\\\"invalid token\\\",\\\"status\\\":false}\",\"error\":null}]',NULL,NULL,NULL,NULL,NULL,NULL),(9,'test','6285885529352',NULL,'general','test','answered',1,'2026-02-05 03:38:45',1,'2026-02-05 03:27:52','2026-02-05 03:38:45',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[142223099],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":967,\\\"remaining\\\":966,\\\"used\\\":1}},\\\"requestid\\\":369052479,\\\"status\\\":true,\\\"target\\\":[\\\"6285885529352\\\"]}\",\"error\":null}]',NULL,NULL,NULL,NULL,NULL,NULL),(10,'UJI COBA TEST E-Recesionis','6281232893316',NULL,'general','Testing auto respon E-Recepsionis','answered',1,'2026-02-05 03:38:44',1,'2026-02-05 03:29:09','2026-02-05 03:38:44',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[142223292],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":966,\\\"remaining\\\":965,\\\"used\\\":1}},\\\"requestid\\\":369054480,\\\"status\\\":true,\\\"target\\\":[\\\"6285885529352\\\"]}\",\"error\":null}]',NULL,NULL,NULL,NULL,NULL,NULL),(11,'wahyu adi saputro (TESTING)','085885529352',NULL,'general','(Testing) Selamat siang saya minta bantuan untuk admision dong','answered',1,'2026-02-05 03:38:43',1,'2026-02-05 03:30:58','2026-02-05 03:38:43',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[142223796],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":965,\\\"remaining\\\":964,\\\"used\\\":1}},\\\"requestid\\\":369056936,\\\"status\\\":true,\\\"target\\\":[\\\"6285885529352\\\"]}\",\"error\":null},{\"phone\":\"6281232893316\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[142223800],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":964,\\\"remaining\\\":963,\\\"used\\\":1}},\\\"requestid\\\":369056942,\\\"status\\\":true,\\\"target\\\":[\\\"6281232893316\\\"]}\",\"error\":null}]',NULL,NULL,NULL,NULL,NULL,NULL),(12,'TESTING (andi sudrajat diningrat mangubroto)','080808080',NULL,'general','TESTING - saya butuh admission dong,mau nanya soal program','answered',1,'2026-02-05 03:38:42',1,'2026-02-05 03:32:55','2026-02-05 03:38:42',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[142224223],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":963,\\\"remaining\\\":962,\\\"used\\\":1}},\\\"requestid\\\":369059073,\\\"status\\\":true,\\\"target\\\":[\\\"6285885529352\\\"]}\",\"error\":null},{\"phone\":\"6281232893316\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[142224224],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":962,\\\"remaining\\\":961,\\\"used\\\":1}},\\\"requestid\\\":369059075,\\\"status\\\":true,\\\"target\\\":[\\\"6281232893316\\\"]}\",\"error\":null},{\"phone\":\"6285711970888\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[142224226],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":961,\\\"remaining\\\":960,\\\"used\\\":1}},\\\"requestid\\\":369059081,\\\"status\\\":true,\\\"target\\\":[\\\"6285711970888\\\"]}\",\"error\":null}]',NULL,NULL,NULL,NULL,NULL,NULL),(13,'TESTING','080808080',NULL,'general','bisakah saya bertemu dengan admission untuk menanyakan programm, saya di recepsionis ya \r\nTerima kasih','answered',1,'2026-02-05 03:38:40',1,'2026-02-05 03:36:11','2026-02-05 03:38:40',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[142224836],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":960,\\\"remaining\\\":959,\\\"used\\\":1}},\\\"requestid\\\":369066292,\\\"status\\\":true,\\\"target\\\":[\\\"6285885529352\\\"]}\",\"error\":null},{\"phone\":\"6281232893316\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[142224840],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":959,\\\"remaining\\\":958,\\\"used\\\":1}},\\\"requestid\\\":369066312,\\\"status\\\":true,\\\"target\\\":[\\\"6281232893316\\\"]}\",\"error\":null},{\"phone\":\"6285711970888\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[142224842],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":958,\\\"remaining\\\":957,\\\"used\\\":1}},\\\"requestid\\\":369066330,\\\"status\\\":true,\\\"target\\\":[\\\"6285711970888\\\"]}\",\"error\":null},{\"phone\":\"6281395320294\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[142224853],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":957,\\\"remaining\\\":956,\\\"used\\\":1}},\\\"requestid\\\":369066356,\\\"status\\\":true,\\\"target\\\":[\\\"6281395320294\\\"]}\",\"error\":null}]',NULL,NULL,NULL,NULL,NULL,NULL),(14,'testing','012930139',NULL,'general','testing','answered',1,'2026-02-06 07:39:53',1,'2026-02-06 03:43:21','2026-02-06 07:39:53',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[142397541],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":954,\\\"remaining\\\":953,\\\"used\\\":1}},\\\"requestid\\\":370587341,\\\"status\\\":true,\\\"target\\\":[\\\"6285885529352\\\"]}\",\"error\":null},{\"phone\":\"6281232893316\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[142397542],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":953,\\\"remaining\\\":952,\\\"used\\\":1}},\\\"requestid\\\":370587342,\\\"status\\\":true,\\\"target\\\":[\\\"6281232893316\\\"]}\",\"error\":null},{\"phone\":\"6285711970888\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[142397543],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":952,\\\"remaining\\\":951,\\\"used\\\":1}},\\\"requestid\\\":370587343,\\\"status\\\":true,\\\"target\\\":[\\\"6285711970888\\\"]}\",\"error\":null},{\"phone\":\"6281395320294\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[142397544],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":951,\\\"remaining\\\":950,\\\"used\\\":1}},\\\"requestid\\\":370587345,\\\"status\\\":true,\\\"target\\\":[\\\"6281395320294\\\"]}\",\"error\":null}]',4,NULL,'Ruang Meeting',NULL,NULL,NULL),(15,'wahyu','92382103',NULL,'general','WOY MAUL','answered',1,'2026-02-06 07:39:52',1,'2026-02-06 07:35:26','2026-02-06 07:39:52',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[142431455],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":950,\\\"remaining\\\":949,\\\"used\\\":1}},\\\"requestid\\\":370989942,\\\"status\\\":true,\\\"target\\\":[\\\"6285885529352\\\"]}\",\"error\":null},{\"phone\":\"6287811229374\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[142431456],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":949,\\\"remaining\\\":948,\\\"used\\\":1}},\\\"requestid\\\":370989947,\\\"status\\\":true,\\\"target\\\":[\\\"6287811229374\\\"]}\",\"error\":null}]',1,NULL,'Ruang Lab Komputer 1',NULL,NULL,NULL),(16,'Wahyu adi saputro','080808080',NULL,'general','Ingin bertemu dengan Team Operasional','answered',1,'2026-02-08 15:54:50',1,'2026-02-08 15:48:43','2026-02-08 15:54:50',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[142694029],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":944,\\\"remaining\\\":943,\\\"used\\\":1}},\\\"requestid\\\":374109330,\\\"status\\\":true,\\\"target\\\":[\\\"6285885529352\\\"]}\",\"error\":null},{\"phone\":\"6281381525044\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[142694031],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":943,\\\"remaining\\\":942,\\\"used\\\":1}},\\\"requestid\\\":374109338,\\\"status\\\":true,\\\"target\\\":[\\\"6281381525044\\\"]}\",\"error\":null}]',4,NULL,'Ruang Meeting',3,NULL,NULL),(17,'testing','098090809',NULL,'general','Admission','answered',1,'2026-02-08 15:58:14',1,'2026-02-08 15:57:36','2026-02-08 15:58:14',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[142694368],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":942,\\\"remaining\\\":941,\\\"used\\\":1}},\\\"requestid\\\":374115740,\\\"status\\\":true,\\\"target\\\":[\\\"6285885529352\\\"]}\",\"error\":null},{\"phone\":\"6281381525044\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[142694369],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":941,\\\"remaining\\\":940,\\\"used\\\":1}},\\\"requestid\\\":374115741,\\\"status\\\":true,\\\"target\\\":[\\\"6281381525044\\\"]}\",\"error\":null}]',2,NULL,'Ruang Seminar',4,NULL,NULL),(18,'wdwd','1231231',NULL,'general','asafadwad','answered',1,'2026-02-11 06:53:10',1,'2026-02-11 06:45:20','2026-02-11 06:53:10',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[143128682],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":934,\\\"remaining\\\":933,\\\"used\\\":1}},\\\"requestid\\\":377935830,\\\"status\\\":true,\\\"target\\\":[\\\"6285885529352\\\"]}\",\"error\":null},{\"phone\":\"6281381525044\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[143128683],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":933,\\\"remaining\\\":932,\\\"used\\\":1}},\\\"requestid\\\":377935834,\\\"status\\\":true,\\\"target\\\":[\\\"6281381525044\\\"]}\",\"error\":null}]',1,NULL,'Ruang Lab Komputer 1',5,NULL,NULL),(19,'sdsd','1231',NULL,'general','esadasd','answered',1,'2026-02-11 06:53:12',1,'2026-02-11 06:51:39','2026-02-11 06:53:12',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[143129740],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":932,\\\"remaining\\\":931,\\\"used\\\":1}},\\\"requestid\\\":377948204,\\\"status\\\":true,\\\"target\\\":[\\\"6285885529352\\\"]}\",\"error\":null}]',1,NULL,'Ruang Lab Komputer 1',6,NULL,NULL),(20,'wd','323123',NULL,'general','sdqwdawd','answered',1,'2026-02-11 06:56:46',1,'2026-02-11 06:56:23','2026-02-11 06:56:46',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[143130420],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":931,\\\"remaining\\\":930,\\\"used\\\":1}},\\\"requestid\\\":377956027,\\\"status\\\":true,\\\"target\\\":[\\\"6285885529352\\\"]}\",\"error\":null}]',1,NULL,'Ruang Lab Komputer 1',8,NULL,NULL),(21,'dwwdw','21321',NULL,'general','123213','answered',1,'2026-02-11 07:00:21',1,'2026-02-11 06:59:55','2026-02-11 07:00:21',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[143130800],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":930,\\\"remaining\\\":929,\\\"used\\\":1}},\\\"requestid\\\":377961868,\\\"status\\\":true,\\\"target\\\":[\\\"6285885529352\\\"]}\",\"error\":null}]',1,NULL,'Ruang Lab Komputer 1',9,NULL,NULL),(22,'wahyu','085885529352',NULL,'general','s','answered',1,'2026-02-18 15:51:52',1,'2026-02-11 15:05:38','2026-02-18 15:51:52',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[143189292],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":929,\\\"remaining\\\":928,\\\"used\\\":1}},\\\"requestid\\\":378542227,\\\"status\\\":true,\\\"target\\\":[\\\"6285885529352\\\"]}\",\"error\":null}]',3,NULL,'Ruang Kelas 301',NULL,NULL,NULL),(23,'wahyu adi saputro','08021312083',NULL,'general','tanya program','answered',1,'2026-02-18 15:51:50',1,'2026-02-18 15:47:51','2026-02-18 15:51:50',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[144225425],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":994,\\\"remaining\\\":993,\\\"used\\\":1}},\\\"requestid\\\":387098831,\\\"status\\\":true,\\\"target\\\":[\\\"6285885529352\\\"]}\",\"error\":null}]',NULL,1,NULL,NULL,NULL,NULL),(24,'wahyu','213891238',NULL,'general','saya ingin tanya program','answered',1,'2026-02-18 15:51:50',1,'2026-02-18 15:51:16','2026-02-18 15:51:50',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[144225569],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":993,\\\"remaining\\\":992,\\\"used\\\":1}},\\\"requestid\\\":387101685,\\\"status\\\":true,\\\"target\\\":[\\\"6285885529352\\\"]}\",\"error\":null}]',NULL,1,NULL,NULL,NULL,NULL),(25,'wahyu adi saputro','085885529352',NULL,'general','help desk','answered',1,'2026-04-07 07:20:24',1,'2026-04-07 05:40:15','2026-04-07 07:20:24',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[150582853],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":977,\\\"remaining\\\":976,\\\"used\\\":1}},\\\"requestid\\\":439487171,\\\"status\\\":true,\\\"target\\\":[\\\"6285885529352\\\"]}\",\"error\":null}]',0,2,'',13,NULL,NULL),(26,'wahyu','087718838762',NULL,'general','kelas','answered',1,'2026-04-07 07:22:16',1,'2026-04-07 07:21:09','2026-04-07 07:22:16',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[150599830],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":976,\\\"remaining\\\":975,\\\"used\\\":1}},\\\"requestid\\\":439599342,\\\"status\\\":true,\\\"target\\\":[\\\"6285885529352\\\"]}\",\"error\":null}]',0,2,'',14,NULL,NULL),(27,'andi','123123131',NULL,'general','mau nanya program','answered',1,'2026-04-07 07:24:25',1,'2026-04-07 07:23:18','2026-04-07 07:24:25',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[150600272],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":975,\\\"remaining\\\":974,\\\"used\\\":1}},\\\"requestid\\\":439601668,\\\"status\\\":true,\\\"target\\\":[\\\"6285885529352\\\"]}\",\"error\":null}]',0,2,'',15,NULL,NULL),(28,'wahyu','8493843948',NULL,'general','nanya','answered',1,'2026-04-07 14:55:28',1,'2026-04-07 14:49:51','2026-04-07 14:55:28',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[150657053],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":974,\\\"remaining\\\":973,\\\"used\\\":1}},\\\"requestid\\\":439934172,\\\"status\\\":true,\\\"target\\\":[\\\"6285885529352\\\"]}\",\"error\":null}]',0,1,'',16,NULL,NULL),(29,'jon','131312',NULL,'general','awdadw','answered',1,'2026-04-07 14:56:15',1,'2026-04-07 14:55:49','2026-04-07 14:56:15',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[150657611],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":973,\\\"remaining\\\":972,\\\"used\\\":1}},\\\"requestid\\\":439937511,\\\"status\\\":true,\\\"target\\\":[\\\"6285885529352\\\"]}\",\"error\":null}]',0,2,'',17,NULL,NULL),(30,'adawdad','123131',NULL,'general','aeadaw','answered',1,'2026-04-07 15:35:09',1,'2026-04-07 15:35:05','2026-04-07 15:35:09',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[150660024],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":972,\\\"remaining\\\":971,\\\"used\\\":1}},\\\"requestid\\\":439955505,\\\"status\\\":true,\\\"target\\\":[\\\"6285885529352\\\"]}\",\"error\":null}]',0,1,'',18,NULL,NULL),(31,'wahyu','085885529352',NULL,'live_chat','dwdawd','answered',1,'2026-04-08 03:00:09',0,'2026-04-08 02:59:57','2026-04-08 03:00:09',NULL,NULL,NULL,1,NULL,19,'012112de-0398-436b-99aa-619eaa4faf08','waiting'),(32,'wahyu','085885529352',NULL,'live_chat','dwdawd','answered',1,'2026-04-08 03:02:08',0,'2026-04-08 03:02:00','2026-04-08 03:02:08',NULL,NULL,NULL,1,NULL,20,'ddbce97d-1bb9-4673-b49e-809794a94442','waiting'),(33,'aead','085885529352',NULL,'live_chat','program','answered',1,'2026-04-08 03:32:25',0,'2026-04-08 03:32:20','2026-04-08 03:32:58',NULL,NULL,NULL,1,NULL,21,'0d138f0f-7b7c-4b2b-932f-06e2ed60f6c5','ended'),(34,'jdjawdja','02193012',NULL,'live_chat','adawd','answered',1,'2026-04-08 03:35:54',0,'2026-04-08 03:35:46','2026-04-08 03:35:54',NULL,NULL,NULL,1,NULL,22,'79b8564d-6f35-4500-b895-ca234b8bf0dc','waiting'),(35,'wahyu','085885529352',NULL,'live_chat','ruang medco batrai mic abis','answered',1,'2026-04-08 03:57:37',0,'2026-04-08 03:57:29','2026-04-08 03:57:37',NULL,NULL,NULL,2,NULL,23,'b46bfb2d-79f9-429a-a184-c64b45e24c83','waiting'),(36,'jon','898989',NULL,'live_chat','program','answered',1,'2026-04-08 04:10:00',0,'2026-04-08 04:09:55','2026-04-08 04:10:00',NULL,NULL,NULL,2,NULL,24,'942d3698-74f3-4d12-a289-295aaa84d47e','active'),(37,'wahyu','21092310',NULL,'live_chat','sdsd','answered',1,'2026-04-08 06:51:20',0,'2026-04-08 06:51:05','2026-04-08 06:52:16',NULL,NULL,NULL,1,NULL,25,'0560d2cd-b5ee-4f9d-94e8-09747d8d6abe','ended'),(38,'andi','2131',NULL,'live_chat','dsd','answered',1,'2026-04-08 06:52:46',0,'2026-04-08 06:52:25','2026-04-08 06:53:03',NULL,NULL,NULL,2,NULL,26,'8b838c3d-df50-4132-ad08-3726e7e18e1f','ended'),(39,'sdsd','1231',NULL,'live_chat','sdada','answered',1,'2026-04-08 06:57:00',0,'2026-04-08 06:56:35','2026-04-08 06:57:00',NULL,NULL,NULL,2,NULL,27,'47d691a6-fd05-48da-92f2-e5f16c8d15a1','active'),(40,'dsdsd','12323',NULL,'live_chat','asdada','answered',1,'2026-04-08 06:59:24',0,'2026-04-08 06:59:17','2026-04-08 06:59:24',NULL,NULL,NULL,2,NULL,28,'9440f101-60e8-40c0-a9a2-581d45d0b3d5','active'),(41,'dsdsd','12323',NULL,'live_chat','asdada','answered',1,'2026-04-08 07:09:15',0,'2026-04-08 07:08:27','2026-04-08 07:09:15',NULL,NULL,NULL,2,NULL,29,'a9a3ad23-3007-420e-a859-a58d6b23c337','active'),(42,'jhon','085885529352',NULL,'live_chat','sdsdsd','answered',1,'2026-04-08 07:10:54',0,'2026-04-08 07:10:49','2026-04-08 10:22:02',NULL,NULL,NULL,2,NULL,30,'868ca0e7-520e-4ae6-879a-21aff6a1920f','ended'),(43,'wadawd','085885529352',NULL,'live_chat','adsada','answered',1,'2026-04-08 10:24:09',0,'2026-04-08 10:24:01','2026-04-08 10:24:23',NULL,NULL,NULL,1,NULL,31,'b8dbc4b0-f721-4132-9711-a133961b0586','ended'),(44,'joesep','085885529352',NULL,'live_chat','saya mau nanya tentang program','answered',1,'2026-04-08 10:53:36',0,'2026-04-08 10:53:31','2026-04-08 10:56:12',NULL,NULL,NULL,1,NULL,32,'6ebbf40e-85b7-4239-847c-a963520c1b44','ended'),(45,'wahyu','085885529352',NULL,'live_chat','saya mau tanya dong','answered',1,'2026-04-08 10:56:47',0,'2026-04-08 10:56:44','2026-04-08 10:56:47',NULL,NULL,NULL,1,NULL,33,'ae30de4c-1b57-45c1-88a6-cfde08576963','active'),(46,'wahyu','085885529352',NULL,'live_chat','dadsdada','answered',1,'2026-04-08 10:58:26',0,'2026-04-08 10:58:23','2026-04-08 10:58:26',NULL,NULL,NULL,1,NULL,34,'d137cb6e-1f9a-4f8d-b786-36b9aad43ba3','active'),(47,'wadwad','081381525044',NULL,'live_chat','HAI manis','answered',1,'2026-04-08 11:00:47',0,'2026-04-08 11:00:44','2026-04-08 11:00:47',NULL,NULL,NULL,1,NULL,35,'2d8d04a3-17c4-416b-9337-be59bef3393a','active'),(48,'Tes WA','08123456789',NULL,'general','uji wa','answered',1,'2026-04-08 11:02:04',1,'2026-04-08 11:01:53','2026-04-08 11:02:04',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"reason\\\":\\\"request invalid on disconnected device\\\",\\\"requestid\\\":440775246,\\\"status\\\":false}\",\"error\":null}]',0,1,'',36,NULL,NULL),(49,'Tes WA2','08123456789',NULL,'general','uji wa2','answered',1,'2026-04-08 11:02:14',0,'2026-04-08 11:02:12','2026-04-08 11:02:14',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"reason\\\":\\\"request invalid on disconnected device\\\",\\\"requestid\\\":440775549,\\\"status\\\":false}\",\"error\":null}]',0,1,'',37,NULL,NULL),(50,'JENNY BLACK PINK','085885529352',NULL,'live_chat','tolongggg','answered',1,'2026-04-08 11:03:15',0,'2026-04-08 11:03:13','2026-04-08 11:03:15',NULL,NULL,NULL,2,NULL,38,'3321674c-3649-4be5-bf45-162ce03c2ec4','active'),(51,'sadad','12313',NULL,'live_chat','wedwdw','answered',1,'2026-04-08 11:06:24',0,'2026-04-08 11:06:21','2026-04-08 11:06:24',NULL,NULL,NULL,2,NULL,39,'f2410d61-4eee-49f0-a03a-6f22e8a61cfb','active'),(52,'jenny','085885529352',NULL,'live_chat','jonnn','answered',1,'2026-04-08 11:08:42',0,'2026-04-08 11:08:39','2026-04-08 11:10:48',NULL,NULL,NULL,2,NULL,40,'0861a0b3-bbc3-40a1-826e-3e3c9df3c167','ended'),(53,'sdsds','1231',NULL,'live_chat','sdasda','answered',1,'2026-04-08 11:11:12',0,'2026-04-08 11:11:09','2026-04-08 11:11:12',NULL,NULL,NULL,1,NULL,41,'75879164-0a7a-4a5a-9fbf-64897726b0cd','active'),(54,'Tes Flow WA','08111111111',NULL,'general','tes notifikasi admin','answered',1,'2026-04-08 11:13:19',1,'2026-04-08 11:13:10','2026-04-08 11:13:19',200,'[{\"phone\":\"6285885529352\",\"http_code\":200,\"response\":\"{\\\"detail\\\":\\\"success! message in queue\\\",\\\"id\\\":[150800107],\\\"process\\\":\\\"pending\\\",\\\"quota\\\":{\\\"085885529352\\\":{\\\"details\\\":\\\"deduced from total quota\\\",\\\"quota\\\":970,\\\"remaining\\\":969,\\\"used\\\":1}},\\\"requestid\\\":440783219,\\\"status\\\":true,\\\"target\\\":[\\\"6285885529352\\\"]}\",\"error\":null}]',0,1,'',42,NULL,NULL),(55,'adawd','12312313',NULL,'live_chat','qdadawd','answered',1,'2026-04-08 15:36:08',0,'2026-04-08 15:36:00','2026-04-08 15:36:34',NULL,NULL,NULL,1,NULL,43,'9c1e0cba-0c6a-45ed-bb12-a1a60be2169b','ended'),(56,'wahyuadi','08080',NULL,'live_chat','wedadawd','answered',1,'2026-04-09 07:32:19',0,'2026-04-09 07:32:16','2026-04-09 07:32:19',NULL,NULL,NULL,2,NULL,44,'a524d13f-e63d-4ce8-b8a9-0f82a5c142b7','active');
/*!40000 ALTER TABLE `staff_calls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('admin','operator') DEFAULT 'operator',
  `status_aktif` tinyint(1) DEFAULT '1',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','$2y$10$DCdA0YXX51M9k7TTvvuKLuFN9PHzfZ6dcVOxdVEbHGjDetzqLTF3.','Administrator','admin@recepsionis.local','admin',1,'2026-04-09 07:21:13','2025-12-12 02:37:58','2026-04-09 07:21:13');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visitors`
--

DROP TABLE IF EXISTS `visitors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visitors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `perusahaan` varchar(200) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `tujuan` text,
  `host_id` int DEFAULT NULL,
  `status` enum('pending','checked-in','checked-out') DEFAULT 'pending',
  `checkin_time` timestamp NULL DEFAULT NULL,
  `checkout_time` timestamp NULL DEFAULT NULL,
  `badge_number` varchar(20) DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `badge_number` (`badge_number`),
  KEY `idx_status` (`status`),
  KEY `idx_badge` (`badge_number`),
  KEY `idx_host` (`host_id`),
  KEY `idx_checkin` (`checkin_time`),
  CONSTRAINT `visitors_ibfk_1` FOREIGN KEY (`host_id`) REFERENCES `hosts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visitors`
--

LOCK TABLES `visitors` WRITE;
/*!40000 ALTER TABLE `visitors` DISABLE KEYS */;
INSERT INTO `visitors` VALUES (1,'wahyu','wahyuadi2189@gmail.com','085885529352','Pertamina','1765508251_693b849be94b3.jpg','Bimbingan dengan pak yos',2,'checked-out','2025-12-12 02:57:31','2025-12-12 12:08:06','202512120001',NULL,'2025-12-12 02:57:31','2025-12-12 12:08:06'),(3,'Wahyu adi saputro',NULL,'080808080','',NULL,'Ingin bertemu dengan Team Operasional',NULL,'checked-out','2026-02-08 15:48:43','2026-02-08 15:55:05',NULL,NULL,'2026-02-08 15:48:43','2026-02-08 15:55:05'),(4,'testing',NULL,'098090809','',NULL,'Admission',NULL,'checked-out','2026-02-08 15:57:36','2026-02-08 16:00:01',NULL,NULL,'2026-02-08 15:57:36','2026-02-08 16:00:01'),(5,'wdwd',NULL,'1231231','',NULL,'asafadwad',NULL,'checked-out','2026-02-11 06:45:20','2026-02-11 07:00:48',NULL,NULL,'2026-02-11 06:45:20','2026-02-11 07:00:48'),(6,'sdsd',NULL,'1231','',NULL,'esadasd',NULL,'checked-out','2026-02-11 06:51:39','2026-02-11 07:00:45',NULL,NULL,'2026-02-11 06:51:39','2026-02-11 07:00:45'),(8,'wd',NULL,'323123','',NULL,'sdqwdawd',NULL,'checked-out','2026-02-11 06:56:23','2026-02-11 07:00:38','TMU202602110003',NULL,'2026-02-11 06:56:23','2026-02-11 07:00:38'),(9,'dwwdw',NULL,'21321','',NULL,'123213',NULL,'checked-out','2026-02-11 06:59:55','2026-02-11 07:00:51','TMU202602110004',NULL,'2026-02-11 06:59:55','2026-02-11 07:00:51'),(13,'wahyu adi saputro',NULL,'085885529352','',NULL,'help desk',NULL,'checked-out','2026-04-07 05:40:15','2026-04-07 14:23:49','TMU202604070001',NULL,'2026-04-07 05:40:15','2026-04-07 14:23:49'),(14,'wahyu',NULL,'087718838762','',NULL,'kelas',NULL,'checked-out','2026-04-07 07:21:09','2026-04-07 14:23:44','TMU202604070002',NULL,'2026-04-07 07:21:09','2026-04-07 14:23:44'),(15,'andi',NULL,'123123131','',NULL,'mau nanya program',NULL,'checked-out','2026-04-07 07:23:18','2026-04-07 14:23:39','TMU202604070003',NULL,'2026-04-07 07:23:18','2026-04-07 14:23:39'),(16,'wahyu',NULL,'8493843948','',NULL,'nanya',NULL,'checked-in','2026-04-07 14:49:51',NULL,'TMU202604070004',NULL,'2026-04-07 14:49:51','2026-04-07 14:49:51'),(17,'jon',NULL,'131312','',NULL,'awdadw',NULL,'checked-in','2026-04-07 14:55:49',NULL,'TMU202604070005',NULL,'2026-04-07 14:55:49','2026-04-07 14:55:49'),(18,'adawdad',NULL,'123131','',NULL,'aeadaw',NULL,'checked-in','2026-04-07 15:35:05',NULL,'TMU202604070006',NULL,'2026-04-07 15:35:05','2026-04-07 15:35:05'),(19,'wahyu',NULL,'085885529352','',NULL,'dwdawd',NULL,'checked-in','2026-04-08 02:59:57',NULL,'TMU202604080001',NULL,'2026-04-08 02:59:57','2026-04-08 02:59:57'),(20,'wahyu',NULL,'085885529352','',NULL,'dwdawd',NULL,'checked-in','2026-04-08 03:02:00',NULL,'TMU202604080002',NULL,'2026-04-08 03:02:00','2026-04-08 03:02:00'),(21,'aead',NULL,'085885529352','',NULL,'program',NULL,'checked-in','2026-04-08 03:32:20',NULL,'TMU202604080003',NULL,'2026-04-08 03:32:20','2026-04-08 03:32:20'),(22,'jdjawdja',NULL,'02193012','',NULL,'adawd',NULL,'checked-in','2026-04-08 03:35:46',NULL,'TMU202604080004',NULL,'2026-04-08 03:35:46','2026-04-08 03:35:46'),(23,'wahyu',NULL,'085885529352','',NULL,'ruang medco batrai mic abis',NULL,'checked-in','2026-04-08 03:57:29',NULL,'TMU202604080005',NULL,'2026-04-08 03:57:29','2026-04-08 03:57:29'),(24,'jon',NULL,'898989','',NULL,'program',NULL,'checked-in','2026-04-08 04:09:55',NULL,'TMU202604080006',NULL,'2026-04-08 04:09:55','2026-04-08 04:09:55'),(25,'wahyu',NULL,'21092310','',NULL,'sdsd',NULL,'checked-in','2026-04-08 06:51:05',NULL,'TMU202604080007',NULL,'2026-04-08 06:51:05','2026-04-08 06:51:05'),(26,'andi',NULL,'2131','',NULL,'dsd',NULL,'checked-in','2026-04-08 06:52:25',NULL,'TMU202604080008',NULL,'2026-04-08 06:52:25','2026-04-08 06:52:25'),(27,'sdsd',NULL,'1231','',NULL,'sdada',NULL,'checked-in','2026-04-08 06:56:35',NULL,'TMU202604080009',NULL,'2026-04-08 06:56:35','2026-04-08 06:56:35'),(28,'dsdsd',NULL,'12323','',NULL,'asdada',NULL,'checked-in','2026-04-08 06:59:17',NULL,'TMU202604080010',NULL,'2026-04-08 06:59:17','2026-04-08 06:59:17'),(29,'dsdsd',NULL,'12323','',NULL,'asdada',NULL,'checked-in','2026-04-08 07:08:27',NULL,'TMU202604080011',NULL,'2026-04-08 07:08:27','2026-04-08 07:08:27'),(30,'jhon',NULL,'085885529352','',NULL,'sdsdsd',NULL,'checked-in','2026-04-08 07:10:49',NULL,'TMU202604080012',NULL,'2026-04-08 07:10:49','2026-04-08 07:10:49'),(31,'wadawd',NULL,'085885529352','',NULL,'adsada',NULL,'checked-in','2026-04-08 10:24:01',NULL,'TMU202604080013',NULL,'2026-04-08 10:24:01','2026-04-08 10:24:01'),(32,'joesep',NULL,'085885529352','',NULL,'saya mau nanya tentang program',NULL,'checked-in','2026-04-08 10:53:31',NULL,'TMU202604080014',NULL,'2026-04-08 10:53:31','2026-04-08 10:53:31'),(33,'wahyu',NULL,'085885529352','',NULL,'saya mau tanya dong',NULL,'checked-in','2026-04-08 10:56:44',NULL,'TMU202604080015',NULL,'2026-04-08 10:56:44','2026-04-08 10:56:44'),(34,'wahyu',NULL,'085885529352','',NULL,'dadsdada',NULL,'checked-in','2026-04-08 10:58:23',NULL,'TMU202604080016',NULL,'2026-04-08 10:58:23','2026-04-08 10:58:23'),(35,'wadwad',NULL,'081381525044','',NULL,'HAI manis',NULL,'checked-in','2026-04-08 11:00:44',NULL,'TMU202604080017',NULL,'2026-04-08 11:00:44','2026-04-08 11:00:44'),(36,'Tes WA',NULL,'08123456789','',NULL,'uji wa',NULL,'checked-in','2026-04-08 11:01:53',NULL,'TMU202604080018',NULL,'2026-04-08 11:01:53','2026-04-08 11:01:53'),(37,'Tes WA2',NULL,'08123456789','',NULL,'uji wa2',NULL,'checked-in','2026-04-08 11:02:12',NULL,'TMU202604080019',NULL,'2026-04-08 11:02:12','2026-04-08 11:02:12'),(38,'JENNY BLACK PINK',NULL,'085885529352','',NULL,'tolongggg',NULL,'checked-in','2026-04-08 11:03:13',NULL,'TMU202604080020',NULL,'2026-04-08 11:03:13','2026-04-08 11:03:13'),(39,'sadad',NULL,'12313','',NULL,'wedwdw',NULL,'checked-in','2026-04-08 11:06:21',NULL,'TMU202604080021',NULL,'2026-04-08 11:06:21','2026-04-08 11:06:21'),(40,'jenny',NULL,'085885529352','',NULL,'jonnn',NULL,'checked-in','2026-04-08 11:08:39',NULL,'TMU202604080022',NULL,'2026-04-08 11:08:39','2026-04-08 11:08:39'),(41,'sdsds',NULL,'1231','',NULL,'sdasda',NULL,'checked-in','2026-04-08 11:11:10',NULL,'TMU202604080023',NULL,'2026-04-08 11:11:10','2026-04-08 11:11:10'),(42,'Tes Flow WA',NULL,'08111111111','',NULL,'tes notifikasi admin',NULL,'checked-in','2026-04-08 11:13:10',NULL,'TMU202604080024',NULL,'2026-04-08 11:13:10','2026-04-08 11:13:10'),(43,'adawd',NULL,'12312313','',NULL,'qdadawd',NULL,'checked-in','2026-04-08 15:36:00',NULL,'TMU202604080025',NULL,'2026-04-08 15:36:00','2026-04-08 15:36:00'),(44,'wahyuadi',NULL,'08080','',NULL,'wedadawd',NULL,'checked-in','2026-04-09 07:32:16',NULL,'TMU202604090001',NULL,'2026-04-09 07:32:16','2026-04-09 07:32:16');
/*!40000 ALTER TABLE `visitors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'recepsionis_db'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-14 23:56:59
