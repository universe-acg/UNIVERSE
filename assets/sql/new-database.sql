CREATE DATABASE IF NOT EXISTS `universedb` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION = 'N' */;
USE `universedb`;
-- MySQL dump 10.13  Distrib 8.0.33, for Win64 (x86_64)
--
-- Host: localhost    Database: universedb
-- ------------------------------------------------------
-- Server version	8.0.36-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE = @@TIME_ZONE */;
/*!40103 SET TIME_ZONE = '+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS = @@UNIQUE_CHECKS, UNIQUE_CHECKS = 0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0 */;
/*!40101 SET @OLD_SQL_MODE = @@SQL_MODE, SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES = @@SQL_NOTES, SQL_NOTES = 0 */;

--
-- Table structure for table `chat_user`
--

DROP TABLE IF EXISTS `chat_user`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_user`
(
    `universe_chat_entity_id` int NOT NULL,
    `user_id`                 int NOT NULL,
    PRIMARY KEY (`universe_chat_entity_id`, `user_id`),
    KEY `IDX_2B0F4B08B5BF092A` (`universe_chat_entity_id`),
    KEY `IDX_2B0F4B08A76ED395` (`user_id`),
    CONSTRAINT `FK_2B0F4B08A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`) ON DELETE CASCADE,
    CONSTRAINT `FK_2B0F4B08B5BF092A` FOREIGN KEY (`universe_chat_entity_id`) REFERENCES `universe_chat` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_user`
--

LOCK TABLES `chat_user` WRITE;
/*!40000 ALTER TABLE `chat_user`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `chat_user`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_user`
--

DROP TABLE IF EXISTS `event_user`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_user`
(
    `event_id` int NOT NULL,
    `user_id`  int NOT NULL,
    PRIMARY KEY (`event_id`, `user_id`),
    KEY `IDX_92589AE271F7E88B` (`event_id`),
    KEY `IDX_92589AE2A76ED395` (`user_id`),
    CONSTRAINT `FK_92589AE271F7E88B` FOREIGN KEY (`event_id`) REFERENCES `universe_event` (`id`) ON DELETE CASCADE,
    CONSTRAINT `FK_92589AE2A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_user`
--

LOCK TABLES `event_user` WRITE;
/*!40000 ALTER TABLE `event_user`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `event_user`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `monthly_schedule`
--

DROP TABLE IF EXISTS `monthly_schedule`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `monthly_schedule`
(
    `id`          int  NOT NULL AUTO_INCREMENT,
    `facility_id` int DEFAULT NULL,
    `start_date`  date NOT NULL,
    `end_date`    date NOT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_1B303E20A7014910` (`facility_id`),
    CONSTRAINT `FK_1B303E20A7014910` FOREIGN KEY (`facility_id`) REFERENCES `universe_facility` (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 2
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `monthly_schedule`
--

LOCK TABLES `monthly_schedule` WRITE;
/*!40000 ALTER TABLE `monthly_schedule`
    DISABLE KEYS */;
INSERT INTO `monthly_schedule`
VALUES (1, 1, '2019-01-01', '2019-01-01');
/*!40000 ALTER TABLE `monthly_schedule`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `operating_hours`
--

DROP TABLE IF EXISTS `operating_hours`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `operating_hours`
(
    `id`                  int                                                           NOT NULL AUTO_INCREMENT,
    `monthly_schedule_id` int DEFAULT NULL,
    `day_of_week`         varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `start_time`          time                                                          NOT NULL,
    `end_time`            time                                                          NOT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_540F2B4F3910A81A` (`monthly_schedule_id`),
    CONSTRAINT `FK_540F2B4F3910A81A` FOREIGN KEY (`monthly_schedule_id`) REFERENCES `monthly_schedule` (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 2
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operating_hours`
--

LOCK TABLES `operating_hours` WRITE;
/*!40000 ALTER TABLE `operating_hours`
    DISABLE KEYS */;
INSERT INTO `operating_hours`
VALUES (1, NULL, 'Monday', '00:00:00', '00:00:00');
/*!40000 ALTER TABLE `operating_hours`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organizer_user`
--

DROP TABLE IF EXISTS `organizer_user`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `organizer_user`
(
    `organizer_id` int NOT NULL,
    `user_id`      int NOT NULL,
    PRIMARY KEY (`organizer_id`, `user_id`),
    KEY `IDX_9744570B876C4DDA` (`organizer_id`),
    KEY `IDX_9744570BA76ED395` (`user_id`),
    CONSTRAINT `FK_9744570B876C4DDA` FOREIGN KEY (`organizer_id`) REFERENCES `universe_organizer` (`id`) ON DELETE CASCADE,
    CONSTRAINT `FK_9744570BA76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organizer_user`
--

LOCK TABLES `organizer_user` WRITE;
/*!40000 ALTER TABLE `organizer_user`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `organizer_user`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_amenity`
--

DROP TABLE IF EXISTS `universe_amenity`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_amenity`
(
    `id`         int                                                          NOT NULL AUTO_INCREMENT,
    `icon`       varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `name`       varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `slug`       varchar(80) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `hidden`     tinyint(1) DEFAULT NULL,
    `created_at` datetime                                                     NOT NULL,
    `updated_at` datetime   DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 3
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_amenity`
--

LOCK TABLES `universe_amenity` WRITE;
/*!40000 ALTER TABLE `universe_amenity`
    DISABLE KEYS */;
INSERT INTO `universe_amenity`
VALUES (1, 'fas fa-desktop', 'Computer', 'computer', NULL, '2024-02-01 21:14:45', '2024-02-01 21:14:45'),
       (2, 'fas fa-video', 'Projector', 'projector', NULL, '2024-02-01 21:15:10', '2024-02-01 21:15:10');
/*!40000 ALTER TABLE `universe_amenity`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_app_layout_setting`
--

DROP TABLE IF EXISTS `universe_app_layout_setting`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_app_layout_setting`
(
    `id`                     int NOT NULL AUTO_INCREMENT,
    `logo_name`              varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `logo_size`              int                                                           DEFAULT NULL,
    `logo_mime_type`         varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `logo_original_name`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `logo_dimensions`        longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `favicon_name`           varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `favicon_size`           int                                                           DEFAULT NULL,
    `favicon_mime_type`      varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `favicon_original_name`  varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `favicon_dimensions`     longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `og_image_name`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `og_image_size`          int                                                           DEFAULT NULL,
    `og_image_mime_type`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `og_image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `og_image_dimensions`    longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `updated_at`             datetime                                                      DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 2
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_app_layout_setting`
--

LOCK TABLES `universe_app_layout_setting` WRITE;
/*!40000 ALTER TABLE `universe_app_layout_setting`
    DISABLE KEYS */;
INSERT INTO `universe_app_layout_setting`
VALUES (1, '65ba92e812427651339060.png', 3156, 'image/png', 'LOGO.png', '195,44', '65ba92d0c6c4a434254519.jpg', 98994,
        'image/jpeg', '1T-03.jpg', '1080,1080', '65ba92d0c6f9e728933195.jpg', 98994, 'image/jpeg', '1T-03.jpg',
        '1080,1080', '2024-01-31 20:35:20');
/*!40000 ALTER TABLE `universe_app_layout_setting`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_athletic_booking`
--

DROP TABLE IF EXISTS `universe_athletic_booking`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_athletic_booking`
(
    `id`           int        NOT NULL AUTO_INCREMENT,
    `facility_id`  int        NOT NULL,
    `user_id`      int      DEFAULT NULL,
    `status`       tinyint(1) NOT NULL,
    `amount`       int        NOT NULL,
    `duration`     int        NOT NULL,
    `booking_date` datetime   NOT NULL,
    `created_at`   datetime   NOT NULL,
    `updated_at`   datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_936BF858A7014910` (`facility_id`),
    KEY `IDX_936BF858A76ED395` (`user_id`),
    CONSTRAINT `FK_936BF858A7014910` FOREIGN KEY (`facility_id`) REFERENCES `universe_facility` (`id`),
    CONSTRAINT `FK_936BF858A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_athletic_booking`
--

LOCK TABLES `universe_athletic_booking` WRITE;
/*!40000 ALTER TABLE `universe_athletic_booking`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_athletic_booking`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_audience`
--

DROP TABLE IF EXISTS `universe_audience`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_audience`
(
    `id`                  int                                                          NOT NULL AUTO_INCREMENT,
    `name`                varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `slug`                varchar(80) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `image_name`          varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci   DEFAULT NULL,
    `image_size`          int                                                            DEFAULT NULL,
    `image_mime_type`     varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci   DEFAULT NULL,
    `image_original_name` varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_dimensions`    longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `hidden`              tinyint(1)                                                   NOT NULL,
    `created_at`          datetime                                                     NOT NULL,
    `updated_at`          datetime                                                       DEFAULT NULL,
    `deleted_at`          datetime                                                       DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 6
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_audience`
--

LOCK TABLES `universe_audience` WRITE;
/*!40000 ALTER TABLE `universe_audience`
    DISABLE KEYS */;
INSERT INTO `universe_audience`
VALUES (1, 'test', 'testslug', '5d641402c4cf9162849552.png', 15678, 'image/png', 'adults.png', '512,512', 0,
        '2019-08-26 17:16:50', '2019-08-26 17:16:50', NULL),
       (2, 'test1', 'testslug1', '5d641451ca81e833369961.png', 15496, 'image/png', 'children.png', '512,512', 0,
        '2019-08-26 17:18:09', '2020-04-21 13:23:14', NULL);
/*!40000 ALTER TABLE `universe_audience`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_blog_post`
--

DROP TABLE IF EXISTS `universe_blog_post`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_blog_post`
(
    `id`                  int                                                           NOT NULL AUTO_INCREMENT,
    `user_id`             int                                                           DEFAULT NULL,
    `category_id`         int                                                           DEFAULT NULL,
    `slug`                varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `tags`                varchar(255) COLLATE utf8mb3_unicode_ci                       DEFAULT NULL,
    `name`                varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `content`             varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `readtime`            int                                                           DEFAULT NULL,
    `image_name`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_size`          int                                                           DEFAULT NULL,
    `image_mime_type`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_dimensions`    longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `views`               int                                                           DEFAULT NULL,
    `hidden`              tinyint(1)                                                    NOT NULL,
    `created_at`          datetime                                                      NOT NULL,
    `updated_at`          datetime                                                      DEFAULT NULL,
    `deleted_at`          datetime                                                      DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `UNIQ_C1970FE6FBC9426` (`tags`),
    UNIQUE KEY `UNIQ_C1970FEA76ED395` (`user_id`),
    KEY `IDX_C1970FE12469DE2` (`category_id`),
    CONSTRAINT `FK_C1970FE12469DE2` FOREIGN KEY (`category_id`) REFERENCES `universe_blog_post_category` (`id`),
    CONSTRAINT `FK_C1970FEA76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_blog_post`
--

LOCK TABLES `universe_blog_post` WRITE;
/*!40000 ALTER TABLE `universe_blog_post`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_blog_post`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_blog_post_category`
--

DROP TABLE IF EXISTS `universe_blog_post_category`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_blog_post_category`
(
    `id`         int                                                           NOT NULL AUTO_INCREMENT,
    `slug`       varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `tags`       varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
    `name`       varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `content`    varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
    `hidden`     tinyint(1)                                                    DEFAULT NULL,
    `created_at` datetime                                                      NOT NULL,
    `updated_at` datetime                                                      DEFAULT NULL,
    `deleted_at` datetime                                                      DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `UNIQ_5F6CCC5F6FBC9426` (`tags`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 5
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_blog_post_category`
--

LOCK TABLES `universe_blog_post_category` WRITE;
/*!40000 ALTER TABLE `universe_blog_post_category`
    DISABLE KEYS */;
