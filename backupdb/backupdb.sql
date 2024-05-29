-- MySQL dump 10.13  Distrib 8.0.33, for Win64 (x86_64)
--
-- Host: localhost    Database: universedb
-- ------------------------------------------------------
-- Server version	8.0.36-0ubuntu0.22.04.1

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
-- Table structure for table `chat_user`
--

DROP TABLE IF EXISTS `chat_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_user` (
  `universe_chat_entity_id` int NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`universe_chat_entity_id`,`user_id`),
  KEY `IDX_2B0F4B08B5BF092A` (`universe_chat_entity_id`),
  KEY `IDX_2B0F4B08A76ED395` (`user_id`),
  CONSTRAINT `FK_2B0F4B08A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_2B0F4B08B5BF092A` FOREIGN KEY (`universe_chat_entity_id`) REFERENCES `universe_chat` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_user`
--

LOCK TABLES `chat_user` WRITE;
/*!40000 ALTER TABLE `chat_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `chat_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_user`
--

DROP TABLE IF EXISTS `event_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_user` (
  `event_id` int NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`event_id`,`user_id`),
  KEY `IDX_92589AE271F7E88B` (`event_id`),
  KEY `IDX_92589AE2A76ED395` (`user_id`),
  CONSTRAINT `FK_92589AE271F7E88B` FOREIGN KEY (`event_id`) REFERENCES `universe_event` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_92589AE2A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_user`
--

LOCK TABLES `event_user` WRITE;
/*!40000 ALTER TABLE `event_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `monthly_schedule`
--

DROP TABLE IF EXISTS `monthly_schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `monthly_schedule` (
  `id` int NOT NULL AUTO_INCREMENT,
  `facility_id` int DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1B303E20A7014910` (`facility_id`),
  CONSTRAINT `FK_1B303E20A7014910` FOREIGN KEY (`facility_id`) REFERENCES `universe_facility` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `monthly_schedule`
--

