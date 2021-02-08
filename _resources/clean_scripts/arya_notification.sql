-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Servidor: westnet-data
-- Tiempo de generación: 15-04-2019 a las 16:28:36
-- Versión del servidor: 10.0.35-MariaDB-1~jessie
-- Versión de PHP: 7.0.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `arya_notification`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `destinatary`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `destinatary_has_company`
--

CREATE TABLE `destinatary_has_company` (
  `company_id` int(11) NOT NULL,
  `destinatary_destinatary_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `destinatary_has_contract`
--

CREATE TABLE `destinatary_has_contract` (
  `destinatary_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `destinatary_has_contract_status`
--

CREATE TABLE `destinatary_has_contract_status` (
  `contract_status` varchar(45) NOT NULL,
  `destinatary_destinatary_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `destinatary_has_customer`
--

CREATE TABLE `destinatary_has_customer` (
  `destinatary_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `destinatary_has_customer_category`
--

CREATE TABLE `destinatary_has_customer_category` (
  `customer_category_id` int(11) NOT NULL,
  `destinatary_destinatary_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `destinatary_has_customer_class`
--

CREATE TABLE `destinatary_has_customer_class` (
  `customer_class_id` int(11) NOT NULL,
  `destinatary_destinatary_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `destinatary_has_customer_status`
--

CREATE TABLE `destinatary_has_customer_status` (
  `customer_status` varchar(45) NOT NULL,
  `destinatary_destinatary_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `destinatary_has_node`
--

CREATE TABLE `destinatary_has_node` (
  `node_id` int(11) NOT NULL,
  `destinatary_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `destinatary_has_plan`
--

CREATE TABLE `destinatary_has_plan` (
  `plan_id` int(11) NOT NULL,
  `destinatary_destinatary_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `integratech_message`
--

CREATE TABLE `integratech_message` (
  `integratech_message_id` int(11) NOT NULL,
  `message` text,
  `phone` text,
  `datetime` timestamp NULL DEFAULT NULL,
  `status` enum('pending','sent','error','cancelled') DEFAULT NULL,
  `response_message_id` text,
  `response_status_code` int(11) DEFAULT NULL,
  `response_status_text` text,
  `notification_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `integratech_received_sms`
--

CREATE TABLE `integratech_received_sms` (
  `integratech_received_sms_id` int(11) NOT NULL,
  `destaddr` text,
  `charcode` text,
  `sourceaddr` text,
  `message` text,
  `customer_id` int(11) DEFAULT NULL,
  `ticket_id` int(11) NOT NULL,
  `datetime` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `integratech_sms_filter`
--

CREATE TABLE `integratech_sms_filter` (
  `integratech_sms_filter_id` int(11) NOT NULL,
  `word` text,
  `action` enum('Delete','Create Ticket') DEFAULT NULL,
  `status` enum('enabled','disabled') DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `is_created_automaticaly` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notification`
--

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
  `status` enum('created','enabled','disabled','pending','sent','error','cancelled','in_process','timeout') DEFAULT NULL,
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
  `email_transport_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `test_phone` int(11) DEFAULT NULL,
  `test_phone_frecuency` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='start_time';

--
-- Volcado de datos para la tabla `notification`
--

INSERT INTO `notification` (`notification_id`, `create_timestamp`, `name`, `content`, `from_date`, `from_time`, `to_date`, `to_time`, `times_per_day`, `status`, `subject`, `layout`, `sender`, `update_timestamp`, `transport_id`, `status_message`, `monday`, `tuesday`, `wednesday`, `thursday`, `friday`, `saturday`, `sunday`, `last_sent`, `scheduler`, `email_transport_id`, `company_id`, `test_phone`, `test_phone_frecuency`) VALUES
(5, 1555340255, 'asdasd', NULL, '2019-04-15', '08:00:00', '2019-04-15', '18:00:00', 1, 'created', NULL, NULL, NULL, 1555340255, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, 8, NULL, '1000'),
(6, 1555340294, 'asdasd', NULL, '2019-04-15', '08:00:00', '2019-04-15', '18:00:00', 1, 'created', NULL, NULL, NULL, 1555340294, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 8, NULL, '1000'),
(7, 1555340300, 'asdasd', NULL, '2019-04-15', '08:00:00', '2019-04-15', '18:00:00', 1, 'created', NULL, NULL, NULL, 1555340300, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3, 8, NULL, '1000'),
(8, 1555340308, 'asdasd', NULL, '2019-04-15', '08:00:00', '2019-04-15', '18:00:00', 1, 'created', NULL, NULL, NULL, 1555340308, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, 8, NULL, '1000');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transport`
--

CREATE TABLE `transport` (
  `transport_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(45) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `class` varchar(255) DEFAULT NULL,
  `status` enum('enabled','disabled') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `transport`
--

INSERT INTO `transport` (`transport_id`, `name`, `slug`, `description`, `class`, `status`) VALUES
(1, 'SMS', 'sms', 'Envia una notificacion SMS a los destinatarios', 'app\\modules\\westnet\\notifications\\components\\transports\\CPortalTransport', 'disabled'),
(2, 'Explorador', 'browser', 'Envia una notificacion al explorador de los destinatarios', 'app\\modules\\westnet\\notifications\\components\\transports\\CPortalTransport', 'enabled'),
(3, 'Email', 'email', 'Envia una notificacion al correo electronico de los destinatarios', 'app\\modules\\westnet\\notifications\\components\\transports\\EmailTransport', 'enabled'),
(4, 'Mobile Push', 'mobile-push', NULL, 'app\\modules\\westnet\\notifications\\components\\transports\\MobilePushTransport', 'enabled'),
(7, 'SMS Integratech', 'sms-integratech', 'Envia una notificación SMS a los destinatarios utilizando Integratech', 'app\\modules\\westnet\\notifications\\components\\transports\\SMSIntegratechTransport', 'enabled');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `destinatary`
--
ALTER TABLE `destinatary`
  ADD PRIMARY KEY (`destinatary_id`),
  ADD KEY `fk_destinatary_notification1_idx` (`notification_id`);

--
-- Indices de la tabla `destinatary_has_company`
--
ALTER TABLE `destinatary_has_company`
  ADD PRIMARY KEY (`company_id`,`destinatary_destinatary_id`),
  ADD KEY `fk_destinatary_has_company_destinatary1_idx` (`destinatary_destinatary_id`);

--
-- Indices de la tabla `destinatary_has_contract`
--
ALTER TABLE `destinatary_has_contract`
  ADD PRIMARY KEY (`destinatary_id`,`contract_id`),
  ADD KEY `fk_customer_destinatary1_idx` (`destinatary_id`),
  ADD KEY `contract_id_index` (`contract_id`);

--
-- Indices de la tabla `destinatary_has_contract_status`
--
ALTER TABLE `destinatary_has_contract_status`
  ADD PRIMARY KEY (`contract_status`,`destinatary_destinatary_id`),
  ADD KEY `fk_destinatary_has_contract_status_destinatary1_idx` (`destinatary_destinatary_id`);

--
-- Indices de la tabla `destinatary_has_customer`
--
ALTER TABLE `destinatary_has_customer`
  ADD PRIMARY KEY (`destinatary_id`,`customer_id`),
  ADD KEY `fk_destinatary_has_customer_destinatary1_idx` (`destinatary_id`);

--
-- Indices de la tabla `destinatary_has_customer_category`
--
ALTER TABLE `destinatary_has_customer_category`
  ADD PRIMARY KEY (`customer_category_id`,`destinatary_destinatary_id`),
  ADD KEY `fk_destinatary_has_customer_category_destinatary1_idx` (`destinatary_destinatary_id`);

--
-- Indices de la tabla `destinatary_has_customer_class`
--
ALTER TABLE `destinatary_has_customer_class`
  ADD PRIMARY KEY (`customer_class_id`,`destinatary_destinatary_id`),
  ADD KEY `fk_destinatary_has_customer_class_destinatary1_idx` (`destinatary_destinatary_id`);

--
-- Indices de la tabla `destinatary_has_customer_status`
--
ALTER TABLE `destinatary_has_customer_status`
  ADD PRIMARY KEY (`customer_status`,`destinatary_destinatary_id`),
  ADD KEY `fk_destinatary_has_customer_status_destinatary1_idx` (`destinatary_destinatary_id`);

--
-- Indices de la tabla `destinatary_has_node`
--
ALTER TABLE `destinatary_has_node`
  ADD PRIMARY KEY (`node_id`,`destinatary_id`),
  ADD KEY `fk_node_destinatary1_idx` (`destinatary_id`);

--
-- Indices de la tabla `destinatary_has_plan`
--
ALTER TABLE `destinatary_has_plan`
  ADD PRIMARY KEY (`plan_id`,`destinatary_destinatary_id`),
  ADD KEY `fk_destinatary_has_plan_destinatary1_idx` (`destinatary_destinatary_id`);

--
-- Indices de la tabla `integratech_message`
--
ALTER TABLE `integratech_message`
  ADD PRIMARY KEY (`integratech_message_id`),
  ADD KEY `fk_integratech_message` (`notification_id`);

--
-- Indices de la tabla `integratech_received_sms`
--
ALTER TABLE `integratech_received_sms`
  ADD PRIMARY KEY (`integratech_received_sms_id`);

--
-- Indices de la tabla `integratech_sms_filter`
--
ALTER TABLE `integratech_sms_filter`
  ADD PRIMARY KEY (`integratech_sms_filter_id`);

--
-- Indices de la tabla `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `fk_notification_transport1_idx` (`transport_id`);

--
-- Indices de la tabla `transport`
--
ALTER TABLE `transport`
  ADD PRIMARY KEY (`transport_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `destinatary`
--
ALTER TABLE `destinatary`
  MODIFY `destinatary_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `integratech_message`
--
ALTER TABLE `integratech_message`
  MODIFY `integratech_message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `integratech_received_sms`
--
ALTER TABLE `integratech_received_sms`
  MODIFY `integratech_received_sms_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `integratech_sms_filter`
--
ALTER TABLE `integratech_sms_filter`
  MODIFY `integratech_sms_filter_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notification`
--
ALTER TABLE `notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `transport`
--
ALTER TABLE `transport`
  MODIFY `transport_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `destinatary`
--
ALTER TABLE `destinatary`
  ADD CONSTRAINT `fk_destinatary_notification1` FOREIGN KEY (`notification_id`) REFERENCES `notification` (`notification_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `destinatary_has_company`
--
ALTER TABLE `destinatary_has_company`
  ADD CONSTRAINT `fk_destinatary_has_company_destinatary1` FOREIGN KEY (`destinatary_destinatary_id`) REFERENCES `destinatary` (`destinatary_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `destinatary_has_contract`
--
ALTER TABLE `destinatary_has_contract`
  ADD CONSTRAINT `fk_customer_destinatary1` FOREIGN KEY (`destinatary_id`) REFERENCES `destinatary` (`destinatary_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `destinatary_has_contract_status`
--
ALTER TABLE `destinatary_has_contract_status`
  ADD CONSTRAINT `fk_destinatary_has_contract_status_destinatary1` FOREIGN KEY (`destinatary_destinatary_id`) REFERENCES `destinatary` (`destinatary_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `destinatary_has_customer`
--
ALTER TABLE `destinatary_has_customer`
  ADD CONSTRAINT `fk_destinatary_has_customer_destinatary1` FOREIGN KEY (`destinatary_id`) REFERENCES `destinatary` (`destinatary_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `destinatary_has_customer_category`
--
ALTER TABLE `destinatary_has_customer_category`
  ADD CONSTRAINT `fk_destinatary_has_customer_category_destinatary1` FOREIGN KEY (`destinatary_destinatary_id`) REFERENCES `destinatary` (`destinatary_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `destinatary_has_customer_class`
--
ALTER TABLE `destinatary_has_customer_class`
  ADD CONSTRAINT `fk_destinatary_has_customer_class_destinatary1` FOREIGN KEY (`destinatary_destinatary_id`) REFERENCES `destinatary` (`destinatary_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `destinatary_has_customer_status`
--
ALTER TABLE `destinatary_has_customer_status`
  ADD CONSTRAINT `fk_destinatary_has_customer_status_destinatary1` FOREIGN KEY (`destinatary_destinatary_id`) REFERENCES `destinatary` (`destinatary_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `destinatary_has_node`
--
ALTER TABLE `destinatary_has_node`
  ADD CONSTRAINT `fk_node_destinatary1` FOREIGN KEY (`destinatary_id`) REFERENCES `destinatary` (`destinatary_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `destinatary_has_plan`
--
ALTER TABLE `destinatary_has_plan`
  ADD CONSTRAINT `fk_destinatary_has_plan_destinatary1` FOREIGN KEY (`destinatary_destinatary_id`) REFERENCES `destinatary` (`destinatary_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `integratech_message`
--
ALTER TABLE `integratech_message`
  ADD CONSTRAINT `fk_integratech_message` FOREIGN KEY (`notification_id`) REFERENCES `notification` (`notification_id`);

--
-- Filtros para la tabla `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `fk_notification_transport1` FOREIGN KEY (`transport_id`) REFERENCES `transport` (`transport_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