INSERT INTO `universe_blog_post_category`
VALUES (1, 'dsa-news', NULL, 'DSA News', NULL, 0, '2024-01-31 19:53:08', '2024-01-31 19:53:08', NULL),
       (2, 'club-news', NULL, 'Club news', NULL, 0, '2024-01-31 19:53:12', '2024-01-31 19:53:12', NULL),
       (3, 'organizations-news', NULL, 'Organizations News', NULL, 0, '2024-01-31 19:53:18', '2024-01-31 19:53:18',
        NULL),
       (4, 'general', NULL, 'General', NULL, 0, '2024-01-31 19:53:25', '2024-01-31 19:53:25', NULL);
/*!40000 ALTER TABLE `universe_blog_post_category`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_category`
--

DROP TABLE IF EXISTS `universe_category`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_category`
(
    `id`                  int                                                           NOT NULL AUTO_INCREMENT,
    `slug`                varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `name`                varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `icon`                varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `image_name`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_size`          int                                                           DEFAULT NULL,
    `image_mime_type`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_dimensions`    longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `hidden`              tinyint(1)                                                    NOT NULL,
    `featured`            tinyint(1)                                                    NOT NULL,
    `featuredorder`       int                                                           DEFAULT NULL,
    `created_at`          datetime                                                      NOT NULL,
    `updated_at`          datetime                                                      DEFAULT NULL,
    `deleted_at`          datetime                                                      DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `UNIQ_23F1C7FE989D9B62` (`slug`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 6
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_category`
--

LOCK TABLES `universe_category` WRITE;
/*!40000 ALTER TABLE `universe_category`
    DISABLE KEYS */;
INSERT INTO `universe_category`
VALUES (1, 'organizations', 'Organizations', 'fas fa-align-justify', '65ba93153cb1c986997099.png', 370493, 'image/png',
        '8.png', '750,750', 0, 1, 1, '2024-01-31 20:36:05', '2024-01-31 20:38:02', NULL),
       (2, 'societies', 'Societies', 'fas fa-align-justify', '65ba932a0ab8a050992884.png', 364096, 'image/png', '3.png',
        '750,750', 0, 1, 2, '2024-01-31 20:36:26', '2024-01-31 20:38:00', NULL),
       (3, 'clubs', 'Clubs', 'fas fa-align-justify', '65ba9333d5f61073450463.png', 359305, 'image/png', '6.png',
        '750,750', 0, 1, 3, '2024-01-31 20:36:35', '2024-01-31 20:37:49', NULL),
       (4, 'offices', 'Offices', 'fas fa-align-justify', '65ba934b17378657656289.png', 373351, 'image/png', '7.png',
        '750,750', 0, 1, 4, '2024-01-31 20:36:59', '2024-01-31 20:37:57', NULL),
       (5, 'out-of-campus', 'Out of Campus', 'fas fa-align-justify', '65ba936835330764467995.png', 391068, 'image/png',
        '9.png', '750,750', 0, 1, 5, '2024-01-31 20:37:28', '2024-01-31 20:37:53', NULL);
/*!40000 ALTER TABLE `universe_category`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_chat`
--

DROP TABLE IF EXISTS `universe_chat`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_chat`
(
    `id`    int                                                           NOT NULL AUTO_INCREMENT,
    `title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_chat`
--

LOCK TABLES `universe_chat` WRITE;
/*!40000 ALTER TABLE `universe_chat`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_chat`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_chat_user`
--

DROP TABLE IF EXISTS `universe_chat_user`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_chat_user`
(
    `chat_id` int NOT NULL,
    `user_id` int NOT NULL,
    PRIMARY KEY (`chat_id`, `user_id`),
    KEY `IDX_9D4CDBEB1A9A7125` (`chat_id`),
    KEY `IDX_9D4CDBEBA76ED395` (`user_id`),
    CONSTRAINT `FK_9D4CDBEB1A9A7125` FOREIGN KEY (`chat_id`) REFERENCES `universe_chat` (`id`),
    CONSTRAINT `FK_9D4CDBEBA76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_chat_user`
--

LOCK TABLES `universe_chat_user` WRITE;
/*!40000 ALTER TABLE `universe_chat_user`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_chat_user`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_comment`
--

DROP TABLE IF EXISTS `universe_comment`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_comment`
(
    `id`         int                                                            NOT NULL AUTO_INCREMENT,
    `thread_id`  varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `author_id`  int                                                           DEFAULT NULL,
    `body`       longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci      NOT NULL,
    `ancestors`  varchar(1024) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `depth`      int                                                            NOT NULL,
    `created_at` datetime                                                       NOT NULL,
    `state`      int                                                            NOT NULL,
    `score`      int                                                            NOT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_4678CDDEE2904019` (`thread_id`),
    KEY `IDX_4678CDDEF675F31B` (`author_id`),
    CONSTRAINT `FK_4678CDDEE2904019` FOREIGN KEY (`thread_id`) REFERENCES `universe_thread` (`id`),
    CONSTRAINT `FK_4678CDDEF675F31B` FOREIGN KEY (`author_id`) REFERENCES `universe_user` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_comment`
--

LOCK TABLES `universe_comment` WRITE;
/*!40000 ALTER TABLE `universe_comment`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_comment`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_event`
--

DROP TABLE IF EXISTS `universe_event`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_event`
(
    `id`                    int                                                           NOT NULL AUTO_INCREMENT,
    `venue_id`              int                                                           DEFAULT NULL,
    `category_id`           int                                                           DEFAULT NULL,
    `organizer_id`          int                                                           DEFAULT NULL,
    `isonhomepageslider_id` int                                                           DEFAULT NULL,
    `name`                  varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `description`           longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
    `slug`                  varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
    `redirectable`          tinyint(1)                                                    DEFAULT NULL,
    `allowoverbook`         tinyint(1)                                                    DEFAULT NULL,
    `viewablebyselect`      tinyint(1)                                                    DEFAULT NULL,
    `outofcampus`           tinyint(1)                                                    DEFAULT NULL,
    `reference`             varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
    `views`                 int                                                           DEFAULT NULL,
    `externallink`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `phonenumber`           varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `email`                 varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `tags`                  varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_name`            varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_size`            int                                                           DEFAULT NULL,
    `image_mime_type`       varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `commonvalue`           varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_original_name`   varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_dimensions`      longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `created_at`            datetime                                                      NOT NULL,
    `updated_at`            datetime                                                      DEFAULT NULL,
    `deleted_at`            datetime                                                      DEFAULT NULL,
    `published`             tinyint(1)                                                    NOT NULL,
    `open`                  tinyint(1)                                                    NOT NULL,
    `isrepeat`              tinyint(1)                                                    NOT NULL,
    `showattendees`         tinyint(1)                                                    NOT NULL,
    `volsneeded`            tinyint(1)                                                    NOT NULL,
    `publish_date`          datetime                                                      DEFAULT NULL,
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
) ENGINE = InnoDB
  AUTO_INCREMENT = 3
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_event`
--

LOCK TABLES `universe_event` WRITE;
/*!40000 ALTER TABLE `universe_event`
    DISABLE KEYS */;
INSERT INTO `universe_event`
VALUES (2, NULL, 3, 3, 1, 'Capture The Flag (CTF) Competition I',
        '<p>Welcome to the ultimate cyber battleground! Our Capture The Flag (CTF) Competition I is a thrilling cybersecurity event where participants showcase their hacking and defensive skills in a virtual arena. Engage in intense challenges designed to test your problem-solving abilities, exploit vulnerabilities, and secure coveted flags hidden within complex systems. With a mix of offensive and defensive tactics, participants will navigate through a maze of cybersecurity puzzles, racing against the clock to emerge victorious. Are you ready to prove your prowess and claim the title of CTF champion?<br></p>',
        'capture-the-flag-ctf-competition-i-b48189a1a7e1df5f511c', NULL, NULL, NULL, NULL, '486d0b1346', 4, NULL, NULL,
        NULL, 'cyberclub', '65ba9500e44ca831527397.jpg', 683144, 'image/jpeg', NULL, '0_7U4Qj-gSIV_gckS6.jpg',
        '5456,3064', '2024-01-31 20:44:16', '2024-02-01 21:13:59', NULL, 1, 1, 0, 1, 0, NULL);
/*!40000 ALTER TABLE `universe_event`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_event_date`
--

DROP TABLE IF EXISTS `universe_event_date`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_event_date`
(
    `id`          int                                                           NOT NULL AUTO_INCREMENT,
    `event_id`    int      DEFAULT NULL,
    `online`      tinyint(1)                                                    NOT NULL,
    `active`      tinyint(1)                                                    NOT NULL,
    `reference`   varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `description` longtext COLLATE utf8mb3_unicode_ci                           NOT NULL,
    `title`       varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `startdate`   datetime DEFAULT NULL,
    `enddate`     datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_8CE08B8371F7E88B` (`event_id`),
    CONSTRAINT `FK_8CE08B8371F7E88B` FOREIGN KEY (`event_id`) REFERENCES `universe_event` (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 2
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_event_date`
--

LOCK TABLES `universe_event_date` WRITE;
/*!40000 ALTER TABLE `universe_event_date`
    DISABLE KEYS */;
INSERT INTO `universe_event_date`
VALUES (1, 2, 0, 1, '9a88c9834a',
        '<p>Welcome to the ultimate cyber battleground! Our Capture The Flag (CTF) Competition I is a thrilling cybersecurity event where participants showcase their hacking and defensive skills in a virtual arena. Engage in intense challenges designed to test your problem-solving abilities, exploit vulnerabilities, and secure coveted flags hidden within complex systems. With a mix of offensive and defensive tactics, participants will navigate through a maze of cybersecurity puzzles, racing against the clock to emerge victorious. Are you ready to prove your prowess and claim the title of CTF champion?<br></p>',
        'Capture The Flag (CTF) Competition I', '2024-02-13 10:00:00', '2024-02-13 12:00:00');
/*!40000 ALTER TABLE `universe_event_date`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_event_date_ticket`
--

DROP TABLE IF EXISTS `universe_event_date_ticket`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_event_date_ticket`
(
    `id`                 int                                                           NOT NULL AUTO_INCREMENT,
    `eventdate_id`       int                                                           DEFAULT NULL,
    `alreadybooked`      tinyint(1)                                                    NOT NULL,
    `active`             tinyint(1)                                                    NOT NULL,
    `reference`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `name`               varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `description`        varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `quantity`           int                                                           DEFAULT NULL,
    `ticketsperattendee` int                                                           DEFAULT NULL,
    `salesstartdate`     datetime                                                      NOT NULL,
    `salesenddate`       datetime                                                      NOT NULL,
    `position`           int                                                           NOT NULL,
    `informedcreator`    tinyint(1)                                                    NOT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_2C419008733DA6BA` (`eventdate_id`),
    CONSTRAINT `FK_2C419008733DA6BA` FOREIGN KEY (`eventdate_id`) REFERENCES `universe_event_date` (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 2
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_event_date_ticket`
--

LOCK TABLES `universe_event_date_ticket` WRITE;
/*!40000 ALTER TABLE `universe_event_date_ticket`
    DISABLE KEYS */;
