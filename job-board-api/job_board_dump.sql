/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.14-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: JobBoard
-- ------------------------------------------------------
-- Server version	10.11.14-MariaDB-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `applications`
--

DROP TABLE IF EXISTS `applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `applications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `candidate_id` bigint(20) unsigned NOT NULL,
  `job_id` bigint(20) unsigned NOT NULL,
  `resume_path` varchar(255) NOT NULL,
  `cover_letter` text DEFAULT NULL,
  `status` enum('applied','interviewing','accepted','rejected') NOT NULL DEFAULT 'applied',
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `applications_candidate_id_foreign` (`candidate_id`),
  KEY `applications_job_id_foreign` (`job_id`),
  CONSTRAINT `applications_candidate_id_foreign` FOREIGN KEY (`candidate_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `applications_job_id_foreign` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `applications`
--

LOCK TABLES `applications` WRITE;
/*!40000 ALTER TABLE `applications` DISABLE KEYS */;
INSERT INTO `applications` VALUES
(1,9,8,'resumes/sample.pdf',NULL,'applied',0,'2026-04-30 04:20:36','2026-04-30 04:20:36'),
(2,10,9,'resumes/sample.pdf',NULL,'applied',0,'2026-04-30 04:20:36','2026-04-30 04:20:36'),
(3,11,7,'resumes/sample.pdf',NULL,'applied',0,'2026-04-30 04:20:36','2026-04-30 04:20:36'),
(4,12,4,'resumes/sample.pdf',NULL,'applied',0,'2026-04-30 04:20:36','2026-04-30 04:20:36'),
(5,13,5,'resumes/sample.pdf',NULL,'applied',0,'2026-04-30 04:20:36','2026-04-30 04:20:36'),
(6,14,1,'resumes/sample.pdf',NULL,'applied',0,'2026-04-30 04:20:36','2026-04-30 04:20:36'),
(7,15,1,'resumes/sample.pdf',NULL,'applied',0,'2026-04-30 04:20:36','2026-04-30 04:20:36'),
(8,16,8,'resumes/sample.pdf',NULL,'applied',0,'2026-04-30 04:20:36','2026-04-30 04:20:36'),
(9,17,8,'resumes/sample.pdf',NULL,'applied',0,'2026-04-30 04:20:36','2026-04-30 04:20:36'),
(10,18,11,'resumes/sample.pdf',NULL,'applied',0,'2026-04-30 04:20:36','2026-04-30 04:20:36'),
(11,2,1,'resumes/6kgmrH1CnANFAK8RYWvO2GmkBzkl7vYPSOLHNEuH.pdf','\"I am a PHP developer with 3 years of experience...\"','applied',0,'2026-04-30 04:20:43','2026-04-30 04:20:43'),
(12,2,16,'resumes/e9VcdQSDMtPWVEJIhhCMjA368G5aEkqwIN4Ikxkm.pdf','\"I am a PHP developer with 3 years of experience...\"','interviewing',0,'2026-04-30 04:37:05','2026-04-30 04:44:36');
/*!40000 ALTER TABLE `applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES
(1,'Programming','programming','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(2,'Design','design','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(3,'Marketing','marketing','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(4,'Writing','writing','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(5,'Sales','sales','2026-04-30 04:20:36','2026-04-30 04:20:36');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `job_id` bigint(20) unsigned NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comments_user_id_foreign` (`user_id`),
  KEY `comments_job_id_foreign` (`job_id`),
  CONSTRAINT `comments_job_id_foreign` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES
(1,1,1,'Esse dolor reprehenderit et consequuntur corrupti libero iure excepturi qui et suscipit.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(2,1,1,'Eum aut voluptates voluptatibus id a quis ut est eligendi praesentium provident.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(3,1,1,'Sint earum sint autem nemo qui sed.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(4,1,1,'Perferendis vel nulla delectus incidunt fuga sunt.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(5,1,1,'A velit nemo impedit beatae ullam laudantium voluptate.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(6,9,2,'Iste iure eligendi repudiandae dolore aspernatur dolorum sunt ipsam eum enim quia.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(7,9,2,'Neque libero temporibus sit quisquam nam iure ut at est aut laborum recusandae.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(8,9,2,'Ut magni nesciunt ratione quo dolorem voluptatem quibusdam nulla placeat velit eum.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(9,15,3,'Qui quaerat aut odio voluptas iusto mollitia.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(10,15,3,'Deserunt eius voluptate placeat accusantium alias in.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(11,15,3,'Aliquam ut quisquam minus dolorem quidem non.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(12,15,3,'Culpa odio vel ea soluta aspernatur non at sed.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(13,15,3,'Voluptatem optio doloribus doloremque corrupti qui autem eos quas.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(14,4,4,'Delectus quia dolorem quibusdam omnis quo dolores voluptatem quas aliquam adipisci.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(15,4,4,'Eos et qui sed provident sed amet dignissimos asperiores.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(16,18,5,'Et aut autem qui quis veniam ducimus asperiores veniam sequi quasi enim.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(17,18,5,'Qui possimus est et perspiciatis in voluptate tempore consequatur fugiat maiores harum incidunt.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(18,18,5,'Commodi qui quia sed aut iste nobis quaerat ad.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(19,18,5,'Illum quo placeat architecto explicabo expedita totam molestiae neque sint officiis.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(20,3,6,'Velit animi libero consequatur omnis ipsum et sed quam id nam.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(21,3,6,'Cupiditate enim sint debitis velit commodi occaecati sed similique quo facere qui quia.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(22,13,7,'Corporis aliquid eum aliquid aut officia nostrum rerum.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(23,13,7,'Deserunt amet nisi eum rerum ea minima et dolore.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(24,13,7,'Quod itaque autem cupiditate et quis dolor laudantium.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(25,13,7,'Tempora ut fugiat velit velit architecto saepe laboriosam deleniti.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(26,13,7,'Nemo soluta velit eveniet nulla sunt est illo saepe sunt velit et.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(27,9,8,'Ut esse est non voluptas optio est omnis est.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(28,9,8,'Minus at quidem est qui dolorem earum reiciendis repudiandae natus doloribus perspiciatis.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(29,11,9,'Amet vel qui vero et quia dolor ipsam voluptatibus ex facere.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(30,11,9,'Blanditiis unde nemo voluptatem et ducimus vero ad.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(31,11,9,'Ab unde tempore placeat et id atque fugit est architecto nostrum.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(32,11,9,'Quod est rerum non recusandae illum qui qui expedita quae voluptatem.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(33,11,9,'Nam alias facilis deleniti illum et aut quo voluptatum minima corporis.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(34,12,10,'Amet dolor doloremque nesciunt est quia rerum eum.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(35,12,10,'Tempore reprehenderit et voluptatibus autem commodi ut rerum itaque velit in quia qui.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(36,12,10,'Esse ut dolorem porro eum odio qui.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(37,12,10,'Mollitia modi est dolores id earum itaque quia molestias amet qui.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(38,16,11,'Et qui necessitatibus consequuntur voluptate sint molestiae quod.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(39,16,11,'Praesentium exercitationem et culpa excepturi voluptate mollitia minus ea.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(40,16,11,'Quis mollitia vel aliquam fuga eveniet cum deserunt dolore dignissimos.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(41,16,11,'Et ratione error numquam voluptas distinctio velit impedit sint odit quia sapiente voluptatem.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(42,8,12,'Velit sequi libero omnis voluptatum dolorum eius voluptatem animi in unde et molestiae modi.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(43,8,12,'Quia tenetur iste iste soluta numquam eveniet quaerat ipsam quis.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(44,8,12,'Quia doloribus est libero et doloribus est molestiae.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(45,11,13,'Magni quo et aliquid sed quod id qui.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(46,11,13,'Doloribus nam sequi temporibus quod non maiores minus autem.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(47,1,14,'Repudiandae ullam qui officiis odit molestiae et dolores corrupti.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(48,1,14,'Amet voluptates minus consequatur iure consequuntur dignissimos non error occaecati earum.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(49,1,14,'Odit vero atque expedita ipsam ipsum possimus et illo accusantium atque.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(50,1,14,'Doloribus distinctio non repudiandae quia laudantium ullam assumenda id iure error.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(51,7,15,'Quas est illum voluptas dicta fugiat molestiae.','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(52,7,15,'Ea officia aut placeat dicta harum in et modi voluptates.','2026-04-30 04:20:36','2026-04-30 04:20:36');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employer_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `responsibilities` text NOT NULL,
  `requirements` text NOT NULL,
  `skills` varchar(255) NOT NULL,
  `salary_min` decimal(10,2) DEFAULT NULL,
  `salary_max` decimal(10,2) DEFAULT NULL,
  `work_type` enum('remote','onsite','hybrid') NOT NULL,
  `location` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `company_logo` varchar(255) DEFAULT NULL,
  `deadline` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_employer_id_foreign` (`employer_id`),
  KEY `jobs_category_id_foreign` (`category_id`),
  CONSTRAINT `jobs_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `jobs_employer_id_foreign` FOREIGN KEY (`employer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES
(1,4,1,'Machine Feeder','Ut necessitatibus omnis vitae eveniet dignissimos. Error quidem et minus est dolor excepturi dolorem necessitatibus. Aut ut qui qui incidunt aut voluptas assumenda. Eius ut nihil laborum aut.\n\nQuia dicta suscipit ea sint est blanditiis. Assumenda iste officiis sint vitae enim.\n\nEt debitis earum atque labore voluptatem consequatur. Nam pariatur voluptatibus eveniet et ipsum ad. Consectetur et sapiente non non magni qui voluptas.','Provident quia autem ducimus eius ipsam iusto itaque.','Minima cupiditate error aliquam accusantium.','PHP, Laravel, Vue.js',2913.00,4473.00,'remote','South Rollin','approved',NULL,'2026-05-30 04:20:36','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(2,4,1,'Marine Cargo Inspector','Odio voluptate dolore officia sed minima. Sint ad sit expedita. Dolores eveniet sunt est saepe mollitia maxime dolor. Et maxime rerum velit ut illo repudiandae. Tempora veniam atque quae odit ducimus perferendis aspernatur.\n\nQuisquam et commodi vero aut exercitationem. Dolorem aut quod magni accusamus. Quos sequi quia commodi. Inventore tempore sapiente quisquam.\n\nFacilis voluptas velit est sit. Omnis autem ipsam inventore eos laboriosam vitae reiciendis. Itaque amet rem et.','Quaerat aut qui adipisci esse illum.','Voluptatum quo quia amet et voluptatem voluptatem.','PHP, Laravel, Vue.js',1012.00,6183.00,'hybrid','Lenoreville','approved',NULL,'2026-05-30 04:20:36','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(3,4,1,'Agricultural Equipment Operator','Expedita ut excepturi quis dicta. Molestiae laboriosam ea earum accusamus sapiente est perferendis magni. Harum et repellat dolores molestiae iure voluptatem minus.\n\nVel nobis nostrum ducimus praesentium repellat et. Officiis ut debitis iure eveniet vel asperiores fuga. Et reiciendis error quia nisi aut esse vitae. Minima necessitatibus non debitis eum sed explicabo iure sed.\n\nDolores voluptatem laudantium officiis qui pariatur. Maiores assumenda omnis qui consequuntur neque cupiditate provident id. Voluptatibus sint debitis cum aut earum et nesciunt. Corrupti quam neque sunt soluta dicta.','Voluptate qui et id architecto.','Soluta maxime quos est velit laudantium.','PHP, Laravel, Vue.js',1143.00,4885.00,'onsite','New Monserrate','approved',NULL,'2026-05-30 04:20:36','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(4,5,3,'Sociologist','Voluptatum assumenda dignissimos deserunt voluptatem ullam sit. Ut corrupti esse provident est et unde ipsum. Rem eum fugit et. Distinctio doloribus qui explicabo vel.\n\nDolorum odio aut mollitia id ipsam recusandae iure. Distinctio veritatis ut harum quae fugit. Quasi aspernatur rerum iste ut. Et rerum id tenetur possimus enim repudiandae libero.\n\nQuia aut quibusdam sit dolore autem voluptatem ipsum dolorem. Omnis est molestiae placeat eum. Ullam asperiores accusantium pariatur harum voluptatem sint praesentium.','Repellat veniam eos eveniet id.','Illum excepturi dicta atque excepturi.','PHP, Laravel, Vue.js',2682.00,4239.00,'hybrid','Geovanyfort','approved',NULL,'2026-05-30 04:20:36','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(5,5,3,'Communication Equipment Worker','Inventore et enim totam veniam commodi velit et. Veritatis incidunt expedita explicabo non fuga perferendis officiis ut. Nostrum cum maxime facilis asperiores. Ipsa repellendus ut sint laborum esse quia.\n\nReiciendis laudantium doloribus velit totam veritatis ut. Et sunt ut qui odit autem. Praesentium temporibus velit dignissimos neque quidem sint quia dolor.\n\nEt quo modi sint minus. Ducimus qui et rem libero.','Iure deserunt veritatis molestias et rem minus.','Eum voluptas ullam quaerat reiciendis ducimus delectus autem.','PHP, Laravel, Vue.js',2636.00,6672.00,'remote','Mayerton','approved',NULL,'2026-05-30 04:20:36','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(6,5,3,'Agricultural Sales Representative','Consequatur dicta vel provident exercitationem. Aut facilis unde aspernatur et vel vel. Possimus aliquam culpa nam modi iste.\n\nEt eius at est. Rerum neque culpa repudiandae sit nihil illo neque. Aut accusantium doloribus fugit quo.\n\nAssumenda corrupti placeat cum ullam enim. Deleniti quibusdam sapiente quia nostrum magni. Dolorem possimus eos porro dolorem tenetur est odit veniam.','Laboriosam fuga sit voluptatibus recusandae numquam debitis.','Error ipsum quae aut ex optio.','PHP, Laravel, Vue.js',1379.00,5713.00,'hybrid','Dellaville','approved',NULL,'2026-05-30 04:20:36','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(7,6,3,'Skin Care Specialist','Numquam quas ab magnam aliquid asperiores mollitia numquam sint. Voluptatem nisi sint ut nihil. Et libero voluptas suscipit velit.\n\nDoloribus nisi possimus officiis officiis est. Incidunt occaecati aliquam amet exercitationem et. Animi velit libero laudantium qui quasi. Ut id alias similique nisi impedit amet. Perspiciatis et aperiam corporis ut consequuntur mollitia vel.\n\nEarum est autem illum incidunt modi quibusdam nulla. Repellendus vero qui minus laboriosam. Vitae soluta culpa in aliquid et velit.','Aliquid qui temporibus molestiae quis quae.','Qui nihil similique pariatur maiores qui numquam vero.','PHP, Laravel, Vue.js',2305.00,7305.00,'hybrid','Bogisichburgh','approved',NULL,'2026-05-30 04:20:36','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(8,6,3,'Graduate Teaching Assistant','Eveniet quae libero qui. Dolore deserunt aliquid perferendis nihil quam.\n\nNihil ipsam sunt vero quidem doloremque nostrum. Qui reprehenderit facilis sed fugiat veritatis voluptatem. Qui et id quia neque quod consequatur.\n\nOfficiis suscipit animi vero placeat. Molestiae perspiciatis temporibus ea debitis eius non. Et aut ut incidunt officia natus neque itaque quis.','Ipsa et dignissimos quisquam explicabo velit dolores sit.','Ex quae consectetur a voluptas cum rerum aut.','PHP, Laravel, Vue.js',1930.00,5074.00,'remote','West Marlinmouth','approved',NULL,'2026-05-30 04:20:36','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(9,6,3,'Pest Control Worker','Facilis ipsam ut unde omnis. Velit odit et dolorem fugiat praesentium libero quod.\n\nEst impedit suscipit nihil quia. Et ut optio suscipit voluptatibus non. Quam facilis repudiandae est dolor porro asperiores sit. Repellendus esse sed error accusantium sit odio porro.\n\nNostrum dolores qui et qui dicta. Consequatur quia nobis id. Sapiente numquam consequatur recusandae ut.','Ad qui omnis quis.','Exercitationem ipsa exercitationem consequatur veniam non.','PHP, Laravel, Vue.js',1710.00,4585.00,'onsite','Santinoberg','approved',NULL,'2026-05-30 04:20:36','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(10,7,2,'Precision Aircraft Systems Assemblers','Aut ea quas voluptatibus. Excepturi dolore aut et sit atque magni in. Esse ut repudiandae nesciunt voluptatem quos. Sint ipsa autem placeat aut enim quidem sequi. Dolorum pariatur earum voluptas quibusdam ullam.\n\nExcepturi facere nihil et inventore. Consectetur incidunt omnis ex eius ut cupiditate. Praesentium rerum omnis fugiat explicabo. Repellendus qui eius dolores sunt.\n\nEst consequatur nemo ea aspernatur soluta tenetur. Et doloremque est temporibus explicabo. Molestias sed expedita modi inventore voluptate. Voluptatibus placeat eos omnis et omnis.','Velit ratione id nobis voluptatibus deleniti.','Ex corrupti animi sunt non.','PHP, Laravel, Vue.js',2190.00,6144.00,'hybrid','North Gust','approved',NULL,'2026-05-30 04:20:36','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(11,7,2,'Veterinary Technician','Nulla totam quas qui exercitationem eveniet minus. Velit error iste nulla. Perferendis atque rem maiores consequatur et. Numquam nostrum quidem enim possimus non.\n\nEt necessitatibus sit quae ducimus quae eos qui. Doloremque placeat illum quia quos illum incidunt at. Porro dolorem ullam numquam in optio sit soluta.\n\nConsequuntur temporibus dolores vel libero maiores ullam quisquam. Placeat ex animi deserunt ad blanditiis esse. Qui blanditiis facilis commodi mollitia eos dolores iste perferendis.','Et voluptate et voluptatem.','Officia sequi sunt voluptate atque voluptas.','PHP, Laravel, Vue.js',2333.00,5959.00,'remote','Astridbury','approved',NULL,'2026-05-30 04:20:36','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(12,7,2,'Conservation Scientist','Ratione commodi velit quo voluptate aut. Quam quod enim et magni mollitia officiis et. Ut veritatis placeat sunt magni nostrum illo. Nesciunt voluptatem corrupti et aut blanditiis.\n\nCumque voluptas eos commodi vel est dolorem illo. Et omnis quas dignissimos eligendi officiis. Eveniet similique omnis consequatur debitis.\n\nFuga nisi natus et sit. Exercitationem est sapiente perferendis. Qui non et ipsa laborum sed perspiciatis expedita.','Et amet sed ex eum itaque nemo.','Tempore quidem omnis dolor.','PHP, Laravel, Vue.js',2911.00,6842.00,'hybrid','North Roselyn','approved',NULL,'2026-05-30 04:20:36','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(13,8,1,'Electrician','Amet illum autem quia et dolore quis praesentium. Laudantium quia sed ea. Optio rem molestiae iusto sed.\n\nMinima et sed nihil. Enim ut fugit possimus explicabo. Et consequatur illum consectetur vitae aut. Qui voluptates quo aut voluptas et ullam.\n\nVoluptate voluptatum adipisci dolorem sit. Iusto aut ea ducimus odit repudiandae blanditiis qui.','Deserunt numquam et et vero blanditiis rem.','Aut totam laudantium asperiores et est aut eum.','PHP, Laravel, Vue.js',1338.00,4392.00,'remote','Nathanaelhaven','approved',NULL,'2026-05-30 04:20:36','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(14,8,1,'Logging Supervisor','Sunt qui aspernatur possimus. Quia distinctio aut voluptas. Enim libero a consequatur corrupti voluptate natus.\n\nVoluptas quia recusandae consequatur blanditiis. Expedita vel hic quam facilis nam ut sunt.\n\nSoluta quae quibusdam quisquam quia. Molestiae eaque et est quia modi. Maiores voluptatem sit sint eaque. Nihil quas quidem maiores voluptatem.','Exercitationem quibusdam repellendus reprehenderit reprehenderit.','Iure aliquam repellendus quia laborum inventore.','PHP, Laravel, Vue.js',2695.00,5938.00,'hybrid','Ryanland','approved',NULL,'2026-05-30 04:20:36','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(15,8,1,'Mechanical Drafter','Porro et rerum magnam ut qui est. Quod et odit qui dolore aut et. Velit modi eum esse optio.\n\nDoloremque velit excepturi porro cupiditate voluptatem labore nobis. Deserunt hic et a repellat dolorem. Alias consequuntur tenetur et facilis eum magni dolorem. Neque quia voluptatem quibusdam repellendus natus mollitia. Fuga quod cupiditate pariatur non et.\n\nMolestiae rerum aspernatur quaerat aliquid ut quia. Et odit et aliquam ratione ipsum dolores eius. Vel molestiae quidem maiores assumenda est fugit quia quia. Voluptatem enim voluptas sed voluptatem dolores quam error earum.','Consequatur quia quam ut aut qui.','Animi nam necessitatibus dolorum excepturi.','PHP, Laravel, Vue.js',2613.00,7660.00,'onsite','West Jovannyfort','approved',NULL,'2026-05-30 04:20:36','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(16,1,1,'Laravel Developer','Pro PHP developer needed','API development','Laravel experience','PHP, Laravel, MySQL, Git',NULL,NULL,'remote','Cairo, Egypt','pending',NULL,NULL,'2026-04-30 04:33:34','2026-04-30 04:33:34');
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'2026_04_29_223345_create_permission_tables',1),
(4,'2026_04_29_223714_create_categories_table',1),
(5,'2026_04_29_223825_create_jobs_table',1),
(6,'2026_04_29_224524_create_applications_table',1),
(7,'2026_04_29_225018_create_payments_table',1),
(8,'2026_04_29_225048_create_comments_table',1),
(9,'2026_04_30_022142_create_personal_access_tokens_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES
(1,'App\\Models\\User',1),
(2,'App\\Models\\User',3),
(2,'App\\Models\\User',4),
(2,'App\\Models\\User',5),
(2,'App\\Models\\User',6),
(2,'App\\Models\\User',7),
(2,'App\\Models\\User',8),
(3,'App\\Models\\User',2),
(3,'App\\Models\\User',9),
(3,'App\\Models\\User',10),
(3,'App\\Models\\User',11),
(3,'App\\Models\\User',12),
(3,'App\\Models\\User',13),
(3,'App\\Models\\User',14),
(3,'App\\Models\\User',15),
(3,'App\\Models\\User',16),
(3,'App\\Models\\User',17),
(3,'App\\Models\\User',18);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employer_id` bigint(20) unsigned NOT NULL,
  `application_id` bigint(20) unsigned NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `currency` varchar(255) NOT NULL DEFAULT 'USD',
  `payment_method` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_transaction_id_unique` (`transaction_id`),
  KEY `payments_employer_id_foreign` (`employer_id`),
  KEY `payments_application_id_foreign` (`application_id`),
  CONSTRAINT `payments_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`),
  CONSTRAINT `payments_employer_id_foreign` FOREIGN KEY (`employer_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES
(1,'App\\Models\\User',2,'auth_token','afca6bcafd4bb7bbaf5aec12df2a2e1aebcd59e3b89c3519b50519ae8898ffe1','[\"*\"]','2026-04-30 04:20:46',NULL,'2026-04-30 04:20:39','2026-04-30 04:20:46'),
(2,'App\\Models\\User',1,'auth_token','3cfe6fc181849bfe5de0efac117e330e538a6a94c6c63c262f45a88521718817','[\"*\"]','2026-04-30 04:22:19',NULL,'2026-04-30 04:20:58','2026-04-30 04:22:19'),
(3,'App\\Models\\User',1,'auth_token','89f04a12d02e54d37af8ef72317e39b3cf7a90e757b1ef22e3914c30d827366a','[\"*\"]','2026-04-30 04:25:18',NULL,'2026-04-30 04:22:26','2026-04-30 04:25:18'),
(4,'App\\Models\\User',2,'auth_token','4c1fb6c0f9b212b422580e44ffceb4754185658f8e582286c059261f83f83272','[\"*\"]','2026-04-30 04:27:53',NULL,'2026-04-30 04:25:27','2026-04-30 04:27:53'),
(5,'App\\Models\\User',3,'auth_token','ce6b66bb04284c0a5364d5167f65e291fd6a190b9ede59430f63d605ea4dc3f2','[\"*\"]','2026-04-30 04:34:52',NULL,'2026-04-30 04:27:58','2026-04-30 04:34:52'),
(6,'App\\Models\\User',1,'auth_token','9dde56956aa6abd1bfcebf6cb858f8a6f9cf170fe886f507dbc44522381239c7','[\"*\"]','2026-04-30 04:35:33',NULL,'2026-04-30 04:34:59','2026-04-30 04:35:33'),
(7,'App\\Models\\User',2,'auth_token','b03e22fc22290971ae3a1fbac97d6df43cbc6d815457319c9d16ddbd29e820a3','[\"*\"]','2026-04-30 04:37:05',NULL,'2026-04-30 04:36:54','2026-04-30 04:37:05'),
(8,'App\\Models\\User',1,'auth_token','d1029139ee896fa9d04949f94b848a468d2419f2558777dd1e8b5c54e3d423d7','[\"*\"]','2026-04-30 04:45:10',NULL,'2026-04-30 04:37:15','2026-04-30 04:45:10'),
(9,'App\\Models\\User',3,'auth_token','2b2dec51347b5a79f1816b007d3f29179bdfea7f2bd5d016a25f07dd86fe47a5','[\"*\"]','2026-04-30 04:45:22',NULL,'2026-04-30 04:45:19','2026-04-30 04:45:22');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES
(1,'admin','api','2026-04-30 04:20:35','2026-04-30 04:20:35'),
(2,'employer','api','2026-04-30 04:20:35','2026-04-30 04:20:35'),
(3,'candidate','api','2026-04-30 04:20:35','2026-04-30 04:20:35');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `role` enum('admin','employer','candidate') NOT NULL DEFAULT 'candidate',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Admin User','admin@gmail.com','2026-04-30 04:20:35','$2y$12$OuBM3Or2/luirc2sKUiUcui6p3i6wlUoCsI.CtERc4P8PXyxW35kS',NULL,'admin','do0Wvfghg9','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(2,'Candidate User','candidate@gmail.com','2026-04-30 04:20:36','$2y$12$VQpWNmZarq9eUCDpMvuhbOFGD7fMza9QTMtj5eCSuTRrf/7ki3TdK',NULL,'candidate','QDOG2Sszt6','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(3,'Employer User','employer@gmail.com','2026-04-30 04:20:36','$2y$12$vVWG6f3ExKayEIkun5TCLuiveepNRFMcJfGCq/2l7Zn.v7wIGVpGy',NULL,'employer','90hqdbmvyD','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(4,'Doris Bailey','blick.monty@example.com','2026-04-30 04:20:36','$2y$12$WtzrFl4E7McS..ZhUIdZyu.RmencqQnVQ7/IWuMxu4V2lZLcJ.nXK',NULL,'employer','PVcIGW6QzO','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(5,'Mr. Stan O\'Keefe DDS','bstrosin@example.org','2026-04-30 04:20:36','$2y$12$WtzrFl4E7McS..ZhUIdZyu.RmencqQnVQ7/IWuMxu4V2lZLcJ.nXK',NULL,'employer','xihrG8xOx1','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(6,'Sally Conroy I','jerome45@example.org','2026-04-30 04:20:36','$2y$12$WtzrFl4E7McS..ZhUIdZyu.RmencqQnVQ7/IWuMxu4V2lZLcJ.nXK',NULL,'employer','DN6y2bVsAS','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(7,'Jennifer Reilly','ruby.schiller@example.net','2026-04-30 04:20:36','$2y$12$WtzrFl4E7McS..ZhUIdZyu.RmencqQnVQ7/IWuMxu4V2lZLcJ.nXK',NULL,'employer','LDENg7nYCv','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(8,'Kaelyn Nicolas','gcrona@example.com','2026-04-30 04:20:36','$2y$12$WtzrFl4E7McS..ZhUIdZyu.RmencqQnVQ7/IWuMxu4V2lZLcJ.nXK',NULL,'employer','faVgnP0GP4','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(9,'Ms. Tania Schuppe','angelo.kreiger@example.com','2026-04-30 04:20:36','$2y$12$WtzrFl4E7McS..ZhUIdZyu.RmencqQnVQ7/IWuMxu4V2lZLcJ.nXK',NULL,'candidate','WFqM8dTfXI','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(10,'Miss Annalise Shields Sr.','von.mandy@example.org','2026-04-30 04:20:36','$2y$12$WtzrFl4E7McS..ZhUIdZyu.RmencqQnVQ7/IWuMxu4V2lZLcJ.nXK',NULL,'candidate','y4f82ZEbTW','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(11,'Claude Moore','trent.donnelly@example.com','2026-04-30 04:20:36','$2y$12$WtzrFl4E7McS..ZhUIdZyu.RmencqQnVQ7/IWuMxu4V2lZLcJ.nXK',NULL,'candidate','XZanQ2RHex','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(12,'Verlie Russel','lang.ocie@example.com','2026-04-30 04:20:36','$2y$12$WtzrFl4E7McS..ZhUIdZyu.RmencqQnVQ7/IWuMxu4V2lZLcJ.nXK',NULL,'candidate','sABZdt7iYO','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(13,'Prof. Nasir Greenholt','gerhold.brenda@example.com','2026-04-30 04:20:36','$2y$12$WtzrFl4E7McS..ZhUIdZyu.RmencqQnVQ7/IWuMxu4V2lZLcJ.nXK',NULL,'candidate','6T39f1JjqG','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(14,'Prof. Alfredo Erdman II','tcollins@example.com','2026-04-30 04:20:36','$2y$12$WtzrFl4E7McS..ZhUIdZyu.RmencqQnVQ7/IWuMxu4V2lZLcJ.nXK',NULL,'candidate','GpP3IZsK7p','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(15,'Mrs. Dakota Kreiger','berenice47@example.net','2026-04-30 04:20:36','$2y$12$WtzrFl4E7McS..ZhUIdZyu.RmencqQnVQ7/IWuMxu4V2lZLcJ.nXK',NULL,'candidate','Jbt7XOq0Jh','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(16,'Mrs. Aida Kulas IV','cindy91@example.com','2026-04-30 04:20:36','$2y$12$WtzrFl4E7McS..ZhUIdZyu.RmencqQnVQ7/IWuMxu4V2lZLcJ.nXK',NULL,'candidate','t8TqtDzYS1','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(17,'Mr. Rusty Legros DDS','xlebsack@example.org','2026-04-30 04:20:36','$2y$12$WtzrFl4E7McS..ZhUIdZyu.RmencqQnVQ7/IWuMxu4V2lZLcJ.nXK',NULL,'candidate','yzkxBtnOJV','2026-04-30 04:20:36','2026-04-30 04:20:36'),
(18,'Naomi Davis I','vhoeger@example.org','2026-04-30 04:20:36','$2y$12$WtzrFl4E7McS..ZhUIdZyu.RmencqQnVQ7/IWuMxu4V2lZLcJ.nXK',NULL,'candidate','7ONUnyk5TM','2026-04-30 04:20:36','2026-04-30 04:20:36');
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

-- Dump completed on 2026-04-30 11:11:34
