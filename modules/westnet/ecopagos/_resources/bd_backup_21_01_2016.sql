-- MySQL dump 10.15  Distrib 10.0.15-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: arya_ecopago
-- ------------------------------------------------------
-- Server version	10.0.15-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `assignation`
--

DROP TABLE IF EXISTS `assignation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assignation` (
  `ecopago_id` int(11) NOT NULL,
  `collector_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `datetime` int(11) NOT NULL,
  PRIMARY KEY (`ecopago_id`,`collector_id`),
  KEY `fk_ecopago_has_collector_collector1_idx` (`collector_id`),
  KEY `fk_ecopago_has_collector_ecopago1_idx` (`ecopago_id`),
  CONSTRAINT `fk_ecopago_has_collector_collector1` FOREIGN KEY (`collector_id`) REFERENCES `collector` (`collector_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ecopago_has_collector_ecopago1` FOREIGN KEY (`ecopago_id`) REFERENCES `ecopago` (`ecopago_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assignation`
--

LOCK TABLES `assignation` WRITE;
/*!40000 ALTER TABLE `assignation` DISABLE KEYS */;
INSERT INTO `assignation` VALUES (1,1,'2016-01-19','13:58:00',1453211922),(1,2,'2016-01-19','13:58:00',1453211923),(1,3,'2016-01-19','13:58:00',1453211923),(2,3,'2015-12-22','14:14:00',1450793690);
/*!40000 ALTER TABLE `assignation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `batch_closure`
--

DROP TABLE IF EXISTS `batch_closure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `batch_closure` (
  `batch_closure_id` int(11) NOT NULL AUTO_INCREMENT,
  `last_batch_closure_id` int(11) DEFAULT NULL,
  `ecopago_id` int(11) NOT NULL,
  `collector_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `datetime` int(11) NOT NULL,
  `number` varchar(50) NOT NULL DEFAULT '0',
  `total` double NOT NULL DEFAULT '0',
  `payment_count` int(11) NOT NULL DEFAULT '0',
  `first_payout_number` varchar(50) NOT NULL DEFAULT '0',
  `last_payout_number` varchar(50) NOT NULL DEFAULT '0',
  `commission` double NOT NULL DEFAULT '0',
  `discount` double DEFAULT '0',
  `status` enum('collected','rendered','canceled') NOT NULL,
  `real_total` double DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`batch_closure_id`),
  KEY `fk_closing_batch_ecopago1_idx` (`ecopago_id`),
  KEY `fk_closing_batch_collector1_idx` (`collector_id`),
  CONSTRAINT `fk_closing_batch_collector1` FOREIGN KEY (`collector_id`) REFERENCES `collector` (`collector_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_closing_batch_ecopago1` FOREIGN KEY (`ecopago_id`) REFERENCES `ecopago` (`ecopago_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `batch_closure`
--

LOCK TABLES `batch_closure` WRITE;
/*!40000 ALTER TABLE `batch_closure` DISABLE KEYS */;
INSERT INTO `batch_closure` VALUES (7,NULL,1,1,'2016-01-11','12:10:24',1452514224,'0',24150,9,'18','31',2415,0,'canceled',NULL,NULL),(8,7,1,1,'2016-01-11','12:14:04',1452514444,'0',24150,9,'18','31',2415,0,'canceled',NULL,NULL),(9,8,1,1,'2016-01-12','14:10:25',1452607825,'0',5800,1,'35','35',580,0,'canceled',NULL,NULL),(10,9,1,1,'2016-01-12','14:10:52',1452607852,'0',5800,1,'35','35',580,0,'canceled',NULL,NULL),(11,10,1,1,'2016-01-12','14:43:42',1452609822,'0',9000,2,'36','37',900,0,'canceled',NULL,NULL),(12,11,1,1,'2016-01-13','11:26:10',1452684370,'0',10570,3,'36','38',1057,0,'canceled',NULL,NULL),(13,12,1,1,'2016-01-15','14:59:18',1452869958,'0',150,1,'39','39',15,0,'canceled',NULL,NULL),(14,13,1,1,'2016-01-18','11:28:20',1453116500,'0',24150,9,'18','31',2415,0,'canceled',NULL,NULL),(15,14,1,1,'2016-01-18','12:10:27',1453119027,'0',39780,13,'18','39',3978,0,'canceled',NULL,NULL),(16,15,1,1,'2016-01-18','12:10:58',1453119058,'0',39780,13,'18','39',3978,0,'canceled',NULL,NULL),(17,16,1,1,'2016-01-18','12:32:26',1453120346,'0',39780,13,'18','39',3978,0,'canceled',NULL,NULL),(18,17,1,1,'2016-01-18','12:34:30',1453120470,'0',39780,13,'18','39',3978,0,'canceled',NULL,NULL),(19,18,1,1,'2016-01-18','14:23:28',1453127008,'0',39780,13,'18','39',3978,0,'canceled',NULL,NULL),(20,19,1,2,'2016-01-18','14:51:11',1453128671,'0',1050,2,'40','41',105,0,'rendered',NULL,NULL),(21,20,1,1,'2016-01-19','13:51:02',1453211462,'0',5000,1,'42','42',500,0,'rendered',NULL,NULL),(22,21,1,1,'2016-01-19','14:10:43',1453212643,'0',39780,13,'18','39',3978,0,'canceled',NULL,NULL),(23,22,1,2,'2016-01-19','14:37:28',1453214248,'0',44280,14,'18','43',4428,0,'canceled',NULL,NULL),(24,23,1,2,'2016-01-20','14:11:56',1453299116,'0',44280,14,'18','43',4428,0,'collected',NULL,NULL);
/*!40000 ALTER TABLE `batch_closure` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `batch_closure_has_payout`
--

DROP TABLE IF EXISTS `batch_closure_has_payout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `batch_closure_has_payout` (
  `batch_closure_id` int(11) NOT NULL,
  `payout_id` int(11) NOT NULL,
  PRIMARY KEY (`batch_closure_id`,`payout_id`),
  KEY `fk_batch_closure_has_payout_payout1_idx` (`payout_id`),
  KEY `fk_batch_closure_has_payout_batch_closure1_idx` (`batch_closure_id`),
  CONSTRAINT `fk_batch_closure_has_payout_batch_closure1` FOREIGN KEY (`batch_closure_id`) REFERENCES `batch_closure` (`batch_closure_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_batch_closure_has_payout_payout1` FOREIGN KEY (`payout_id`) REFERENCES `payout` (`payout_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `batch_closure_has_payout`
--

LOCK TABLES `batch_closure_has_payout` WRITE;
/*!40000 ALTER TABLE `batch_closure_has_payout` DISABLE KEYS */;
INSERT INTO `batch_closure_has_payout` VALUES (17,18),(17,21),(17,26),(17,27),(17,28),(17,29),(17,30),(17,31),(17,35),(17,36),(17,37),(17,38),(17,39),(18,18),(18,21),(18,26),(18,27),(18,28),(18,29),(18,30),(18,31),(18,35),(18,36),(18,37),(18,38),(18,39),(19,18),(19,21),(19,26),(19,27),(19,28),(19,29),(19,30),(19,31),(19,35),(19,36),(19,37),(19,38),(19,39),(20,40),(20,41),(21,42),(22,18),(22,21),(22,26),(22,27),(22,28),(22,29),(22,30),(22,31),(22,35),(22,36),(22,37),(22,38),(22,39),(23,18),(23,21),(23,26),(23,27),(23,28),(23,29),(23,30),(23,31),(23,35),(23,36),(23,37),(23,38),(23,39),(23,43),(24,18),(24,21),(24,26),(24,27),(24,28),(24,29),(24,30),(24,31),(24,35),(24,36),(24,37),(24,38),(24,39),(24,43);
/*!40000 ALTER TABLE `batch_closure_has_payout` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cashier`
--

DROP TABLE IF EXISTS `cashier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cashier` (
  `cashier_id` int(11) NOT NULL AUTO_INCREMENT,
  `address_id` int(11) DEFAULT NULL,
  `ecopago_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `number` varchar(20) NOT NULL,
  `document_number` varchar(20) NOT NULL,
  `document_type` enum('DNI','Otro') NOT NULL DEFAULT 'DNI',
  `user_id` int(11) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`cashier_id`),
  KEY `fk_cashier_ecopago1_idx` (`ecopago_id`),
  CONSTRAINT `fk_cashier_ecopago1` FOREIGN KEY (`ecopago_id`) REFERENCES `ecopago` (`ecopago_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cashier`
--

LOCK TABLES `cashier` WRITE;
/*!40000 ALTER TABLE `cashier` DISABLE KEYS */;
INSERT INTO `cashier` VALUES (1,NULL,1,'superadmin','Seba','Maldonado','001','34960955','DNI',1,'active'),(17,NULL,1,'admin','admin','admin','admin','32132132','DNI',16,'active'),(18,NULL,1,'cobrador','cobrador','cobrador','cobrador','34960955','DNI',17,'active');
/*!40000 ALTER TABLE `cashier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `collector`
--

DROP TABLE IF EXISTS `collector`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `collector` (
  `collector_id` int(11) NOT NULL AUTO_INCREMENT,
  `address_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `number` varchar(20) NOT NULL,
  `document_number` varchar(20) NOT NULL,
  `document_type` enum('DNI','Otro') NOT NULL DEFAULT 'DNI',
  `password` varchar(255) NOT NULL,
  `limit` double DEFAULT NULL,
  PRIMARY KEY (`collector_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `collector`
--

LOCK TABLES `collector` WRITE;
/*!40000 ALTER TABLE `collector` DISABLE KEYS */;
INSERT INTO `collector` VALUES (1,NULL,'John','Rambo','001','34998877','DNI','$2y$13$w3olqzruuebQJjSh6mbyFeBqdjxsEjdvLfW3GW4Nmxb5yYkDhn6we',15000),(2,NULL,'Tony','Stark','002','44889955','DNI','$2y$13$9U6bgsznHcS26Bn2pnlXbOhY93a7yRJnEoVeYjCNoEE7/HLnUd04.',25000),(3,NULL,'Jon','Snow','003','33226699','DNI','$2y$13$xOyLCILOPin575ho.ly0o.JUFFOG7E4ua0mniIS/iCdxWQziYfKsa',35000);
/*!40000 ALTER TABLE `collector` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commission`
--

DROP TABLE IF EXISTS `commission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commission` (
  `commission_id` int(11) NOT NULL AUTO_INCREMENT,
  `ecopago_id` int(11) NOT NULL,
  `create_datetime` int(11) NOT NULL,
  `update_datetime` int(11) DEFAULT NULL,
  `type` enum('percentage','fixed') NOT NULL DEFAULT 'percentage',
  `value` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`commission_id`),
  KEY `fk_commission_ecopago1_idx` (`ecopago_id`),
  CONSTRAINT `fk_commission_ecopago1` FOREIGN KEY (`ecopago_id`) REFERENCES `ecopago` (`ecopago_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commission`
--

LOCK TABLES `commission` WRITE;
/*!40000 ALTER TABLE `commission` DISABLE KEYS */;
INSERT INTO `commission` VALUES (1,1,1449061446,NULL,'percentage',4),(2,1,1449061563,NULL,'fixed',4),(3,1,1449062131,NULL,'percentage',4),(8,1,1449141612,NULL,'percentage',4),(9,1,1449141649,NULL,'percentage',4),(10,1,1449141682,NULL,'percentage',4),(11,1,1449141687,NULL,'percentage',4),(12,1,1449150559,NULL,'percentage',4),(13,1,1449150565,NULL,'percentage',4),(14,2,1450793642,NULL,'percentage',4),(15,2,1450793691,NULL,'percentage',4),(16,1,1450793701,NULL,'percentage',4),(17,1,1450868893,NULL,'percentage',4),(18,1,1450868982,NULL,'percentage',4),(19,1,1450875724,NULL,'percentage',4),(20,1,1450875797,NULL,'percentage',4),(21,1,1450875839,NULL,'percentage',4),(22,1,1450875847,NULL,'percentage',4),(23,1,1450875851,NULL,'percentage',4),(24,1,1451304460,NULL,'percentage',10),(25,1,1453211908,NULL,'percentage',10),(26,1,1453211923,NULL,'percentage',10),(27,1,1453296328,NULL,'percentage',10),(28,1,1453297081,NULL,'percentage',10),(29,1,1453297087,NULL,'percentage',10),(30,1,1453297093,NULL,'percentage',10),(32,1,1453298605,NULL,'percentage',10),(33,1,1453300658,NULL,'percentage',10);
/*!40000 ALTER TABLE `commission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `daily_closure`
--

DROP TABLE IF EXISTS `daily_closure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `daily_closure` (
  `daily_closure_id` int(11) NOT NULL AUTO_INCREMENT,
  `cashier_id` int(11) NOT NULL,
  `ecopago_id` int(11) NOT NULL,
  `datetime` int(11) NOT NULL,
  `first_payout_number` varchar(50) NOT NULL DEFAULT '0',
  `last_payout_number` varchar(50) NOT NULL DEFAULT '0',
  `payment_count` int(11) NOT NULL DEFAULT '0',
  `total` double NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  `time` time NOT NULL,
  `status` enum('open','closed','canceled') NOT NULL DEFAULT 'open',
  `close_datetime` int(11) DEFAULT NULL,
  PRIMARY KEY (`daily_closure_id`),
  KEY `fk_daily_closure_cashier1_idx` (`cashier_id`),
  KEY `fk_daily_closure_ecopago1_idx` (`ecopago_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daily_closure`
--

LOCK TABLES `daily_closure` WRITE;
/*!40000 ALTER TABLE `daily_closure` DISABLE KEYS */;
/*!40000 ALTER TABLE `daily_closure` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ecopago`
--

DROP TABLE IF EXISTS `ecopago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ecopago` (
  `ecopago_id` int(11) NOT NULL AUTO_INCREMENT,
  `address_id` int(11) DEFAULT NULL,
  `status_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `create_datetime` int(11) NOT NULL,
  `update_datetime` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `limit` double DEFAULT NULL,
  `number` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ecopago_id`),
  KEY `fk_ecopago_status_idx` (`status_id`),
  CONSTRAINT `fk_ecopago_status` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ecopago`
--

LOCK TABLES `ecopago` WRITE;
/*!40000 ALTER TABLE `ecopago` DISABLE KEYS */;
INSERT INTO `ecopago` VALUES (1,NULL,1,14,1448974697,1453300658,'Sucursal Centro','Sucursal ubicada en Av. San Martin 1520 - CP 5500',150000,'001'),(2,NULL,1,0,1450793641,1450793690,'Sucursal Godoy Cruz','Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem. Quisque velit nisi, pretium ut lacinia in, elementum id enim. Donec sollicitudin molestie malesuada. Sed porttitor lectus nibh. Cras ultricies ligula sed magna dictum porta. Vivamus suscipit tortor eget felis porttitor volutpat. Donec sollicitudin molestie malesuada. Vivamus magna justo, lacinia eget consectetur sed, convallis at tellus. Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem. Curabitur non nulla sit amet nisl tempus convallis quis ac lectus.',150000,'002');
/*!40000 ALTER TABLE `ecopago` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payout`
--

DROP TABLE IF EXISTS `payout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payout` (
  `payout_id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `ecopago_id` int(11) NOT NULL,
  `cashier_id` int(11) NOT NULL,
  `customer_number` varchar(45) NOT NULL,
  `amount` double NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `datetime` int(11) NOT NULL,
  `number` varchar(45) NOT NULL,
  `status` enum('valid','reversed','closed_by_batch','closed') NOT NULL DEFAULT 'valid',
  `batch_closure_id` int(11) DEFAULT NULL,
  `daily_closure_id` int(11) DEFAULT NULL,
  `period_closure_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`payout_id`),
  KEY `fk_payout_ecopago1_idx` (`ecopago_id`),
  KEY `fk_payout_cashier1_idx` (`cashier_id`),
  CONSTRAINT `fk_payout_cashier1` FOREIGN KEY (`cashier_id`) REFERENCES `cashier` (`cashier_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_payout_ecopago1` FOREIGN KEY (`ecopago_id`) REFERENCES `ecopago` (`ecopago_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payout`
--

LOCK TABLES `payout` WRITE;
/*!40000 ALTER TABLE `payout` DISABLE KEYS */;
INSERT INTO `payout` VALUES (18,372,4,1,1,'MR3RH9XDB05260H',1500,'2015-12-30','13:26:22',1451481982,'','closed_by_batch',24,NULL,NULL),(19,373,5,1,1,'MR3RH9XDB052600',890,'2015-12-30','13:47:46',1451483266,'','closed_by_batch',NULL,NULL,NULL),(20,374,4,2,1,'MR3RH9XDB05260H',560,'2015-12-30','13:58:50',1451483930,'','valid',NULL,NULL,NULL),(21,375,4,1,1,'MR3RH9XDB05260H',580,'2016-01-04','14:25:51',1451917551,'','closed_by_batch',24,NULL,NULL),(22,376,4,1,1,'MR3RH9XDB05260H',7000,'2016-01-04','14:27:47',1451917667,'','reversed',NULL,NULL,NULL),(23,380,1,1,1,'060190',8700,'2016-01-06','15:10:50',1452093050,'','reversed',NULL,NULL,NULL),(24,381,1,1,1,'060190',100,'2016-01-06','15:11:34',1452093094,'','reversed',NULL,NULL,NULL),(25,383,1,1,1,'060190',250,'2016-01-06','15:14:49',1452093289,'','reversed',NULL,NULL,NULL),(26,384,1,1,1,'060190',500,'2016-01-06','15:15:35',1452093335,'','closed_by_batch',24,NULL,NULL),(27,386,1,1,1,'060190',500,'2016-01-06','15:17:00',1452093420,'','closed_by_batch',24,NULL,NULL),(28,387,1,1,1,'060190',600,'2016-01-06','15:17:18',1452093438,'','closed_by_batch',24,NULL,NULL),(29,389,1,1,1,'060190',9000,'2016-01-06','15:39:37',1452094777,'','closed_by_batch',24,NULL,NULL),(30,390,1,1,1,'060190',10000,'2016-01-06','15:40:18',1452094818,'','closed_by_batch',24,NULL,NULL),(31,391,1,1,1,'060190',580,'2016-01-07','11:27:51',1452166071,'','closed_by_batch',24,NULL,NULL),(32,392,1,1,1,'060190',500,'2016-01-07','12:23:22',1452169402,'','reversed',NULL,NULL,NULL),(33,393,1,1,1,'060190',500,'2016-01-07','15:15:00',1452179700,'','reversed',NULL,NULL,NULL),(34,394,1,1,1,'060190',999,'2016-01-11','11:39:16',1452512356,'','reversed',NULL,NULL,NULL),(35,395,1,1,1,'060190',5800,'2016-01-12','12:30:40',1452601840,'','closed_by_batch',24,NULL,NULL),(36,396,4,1,1,'MR3RH9XDB05260H',1500,'2016-01-12','14:32:58',1452609178,'','closed_by_batch',24,NULL,NULL),(37,397,1,1,1,'060190',7500,'2016-01-12','14:43:32',1452609812,'','closed_by_batch',24,NULL,NULL),(38,398,1,1,1,'060190',1570,'2016-01-13','11:25:51',1452684351,'','closed_by_batch',24,NULL,NULL),(39,399,1,1,1,'060190',150,'2016-01-15','14:56:59',1452869819,'','closed_by_batch',24,NULL,NULL),(40,400,1,1,1,'060190',175,'2016-01-18','14:50:30',1453128630,'','closed_by_batch',20,1,NULL),(41,401,1,1,1,'060190',875,'2016-01-18','14:50:40',1453128640,'','closed_by_batch',20,1,NULL),(42,402,1,1,1,'060190',5000,'2016-01-19','13:45:49',1453211149,'','closed_by_batch',21,2,NULL),(43,403,1,1,1,'060190',4500,'2016-01-19','14:37:07',1453214227,'','closed_by_batch',24,NULL,NULL);
/*!40000 ALTER TABLE `payout` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `period_closure`
--

DROP TABLE IF EXISTS `period_closure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `period_closure` (
  `period_closure_id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` int(11) NOT NULL,
  `cashier_id` int(11) NOT NULL,
  `payment_count` int(11) NOT NULL,
  `first_payout_number` varchar(50) NOT NULL,
  `last_payout_number` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `status` enum('closed','canceled') NOT NULL,
  PRIMARY KEY (`period_closure_id`),
  KEY `fk_period_closure_cashier1_idx` (`cashier_id`),
  CONSTRAINT `fk_period_closure_cashier1` FOREIGN KEY (`cashier_id`) REFERENCES `cashier` (`cashier_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `period_closure`
--

LOCK TABLES `period_closure` WRITE;
/*!40000 ALTER TABLE `period_closure` DISABLE KEYS */;
/*!40000 ALTER TABLE `period_closure` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `status`
--

DROP TABLE IF EXISTS `status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `status` (
  `status_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `slug` varchar(45) NOT NULL,
  PRIMARY KEY (`status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status`
--

LOCK TABLES `status` WRITE;
/*!40000 ALTER TABLE `status` DISABLE KEYS */;
INSERT INTO `status` VALUES (1,'Habilitado','Ecopago habilitado','enabled'),(2,'Deshabilitado','Ecopago deshabilitado','disabled');
/*!40000 ALTER TABLE `status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `withdrawal`
--

DROP TABLE IF EXISTS `withdrawal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `withdrawal` (
  `withdrawal_id` int(11) NOT NULL AUTO_INCREMENT,
  `daily_closure_id` int(11) NOT NULL,
  `cashier_id` int(11) NOT NULL,
  `amount` double NOT NULL,
  `datetime` time NOT NULL,
  PRIMARY KEY (`withdrawal_id`),
  KEY `fk_withdrawal_daily_closure1_idx` (`daily_closure_id`),
  KEY `fk_withdrawal_cashier1_idx` (`cashier_id`),
  CONSTRAINT `fk_withdrawal_cashier1` FOREIGN KEY (`cashier_id`) REFERENCES `cashier` (`cashier_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_withdrawal_daily_closure1` FOREIGN KEY (`daily_closure_id`) REFERENCES `daily_closure` (`daily_closure_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `withdrawal`
--

LOCK TABLES `withdrawal` WRITE;
/*!40000 ALTER TABLE `withdrawal` DISABLE KEYS */;
/*!40000 ALTER TABLE `withdrawal` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-01-21 12:00:28