INSERT INTO `universe_event_date_ticket`
VALUES (1, 1, 0, 1, 'a2b396a14b', 'Ticket', '', 100, 1, '2024-01-31 20:44:16', '2024-01-31 20:44:16', 0, 0);
/*!40000 ALTER TABLE `universe_event_date_ticket`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_event_file`
--

DROP TABLE IF EXISTS `universe_event_file`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_event_file`
(
    `id`                 int      NOT NULL AUTO_INCREMENT,
    `event_id`           int                                                           DEFAULT NULL,
    `created_at`         datetime NOT NULL,
    `updated_at`         datetime                                                      DEFAULT NULL,
    `file_name`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `file_size`          int                                                           DEFAULT NULL,
    `file_mime_type`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `file_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `file_dimensions`    longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `position`           int                                                           DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_AAE18AE971F7E88B` (`event_id`),
    CONSTRAINT `FK_AAE18AE971F7E88B` FOREIGN KEY (`event_id`) REFERENCES `universe_event` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_event_file`
--

LOCK TABLES `universe_event_file` WRITE;
/*!40000 ALTER TABLE `universe_event_file`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_event_file`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_event_image`
--

DROP TABLE IF EXISTS `universe_event_image`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_event_image`
(
    `id`                  int NOT NULL AUTO_INCREMENT,
    `event_id`            int                                                           DEFAULT NULL,
    `image_name`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_size`          int                                                           DEFAULT NULL,
    `image_mime_type`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_dimensions`    longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `position`            int                                                           DEFAULT NULL,
    `updated_at`          datetime                                                      DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_17A305B71F7E88B` (`event_id`),
    CONSTRAINT `FK_17A305B71F7E88B` FOREIGN KEY (`event_id`) REFERENCES `universe_event` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_event_image`
--

LOCK TABLES `universe_event_image` WRITE;
/*!40000 ALTER TABLE `universe_event_image`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_event_image`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_facility`
--

DROP TABLE IF EXISTS `universe_facility`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_facility`
(
    `id`                  int                                                           NOT NULL AUTO_INCREMENT,
    `hidden`              tinyint(1)                                                    NOT NULL,
    `created_at`          datetime                                                      NOT NULL,
    `main_image`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `duration`            int                                                           NOT NULL,
    `name`                varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `description`         longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
    `min_players`         int                                                           NOT NULL,
    `max_players`         int                                                           NOT NULL,
    `reference`           varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `views`               int                                                           DEFAULT NULL,
    `externallink`        varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `phonenumber`         varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `email`               varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `tags`                varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_name`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_size`          int                                                           DEFAULT NULL,
    `image_mime_type`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_dimensions`    longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `updated_at`          datetime                                                      NOT NULL,
    `deleted_at`          datetime                                                      DEFAULT NULL,
    `published`           tinyint(1)                                                    DEFAULT NULL,
    `slug`                varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 2
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_facility`
--

LOCK TABLES `universe_facility` WRITE;
/*!40000 ALTER TABLE `universe_facility`
    DISABLE KEYS */;
INSERT INTO `universe_facility`
VALUES (1, 0, '2024-02-01 21:16:34', 'TennisCourts-05.jpg', 30, 'Tennis Court I',
        '<p>State-of-the-art tennis facility offering pristine courts, professional coaching, and a vibrant community atmosphere. Elevate your game in a modern setting designed for players of all levels, providing a perfect blend of top-notch amenities and a passion for tennis excellence.<br></p>',
        1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 270949, 'image/jpeg', 'TennisCourts-05.jpg', '1200,800',
        '2024-02-01 21:16:34', NULL, NULL, 'tennis-court-i');
/*!40000 ALTER TABLE `universe_facility`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_facility_images`
--

DROP TABLE IF EXISTS `universe_facility_images`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_facility_images`
(
    `id`                  int NOT NULL AUTO_INCREMENT,
    `facility_id`         int                                                           DEFAULT NULL,
    `position`            int                                                           DEFAULT NULL,
    `image_name`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_size`          int                                                           DEFAULT NULL,
    `image_mime_type`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_dimensions`    longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `updated_at`          datetime                                                      DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_448F925DA7014910` (`facility_id`),
    CONSTRAINT `FK_448F925DA7014910` FOREIGN KEY (`facility_id`) REFERENCES `universe_facility` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_facility_images`
--

LOCK TABLES `universe_facility_images` WRITE;
/*!40000 ALTER TABLE `universe_facility_images`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_facility_images`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_favorites`
--

DROP TABLE IF EXISTS `universe_favorites`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_favorites`
(
    `Event_id` int NOT NULL,
    `User_Id`  int NOT NULL,
    PRIMARY KEY (`Event_id`, `User_Id`),
    KEY `IDX_522AF01688818ADD` (`Event_id`),
    KEY `IDX_522AF016FD57CEAB` (`User_Id`),
    CONSTRAINT `FK_522AF01688818ADD` FOREIGN KEY (`Event_id`) REFERENCES `universe_event` (`id`),
    CONSTRAINT `FK_522AF016FD57CEAB` FOREIGN KEY (`User_Id`) REFERENCES `universe_user` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_favorites`
--

LOCK TABLES `universe_favorites` WRITE;
/*!40000 ALTER TABLE `universe_favorites`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_favorites`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_file`
--

DROP TABLE IF EXISTS `universe_file`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_file`
(
    `id`                 int                                                           NOT NULL AUTO_INCREMENT,
    `user_id`            int      DEFAULT NULL,
    `file_name`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `file_size`          int                                                           NOT NULL,
    `created_at`         datetime                                                      NOT NULL,
    `file_mime_type`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `updated_at`         datetime DEFAULT NULL,
    `file_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `title`              varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_D3A2DC2EA76ED395` (`user_id`),
    CONSTRAINT `FK_D3A2DC2EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_file`
--

LOCK TABLES `universe_file` WRITE;
/*!40000 ALTER TABLE `universe_file`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_file`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_following`
--

DROP TABLE IF EXISTS `universe_following`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_following`
(
    `Organizer_id` int NOT NULL,
    `User_Id`      int NOT NULL,
    PRIMARY KEY (`Organizer_id`, `User_Id`),
    KEY `IDX_C7FC1D009F5D9622` (`Organizer_id`),
    KEY `IDX_C7FC1D00FD57CEAB` (`User_Id`),
    CONSTRAINT `FK_C7FC1D009F5D9622` FOREIGN KEY (`Organizer_id`) REFERENCES `universe_organizer` (`id`),
    CONSTRAINT `FK_C7FC1D00FD57CEAB` FOREIGN KEY (`User_Id`) REFERENCES `universe_user` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_following`
--

LOCK TABLES `universe_following` WRITE;
/*!40000 ALTER TABLE `universe_following`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_following`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_gbmembers`
--

DROP TABLE IF EXISTS `universe_gbmembers`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_gbmembers`
(
    `id`          int                                                           NOT NULL AUTO_INCREMENT,
    `attendeeid`  int      DEFAULT NULL,
    `orgid`       int      DEFAULT NULL,
    `title`       varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `created_at`  datetime                                                      NOT NULL,
    `updated_at`  datetime DEFAULT NULL,
    `description` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci     NOT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_224AC267633CAD3B` (`attendeeid`),
    KEY `IDX_224AC267AF0ED79C` (`orgid`),
    CONSTRAINT `FK_224AC267633CAD3B` FOREIGN KEY (`attendeeid`) REFERENCES `universe_user` (`id`),
    CONSTRAINT `FK_224AC267AF0ED79C` FOREIGN KEY (`orgid`) REFERENCES `universe_organizer` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_gbmembers`
--

LOCK TABLES `universe_gbmembers` WRITE;
/*!40000 ALTER TABLE `universe_gbmembers`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_gbmembers`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_help_center_article`
--

DROP TABLE IF EXISTS `universe_help_center_article`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_help_center_article`
(
    `id`          int                                                           NOT NULL AUTO_INCREMENT,
    `category_id` int                                     DEFAULT NULL,
    `slug`        varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `tags`        varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `title`       varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `content`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `views`       int                                     DEFAULT NULL,
    `hidden`      tinyint(1)                                                    NOT NULL,
    `featured`    tinyint(1)                                                    NOT NULL,
    `created_at`  datetime                                                      NOT NULL,
    `updated_at`  datetime                                DEFAULT NULL,
    `deleted_at`  datetime                                DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `UNIQ_3C5A4E9D989D9B62` (`slug`),
    UNIQUE KEY `UNIQ_3C5A4E9D6FBC9426` (`tags`),
    KEY `IDX_3C5A4E9D12469DE2` (`category_id`),
    CONSTRAINT `FK_3C5A4E9D12469DE2` FOREIGN KEY (`category_id`) REFERENCES `universe_help_center_category` (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 7
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_help_center_article`
--

LOCK TABLES `universe_help_center_article` WRITE;
/*!40000 ALTER TABLE `universe_help_center_article`
    DISABLE KEYS */;
INSERT INTO `universe_help_center_article`
VALUES (1, 2, 'how-do-i-create-an-event', NULL, 'How do I create an event?',
        '<p>To create an event, log in to your account and navigate to the \"Create Event\" page. Fill in the required details such as event name, date, and location. You can also customize additional settings to make your event unique.<br></p>',
        0, 0, 1, '2024-01-31 19:57:08', '2024-01-31 19:58:18', NULL),
       (2, 2, 'can-i-edit-my-event-details-after-creation', NULL, 'Can I edit my event details after creation?',
        '<p>Yes, you can edit your event details at any time. Simply go to the \"Manage Events\" section, select the event you want to edit, and click on the \"Edit\" option. Make the necessary changes and save your updates.<br></p>',
        0, 0, 1, '2024-01-31 19:57:17', '2024-01-31 19:58:15', NULL),
       (3, 1, 'how-do-i-register-for-an-event', NULL, 'How do I register for an event?',
        '<p>To register for an event, find the event page and click on the \"Register\" button. Fill in your details and follow the prompts. Some events may require payment for registration, so have your payment information ready.<br></p>',
        0, 0, 1, '2024-01-31 19:57:29', '2024-01-31 19:58:10', NULL),
       (4, 1, 'can-i-cancel-my-event-registration', NULL, 'Can I cancel my event registration?',
        '<p>Yes, you can cancel your registration for an event. Go to the event page, locate the \"Cancel Registration\" option, and follow the instructions. Keep in mind that some events may have specific cancellation policies.<br></p>',
        0, 0, 1, '2024-01-31 19:57:40', '2024-01-31 19:58:07', NULL),
       (5, 3, 'how-do-i-reset-my-password', NULL, 'How do I reset my password?',
        '<p>If you forget your password, click on the \"Forgot Password\" link on the login page. Enter your email address, and you\'ll receive instructions on how to reset your password.<br></p>',
        1, 0, 1, '2024-01-31 19:57:48', '2024-01-31 19:58:58', NULL),
       (6, 3, 'is-my-personal-information-secure', NULL, 'Is my personal information secure?',
        '<p>Yes, we take the security of your information seriously. We use industry-standard encryption and security measures to protect your data. Check our Privacy Policy for more details on how we handle your information.<br></p>',
        0, 0, 1, '2024-01-31 19:57:55', '2024-01-31 19:58:31', NULL);
/*!40000 ALTER TABLE `universe_help_center_article`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_help_center_category`
--

DROP TABLE IF EXISTS `universe_help_center_category`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_help_center_category`
(
    `id`         int                                                           NOT NULL AUTO_INCREMENT,
    `parent_id`  int                                                           DEFAULT NULL,
    `slug`       varchar(255) COLLATE utf8mb3_unicode_ci                       DEFAULT NULL,
    `name`       varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `icon`       varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `hidden`     tinyint(1)                                                    NOT NULL,
    `created_at` datetime                                                      NOT NULL,
    `updated_at` datetime                                                      DEFAULT NULL,
    `deleted_at` datetime                                                      DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `UNIQ_2C1D5215989D9B62` (`slug`),
    KEY `IDX_2C1D5215727ACA70` (`parent_id`),
    CONSTRAINT `FK_2C1D5215727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `universe_help_center_category` (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 4
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_help_center_category`
--

LOCK TABLES `universe_help_center_category` WRITE;
/*!40000 ALTER TABLE `universe_help_center_category`
    DISABLE KEYS */;
