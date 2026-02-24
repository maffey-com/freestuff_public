-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: localhost    Database: freestuff
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
-- Table structure for table `adsense`
--

CREATE DATABASE IF NOT EXISTS freestuff;

USE freestuff;


DROP TABLE IF EXISTS `adsense`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `adsense` (
  `serial` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `category_profile_mark`
--

DROP TABLE IF EXISTS `category_profile_mark`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category_profile_mark` (
  `profile_mark_id` int unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(45) NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `date` datetime NOT NULL,
  `ip_address` varchar(20) NOT NULL,
  PRIMARY KEY (`profile_mark_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19575817 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `contact_date` datetime NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `enquiry` text,
  `status` enum('New','Closed') NOT NULL DEFAULT 'New',
  `freestuff_action_date` datetime DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `reply` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3091 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `district`
--

DROP TABLE IF EXISTS `district`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `district` (
  `district_id` int NOT NULL AUTO_INCREMENT,
  `district` varchar(45) NOT NULL,
  `region` varchar(20) NOT NULL,
  PRIMARY KEY (`district_id`)
) ENGINE=InnoDB AUTO_INCREMENT=157 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_templates`
--

DROP TABLE IF EXISTS `email_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_templates` (
  `email_template_id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text,
  `from_name` varchar(255) DEFAULT NULL,
  `from_address` varchar(255) DEFAULT NULL,
  `reply_to_name` varchar(255) DEFAULT NULL,
  `reply_to_address` varchar(255) DEFAULT NULL,
  `bcc` varchar(255) DEFAULT NULL,
  `to_address` varchar(255) DEFAULT NULL,
  `to_name` varchar(255) DEFAULT NULL,
  `translated_code` text,
  `count` int unsigned DEFAULT NULL,
  PRIMARY KEY (`email_template_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_tracker`
--

DROP TABLE IF EXISTS `email_tracker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_tracker` (
  `email_tracker_id` int unsigned NOT NULL AUTO_INCREMENT,
  `date_sent` datetime NOT NULL,
  `template_name` varchar(255) NOT NULL DEFAULT '',
  `email_body` text NOT NULL,
  `to_address` varchar(255) NOT NULL,
  `email_subject` varchar(255) NOT NULL,
  `recipient_user_id` int unsigned NOT NULL,
  `staff_user_id` int unsigned NOT NULL,
  `from_address` varchar(255) NOT NULL,
  `email_blocked` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`email_tracker_id`),
  KEY `to_address` (`to_address`),
  KEY `user_id` (`recipient_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1523807 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `firebase_message`
--

DROP TABLE IF EXISTS `firebase_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `firebase_message` (
  `firebase_message_id` int NOT NULL AUTO_INCREMENT,
  `sent_date` datetime DEFAULT NULL,
  `user` varchar(45) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `body` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`firebase_message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3020 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `listing`
--

DROP TABLE IF EXISTS `listing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `listing` (
  `listing_id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `authorised` enum('y','n','p') NOT NULL DEFAULT 'p',
  `listing_date` datetime NOT NULL,
  `visits` int unsigned NOT NULL DEFAULT '0',
  `last_updated` datetime NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `has_image` enum('y','n') NOT NULL DEFAULT 'n',
  `user_firstname` varchar(255) NOT NULL,
  `removed_reason` varchar(255) DEFAULT NULL,
  `removed_date` datetime DEFAULT NULL,
  `reserved_date` datetime DEFAULT NULL,
  `pushed_to_twitter` datetime DEFAULT NULL,
  `pushed_to_facebook` datetime DEFAULT NULL,
  `listing_type` enum('free','wanted') NOT NULL DEFAULT 'free',
  `expiry_reminded` datetime DEFAULT NULL,
  `original_listing_date` datetime DEFAULT NULL,
  `request_count` tinyint DEFAULT NULL,
  `saved_search_date` datetime DEFAULT NULL,
  `saved_region_search_date` datetime DEFAULT NULL,
  `district_id` int DEFAULT NULL,
  `listing_status` enum('available','reserved','expired','gone','removed') NOT NULL DEFAULT 'available',
  PRIMARY KEY (`listing_id`),
  FULLTEXT KEY `ft` (`title`,`description`)
) ENGINE=InnoDB AUTO_INCREMENT=92766 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `listing_profile_mark`
--

DROP TABLE IF EXISTS `listing_profile_mark`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `listing_profile_mark` (
  `profile_mark_id` int unsigned NOT NULL AUTO_INCREMENT,
  `listing_id` int unsigned NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `date` datetime NOT NULL,
  `ip_address` varchar(20) NOT NULL,
  `platform` char(1) NOT NULL DEFAULT 'W',
  PRIMARY KEY (`profile_mark_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18382309 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `listing_request`
--

DROP TABLE IF EXISTS `listing_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `listing_request` (
  `request_id` int NOT NULL AUTO_INCREMENT,
  `listing_id` int DEFAULT NULL,
  `request_timestamp` datetime DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `user_firstname` varchar(45) DEFAULT NULL,
  `user_ip_address` varchar(45) DEFAULT NULL,
  `district_id` int DEFAULT NULL,
  PRIMARY KEY (`request_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=94315 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `login_profile_mark`
--

DROP TABLE IF EXISTS `login_profile_mark`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_profile_mark` (
  `login_id` int NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT NULL,
  `username` varchar(45) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`login_id`)
) ENGINE=InnoDB AUTO_INCREMENT=854657 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `message` (
  `message_id` int NOT NULL AUTO_INCREMENT,
  `conversation_key` varchar(45) NOT NULL,
  `sender_user_id` int NOT NULL,
  `receiver_user_id` int NOT NULL,
  `message` varchar(1024) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_notified` datetime DEFAULT NULL,
  `date_viewed` datetime DEFAULT NULL,
  `email_message_id` varchar(512) DEFAULT NULL,
  `request_id` int DEFAULT NULL,
  `is_latest` enum('y','n') DEFAULT NULL,
  PRIMARY KEY (`message_id`),
  KEY `latest` (`is_latest`),
  KEY `conversation_key` (`conversation_key`)
) ENGINE=InnoDB AUTO_INCREMENT=238511 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report`
--

DROP TABLE IF EXISTS `report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `report` (
  `report_id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `report_date` datetime NOT NULL,
  `report_comment` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `listing_id` int unsigned NOT NULL,
  `freestuff_comment` varchar(255) DEFAULT NULL,
  `status` enum('NEW','Listing Removed','Warning','Report Rejected','Wanted','Closed') NOT NULL DEFAULT 'NEW',
  `freestuff_action_date` datetime DEFAULT NULL,
  PRIMARY KEY (`report_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1778 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `saved_search`
--

DROP TABLE IF EXISTS `saved_search`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `saved_search` (
  `search_id` int NOT NULL AUTO_INCREMENT,
  `created_date` datetime DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `search_string` varchar(255) DEFAULT NULL,
  `regions` varchar(255) DEFAULT NULL,
  `listing_type` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`search_id`),
  FULLTEXT KEY `ftx` (`search_string`)
) ENGINE=InnoDB AUTO_INCREMENT=3130 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sms_outgoing`
--

DROP TABLE IF EXISTS `sms_outgoing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_outgoing` (
  `sms_id` int unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `phone_no` varchar(15) NOT NULL,
  `ip_address` varchar(15) NOT NULL,
  `sms_global_msg_id` varchar(45) DEFAULT NULL,
  `sms_global_error` varchar(255) DEFAULT NULL,
  `sms_global_callback` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`sms_id`)
) ENGINE=InnoDB AUTO_INCREMENT=83666 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stats_monthly`
--

DROP TABLE IF EXISTS `stats_monthly`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stats_monthly` (
  `month` int unsigned NOT NULL AUTO_INCREMENT,
  `listing_views` int unsigned DEFAULT NULL,
  `new_free_listings` int unsigned DEFAULT NULL,
  `new_users` int unsigned DEFAULT NULL,
  `mobile_validations` int unsigned DEFAULT NULL,
  `contacts` int unsigned DEFAULT NULL,
  `category_views` int unsigned DEFAULT NULL,
  `ad_views` int unsigned DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL,
  `new_wanted_listings` int unsigned DEFAULT NULL,
  `adsense_earnings` float DEFAULT NULL,
  PRIMARY KEY (`month`)
) ENGINE=InnoDB AUTO_INCREMENT=202509 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `thumb`
--

DROP TABLE IF EXISTS `thumb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `thumb` (
  `thumb_id` int NOT NULL AUTO_INCREMENT,
  `lister_id` int NOT NULL,
  `requester_id` int NOT NULL,
  `request_id` int DEFAULT NULL,
  `thumb_date` datetime NOT NULL,
  `thumb_ip` varchar(45) DEFAULT NULL,
  `up_down` enum('u','d','x') NOT NULL DEFAULT 'u',
  `credited_date` datetime DEFAULT NULL,
  PRIMARY KEY (`thumb_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4552 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `user_id` int unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(45) DEFAULT NULL,
  `firstname` varchar(45) NOT NULL,
  `created_on` datetime NOT NULL,
  `email_validated` datetime DEFAULT NULL,
  `mobile_validated` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `admin` enum('y','n') NOT NULL DEFAULT 'n',
  `staff` enum('y','n') NOT NULL DEFAULT 'n',
  `mailchimp_push` datetime DEFAULT NULL,
  `user_listing_count` int DEFAULT NULL,
  `user_request_count` int DEFAULT NULL,
  `thumbs_up` int DEFAULT NULL,
  `thumbs_down` int DEFAULT NULL,
  `user_status` enum('active','inactive','banned') DEFAULT 'active',
  `password_hash` varchar(255) DEFAULT NULL,
  `district_id` int DEFAULT NULL,
  `email_bounced_date` datetime DEFAULT NULL,
  `firebase_token` varchar(255) DEFAULT NULL,
  `os_version` enum('IOS','ANDROID') DEFAULT NULL,
  `brevo_id` int DEFAULT NULL,
  `brevo_status` varchar(100) DEFAULT NULL,
  `request_credit` tinyint NOT NULL DEFAULT '5',
  `request_credit_refresh_date` date DEFAULT NULL,
  `z_user_region` varchar(20) DEFAULT NULL,
  `brevo_push_date` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=162600 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_blocked`
--

DROP TABLE IF EXISTS `user_blocked`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_blocked` (
  `user_blocked_id` int NOT NULL AUTO_INCREMENT,
  `blocker_user_id` int NOT NULL,
  `blocked_user_id` int NOT NULL,
  `hide_messages` enum('y','n') NOT NULL,
  `date_blocked` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_blocked_id`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_history`
--

DROP TABLE IF EXISTS `user_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_history` (
  `user_history_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `field` varchar(128) DEFAULT NULL,
  `old_value` varchar(255) DEFAULT NULL,
  `new_value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_history_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5968 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_naughty`
--

DROP TABLE IF EXISTS `user_naughty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_naughty` (
  `naughty_id` int NOT NULL AUTO_INCREMENT,
  `email_hash_1` varchar(255) DEFAULT NULL,
  `email_hash_2` varchar(255) DEFAULT NULL,
  `mobile_hash_1` varchar(255) DEFAULT NULL,
  `mobile_hash_2` varchar(255) DEFAULT NULL,
  `naughty_date` datetime DEFAULT NULL,
  `naughty_offence` varchar(45) DEFAULT NULL,
  `naughty_note` varchar(255) DEFAULT NULL,
  `naughty_score` tinyint NOT NULL DEFAULT '20',
  PRIMARY KEY (`naughty_id`)
) ENGINE=InnoDB AUTO_INCREMENT=293 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_remember_me`
--

DROP TABLE IF EXISTS `user_remember_me`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_remember_me` (
  `remember_me_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `passkey` varchar(45) DEFAULT NULL,
  `remembered_date` datetime DEFAULT NULL,
  `last_used_date` datetime DEFAULT NULL,
  PRIMARY KEY (`remember_me_id`)
) ENGINE=InnoDB AUTO_INCREMENT=190913 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_verify`
--

DROP TABLE IF EXISTS `user_verify`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_verify` (
  `verify_id` varchar(8) NOT NULL,
  `six_digit_code` varchar(6) DEFAULT NULL,
  `verify_type` varchar(45) DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_checked` datetime DEFAULT NULL,
  `date_expired` datetime DEFAULT NULL,
  `data` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`verify_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


/*insert districts*/

INSERT INTO freestuff.district (district_id,district,region) VALUES
                                                                 (1,'Dargaville','Northland'),
                                                                 (2,'Kaikohe','Northland'),
                                                                 (3,'Kaitaia','Northland'),
                                                                 (4,'Kawakawa','Northland'),
                                                                 (5,'Kerikeri','Northland'),
                                                                 (6,'Mangawhai','Northland'),
                                                                 (7,'Maungaturoto','Northland'),
                                                                 (8,'Paihia','Northland'),
                                                                 (9,'Whangarei','Northland'),
                                                                 (10,'Albany','Auckland');
INSERT INTO freestuff.district (district_id,district,region) VALUES
                                                                 (11,'Auckland City','Auckland'),
                                                                 (12,'Botany Downs','Auckland'),
                                                                 (13,'Clevedon','Auckland'),
                                                                 (14,'Franklin','Auckland'),
                                                                 (15,'Great Barrier Island','Auckland'),
                                                                 (16,'Helensville','Auckland'),
                                                                 (17,'Henderson','Auckland'),
                                                                 (18,'Hibiscus Coast','Auckland'),
                                                                 (19,'Kumeu','Auckland'),
                                                                 (20,'Mangere','Auckland');
INSERT INTO freestuff.district (district_id,district,region) VALUES
                                                                 (21,'Manukau','Auckland'),
                                                                 (22,'New Lynn','Auckland'),
                                                                 (23,'North Shore','Auckland'),
                                                                 (24,'Onehunga','Auckland'),
                                                                 (25,'Papakura','Auckland'),
                                                                 (26,'Pukekohe','Auckland'),
                                                                 (27,'Remuera','Auckland'),
                                                                 (28,'Waiheke Island','Auckland'),
                                                                 (29,'Waitakere','Auckland'),
                                                                 (30,'Waiuku','Auckland');
INSERT INTO freestuff.district (district_id,district,region) VALUES
                                                                 (31,'Warkworth','Auckland'),
                                                                 (32,'Wellsford','Auckland'),
                                                                 (33,'Cambridge','Waikato'),
                                                                 (34,'Coromandel','Waikato'),
                                                                 (35,'Hamilton','Waikato'),
                                                                 (36,'Huntly','Waikato'),
                                                                 (37,'Matamata','Waikato'),
                                                                 (38,'Morrinsville','Waikato'),
                                                                 (39,'Ngaruawahia','Waikato'),
                                                                 (40,'Ngatea','Waikato');
INSERT INTO freestuff.district (district_id,district,region) VALUES
                                                                 (41,'Otorohanga','Waikato'),
                                                                 (42,'Paeroa','Waikato'),
                                                                 (43,'Raglan','Waikato'),
                                                                 (44,'Taumarunui','Waikato'),
                                                                 (45,'Taupo','Waikato'),
                                                                 (46,'Te Awamutu','Waikato'),
                                                                 (47,'Te Kuiti','Waikato'),
                                                                 (48,'Thames','Waikato'),
                                                                 (49,'Tokoroa/Putaruru','Waikato'),
                                                                 (50,'Turangi','Waikato');
INSERT INTO freestuff.district (district_id,district,region) VALUES
                                                                 (51,'Waihi','Waikato'),
                                                                 (52,'Whangamata','Waikato'),
                                                                 (53,'Whitianga','Waikato'),
                                                                 (54,'Katikati','Bay of Plenty'),
                                                                 (55,'Kawerau','Bay of Plenty'),
                                                                 (56,'Mt. Maunganui','Bay of Plenty'),
                                                                 (57,'Opotiki','Bay of Plenty'),
                                                                 (58,'Papamoa','Bay of Plenty'),
                                                                 (59,'Rotorua','Bay of Plenty'),
                                                                 (60,'Tauranga','Bay of Plenty');
INSERT INTO freestuff.district (district_id,district,region) VALUES
                                                                 (61,'Te Puke','Bay of Plenty'),
                                                                 (62,'Waihi Beach','Bay of Plenty'),
                                                                 (63,'Whakatane','Bay of Plenty'),
                                                                 (64,'Gisborne','Gisborne'),
                                                                 (65,'Ruatoria','Gisborne'),
                                                                 (66,'Hastings','Hawkes Bay'),
                                                                 (67,'Napier','Hawkes Bay'),
                                                                 (68,'Waipukurau','Hawkes Bay'),
                                                                 (69,'Wairoa','Hawkes Bay'),
                                                                 (70,'Hawera','Taranaki');
INSERT INTO freestuff.district (district_id,district,region) VALUES
                                                                 (71,'Mokau','Taranaki'),
                                                                 (72,'New Plymouth','Taranaki'),
                                                                 (73,'Opunake','Taranaki'),
                                                                 (74,'Stratford','Taranaki'),
                                                                 (75,'Ohakune','Manawatu-Wanganui'),
                                                                 (76,'Taihape','Manawatu-Wanganui'),
                                                                 (77,'Waiouru','Manawatu-Wanganui'),
                                                                 (78,'Whanganui','Manawatu-Wanganui'),
                                                                 (79,'Bulls','Manawatu-Wanganui'),
                                                                 (80,'Dannevirke','Manawatu-Wanganui');
INSERT INTO freestuff.district (district_id,district,region) VALUES
                                                                 (81,'Feilding','Manawatu-Wanganui'),
                                                                 (82,'Levin','Manawatu-Wanganui'),
                                                                 (83,'Manawatu','Manawatu-Wanganui'),
                                                                 (84,'Marton','Manawatu-Wanganui'),
                                                                 (85,'Pahiatua','Manawatu-Wanganui'),
                                                                 (86,'Palmerston North','Manawatu-Wanganui'),
                                                                 (87,'Woodville','Manawatu-Wanganui'),
                                                                 (88,'Carterton','Wellington'),
                                                                 (89,'Featherston','Wellington'),
                                                                 (90,'Greytown','Wellington');
INSERT INTO freestuff.district (district_id,district,region) VALUES
                                                                 (91,'Martinborough','Wellington'),
                                                                 (92,'Masterton','Wellington'),
                                                                 (93,'Kapiti','Wellington'),
                                                                 (94,'Lower Hutt City','Wellington'),
                                                                 (95,'Porirua','Wellington'),
                                                                 (96,'Upper Hutt City','Wellington'),
                                                                 (97,'Wellington City','Wellington'),
                                                                 (98,'Golden Bay','Nelson-Tasman'),
                                                                 (99,'Motueka','Nelson-Tasman'),
                                                                 (100,'Murchison','Nelson-Tasman');
INSERT INTO freestuff.district (district_id,district,region) VALUES
                                                                 (101,'Nelson City','Nelson-Tasman'),
                                                                 (102,'Richmond','Nelson-Tasman'),
                                                                 (103,'Stoke','Nelson-Tasman'),
                                                                 (104,'Blenheim','Marlborough'),
                                                                 (105,'Marlborough Sounds','Marlborough'),
                                                                 (106,'Picton','Marlborough'),
                                                                 (107,'Greymouth','West Coast'),
                                                                 (108,'Hokitika','West Coast'),
                                                                 (109,'Westport','West Coast'),
                                                                 (110,'Akaroa','Canterbury');
INSERT INTO freestuff.district (district_id,district,region) VALUES
                                                                 (111,'Amberley','Canterbury'),
                                                                 (112,'Ashburton','Canterbury'),
                                                                 (113,'Belfast','Canterbury'),
                                                                 (114,'Cheviot','Canterbury'),
                                                                 (115,'Christchurch City','Canterbury'),
                                                                 (116,'Darfield','Canterbury'),
                                                                 (117,'Fairlie','Canterbury'),
                                                                 (118,'Ferrymead','Canterbury'),
                                                                 (119,'Geraldine','Canterbury'),
                                                                 (120,'Halswell','Canterbury');
INSERT INTO freestuff.district (district_id,district,region) VALUES
                                                                 (121,'Hanmer Springs','Canterbury'),
                                                                 (122,'Kaiapoi','Canterbury'),
                                                                 (123,'Kaikoura','Canterbury'),
                                                                 (124,'Kurow','Canterbury'),
                                                                 (125,'Lyttelton','Canterbury'),
                                                                 (126,'Mt Cook','Canterbury'),
                                                                 (127,'Rangiora','Canterbury'),
                                                                 (128,'Rolleston','Canterbury'),
                                                                 (129,'Selwyn','Canterbury'),
                                                                 (130,'Timaru','Canterbury');
INSERT INTO freestuff.district (district_id,district,region) VALUES
                                                                 (131,'Twizel','Canterbury'),
                                                                 (132,'Waimate','Canterbury'),
                                                                 (133,'Alexandra','Otago'),
                                                                 (134,'Balclutha','Otago'),
                                                                 (135,'Cromwell','Otago'),
                                                                 (136,'Dunedin','Otago'),
                                                                 (137,'Lawrence','Otago'),
                                                                 (138,'Milton','Otago'),
                                                                 (139,'Oamaru','Otago'),
                                                                 (140,'Palmerston','Otago');
INSERT INTO freestuff.district (district_id,district,region) VALUES
                                                                 (141,'Queenstown','Otago'),
                                                                 (142,'Ranfurly','Otago'),
                                                                 (143,'Roxburgh','Otago'),
                                                                 (144,'Tapanui','Otago'),
                                                                 (145,'Wanaka','Otago'),
                                                                 (146,'Bluff','Southland'),
                                                                 (147,'Edendale','Southland'),
                                                                 (148,'Gore','Southland'),
                                                                 (149,'Invercargill','Southland'),
                                                                 (150,'Lumsden','Southland');
INSERT INTO freestuff.district (district_id,district,region) VALUES
                                                                 (151,'Otautau','Southland'),
                                                                 (152,'Riverton','Southland'),
                                                                 (153,'Stewart Island','Southland'),
                                                                 (154,'Te Anau','Southland'),
                                                                 (155,'Tokanui','Southland'),
                                                                 (156,'Winton','Southland');



INSERT INTO freestuff.`user` (user_id,email,mobile,firstname,created_on,email_validated,mobile_validated,last_login,admin,staff,mailchimp_push,user_listing_count,user_request_count,thumbs_up,thumbs_down,user_status,password_hash,district_id,email_bounced_date,firebase_token,os_version,brevo_id,brevo_status,request_credit,request_credit_refresh_date,z_user_region,brevo_push_date) VALUES
    (1,'admin@freestuff.co.nz','12345','admin','2010-12-24 08:51:03','2012-10-16 14:57:12','2023-11-16 09:29:07','2025-08-25 04:41:44','y','y','2011-05-16 15:10:33',653,17,3,0,'active','',10,NULL,'','IOS',70,'opened',5,'2025-08-13','Auckland','2023-12-15 08:58:02'),
    (2,'lister@freestuff.co.nz','55526272','lister','2010-12-24 08:51:03','2012-10-16 14:57:12','2023-11-16 09:29:07','2025-08-25 04:41:44','n','n','2011-05-16 15:10:33',653,17,3,0,'active','',10,NULL,'','IOS',70,'opened',5,'2025-08-13','Auckland','2023-12-15 08:58:02'),
    (3,'requester@freestuff.co.nz','62726311','requester','2010-12-24 08:51:03','2012-10-16 14:57:12','2023-11-16 09:29:07','2025-08-25 04:41:44','n','n','2011-05-16 15:10:33',653,17,3,0,'active','',10,NULL,'','IOS',70,'opened',5,'2025-08-13','Auckland','2023-12-15 08:58:02');

INSERT INTO freestuff.email_templates (email_template_id,name,subject,message,from_name,from_address,reply_to_name,reply_to_address,bcc,to_address,to_name,translated_code,count) VALUES
(2,'Successful Signup','Welcome to Freestuff','Welcome __firstname__

Freestuff is an exciting new site where everything is free.

Why give stuff away:
   1. Get rid of clutter
   2. Help out your fellow man (or woman)
   3. Recycle, help save the planet

To list an item go here: http://freestuff.co.nz/list

To browse freestuff, go here: http://www.freestuff.co.nz/

Your username is: __email__
Your password is: __password__

Cheers

The Freestuff Team','Freestuff','do-not-reply@freestuff.co.nz','Freestuff','do-not-reply@freestuff.co.nz',NULL,NULL,NULL,'',NULL),
(10,'Remove Listing','Your Listing has been removed','Dear __lister_firstname__

Your listing __listing_title__ has been removed from Freestuff.  For future reference regarding non permitted listings please refer to our terms and conditions page https://freestuff.co.nz/page/terms

__reason__

Regards

The Freestuff Team','Freestuff','do-not-reply@freestuff.co.nz','Freestuff','do-not-reply@freestuff.co.nz',NULL,NULL,NULL,'',NULL),
(11,'Ban User',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL),
(12,'Un-Ban User',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL),
(17,'Report email to Lister','Your Listing has been removed','Dear __lister_firstname__

Your listing __listing_title__ has been removed from Freestuff.  For future reference regarding non permitted listings please refer to our terms and conditions page http://freestuff.co.nz/page/terms

__reason__

Regards

The Freestuff Team','Freestuff','do-not-reply@freestuff.co.nz','Freestuff','do-not-reply@freestuff.co.nz',NULL,NULL,NULL,'',NULL),
(18,'Report email to Reporter','Thanks for the Heads Up','Hi __reporter_firstname__

Thank you for reporting the following listing on freestuff:
__listing_title__

__comment__

Kind Regards
The Freestuff Team','Freestuff','do-not-reply@freestuff.co.nz','Freestuff','do-not-reply@freestuff.co.nz',NULL,NULL,NULL,'',NULL),
(21,'Freestuff expiry reminder','Your listing on Freestuff has expired','Hi __lister_firstname__

You recently gave away the following stuff:
__listing_title__

To keep the site fresh, listings are automatically removed after 2 weeks.

If you still have the item and want to relist it, just click the link below:
__link__


Cheers

The Freestuff Team','Freestuff','no-reply@freestuff.co.nz','Freestuff','no-reply@freestuff.co.nz',NULL,NULL,NULL,'',NULL),
(22,'Wanted stuff expiry reminder','Your Freestuff listing has expired','Hi __lister_firstname__

You recently requested the following stuff:
__listing_title__

To keep the site fresh, listings are automatically removed after 2 weeks.

If you want to relist, just use the link below:
__link__


Cheers

The Freestuff Team','Freestuff','no-reply@freestuff.co.nz',NULL,'no-reply@freestuff.co.nz',NULL,NULL,NULL,'',NULL),
(25,'Reply web enquiry','Thanks for getting in touch',' Hi __name__

Thanks for getting in touch with your enquiry:
__enquiry__

Please see our response below:
__reply__

Kind Regards
The Freestuff Team




 ','Freestuff','do-not-reply@freestuff.co.nz','Freestuff','do-not-reply@freestuff.co.nz',NULL,NULL,NULL,'',NULL),
(28,'Saved Search Match','New listing on freestuff matching your search','Hi __firstname__

There is a new listing matching your search criteria:
__listing_title__
__description__
__link__

If you don''t want these emails, click this link to change your saved searches:
__saved_searches_link__
or click here to unsubscribe from all:
__unsubscribe_link__

Cheers
The Freestuff Team','Freestuff','no-reply@freestuff.co.nz','Freestuff','do-not-reply@freestuff.co.nz',NULL,NULL,NULL,'',NULL);
INSERT INTO freestuff.email_templates (email_template_id,name,subject,message,from_name,from_address,reply_to_name,reply_to_address,bcc,to_address,to_name,translated_code,count) VALUES
(31,'Listing New Request','Freestuff New Request','Hi __firstname__

__message__

Click here to view your listing:
__listing_url__



Kind Regards
The Freestuff Team','Freestuff','team@freestuff.co.nz','Freestuff','do-not-reply@freestuff.co.nz',NULL,NULL,NULL,'',NULL),
(32,'Request New Message','Freestuff New Message','Hi __firstname__

__message__

__action_block__



Kind Regards
The Freestuff Team','Freestuff','team@freestuff.co.nz','Freestuff','do-not-reply@freestuff.co.nz',NULL,NULL,NULL,'',NULL),
(33,'User Verify','Email verification Code','Hi There

Your verification code is: __code__

Alternately, use the link below to verify for email:
__link__

Kind Regards
The Freestuff Team','Freestuff','team@freestuff.co.nz',NULL,NULL,NULL,NULL,NULL,'',NULL),
(34,'Saved Region Match','New listings on freestuff in your area','Hi __firstname__

There are new listings in your region:
__listings__


If you don''t want these emails, click this link to change your saved searches:
__saved_searches_link__
or click here to unsubscribe from all:
__unsubscribe_link__

Cheers
The Freestuff Team','Freestuff','team@freestuff.co.nz','Freestuff','do-not-reply@freestuff.co.nz',NULL,NULL,NULL,'',NULL),
(35,'New Message','New Message','Hi __firstname__

__message__

Reply to the message by clicking the link below:
__reply_url__



__re__


Kind Regards
The Freestuff Team','Freestuff',NULL,NULL,NULL,NULL,NULL,NULL,'',NULL);

