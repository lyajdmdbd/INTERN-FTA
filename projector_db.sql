-- MySQL dump 10.13  Distrib 9.1.0, for Win64 (x86_64)
--
-- Host: localhost    Database: projector_db
-- ------------------------------------------------------
-- Server version	9.1.0

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
-- Table structure for table `tempahan`
--

DROP TABLE IF EXISTS `tempahan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tempahan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tarikh` date NOT NULL,
  `masa_mula` time NOT NULL,
  `masa_tamat` time NOT NULL,
  `kelas` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tujuan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `device` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'projector',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=213 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tempahan`
--

LOCK TABLES `tempahan` WRITE;
/*!40000 ALTER TABLE `tempahan` DISABLE KEYS */;
INSERT INTO `tempahan` VALUES (129,'reza','2026-05-07','18:00:00','19:30:00','BETA','Epson','projector','2026-05-06 12:48:01'),(130,'kesaven','2026-05-07','18:00:00','19:48:00','GAMMA','-','projector','2026-05-06 12:49:24'),(131,'reza','2026-05-07','18:00:00','19:30:00','ALPHA','-','projector','2026-05-07 05:59:25'),(132,'amar bahrin','2026-05-07','18:00:00','19:30:00','HAL A','-','projector','2026-05-07 06:00:15'),(133,'madnor 46','2026-05-07','18:00:00','19:30:00','ALPHA','-','projector','2026-05-07 06:00:54'),(134,'reza','2026-05-07','15:00:00','16:00:00','AL-FARABI','-','projector','2026-05-07 06:02:13'),(135,'subaimanam','2026-05-07','15:00:00','18:00:00','AL-HAMKA','meeting','projector','2026-05-07 06:03:29'),(174,'kesaven','2026-05-11','00:00:00','00:00:00','TING 1','-','projector','2026-05-11 09:38:02'),(176,'tablet','2026-05-11','00:00:00','00:00:00','LECT ROOM 1','tablet','tablet','2026-05-11 12:43:54'),(177,'ipad','2026-05-11','00:00:00','00:00:00','LECT ROOM 1','-','ipad','2026-05-11 12:44:29'),(178,'reza','2026-05-11','20:45:00','22:00:00','AL-HAMKA','smart projector','projector','2026-05-11 12:45:17'),(180,'cikgu umar','2026-05-14','18:00:00','19:30:00','ALPHA','smart projector','projector','2026-05-11 13:01:26'),(181,'cikgu umar','2026-05-14','20:00:00','22:00:00','LECT ROOM 1','smart projector','projector','2026-05-11 13:02:41'),(182,'reza','2026-05-12','17:50:00','19:50:00','','tablet','tablet','2026-05-12 08:49:32'),(183,'cikgu zul','2026-05-12','18:00:00','19:30:00','HAL A','smart projector','projector','2026-05-12 08:51:28'),(184,'cikgu sharofi','2026-05-12','18:00:00','19:30:00','','ipad','ipad','2026-05-12 08:53:01'),(186,'cikgu sholihi','2026-05-13','20:00:00','21:30:00','GAMMA','-','projector','2026-05-13 06:46:10'),(187,'projector','2026-05-13','20:59:00','21:00:00','HALL A','epson','projector','2026-05-13 12:01:07'),(188,'test 1','2026-05-13','20:05:00','21:04:00','HALL A','-','projector','2026-05-13 12:04:30'),(190,'tablet','2026-05-14','18:00:00','19:30:00','','-','tablet','2026-05-14 06:29:49'),(191,'ipad','2026-05-14','18:00:00','19:00:00','','-','ipad','2026-05-14 08:11:26'),(192,'cikgu zul','2026-05-16','18:00:00','19:30:00','HALL A','smart epson','projector','2026-05-16 01:22:40'),(193,'cikgu sharofi','2026-05-16','18:00:00','19:30:00','','-','tablet','2026-05-16 01:23:50'),(195,'reza','2026-05-16','18:00:00','19:30:00','','-','ipad','2026-05-16 01:25:40'),(196,'amar','2026-05-16','20:00:00','22:00:00','GAMMA','smart epson','projector','2026-05-16 01:26:30'),(197,'ahmad reza','2026-05-16','10:39:00','12:40:00','HALL A','meeting','projector','2026-05-16 02:39:05'),(198,'reza','2026-05-18','18:00:00','19:30:00','HALL A','meeting','projector','2026-05-18 07:02:00'),(199,'cikgu umar','2026-05-21','18:00:00','19:30:00','ALPHA','projector Epson','projector','2026-05-18 07:11:58'),(200,'cikgu umar','2026-05-21','20:00:00','22:00:00','LECT ROOM 2','projector Epson','projector','2026-05-18 07:12:58'),(201,'cikgu zul','2026-05-18','18:00:00','19:30:00','HALL A','smart projector','projector','2026-05-18 07:33:35'),(202,'cikgu zul','2026-05-19','18:00:00','19:30:00','HALL A','smart projector','projector','2026-05-18 07:34:15'),(203,'amar danish','2026-05-18','19:40:00','20:40:00','','samsung','tablet','2026-05-18 11:33:52'),(204,'danish ayan','2026-05-18','19:40:00','20:40:00','','apple','ipad','2026-05-18 11:34:38'),(205,'reza','2026-05-19','18:00:00','19:30:00','','tablet','tablet','2026-05-19 07:46:19'),(206,'amar danish','2026-05-19','20:00:00','22:00:00','','ipad','ipad','2026-05-19 07:47:21'),(207,'ahmad','2026-05-19','19:00:00','20:00:00','','samsung','tablet','2026-05-19 08:08:24'),(209,'ozil','2026-05-21','18:00:00','20:00:00','','-','ipad','2026-05-21 06:53:47'),(210,'cikgu zul','2026-05-21','20:00:00','21:00:00','HALL A','-','projector','2026-05-21 09:05:37'),(211,'cikgu sholihi','2026-05-21','18:00:00','19:30:00','ALPHA','Epson','projector','2026-05-21 09:06:40');
/*!40000 ALTER TABLE `tempahan` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-26 14:44:17