INSERT INTO `universe_help_center_category`
VALUES (1, NULL, 'attendee', 'Attendee', 'fas fa-user-graduate', 0, '2024-01-31 19:54:18', '2024-01-31 19:55:28', NULL),
       (2, NULL, 'creator', 'Creator', 'fas fa-building', 0, '2024-01-31 19:54:30', '2024-01-31 19:55:42', NULL),
       (3, NULL, 'account', 'Account', 'fas fa-user-alt', 0, '2024-01-31 19:55:02', '2024-01-31 19:55:02', NULL);
/*!40000 ALTER TABLE `universe_help_center_category`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_homepage_hero_setting`
--

DROP TABLE IF EXISTS `universe_homepage_hero_setting`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_homepage_hero_setting`
(
    `id`                              int                                                           NOT NULL AUTO_INCREMENT,
    `title`                           varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `content`                         varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `paragraph`                       varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `custom_background_name`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `custom_background_size`          int                                                           DEFAULT NULL,
    `custom_background_mime_type`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `custom_background_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `custom_background_dimensions`    longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `show_search_box`                 tinyint(1)                                                    DEFAULT NULL,
    `updated_at`                      datetime                                                      DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `UNIQ_5FBE84E82B36786B` (`title`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 2
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_homepage_hero_setting`
--

LOCK TABLES `universe_homepage_hero_setting` WRITE;
/*!40000 ALTER TABLE `universe_homepage_hero_setting`
    DISABLE KEYS */;
INSERT INTO `universe_homepage_hero_setting`
VALUES (1, 'Universe', 'events', 'Say goodbye to media clutter with a one-stop seamless experience!',
        '5d99d60e41207545475471.jpg', 346806, 'image/jpeg', 'ezra-jeffrey-comeau-pPquxoraq_M-unsplash.jpg', '1500,1000',
        1, '2024-01-31 20:33:18');
/*!40000 ALTER TABLE `universe_homepage_hero_setting`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_listing`
--

DROP TABLE IF EXISTS `universe_listing`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_listing`
(
    `id`                  int                                                           NOT NULL AUTO_INCREMENT,
    `event_id`            int                                                           DEFAULT NULL,
    `category_id`         int                                                           DEFAULT NULL,
    `organizer_id`        int                                                           DEFAULT NULL,
    `name`                varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `description`         longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
    `shortdescription`    longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
    `slug`                varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `reference`           varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
    `views`               int                                                           DEFAULT NULL,
    `externallink`        varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `phonenumber`         varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `email`               varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `tags`                varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_name`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_size`          int                                                           DEFAULT NULL,
    `image_mime_type`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `typeoflisting`       varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_dimensions`    longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `created_at`          datetime                                                      NOT NULL,
    `updated_at`          datetime                                                      DEFAULT NULL,
    `deleted_at`          datetime                                                      DEFAULT NULL,
    `alreadyapplied`      tinyint(1)                                                    NOT NULL,
    `published`           tinyint(1)                                                    NOT NULL,
    `isexternal`          tinyint(1)                                                    NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `UNIQ_190CD766989D9B62` (`slug`),
    KEY `IDX_190CD76671F7E88B` (`event_id`),
    KEY `IDX_190CD76612469DE2` (`category_id`),
    KEY `IDX_190CD766876C4DDA` (`organizer_id`),
    CONSTRAINT `FK_190CD76612469DE2` FOREIGN KEY (`category_id`) REFERENCES `universe_listingcategory` (`id`),
    CONSTRAINT `FK_190CD76671F7E88B` FOREIGN KEY (`event_id`) REFERENCES `universe_event` (`id`),
    CONSTRAINT `FK_190CD766876C4DDA` FOREIGN KEY (`organizer_id`) REFERENCES `universe_organizer` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_listing`
--

LOCK TABLES `universe_listing` WRITE;
/*!40000 ALTER TABLE `universe_listing`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_listing`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_listing_date_positions`
--

DROP TABLE IF EXISTS `universe_listing_date_positions`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_listing_date_positions`
(
    `id`               int        NOT NULL AUTO_INCREMENT,
    `listing_id`       int                                                           DEFAULT NULL,
    `created_at`       datetime   NOT NULL,
    `updated_at`       datetime                                                      DEFAULT NULL,
    `startdate`        datetime   NOT NULL,
    `enddate`          datetime   NOT NULL,
    `allowoverapply`   tinyint(1) NOT NULL,
    `shortdescription` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `candidatesamount` int                                                           DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_9FEAFA7FD4619D1A` (`listing_id`),
    CONSTRAINT `FK_9FEAFA7FD4619D1A` FOREIGN KEY (`listing_id`) REFERENCES `universe_listing` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_listing_date_positions`
--

LOCK TABLES `universe_listing_date_positions` WRITE;
/*!40000 ALTER TABLE `universe_listing_date_positions`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_listing_date_positions`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_listing_file`
--

DROP TABLE IF EXISTS `universe_listing_file`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_listing_file`
(
    `id`                 int      NOT NULL AUTO_INCREMENT,
    `listing_id`         int                                                           DEFAULT NULL,
    `created_at`         datetime NOT NULL,
    `updated_at`         datetime                                                      DEFAULT NULL,
    `file_name`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `file_size`          int                                                           DEFAULT NULL,
    `file_mime_type`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `file_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `file_dimensions`    longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `position`           int                                                           DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_EE5115BAD4619D1A` (`listing_id`),
    CONSTRAINT `FK_EE5115BAD4619D1A` FOREIGN KEY (`listing_id`) REFERENCES `universe_listing` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_listing_file`
--

LOCK TABLES `universe_listing_file` WRITE;
/*!40000 ALTER TABLE `universe_listing_file`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_listing_file`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_listing_image`
--

DROP TABLE IF EXISTS `universe_listing_image`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_listing_image`
(
    `id`                  int NOT NULL AUTO_INCREMENT,
    `listing_id`          int                                                           DEFAULT NULL,
    `image_name`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_size`          int                                                           DEFAULT NULL,
    `image_mime_type`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_dimensions`    longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `position`            int                                                           DEFAULT NULL,
    `updated_at`          datetime                                                      DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_F35C808AD4619D1A` (`listing_id`),
    CONSTRAINT `FK_F35C808AD4619D1A` FOREIGN KEY (`listing_id`) REFERENCES `universe_listing` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_listing_image`
--

LOCK TABLES `universe_listing_image` WRITE;
/*!40000 ALTER TABLE `universe_listing_image`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_listing_image`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_listing_submission`
--

DROP TABLE IF EXISTS `universe_listing_submission`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_listing_submission`
(
    `id`                 int                                                       NOT NULL AUTO_INCREMENT,
    `listing_id`         int                                                           DEFAULT NULL,
    `position_id`        int                                                           DEFAULT NULL,
    `user_id`            int                                                           DEFAULT NULL,
    `content`            longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `file_name`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `file_size`          int                                                           DEFAULT NULL,
    `file_mime_type`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `file_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `file_dimensions`    longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `created_at`         datetime                                                  NOT NULL,
    `deleted_at`         datetime                                                  NOT NULL,
    `updated_at`         datetime                                                      DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_3822BBD4D4619D1A` (`listing_id`),
    KEY `IDX_3822BBD4DD842E46` (`position_id`),
    KEY `IDX_3822BBD4A76ED395` (`user_id`),
    CONSTRAINT `FK_3822BBD4A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`),
    CONSTRAINT `FK_3822BBD4D4619D1A` FOREIGN KEY (`listing_id`) REFERENCES `universe_listing` (`id`),
    CONSTRAINT `FK_3822BBD4DD842E46` FOREIGN KEY (`position_id`) REFERENCES `universe_listing_date_positions` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_listing_submission`
--

LOCK TABLES `universe_listing_submission` WRITE;
/*!40000 ALTER TABLE `universe_listing_submission`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_listing_submission`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_listingcategory`
--

DROP TABLE IF EXISTS `universe_listingcategory`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_listingcategory`
(
    `id`                  int                                                           NOT NULL AUTO_INCREMENT,
    `slug`                varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `name`                varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `icon`                varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `image_name`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_size`          int                                                           DEFAULT NULL,
    `image_mime_type`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_dimensions`    longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `hidden`              tinyint(1)                                                    NOT NULL,
    `featured`            tinyint(1)                                                    NOT NULL,
    `featuredorder`       int                                                           DEFAULT NULL,
    `created_at`          datetime                                                      NOT NULL,
    `updated_at`          datetime                                                      DEFAULT NULL,
    `deleted_at`          datetime                                                      DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `UNIQ_649EB8BE989D9B62` (`slug`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_listingcategory`
--

LOCK TABLES `universe_listingcategory` WRITE;
/*!40000 ALTER TABLE `universe_listingcategory`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_listingcategory`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_lives`
--

DROP TABLE IF EXISTS `universe_lives`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_lives`
(
    `id`                  int                                                           NOT NULL AUTO_INCREMENT,
    `main_image`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `slug`                varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `hidden`              tinyint(1)                                                    NOT NULL,
    `title`               varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `link`                varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `description`         longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
    `created_at`          datetime                                                      NOT NULL,
    `image_name`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_size`          int                                                           DEFAULT NULL,
    `status`              int                                                           DEFAULT NULL,
    `image_mime_type`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_dimensions`    longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `updated_at`          datetime                                                      NOT NULL,
    `deleted_at`          datetime                                                      DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `UNIQ_9C0A5E1F989D9B62` (`slug`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_lives`
--

LOCK TABLES `universe_lives` WRITE;
/*!40000 ALTER TABLE `universe_lives`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_lives`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_managementclubssocieties`
--

DROP TABLE IF EXISTS `universe_managementclubssocieties`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_managementclubssocieties`
(
    `id`          int                                                           NOT NULL AUTO_INCREMENT,
    `userid`      int DEFAULT NULL,
    `orgid`       int DEFAULT NULL,
    `initiatedby` int DEFAULT NULL,
    `created_at`  datetime                                                      NOT NULL,
    `status`      varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_EDA30F7BF132696E` (`userid`),
    KEY `IDX_EDA30F7BAF0ED79C` (`orgid`),
    KEY `IDX_EDA30F7BBB0AB2B1` (`initiatedby`),
    CONSTRAINT `FK_EDA30F7BAF0ED79C` FOREIGN KEY (`orgid`) REFERENCES `universe_organizer` (`id`),
    CONSTRAINT `FK_EDA30F7BBB0AB2B1` FOREIGN KEY (`initiatedby`) REFERENCES `universe_user` (`id`),
    CONSTRAINT `FK_EDA30F7BF132696E` FOREIGN KEY (`userid`) REFERENCES `universe_user` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_managementclubssocieties`
--

LOCK TABLES `universe_managementclubssocieties` WRITE;
/*!40000 ALTER TABLE `universe_managementclubssocieties`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_managementclubssocieties`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_menu`
--

DROP TABLE IF EXISTS `universe_menu`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_menu`
(
    `id`   int                                                           NOT NULL AUTO_INCREMENT,
    `slug` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 7
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_menu`
--

LOCK TABLES `universe_menu` WRITE;
/*!40000 ALTER TABLE `universe_menu`
    DISABLE KEYS */;