LOCK TABLES `monthly_schedule` WRITE;
/*!40000 ALTER TABLE `monthly_schedule` DISABLE KEYS */;
INSERT INTO `monthly_schedule` VALUES (1,1,'2019-01-01','2019-01-01'),(2,2,'2019-01-01','2019-01-01'),(3,3,'2019-01-01','2019-01-01'),(4,4,'2019-01-01','2019-01-01');
/*!40000 ALTER TABLE `monthly_schedule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `operating_hours`
--

DROP TABLE IF EXISTS `operating_hours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `operating_hours` (
  `id` int NOT NULL AUTO_INCREMENT,
  `monthly_schedule_id` int DEFAULT NULL,
  `day_of_week` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_540F2B4F3910A81A` (`monthly_schedule_id`),
  CONSTRAINT `FK_540F2B4F3910A81A` FOREIGN KEY (`monthly_schedule_id`) REFERENCES `monthly_schedule` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operating_hours`
--

LOCK TABLES `operating_hours` WRITE;
/*!40000 ALTER TABLE `operating_hours` DISABLE KEYS */;
INSERT INTO `operating_hours` VALUES (1,NULL,'Monday','00:00:00','00:00:00'),(2,NULL,'Monday','00:00:00','00:00:00'),(3,NULL,'Monday','00:00:00','00:00:00'),(4,NULL,'Monday','00:00:00','00:00:00'),(5,NULL,'Monday','00:00:00','00:00:00');
/*!40000 ALTER TABLE `operating_hours` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organizer_user`
--

DROP TABLE IF EXISTS `organizer_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `organizer_user` (
  `organizer_id` int NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`organizer_id`,`user_id`),
  KEY `IDX_9744570B876C4DDA` (`organizer_id`),
  KEY `IDX_9744570BA76ED395` (`user_id`),
  CONSTRAINT `FK_9744570B876C4DDA` FOREIGN KEY (`organizer_id`) REFERENCES `universe_organizer` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_9744570BA76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organizer_user`
--

LOCK TABLES `organizer_user` WRITE;
/*!40000 ALTER TABLE `organizer_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `organizer_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_amenity`
--

DROP TABLE IF EXISTS `universe_amenity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_amenity` (
  `id` int NOT NULL AUTO_INCREMENT,
  `icon` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `slug` varchar(80) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `hidden` tinyint(1) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_amenity`
--

LOCK TABLES `universe_amenity` WRITE;
/*!40000 ALTER TABLE `universe_amenity` DISABLE KEYS */;
INSERT INTO `universe_amenity` VALUES (1,'fas fa-desktop','Computer','computer',NULL,'2024-02-01 21:14:45','2024-02-01 21:14:45'),(2,'fas fa-video','Projector','projector',NULL,'2024-02-01 21:15:10','2024-02-01 21:15:10');
/*!40000 ALTER TABLE `universe_amenity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_app_layout_setting`
--

DROP TABLE IF EXISTS `universe_app_layout_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_app_layout_setting` (
  `id` int NOT NULL AUTO_INCREMENT,
  `logo_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `logo_size` int DEFAULT NULL,
  `logo_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `logo_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `logo_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `favicon_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `favicon_size` int DEFAULT NULL,
  `favicon_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `favicon_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `favicon_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `og_image_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `og_image_size` int DEFAULT NULL,
  `og_image_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `og_image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `og_image_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_app_layout_setting`
--

LOCK TABLES `universe_app_layout_setting` WRITE;
/*!40000 ALTER TABLE `universe_app_layout_setting` DISABLE KEYS */;
INSERT INTO `universe_app_layout_setting` VALUES (1,'65c3ddb357d1b808824496.png',41733,'image/jpeg','MicrosoftTeams-image (11).png','537,90','65ba92d0c6c4a434254519.jpg',98994,'image/jpeg','1T-03.jpg','1080,1080','65ba92d0c6f9e728933195.jpg',98994,'image/jpeg','1T-03.jpg','1080,1080','2024-02-07 21:44:51');
/*!40000 ALTER TABLE `universe_app_layout_setting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_athletic_booking`
--

DROP TABLE IF EXISTS `universe_athletic_booking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_athletic_booking` (
  `id` int NOT NULL AUTO_INCREMENT,
  `facility_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `amount` int NOT NULL,
  `duration` int NOT NULL,
  `booking_date` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_936BF858A7014910` (`facility_id`),
  KEY `IDX_936BF858A76ED395` (`user_id`),
  CONSTRAINT `FK_936BF858A7014910` FOREIGN KEY (`facility_id`) REFERENCES `universe_facility` (`id`),
  CONSTRAINT `FK_936BF858A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_athletic_booking`
--

LOCK TABLES `universe_athletic_booking` WRITE;
/*!40000 ALTER TABLE `universe_athletic_booking` DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_athletic_booking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_audience`
--

DROP TABLE IF EXISTS `universe_audience`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_audience` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `slug` varchar(80) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `image_name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_size` int DEFAULT NULL,
  `image_mime_type` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_original_name` varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `hidden` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_audience`
--

LOCK TABLES `universe_audience` WRITE;
/*!40000 ALTER TABLE `universe_audience` DISABLE KEYS */;
INSERT INTO `universe_audience` VALUES (1,'test','testslug','5d641402c4cf9162849552.png',15678,'image/png','adults.png','512,512',0,'2019-08-26 17:16:50','2019-08-26 17:16:50',NULL),(2,'test1','testslug1','5d641451ca81e833369961.png',15496,'image/png','children.png','512,512',0,'2019-08-26 17:18:09','2020-04-21 13:23:14',NULL);
/*!40000 ALTER TABLE `universe_audience` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_blog_post`
--

DROP TABLE IF EXISTS `universe_blog_post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_blog_post` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tags` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `content` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `readtime` int DEFAULT NULL,
  `image_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_size` int DEFAULT NULL,
  `image_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `views` int DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_C1970FE6FBC9426` (`tags`),
  UNIQUE KEY `UNIQ_C1970FEA76ED395` (`user_id`),
  KEY `IDX_C1970FE12469DE2` (`category_id`),
  CONSTRAINT `FK_C1970FE12469DE2` FOREIGN KEY (`category_id`) REFERENCES `universe_blog_post_category` (`id`),
  CONSTRAINT `FK_C1970FEA76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_blog_post`
--

LOCK TABLES `universe_blog_post` WRITE;
/*!40000 ALTER TABLE `universe_blog_post` DISABLE KEYS */;
INSERT INTO `universe_blog_post` VALUES (1,9,5,'new-year-new-resolutions','acg,newyear','New year, new resolutions','<p><br></p>',NULL,'65c7c1325837f266531484.jpg',166325,'image/jpeg','419722686_18405338965061930_439851310006493808_n.jpg','1080,1080',7,0,'2024-02-10 20:32:18','2024-02-12 03:04:13',NULL);
/*!40000 ALTER TABLE `universe_blog_post` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_blog_post_category`
--

DROP TABLE IF EXISTS `universe_blog_post_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_blog_post_category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tags` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `content` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `hidden` tinyint(1) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_5F6CCC5F6FBC9426` (`tags`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_blog_post_category`
--

LOCK TABLES `universe_blog_post_category` WRITE;
/*!40000 ALTER TABLE `universe_blog_post_category` DISABLE KEYS */;
INSERT INTO `universe_blog_post_category` VALUES (1,'dsa-news',NULL,'DSA News',NULL,0,'2024-01-31 19:53:08','2024-01-31 19:53:08',NULL),(2,'club-news',NULL,'Club news',NULL,0,'2024-01-31 19:53:12','2024-01-31 19:53:12',NULL),(3,'organizations-news',NULL,'Organizations News',NULL,0,'2024-01-31 19:53:18','2024-01-31 19:53:18',NULL),(4,'general',NULL,'General',NULL,0,'2024-01-31 19:53:25','2024-01-31 19:53:25',NULL),(5,'offices-news',NULL,'Offices News',NULL,0,'2024-01-31 19:53:25','2024-01-31 19:53:25',NULL);
/*!40000 ALTER TABLE `universe_blog_post_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_category`
--

DROP TABLE IF EXISTS `universe_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `image_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_size` int DEFAULT NULL,
  `image_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `hidden` tinyint(1) NOT NULL,
  `featured` tinyint(1) NOT NULL,
  `featuredorder` int DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_23F1C7FE989D9B62` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_category`
--

LOCK TABLES `universe_category` WRITE;
/*!40000 ALTER TABLE `universe_category` DISABLE KEYS */;
INSERT INTO `universe_category` VALUES (1,'organizations','Organizations','fas fa-align-justify','65ba93153cb1c986997099.png',370493,'image/png','8.png','750,750',0,1,1,'2024-01-31 20:36:05','2024-01-31 20:38:02',NULL),(2,'societies','Societies','fas fa-align-justify','65ba932a0ab8a050992884.png',364096,'image/png','3.png','750,750',0,1,2,'2024-01-31 20:36:26','2024-01-31 20:38:00',NULL),(3,'clubs','Clubs','fas fa-align-justify','65ba9333d5f61073450463.png',359305,'image/png','6.png','750,750',0,1,3,'2024-01-31 20:36:35','2024-01-31 20:37:49',NULL),(4,'offices','Offices','fas fa-align-justify','65ba934b17378657656289.png',373351,'image/png','7.png','750,750',0,1,4,'2024-01-31 20:36:59','2024-01-31 20:37:57',NULL),(5,'out-of-campus','Out of Campus','fas fa-align-justify','65ba936835330764467995.png',391068,'image/png','9.png','750,750',0,1,5,'2024-01-31 20:37:28','2024-01-31 20:37:53',NULL),(6,'open-events','Open events','fas fa-align-justify','65c3d4346a69a534224052.png',1020802,'image/png','UniVerse (14).png','1000,1000',0,1,6,'2024-02-07 21:04:20','2024-02-07 21:04:28',NULL);
/*!40000 ALTER TABLE `universe_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_chat`
--

DROP TABLE IF EXISTS `universe_chat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_chat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_chat`
--

LOCK TABLES `universe_chat` WRITE;
/*!40000 ALTER TABLE `universe_chat` DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_chat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_chat_user`
--

DROP TABLE IF EXISTS `universe_chat_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_chat_user` (
  `chat_id` int NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`chat_id`,`user_id`),
  KEY `IDX_9D4CDBEB1A9A7125` (`chat_id`),
  KEY `IDX_9D4CDBEBA76ED395` (`user_id`),
  CONSTRAINT `FK_9D4CDBEB1A9A7125` FOREIGN KEY (`chat_id`) REFERENCES `universe_chat` (`id`),
  CONSTRAINT `FK_9D4CDBEBA76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_chat_user`
--

LOCK TABLES `universe_chat_user` WRITE;
/*!40000 ALTER TABLE `universe_chat_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_chat_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_comment`
--

DROP TABLE IF EXISTS `universe_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_comment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `thread_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `author_id` int DEFAULT NULL,
  `body` longtext NOT NULL,
  `ancestors` varchar(1024) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `depth` int NOT NULL,
  `created_at` datetime NOT NULL,
  `state` int NOT NULL,
  `score` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4678CDDEE2904019` (`thread_id`),
  KEY `IDX_4678CDDEF675F31B` (`author_id`),
  CONSTRAINT `FK_4678CDDEE2904019` FOREIGN KEY (`thread_id`) REFERENCES `universe_thread` (`id`),
  CONSTRAINT `FK_4678CDDEF675F31B` FOREIGN KEY (`author_id`) REFERENCES `universe_user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_comment`
--

LOCK TABLES `universe_comment` WRITE;
/*!40000 ALTER TABLE `universe_comment` DISABLE KEYS */;
INSERT INTO `universe_comment` VALUES (1,'tuesday-march-15-day-1-business-week-9b7ead3255a419f416ff',7,'Can\'t wait : )','',0,'2024-02-08 06:44:30',0,0),(3,'capture-the-flag-ctf-competition-i-bbe2cd191427f02226d3',7,'hvh v','',0,'2024-03-28 19:39:30',0,0);
/*!40000 ALTER TABLE `universe_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_event`
--

DROP TABLE IF EXISTS `universe_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_event` (
  `id` int NOT NULL AUTO_INCREMENT,
  `venue_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `organizer_id` int DEFAULT NULL,
  `isonhomepageslider_id` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `slug` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `redirectable` tinyint(1) DEFAULT NULL,
  `allowoverbook` tinyint(1) DEFAULT NULL,
  `viewablebyselect` tinyint(1) DEFAULT NULL,
  `outofcampus` tinyint(1) DEFAULT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `views` int DEFAULT NULL,
  `externallink` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `phonenumber` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tags` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_size` int DEFAULT NULL,
  `image_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `commonvalue` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `published` tinyint(1) NOT NULL,
  `open` tinyint(1) NOT NULL,
  `isrepeat` tinyint(1) NOT NULL,
  `showattendees` tinyint(1) NOT NULL,
  `volsneeded` tinyint(1) NOT NULL,
  `publish_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_FA902AE6989D9B62` (`slug`),
  KEY `IDX_FA902AE640A73EBA` (`venue_id`),
  KEY `IDX_FA902AE612469DE2` (`category_id`),
  KEY `IDX_FA902AE6876C4DDA` (`organizer_id`),
  KEY `IDX_FA902AE6376C51EF` (`isonhomepageslider_id`),
  CONSTRAINT `FK_FA902AE612469DE2` FOREIGN KEY (`category_id`) REFERENCES `universe_category` (`id`),
  CONSTRAINT `FK_FA902AE6376C51EF` FOREIGN KEY (`isonhomepageslider_id`) REFERENCES `universe_homepage_hero_setting` (`id`),
  CONSTRAINT `FK_FA902AE640A73EBA` FOREIGN KEY (`venue_id`) REFERENCES `universe_venue` (`id`),
  CONSTRAINT `FK_FA902AE6876C4DDA` FOREIGN KEY (`organizer_id`) REFERENCES `universe_organizer` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_event`
--

LOCK TABLES `universe_event` WRITE;
/*!40000 ALTER TABLE `universe_event` DISABLE KEYS */;
INSERT INTO `universe_event` VALUES (2,25,3,3,1,'Capture The Flag (CTF) Competition I','<p>Welcome to the ultimate cyber battleground! Our Capture The Flag (CTF) Competition I is a thrilling cybersecurity event where participants showcase their hacking and defensive skills in a virtual arena. Engage in intense challenges designed to test your problem-solving abilities, exploit vulnerabilities, and secure coveted flags hidden within complex systems. With a mix of offensive and defensive tactics, participants will navigate through a maze of cybersecurity puzzles, racing against the clock to emerge victorious. Are you ready to prove your prowess and claim the title of CTF champion?<br></p>','capture-the-flag-ctf-competition-i-dac7e7808777c626ea10',NULL,NULL,NULL,0,'486d0b1346',23,NULL,NULL,NULL,'cyberclub','65ba9500e44ca831527397.jpg',683144,'image/jpeg',NULL,'0_7U4Qj-gSIV_gckS6.jpg','5456,3064','2024-01-31 20:44:16','2024-05-28 21:17:37',NULL,1,1,0,1,0,NULL),(3,20,3,3,1,'Code &amp; Coffee Meetup','<p>Join us for a relaxed coding session fueled by coffee and camaraderie! Whether you\'re a seasoned coder or just starting out, bring your laptop and work on personal projects, assignments, or practice coding challenges. It\'s a great opportunity to network, exchange ideas, and enjoy some caffeine-fueled coding fun.<br></p>','code-amp-coffee-meetup-27cd91fecd0e49090ab4',NULL,NULL,NULL,0,'29aabd9f4d',10,NULL,NULL,NULL,'code','65c3941f424d6919525334.jpg',93191,'image/jpeg',NULL,'653e84ae272d2459979789.jpg','848,565','2024-02-07 16:30:55','2024-05-28 03:12:04',NULL,1,1,0,1,0,NULL),(4,15,3,3,1,'Tech Talk Series: Exploring Blockchain','<p>Curious about blockchain technology and its applications beyond cryptocurrency? Join us for an insightful tech talk where industry experts will discuss the basics of blockchain, its real-world use cases, and the future of decentralized technologies. Whether you\'re a blockchain enthusiast or new to the concept, this event is sure to broaden your horizons!<br></p>','tech-talk-series-exploring-blockchain-357b28cb02b7dbc83e77',NULL,NULL,NULL,0,'1dfdf14443',13,NULL,NULL,NULL,'tech-talk','65c394882c646309516960.png',1156073,'image/png',NULL,'workshop.png','1920,1080','2024-02-07 16:32:40','2024-05-28 21:17:20',NULL,1,1,0,1,0,NULL),(5,NULL,4,4,1,'Tuesday March 15 - Day 1 - Business Week','<p>Deree Business Week is an annual forum organized by the Student Academic Societies of the School of \r\nBusiness &amp; Economics of Deree – The American College of Greece. It aims at bringing together ACG \r\nbusiness students, faculty and alumni with distinguished industry professionals while fostering the \r\nsharing of ideas and best practices between them.\r\nIn its 15th edition, Business Week will focus on Accelerating the Business Cycle to achieve prosperity.\r\nThe approach to the theme involves both individual and corporate aspects of resilience and tools to \r\nenhance recovery, notably venture capital. \r\nThe speakers are introduced by representatives of the Student Academic Societies.<br></p>','tuesday-march-15-day-1-business-week-9b7ead3255a419f416ff',0,NULL,0,0,'9673901144',22,NULL,'+306989442438','alexandros.kourtis@acg.edu','deree','65c3fab6ea208020588929.jpg',166647,'image/jpeg',NULL,'275373642_2116085051874190_2724616131743363188_n.jpg','1920,1005','2024-02-07 23:48:38','2024-03-20 11:58:20',NULL,1,0,0,1,0,NULL),(6,10,3,3,1,'dsfadsfsfasfsfasfs','<p>gdfgsgfdsgfgsdfgddf</p>','dsfadsfsfasfsfasfs-bd84d1dfc7334b89300a',0,NULL,0,0,'1e5236b247',103,NULL,'+306989442438','alexandros.kourtis@acg.edu','dfdsfs','65c984537a2f2004321154.jpg',78980,'image/jpeg',NULL,'laptop-management-server-administration-wallpaper-preview.jpg','728,546','2024-02-12 04:37:07','2024-05-28 03:49:21',NULL,1,1,0,1,0,NULL);
/*!40000 ALTER TABLE `universe_event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_event_date`
--

DROP TABLE IF EXISTS `universe_event_date`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_event_date` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int DEFAULT NULL,
  `online` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `startdate` datetime DEFAULT NULL,
  `enddate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8CE08B8371F7E88B` (`event_id`),
  CONSTRAINT `FK_8CE08B8371F7E88B` FOREIGN KEY (`event_id`) REFERENCES `universe_event` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_event_date`
--

LOCK TABLES `universe_event_date` WRITE;
/*!40000 ALTER TABLE `universe_event_date` DISABLE KEYS */;
INSERT INTO `universe_event_date` VALUES (1,2,0,1,'9a88c9834a','<p>Welcome to the ultimate cyber battleground! Our Capture The Flag (CTF) Competition I is a thrilling cybersecurity event where participants showcase their hacking and defensive skills in a virtual arena. Engage in intense challenges designed to test your problem-solving abilities, exploit vulnerabilities, and secure coveted flags hidden within complex systems. With a mix of offensive and defensive tactics, participants will navigate through a maze of cybersecurity puzzles, racing against the clock to emerge victorious. Are you ready to prove your prowess and claim the title of CTF champion?<br></p>','Capture The Flag (CTF) Competition I','2024-08-24 10:00:00','2024-08-24 12:00:00'),(2,3,0,1,'cc0471f249','<p>Join us for a relaxed coding session fueled by coffee and camaraderie! Whether you\'re a seasoned coder or just starting out, bring your laptop and work on personal projects, assignments, or practice coding challenges. It\'s a great opportunity to network, exchange ideas, and enjoy some caffeine-fueled coding fun.<br></p>','Code &amp; Coffee Meetup','2024-04-11 10:00:00','2024-04-11 12:00:00'),(3,4,0,1,'1331e2c546','<p>Curious about blockchain technology and its applications beyond cryptocurrency? Join us for an insightful tech talk where industry experts will discuss the basics of blockchain, its real-world use cases, and the future of decentralized technologies. Whether you\'re a blockchain enthusiast or new to the concept, this event is sure to broaden your horizons!<br></p>','Tech Talk Series: Exploring Blockchain','2024-06-14 14:00:00','2024-06-15 17:00:00'),(4,5,0,1,'7680303f4e','<p>Deree Business Week is an annual forum organized by the Student Academic Societies of the School of \r\nBusiness &amp; Economics of Deree – The American College of Greece. It aims at bringing together ACG \r\nbusiness students, faculty and alumni with distinguished industry professionals while fostering the \r\nsharing of ideas and best practices between them.\r\nIn its 15th edition, Business Week will focus on Accelerating the Business Cycle to achieve prosperity.\r\nThe approach to the theme involves both individual and corporate aspects of resilience and tools to \r\nenhance recovery, notably venture capital. \r\nThe speakers are introduced by representatives of the Student Academic Societies.<br></p>','Tuesday March 15 - Day 1 - Business Week','2024-02-15 12:30:00','2024-02-15 14:00:00'),(5,5,0,1,'1fa9870a45','<p>Deree Business Week is an annual forum organized by the Student Academic Societies of the School of \r\nBusiness &amp; Economics of Deree – The American College of Greece. It aims at bringing together ACG \r\nbusiness students, faculty and alumni with distinguished industry professionals while fostering the \r\nsharing of ideas and best practices between them.\r\nIn its 15th edition, Business Week will focus on Accelerating the Business Cycle to achieve prosperity.\r\nThe approach to the theme involves both individual and corporate aspects of resilience and tools to \r\nenhance recovery, notably venture capital. \r\nThe speakers are introduced by representatives of the Student Academic Societies.<br></p>','Tuesday March 15 - Day 1 - Business Week','2024-02-15 14:10:00','2024-02-15 15:10:00'),(6,5,0,1,'9dc4450f42','<p>Deree Business Week is an annual forum organized by the Student Academic Societies of the School of \r\nBusiness &amp; Economics of Deree – The American College of Greece. It aims at bringing together ACG \r\nbusiness students, faculty and alumni with distinguished industry professionals while fostering the \r\nsharing of ideas and best practices between them.\r\nIn its 15th edition, Business Week will focus on Accelerating the Business Cycle to achieve prosperity.\r\nThe approach to the theme involves both individual and corporate aspects of resilience and tools to \r\nenhance recovery, notably venture capital. \r\nThe speakers are introduced by representatives of the Student Academic Societies.<br></p>','Tuesday March 15 - Day 1 - Business Week','2024-02-15 15:20:00','2024-02-15 16:35:00'),(7,5,0,1,'5f7b2b1645','<p>Deree Business Week is an annual forum organized by the Student Academic Societies of the School of \r\nBusiness &amp; Economics of Deree – The American College of Greece. It aims at bringing together ACG \r\nbusiness students, faculty and alumni with distinguished industry professionals while fostering the \r\nsharing of ideas and best practices between them.\r\nIn its 15th edition, Business Week will focus on Accelerating the Business Cycle to achieve prosperity.\r\nThe approach to the theme involves both individual and corporate aspects of resilience and tools to \r\nenhance recovery, notably venture capital. \r\nThe speakers are introduced by representatives of the Student Academic Societies.<br></p>','Tuesday March 15 - Day 1 - Business Week','2024-02-15 16:45:00','2024-02-15 18:00:00'),(8,6,0,1,'a5c1b3504a','<p>gdfgsgfdsgfgsdfgddf</p>','dsfadsfsfasfsfasfs','2024-08-10 10:00:00','2024-08-31 13:00:00');
/*!40000 ALTER TABLE `universe_event_date` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_event_date_ticket`
--

DROP TABLE IF EXISTS `universe_event_date_ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_event_date_ticket` (
  `id` int NOT NULL AUTO_INCREMENT,
  `eventdate_id` int DEFAULT NULL,
  `alreadybooked` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `ticketsperattendee` int DEFAULT NULL,
  `salesstartdate` datetime NOT NULL,
  `salesenddate` datetime NOT NULL,
  `position` int NOT NULL,
  `informedcreator` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2C419008733DA6BA` (`eventdate_id`),
  CONSTRAINT `FK_2C419008733DA6BA` FOREIGN KEY (`eventdate_id`) REFERENCES `universe_event_date` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_event_date_ticket`
--

LOCK TABLES `universe_event_date_ticket` WRITE;
/*!40000 ALTER TABLE `universe_event_date_ticket` DISABLE KEYS */;
INSERT INTO `universe_event_date_ticket` VALUES (1,1,0,1,'a2b396a14b','Ticket','',100,1,'2024-01-31 20:44:16','2024-01-31 20:44:16',0,0),(2,2,0,1,'8734908543','Ticket','',100,1,'2024-02-07 16:30:54','2024-02-07 16:30:54',0,0),(3,3,0,1,'220d65ab49','Ticket','',100,1,'2024-02-07 16:32:40','2024-02-07 16:32:40',0,0),(4,4,0,1,'8b54064d4b','Opening','',100,1,'2024-02-07 23:48:38','2024-02-07 23:48:38',0,0),(5,5,0,1,'5fe6a2f144','Opening Reception','',100,1,'2024-02-07 23:48:38','2024-02-07 23:48:38',0,0),(6,6,0,1,'842e05144c','Mihalis Boutaris','',100,1,'2024-02-07 23:48:38','2024-02-07 23:48:38',0,0),(7,7,0,1,'ffedf19641','Aris Ikkos, Ph.D','',100,1,'2024-02-07 23:48:38','2024-02-07 23:48:38',0,0),(8,8,0,1,'cb47870f40','Speaker 1','',100,1,'2024-02-12 04:37:07','2024-02-12 04:37:07',0,0);
/*!40000 ALTER TABLE `universe_event_date_ticket` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_event_file`
--

DROP TABLE IF EXISTS `universe_event_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_event_file` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `file_size` int DEFAULT NULL,
  `file_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `file_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `file_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `position` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_AAE18AE971F7E88B` (`event_id`),
  CONSTRAINT `FK_AAE18AE971F7E88B` FOREIGN KEY (`event_id`) REFERENCES `universe_event` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_event_file`
--

LOCK TABLES `universe_event_file` WRITE;
/*!40000 ALTER TABLE `universe_event_file` DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_event_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_event_image`
--

DROP TABLE IF EXISTS `universe_event_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_event_image` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int DEFAULT NULL,
  `image_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_size` int DEFAULT NULL,
  `image_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `position` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_17A305B71F7E88B` (`event_id`),
  CONSTRAINT `FK_17A305B71F7E88B` FOREIGN KEY (`event_id`) REFERENCES `universe_event` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_event_image`
--

LOCK TABLES `universe_event_image` WRITE;
/*!40000 ALTER TABLE `universe_event_image` DISABLE KEYS */;
INSERT INTO `universe_event_image` VALUES (1,5,'65c3fb28a234e852173050.jpg',428792,'image/jpeg','337300223_229471609558946_4127505755543809913_n.jpg','2048,1453',0,'2024-02-07 23:50:32'),(2,5,'65c3fb28a29c4221505981.jpg',482426,'image/jpeg','336772297_732965718495389_6809338014361496078_n.jpg','2048,1365',1,'2024-02-07 23:50:32'),(3,5,'65c3fb28a2cc7972830264.jpg',509014,'image/jpeg','337234230_195337783206430_559224700292273766_n.jpg','2048,1365',2,'2024-02-07 23:50:32'),(4,5,'65c3fb28a2f63087662151.jpg',394879,'image/jpeg','336768789_3129816763978231_2951870668924980036_n.jpg','2048,1366',3,'2024-02-07 23:50:32'),(5,5,'65c3fb28a31b7796925111.jpg',349946,'image/jpeg','336770510_673649141229404_8184937087311801762_n.jpg','2048,1366',4,'2024-02-07 23:50:32');
/*!40000 ALTER TABLE `universe_event_image` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_facility`
--

DROP TABLE IF EXISTS `universe_facility`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_facility` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hidden` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `main_image` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `duration` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `min_players` int NOT NULL,
  `max_players` int NOT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `views` int DEFAULT NULL,
  `externallink` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `phonenumber` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tags` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_size` int DEFAULT NULL,
  `image_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `published` tinyint(1) DEFAULT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_facility`
--

LOCK TABLES `universe_facility` WRITE;
/*!40000 ALTER TABLE `universe_facility` DISABLE KEYS */;
INSERT INTO `universe_facility` VALUES (1,0,'2024-02-01 21:16:34','TennisCourts-05.jpg',30,'Tennis Court','<p>State-of-the-art tennis facility offering pristine courts, professional coaching, and a vibrant community atmosphere. Elevate your game in a modern setting designed for players of all levels, providing a perfect blend of top-notch amenities and a passion for tennis excellence.<br></p>',1,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,270949,'image/jpeg','TennisCourts-05.jpg','1200,800','2024-02-01 21:16:34',NULL,NULL,'tennis-court'),(2,0,'2024-02-11 04:22:49','Deree-Gym-Basketball-04.jpg',30,'Basketball Court','<p style=\"border: 0px solid rgb(227, 227, 227); --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(69,89,164,.5); --tw-ring-offset-shadow: 0 0 transparent; --tw-ring-shadow: 0 0 transparent; --tw-shadow: 0 0 transparent; --tw-shadow-colored: 0 0 transparent; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; margin: 1.25em 0px;\">Step onto our basketball court and immerse yourself in the electrifying energy of the game. Whether you\'re a seasoned baller or a casual enthusiast, our court sets the stage for exhilarating matchups and unforgettable moments of athletic prowess.</p><p style=\"border: 0px solid rgb(227, 227, 227); --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(69,89,164,.5); --tw-ring-offset-shadow: 0 0 transparent; --tw-ring-shadow: 0 0 transparent; --tw-shadow: 0 0 transparent; --tw-shadow-colored: 0 0 transparent; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; margin: 1.25em 0px;\">With its sleek hardwood surface and regulation dimensions, our basketball court provides the perfect arena for players to showcase their skills. From fast breaks to precision three-pointers, every aspect of the game comes to life on our meticulously maintained court.</p><p style=\"border: 0px solid rgb(227, 227, 227); --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(69,89,164,.5); --tw-ring-offset-shadow: 0 0 transparent; --tw-ring-shadow: 0 0 transparent; --tw-shadow: 0 0 transparent; --tw-shadow-colored: 0 0 transparent; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; margin: 1.25em 0px;\">Feel the adrenaline surge as you dribble down the court, executing flawless crossovers and driving towards the hoop with determination. Whether you\'re engaging in a friendly pickup game or competing in organized leagues, our court offers the space and amenities to support your basketball aspirations.</p><p style=\"border: 0px solid rgb(227, 227, 227); --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(69,89,164,.5); --tw-ring-offset-shadow: 0 0 transparent; --tw-ring-shadow: 0 0 transparent; --tw-shadow: 0 0 transparent; --tw-shadow-colored: 0 0 transparent; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; margin: 1.25em 0px;\">Equipped with adjustable hoops, ample seating for spectators, and a vibrant atmosphere that inspires players to give it their all, our basketball court is more than just a place to play—it\'s a community hub where passion for the game thrives.</p><p style=\"border: 0px solid rgb(227, 227, 227); --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(69,89,164,.5); --tw-ring-offset-shadow: 0 0 transparent; --tw-ring-shadow: 0 0 transparent; --tw-shadow: 0 0 transparent; --tw-shadow-colored: 0 0 transparent; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; margin: 1.25em 0px;\">Join us on the court and experience the thrill of basketball at its finest. Whether you\'re here to compete, train, or simply shoot some hoops with friends, our basketball court welcomes players of all skill levels to come together and share in the joy of the game.</p>',1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,228806,'image/jpeg','Deree-Gym-Basketball-04.jpg','1200,800','2024-02-11 04:22:49',NULL,NULL,'basketball-court'),(3,0,'2024-02-11 04:23:55','Pierce-Gym-04.jpg',30,'Ping Pong Tables','<p style=\"border: 0px solid rgb(227, 227, 227); --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(69,89,164,.5); --tw-ring-offset-shadow: 0 0 transparent; --tw-ring-shadow: 0 0 transparent; --tw-shadow: 0 0 transparent; --tw-shadow-colored: 0 0 transparent; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; margin: 1.25em 0px;\">Welcome to our ping pong paradise, where skill meets strategy and every match is a thrilling test of reflexes and finesse. Whether you\'re a seasoned pro or a casual player, our ping pong facility offers the perfect blend of competition and camaraderie for players of all levels.</p><p style=\"border: 0px solid rgb(227, 227, 227); --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(69,89,164,.5); --tw-ring-offset-shadow: 0 0 transparent; --tw-ring-shadow: 0 0 transparent; --tw-shadow: 0 0 transparent; --tw-shadow-colored: 0 0 transparent; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; margin: 1.25em 0px;\">Step onto our regulation-size tables and feel the smooth surface beneath your paddle as you prepare to serve up some serious excitement. With ample space, optimal lighting, and professional-grade equipment, our ping pong facility provides the ideal setting for fast-paced rallies and intense showdowns.</p><p style=\"border: 0px solid rgb(227, 227, 227); --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(69,89,164,.5); --tw-ring-offset-shadow: 0 0 transparent; --tw-ring-shadow: 0 0 transparent; --tw-shadow: 0 0 transparent; --tw-shadow-colored: 0 0 transparent; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; margin: 1.25em 0px;\">Whether you\'re engaging in friendly matches with friends or competing in organized tournaments, our ping pong facility offers a welcoming atmosphere where players can come together to enjoy the game they love. With comfortable seating areas for spectators and a vibrant ambiance that encourages friendly banter and sportsmanship, our facility is more than just a place to play—it\'s a community hub where ping pong enthusiasts can connect and thrive.</p><p style=\"border: 0px solid rgb(227, 227, 227); --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(69,89,164,.5); --tw-ring-offset-shadow: 0 0 transparent; --tw-ring-shadow: 0 0 transparent; --tw-shadow: 0 0 transparent; --tw-shadow-colored: 0 0 transparent; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; margin: 1.25em 0px;\">Join us on the tables and experience the thrill of ping pong like never before. Whether you\'re here to improve your skills, challenge your friends, or simply unwind with a few games, our ping pong facility promises endless hours of excitement and entertainment for players of all ages and abilities.</p>',1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,138490,'image/jpeg','Pierce-Gym-04.jpg','1200,800','2024-02-11 04:23:55',NULL,NULL,'ping-pong-tables'),(4,0,'2024-02-11 04:24:24','Pierce-Gym-05.jpg',30,'Children\'s Basketball Courts:','<p style=\"border: 0px solid rgb(227, 227, 227); --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(69,89,164,.5); --tw-ring-offset-shadow: 0 0 transparent; --tw-ring-shadow: 0 0 transparent; --tw-shadow: 0 0 transparent; --tw-shadow-colored: 0 0 transparent; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; margin: 1.25em 0px;\">Welcome to our children\'s basketball courts, where young athletes can dream big and shoot for the stars. Designed with safety and fun in mind, our courts provide the perfect environment for kids to learn the fundamentals of basketball while developing confidence, teamwork, and sportsmanship.</p><p style=\"border: 0px solid rgb(227, 227, 227); --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(69,89,164,.5); --tw-ring-offset-shadow: 0 0 transparent; --tw-ring-shadow: 0 0 transparent; --tw-shadow: 0 0 transparent; --tw-shadow-colored: 0 0 transparent; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; margin: 1.25em 0px;\">With adjustable hoops, scaled-down equipment, and age-appropriate instruction, our children\'s basketball courts cater to kids of all ages and skill levels. Whether they\'re taking their first dribbles or mastering layups and jump shots, our experienced coaches are here to guide and encourage them every step of the way.</p><p style=\"border: 0px solid rgb(227, 227, 227); --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(69,89,164,.5); --tw-ring-offset-shadow: 0 0 transparent; --tw-ring-shadow: 0 0 transparent; --tw-shadow: 0 0 transparent; --tw-shadow-colored: 0 0 transparent; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; margin: 1.25em 0px;\">Watch with pride as your child\'s skills and confidence grow with each game and practice session. With spacious courts, colorful markings, and a supportive atmosphere that celebrates effort and improvement, our children\'s basketball program offers a positive and inclusive experience that inspires a lifelong love of the game.</p><p style=\"border: 0px solid rgb(227, 227, 227); --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgba(69,89,164,.5); --tw-ring-offset-shadow: 0 0 transparent; --tw-ring-shadow: 0 0 transparent; --tw-shadow: 0 0 transparent; --tw-shadow-colored: 0 0 transparent; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; margin: 1.25em 0px;\">Join us on the courts and watch as your child discovers the joy of basketball in a fun and nurturing environment. Whether they\'re here to learn new skills, make friends, or simply burn off some energy, our children\'s basketball program promises an unforgettable experience that will leave them smiling and asking for more.</p>',1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,153988,'image/jpeg','Pierce-Gym-05.jpg','1200,800','2024-02-11 04:24:24',NULL,NULL,'childrens-basketball-courts');
/*!40000 ALTER TABLE `universe_facility` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_facility_images`
--

DROP TABLE IF EXISTS `universe_facility_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_facility_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `facility_id` int DEFAULT NULL,
  `position` int DEFAULT NULL,
  `image_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_size` int DEFAULT NULL,
  `image_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_448F925DA7014910` (`facility_id`),
  CONSTRAINT `FK_448F925DA7014910` FOREIGN KEY (`facility_id`) REFERENCES `universe_facility` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_facility_images`
--

LOCK TABLES `universe_facility_images` WRITE;
/*!40000 ALTER TABLE `universe_facility_images` DISABLE KEYS */;
INSERT INTO `universe_facility_images` VALUES (1,2,0,'65c82f791b4a4911443860.jpg',222476,'image/jpeg','Deree-Gym-Basketball-03 (1).jpg',NULL,'2024-02-11 04:22:49'),(2,2,1,'65c82f791b808653793964.jpg',238879,'image/jpeg','Deree-Gym-Basketball-05.jpg',NULL,'2024-02-11 04:22:49'),(3,4,0,'65c82fd81c452939669044.jpg',147050,'image/jpeg','Pierce-Gym-03.jpg',NULL,'2024-02-11 04:24:24');
/*!40000 ALTER TABLE `universe_facility_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_favorites`
--

DROP TABLE IF EXISTS `universe_favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_favorites` (
  `Event_id` int NOT NULL,
  `User_Id` int NOT NULL,
  PRIMARY KEY (`Event_id`,`User_Id`),
  KEY `IDX_522AF01688818ADD` (`Event_id`),
  KEY `IDX_522AF016FD57CEAB` (`User_Id`),
  CONSTRAINT `FK_522AF01688818ADD` FOREIGN KEY (`Event_id`) REFERENCES `universe_event` (`id`),
  CONSTRAINT `FK_522AF016FD57CEAB` FOREIGN KEY (`User_Id`) REFERENCES `universe_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_favorites`
--

LOCK TABLES `universe_favorites` WRITE;
/*!40000 ALTER TABLE `universe_favorites` DISABLE KEYS */;
INSERT INTO `universe_favorites` VALUES (2,7),(4,3),(4,7),(5,3),(5,7),(6,3);
/*!40000 ALTER TABLE `universe_favorites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_file`
--

DROP TABLE IF EXISTS `universe_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_file` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `file_size` int NOT NULL,
  `created_at` datetime NOT NULL,
  `file_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `file_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D3A2DC2EA76ED395` (`user_id`),
  CONSTRAINT `FK_D3A2DC2EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_file`
--

LOCK TABLES `universe_file` WRITE;
/*!40000 ALTER TABLE `universe_file` DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_following`
--

DROP TABLE IF EXISTS `universe_following`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_following` (
  `Follow_to` int NOT NULL,
  `Follow_From` int NOT NULL,
  KEY `IDX_C7FC1D009F5D9622` (`Follow_to`),
  KEY `IDX_C7FC1D00FD57CEAB` (`Follow_From`),
  CONSTRAINT `FK_C7FC1D009F5D9622` FOREIGN KEY (`Follow_to`) REFERENCES `universe_user` (`id`),
  CONSTRAINT `FK_C7FC1D00FD57CEAB` FOREIGN KEY (`Follow_From`) REFERENCES `universe_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_following`
--

LOCK TABLES `universe_following` WRITE;
/*!40000 ALTER TABLE `universe_following` DISABLE KEYS */;
INSERT INTO `universe_following` VALUES (3,5),(3,7),(4,7),(2,7),(9,7);
/*!40000 ALTER TABLE `universe_following` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_gbmembers`
--

DROP TABLE IF EXISTS `universe_gbmembers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_gbmembers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `attendeeid` int DEFAULT NULL,
  `orgid` int DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_224AC267633CAD3B` (`attendeeid`),
  KEY `IDX_224AC267AF0ED79C` (`orgid`),
  CONSTRAINT `FK_224AC267633CAD3B` FOREIGN KEY (`attendeeid`) REFERENCES `universe_user` (`id`),
  CONSTRAINT `FK_224AC267AF0ED79C` FOREIGN KEY (`orgid`) REFERENCES `universe_organizer` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_gbmembers`
--

LOCK TABLES `universe_gbmembers` WRITE;
/*!40000 ALTER TABLE `universe_gbmembers` DISABLE KEYS */;
INSERT INTO `universe_gbmembers` VALUES (1,7,3,'President','2024-02-12 03:15:01','2024-02-12 03:15:01','President');
/*!40000 ALTER TABLE `universe_gbmembers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_help_center_article`
--

DROP TABLE IF EXISTS `universe_help_center_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_help_center_article` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int DEFAULT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tags` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `content` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `views` int DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL,
  `featured` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_3C5A4E9D989D9B62` (`slug`),
  UNIQUE KEY `UNIQ_3C5A4E9D6FBC9426` (`tags`),
  KEY `IDX_3C5A4E9D12469DE2` (`category_id`),
  CONSTRAINT `FK_3C5A4E9D12469DE2` FOREIGN KEY (`category_id`) REFERENCES `universe_help_center_category` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_help_center_article`
--

LOCK TABLES `universe_help_center_article` WRITE;
/*!40000 ALTER TABLE `universe_help_center_article` DISABLE KEYS */;
INSERT INTO `universe_help_center_article` VALUES (1,2,'how-do-i-create-an-event',NULL,'How do I create an event?','<p>To create an event, log in to your account and navigate to the \"Create Event\" page. Fill in the required details such as event name, date, and location. You can also customize additional settings to make your event unique.<br></p>',9,0,1,'2024-01-31 19:57:08','2024-05-01 17:23:27',NULL),(2,2,'can-i-edit-my-event-details-after-creation',NULL,'Can I edit my event details after creation?','<p>Yes, you can edit your event details at any time. Simply go to the \"Manage Events\" section, select the event you want to edit, and click on the \"Edit\" option. Make the necessary changes and save your updates.<br></p>',11,0,1,'2024-01-31 19:57:17','2024-05-01 18:03:47',NULL),(3,1,'how-do-i-register-for-an-event',NULL,'How do I register for an event?','<p>To register for an event, find the event page and click on the \"Register\" button. Fill in your details and follow the prompts. Some events may require payment for registration, so have your payment information ready.<br></p>',13,0,1,'2024-01-31 19:57:29','2024-05-01 17:59:47',NULL),(4,1,'can-i-cancel-my-event-registration',NULL,'Can I cancel my event registration?','<p>Yes, you can cancel your registration for an event. Go to the event page, locate the \"Cancel Registration\" option, and follow the instructions. Keep in mind that some events may have specific cancellation policies.<br></p>',15,0,1,'2024-01-31 19:57:40','2024-05-17 18:16:10',NULL),(5,3,'how-do-i-reset-my-password',NULL,'How do I reset my password?','<p>If you forget your password, click on the \"Forgot Password\" link on the login page. Enter your email address, and you\'ll receive instructions on how to reset your password.<br></p>',17,0,1,'2024-01-31 19:57:48','2024-05-10 18:44:37',NULL),(6,3,'is-my-personal-information-secure',NULL,'Is my personal information secure?','<p>Yes, we take the security of your information seriously. We use industry-standard encryption and security measures to protect your data. Check our Privacy Policy for more details on how we handle your information.<br></p>',11,0,1,'2024-01-31 19:57:55','2024-05-01 07:04:45',NULL);
/*!40000 ALTER TABLE `universe_help_center_article` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_help_center_category`
--

DROP TABLE IF EXISTS `universe_help_center_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_help_center_category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `parent_id` int DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_2C1D5215989D9B62` (`slug`),
  KEY `IDX_2C1D5215727ACA70` (`parent_id`),
  CONSTRAINT `FK_2C1D5215727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `universe_help_center_category` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_help_center_category`
--

LOCK TABLES `universe_help_center_category` WRITE;
/*!40000 ALTER TABLE `universe_help_center_category` DISABLE KEYS */;
INSERT INTO `universe_help_center_category` VALUES (1,NULL,'attendee','Attendee','fas fa-user-graduate',0,'2024-01-31 19:54:18','2024-01-31 19:55:28',NULL),(2,NULL,'creator','Creator','fas fa-building',0,'2024-01-31 19:54:30','2024-01-31 19:55:42',NULL),(3,NULL,'account','Account','fas fa-user-alt',0,'2024-01-31 19:55:02','2024-01-31 19:55:02',NULL);
/*!40000 ALTER TABLE `universe_help_center_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_homepage_hero_setting`
--

DROP TABLE IF EXISTS `universe_homepage_hero_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_homepage_hero_setting` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `content` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `paragraph` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `custom_background_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `custom_background_size` int DEFAULT NULL,
  `custom_background_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `custom_background_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `custom_background_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `show_search_box` tinyint(1) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_5FBE84E82B36786B` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_homepage_hero_setting`
--

LOCK TABLES `universe_homepage_hero_setting` WRITE;
/*!40000 ALTER TABLE `universe_homepage_hero_setting` DISABLE KEYS */;
INSERT INTO `universe_homepage_hero_setting` VALUES (1,'Universe','events','Say goodbye to media clutter with a one-stop seamless experience!','5d99d60e41207545475471.jpg',346806,'image/jpeg','ezra-jeffrey-comeau-pPquxoraq_M-unsplash.jpg','1500,1000',1,'2024-01-31 20:33:18');
/*!40000 ALTER TABLE `universe_homepage_hero_setting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_listing`
--

DROP TABLE IF EXISTS `universe_listing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_listing` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `organizer_id` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `shortdescription` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `slug` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `views` int DEFAULT NULL,
  `externallink` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `phonenumber` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tags` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_size` int DEFAULT NULL,
  `image_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `typeoflisting` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `alreadyapplied` tinyint(1) DEFAULT NULL,
  `published` tinyint(1) DEFAULT NULL,
  `isexternal` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_190CD766989D9B62` (`slug`),
  KEY `IDX_190CD76671F7E88B` (`event_id`),
  KEY `IDX_190CD76612469DE2` (`category_id`),
  KEY `IDX_190CD766876C4DDA` (`organizer_id`),
  CONSTRAINT `FK_190CD76612469DE2` FOREIGN KEY (`category_id`) REFERENCES `universe_listingcategory` (`id`),
  CONSTRAINT `FK_190CD76671F7E88B` FOREIGN KEY (`event_id`) REFERENCES `universe_event` (`id`),
  CONSTRAINT `FK_190CD766876C4DDA` FOREIGN KEY (`organizer_id`) REFERENCES `universe_organizer` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_listing`
--

LOCK TABLES `universe_listing` WRITE;
/*!40000 ALTER TABLE `universe_listing` DISABLE KEYS */;
INSERT INTO `universe_listing` VALUES (2,6,1,3,'Social Media Manager','<p>Plan and execute engaging events and activities, such as hackathons, capture-the-flag competitions, and guest speaker sessions, to foster a culture of cybersecurity awareness and innovation on campus. Coordinate with industry partners, student organizations, and academic departments to ensure the success of cybersecurity initiatives.<br></p>','Manage our social media accounts','social-media-manager-edaeba86147ba0d74f13','b0f78fd24c',0,NULL,'+306989442438','alexandros.kourtis@acg.edu',NULL,'65ca74a1f23cd754805381.jpg',153559,'image/jpeg',NULL,'volunteer-opportunity-digital-media.jpg','1280,900','2024-02-11 19:49:53','2024-02-12 21:44:47',NULL,NULL,1,NULL),(3,6,1,3,'Student Cybersecurity Ambassador','<p style=\"font-size: 12.88px;\">Spread cybersecurity awareness across campus through fun and interactive events.</p><p style=\"font-size: 12.88px;\">Qualifications: Passion for cybersecurity, strong communication skills, and a desire to engage with fellow students.</p>','Spread cybersecurity awareness across campus through fun and interactive events.','student-cybersecurity-ambassador-bb12a3b13ae3df1b98b1','d518e8714e',0,NULL,'+306989442438','alexandros.kourtis@acg.edu',NULL,'65c90aed20c85284169529.jpg',75473,'image/jpeg',NULL,'20096105_0.jpg','630,630','2024-02-11 19:59:09','2024-02-13 04:49:01',NULL,NULL,1,NULL),(4,NULL,1,3,'Junior Systems Administrator','<p>•&nbsp; &nbsp; Manages computer systems like Linux and Windows.</p><p>•&nbsp; &nbsp; Configures Web Servers&nbsp;</p><p>•&nbsp; &nbsp; Handles network setups and basic security.</p><p>•&nbsp; &nbsp; Understands cloud platforms and backups.</p>','Manages computer systems like Linux and Windows.','junior-systems-administrator-864e218b2e571070c4f6','62a53adf48',1,NULL,'+306989442438','alexandros.kourtis@acg.edu',NULL,'65c90b87612ca629194535.jpg',78980,'image/jpeg',NULL,'laptop-management-server-administration-wallpaper-preview.jpg','728,546','2024-02-11 20:01:43','2024-02-12 04:18:22',NULL,NULL,1,NULL),(5,NULL,1,3,'Junior Front-End Developer','<p><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">•    Proficient in HTML</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">, CSS</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">, and JavaScript</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">.\r\n•    Experience with front</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">-end frameworks like React</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">, Angular</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">, or Vue</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">.\r\n•    Responsive web design skills</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">.\r\n•    Cross</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">-browser compatibility testing</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">.</span><br></p>','Proficient in HTML, CSS, and JavaScript.','junior-front-end-developer-495c0a70545b87c53d43','50347eb84b',0,NULL,'+306989442438','alexandros.kourtis@acg.edu',NULL,'65c90bb7513c6048555061.jpeg',260427,'image/jpeg',NULL,'5ad57530-0130-11ed-974c-4d76d2fc8eac-Junior-Front-End-Developer.jpeg','1920,1280','2024-02-11 20:02:31','2024-02-11 20:02:31',NULL,NULL,1,NULL),(6,NULL,1,3,'Junior Back-End Developer','<p><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">•    Strong knowledge of server</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">-side languages </span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">(e</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">.g</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">.</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">, Node</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">.js</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">, Python</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">, Ruby</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">)</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">.\r\n•    Experience with web frameworks </span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">(e</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">.g</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">.</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">, Express</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">, Django</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">, Flask</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">)</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">.\r\n•    Database management skills </span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">(MySQL</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">, MongoDB</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">, or similar</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">)</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">.\r\n•    API design and integration expertise</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">.</span><br></p>','Strong knowledge of server-side languages (e.g., Node.js, Python, Ruby)','junior-back-end-developer-1654dadcde4f992bbcfe','e39698844a',0,NULL,'+306989442438','alexandros.kourtis@acg.edu',NULL,'65c90be51b17d965303919.jpeg',50709,'image/jpeg',NULL,'backend-developer-Large.jpeg','978,544','2024-02-11 20:03:17','2024-02-11 20:03:17',NULL,NULL,1,NULL),(7,NULL,1,3,'Junior MySQL Developer','<p><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">•    Proficient in MySQL database management</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">.\r\n•    Database design and optimization skills</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">.\r\n•    SQL query optimization</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">.\r\n•    Data modeling experience</span><span style=\"margin: 0px; padding: 0px; border: 0px; font-family: &quot;gg sans&quot;, &quot;Noto Sans&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 16px; vertical-align: baseline; outline: 0px; color: rgb(219, 222, 225); white-space-collapse: break-spaces; background-color: rgb(49, 51, 56);\">.</span><br></p>','Proficient in MySQL database management.','junior-mysql-developer-057e34059e76fdf919a2','060c641a41',0,NULL,'+306989442438','alexandros.kourtis@acg.edu',NULL,'65c90c05db9b5696285968.jpg',38470,'image/jpeg',NULL,'5167442_873c.jpg','750,422','2024-02-11 20:03:49','2024-02-11 20:03:49',NULL,NULL,1,NULL);
/*!40000 ALTER TABLE `universe_listing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_listing_date_positions`
--

DROP TABLE IF EXISTS `universe_listing_date_positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_listing_date_positions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `listing_id` int DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `startdate` datetime NOT NULL,
  `enddate` datetime NOT NULL,
  `allowoverapply` tinyint(1) NOT NULL,
  `shortdescription` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `candidatesamount` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9FEAFA7FD4619D1A` (`listing_id`),
  CONSTRAINT `FK_9FEAFA7FD4619D1A` FOREIGN KEY (`listing_id`) REFERENCES `universe_listing` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_listing_date_positions`
--

LOCK TABLES `universe_listing_date_positions` WRITE;
/*!40000 ALTER TABLE `universe_listing_date_positions` DISABLE KEYS */;
INSERT INTO `universe_listing_date_positions` VALUES (1,2,'2024-02-12 21:44:47','2024-02-12 21:44:47','2024-02-13 21:44:00','2024-02-13 12:44:00',1,'dfgds',10);
/*!40000 ALTER TABLE `universe_listing_date_positions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_listing_file`
--

DROP TABLE IF EXISTS `universe_listing_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_listing_file` (
  `id` int NOT NULL AUTO_INCREMENT,
  `listing_id` int DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `file_size` int DEFAULT NULL,
  `file_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `file_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `file_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `position` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_EE5115BAD4619D1A` (`listing_id`),
  CONSTRAINT `FK_EE5115BAD4619D1A` FOREIGN KEY (`listing_id`) REFERENCES `universe_listing` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_listing_file`
--

LOCK TABLES `universe_listing_file` WRITE;
/*!40000 ALTER TABLE `universe_listing_file` DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_listing_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_listing_image`
--

DROP TABLE IF EXISTS `universe_listing_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_listing_image` (
  `id` int NOT NULL AUTO_INCREMENT,
  `listing_id` int DEFAULT NULL,
  `image_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_size` int DEFAULT NULL,
  `image_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `position` int DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F35C808AD4619D1A` (`listing_id`),
  CONSTRAINT `FK_F35C808AD4619D1A` FOREIGN KEY (`listing_id`) REFERENCES `universe_listing` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_listing_image`
--

LOCK TABLES `universe_listing_image` WRITE;
/*!40000 ALTER TABLE `universe_listing_image` DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_listing_image` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_listing_submission`
--

DROP TABLE IF EXISTS `universe_listing_submission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_listing_submission` (
  `id` int NOT NULL AUTO_INCREMENT,
  `listing_id` int DEFAULT NULL,
  `position_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `content` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `file_size` int DEFAULT NULL,
  `file_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `file_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `file_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `created_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3822BBD4D4619D1A` (`listing_id`),
  KEY `IDX_3822BBD4DD842E46` (`position_id`),
  KEY `IDX_3822BBD4A76ED395` (`user_id`),
  CONSTRAINT `FK_3822BBD4A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`),
  CONSTRAINT `FK_3822BBD4D4619D1A` FOREIGN KEY (`listing_id`) REFERENCES `universe_listing` (`id`),
  CONSTRAINT `FK_3822BBD4DD842E46` FOREIGN KEY (`position_id`) REFERENCES `universe_listing_date_positions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_listing_submission`
--

LOCK TABLES `universe_listing_submission` WRITE;
/*!40000 ALTER TABLE `universe_listing_submission` DISABLE KEYS */;
INSERT INTO `universe_listing_submission` VALUES (1,4,NULL,2,'test','65c97fee623d7806002797.pdf',87304,'application/pdf','CV-Sardis.pdf',NULL,'2024-02-12 04:18:22',NULL,'2024-02-12 04:18:22');
/*!40000 ALTER TABLE `universe_listing_submission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_listingcategory`
--

DROP TABLE IF EXISTS `universe_listingcategory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_listingcategory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_size` int DEFAULT NULL,
  `image_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `hidden` tinyint(1) NOT NULL,
  `featured` tinyint(1) NOT NULL,
  `featuredorder` int DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_649EB8BE989D9B62` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_listingcategory`
--

LOCK TABLES `universe_listingcategory` WRITE;
/*!40000 ALTER TABLE `universe_listingcategory` DISABLE KEYS */;
INSERT INTO `universe_listingcategory` VALUES (1,'volunteer','Volunteer',NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,'2024-02-04 21:17:40','2024-02-04 21:17:40',NULL),(2,'part-time','Part Time',NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,'2024-02-04 21:17:59','2024-02-04 21:18:12',NULL),(3,'full-time','Full-time',NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,'2024-02-04 21:18:06','2024-02-04 21:18:06',NULL),(4,'other','Other',NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,'2024-02-04 21:22:37','2024-02-04 21:22:37',NULL);
/*!40000 ALTER TABLE `universe_listingcategory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_lives`
--

DROP TABLE IF EXISTS `universe_lives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_lives` (
  `id` int NOT NULL AUTO_INCREMENT,
  `main_image` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `hidden` tinyint(1) NOT NULL,
  `title` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `link` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created_at` datetime NOT NULL,
  `image_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_size` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `image_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_9C0A5E1F989D9B62` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_lives`
--

LOCK TABLES `universe_lives` WRITE;
/*!40000 ALTER TABLE `universe_lives` DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_lives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_managementclubssocieties`
--

DROP TABLE IF EXISTS `universe_managementclubssocieties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_managementclubssocieties` (
  `id` int NOT NULL AUTO_INCREMENT,
  `userid` int DEFAULT NULL,
  `orgid` int DEFAULT NULL,
  `initiatedby` int DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_EDA30F7BF132696E` (`userid`),
  KEY `IDX_EDA30F7BAF0ED79C` (`orgid`),
  KEY `IDX_EDA30F7BBB0AB2B1` (`initiatedby`),
  CONSTRAINT `FK_EDA30F7BAF0ED79C` FOREIGN KEY (`orgid`) REFERENCES `universe_organizer` (`id`),
  CONSTRAINT `FK_EDA30F7BBB0AB2B1` FOREIGN KEY (`initiatedby`) REFERENCES `universe_user` (`id`),
  CONSTRAINT `FK_EDA30F7BF132696E` FOREIGN KEY (`userid`) REFERENCES `universe_user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_managementclubssocieties`
--

LOCK TABLES `universe_managementclubssocieties` WRITE;
/*!40000 ALTER TABLE `universe_managementclubssocieties` DISABLE KEYS */;
INSERT INTO `universe_managementclubssocieties` VALUES (6,7,5,7,'2024-03-28 19:40:37','0'),(7,7,3,2,'2024-03-28 20:03:56','0');
/*!40000 ALTER TABLE `universe_managementclubssocieties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_menu`
--

DROP TABLE IF EXISTS `universe_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_menu` (
  `id` int NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_menu`
--

LOCK TABLES `universe_menu` WRITE;
/*!40000 ALTER TABLE `universe_menu` DISABLE KEYS */;
INSERT INTO `universe_menu` VALUES (3,'header-menu','Header'),(4,'first-footer-section-menu','First'),(5,'second-footer-section-menu','Second'),(6,'third-footer-section-menu','Third');
/*!40000 ALTER TABLE `universe_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_menu_element`
--

DROP TABLE IF EXISTS `universe_menu_element`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_menu_element` (
  `id` int NOT NULL AUTO_INCREMENT,
  `menu_id` int DEFAULT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `link` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `custom_link` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `label` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `position` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_FCABB7F8989D9B62` (`slug`),
  KEY `IDX_FCABB7F8CCD7E912` (`menu_id`),
  CONSTRAINT `FK_FCABB7F8CCD7E912` FOREIGN KEY (`menu_id`) REFERENCES `universe_menu` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_menu_element`
--

LOCK TABLES `universe_menu_element` WRITE;
/*!40000 ALTER TABLE `universe_menu_element` DISABLE KEYS */;
INSERT INTO `universe_menu_element` VALUES (3,3,'fas fa-calendar-alt','events','/events',NULL,'Events',0),(7,3,'fas fa-ticket-alt','my-tickets',NULL,'https://universe.alexandroskourtis.me/dashboard/attendee/my-tickets','My Tickets',2),(8,4,NULL,'about-us','/page/about-us',NULL,'About us',0),(9,4,NULL,'help-center',NULL,'https://universe.alexandroskourtis.me/help-center','Help center',1),(10,4,NULL,'privacy-policy',NULL,'https://universe.alexandroskourtis.me/page/privacy-policy','Privacy policy',2),(11,4,NULL,'cookie-policy',NULL,'https://universe.alexandroskourtis.me/page/cookie-policy','Cookie policy',3),(12,4,NULL,'terms-of-service',NULL,'https://universe.alexandroskourtis.me/page/terms-of-service','Terms of service',4),(13,5,NULL,'register-as-attendee',NULL,'https://universe.alexandroskourtis.me/signup/attendee','Register as attendee',0),(14,5,NULL,'register-as-creator',NULL,'https://universe.alexandroskourtis.me/signup/organizer','Register as creator',1),(15,5,NULL,'my-tickets-1',NULL,'https://universe.alexandroskourtis.me/dashboard/attendee/my-tickets','My tickets',2),(16,5,NULL,'update-password',NULL,'https://universe.alexandroskourtis.me/dashboard/change-password','Update password',3),(17,5,NULL,'update-email',NULL,'https://universe.alexandroskourtis.me/api/profile/change-email','Update email',4),(18,6,NULL,'events-1',NULL,'https://universe.alexandroskourtis.me/events','Events',0),(19,6,NULL,'leaderboards',NULL,'https://universe.alexandroskourtis.me/leaderboards','Leaderboards',1),(20,6,NULL,'venues-1',NULL,'https://universe.alexandroskourtis.me/venues','Venues',2),(21,6,NULL,'athletics',NULL,'https://universe.alexandroskourtis.me/athletics','Athletics',3),(22,6,NULL,'listings',NULL,'https://universe.alexandroskourtis.me/listings','Listings',4),(24,3,'fas fa-address-book','creators',NULL,'https://universe.alexandroskourtis.me/creators','Creators',1),(25,3,'fas fa-calendar','add-event',NULL,'https://universe.alexandroskourtis.me/dashboard/organizer/my-events/add','Add Event',3);
/*!40000 ALTER TABLE `universe_menu_element` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_message`
--

DROP TABLE IF EXISTS `universe_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_message` (
  `id` int NOT NULL AUTO_INCREMENT,
  `universe_chat_id` int DEFAULT NULL,
  `sender_id` int DEFAULT NULL,
  `receiver_id` int DEFAULT NULL,
  `content` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `sent_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_64B1AFCDC896EE97` (`universe_chat_id`),
  KEY `IDX_64B1AFCDF624B39D` (`sender_id`),
  KEY `IDX_64B1AFCDCD53EDB6` (`receiver_id`),
  CONSTRAINT `FK_64B1AFCDC896EE97` FOREIGN KEY (`universe_chat_id`) REFERENCES `universe_chat` (`id`),
  CONSTRAINT `FK_64B1AFCDCD53EDB6` FOREIGN KEY (`receiver_id`) REFERENCES `universe_user` (`id`),
  CONSTRAINT `FK_64B1AFCDF624B39D` FOREIGN KEY (`sender_id`) REFERENCES `universe_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_message`
--

LOCK TABLES `universe_message` WRITE;
/*!40000 ALTER TABLE `universe_message` DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_notification`
--

DROP TABLE IF EXISTS `universe_notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_notification` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `userid` int NOT NULL,
  `isread` tinyint(1) NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_notification`
--

LOCK TABLES `universe_notification` WRITE;
/*!40000 ALTER TABLE `universe_notification` DISABLE KEYS */;
INSERT INTO `universe_notification` VALUES (1,'You have just booked this event: Capture The Flag (CTF) Competition I | Ticket',7,0,'Universe - Capture The Flag (CTF) Competition I event booked #a2b396a14b','2024-02-08 06:36:34','2024-02-08 06:36:34'),(2,'You have just booked this event: Capture The Flag (CTF) Competition I | Ticket',7,0,'Universe - Capture The Flag (CTF) Competition I event booked #a2b396a14b','2024-02-08 06:36:34','2024-02-08 06:36:34'),(3,'You have just booked this event: Tuesday March 15 - Day 1 - Business Week | Aris Ikkos, Ph.D',7,0,'Universe - Tuesday March 15 - Day 1 - Business Week event booked #ffedf19641','2024-02-08 06:37:16','2024-02-08 06:37:16'),(4,'You have just booked this event: Tuesday March 15 - Day 1 - Business Week | Aris Ikkos, Ph.D',7,0,'Universe - Tuesday March 15 - Day 1 - Business Week event booked #ffedf19641','2024-02-08 06:37:16','2024-02-08 06:37:16'),(5,'You have just booked this event: Tuesday March 15 - Day 1 - Business Week | Opening Reception',7,0,'Universe - Tuesday March 15 - Day 1 - Business Week event booked #5fe6a2f144','2024-02-08 20:10:35','2024-02-08 20:10:35'),(6,'You have just booked this event: Tuesday March 15 - Day 1 - Business Week | Opening Reception',7,0,'Universe - Tuesday March 15 - Day 1 - Business Week event booked #5fe6a2f144','2024-02-08 20:10:35','2024-02-08 20:10:35'),(7,'You have just booked this event: Tuesday March 15 - Day 1 - Business Week | Mihalis Boutaris',7,0,'Universe - Tuesday March 15 - Day 1 - Business Week event booked #842e05144c','2024-02-09 04:32:59','2024-02-09 04:32:59'),(8,'You have just booked this event: Tuesday March 15 - Day 1 - Business Week | Mihalis Boutaris',7,0,'Universe - Tuesday March 15 - Day 1 - Business Week event booked #842e05144c','2024-02-09 04:32:59','2024-02-09 04:32:59'),(9,'You have just booked this event: dsfadsfsfasfsfasfs | Speaker 1',5,0,'Universe - dsfadsfsfasfsfasfs event booked #cb47870f40','2024-02-12 21:09:41','2024-02-12 21:09:41'),(10,'You have just booked this event: dsfadsfsfasfsfasfs | Speaker 1',5,0,'Universe - dsfadsfsfasfsfasfs event booked #cb47870f40','2024-02-12 21:09:42','2024-02-12 21:09:42'),(11,'You have just booked this event: Capture The Flag (CTF) Competition I | Ticket',5,0,'Universe - Capture The Flag (CTF) Competition I event booked #a2b396a14b','2024-02-12 21:09:44','2024-02-12 21:09:44'),(12,'You have just booked this event: Capture The Flag (CTF) Competition I | Ticket',5,0,'Universe - Capture The Flag (CTF) Competition I event booked #a2b396a14b','2024-02-12 21:09:44','2024-02-12 21:09:44'),(13,'You have just booked this event: Tuesday March 15 - Day 1 - Business Week | Opening',5,0,'Universe - Tuesday March 15 - Day 1 - Business Week event booked #8b54064d4b','2024-02-12 21:09:47','2024-02-12 21:09:47'),(14,'You have just booked this event: Tuesday March 15 - Day 1 - Business Week | Opening',5,0,'Universe - Tuesday March 15 - Day 1 - Business Week event booked #8b54064d4b','2024-02-12 21:09:47','2024-02-12 21:09:47'),(15,'You have just booked this event: Code &amp; Coffee Meetup | Ticket',5,0,'Universe - Code &amp; Coffee Meetup event booked #8734908543','2024-02-12 21:09:52','2024-02-12 21:09:52'),(16,'You have just booked this event: Code &amp; Coffee Meetup | Ticket',5,0,'Universe - Code &amp; Coffee Meetup event booked #8734908543','2024-02-12 21:09:52','2024-02-12 21:09:52'),(17,'You have just booked this event: Tech Talk Series: Exploring Blockchain | Ticket',5,0,'Universe - Tech Talk Series: Exploring Blockchain event booked #220d65ab49','2024-02-12 21:09:55','2024-02-12 21:09:55'),(18,'You have just booked this event: Tech Talk Series: Exploring Blockchain | Ticket',5,0,'Universe - Tech Talk Series: Exploring Blockchain event booked #220d65ab49','2024-02-12 21:09:55','2024-02-12 21:09:55'),(19,'Important details of this event: Capture The Flag (CTF) Competition I, which you have booked have been changed.',2,0,'Universe - Capture The Flag (CTF) Competition I event details udpated','2024-02-12 21:28:43','2024-02-12 21:28:43'),(20,'Important details of this event: Capture The Flag (CTF) Competition I, which you have booked have been changed.',2,0,'Universe - Capture The Flag (CTF) Competition I event details udpated','2024-02-12 21:28:43','2024-02-12 21:28:43'),(21,'Important details of this event: Code &amp; Coffee Meetup, which you have booked have been changed.',2,0,'Universe - Code &amp; Coffee Meetup event details udpated','2024-02-12 21:28:55','2024-02-12 21:28:55'),(22,'Important details of this event: Tech Talk Series: Exploring Blockchain, which you have booked have been changed.',2,0,'Universe - Tech Talk Series: Exploring Blockchain event details udpated','2024-02-12 21:29:07','2024-02-12 21:29:07'),(23,'Important details of this event: dsfadsfsfasfsfasfs, which you have booked have been changed.',2,0,'Universe - dsfadsfsfasfsfasfs event details udpated','2024-02-12 21:29:13','2024-02-12 21:29:13'),(24,'You have just booked this event: Capture The Flag (CTF) Competition I | Ticket',3,0,'Universe - Capture The Flag (CTF) Competition I event booked #a2b396a14b','2024-02-13 05:04:14','2024-02-13 05:04:14'),(25,'You have just booked this event: Capture The Flag (CTF) Competition I | Ticket',3,0,'Universe - Capture The Flag (CTF) Competition I event booked #a2b396a14b','2024-02-13 05:04:14','2024-02-13 05:04:14'),(26,'You have just booked this event: Tuesday March 15 - Day 1 - Business Week | Opening Reception',3,0,'Universe - Tuesday March 15 - Day 1 - Business Week event booked #5fe6a2f144','2024-02-13 05:04:17','2024-02-13 05:04:17'),(27,'You have just booked this event: Tuesday March 15 - Day 1 - Business Week | Opening Reception',3,0,'Universe - Tuesday March 15 - Day 1 - Business Week event booked #5fe6a2f144','2024-02-13 05:04:17','2024-02-13 05:04:17');
/*!40000 ALTER TABLE `universe_notification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_order`
--

DROP TABLE IF EXISTS `universe_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_order` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `overbook` tinyint(1) DEFAULT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `note` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `status` int NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3417B3D9A76ED395` (`user_id`),
  CONSTRAINT `FK_3417B3D9A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_order`
--

LOCK TABLES `universe_order` WRITE;
/*!40000 ALTER TABLE `universe_order` DISABLE KEYS */;
INSERT INTO `universe_order` VALUES (1,7,NULL,'c9f00140463187f',NULL,1,'2024-02-08 06:36:34','2024-02-08 06:36:34',NULL),(2,7,NULL,'5f4bc602493d958',NULL,1,'2024-02-08 06:37:16','2024-02-08 06:37:16',NULL),(3,7,NULL,'a5771300436f859',NULL,1,'2024-02-08 20:10:35','2024-02-08 20:10:35',NULL),(4,7,NULL,'c5e08a1f49428b9',NULL,1,'2024-02-09 04:32:59','2024-02-09 04:32:59',NULL),(5,5,NULL,'8176860e426aa21',NULL,1,'2024-02-12 21:09:41','2024-02-12 21:09:41',NULL),(6,5,NULL,'a06f901e4a028a8',NULL,2,'2024-02-12 21:09:44','2024-02-12 21:09:44',NULL),(7,5,NULL,'23ae71f145ae895',NULL,2,'2024-02-12 21:09:47','2024-02-12 21:09:47',NULL),(8,5,NULL,'95b94e8c4b93a18',NULL,2,'2024-02-12 21:09:52','2024-02-12 21:09:52',NULL),(9,5,NULL,'e6dab31847868e2',NULL,2,'2024-02-12 21:09:55','2024-02-12 21:09:55',NULL),(10,3,NULL,'54a28873417cbef',NULL,2,'2024-02-13 05:04:14','2024-02-13 05:04:14',NULL),(11,3,NULL,'992d7d434aa2b73',NULL,2,'2024-02-13 05:04:17','2024-02-13 05:04:17',NULL);
/*!40000 ALTER TABLE `universe_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_order_element`
--

DROP TABLE IF EXISTS `universe_order_element`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_order_element` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `eventticket_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_77B5AB2B8D9F6D38` (`order_id`),
  KEY `IDX_77B5AB2B182CEB62` (`eventticket_id`),
  CONSTRAINT `FK_77B5AB2B182CEB62` FOREIGN KEY (`eventticket_id`) REFERENCES `universe_event_date_ticket` (`id`),
  CONSTRAINT `FK_77B5AB2B8D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `universe_order` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_order_element`
--

LOCK TABLES `universe_order_element` WRITE;
/*!40000 ALTER TABLE `universe_order_element` DISABLE KEYS */;
INSERT INTO `universe_order_element` VALUES (1,1,1,1,NULL),(2,2,7,1,NULL),(3,3,5,1,NULL),(4,4,6,1,NULL),(5,5,8,1,NULL),(6,6,1,1,NULL),(7,7,4,1,NULL),(8,8,2,1,NULL),(9,9,3,1,NULL),(10,10,1,1,NULL),(11,11,5,1,NULL);
/*!40000 ALTER TABLE `universe_order_element` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_order_ticket`
--

DROP TABLE IF EXISTS `universe_order_ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_order_ticket` (
  `id` int NOT NULL AUTO_INCREMENT,
  `orderelement_id` int DEFAULT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_611F4909EE04F0C1` (`orderelement_id`),
  CONSTRAINT `FK_611F4909EE04F0C1` FOREIGN KEY (`orderelement_id`) REFERENCES `universe_order_element` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_order_ticket`
--

LOCK TABLES `universe_order_ticket` WRITE;
/*!40000 ALTER TABLE `universe_order_ticket` DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_order_ticket` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_organizer`
--

DROP TABLE IF EXISTS `universe_organizer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_organizer` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `website` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `facebook` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tiktok` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `twitter` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `instagram` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `linkedin` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `youtubeurl` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `category` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `logo_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `logo_size` int DEFAULT NULL,
  `logo_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `logo_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `logo_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `cover_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cover_size` int DEFAULT NULL,
  `cover_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cover_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cover_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `views` int NOT NULL,
  `isathletics` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_2F97E190989D9B62` (`slug`),
  KEY `IDX_2F97E190A76ED395` (`user_id`),
  CONSTRAINT `FK_2F97E190A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_organizer`
--

LOCK TABLES `universe_organizer` WRITE;
/*!40000 ALTER TABLE `universe_organizer` DISABLE KEYS */;
INSERT INTO `universe_organizer` VALUES (3,2,'cyber-club','Cyber Club','<p>Cyber Club at Deree College is a dynamic hub for tech enthusiasts, fostering a vibrant community of students passionate about all things IT. Our club is dedicated to exploring the latest trends in cybersecurity, programming, and emerging technologies. Through engaging workshops, collaborative projects, and knowledge-sharing sessions, Cyber Club provides a platform for students to enhance their skills, stay informed about industry developments, and build lasting connections with like-minded peers. Join us on the forefront of technological innovation as we navigate the exciting world of cybersecurity and IT at Deree College\'s Cyber Club!<br></p>',NULL,'akourtisdev@gmail.com',NULL,NULL,NULL,NULL,'https://www.instagram.com/deree_cyber_club/',NULL,NULL,'Club','65bfe013563aa999767631.png',29832,'image/png','New Cyber Club Logo (512 × 512 px).png','512,512','65bfe187c6f91540383001.jpg',136572,'image/jpeg','Always-on-Top-Linux.jpg','1680,840','2024-01-31 20:40:04','2024-02-12 20:44:32',NULL,0,0),(4,8,'businessweek','BusinessWeek','Business Week, is an annual forum organized by the Academic Societies of the School of Business of the American College of Greece. #DereeBW2019 #BW2019',NULL,'businessweek1231@acg.edu',NULL,NULL,NULL,NULL,'https://www.instagram.com/dereebusinessweek/',NULL,NULL,'Office','65c3f553275bc533935795.jpg',33953,'image/jpeg','333763723_3471276656528346_5235025027818724675_n.jpg','750,750','65c3f55327959652213667.jpg',57756,'image/jpeg','333249594_625112459448987_6859691357429921630_n.jpg','1200,600','2024-02-07 23:22:42','2024-02-09 04:32:57',NULL,0,0),(5,9,'sustainability-acg','Sustainability ACG','<p>sdfadff</p>',NULL,'sustainability2131@acg.edu',NULL,NULL,NULL,NULL,'https://www.instagram.com/sustainableacg/',NULL,NULL,'Office','65c7bf9703c26029825758.jpg',6690,'image/jpeg','images.jpg','225,225','65c7bf9703e37789172964.jpg',266758,'image/jpeg','ACG-Center2341-of-Excellence-in-Sustainability-featured-image.jpg','1200,621','2024-02-10 20:21:55','2024-02-10 20:25:27',NULL,0,0);
/*!40000 ALTER TABLE `universe_organizer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_organizer_category`
--

DROP TABLE IF EXISTS `universe_organizer_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_organizer_category` (
  `Organizer_id` int NOT NULL,
  `Category_Id` int NOT NULL,
  PRIMARY KEY (`Organizer_id`,`Category_Id`),
  KEY `IDX_71F6C4319F5D9622` (`Organizer_id`),
  KEY `IDX_71F6C43115E3697` (`Category_Id`),
  CONSTRAINT `FK_71F6C43115E3697` FOREIGN KEY (`Category_Id`) REFERENCES `universe_category` (`id`),
  CONSTRAINT `FK_71F6C4319F5D9622` FOREIGN KEY (`Organizer_id`) REFERENCES `universe_organizer` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_organizer_category`
--

LOCK TABLES `universe_organizer_category` WRITE;
/*!40000 ALTER TABLE `universe_organizer_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_organizer_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_page`
--

DROP TABLE IF EXISTS `universe_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_page` (
  `id` int NOT NULL AUTO_INCREMENT,
  `content` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_4B375C1E989D9B62` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_page`
--

LOCK TABLES `universe_page` WRITE;
/*!40000 ALTER TABLE `universe_page` DISABLE KEYS */;
INSERT INTO `universe_page` VALUES (1,'<p style=\"border:0px solid rgb(217,217,227);margin-right:0px;margin-bottom:1.25em;margin-left:0px;\"><span style=\"border:0px solid rgb(217,217,227);\">UniVerse Terms of Service</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">These Terms of Service (\"Terms\") govern your use of UniVerse\'s services and website (collectively referred to as the \"Service\"). By accessing or using the Service, you agree to be bound by these Terms. If you disagree with any part of the terms, then you may not access the Service.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">1. Acceptance of Terms</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">By using the Service, you acknowledge that you have read, understood, and agree to be bound by these Terms. UniVerse reserves the right to update, change, or replace any part of these Terms by posting updates or changes on its website. Your continued use of the Service after such changes constitutes your acceptance of the new Terms.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">2. Use of the Service</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">a. Eligibility: You must be at least 18 years old to use the Service. By using the Service, you represent and warrant that you are at least 18 years old.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">b. Account: In order to access certain features of the Service, you may be required to create an account. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">c. Prohibited Activities: You agree not to engage in any activity that may interfere with or disrupt the Service or servers or networks connected to the Service. Prohibited activities include, but are not limited to, hacking, spamming, or transmitting any viruses or malware.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">3. Content</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">a. User Content: You retain ownership of any content you submit or upload to the Service (\"User Content\"). By submitting User Content, you grant UniVerse a worldwide, non-exclusive, royalty-free license to use, reproduce, modify, adapt, publish, translate, distribute, and display such User Content in connection with the Service.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">b. Intellectual Property: All content included on the Service, such as text, graphics, logos, images, and software, is the property of UniVerse or its licensors and is protected by copyright and other intellectual property laws.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">4. Privacy</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">UniVerse\'s Privacy Policy governs the collection, use, and disclosure of your personal information. By using the Service, you consent to the collection and use of your personal information as outlined in the Privacy Policy.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">5. Disclaimer of Warranties</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">The Service is provided on an \"as is\" and \"as available\" basis. UniVerse makes no warranties or representations about the accuracy or completeness of the content provided through the Service. Your use of the Service is at your own risk.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">6. Limitation of Liability</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">In no event shall UniVerse be liable for any direct, indirect, incidental, special, or consequential damages arising out of or in any way connected with your use of the Service.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">7. Indemnification</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">You agree to indemnify, defend, and hold harmless UniVerse, its affiliates, and their respective officers, directors, employees, and agents from any and all claims, liabilities, damages, losses, costs, expenses, or fees (including reasonable attorneys\' fees) arising out of or in connection with your use of the Service or violation of these Terms.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">8. Governing Law</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">These Terms shall be governed by and construed in accordance with the laws of [Your Jurisdiction], without regard to its conflict of law provisions.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">9. Changes to Terms</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">UniVerse reserves the right to modify or replace these Terms at any time. If a revision is material, UniVerse will provide at least 30 days\' notice prior to any new terms taking effect. What constitutes a material change will be determined at UniVerse\'s sole discretion.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">10. Contact Us</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">If you have any questions about these Terms, please contact us at universeaug@gmail.com</p><hr style=\"border-right-style:solid;border-bottom-style:solid;border-left-style:solid;margin:3em 0px;\" /><p style=\"border:0px solid rgb(217,217,227);margin-right:0px;margin-bottom:0px;margin-left:0px;\">Please note that this is a sample Terms of Service document and should be reviewed by a legal professional to ensure it complies with all relevant laws and regulations in your jurisdiction and accurately reflects the policies and practices of UniVerse.</p>','Terms of service','terms-of-service','2024-02-07 04:06:30'),(2,'<p style=\"border:0px solid rgb(217,217,227);margin-right:0px;margin-bottom:1.25em;margin-left:0px;\"><span style=\"border:0px solid rgb(217,217,227);\">UniVerse Privacy Policy</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">This Privacy Policy describes how UniVerse (\"we,\" \"us,\" or \"our\") collects, uses, and discloses your personal information when you use our website and services (collectively, the \"Service\").</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">1. Information We Collect</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">a. Information You Provide: When you use the Service, you may provide us with certain personal information, such as your name, email address, and any other information you choose to provide.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">b. Automatically Collected Information: We may also collect certain information automatically when you use the Service, such as your IP address, browser type, device identifiers, and usage data.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">2. Use of Information</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">We may use the information we collect for various purposes, including:</p><ul style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;padding:0px;\"><li style=\"border:0px solid rgb(217,217,227);margin:0px;padding-left:0.375em;min-height:28px;\">Providing and maintaining the Service;</li><li style=\"border:0px solid rgb(217,217,227);margin:0px;padding-left:0.375em;min-height:28px;\">Personalizing your experience and delivering targeted content;</li><li style=\"border:0px solid rgb(217,217,227);margin:0px;padding-left:0.375em;min-height:28px;\">Analyzing usage of the Service and improving our offerings;</li><li style=\"border:0px solid rgb(217,217,227);margin:0px;padding-left:0.375em;min-height:28px;\">Communicating with you, including responding to inquiries and providing customer support;</li><li style=\"border:0px solid rgb(217,217,227);margin:0px;padding-left:0.375em;min-height:28px;\">Detecting, preventing, and addressing technical issues or security breaches; and</li><li style=\"border:0px solid rgb(217,217,227);margin:0px;padding-left:0.375em;min-height:28px;\">Complying with legal obligations.</li></ul><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">3. Sharing of Information</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">We may share your personal information with third parties in the following circumstances:</p><ul style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;padding:0px;\"><li style=\"border:0px solid rgb(217,217,227);margin:0px;padding-left:0.375em;min-height:28px;\">With service providers who assist us in providing the Service;</li><li style=\"border:0px solid rgb(217,217,227);margin:0px;padding-left:0.375em;min-height:28px;\">With affiliates and partners for marketing and promotional purposes, with your consent;</li><li style=\"border:0px solid rgb(217,217,227);margin:0px;padding-left:0.375em;min-height:28px;\">In response to a legal request or to comply with applicable laws or regulations;</li><li style=\"border:0px solid rgb(217,217,227);margin:0px;padding-left:0.375em;min-height:28px;\">To protect the rights, property, or safety of UniVerse, our users, or others; or</li><li style=\"border:0px solid rgb(217,217,227);margin:0px;padding-left:0.375em;min-height:28px;\">With your consent or at your direction.</li></ul><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">4. Cookies and Similar Technologies</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">We may use cookies and similar technologies to collect information about your use of the Service and improve its functionality. You can set your browser to refuse all or some browser cookies, or to alert you when cookies are being sent. However, if you disable or refuse cookies, some parts of the Service may not function properly.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">5. Data Security</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">We take reasonable measures to protect the security of your personal information and prevent unauthorized access, disclosure, alteration, or destruction. However, no method of transmission over the internet or electronic storage is completely secure, and we cannot guarantee absolute security.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">6. Your Choices</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">You may opt-out of receiving promotional communications from us by following the unsubscribe instructions provided in such communications. Please note that even if you opt-out, we may still send you non-promotional communications related to your use of the Service.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">7. Children\'s Privacy</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">The Service is not directed to children under the age of 13, and we do not knowingly collect personal information from children under 13. If we learn that we have collected personal information from a child under 13, we will take appropriate steps to delete such information.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">8. Changes to this Policy</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">We may update this Privacy Policy from time to time by posting a new version on our website. You are advised to review this Privacy Policy periodically for any changes.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">9. Contact Us</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">If you have any questions about this Privacy Policy, please contact us at <a style=\"border:0px solid rgb(217,217,227);\">universeaug@gmail.com</a>.</p><hr style=\"border-right-style:solid;border-bottom-style:solid;border-left-style:solid;margin:3em 0px;\" /><p style=\"border:0px solid rgb(217,217,227);margin-right:0px;margin-bottom:0px;margin-left:0px;\">This is a sample Privacy Policy and should be reviewed by a legal professional to ensure it complies with all relevant laws and regulations in your jurisdiction and accurately reflects the policies and practices of UniVerse.</p>','Privacy policy','privacy-policy','2024-02-07 04:07:01'),(3,'<p style=\"border:0px solid rgb(217,217,227);margin-right:0px;margin-bottom:1.25em;margin-left:0px;\"><span style=\"border:0px solid rgb(217,217,227);\">UniVerse Cookie Policy</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">This Cookie Policy explains how UniVerse (\"we,\" \"us,\" or \"our\") uses cookies and similar tracking technologies when you visit our website (the \"Service\").</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">1. What are Cookies</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">Cookies are small text files that are stored on your device (computer, smartphone, or tablet) when you visit a website. They are widely used to make websites work more efficiently and to provide information to the website owners.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">2. How We Use Cookies</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">We use cookies for the following purposes:</p><ul style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;padding:0px;\"><li style=\"border:0px solid rgb(217,217,227);margin:0px;padding-left:0.375em;min-height:28px;\"><p style=\"border:0px solid rgb(217,217,227);margin-right:0px;margin-bottom:0px;margin-left:0px;\"><span style=\"border:0px solid rgb(217,217,227);\">Essential Cookies</span>: These cookies are necessary for the operation of our website. They enable core functionalities such as page navigation and access to secure areas of the website.</p></li><li style=\"border:0px solid rgb(217,217,227);margin:0px;padding-left:0.375em;min-height:28px;\"><p style=\"border:0px solid rgb(217,217,227);margin-right:0px;margin-bottom:0px;margin-left:0px;\"><span style=\"border:0px solid rgb(217,217,227);\">Analytical/Performance Cookies</span>: These cookies allow us to recognize and count the number of visitors and to see how visitors move around our website when they are using it. This helps us to improve the way our website works, for example, by ensuring that users can easily find what they are looking for.</p></li><li style=\"border:0px solid rgb(217,217,227);margin:0px;padding-left:0.375em;min-height:28px;\"><p style=\"border:0px solid rgb(217,217,227);margin-right:0px;margin-bottom:0px;margin-left:0px;\"><span style=\"border:0px solid rgb(217,217,227);\">Functionality Cookies</span>: These cookies are used to recognize you when you return to our website. This enables us to personalize our content for you, greet you by name, and remember your preferences (such as your choice of language or region).</p></li><li style=\"border:0px solid rgb(217,217,227);margin:0px;padding-left:0.375em;min-height:28px;\"><p style=\"border:0px solid rgb(217,217,227);margin-right:0px;margin-bottom:0px;margin-left:0px;\"><span style=\"border:0px solid rgb(217,217,227);\">Advertising/Targeting Cookies</span>: These cookies are used to deliver advertisements that are more relevant to you and your interests. They may be used to track the effectiveness of advertising campaigns or to limit the number of times you see an advertisement.</p></li></ul><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">3. Third-Party Cookies</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">We may also use third-party cookies, such as Google Analytics, to analyze the use of our website and to generate statistical reports on website activity. These cookies are provided by third-party service providers and may collect information about your browsing habits across different websites.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">4. Managing Cookies</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">You can control and manage cookies in various ways, including by adjusting your browser settings. Please note that if you choose to block cookies, you may not be able to access certain features or functionality of our website.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">To learn more about cookies and how to manage them, you can visit <a href=\"http://www.allaboutcookies.org/\" style=\"border:0px solid rgb(217,217,227);\">www.allaboutcookies.org</a>.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">5. Changes to this Policy</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">We may update this Cookie Policy from time to time by posting a new version on our website. You are advised to review this Cookie Policy periodically for any changes.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">6. Contact Us</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">If you have any questions about this Cookie Policy, please contact us at <a style=\"border:0px solid rgb(217,217,227);\">universeaug@gmail.com</a>.</p>','Cookie policy','cookie-policy','2024-02-07 04:07:30'),(4,'<p style=\"border:0px solid rgb(217,217,227);margin-right:0px;margin-bottom:1.25em;margin-left:0px;\"><span style=\"border:0px solid rgb(217,217,227);\">UniVerse GDPR Privacy Policy</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">This Privacy Policy describes how UniVerse (\"we,\" \"us,\" or \"our\") collects, uses, and discloses your personal information when you use our website and services (collectively, the \"Service\"). This Policy is designed to comply with the General Data Protection Regulation (GDPR) and other applicable data protection laws.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">1. Information We Collect</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">a. <span style=\"border:0px solid rgb(217,217,227);\">Legal Basis for Processing</span>: We collect and process personal information about you only where we have a legal basis for doing so under applicable data protection law. Such legal basis includes obtaining your consent, performing our contractual obligations, complying with legal obligations, and pursuing our legitimate interests.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">b. <span style=\"border:0px solid rgb(217,217,227);\">Types of Information</span>: We may collect various types of personal information, including your name, email address, IP address, and usage data, as described in our Privacy Policy.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">2. Purpose of Processing</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">We collect and process your personal information for the following purposes:</p><ul style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;padding:0px;\"><li style=\"border:0px solid rgb(217,217,227);margin:0px;padding-left:0.375em;min-height:28px;\">Providing and maintaining the Service;</li><li style=\"border:0px solid rgb(217,217,227);margin:0px;padding-left:0.375em;min-height:28px;\">Personalizing your experience and delivering targeted content;</li><li style=\"border:0px solid rgb(217,217,227);margin:0px;padding-left:0.375em;min-height:28px;\">Analyzing usage of the Service and improving our offerings;</li><li style=\"border:0px solid rgb(217,217,227);margin:0px;padding-left:0.375em;min-height:28px;\">Communicating with you, including responding to inquiries and providing customer support;</li><li style=\"border:0px solid rgb(217,217,227);margin:0px;padding-left:0.375em;min-height:28px;\">Detecting, preventing, and addressing technical issues or security breaches; and</li><li style=\"border:0px solid rgb(217,217,227);margin:0px;padding-left:0.375em;min-height:28px;\">Complying with legal obligations.</li></ul><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">3. Data Retention</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">We will retain your personal information only for as long as necessary to fulfill the purposes outlined in this Privacy Policy unless a longer retention period is required or permitted by law.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">4. Data Subject Rights</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">Under the GDPR, you have certain rights regarding your personal information, including the right to access, rectify, delete, or restrict processing of your personal information. You also have the right to data portability and the right to object to processing of your personal information.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">5. International Data Transfers</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">Your personal information may be transferred to and processed in countries other than your own. We will take appropriate safeguards to ensure that your personal information is adequately protected in accordance with applicable data protection laws.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">6. Data Security</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">We take reasonable measures to protect the security of your personal information and prevent unauthorized access, disclosure, alteration, or destruction.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">7. Contact Us</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">If you have any questions about this GDPR Privacy Policy or wish to exercise your data subject rights, please contact us at <a style=\"border:0px solid rgb(217,217,227);\">universeaug@gmail.com</a>.</p><hr style=\"border-right-style:solid;border-bottom-style:solid;border-left-style:solid;margin:3em 0px;\" /><p style=\"border:0px solid rgb(217,217,227);margin-right:0px;margin-bottom:0px;margin-left:0px;\">This GDPR Privacy Policy is intended to provide transparency about our data processing activities and your rights under the GDPR. It should be reviewed by a legal professional to ensure compliance with applicable laws and regulations.</p>','GDPR compliance','gdpr-compliance','2024-02-07 04:08:02'),(6,'<p style=\"border:0px solid rgb(217,217,227);margin-right:0px;margin-bottom:1.25em;margin-left:0px;\"><span style=\"border:0px solid rgb(217,217,227);\">About Us</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">Welcome to UniVerse, where we are passionate about creating innovative solutions for a connected world. Founded in [year], UniVerse is dedicated to pushing the boundaries of technology and providing exceptional services to our customers.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">Our Mission</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">At UniVerse, our mission is to empower individuals and businesses to thrive in the digital age. We believe in harnessing the power of technology to solve complex problems, drive growth, and enhance the way people live and work.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">What We Do</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">UniVerse offers a range of services tailored to meet the diverse needs of our clients. From web development and cybersecurity to data analytics and cloud solutions, we provide comprehensive services to help you achieve your goals.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">Our Team</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">Our team consists of skilled professionals with expertise in various fields, including software development, cybersecurity, data science, and project management. We are committed to delivering high-quality solutions and exceeding our clients\' expectations.</p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">Why Choose UniVerse?</span></p><ul style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;padding:0px;\"><li style=\"border:0px solid rgb(217,217,227);margin:0px;padding-left:0.375em;min-height:28px;\">Expertise: Our team consists of experienced professionals with a proven track record of success in their respective fields.</li><li style=\"border:0px solid rgb(217,217,227);margin:0px;padding-left:0.375em;min-height:28px;\">Innovation: We are dedicated to staying at the forefront of technological advancements and implementing cutting-edge solutions.</li><li style=\"border:0px solid rgb(217,217,227);margin:0px;padding-left:0.375em;min-height:28px;\">Customer-Centric Approach: We prioritize customer satisfaction and strive to build long-lasting relationships based on trust and transparency.</li><li style=\"border:0px solid rgb(217,217,227);margin:0px;padding-left:0.375em;min-height:28px;\">Results-Driven: We are committed to delivering tangible results that drive growth and add value to your business.</li></ul><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\"><span style=\"border:0px solid rgb(217,217,227);\">Get in Touch</span></p><p style=\"border:0px solid rgb(217,217,227);margin:1.25em 0px;\">Whether you\'re looking to launch a new project, enhance your cybersecurity defenses, or leverage data analytics to drive business insights, UniVerse is here to help. Contact us today to learn more about our services and how we can assist you in achieving your goals.</p><div><br /></div>','About us','about-us','2024-02-07 04:08:30');
/*!40000 ALTER TABLE `universe_page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_reels`
--

DROP TABLE IF EXISTS `universe_reels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_reels` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `type` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `linkvideo` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tags` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `video_file_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `content` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `main_image` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_size` int DEFAULT NULL,
  `image_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `views` int DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_C6AE6E246FBC9426` (`tags`),
  UNIQUE KEY `UNIQ_C6AE6E24A76ED395` (`user_id`),
  CONSTRAINT `FK_C6AE6E24A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_reels`
--

LOCK TABLES `universe_reels` WRITE;
/*!40000 ALTER TABLE `universe_reels` DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_reels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_reels_likes`
--

DROP TABLE IF EXISTS `universe_reels_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_reels_likes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `reel_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_415BA829A76ED395` (`user_id`),
  UNIQUE KEY `UNIQ_415BA8297802C31` (`reel_id`),
  CONSTRAINT `FK_415BA8297802C31` FOREIGN KEY (`reel_id`) REFERENCES `universe_reels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_415BA829A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_reels_likes`
--

LOCK TABLES `universe_reels_likes` WRITE;
/*!40000 ALTER TABLE `universe_reels_likes` DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_reels_likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_review`
--

DROP TABLE IF EXISTS `universe_review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_review` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `rating` int NOT NULL,
  `headline` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `slug` varchar(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `details` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `visible` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_3D9D9182989D9B62` (`slug`),
  KEY `IDX_3D9D918271F7E88B` (`event_id`),
  KEY `IDX_3D9D9182A76ED395` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_review`
--

LOCK TABLES `universe_review` WRITE;
/*!40000 ALTER TABLE `universe_review` DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_review` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_settings`
--

DROP TABLE IF EXISTS `universe_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `key` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `value` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_settings`
--

LOCK TABLES `universe_settings` WRITE;
/*!40000 ALTER TABLE `universe_settings` DISABLE KEYS */;
INSERT INTO `universe_settings` VALUES (1,'currency_ccy','USD'),(2,'currency_symbol','$'),(3,'currency_position','right'),(4,'feed_name','Events RSS feed'),(6,'feed_description','Latest events'),(7,'feed_events_limit','100'),(8,'img_loader','assets/img/placeholders/img-404.jpg'),(9,'blog_comments_enabled','no'),(10,'facebook_app_id',''),(11,'disqus_subdomain',''),(12,'newsletter_enabled','no'),(13,'mailchimp_api_key',''),(14,'mailchimp_list_id',''),(15,'homepage_show_search_box','no'),(16,'homepage_events_number','12'),(17,'homepage_categories_number','8'),(18,'homepage_blogposts_number','8'),(19,'homepage_show_call_to_action','no'),(20,'show_terms_of_service_page','no'),(21,'terms_of_service_page_content','terms_of_service_page_content'),(22,'show_privacy_policy_page','yes'),(23,'privacy_policy_page_content','privacy_policy_page_content'),(24,'show_cookie_policy_page','yes'),(25,'cookie_policy_page_content','cookie_policy_page_content'),(26,'show_gdpr_compliance_page','yes'),(27,'gdpr_compliance_page_content','gdpr_compliance_page_content'),(28,'terms_of_service_page_slug','terms-of-service'),(29,'privacy_policy_page_slug','terms-of-service'),(30,'cookie_policy_page_slug','terms-of-service'),(31,'gdpr_compliance_page_slug','terms-of-service'),(32,'ticket_fee_online','0.00'),(33,'ticket_fee_pos','0.00'),(34,'checkout_timeleft','1800'),(35,'organizer_payout_paypal_enabled','yes'),(36,'organizer_payout_stripe_enabled','yes'),(37,'online_ticket_price_percentage_cut','0'),(38,'pos_ticket_price_percentage_cut','0'),(39,'blog_posts_per_page','9'),(40,'events_per_page','24'),(41,'show_map_button','no'),(42,'show_calendar_button','yes'),(43,'show_rss_feed_button','yes'),(44,'show_category_filter','yes'),(45,'show_location_filter','yes'),(46,'show_date_filter','yes'),(47,'show_ticket_price_filter','yes'),(48,'show_audience_filter','yes'),(49,'venue_comments_enabled','no'),(50,'show_tickets_left_on_cart_modal','yes'),(51,'website_name','Universe'),(52,'website_slug','Universe'),(54,'website_root_url','universeacg.xyz'),(55,'website_description_en','Say goodbye to media clutter with a one-stop seamless experience!'),(56,'website_keywords_en','deree, acg, universe'),(57,'contact_email','universeaug@gmail.com'),(58,'contact_phone','+306989442438'),(59,'contact_email','+123456789'),(60,'contact_address','Gravias 6, Aghia Paraskevi'),(61,'facebook_url','https://www.facebook.com/profile.php?id=100084289854344'),(62,'instagram_url','https://www.instagram.com/acguniverse/'),(63,'youtube_url','https://www.youtube.com/channel/UCsKRxeDrt98BoHCz247WQwg'),(64,'twitter_url','https://twitter.com/acguniverse'),(65,'primary_color','#2E7599'),(66,'no_reply_email','no-reply@universeacg.xyz'),(67,'show_back_to_top_button','yes'),(68,'show_cookie_policy_bar','yes'),(69,'custom_css',''),(70,'google_analytics_code','G-08KD17D9LL'),(71,'website_description_fr','Gestion d\'événements et vente de billets'),(74,'website_description_ar','إدارة الأحداث ومبيعات التذاكر'),(75,'website_keywords_fr','organiser mon événement, billets en ligne, acheter des billets'),(78,'website_keywords_ar','تنظيم الحدث الخاص بي ، التذاكر عبر الإنترنت ، شراء التذاكر'),(79,'mail_server_transport','gmail'),(80,'mail_server_host','smtp.gmail.com'),(81,'mail_server_port','465'),(82,'mail_server_encryption','tls'),(83,'mail_server_auth_mode',NULL),(84,'mail_server_username','universeaug%40gmail.com'),(85,'mail_server_password','yaxqvholtlpunudv'),(86,'google_recaptcha_enabled','no'),(87,'google_recaptcha_site_key','113445586031-s85u7f3pseg8r7ro7hg07tqmrl62l2nr.apps.googleusercontent.com'),(88,'google_recaptcha_secret_key','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'),(89,'social_login_facebook_enabled','no'),(90,'social_login_facebook_id',''),(91,'social_login_facebook_secret',''),(92,'social_login_google_id',''),(93,'social_login_google_secret',''),(94,'social_login_google_enabled','no'),(95,'app_environment','prod'),(96,'maintenance_mode','0'),(97,'maintenance_mode_custom_message',''),(98,'app_theme','orange'),(99,'app_layout','container'),(100,'website_url','https://universeacg.xyz'),(101,'website_description_es','Gestión de eventos y venta de entradas'),(102,'website_keywords_es','organizar mi evento, entradas online, comprar entradas');
/*!40000 ALTER TABLE `universe_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_thread`
--

DROP TABLE IF EXISTS `universe_thread`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_thread` (
  `id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `permalink` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `is_commentable` tinyint(1) NOT NULL,
  `num_comments` int NOT NULL,
  `last_comment_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_thread`
--

LOCK TABLES `universe_thread` WRITE;
/*!40000 ALTER TABLE `universe_thread` DISABLE KEYS */;
INSERT INTO `universe_thread` VALUES ('capture-the-flag-ctf-competition-i-b48189a1a7e1df5f511c','https://universeacg.xyz/event/capture-the-flag-ctf-competition-i-b48189a1a7e1df5f511c',1,0,NULL),('capture-the-flag-ctf-competition-i-bbe2cd191427f02226d3','https://universeacg.xyz/event/capture-the-flag-ctf-competition-i-bbe2cd191427f02226d3',1,2,'2024-03-28 19:39:30'),('capture-the-flag-ctf-competition-i-dac7e7808777c626ea10','https://universe.alexandroskourtis.me/event/capture-the-flag-ctf-competition-i-dac7e7808777c626ea10',1,0,NULL),('code-amp-coffee-meetup-27cd91fecd0e49090ab4','https://universe.alexandroskourtis.me/event/code-amp-coffee-meetup-27cd91fecd0e49090ab4',1,0,NULL),('code-amp-coffee-meetup-e6a461737a2c70f4a597','https://universeacg.xyz/event/code-amp-coffee-meetup-e6a461737a2c70f4a597',1,0,NULL),('day-1-business-week-f9791b83d01db119d7de','https://universeacg.xyz/event/day-1-business-week-f9791b83d01db119d7de',1,0,NULL),('dsfadsfsfasfsfasfs-86daae9502c975040f8d','https://universeacg.xyz/event/dsfadsfsfasfsfasfs-86daae9502c975040f8d',1,0,NULL),('dsfadsfsfasfsfasfs-ce124bf3b20899178ec6','https://universe.alexandroskourtis.me/event/dsfadsfsfasfsfasfs-ce124bf3b20899178ec6',1,0,NULL),('new-year-new-resolutions','https://universeacg.xyz/blog-article/new-year-new-resolutions',1,0,NULL),('tech-talk-series-exploring-blockchain-357b28cb02b7dbc83e77','https://universe.alexandroskourtis.me/event/tech-talk-series-exploring-blockchain-357b28cb02b7dbc83e77',1,0,NULL),('tech-talk-series-exploring-blockchain-e258d3ef51e519d1297d','https://universe.alexandroskourtis.me/event/tech-talk-series-exploring-blockchain-e258d3ef51e519d1297d',1,0,NULL),('tech-talk-series-exploring-blockchain-e7a2ca33c7064f6e8f84','https://universeacg.xyz/event/tech-talk-series-exploring-blockchain-e7a2ca33c7064f6e8f84',1,0,NULL),('tuesday-march-15-day-1-business-week-9b7ead3255a419f416ff','https://universeacg.xyz/event/tuesday-march-15-day-1-business-week-9b7ead3255a419f416ff',1,1,'2024-02-08 06:44:30');
/*!40000 ALTER TABLE `universe_thread` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_ticket_reservation`
--

DROP TABLE IF EXISTS `universe_ticket_reservation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_ticket_reservation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `eventticket_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `orderelement_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BB13D979182CEB62` (`eventticket_id`),
  KEY `IDX_BB13D979A76ED395` (`user_id`),
  KEY `IDX_BB13D979EE04F0C1` (`orderelement_id`),
  CONSTRAINT `FK_BB13D979182CEB62` FOREIGN KEY (`eventticket_id`) REFERENCES `universe_event_date_ticket` (`id`),
  CONSTRAINT `FK_BB13D979A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`),
  CONSTRAINT `FK_BB13D979EE04F0C1` FOREIGN KEY (`orderelement_id`) REFERENCES `universe_order_element` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_ticket_reservation`
--

LOCK TABLES `universe_ticket_reservation` WRITE;
/*!40000 ALTER TABLE `universe_ticket_reservation` DISABLE KEYS */;
INSERT INTO `universe_ticket_reservation` VALUES (1,1,7,1,1,'2024-02-08 06:36:34',NULL),(2,7,7,2,1,'2024-02-08 06:37:16',NULL),(3,5,7,3,1,'2024-02-08 20:10:35',NULL),(4,6,7,4,1,'2024-02-09 04:32:59',NULL),(5,8,5,5,1,'2024-02-12 21:09:41',NULL),(6,1,5,6,1,'2024-02-12 21:09:44',NULL),(7,4,5,7,1,'2024-02-12 21:09:47',NULL),(8,2,5,8,1,'2024-02-12 21:09:52',NULL),(9,3,5,9,1,'2024-02-12 21:09:55',NULL),(10,1,3,10,1,'2024-02-13 05:04:14',NULL),(11,5,3,11,1,'2024-02-13 05:04:17',NULL);
/*!40000 ALTER TABLE `universe_ticket_reservation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_user`
--

DROP TABLE IF EXISTS `universe_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `organizer_id` int DEFAULT NULL,
  `isorganizeronhomepageslider_id` int DEFAULT NULL,
  `username` varchar(180) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `username_canonical` varchar(180) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(180) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `email_canonical` varchar(180) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `confirmation_token` varchar(180) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `points` double NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `studentid` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `bio` varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `website` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `facebook` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tiktok` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `twitter` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `instagram` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `linkedin` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `youtubeurl` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `isbusinessweek` tinyint(1) DEFAULT NULL,
  `nolevel` tinyint(1) DEFAULT NULL,
  `sendsms` tinyint(1) DEFAULT NULL,
  `specialuser` tinyint(1) DEFAULT NULL,
  `showfavs` tinyint(1) DEFAULT NULL,
  `showattendeed` tinyint(1) DEFAULT NULL,
  `showcomments` tinyint(1) DEFAULT NULL,
  `gender` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `firstname` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `lastname` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `street` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `street2` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `state` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `postalcode` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `avatar_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `avatar_size` int DEFAULT NULL,
  `avatar_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `avatar_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `avatar_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `api_key` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `logo_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `logo_size` int DEFAULT NULL,
  `logo_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `logo_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `logo_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `cover_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cover_size` int DEFAULT NULL,
  `cover_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cover_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cover_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `selectedimage` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `showfollowers` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_D2AE3C7792FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_D2AE3C77A0D96FBF` (`email_canonical`),
  UNIQUE KEY `UNIQ_D2AE3C77989D9B62` (`slug`),
  UNIQUE KEY `UNIQ_D2AE3C77C05FB297` (`confirmation_token`),
  UNIQUE KEY `UNIQ_D2AE3C77C912ED9D` (`api_key`),
  UNIQUE KEY `UNIQ_D2AE3C77D1BB795C` (`selectedimage`),
  UNIQUE KEY `UNIQ_D2AE3C77876C4DDA` (`organizer_id`),
  KEY `IDX_D2AE3C77F2709FF9` (`isorganizeronhomepageslider_id`),
  CONSTRAINT `FK_D2AE3C77876C4DDA` FOREIGN KEY (`organizer_id`) REFERENCES `universe_organizer` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_D2AE3C77F2709FF9` FOREIGN KEY (`isorganizeronhomepageslider_id`) REFERENCES `universe_homepage_hero_setting` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_user`
--

LOCK TABLES `universe_user` WRITE;
/*!40000 ALTER TABLE `universe_user` DISABLE KEYS */;
INSERT INTO `universe_user` VALUES (1,NULL,NULL,'administrator','administrator','administrator@yourdomain.com','administrator@yourdomain.com',1,'IyGzEV46y9UDotJJUd6wjZ1xgBI7/rCRT3ZSi6Xc.Ag','$2y$13$jxwSWnS/e/LFjSyzYVXKLOdlvtKljfxuFE25M6KB0MGo8jxhj4lXq','2024-05-29 00:06:29',NULL,NULL,'a:2:{i:0;s:16:\"ROLE_SUPER_ADMIN\";i:1;s:18:\"ROLE_ADMINISTRATOR\";}',0,'administrator','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0,0,0,0,NULL,'Administrator','Administrator',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-27 08:01:04','2024-05-29 00:06:29',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,3,NULL,'dcAcyberclub123?','dcacyberclub123?','dcAcyberclub123?@acg.edu','dcacyberclub123?@acg.edu',1,NULL,'$2y$13$OgSQugOrD4mpuzpsV3To/ux7p//aM.UMHF0fEkEqaYGxXbMGTAAbW',NULL,'7K2V5k-E_8BtZ9G7m-tLPmilBvjbV6Hxe0zY7Co472Q',NULL,'a:1:{i:0;s:14:\"ROLE_ORGANIZER\";}',0,'dcacyberclub123',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2024-01-31 20:40:04','2024-03-26 19:45:03',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,NULL,NULL,'agkouskos','agkouskos','agkouskos123@acg.edu','agkouskos123@acg.edu',1,NULL,'$2y$13$j7OSz5V22tHc792XoPMCXeqZ8YMdSdlWqm0hfiF3Md.kTbMEosFlm',NULL,'4uTQtN5NGdrC3dU-tb73-WAMFWYhzyilcWF6E_vqGs8',NULL,'a:1:{i:0;s:13:\"ROLE_ATTENDEE\";}',0,'agkouskos',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,1,0,0,0,NULL,'agkouskos','agkouskos',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2024-02-07 03:37:16','2024-02-07 21:29:12',NULL,NULL,'65c3da0882f20403402712.jpg',69661,'image/jpeg','416033759_18405869917063785_4518031736834832567_n.jpg','720,900',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,NULL,NULL,'eleftherioskardasisuniverse','eleftherioskardasisuniverse','eleftherioskardasisuniverse@acg.edu','eleftherioskardasisuniverse@acg.edu',1,NULL,'$2y$13$SW4/EB4.7oeBXmJloJQbD.f38eC1tj1Tc4UwuFH537tY0TOPk926e',NULL,'PVLGSCsPjXB6labYh1GzxnI3sy0KGqIZAElzAN7g31o',NULL,'a:1:{i:0;s:13:\"ROLE_ATTENDEE\";}',0,'eleftherioskardasisuniverse',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,1,0,0,0,NULL,'Eleftherios','kardasis',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2024-02-07 03:38:34','2024-02-07 21:23:01',NULL,NULL,'65c2e1a3916c9983971971.jpg',36468,'image/jpeg','1670085793676.jpg','497,497',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,NULL,NULL,'mariamitsouuniverse','mariamitsouuniverse','mariamitsouuniverse@acg.edu','mariamitsouuniverse@acg.edu',1,NULL,'$2y$13$Usc70dvLohkySnKA3.drAux4hT4gKYrgztzBVjMBSYC58gzZpLSXq',NULL,'lFmvUZkJXLzpdFAm1jzabMcUNFuoh4ES3OIOohFXLEY',NULL,'a:1:{i:0;s:13:\"ROLE_ATTENDEE\";}',0,'mariamitsouuniverse',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,1,0,1,0,NULL,'Maria','Mitsou',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2024-02-07 03:39:20','2024-03-14 12:46:26',NULL,NULL,'65c2e1cfa07ea216790468.jpg',20610,'image/jpeg','322735455_909834110461270_1264277115959685451_n.jpg','320,320',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(6,NULL,NULL,'alexandroskourtisuniverse','alexandroskourtisuniverse','alexandroskourtisuniverse@acg.edu','alexandroskourtisuniverse@acg.edu',1,NULL,'$2y$13$uW9cdPUvp95Tsn8M4.BS0.ixnb.7EGx.piHlImHq2zU6gPpSquJvu',NULL,'ugc7-YtUs5P99YT6d_RNDMAip_Vcaa3JZTeWcpi61lI',NULL,'a:1:{i:0;s:13:\"ROLE_ATTENDEE\";}',0,'alexandroskourtisuniverse',NULL,'Long story short, I love to write software : )','https://alexandroskourtis.me','+306989442438','https://www.facebook.com/alexandros.kourtis.581',NULL,'https://twitter.com/dreadpirate98','https://www.instagram.com/alexandros.kourtis.98','https://www.linkedin.com/in/alexandros-kourtis-3946831aa/',NULL,1,NULL,1,1,1,1,0,NULL,'Alexandros','Kourtis',NULL,NULL,NULL,NULL,NULL,'1998-04-22',NULL,NULL,NULL,NULL,NULL,'2024-02-07 03:40:06','2024-02-07 21:31:23',NULL,NULL,'65c3da3e688db594512034.png',512775,'image/png','new insta pic.png','800,800','65c3da8bc0345763444828.jpg',683144,'image/jpeg','0_7U4Qj-gSIV_gckS6.jpg','5456,3064',NULL,NULL),(7,NULL,NULL,'athinaioannidiuniverse','athinaioannidiuniverse','athinaioannidiuniverse@acg.edu','athinaioannidiuniverse@acg.edu',1,NULL,'$2y$13$sYB.8p58tctCUcmfscORme6kOFmJrhDD92sFQLBeZ1.s9MpWPJM/a','2024-03-10 20:38:41','KTtz8zOFhatYkNCfm5lP45OUPhORIe5bma8jRljyaHg',NULL,'a:1:{i:0;s:13:\"ROLE_ATTENDEE\";}',1,'athinaioannidiuniverse',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,1,0,0,0,NULL,'Athina','Ioannidi',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2024-02-07 03:40:57','2024-03-10 20:38:41',NULL,NULL,'65c2e1f8f14c8281561958.jpg',93886,'image/jpeg','18358680_1940200849339441_824599690989529725_o.jpg','1536,1024','65c45a031bbcc064036725.jpg',136905,'image/jpeg','FlowerFieldsinCarlsbadRanch_ChelseaLoren02-06289657457c4ff1a6e493574dd2e332.jpg','1500,1000',NULL,NULL),(8,4,NULL,'BusinessWeek','businessweek','businessweek1231@acg.edu','businessweek1231@acg.edu',1,NULL,'$2y$13$lGA3yXENDGIDvB6ZkosoIuTJG6z1aNgSdWnoLoPeOQDBW2r1Hd.Jq',NULL,'5xcF5t_JCyppC0VTfx7wUlVGc3oUeQAnrEbFCPXrtto',NULL,'a:1:{i:0;s:14:\"ROLE_ORGANIZER\";}',0,'businessweek',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,0,0,0,NULL,'Business','Week',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2024-02-07 23:22:42','2024-02-07 23:24:14',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,5,NULL,'sustainability2131','sustainability2131','sustainability2131@acg.edu','sustainability2131@acg.edu',1,NULL,'$2y$13$xlLeVVMAOg.U6Wp5nc1O..qYlBJbBfkyACnrpEznXy1X1qHT61vEC','2024-02-15 08:08:57','oFhLD1FDgpXGLsiFa6YlekJrVdb0CPTpOI3kI24TcO4',NULL,'a:1:{i:0;s:14:\"ROLE_ORGANIZER\";}',0,'sustainability2131',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2024-02-10 20:21:55','2024-03-28 19:40:34',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `universe_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_user_cv`
--

DROP TABLE IF EXISTS `universe_user_cv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_user_cv` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `file_size` int DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `file_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `file_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_7C34D5E5A76ED395` (`user_id`),
  CONSTRAINT `FK_7C34D5E5A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_user_cv`
--

LOCK TABLES `universe_user_cv` WRITE;
/*!40000 ALTER TABLE `universe_user_cv` DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_user_cv` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_user_doc`
--

DROP TABLE IF EXISTS `universe_user_doc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_user_doc` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `file_size` int DEFAULT NULL,
  `file_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `file_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `file_name1` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `file_size1` int DEFAULT NULL,
  `file_mime_type1` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `file_original_name1` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_48EA42D0A76ED395` (`user_id`),
  CONSTRAINT `FK_48EA42D0A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_user_doc`
--

LOCK TABLES `universe_user_doc` WRITE;
/*!40000 ALTER TABLE `universe_user_doc` DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_user_doc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_user_doc1`
--

DROP TABLE IF EXISTS `universe_user_doc1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_user_doc1` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `file_size` int DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `file_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `file_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_547D721A76ED395` (`user_id`),
  CONSTRAINT `FK_547D721A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_user_doc1`
--

LOCK TABLES `universe_user_doc1` WRITE;
/*!40000 ALTER TABLE `universe_user_doc1` DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_user_doc1` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_user_doc2`
--

DROP TABLE IF EXISTS `universe_user_doc2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_user_doc2` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `file_size` int DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `file_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `file_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_9C4E869BA76ED395` (`user_id`),
  CONSTRAINT `FK_9C4E869BA76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_user_doc2`
--

LOCK TABLES `universe_user_doc2` WRITE;
/*!40000 ALTER TABLE `universe_user_doc2` DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_user_doc2` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_venue`
--

DROP TABLE IF EXISTS `universe_venue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_venue` (
  `id` int NOT NULL AUTO_INCREMENT,
  `main_image` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `availability` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `hidden` tinyint(1) DEFAULT NULL,
  `title` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `contact_email` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `image_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_size` int DEFAULT NULL,
  `image_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_50AF3B4C989D9B62` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_venue`
--

LOCK TABLES `universe_venue` WRITE;
/*!40000 ALTER TABLE `universe_venue` DISABLE KEYS */;
INSERT INTO `universe_venue` VALUES (1,'ACG-Events-Hall-02-scaled.jpg','acg-events-hall',NULL,NULL,'ACG Events Hall','<p>Located at the library\'s upper level, ACG Events Hall was completely renovated and inaugurated in January 2020. It is equipped with state-of-the-art audiovisual technology and is ideal for conferences, panel discussions and lectures.<br></p>',NULL,'2024-02-10 19:13:37',NULL,534750,'image/jpeg','ACG-Events-Hall-02-scaled.jpg','2560,1707','2024-02-10 19:13:37',NULL),(2,'Conference-Room-02-scaled.jpg','conference-room',NULL,NULL,'Conference Room','<p>The John S. Bailey Library\'s conference room is located on the ground level. It has modern audiovisual capabilities and is quite isolated, making it the ideal setting for more intimate meetings or breakout sessions.<br></p>',NULL,'2024-02-10 19:14:14',NULL,452785,'image/jpeg','Conference-Room-02-scaled.jpg','2560,1707','2024-02-10 19:14:14',NULL),(3,'Irene-Bailey-Theater-02-scaled.jpg','irene-bailey-theater',NULL,NULL,'Irene Bailey Theater','<p>Inspired by ancient Greek amphitheaters, the Irene Bailey open air theater is a splendid work of art. Its extraordinary acoustics combined with a scenic panoramic view makes it the perfect venue for formal occasions, official ceremonies and musical or theatrical performances.<br></p>',NULL,'2024-02-10 19:14:23',NULL,546146,'image/jpeg','Irene-Bailey-Theater-02-scaled.jpg','2560,1707','2024-02-10 19:14:23',NULL),(4,'Irene-Bailey-Theater-02-scaled.jpg','irene-bailey-theater-plaza',NULL,NULL,'Irene Bailey Theater Plaza','<p>Adjacent to the Irene Bailey open air theater and surrounded by pine forest, the open air Plaza can host events of all sizes against the backdrop of northeast Attica\'s rolling hills.<br></p>',NULL,'2024-02-10 19:14:37',NULL,546146,'image/jpeg','Irene-Bailey-Theater-02-scaled.jpg','2560,1707','2024-02-10 19:14:37',NULL),(5,'6th-Level-Auditorium-02-scaled.jpg','6th-level-auditorium',NULL,NULL,'6th Level Auditorium','<p>Utilized for lectures and presentations, the 6th Level Auditorium is yet another addition to ACG´s modern schooling facilities and is fully equipped with audiovisual technology.<br></p>',NULL,'2024-02-10 19:14:43',NULL,490607,'image/jpeg','6th-Level-Auditorium-02-scaled.jpg','2560,1707','2024-02-10 19:14:43',NULL),(6,'7th-Level-Auditorium-02-scaled.jpg','7th-level-auditorium',NULL,NULL,'7th Level Auditorium','<p>One of several modern amphitheaters on campus, the 7th Level Auditorium has exquisite accoustics and is regularly used for musical performances, lectures and film screenings.<br></p>',NULL,'2024-02-10 19:14:50',NULL,481258,'image/jpeg','7th-Level-Auditorium-02-scaled.jpg','2560,1707','2024-02-10 19:14:50',NULL),(7,'Center-for-the-Arts-Auditorium-02-scaled.jpg','center-for-the-arts-auditorium',NULL,NULL,'Center for the Arts Auditorium','<p>Equipped with a smart projector and screen, soundproof panels and two monitors, the Arts Center Auditorium is an ideal location on campus for hosting small audience lectures and presentations.<br></p>',NULL,'2024-02-10 19:14:58',NULL,464714,'image/jpeg','Center-for-the-Arts-Auditorium-02-scaled.jpg','2560,1707','2024-02-10 19:14:58',NULL),(8,'Center-for-the-Arts-Gallery-02-scaled.jpg','center-for-the-arts-gallery',NULL,NULL,'Center for the Arts Gallery','<p>The Arts Center Gallery hosts various exhibitions and is perfectly situated, offering easy access on a daily basis to students and visitors alike.<br></p>',NULL,'2024-02-10 19:15:07',NULL,506653,'image/jpeg','Center-for-the-Arts-Gallery-02-scaled.jpg','2560,1524','2024-02-10 19:15:07',NULL),(9,'ACG-Art-Gallery-02-scaled.jpg','acg-art-gallery',NULL,NULL,'ACG Art Gallery','<p>A modern open plan space that can be divided by modular panels, the Art Gallery is located in the lower level of the John S. Bailey Library and can host exhibitions featuring a variety of artistic and curatorial perspectives.<br></p>',NULL,'2024-02-10 19:15:15',NULL,341219,'image/jpeg','ACG-Art-Gallery-02-scaled.jpg','2560,1707','2024-02-10 19:15:15',NULL),(10,'Deree-Faculty-Lounge-02-scaled.jpg','deree-faculty-lounge',NULL,NULL,'Deree Faculty Lounge','<p>Designed with wooden panelling and boasting wide-open vistas of the campus\'s front lawns, this magnificent polymorphic lounge is an elegant venue to host lectures, workshops and small receptions.<br></p>',NULL,'2024-02-10 19:15:22',NULL,580565,'image/jpeg','Deree-Faculty-Lounge-02-scaled.jpg','2560,1707','2024-02-10 19:15:22',NULL),(11,'Deree-Student-Lounge-02-scaled.jpg','deree-student-lounge',NULL,NULL,'Deree Student Lounge','<p>The Deree Student Lounge is a versatile space, furnished for comfort and equipped with a projector and other audiovisual technology. Located at the heart of the campus and adjacent to the dining hall, it serves as a student meeting point and a place for relaxation while it can also host receptions and other events.<br></p>',NULL,'2024-02-10 19:15:29',NULL,526490,'image/jpeg','Deree-Student-Lounge-02-scaled.jpg','2560,1707','2024-02-10 19:15:29',NULL),(12,'200-Level-Patio-02-scaled.jpg','200-level-patio',NULL,NULL,'200 Level Patio','<p>A spacious and idyllic area surrounded by expanses of trees and greenery that can host from conference cocktails to receptions.<br></p>',NULL,'2024-02-10 19:15:46',NULL,969573,'image/jpeg','200-Level-Patio-02-scaled.jpg','2560,1707','2024-02-10 19:15:46',NULL),(13,'Black-Box-Theater-02-scaled.jpg','black-box-theater',NULL,NULL,'Black Box Theater','<p>With a flexible layout and a multitude of possible set up arrangements, the Black Box Theater is the most adaptable performing arts venue on campus and the perfect place for theatrical performances.<br></p>',NULL,'2024-02-10 19:15:53',NULL,521129,'image/jpeg','Black-Box-Theater-02-scaled.jpg','2560,1707','2024-02-10 19:15:53',NULL),(14,'Pierce-Theater-02-scaled.jpg','pierce-theater',NULL,NULL,'Pierce Theater','<p>Pierce Theater is the venue of choice for exceptional and large-scale performances. Equipped with exquisite soundproof panels and multiple surround sound monitors, a high quality modern projector and special lighting, it is the single largest venue of The American College of Greece.<br></p>',NULL,'2024-02-10 19:16:10',NULL,577047,'image/jpeg','Pierce-Theater-02-scaled.jpg','2560,1707','2024-02-10 19:16:10',NULL),(15,'Pierce-Theater-Lobby-02-scaled.jpg','pierce-theater-lobby',NULL,NULL,'Pierce Theater Lobby','<p>The spacious Pierce Theater Lobby space can serve not only as a waiting area but also as a venue for exhibitions or a reception.<br></p>',NULL,'2024-02-10 19:16:17',NULL,330191,'image/jpeg','Pierce-Theater-Lobby-02-scaled.jpg','2560,1707','2024-02-10 19:16:17',NULL),(16,'Pierce-Theater-Atrium-02-scaled.jpg','pierce-theater-atrium',NULL,NULL,'Pierce Theater Atrium','<p>Adjacent to the Pierce Theater, the Atrium is a major outdoor venue, with flexible space and fit to host events of various sizes, such as outdoor exhibitions, festivals and sitting or standing receptions.<br></p>',NULL,'2024-02-10 19:16:25',NULL,887406,'image/jpeg','Pierce-Theater-Atrium-02-scaled.jpg','2560,1707','2024-02-10 19:16:25',NULL),(17,'Pierce-Amphitheater-02-scaled.jpg','pierce-amphitheater',NULL,NULL,'Pierce Amphitheater','<p>Completely renovated in 2014 and endowed with forefront sound and video equipment, the Pierce Amphitheater serves splendidly both as a classroom and as a lecture hall.<br></p>',NULL,'2024-02-10 19:16:33',NULL,426940,'image/jpeg','Pierce-Amphitheater-02-scaled.jpg','2560,1707','2024-02-10 19:16:33',NULL),(18,'Pierce-Faculty-Lounge-02-scaled.jpg','pierce-faculty-lounge',NULL,NULL,'Pierce Faculty Lounge','<p>Furbished with comfy couches and overseeing the beautiful outdoor patio, Pierce Faculty Lounge lies just accross Pierce Cafeteria and can be used to host small conferences, workshops or meetings.<br></p>',NULL,'2024-02-10 19:16:41',NULL,578247,'image/jpeg','Pierce-Faculty-Lounge-02-scaled.jpg','2560,1707','2024-02-10 19:16:41',NULL),(19,'Pierce-Cafeteria-02-scaled.jpg','pierce-cafeteria',NULL,NULL,'Pierce Cafeteria','<p>A casual and spacious lunch break venue for Pierce students, Pierce cafeteria is ideal for informal team-building workshops, coffee breaks or as an exhibition area.<br></p>',NULL,'2024-02-10 19:16:47',NULL,597760,'image/jpeg','Pierce-Cafeteria-02-scaled.jpg','2560,1707','2024-02-10 19:16:47',NULL),(20,'Alba-Amphitheater-02-scaled.jpg','alba-amphitheater',NULL,NULL,'Alba Amphitheater','<p>An elegant, completely renovated space equipped with technology to meet the demands of any event. Situated at the ground level of Alba Graduate Business School, in downtown Athens, the amphitheater has an adjacent lobby fit to host a lunch break or even a small exhibition. It is a short walking distance from the closest metro station, among other transportation means.<br></p>',NULL,'2024-02-10 19:16:55',NULL,423196,'image/jpeg','Alba-Amphitheater-02-scaled.jpg','2560,1707','2024-02-10 19:16:55',NULL),(22,'Chapel-02-scaled.jpg','chapel',NULL,NULL,'Chapel','<p>The Three Hierarchs Chapel is situated at the highest and most scenic part of ACG campus. Supported by generous donations, it was designed after Byzantine architecture by Stefanos Nomikos and adorned with frescoes by Yannis Karouzos. The chapel was consecrated by His Beatitude Christodoulos in 2002.<br></p>',NULL,'2024-02-10 19:17:08',NULL,698019,'image/jpeg','Chapel-02-scaled.jpg','2560,1707','2024-02-10 19:17:08',NULL),(23,'Chapel-Patio-02-scaled.jpg','chapel-patio',NULL,NULL,'Chapel Patio','<p>Surrounded by pine forest and offering spectacular views of Athens, the Chapel Patio is the ideal complement of the Three Hierarchs Chapel.<br></p>',NULL,'2024-02-10 19:17:17',NULL,674441,'image/jpeg','Chapel-Patio-02-scaled.jpg','2560,1707','2024-02-10 19:17:17',NULL),(24,'Deree-Gym-Basketball-02-scaled.jpg','deree-gym-basketball-level',NULL,NULL,'Deree Gym - Basketball Level','<p>Located at the east end of ACG campus, the Deree Gym is a multi-purpose facility built on three levels that offers two cross-courts and one game court, used for basketball, volleyball and futsal, a fully equipped fitness center, dance studios, a climbing wall as well as a sports and games lounge. The basketball level conveniently accommodates ceremonies and various events.<br></p>',NULL,'2024-02-10 19:17:32',NULL,649070,'image/jpeg','Deree-Gym-Basketball-02-scaled.jpg','2560,1707','2024-02-10 19:17:32',NULL),(25,'Pierce-Gym-02-scaled.jpg','pierce-gym',NULL,NULL,'Pierce Gym','<p>Pierce Gym is a two game courts indoor facility (basketball, volleyball, indoor soccer, badminton) available for athletic events.<br></p>',NULL,'2024-02-10 19:17:41',NULL,443727,'image/jpeg','Pierce-Gym-02-scaled.jpg','2560,1707','2024-02-10 19:17:41',NULL),(26,'Swimming-Pool-02-scaled.jpg','swimming-pool',NULL,NULL,'Swimming Pool','<p>A heated, Olympic size swimming pool offering breathtaking views of Hymettos mountain and the city of Athens.<br></p>',NULL,'2024-02-10 19:17:49',NULL,683316,'image/jpeg','Swimming-Pool-02-scaled.jpg','2560,1707','2024-02-10 19:17:49',NULL),(28,'TennisCourts-02-scaled.jpg','tennis-courts-1',NULL,NULL,'Tennis Courts','<p>The tennis facilities consist of four outdoor lighted hard surface courts as well as a full services area.<br></p>',NULL,'2024-02-10 19:17:57',NULL,411492,'image/jpeg','TennisCourts-02-scaled.jpg','2560,1707','2024-02-10 19:17:57',NULL),(29,'Track-and-Field-02-scaled.jpg','acg-soccer-and-track-complex',NULL,0,'ACG Soccer and Track complex','<p>An outdoor sports facility including a soccer field surfaced with synthetic turf, and an organic, environmentally friendly cork infill that creates playing characteristics similar to natural grass, and a 400-meter synthetic track with 6 lanes.<br></p>',NULL,'2024-02-10 19:18:06',NULL,664710,'image/jpeg','Track-and-Field-02-scaled.jpg','2560,1706','2024-02-10 19:18:06',NULL);
/*!40000 ALTER TABLE `universe_venue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_venue_amenities`
--

DROP TABLE IF EXISTS `universe_venue_amenities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_venue_amenities` (
  `venue_id` int NOT NULL,
  `amenity_id` int NOT NULL,
  PRIMARY KEY (`venue_id`,`amenity_id`),
  KEY `IDX_DEB650DC40A73EBA` (`venue_id`),
  KEY `IDX_DEB650DC9F9F1305` (`amenity_id`),
  CONSTRAINT `FK_DEB650DC40A73EBA` FOREIGN KEY (`venue_id`) REFERENCES `universe_venue` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_DEB650DC9F9F1305` FOREIGN KEY (`amenity_id`) REFERENCES `universe_amenity` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_venue_amenities`
--

LOCK TABLES `universe_venue_amenities` WRITE;
/*!40000 ALTER TABLE `universe_venue_amenities` DISABLE KEYS */;
INSERT INTO `universe_venue_amenities` VALUES (24,2);
/*!40000 ALTER TABLE `universe_venue_amenities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_venue_images`
--

DROP TABLE IF EXISTS `universe_venue_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_venue_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `venue_id` int DEFAULT NULL,
  `position` int DEFAULT NULL,
  `image_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_size` int DEFAULT NULL,
  `image_mime_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image_dimensions` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CD40B66F40A73EBA` (`venue_id`),
  CONSTRAINT `FK_CD40B66F40A73EBA` FOREIGN KEY (`venue_id`) REFERENCES `universe_venue` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_venue_images`
--

LOCK TABLES `universe_venue_images` WRITE;
/*!40000 ALTER TABLE `universe_venue_images` DISABLE KEYS */;
INSERT INTO `universe_venue_images` VALUES (6,29,0,'65c9665d63999399664104.jpg',301805,'image/jpeg','Track-and-Field-03.jpg',NULL,'2024-02-12 02:29:17'),(7,29,1,'65c9665d63e10522456573.jpg',152325,'image/jpeg','Track-and-Field-04.jpg',NULL,'2024-02-12 02:29:17'),(8,29,2,'65c9665d640a4981260890.jpg',226236,'image/jpeg','Track-and-Field-05.jpg',NULL,'2024-02-12 02:29:17'),(9,29,3,'65c9665d642ad350796982.jpg',219042,'image/jpeg','Track-and-Field-06.jpg',NULL,'2024-02-12 02:29:17'),(10,28,0,'65c9666ec574c102240116.jpg',317551,'image/jpeg','TennisCourts-03.jpg',NULL,'2024-02-12 02:29:34'),(11,28,1,'65c9666ec5afd813607185.jpg',543210,'image/jpeg','TennisCourts-04.jpg',NULL,'2024-02-12 02:29:34'),(12,28,2,'65c9666ec5d9b459833452.jpg',270949,'image/jpeg','TennisCourts-05.jpg',NULL,'2024-02-12 02:29:34'),(13,26,0,'65c967a66be88834689986.jpg',240350,'image/jpeg','Swimming-Pool-03.jpg',NULL,'2024-02-12 02:34:46'),(14,26,1,'65c967a66c22b297522967.jpg',270986,'image/jpeg','Swimming-Pool-04.jpg',NULL,'2024-02-12 02:34:46'),(15,26,2,'65c967a66c45b674487414.jpg',322293,'image/jpeg','Swimming-Pool-05.jpg',NULL,'2024-02-12 02:34:46'),(16,26,3,'65c967a66c6d6786267261.jpg',138720,'image/jpeg','Swimming-Pool-06.jpg',NULL,'2024-02-12 02:34:46'),(17,25,0,'65c967b430a86974914385.jpg',147050,'image/jpeg','Pierce-Gym-03.jpg',NULL,'2024-02-12 02:35:00'),(18,25,1,'65c967b430dd1145034986.jpg',138490,'image/jpeg','Pierce-Gym-04.jpg',NULL,'2024-02-12 02:35:00'),(19,25,2,'65c967b430fd5845385573.jpg',153988,'image/jpeg','Pierce-Gym-05.jpg',NULL,'2024-02-12 02:35:00'),(20,24,0,'65c967efbd034303841748.jpg',222476,'image/jpeg','Deree-Gym-Basketball-03.jpg',NULL,'2024-02-12 02:35:59'),(21,24,1,'65c967efbd58b184567677.jpg',228806,'image/jpeg','Deree-Gym-Basketball-04.jpg',NULL,'2024-02-12 02:35:59'),(22,24,2,'65c967efbd8ae910769293.jpg',238879,'image/jpeg','Deree-Gym-Basketball-05.jpg',NULL,'2024-02-12 02:35:59'),(23,24,3,'65c967efbdbdf044616308.jpg',243983,'image/jpeg','Deree-Gym-Basketball-06.jpg',NULL,'2024-02-12 02:35:59'),(24,24,4,'65c967efbdf0b541958656.jpg',269499,'image/jpeg','Deree-Gym-Basketball-07.jpg',NULL,'2024-02-12 02:35:59'),(25,24,5,'65c967efbe298602748946.jpg',225901,'image/jpeg','Deree-Gym-Basketball-08.jpg',NULL,'2024-02-12 02:35:59'),(26,24,6,'65c967efbe5d5406995220.jpg',366813,'image/jpeg','Deree-Gym-Basketball-09.jpg',NULL,'2024-02-12 02:35:59'),(27,23,0,'65c967fb7057c479717058.jpg',253360,'image/jpeg','Chapel-Patio-03.jpg',NULL,'2024-02-12 02:36:11'),(28,23,1,'65c967fb7092a415239617.jpg',230815,'image/jpeg','Chapel-Patio-04.jpg',NULL,'2024-02-12 02:36:11'),(29,23,2,'65c967fb70b93643382547.jpg',226630,'image/jpeg','Chapel-Patio-05.jpg',NULL,'2024-02-12 02:36:11'),(30,22,0,'65c9680842702253462591.jpg',278903,'image/jpeg','Chapel-03.jpg',NULL,'2024-02-12 02:36:24'),(31,22,1,'65c9680842b23370989879.jpg',292469,'image/jpeg','Chapel-04.jpg',NULL,'2024-02-12 02:36:24'),(32,22,2,'65c9680842d73357139262.jpg',291662,'image/jpeg','Chapel-05.jpg',NULL,'2024-02-12 02:36:24'),(33,19,0,'65c968427f887999131262.jpg',186302,'image/jpeg','Pierce-Cafeteria-03.jpg',NULL,'2024-02-12 02:37:22'),(34,19,1,'65c968427fbec574525717.jpg',199283,'image/jpeg','Pierce-Cafeteria-04.jpg',NULL,'2024-02-12 02:37:22'),(35,19,2,'65c968427fe4e891405400.jpg',88969,'image/jpeg','Pierce-Cafeteria-05.jpg',NULL,'2024-02-12 02:37:22'),(36,18,0,'65c96853ecbb5988406773.jpg',164286,'image/jpeg','Pierce-Faculty-Lounge-03.jpg',NULL,'2024-02-12 02:37:39'),(37,18,1,'65c96853ecf49047634440.jpg',150826,'image/jpeg','Pierce-Faculty-Lounge-04.jpg',NULL,'2024-02-12 02:37:39'),(38,18,2,'65c96853ed13c647503237.jpg',204255,'image/jpeg','Pierce-Faculty-Lounge-05.jpg',NULL,'2024-02-12 02:37:39'),(39,17,0,'65c9686103073727683994.jpg',141133,'image/jpeg','Pierce-Amphitheater-03.jpg',NULL,'2024-02-12 02:37:53'),(40,17,1,'65c9686103539228697787.jpg',174173,'image/jpeg','Pierce-Amphitheater-04.jpg',NULL,'2024-02-12 02:37:53'),(41,17,2,'65c968610383b194165202.jpg',180756,'image/jpeg','Pierce-Amphitheater-05.jpg',NULL,'2024-02-12 02:37:53'),(42,16,0,'65c9687909572553065622.jpg',209409,'image/jpeg','Pierce-Theater-Atrium-03.jpg',NULL,'2024-02-12 02:38:17'),(43,16,1,'65c9687909ae5858220268.jpg',301436,'image/jpeg','Pierce-Theater-Atrium-04.jpg',NULL,'2024-02-12 02:38:17'),(44,16,2,'65c9687909ed5371633754.jpg',296333,'image/jpeg','Pierce-Theater-Atrium-05.jpg',NULL,'2024-02-12 02:38:17'),(45,15,0,'65c9687fd57cc197906768.jpg',147239,'image/jpeg','Pierce-Theater-Lobby-03.jpg',NULL,'2024-02-12 02:38:23'),(46,15,1,'65c9687fd5c76977157552.jpg',128928,'image/jpeg','Pierce-Theater-Lobby-04.jpg',NULL,'2024-02-12 02:38:23'),(47,15,2,'65c9687fd5e91357885795.jpg',142633,'image/jpeg','Pierce-Theater-Lobby-05.jpg',NULL,'2024-02-12 02:38:23'),(48,14,0,'65c9689304b76619021392.jpg',204857,'image/jpeg','Pierce-Theater-03.jpg',NULL,'2024-02-12 02:38:43'),(49,14,1,'65c9689305042963571000.jpg',201828,'image/jpeg','Pierce-Theater-04.jpg',NULL,'2024-02-12 02:38:43'),(50,14,2,'65c968930533f318522761.jpg',198416,'image/jpeg','Pierce-Theater-05.jpg',NULL,'2024-02-12 02:38:43'),(51,13,0,'65c968a17556d467220681.jpg',151312,'image/jpeg','Black-Box-Theater-03.jpg',NULL,'2024-02-12 02:38:57'),(52,13,1,'65c968a175969380842297.jpg',153461,'image/jpeg','Black-Box-Theater-04.jpg',NULL,'2024-02-12 02:38:57'),(53,13,2,'65c968a175bd5480021504.jpg',165974,'image/jpeg','Black-Box-Theater-05.jpg',NULL,'2024-02-12 02:38:57'),(54,12,0,'65c968b972b2f029167547.jpg',215402,'image/jpeg','200-Level-Patio-03.jpg',NULL,'2024-02-12 02:39:21'),(55,12,1,'65c968b972e8b690877180.jpg',436645,'image/jpeg','200-Level-Patio-04.jpg',NULL,'2024-02-12 02:39:21'),(56,12,2,'65c968b973115795736362.jpg',278102,'image/jpeg','200-Level-Patio-05.jpg',NULL,'2024-02-12 02:39:21'),(57,11,0,'65c968c86e901691271861.jpg',235855,'image/jpeg','Deree-Student-Lounge-03.jpg',NULL,'2024-02-12 02:39:36'),(58,11,1,'65c968c86ed16230112569.jpg',174645,'image/jpeg','Deree-Student-Lounge-04.jpg',NULL,'2024-02-12 02:39:36'),(59,11,2,'65c968c86efef240456339.jpg',199573,'image/jpeg','Deree-Student-Lounge-05.jpg',NULL,'2024-02-12 02:39:36'),(60,11,3,'65c968c86f272782095448.jpg',261406,'image/jpeg','Deree-Student-Lounge-06.jpg',NULL,'2024-02-12 02:39:36'),(61,10,0,'65c968e500776347434352.jpg',171080,'image/jpeg','Deree-Faculty-Lounge-03.jpg',NULL,'2024-02-12 02:40:05'),(62,10,1,'65c968e500cb4077255242.jpg',239974,'image/jpeg','Deree-Faculty-Lounge-04.jpg',NULL,'2024-02-12 02:40:05'),(63,10,2,'65c968e5010ee865667156.jpg',215404,'image/jpeg','Deree-Faculty-Lounge-05.jpg',NULL,'2024-02-12 02:40:05'),(64,10,3,'65c968e50144d728400219.jpg',223021,'image/jpeg','Deree-Faculty-Lounge-06.jpg',NULL,'2024-02-12 02:40:05'),(65,9,0,'65c968e61c716528031637.jpg',114323,'image/jpeg','ACG-Art-Gallery-03.jpg',NULL,'2024-02-12 02:40:06'),(66,9,1,'65c968e61ca36259208522.jpg',101042,'image/jpeg','ACG-Art-Gallery-04.jpg',NULL,'2024-02-12 02:40:06'),(67,9,2,'65c968e61cc1b204081567.jpg',129574,'image/jpeg','ACG-Art-Gallery-05.jpg',NULL,'2024-02-12 02:40:06'),(68,8,0,'65c968fa9dab7207471326.jpg',165893,'image/jpeg','Center-for-the-Arts-Gallery-03.jpg',NULL,'2024-02-12 02:40:26'),(69,8,1,'65c968fa9df4d391122682.jpg',138087,'image/jpeg','Center-for-the-Arts-Gallery-04.jpg',NULL,'2024-02-12 02:40:26'),(70,8,2,'65c968fa9e26a138092713.jpg',171115,'image/jpeg','Center-for-the-Arts-Gallery-05.jpg',NULL,'2024-02-12 02:40:26'),(71,7,0,'65c9690e97321514627010.jpg',172312,'image/jpeg','Center-for-the-Arts-Auditorium-03.jpg',NULL,'2024-02-12 02:40:46'),(72,7,1,'65c9690e97681904951248.jpg',132916,'image/jpeg','Center-for-the-Arts-Auditorium-04.jpg',NULL,'2024-02-12 02:40:46'),(73,7,2,'65c9690e978c3589858009.jpg',188914,'image/jpeg','Center-for-the-Arts-Auditorium-05.jpg',NULL,'2024-02-12 02:40:46'),(74,6,0,'65c9691bc8f94947525793.jpg',193698,'image/jpeg','7th-Level-Auditorium-03.jpg',NULL,'2024-02-12 02:40:59'),(75,6,1,'65c9691bc9300832116924.jpg',179790,'image/jpeg','7th-Level-Auditorium-04.jpg',NULL,'2024-02-12 02:40:59'),(76,6,2,'65c9691bc9516547939949.jpg',176969,'image/jpeg','7th-Level-Auditorium-05.jpg',NULL,'2024-02-12 02:40:59'),(77,5,0,'65c9692b0ff19349246199.jpg',183474,'image/jpeg','6th-Level-Auditorium-03.jpg',NULL,'2024-02-12 02:41:15'),(78,5,1,'65c9692b10288777829253.jpg',164523,'image/jpeg','6th-Level-Auditorium-04.jpg',NULL,'2024-02-12 02:41:15'),(79,5,2,'65c9692b104c7476218183.jpg',161308,'image/jpeg','6th-Level-Auditorium-05.jpg',NULL,'2024-02-12 02:41:15'),(80,20,0,'65c9695a4b5da096386372.jpg',159457,'image/jpeg','Alba-Amphitheater-03.jpg',NULL,'2024-02-12 02:42:02'),(81,20,1,'65c9695a4b9a0451275919.jpg',160266,'image/jpeg','Alba-Amphitheater-04.jpg',NULL,'2024-02-12 02:42:02'),(82,20,2,'65c9695a4bbb0339701103.jpg',151010,'image/jpeg','Alba-Amphitheater-05.jpg',NULL,'2024-02-12 02:42:02'),(83,4,0,'65c969712c8cc953276900.jpg',226723,'image/jpeg','Irene-Bailey-Theater-03.jpg',NULL,'2024-02-12 02:42:25'),(84,4,1,'65c969712cc3b904588276.jpg',237131,'image/jpeg','Irene-Bailey-Theater-04.jpg',NULL,'2024-02-12 02:42:25'),(85,4,2,'65c969712ce60346177424.jpg',190677,'image/jpeg','Irene-Bailey-Theater-05.jpg',NULL,'2024-02-12 02:42:25'),(86,2,0,'65c96985395fc974299682.jpg',276691,'image/jpeg','Conference-Room-03.jpg',NULL,'2024-02-12 02:42:45'),(87,2,1,'65c9698539996942226604.jpg',131221,'image/jpeg','Conference-Room-04.jpg',NULL,'2024-02-12 02:42:45'),(88,2,2,'65c9698539bd8403567599.jpg',160052,'image/jpeg','Conference-Room-05.jpg',NULL,'2024-02-12 02:42:45'),(89,1,0,'65c969a8263f8727539101.jpg',231165,'image/jpeg','ACG-Events-Hall-03.jpg',NULL,'2024-02-12 02:43:20'),(90,1,1,'65c969a8268a7111631251.jpg',244295,'image/jpeg','ACG-Events-Hall-04.jpg',NULL,'2024-02-12 02:43:20'),(91,1,2,'65c969a826bb1001127563.jpg',312825,'image/jpeg','ACG-Events-Hall-06.jpg',NULL,'2024-02-12 02:43:20'),(92,1,3,'65c969a826eec885336951.jpg',370896,'image/jpeg','ACG-Events-Hall-07.jpg',NULL,'2024-02-12 02:43:20'),(93,1,4,'65c969a827276573897063.jpg',146360,'image/jpeg','ACG-Events-Hall-08.jpg',NULL,'2024-02-12 02:43:20'),(94,3,0,'65c96a0ae01db164261270.jpg',226723,'image/jpeg','Irene-Bailey-Theater-03.jpg',NULL,'2024-02-12 02:44:58'),(95,3,1,'65c96a0ae06ea343622854.jpg',237131,'image/jpeg','Irene-Bailey-Theater-04.jpg',NULL,'2024-02-12 02:44:58'),(96,3,2,'65c96a0ae0a57095256633.jpg',190677,'image/jpeg','Irene-Bailey-Theater-05.jpg',NULL,'2024-02-12 02:44:58');
/*!40000 ALTER TABLE `universe_venue_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_volunteer_event`
--

DROP TABLE IF EXISTS `universe_volunteer_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_volunteer_event` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `universe_event_id` int NOT NULL,
  `message` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_64EC18A76ED395` (`user_id`),
  CONSTRAINT `FK_64EC18A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_volunteer_event`
--

LOCK TABLES `universe_volunteer_event` WRITE;
/*!40000 ALTER TABLE `universe_volunteer_event` DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_volunteer_event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_volunteer_event_select_attendees`
--

DROP TABLE IF EXISTS `universe_volunteer_event_select_attendees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_volunteer_event_select_attendees` (
  `event_id` int NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`event_id`,`user_id`),
  KEY `IDX_FBECCD3471F7E88B` (`event_id`),
  KEY `IDX_FBECCD34A76ED395` (`user_id`),
  CONSTRAINT `FK_FBECCD3471F7E88B` FOREIGN KEY (`event_id`) REFERENCES `universe_event` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_FBECCD34A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_volunteer_event_select_attendees`
--

LOCK TABLES `universe_volunteer_event_select_attendees` WRITE;
/*!40000 ALTER TABLE `universe_volunteer_event_select_attendees` DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_volunteer_event_select_attendees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_vote`
--

DROP TABLE IF EXISTS `universe_vote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_vote` (
  `id` int NOT NULL AUTO_INCREMENT,
  `comment_id` int DEFAULT NULL,
  `voter_id` int DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `value` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_52D6F5AF8697D13` (`comment_id`),
  KEY `IDX_52D6F5AEBB4B8AD` (`voter_id`),
  CONSTRAINT `FK_52D6F5AEBB4B8AD` FOREIGN KEY (`voter_id`) REFERENCES `universe_user` (`id`),
  CONSTRAINT `FK_52D6F5AF8697D13` FOREIGN KEY (`comment_id`) REFERENCES `universe_comment` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_vote`
--

LOCK TABLES `universe_vote` WRITE;
/*!40000 ALTER TABLE `universe_vote` DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_vote` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-05-29 14:51:01
