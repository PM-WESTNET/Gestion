SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE `destinatary` (
  `destinatary_id` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `all_subscribed` tinyint(1) DEFAULT NULL,
  `all_unsubscribed` tinyint(1) DEFAULT NULL,
  `overdue_bills_from` int(11) DEFAULT NULL,
  `overdue_bills_to` int(11) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  `contract_min_age` int(11) DEFAULT NULL,
  `contract_max_age` int(11) DEFAULT NULL,
  `debt_from` int(11) DEFAULT '0',
  `debt_to` int(11) DEFAULT '100000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `destinatary` (`destinatary_id`, `notification_id`, `name`, `code`, `all_subscribed`, `all_unsubscribed`, `overdue_bills_from`, `overdue_bills_to`, `type`, `contract_min_age`, `contract_max_age`, `debt_from`, `debt_to`) VALUES
(1, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'by_filters', NULL, NULL, NULL, NULL),
(2, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'by_filters', NULL, NULL, NULL, NULL),
(3, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'by_filters', NULL, NULL, NULL, NULL);

CREATE TABLE `destinatary_has_company` (
  `company_id` int(11) NOT NULL,
  `destinatary_destinatary_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `destinatary_has_contract` (
  `destinatary_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `destinatary_has_contract_status` (
  `contract_status` varchar(45) NOT NULL,
  `destinatary_destinatary_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `destinatary_has_customer` (
  `destinatary_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `destinatary_has_customer_category` (
  `customer_category_id` int(11) NOT NULL,
  `destinatary_destinatary_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `destinatary_has_customer_class` (
  `customer_class_id` int(11) NOT NULL,
  `destinatary_destinatary_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `destinatary_has_customer_status` (
  `customer_status` varchar(45) NOT NULL,
  `destinatary_destinatary_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `destinatary_has_node` (
  `node_id` int(11) NOT NULL,
  `destinatary_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `destinatary_has_plan` (
  `plan_id` int(11) NOT NULL,
  `destinatary_destinatary_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `notification` (
  `notification_id` int(11) NOT NULL,
  `create_timestamp` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `content` text,
  `from_date` date DEFAULT NULL,
  `from_time` time DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `to_time` time DEFAULT NULL,
  `times_per_day` int(11) DEFAULT '1',
  `status` enum('created','enabled','disabled','sent','error') NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `layout` varchar(255) DEFAULT NULL,
  `sender` varchar(255) DEFAULT NULL,
  `update_timestamp` int(11) DEFAULT NULL,
  `transport_id` int(11) NOT NULL,
  `status_message` text,
  `monday` tinyint(1) DEFAULT NULL,
  `tuesday` tinyint(1) DEFAULT NULL,
  `wednesday` tinyint(1) DEFAULT NULL,
  `thursday` tinyint(1) DEFAULT NULL,
  `friday` tinyint(1) DEFAULT NULL,
  `saturday` tinyint(1) DEFAULT NULL,
  `sunday` tinyint(1) DEFAULT NULL,
  `last_sent` date DEFAULT NULL,
  `scheduler` varchar(255) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `email_transport_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='start_time';

INSERT INTO `notification` (`notification_id`, `create_timestamp`, `name`, `content`, `from_date`, `from_time`, `to_date`, `to_time`, `times_per_day`, `status`, `subject`, `layout`, `sender`, `update_timestamp`, `transport_id`, `status_message`, `monday`, `tuesday`, `wednesday`, `thursday`, `friday`, `saturday`, `sunday`, `last_sent`, `scheduler`, `company_id`, `email_transport_id`) VALUES
(1, 1476726379, 'Por email', 'Contenido de email', '0000-00-00', '08:00:00', '0000-00-00', '18:00:00', 1, 'created', NULL, NULL, NULL, 1476726379, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 1476726379, 'Por SMS', 'Contenido de SMS', '0000-00-00', '08:00:00', '0000-00-00', '18:00:00', 1, 'created', NULL, NULL, NULL, 1476726379, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 1476726379, 'Por Explorador', 'Contenido de explorador', '0000-00-00', '08:00:00', '0000-00-00', '18:00:00', 1, 'created', NULL, NULL, NULL, 1476726379, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

CREATE TABLE `transport` (
  `transport_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(45) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `class` varchar(255) DEFAULT NULL,
  `status` enum('enabled','disabled') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `transport` (`transport_id`, `name`, `slug`, `description`, `class`, `status`) VALUES
(1, 'SMS', 'sms', 'Envia una notificacion SMS a los destinatarios', 'app\\modules\\westnet\\notifications\\components\\transports\\SMSTransport', NULL),
(2, 'Explorador', 'browser', 'Envia una notificacion al explorador de los destinatarios', 'app\\modules\\westnet\\notifications\\components\\transports\\CPortalTransport', NULL),
(3, 'Email', 'email', 'Envia una notificacion al correo electronico de los destinatarios', 'app\\modules\\westnet\\notifications\\components\\transports\\EmailTransport', NULL),
(4, 'Mobile Push', 'mobile-push', NULL, 'app\\modules\\westnet\\notifications\\components\\transports\\MobilePushTransport', NULL);


ALTER TABLE `destinatary`
  ADD PRIMARY KEY (`destinatary_id`),
  ADD KEY `fk_destinatary_notification1_idx` (`notification_id`);

ALTER TABLE `destinatary_has_company`
  ADD PRIMARY KEY (`company_id`,`destinatary_destinatary_id`),
  ADD KEY `fk_destinatary_has_company_destinatary1_idx` (`destinatary_destinatary_id`);

ALTER TABLE `destinatary_has_contract`
  ADD PRIMARY KEY (`destinatary_id`,`contract_id`),
  ADD KEY `fk_customer_destinatary1_idx` (`destinatary_id`),
  ADD KEY `contract_id_index` (`contract_id`);

ALTER TABLE `destinatary_has_contract_status`
  ADD PRIMARY KEY (`contract_status`,`destinatary_destinatary_id`),
  ADD KEY `fk_destinatary_has_contract_status_destinatary1_idx` (`destinatary_destinatary_id`);

ALTER TABLE `destinatary_has_customer`
  ADD PRIMARY KEY (`destinatary_id`,`customer_id`),
  ADD KEY `fk_destinatary_has_customer_destinatary1_idx` (`destinatary_id`);

ALTER TABLE `destinatary_has_customer_category`
  ADD PRIMARY KEY (`customer_category_id`,`destinatary_destinatary_id`),
  ADD KEY `fk_destinatary_has_customer_category_destinatary1_idx` (`destinatary_destinatary_id`);

ALTER TABLE `destinatary_has_customer_class`
  ADD PRIMARY KEY (`customer_class_id`,`destinatary_destinatary_id`),
  ADD KEY `fk_destinatary_has_customer_class_destinatary1_idx` (`destinatary_destinatary_id`);

ALTER TABLE `destinatary_has_customer_status`
  ADD PRIMARY KEY (`customer_status`,`destinatary_destinatary_id`),
  ADD KEY `fk_destinatary_has_customer_status_destinatary1_idx` (`destinatary_destinatary_id`);

ALTER TABLE `destinatary_has_node`
  ADD PRIMARY KEY (`node_id`,`destinatary_id`),
  ADD KEY `fk_node_destinatary1_idx` (`destinatary_id`);

ALTER TABLE `destinatary_has_plan`
  ADD PRIMARY KEY (`plan_id`,`destinatary_destinatary_id`),
  ADD KEY `fk_destinatary_has_plan_destinatary1_idx` (`destinatary_destinatary_id`);

ALTER TABLE `notification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `fk_notification_transport1_idx` (`transport_id`),
  ADD KEY `notification_company_company_id_fk` (`company_id`),
  ADD KEY `notification_email_transport_company_id_fk` (`email_transport_id`);

ALTER TABLE `transport`
  ADD PRIMARY KEY (`transport_id`);


ALTER TABLE `destinatary`
  MODIFY `destinatary_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `transport`
  MODIFY `transport_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `destinatary`
  ADD CONSTRAINT `fk_destinatary_notification1` FOREIGN KEY (`notification_id`) REFERENCES `notification` (`notification_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `destinatary_has_company`
  ADD CONSTRAINT `fk_destinatary_has_company_destinatary1` FOREIGN KEY (`destinatary_destinatary_id`) REFERENCES `destinatary` (`destinatary_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `destinatary_has_contract`
  ADD CONSTRAINT `fk_customer_destinatary1` FOREIGN KEY (`destinatary_id`) REFERENCES `destinatary` (`destinatary_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `destinatary_has_contract_status`
  ADD CONSTRAINT `fk_destinatary_has_contract_status_destinatary1` FOREIGN KEY (`destinatary_destinatary_id`) REFERENCES `destinatary` (`destinatary_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `destinatary_has_customer`
  ADD CONSTRAINT `fk_destinatary_has_customer_destinatary1` FOREIGN KEY (`destinatary_id`) REFERENCES `destinatary` (`destinatary_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `destinatary_has_customer_category`
  ADD CONSTRAINT `fk_destinatary_has_customer_category_destinatary1` FOREIGN KEY (`destinatary_destinatary_id`) REFERENCES `destinatary` (`destinatary_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `destinatary_has_customer_class`
  ADD CONSTRAINT `fk_destinatary_has_customer_class_destinatary1` FOREIGN KEY (`destinatary_destinatary_id`) REFERENCES `destinatary` (`destinatary_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `destinatary_has_customer_status`
  ADD CONSTRAINT `fk_destinatary_has_customer_status_destinatary1` FOREIGN KEY (`destinatary_destinatary_id`) REFERENCES `destinatary` (`destinatary_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `destinatary_has_node`
  ADD CONSTRAINT `fk_node_destinatary1` FOREIGN KEY (`destinatary_id`) REFERENCES `destinatary` (`destinatary_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `destinatary_has_plan`
  ADD CONSTRAINT `fk_destinatary_has_plan_destinatary1` FOREIGN KEY (`destinatary_destinatary_id`) REFERENCES `destinatary` (`destinatary_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `notification`
  ADD CONSTRAINT `fk_notification_transport1` FOREIGN KEY (`transport_id`) REFERENCES `transport` (`transport_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `notification_company_company_id_fk` FOREIGN KEY (`company_id`) REFERENCES `test`.`company` (`company_id`),
  ADD CONSTRAINT `notification_email_transport_company_id_fk` FOREIGN KEY (`email_transport_id`) REFERENCES `test`.`email_transport` (`email_transport_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