INSERT INTO `universe_menu`
VALUES (3, 'header-menu', 'Header'),
       (4, 'first-footer-section-menu', 'First'),
       (5, 'second-footer-section-menu', 'Second'),
       (6, 'third-footer-section-menu', 'Third');
/*!40000 ALTER TABLE `universe_menu`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_menu_element`
--

DROP TABLE IF EXISTS `universe_menu_element`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_menu_element`
(
    `id`          int                                                           NOT NULL AUTO_INCREMENT,
    `menu_id`     int                                                           DEFAULT NULL,
    `icon`        varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `slug`        varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `link`        varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `custom_link` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `label`       varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `position`    int                                                           NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `UNIQ_FCABB7F8989D9B62` (`slug`),
    KEY `IDX_FCABB7F8CCD7E912` (`menu_id`),
    CONSTRAINT `FK_FCABB7F8CCD7E912` FOREIGN KEY (`menu_id`) REFERENCES `universe_menu` (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 24
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_menu_element`
--

LOCK TABLES `universe_menu_element` WRITE;
/*!40000 ALTER TABLE `universe_menu_element`
    DISABLE KEYS */;
INSERT INTO `universe_menu_element`
VALUES (2, 3, 'fas fa-home', 'home', '/', NULL, 'Home', 0),
       (3, 3, 'fas fa-calendar-alt', 'events', '/events', NULL, 'Events', 1),
       (4, 3, 'fas fa-stream', 'categories', NULL, NULL, 'Categories', 2),
       (5, 3, 'far fa-building', 'venues', NULL, 'https://universe.alexandroskourtis.me/venues', 'Venues', 3),
       (6, 3, 'far fa-newspaper', 'announcements', NULL, 'https://universe.alexandroskourtis.me/blog', 'Announcements', 4),
       (7, 3, 'fas fa-ticket-alt', 'my-tickets', NULL, 'https://universe.alexandroskourtis.me/dashboard/attendee/my-tickets',
        'My Tickets', 5),
       (8, 4, NULL, 'about-us', '/page/about-us', NULL, 'About us', 0),
       (9, 4, NULL, 'help-center', NULL, 'https://universe.alexandroskourtis.me/help-center', 'Help center', 1),
       (10, 4, NULL, 'privacy-policy', NULL, 'https://universe.alexandroskourtis.me/page/privacy-policy', 'Privacy policy', 2),
       (11, 4, NULL, 'cookie-policy', NULL, 'https://universe.alexandroskourtis.me/page/cookie-policy', 'Cookie policy', 3),
       (12, 4, NULL, 'terms-of-service', NULL, 'https://universe.alexandroskourtis.me/page/terms-of-service', 'Terms of service', 4),
       (13, 5, NULL, 'register-as-attendee', NULL, 'https://universe.alexandroskourtis.me/signup/attendee', 'Register as attendee',
        0),
       (14, 5, NULL, 'register-as-creator', NULL, 'https://universe.alexandroskourtis.me/signup/organizer', 'Register as creator', 1),
       (15, 5, NULL, 'my-tickets-1', NULL, 'https://universe.alexandroskourtis.me/dashboard/attendee/my-tickets', 'My tickets', 2),
       (16, 5, NULL, 'update-password', NULL, 'https://universe.alexandroskourtis.me/dashboard/change-password', 'Update password',
        3),
       (17, 5, NULL, 'update-email', NULL, 'https://universe.alexandroskourtis.me/api/profile/change-email', 'Update email', 4),
       (18, 6, NULL, 'categories-1', NULL, 'https://universe.alexandroskourtis.me/categories', 'Categories', 0),
       (19, 6, NULL, 'leaderboards', NULL, 'https://universe.alexandroskourtis.me/leaderboards', 'Leaderboards', 1),
       (20, 6, NULL, 'venues-1', NULL, 'https://universe.alexandroskourtis.me/venues', 'Venues', 2),
       (21, 6, NULL, 'athletics', NULL, 'https://universe.alexandroskourtis.me/athletics', 'Athletics', 3),
       (22, 6, NULL, 'listings', NULL, 'https://universe.alexandroskourtis.me/listings', 'Listings', 4);
/*!40000 ALTER TABLE `universe_menu_element`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_message`
--

DROP TABLE IF EXISTS `universe_message`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_message`
(
    `id`               int                                                       NOT NULL AUTO_INCREMENT,
    `universe_chat_id` int DEFAULT NULL,
    `sender_id`        int DEFAULT NULL,
    `receiver_id`      int DEFAULT NULL,
    `content`          longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `sent_at`          datetime                                                  NOT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_64B1AFCDC896EE97` (`universe_chat_id`),
    KEY `IDX_64B1AFCDF624B39D` (`sender_id`),
    KEY `IDX_64B1AFCDCD53EDB6` (`receiver_id`),
    CONSTRAINT `FK_64B1AFCDC896EE97` FOREIGN KEY (`universe_chat_id`) REFERENCES `universe_chat` (`id`),
    CONSTRAINT `FK_64B1AFCDCD53EDB6` FOREIGN KEY (`receiver_id`) REFERENCES `universe_user` (`id`),
    CONSTRAINT `FK_64B1AFCDF624B39D` FOREIGN KEY (`sender_id`) REFERENCES `universe_user` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_message`
--

LOCK TABLES `universe_message` WRITE;
/*!40000 ALTER TABLE `universe_message`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_message`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_notification`
--

DROP TABLE IF EXISTS `universe_notification`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_notification`
(
    `id`          int                                                           NOT NULL AUTO_INCREMENT,
    `name`        varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `userid`      int                                                           NOT NULL,
    `isread`      tinyint(1)                                                    NOT NULL,
    `description` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `created_at`  datetime                                                      NOT NULL,
    `updated_at`  datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_notification`
--

LOCK TABLES `universe_notification` WRITE;
/*!40000 ALTER TABLE `universe_notification`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_notification`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_order`
--

DROP TABLE IF EXISTS `universe_order`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_order`
(
    `id`         int                                                           NOT NULL AUTO_INCREMENT,
    `user_id`    int                                                           DEFAULT NULL,
    `overbook`   tinyint(1)                                                    NOT NULL,
    `reference`  varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `note`       varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `status`     int                                                           NOT NULL,
    `created_at` datetime                                                      NOT NULL,
    `updated_at` datetime                                                      DEFAULT NULL,
    `deleted_at` datetime                                                      DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_3417B3D9A76ED395` (`user_id`),
    CONSTRAINT `FK_3417B3D9A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_order`
--

LOCK TABLES `universe_order` WRITE;
/*!40000 ALTER TABLE `universe_order`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_order`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_order_element`
--

DROP TABLE IF EXISTS `universe_order_element`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_order_element`
(
    `id`             int NOT NULL AUTO_INCREMENT,
    `order_id`       int      DEFAULT NULL,
    `eventticket_id` int      DEFAULT NULL,
    `quantity`       int      DEFAULT NULL,
    `deleted_at`     datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_77B5AB2B8D9F6D38` (`order_id`),
    KEY `IDX_77B5AB2B182CEB62` (`eventticket_id`),
    CONSTRAINT `FK_77B5AB2B182CEB62` FOREIGN KEY (`eventticket_id`) REFERENCES `universe_event_date_ticket` (`id`),
    CONSTRAINT `FK_77B5AB2B8D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `universe_order` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_order_element`
--

LOCK TABLES `universe_order_element` WRITE;
/*!40000 ALTER TABLE `universe_order_element`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_order_element`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_order_ticket`
--

DROP TABLE IF EXISTS `universe_order_ticket`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_order_ticket`
(
    `id`              int                                                           NOT NULL AUTO_INCREMENT,
    `orderelement_id` int      DEFAULT NULL,
    `reference`       varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `created_at`      datetime                                                      NOT NULL,
    `updated_at`      datetime DEFAULT NULL,
    `deleted_at`      datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_611F4909EE04F0C1` (`orderelement_id`),
    CONSTRAINT `FK_611F4909EE04F0C1` FOREIGN KEY (`orderelement_id`) REFERENCES `universe_order_element` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_order_ticket`
--

LOCK TABLES `universe_order_ticket` WRITE;
/*!40000 ALTER TABLE `universe_order_ticket`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_order_ticket`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_organizer`
--

DROP TABLE IF EXISTS `universe_organizer`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_organizer`
(
    `id`                  int                                                           NOT NULL AUTO_INCREMENT,
    `user_id`             int                                                            DEFAULT NULL,
    `slug`                varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `name`                varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `description`         varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `website`             varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `email`               varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `phone`               varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `facebook`            varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `tiktok`              varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `twitter`             varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `instagram`           varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `linkedin`            varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `youtubeurl`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `category`            varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `logo_name`           varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `logo_size`           int                                                            DEFAULT NULL,
    `logo_mime_type`      varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `logo_original_name`  varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `logo_dimensions`     longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `cover_name`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `cover_size`          int                                                            DEFAULT NULL,
    `cover_mime_type`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `cover_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `cover_dimensions`    longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `created_at`          datetime                                                      NOT NULL,
    `updated_at`          datetime                                                       DEFAULT NULL,
    `deleted_at`          datetime                                                       DEFAULT NULL,
    `views`               int                                                           NOT NULL,
    `showfollowers`       tinyint(1)                                                    NOT NULL,
    `isathletics`         tinyint(1)                                                    NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `UNIQ_2F97E190989D9B62` (`slug`),
    KEY `IDX_2F97E190A76ED395` (`user_id`),
    CONSTRAINT `FK_2F97E190A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 4
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_organizer`
--

LOCK TABLES `universe_organizer` WRITE;
/*!40000 ALTER TABLE `universe_organizer`
    DISABLE KEYS */;
INSERT INTO `universe_organizer`
VALUES (3, 2, 'cyber-club', 'Cyber Club', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Club', NULL,
        NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-01-31 20:40:04', '2024-01-31 20:40:04', NULL, 0, 1,
        0);
/*!40000 ALTER TABLE `universe_organizer`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_organizer_category`
--

DROP TABLE IF EXISTS `universe_organizer_category`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_organizer_category`
(
    `Organizer_id` int NOT NULL,
    `Category_Id`  int NOT NULL,
    PRIMARY KEY (`Organizer_id`, `Category_Id`),
    KEY `IDX_71F6C4319F5D9622` (`Organizer_id`),
    KEY `IDX_71F6C43115E3697` (`Category_Id`),
    CONSTRAINT `FK_71F6C43115E3697` FOREIGN KEY (`Category_Id`) REFERENCES `universe_category` (`id`),
    CONSTRAINT `FK_71F6C4319F5D9622` FOREIGN KEY (`Organizer_id`) REFERENCES `universe_organizer` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_organizer_category`
--

LOCK TABLES `universe_organizer_category` WRITE;
/*!40000 ALTER TABLE `universe_organizer_category`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_organizer_category`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_page`
--

DROP TABLE IF EXISTS `universe_page`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_page`
(
    `id`         int                                                           NOT NULL AUTO_INCREMENT,
    `content`    longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci     NOT NULL,
    `title`      varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `slug`       varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `updated_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `UNIQ_4B375C1E989D9B62` (`slug`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 9
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_page`
--

LOCK TABLES `universe_page` WRITE;
/*!40000 ALTER TABLE `universe_page`
    DISABLE KEYS */;
