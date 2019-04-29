-- MySQL dump 10.15  Distrib 10.0.15-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: config
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
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `status` enum('enabled','disabled') DEFAULT NULL,
  `superadmin` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'Agenda','enabled',NULL),(2,'Comprobantes','enabled',NULL),(3,'Gestión de Stock','enabled',NULL),(4,'Media','enabled',NULL),(5,'Productos','enabled',NULL),(7,'Ecopago','enabled',NULL);
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT,
  `value` text,
  `item_id` int(11) NOT NULL,
  PRIMARY KEY (`config_id`),
  KEY `fk_config_item_idx` (`item_id`),
  CONSTRAINT `fk_config_item_idx` FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config`
--

LOCK TABLES `config` WRITE;
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
INSERT INTO `config` VALUES (1,'1',1),(2,'28800',2),(3,'08:00',3),(4,'18:00',4),(5,'10:00',5),(7,'Contado',20),(8,'cajas_ecopago',21);
/*!40000 ALTER TABLE `config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item`
--

DROP TABLE IF EXISTS `item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `attr` varchar(45) NOT NULL,
  `type` varchar(45) DEFAULT NULL,
  `default` varchar(255) DEFAULT NULL,
  `label` varchar(140) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `multiple` tinyint(1) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `superadmin` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `attr` (`attr`),
  KEY `fk_item_category1` (`category_id`),
  CONSTRAINT `fk_item_category1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item`
--

LOCK TABLES `item` WRITE;
/*!40000 ALTER TABLE `item` DISABLE KEYS */;
INSERT INTO `item` VALUES (1,'check_expiration_on_login','checkbox','1','Revisar tareas vencidas al iniciar sesión','Indica si se revisarán las tareas vencidas de un usuario cuando loguee o no.',0,1,1),(2,'check_expiration_timeout','textInput','28800','Timeout para revisión de tareas vencidas (s)','Timeout para revisión de tareas vencidas (en segundos): 28800s por defecto',0,1,1),(3,'work_hours_start','textInput','08:00','Hora de inicio de día laboral','Indica la hora de inicio de un día laboral (formato H:i)',0,1,1),(4,'work_hours_end','textInput','18:00','Hora de fin de día laboral','Indica la hora de fin de un día laboral (formato H:i)',0,1,1),(5,'work_hours_quantity','textInput','10:00','Cantidad de horas laborables en un día','Cantidad de horas laborables en un día habil (Formato H:i, i.e. 10 horas laborables => 10:00)',0,1,1),(6,'bill_default_expiration_days','textInput','14','Días por defecto para vencimiento de órden de venta','',0,2,0),(7,'force_customer_company','checkbox','','Forzar la utilización de la empresa asociada al cliente','',0,2,0),(8,'show_delivery_note_verification_column','checkbox','1','Mostrar columna de verificación en remito','',0,2,0),(9,'show_price_delivery_note','checkbox','1','Mostrar precios en remito','',0,2,0),(10,'strict_stock','checkbox','','Stock estricto (no se permite stock negativo)','',0,3,0),(11,'enable_secondary_stock','checkbox','1','Habilitar stock secundario','',0,3,0),(12,'image_min_width','textInput','200','Ancho mínimo de imagenes en pixeles','',0,4,0),(13,'image_min_height','textInput','200','Alto mínimo de imagenes en pixeles','',0,4,0),(14,'image_max_width','textInput','1920','Ancho máximo de imagenes en pixeles','',0,4,0),(15,'image_max_height','textInput','1920','Alto máximo de imagenes en pixeles','',0,4,0),(16,'image_quality','textInput','0.8','Calidad de imagen','Valor entre 0 y 1, siendo 1 la máxima calidad',0,4,0),(17,'image_thumbnail_mode_inset','checkbox','1','Al generar miniatura, mantener relación de aspecto original','',0,4,0),(18,'sale_products_list_view','textInput','1','¿Mostrar imágenes en la lista de productos al generar un comprobante?','',0,5,0),(20,'payment_method','textInput','Contado','Método de pago','Método de pago utilizado por defecto para pagos de Ecopagos',0,7,1),(21,'money_box_type','textInput','cajas_ecopago','Tipo de entidad bancaria','Tipo de entidad bancaria utilizada para mostrar a que entidades bancarias rendir dinero de cierres de lote',0,7,1);
/*!40000 ALTER TABLE `item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rule`
--

DROP TABLE IF EXISTS `rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rule` (
  `rule_id` int(11) NOT NULL AUTO_INCREMENT,
  `message` varchar(255) DEFAULT NULL,
  `max` double DEFAULT NULL,
  `min` double DEFAULT NULL,
  `pattern` varchar(255) DEFAULT NULL,
  `format` varchar(45) DEFAULT NULL,
  `targetAttribute` varchar(45) DEFAULT NULL,
  `targetClass` varchar(255) DEFAULT NULL,
  `item_id` int(11) NOT NULL,
  `validator` varchar(45) NOT NULL,
  PRIMARY KEY (`rule_id`),
  KEY `fk_rule_item1_idx` (`item_id`),
  CONSTRAINT `fk_rule_item1_idx` FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rule`
--

LOCK TABLES `rule` WRITE;
/*!40000 ALTER TABLE `rule` DISABLE KEYS */;
INSERT INTO `rule` VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'boolean'),(2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'required'),(3,NULL,172800,60,NULL,NULL,NULL,NULL,2,'number'),(4,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,'required'),(5,NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,'string'),(6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,'required'),(7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'string'),(8,NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'required'),(9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,5,'string'),(10,NULL,NULL,NULL,NULL,NULL,NULL,NULL,5,'required'),(11,'',NULL,0,NULL,NULL,NULL,NULL,6,'integer'),(12,'',4000,1,NULL,NULL,NULL,NULL,12,'integer'),(13,'',4000,1,NULL,NULL,NULL,NULL,13,'integer'),(14,'',5000,1,NULL,NULL,NULL,NULL,14,'integer'),(15,'',5000,1,NULL,NULL,NULL,NULL,15,'integer'),(16,'',1,0,NULL,NULL,NULL,NULL,16,'double'),(19,NULL,NULL,NULL,NULL,NULL,NULL,NULL,20,'string'),(20,NULL,NULL,NULL,NULL,NULL,NULL,NULL,20,'required'),(21,NULL,NULL,NULL,NULL,NULL,NULL,NULL,21,'string'),(22,NULL,NULL,NULL,NULL,NULL,NULL,NULL,21,'required');
/*!40000 ALTER TABLE `rule` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-01-26  9:17:38