INSERT INTO `universe_page`
VALUES (1,
        '<p>The standard Lorem Ipsum passage, used since the 1500s</p><p>\"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\"</p><p><br /></p><p>Section 1.10.32 of \"de Finibus Bonorum et Malorum\", written by Cicero in 45 BC</p><p>\"Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?\"</p><p><br /></p><p>1914 translation by H. Rackham</p><p>\"But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure. To take a trivial example, which of us ever undertakes laborious physical exercise, except to obtain some advantage from it? But who has any right to find fault with a man who chooses to enjoy a pleasure that has no annoying consequences, or one who avoids a pain that produces no resultant pleasure?\"</p><p><br /></p><p><span style=\"font-size:.805rem;\">Section 1.10.33 of \"de Finibus Bonorum et Malorum\", written by Cicero in 45 BC</span><br /></p><p>\"At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.\"</p><p><br /></p><p>1914 translation by H. Rackham</p><p>\"On the other hand, we denounce with righteous indignation and dislike men who are so beguiled and demoralized by the charms of pleasure of the moment, so blinded by desire, that they cannot foresee the pain and trouble that are bound to ensue; and equal blame belongs to those who fail in their duty through weakness of will, which is the same as saying through shrinking from toil and pain. These cases are perfectly simple and easy to distinguish. In a free hour, when our power of choice is untrammelled and when nothing prevents our being able to do what we like best, every pleasure is to be welcomed and every pain avoided. But in certain circumstances and owing to the claims of duty or the obligations of business it will frequently occur that pleasures have to be repudiated and annoyances accepted. The wise man therefore always holds in these matters to this principle of selection: he rejects pleasures to secure other greater pleasures, or else he endures pains to avoid worse pains.\"</p>',
        'Terms of service', 'terms-of-service', NULL),
       (2,
        '<p style=\"font-size:12.88px;\">The standard Lorem Ipsum passage, used since the 1500s</p><p style=\"font-size:12.88px;\">\"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\"</p><p style=\"font-size:12.88px;\"><br /></p><p style=\"font-size:12.88px;\">Section 1.10.32 of \"de Finibus Bonorum et Malorum\", written by Cicero in 45 BC</p><p style=\"font-size:12.88px;\">\"Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?\"</p><p style=\"font-size:12.88px;\"><br /></p><p style=\"font-size:12.88px;\">1914 translation by H. Rackham</p><p style=\"font-size:12.88px;\">\"But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure. To take a trivial example, which of us ever undertakes laborious physical exercise, except to obtain some advantage from it? But who has any right to find fault with a man who chooses to enjoy a pleasure that has no annoying consequences, or one who avoids a pain that produces no resultant pleasure?\"</p><p style=\"font-size:12.88px;\"><br /></p><p style=\"font-size:12.88px;\"><span style=\"font-size:.805rem;\">Section 1.10.33 of \"de Finibus Bonorum et Malorum\", written by Cicero in 45 BC</span><br /></p><p style=\"font-size:12.88px;\">\"At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.\"</p><p style=\"font-size:12.88px;\"><br /></p><p style=\"font-size:12.88px;\">1914 translation by H. Rackham</p><p style=\"font-size:12.88px;\">\"On the other hand, we denounce with righteous indignation and dislike men who are so beguiled and demoralized by the charms of pleasure of the moment, so blinded by desire, that they cannot foresee the pain and trouble that are bound to ensue; and equal blame belongs to those who fail in their duty through weakness of will, which is the same as saying through shrinking from toil and pain. These cases are perfectly simple and easy to distinguish. In a free hour, when our power of choice is untrammelled and when nothing prevents our being able to do what we like best, every pleasure is to be welcomed and every pain avoided. But in certain circumstances and owing to the claims of duty or the obligations of business it will frequently occur that pleasures have to be repudiated and annoyances accepted. The wise man therefore always holds in these matters to this principle of selection: he rejects pleasures to secure other greater pleasures, or else he endures pains to avoid worse pains.\"</p>',
        'Privacy policy', 'privacy-policy', NULL),
       (3,
        '<p style=\"font-size:12.88px;\">The standard Lorem Ipsum passage, used since the 1500s</p><p style=\"font-size:12.88px;\">\"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\"</p><p style=\"font-size:12.88px;\"><br /></p><p style=\"font-size:12.88px;\">Section 1.10.32 of \"de Finibus Bonorum et Malorum\", written by Cicero in 45 BC</p><p style=\"font-size:12.88px;\">\"Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?\"</p><p style=\"font-size:12.88px;\"><br /></p><p style=\"font-size:12.88px;\">1914 translation by H. Rackham</p><p style=\"font-size:12.88px;\">\"But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure. To take a trivial example, which of us ever undertakes laborious physical exercise, except to obtain some advantage from it? But who has any right to find fault with a man who chooses to enjoy a pleasure that has no annoying consequences, or one who avoids a pain that produces no resultant pleasure?\"</p><p style=\"font-size:12.88px;\"><br /></p><p style=\"font-size:12.88px;\"><span style=\"font-size:.805rem;\">Section 1.10.33 of \"de Finibus Bonorum et Malorum\", written by Cicero in 45 BC</span><br /></p><p style=\"font-size:12.88px;\">\"At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.\"</p><p style=\"font-size:12.88px;\"><br /></p><p style=\"font-size:12.88px;\">1914 translation by H. Rackham</p><p style=\"font-size:12.88px;\">\"On the other hand, we denounce with righteous indignation and dislike men who are so beguiled and demoralized by the charms of pleasure of the moment, so blinded by desire, that they cannot foresee the pain and trouble that are bound to ensue; and equal blame belongs to those who fail in their duty through weakness of will, which is the same as saying through shrinking from toil and pain. These cases are perfectly simple and easy to distinguish. In a free hour, when our power of choice is untrammelled and when nothing prevents our being able to do what we like best, every pleasure is to be welcomed and every pain avoided. But in certain circumstances and owing to the claims of duty or the obligations of business it will frequently occur that pleasures have to be repudiated and annoyances accepted. The wise man therefore always holds in these matters to this principle of selection: he rejects pleasures to secure other greater pleasures, or else he endures pains to avoid worse pains.\"</p>',
        'Cookie policy', 'cookie-policy', NULL),
       (4,
        '<p style=\"font-size:12.88px;\">The standard Lorem Ipsum passage, used since the 1500s</p><p style=\"font-size:12.88px;\">\"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\"</p><p style=\"font-size:12.88px;\"><br /></p><p style=\"font-size:12.88px;\">Section 1.10.32 of \"de Finibus Bonorum et Malorum\", written by Cicero in 45 BC</p><p style=\"font-size:12.88px;\">\"Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?\"</p><p style=\"font-size:12.88px;\"><br /></p><p style=\"font-size:12.88px;\">1914 translation by H. Rackham</p><p style=\"font-size:12.88px;\">\"But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure. To take a trivial example, which of us ever undertakes laborious physical exercise, except to obtain some advantage from it? But who has any right to find fault with a man who chooses to enjoy a pleasure that has no annoying consequences, or one who avoids a pain that produces no resultant pleasure?\"</p><p style=\"font-size:12.88px;\"><br /></p><p style=\"font-size:12.88px;\"><span style=\"font-size:.805rem;\">Section 1.10.33 of \"de Finibus Bonorum et Malorum\", written by Cicero in 45 BC</span><br /></p><p style=\"font-size:12.88px;\">\"At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.\"</p><p style=\"font-size:12.88px;\"><br /></p><p style=\"font-size:12.88px;\">1914 translation by H. Rackham</p><p style=\"font-size:12.88px;\">\"On the other hand, we denounce with righteous indignation and dislike men who are so beguiled and demoralized by the charms of pleasure of the moment, so blinded by desire, that they cannot foresee the pain and trouble that are bound to ensue; and equal blame belongs to those who fail in their duty through weakness of will, which is the same as saying through shrinking from toil and pain. These cases are perfectly simple and easy to distinguish. In a free hour, when our power of choice is untrammelled and when nothing prevents our being able to do what we like best, every pleasure is to be welcomed and every pain avoided. But in certain circumstances and owing to the claims of duty or the obligations of business it will frequently occur that pleasures have to be repudiated and annoyances accepted. The wise man therefore always holds in these matters to this principle of selection: he rejects pleasures to secure other greater pleasures, or else he endures pains to avoid worse pains.\"</p>',
        'GDPR compliance', 'gdpr-compliance', NULL),
       (6,
        '<p style=\"font-size:12.88px;\">The standard Lorem Ipsum passage, used since the 1500s</p><p style=\"font-size:12.88px;\">\"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\"</p><p style=\"font-size:12.88px;\"><br /></p><p style=\"font-size:12.88px;\">Section 1.10.32 of \"de Finibus Bonorum et Malorum\", written by Cicero in 45 BC</p><p style=\"font-size:12.88px;\">\"Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?\"</p><p style=\"font-size:12.88px;\"><br /></p><p style=\"font-size:12.88px;\">1914 translation by H. Rackham</p><p style=\"font-size:12.88px;\">\"But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure. To take a trivial example, which of us ever undertakes laborious physical exercise, except to obtain some advantage from it? But who has any right to find fault with a man who chooses to enjoy a pleasure that has no annoying consequences, or one who avoids a pain that produces no resultant pleasure?\"</p><p style=\"font-size:12.88px;\"><br /></p><p style=\"font-size:12.88px;\"><span style=\"font-size:.805rem;\">Section 1.10.33 of \"de Finibus Bonorum et Malorum\", written by Cicero in 45 BC</span><br /></p><p style=\"font-size:12.88px;\">\"At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.\"</p><p style=\"font-size:12.88px;\"><br /></p><p style=\"font-size:12.88px;\">1914 translation by H. Rackham</p><p style=\"font-size:12.88px;\">\"On the other hand, we denounce with righteous indignation and dislike men who are so beguiled and demoralized by the charms of pleasure of the moment, so blinded by desire, that they cannot foresee the pain and trouble that are bound to ensue; and equal blame belongs to those who fail in their duty through weakness of will, which is the same as saying through shrinking from toil and pain. These cases are perfectly simple and easy to distinguish. In a free hour, when our power of choice is untrammelled and when nothing prevents our being able to do what we like best, every pleasure is to be welcomed and every pain avoided. But in certain circumstances and owing to the claims of duty or the obligations of business it will frequently occur that pleasures have to be repudiated and annoyances accepted. The wise man therefore always holds in these matters to this principle of selection: he rejects pleasures to secure other greater pleasures, or else he endures pains to avoid worse pains.\"</p>',
        'About us', 'about-us', NULL),
       (7, '<p>fsdfssdfs</p>', 'Help center', 'help-center', '2024-02-01 21:26:25'),
       (8, '<p><span style=\"font-size:12.88px;\">Announcements</span><br /></p>', 'Announcements', 'announcements',
        '2024-02-01 21:26:33');
/*!40000 ALTER TABLE `universe_page`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_reels`
--

DROP TABLE IF EXISTS `universe_reels`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_reels`
(
    `id`                  int                                                           NOT NULL AUTO_INCREMENT,
    `user_id`             int                                                           DEFAULT NULL,
    `slug`                varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `type`                varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `linkvideo`           varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `tags`                varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `video_file_name`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `title`               varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `content`             varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `main_image`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_name`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_size`          int                                                           DEFAULT NULL,
    `image_mime_type`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_dimensions`    longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `views`               int                                                           DEFAULT NULL,
    `hidden`              tinyint(1)                                                    NOT NULL,
    `created_at`          datetime                                                      NOT NULL,
    `updated_at`          datetime                                                      DEFAULT NULL,
    `deleted_at`          datetime                                                      DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `UNIQ_C6AE6E246FBC9426` (`tags`),
    UNIQUE KEY `UNIQ_C6AE6E24A76ED395` (`user_id`),
    CONSTRAINT `FK_C6AE6E24A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_reels`
--

LOCK TABLES `universe_reels` WRITE;
/*!40000 ALTER TABLE `universe_reels`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_reels`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_reels_likes`
--

DROP TABLE IF EXISTS `universe_reels_likes`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_reels_likes`
(
    `id`      int NOT NULL AUTO_INCREMENT,
    `user_id` int DEFAULT NULL,
    `reel_id` int DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `UNIQ_415BA829A76ED395` (`user_id`),
    UNIQUE KEY `UNIQ_415BA8297802C31` (`reel_id`),
    CONSTRAINT `FK_415BA8297802C31` FOREIGN KEY (`reel_id`) REFERENCES `universe_reels` (`id`) ON DELETE CASCADE,
    CONSTRAINT `FK_415BA829A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_reels_likes`
--

LOCK TABLES `universe_reels_likes` WRITE;
/*!40000 ALTER TABLE `universe_reels_likes`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_reels_likes`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_review`
--

DROP TABLE IF EXISTS `universe_review`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_review`
(
    `id`         int                                                           NOT NULL AUTO_INCREMENT,
    `event_id`   int                                                           DEFAULT NULL,
    `user_id`    int                                                           DEFAULT NULL,
    `rating`     int                                                           NOT NULL,
    `headline`   varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `slug`       varchar(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `details`    varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `created_at` datetime                                                      NOT NULL,
    `updated_at` datetime                                                      DEFAULT NULL,
    `deleted_at` datetime                                                      DEFAULT NULL,
    `visible`    tinyint(1)                                                    NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `UNIQ_3D9D9182989D9B62` (`slug`),
    KEY `IDX_3D9D918271F7E88B` (`event_id`),
    KEY `IDX_3D9D9182A76ED395` (`user_id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 4
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_review`
--

LOCK TABLES `universe_review` WRITE;
/*!40000 ALTER TABLE `universe_review`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_review`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_settings`
--

DROP TABLE IF EXISTS `universe_settings`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_settings`
(
    `id`    int                                                           NOT NULL AUTO_INCREMENT,
    `key`   varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `value` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 103
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_settings`
--

LOCK TABLES `universe_settings` WRITE;
/*!40000 ALTER TABLE `universe_settings`
    DISABLE KEYS */;
INSERT INTO `universe_settings`
VALUES (1, 'currency_ccy', 'USD'),
       (2, 'currency_symbol', '$'),
       (3, 'currency_position', 'right'),
       (4, 'feed_name', 'Events RSS feed'),
       (6, 'feed_description', 'Latest events'),
       (7, 'feed_events_limit', '100'),
       (8, 'img_loader', 'assets/img/placeholders/img-404.jpg'),
       (9, 'blog_comments_enabled', 'no'),
       (10, 'facebook_app_id', ''),
       (11, 'disqus_subdomain', ''),
       (12, 'newsletter_enabled', 'no'),
       (13, 'mailchimp_api_key', ''),
       (14, 'mailchimp_list_id', ''),
       (15, 'homepage_show_search_box', 'no'),
       (16, 'homepage_events_number', '12'),
       (17, 'homepage_categories_number', '8'),
       (18, 'homepage_blogposts_number', '8'),
       (19, 'homepage_show_call_to_action', 'no'),
       (20, 'show_terms_of_service_page', 'no'),
       (21, 'terms_of_service_page_content', 'terms_of_service_page_content'),
       (22, 'show_privacy_policy_page', 'yes'),
       (23, 'privacy_policy_page_content', 'privacy_policy_page_content'),
       (24, 'show_cookie_policy_page', 'yes'),
       (25, 'cookie_policy_page_content', 'cookie_policy_page_content'),
       (26, 'show_gdpr_compliance_page', 'yes'),
       (27, 'gdpr_compliance_page_content', 'gdpr_compliance_page_content'),
       (28, 'terms_of_service_page_slug', 'terms-of-service'),
       (29, 'privacy_policy_page_slug', 'terms-of-service'),
       (30, 'cookie_policy_page_slug', 'terms-of-service'),
       (31, 'gdpr_compliance_page_slug', 'terms-of-service'),
       (32, 'ticket_fee_online', '0.00'),
       (33, 'ticket_fee_pos', '0.00'),
       (34, 'checkout_timeleft', '1800'),
       (35, 'organizer_payout_paypal_enabled', 'yes'),
       (36, 'organizer_payout_stripe_enabled', 'yes'),
       (37, 'online_ticket_price_percentage_cut', '0'),
       (38, 'pos_ticket_price_percentage_cut', '0'),
       (39, 'blog_posts_per_page', '9'),
       (40, 'events_per_page', '24'),
       (41, 'show_map_button', 'no'),
       (42, 'show_calendar_button', 'yes'),
       (43, 'show_rss_feed_button', 'yes'),
       (44, 'show_category_filter', 'yes'),
       (45, 'show_location_filter', 'yes'),
       (46, 'show_date_filter', 'yes'),
       (47, 'show_ticket_price_filter', 'yes'),
       (48, 'show_audience_filter', 'yes'),
       (49, 'venue_comments_enabled', 'no'),
       (50, 'show_tickets_left_on_cart_modal', 'yes'),
       (51, 'website_name', 'Universe'),
       (52, 'website_slug', 'Universe'),
       (54, 'website_root_url', 'universe.alexandroskourtis.me'),
       (55, 'website_description_en', 'Say goodbye to media clutter with a one-stop seamless experience!'),
       (56, 'website_keywords_en', 'deree, acg, universe'),
       (57, 'contact_email', 'universeaug@gmail.com'),
       (58, 'contact_phone', '+306989442438'),
       (59, 'contact_email', '+123456789'),
       (60, 'contact_address', 'Gravias 6, Aghia Paraskevi'),
       (61, 'facebook_url', 'https://www.facebook.com/profile.php?id=100084289854344'),
       (62, 'instagram_url', 'https://www.instagram.com/acguniverse/'),
       (63, 'youtube_url', 'https://www.youtube.com/channel/UCsKRxeDrt98BoHCz247WQwg'),
       (64, 'twitter_url', 'https://twitter.com/acguniverse'),
       (65, 'primary_color', '#2E7599'),
       (66, 'no_reply_email', 'no-reply@universe.alexandroskourtis.me'),
       (67, 'show_back_to_top_button', 'yes'),
       (68, 'show_cookie_policy_bar', 'yes'),
       (69, 'custom_css', ''),
       (70, 'google_analytics_code', 'G-08KD17D9LL'),
       (71, 'website_description_fr', 'Gestion d\'événements et vente de billets'),
       (74, 'website_description_ar', 'إدارة الأحداث ومبيعات التذاكر'),
       (75, 'website_keywords_fr', 'organiser mon événement, billets en ligne, acheter des billets'),
       (78, 'website_keywords_ar', 'تنظيم الحدث الخاص بي ، التذاكر عبر الإنترنت ، شراء التذاكر'),
       (79, 'mail_server_transport', 'gmail'),
       (80, 'mail_server_host', 'smtp.gmail.com'),
       (81, 'mail_server_port', '465'),
       (82, 'mail_server_encryption', 'tls'),
       (83, 'mail_server_auth_mode', NULL),
       (84, 'mail_server_username', 'universeaug%40gmail.com'),
       (85, 'mail_server_password', 'yaxqvholtlpunudv'),
       (86, 'google_recaptcha_enabled', 'no'),
       (87, 'google_recaptcha_site_key', '113445586031-s85u7f3pseg8r7ro7hg07tqmrl62l2nr.apps.googleusercontent.com'),
       (88, 'google_recaptcha_secret_key', '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'),
       (89, 'social_login_facebook_enabled', 'no'),
       (90, 'social_login_facebook_id', ''),
       (91, 'social_login_facebook_secret', ''),
       (92, 'social_login_google_id', ''),
       (93, 'social_login_google_secret', ''),
       (94, 'social_login_google_enabled', 'no'),
       (95, 'app_environment', 'prod'),
       (96, 'maintenance_mode', '0'),
       (97, 'maintenance_mode_custom_message', ''),
       (98, 'app_theme', 'orange'),
       (99, 'app_layout', 'container'),
       (100, 'website_url', 'https://universe.alexandroskourtis.me'),
       (101, 'website_description_es', 'Gestión de eventos y venta de entradas'),
       (102, 'website_keywords_es', 'organizar mi evento, entradas online, comprar entradas');
/*!40000 ALTER TABLE `universe_settings`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_thread`
--

DROP TABLE IF EXISTS `universe_thread`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_thread`
(
    `id`              varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `permalink`       varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `is_commentable`  tinyint(1)                                                    NOT NULL,
    `num_comments`    int                                                           NOT NULL,
    `last_comment_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_thread`
--

LOCK TABLES `universe_thread` WRITE;
/*!40000 ALTER TABLE `universe_thread`
    DISABLE KEYS */;
INSERT INTO `universe_thread`
VALUES ('capture-the-flag-ctf-competition-i-b48189a1a7e1df5f511c',
        'https://universe.alexandroskourtis.me/event/capture-the-flag-ctf-competition-i-b48189a1a7e1df5f511c', 1, 0, NULL);
/*!40000 ALTER TABLE `universe_thread`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_ticket_reservation`
--

DROP TABLE IF EXISTS `universe_ticket_reservation`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_ticket_reservation`
(
    `id`              int      NOT NULL AUTO_INCREMENT,
    `eventticket_id`  int      DEFAULT NULL,
    `user_id`         int      DEFAULT NULL,
    `orderelement_id` int      DEFAULT NULL,
    `quantity`        int      DEFAULT NULL,
    `created_at`      datetime NOT NULL,
    `deleted_at`      datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_BB13D979182CEB62` (`eventticket_id`),
    KEY `IDX_BB13D979A76ED395` (`user_id`),
    KEY `IDX_BB13D979EE04F0C1` (`orderelement_id`),
    CONSTRAINT `FK_BB13D979182CEB62` FOREIGN KEY (`eventticket_id`) REFERENCES `universe_event_date_ticket` (`id`),
    CONSTRAINT `FK_BB13D979A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`),
    CONSTRAINT `FK_BB13D979EE04F0C1` FOREIGN KEY (`orderelement_id`) REFERENCES `universe_order_element` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_ticket_reservation`
--

LOCK TABLES `universe_ticket_reservation` WRITE;
/*!40000 ALTER TABLE `universe_ticket_reservation`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_ticket_reservation`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_user`
--

DROP TABLE IF EXISTS `universe_user`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_user`
(
    `id`                             int                                                           NOT NULL AUTO_INCREMENT,
    `organizer_id`                   int                                                            DEFAULT NULL,
    `isorganizeronhomepageslider_id` int                                                            DEFAULT NULL,
    `username`                       varchar(180) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `username_canonical`             varchar(180) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `email`                          varchar(180) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `email_canonical`                varchar(180) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `enabled`                        tinyint(1)                                                    NOT NULL,
    `salt`                           varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `password`                       varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `last_login`                     datetime                                                       DEFAULT NULL,
    `confirmation_token`             varchar(180) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `password_requested_at`          datetime                                                       DEFAULT NULL,
    `roles`                          longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci     NOT NULL COMMENT '(DC2Type:array)',
    `points`                         double                                                        NOT NULL,
    `slug`                           varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `studentid`                      varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci  DEFAULT NULL,
    `bio`                            varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `website`                        varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `phone`                          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `facebook`                       varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `tiktok`                         varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `twitter`                        varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `instagram`                      varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `linkedin`                       varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `youtubeurl`                     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `isbusinessweek`                 tinyint(1)                                                     DEFAULT NULL,
    `nolevel`                        tinyint(1)                                                     DEFAULT NULL,
    `sendsms`                        tinyint(1)                                                     DEFAULT NULL,
    `specialuser`                    tinyint(1)                                                     DEFAULT NULL,
    `showfavs`                       tinyint(1)                                                     DEFAULT NULL,
    `showattendeed`                  tinyint(1)                                                     DEFAULT NULL,
    `showcomments`                   tinyint(1)                                                     DEFAULT NULL,
    `gender`                         varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `firstname`                      varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `lastname`                       varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `street`                         varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `street2`                        varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `city`                           varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `state`                          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `postalcode`                     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `birthdate`                      date                                                           DEFAULT NULL,
    `avatar_name`                    varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `avatar_size`                    int                                                            DEFAULT NULL,
    `avatar_mime_type`               varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `avatar_original_name`           varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `avatar_dimensions`              longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `created_at`                     datetime                                                      NOT NULL,
    `updated_at`                     datetime                                                       DEFAULT NULL,
    `deleted_at`                     datetime                                                       DEFAULT NULL,
    `api_key`                        varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `logo_name`                      varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `logo_size`                      int                                                            DEFAULT NULL,
    `logo_mime_type`                 varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `logo_original_name`             varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `logo_dimensions`                longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `cover_name`                     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `cover_size`                     int                                                            DEFAULT NULL,
    `cover_mime_type`                varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `cover_original_name`            varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `cover_dimensions`               longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `selectedimage`                  varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci  DEFAULT NULL,
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
) ENGINE = InnoDB
  AUTO_INCREMENT = 3
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_user`
--

LOCK TABLES `universe_user` WRITE;
/*!40000 ALTER TABLE `universe_user`
    DISABLE KEYS */;
INSERT INTO `universe_user`
VALUES (1, NULL, NULL, 'administrator', 'administrator', 'administrator@yourdomain.com', 'administrator@yourdomain.com',
        1, 'IyGzEV46y9UDotJJUd6wjZ1xgBI7/rCRT3ZSi6Xc.Ag',
        '$2y$13$jxwSWnS/e/LFjSyzYVXKLOdlvtKljfxuFE25M6KB0MGo8jxhj4lXq', '2024-02-01 21:30:30', NULL, NULL,
        'a:2:{i:0;s:16:\"ROLE_SUPER_ADMIN\";i:1;s:18:\"ROLE_ADMINISTRATOR\";}', 0, 'administrator', '', NULL, NULL,
        NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, NULL, 'Administrator', 'Administrator', NULL,
        NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-09-27 08:01:04', '2024-02-01 21:30:30', NULL,
        NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
       (2, 3, NULL, 'dcAcyberclub123?', 'dcacyberclub123?', 'dcAcyberclub123?@acg.edu', 'dcacyberclub123?@acg.edu', 1,
        NULL, '$2y$13$OgSQugOrD4mpuzpsV3To/ux7p//aM.UMHF0fEkEqaYGxXbMGTAAbW', NULL,
        '7K2V5k-E_8BtZ9G7m-tLPmilBvjbV6Hxe0zY7Co472Q', NULL, 'a:1:{i:0;s:14:\"ROLE_ORGANIZER\";}', 0, 'dcacyberclub123',
        NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL,
        NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-01-31 20:40:04', '2024-01-31 20:40:04',
        NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
/*!40000 ALTER TABLE `universe_user`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_user_cv`
--

DROP TABLE IF EXISTS `universe_user_cv`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_user_cv`
(
    `id`                 int      NOT NULL AUTO_INCREMENT,
    `user_id`            int      NOT NULL,
    `file_name`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `file_size`          int                                                           DEFAULT NULL,
    `created_at`         datetime NOT NULL,
    `file_mime_type`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `updated_at`         datetime                                                      DEFAULT NULL,
    `file_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `UNIQ_7C34D5E5A76ED395` (`user_id`),
    CONSTRAINT `FK_7C34D5E5A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_user_cv`
--

LOCK TABLES `universe_user_cv` WRITE;
/*!40000 ALTER TABLE `universe_user_cv`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_user_cv`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_user_doc`
--

DROP TABLE IF EXISTS `universe_user_doc`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_user_doc`
(
    `id`                  int      NOT NULL AUTO_INCREMENT,
    `user_id`             int      NOT NULL,
    `created_at`          datetime NOT NULL,
    `updated_at`          datetime                                                      DEFAULT NULL,
    `file_name`           varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `file_size`           int                                                           DEFAULT NULL,
    `file_mime_type`      varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `file_original_name`  varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `file_name1`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `file_size1`          int                                                           DEFAULT NULL,
    `file_mime_type1`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `file_original_name1` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `UNIQ_48EA42D0A76ED395` (`user_id`),
    CONSTRAINT `FK_48EA42D0A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_user_doc`
--

LOCK TABLES `universe_user_doc` WRITE;
/*!40000 ALTER TABLE `universe_user_doc`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_user_doc`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_user_doc1`
--

DROP TABLE IF EXISTS `universe_user_doc1`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_user_doc1`
(
    `id`                 int      NOT NULL AUTO_INCREMENT,
    `user_id`            int      NOT NULL,
    `file_name`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `file_size`          int                                                           DEFAULT NULL,
    `created_at`         datetime NOT NULL,
    `file_mime_type`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `updated_at`         datetime                                                      DEFAULT NULL,
    `file_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `UNIQ_547D721A76ED395` (`user_id`),
    CONSTRAINT `FK_547D721A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_user_doc1`
--

LOCK TABLES `universe_user_doc1` WRITE;
/*!40000 ALTER TABLE `universe_user_doc1`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_user_doc1`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_user_doc2`
--

DROP TABLE IF EXISTS `universe_user_doc2`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_user_doc2`
(
    `id`                 int      NOT NULL AUTO_INCREMENT,
    `user_id`            int      NOT NULL,
    `file_name`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `file_size`          int                                                           DEFAULT NULL,
    `created_at`         datetime NOT NULL,
    `file_mime_type`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `updated_at`         datetime                                                      DEFAULT NULL,
    `file_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `UNIQ_9C4E869BA76ED395` (`user_id`),
    CONSTRAINT `FK_9C4E869BA76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_user_doc2`
--

LOCK TABLES `universe_user_doc2` WRITE;
/*!40000 ALTER TABLE `universe_user_doc2`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_user_doc2`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_venue`
--

DROP TABLE IF EXISTS `universe_venue`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_venue`
(
    `id`                  int                                                           NOT NULL AUTO_INCREMENT,
    `main_image`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `slug`                varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `availability`        varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `hidden`              tinyint(1)                                                    NOT NULL,
    `title`               varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `description`         longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
    `contact_email`       varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci  DEFAULT NULL,
    `created_at`          datetime                                                      NOT NULL,
    `image_name`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_size`          int                                                           DEFAULT NULL,
    `image_mime_type`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_dimensions`    longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `updated_at`          datetime                                                      NOT NULL,
    `deleted_at`          datetime                                                      DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `UNIQ_50AF3B4C989D9B62` (`slug`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_venue`
--

LOCK TABLES `universe_venue` WRITE;
/*!40000 ALTER TABLE `universe_venue`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_venue`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_venue_amenities`
--

DROP TABLE IF EXISTS `universe_venue_amenities`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_venue_amenities`
(
    `venue_id`   int NOT NULL,
    `amenity_id` int NOT NULL,
    PRIMARY KEY (`venue_id`, `amenity_id`),
    KEY `IDX_DEB650DC40A73EBA` (`venue_id`),
    KEY `IDX_DEB650DC9F9F1305` (`amenity_id`),
    CONSTRAINT `FK_DEB650DC40A73EBA` FOREIGN KEY (`venue_id`) REFERENCES `universe_venue` (`id`) ON DELETE CASCADE,
    CONSTRAINT `FK_DEB650DC9F9F1305` FOREIGN KEY (`amenity_id`) REFERENCES `universe_amenity` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_venue_amenities`
--

LOCK TABLES `universe_venue_amenities` WRITE;
/*!40000 ALTER TABLE `universe_venue_amenities`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_venue_amenities`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_venue_images`
--

DROP TABLE IF EXISTS `universe_venue_images`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_venue_images`
(
    `id`                  int NOT NULL AUTO_INCREMENT,
    `venue_id`            int                                                           DEFAULT NULL,
    `position`            int                                                           DEFAULT NULL,
    `image_name`          varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_size`          int                                                           DEFAULT NULL,
    `image_mime_type`     varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_original_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
    `image_dimensions`    longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci COMMENT '(DC2Type:simple_array)',
    `updated_at`          datetime                                                      DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_CD40B66F40A73EBA` (`venue_id`),
    CONSTRAINT `FK_CD40B66F40A73EBA` FOREIGN KEY (`venue_id`) REFERENCES `universe_venue` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_venue_images`
--

LOCK TABLES `universe_venue_images` WRITE;
/*!40000 ALTER TABLE `universe_venue_images`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_venue_images`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_volunteer_event`
--

DROP TABLE IF EXISTS `universe_volunteer_event`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_volunteer_event`
(
    `id`                int      NOT NULL AUTO_INCREMENT,
    `user_id`           int      DEFAULT NULL,
    `universe_event_id` int      NOT NULL,
    `message`           longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
    `created_at`        datetime NOT NULL,
    `updated_at`        datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_64EC18A76ED395` (`user_id`),
    CONSTRAINT `FK_64EC18A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_volunteer_event`
--

LOCK TABLES `universe_volunteer_event` WRITE;
/*!40000 ALTER TABLE `universe_volunteer_event`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_volunteer_event`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_volunteer_event_select_attendees`
--

DROP TABLE IF EXISTS `universe_volunteer_event_select_attendees`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_volunteer_event_select_attendees`
(
    `event_id` int NOT NULL,
    `user_id`  int NOT NULL,
    PRIMARY KEY (`event_id`, `user_id`),
    KEY `IDX_FBECCD3471F7E88B` (`event_id`),
    KEY `IDX_FBECCD34A76ED395` (`user_id`),
    CONSTRAINT `FK_FBECCD3471F7E88B` FOREIGN KEY (`event_id`) REFERENCES `universe_event` (`id`) ON DELETE CASCADE,
    CONSTRAINT `FK_FBECCD34A76ED395` FOREIGN KEY (`user_id`) REFERENCES `universe_user` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_volunteer_event_select_attendees`
--

LOCK TABLES `universe_volunteer_event_select_attendees` WRITE;
/*!40000 ALTER TABLE `universe_volunteer_event_select_attendees`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_volunteer_event_select_attendees`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `universe_vote`
--

DROP TABLE IF EXISTS `universe_vote`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `universe_vote`
(
    `id`         int      NOT NULL AUTO_INCREMENT,
    `comment_id` int DEFAULT NULL,
    `voter_id`   int DEFAULT NULL,
    `created_at` datetime NOT NULL,
    `value`      int      NOT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_52D6F5AF8697D13` (`comment_id`),
    KEY `IDX_52D6F5AEBB4B8AD` (`voter_id`),
    CONSTRAINT `FK_52D6F5AEBB4B8AD` FOREIGN KEY (`voter_id`) REFERENCES `universe_user` (`id`),
    CONSTRAINT `FK_52D6F5AF8697D13` FOREIGN KEY (`comment_id`) REFERENCES `universe_comment` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb3
  COLLATE = utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `universe_vote`
--

LOCK TABLES `universe_vote` WRITE;
/*!40000 ALTER TABLE `universe_vote`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `universe_vote`
    ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'universedb'
--

--
-- Dumping routines for database 'universedb'
--
/*!40103 SET TIME_ZONE = @OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE = @OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS = @OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES = @OLD_SQL_NOTES */;

/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

-- Dump completed on 2024-02-01 21:45:16
