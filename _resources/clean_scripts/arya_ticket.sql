-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Servidor: westnet-data
-- Tiempo de generación: 15-04-2019 a las 16:30:02
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
-- Base de datos: `arya_ticket`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `action`
--

CREATE TABLE `action` (
  `action_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `type` enum('ticket','event') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `assignation`
--

CREATE TABLE `assignation` (
  `assignation_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `user_id` varchar(45) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `external_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `slug` varchar(45) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `lft` int(11) DEFAULT NULL,
  `rgt` int(11) DEFAULT NULL,
  `notify` int(11) DEFAULT NULL,
  `external_user_id` int(11) DEFAULT NULL,
  `responsible_user_id` int(11) DEFAULT NULL,
  `schema_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `category`
--

INSERT INTO `category` (`category_id`, `name`, `description`, `slug`, `parent_id`, `lft`, `rgt`, `notify`, `external_user_id`, `responsible_user_id`, `schema_id`) VALUES
(1, 'Administración', NULL, 'Administración', NULL, 1, 22, NULL, NULL, NULL, 1),
(2, 'Facturación', NULL, 'Facturación', 1, 4, 5, NULL, NULL, NULL, 1),
(3, 'Intimación', NULL, 'Intimación', 1, 6, 7, NULL, NULL, NULL, 1),
(4, 'Area técnica', NULL, 'Area técnica', NULL, 23, 62, NULL, NULL, NULL, 1),
(6, 'Reconexión', '', 'Reconexión', 4, 60, 61, 1, NULL, NULL, 1),
(7, 'Router - Conf. router', NULL, 'Router - Conf. router', 4, 26, 27, NULL, NULL, NULL, 1),
(8, 'Resuelto confirmar cliente', NULL, 'Resuelto confirmar cliente', 4, 28, 29, NULL, NULL, NULL, 1),
(9, 'Llamar a cliente', NULL, 'Llamar a cliente', 4, 30, 31, NULL, NULL, NULL, 1),
(10, 'Nota de crédito', NULL, 'Nota de crédito', 1, 8, 9, NULL, NULL, NULL, 1),
(11, 'Varios', NULL, 'Varios', 1, 10, 11, NULL, NULL, NULL, 1),
(12, 'Correos', NULL, 'Correos', 4, 32, 33, NULL, NULL, NULL, 1),
(13, 'Sin Internet', NULL, 'Sin Internet', 4, 34, 41, NULL, NULL, NULL, 1),
(15, 'BAJA', '', 'BAJA', NULL, 187, 188, 1, 212, NULL, 1),
(17, 'Dejó de pagar - Problemas económicos', '', 'Dejó de pagar - Problemas económicos', 15, 110, 111, 0, NULL, NULL, 1),
(18, 'Imposible comunicarse', '', 'Imposible comunicarse', 15, 108, 109, 0, NULL, NULL, 1),
(19, 'No cumplió acuerdo retirar equipo', '', 'No cumplió acuerdo retirar equipo', 15, 106, 107, 0, NULL, NULL, 1),
(20, 'Nunca pagó', '', 'Nunca pagó', 15, 104, 105, 0, NULL, NULL, 1),
(22, 'Real - PC rota', '', 'Real - PC rota', 15, 102, 103, 0, NULL, NULL, 1),
(23, 'Real - por mudanza', '', 'Real - por mudanza', 15, 100, 101, 0, NULL, NULL, 1),
(24, 'Real - Problemas cambio compañía', '', 'Real - Problemas cambio compañía', 15, 98, 99, 0, NULL, NULL, 1),
(25, 'Real - Problemas técnicos', '', 'Real - Problemas técnicos', 15, 96, 97, 0, NULL, NULL, 1),
(26, 'Sin especificar causa', '', 'Sin especificar causa', 15, 94, 95, 0, NULL, NULL, 1),
(27, 'Suspendida', '', 'Suspendida', 15, 92, 93, 0, NULL, NULL, 1),
(28, 'Competencia - Tarde', NULL, 'Competencia - Tarde', NULL, 137, 138, NULL, NULL, NULL, 1),
(30, 'Duplicidad', NULL, 'Duplicidad', NULL, 139, 140, NULL, NULL, NULL, 1),
(31, 'Instalaciones', '', 'Instalaciones', NULL, 187, 188, 1, 7, NULL, 1),
(32, 'Instalaciones nos llaman', NULL, 'Instalaciones nos llaman', NULL, 145, 146, NULL, NULL, NULL, 1),
(42, 'Venta routers instaladores', NULL, 'Venta routers instaladores', NULL, 147, 148, NULL, NULL, NULL, 1),
(43, 'Autoinstalable', NULL, 'Autoinstalable', NULL, 149, 150, NULL, NULL, NULL, 1),
(45, 'Cambio de Patch', NULL, 'Cambio de Patch', 13, 35, 36, NULL, NULL, NULL, 1),
(46, 'Cambio de POE', NULL, 'Cambio de POE', 13, 37, 38, NULL, NULL, NULL, 1),
(48, 'Problemas PC Cliente', NULL, 'Problemas PC Cliente', 13, 39, 40, NULL, NULL, NULL, 1),
(52, 'Torre (temporal)', NULL, 'Torre (temporal)', 4, 42, 43, NULL, NULL, NULL, 1),
(53, 'Instalaciones de Empresa', NULL, 'Instalaciones de Empresa', 31, 142, 143, NULL, NULL, NULL, 1),
(55, 'Redireccionar', NULL, 'Redireccionar', 4, 44, 45, NULL, NULL, NULL, 1),
(56, 'Mala Señal', NULL, 'Mala Señal', 4, 46, 47, NULL, NULL, NULL, 1),
(57, 'Por Garantia Instalacion', NULL, 'Por Garantia Instalacion', 4, 48, 49, NULL, NULL, NULL, 1),
(58, 'Sin Revisar', NULL, 'Sin Revisar', 4, 50, 51, NULL, NULL, NULL, 1),
(59, 'BAJAS PROBLEMATICAS', NULL, 'BAJAS PROBLEMATICAS', 15, 84, 89, NULL, NULL, NULL, 1),
(60, 'Cliente ya retiró el equipo', NULL, 'Cliente ya retiró el equipo', 59, 85, 86, NULL, NULL, NULL, 1),
(61, 'Problema administrativo para recuperar equipo', NULL, 'Problema administrativo para recuperar equipo', 59, 87, 88, NULL, NULL, NULL, 1),
(62, 'INSTALACIONES CANCELADAS', NULL, 'INSTALACIONES CANCELADAS', NULL, 151, 174, NULL, NULL, NULL, 1),
(63, 'Sin cobertura', NULL, 'Sin cobertura', 62, 152, 153, NULL, NULL, NULL, 1),
(64, 'Contrató otro servicio', NULL, 'Contrató otro servicio', 62, 154, 155, NULL, NULL, NULL, 1),
(65, 'Datos de contacto erroneos', NULL, 'Datos de contacto erroneos', 62, 156, 157, NULL, NULL, NULL, 1),
(66, 'Instalacion duplicada', NULL, 'Instalacion duplicada', 62, 158, 159, NULL, NULL, NULL, 1),
(67, 'Condiciones distintas a lo vendido', NULL, 'Condiciones distintas a lo vendido', 62, 160, 161, NULL, NULL, NULL, 1),
(68, 'No lo quiere mas', NULL, 'No lo quiere mas', 62, 162, 163, NULL, NULL, NULL, 1),
(69, 'Cliente no confiable', NULL, 'Cliente no confiable', 62, 164, 165, NULL, NULL, NULL, 1),
(70, 'Esperó demasiado', NULL, 'Esperó demasiado', 62, 166, 167, NULL, NULL, NULL, 1),
(71, 'TRASLADOS', NULL, 'TRASLADOS', 4, 52, 57, NULL, NULL, NULL, 1),
(72, 'Traslado interno', NULL, 'Traslado interno', 71, 53, 54, NULL, NULL, NULL, 1),
(73, 'Traslado externo', NULL, 'Traslado externo', 71, 55, 56, NULL, NULL, NULL, 1),
(74, 'Cliente indeciso', NULL, 'Cliente indeciso', 62, 168, 169, NULL, NULL, NULL, 1),
(75, 'Baja duplicada', NULL, 'Baja duplicada', 15, 90, 91, NULL, NULL, NULL, 1),
(76, 'Enlace interno malo', NULL, 'Enlace interno malo', 78, 176, 177, NULL, NULL, NULL, 1),
(77, 'Ultima milla mala', NULL, 'Ultima milla mala', 78, 178, 179, NULL, NULL, NULL, 1),
(78, 'AREA INGENIERIA', NULL, 'AREA INGENIERIA', NULL, 175, 184, NULL, NULL, NULL, 1),
(79, 'Monitorear con APP', NULL, 'Monitorear con APP', 78, 180, 181, NULL, NULL, NULL, 1),
(80, 'Llamada verificacion', NULL, 'Llamada verificacion', 78, 182, 183, NULL, NULL, NULL, 1),
(81, 'Domicilios complicados para instalar', NULL, 'Domicilios complicados para instalar', 62, 170, 171, NULL, NULL, NULL, 1),
(82, 'Relevamiento negativo en sitio', NULL, 'Relevamiento negativo en sitio', 62, 172, 173, NULL, NULL, NULL, 1),
(83, 'ANTI-BAJA', NULL, 'ANTI-BAJA', 4, 58, 59, NULL, NULL, NULL, 1),
(84, 'BAJAS IRRECUPERABLES', NULL, 'BAJAS IRRECUPERABLES', NULL, 185, 186, NULL, NULL, NULL, 1),
(85, 'Informe de primer pago', 'se seleccionara esta categoría cuando llamemos a los clientes que recién le instalan  para informar el importe de la primer factura', 'Informe de primer pago', 1, 14, 15, 0, NULL, NULL, 1),
(86, 'BAJAS MENSUALES', 'se cargaran con este nombre las bajas que tomen todas las administrativas durante el mes', 'BAJAS MENSUALES', 1, 16, 17, 0, NULL, NULL, 1),
(87, 'DESCUENTO TECNICO', '', 'DESCUENTO TECNICO', 1, 18, 19, 0, NULL, NULL, 1),
(88, 'SIN REVISAR', 'en esta categoría la gente de área técnica va a cargar los llamados que reciba para administracion y que no pueden  derivar  al decimo', 'SIN REVISAR', 1, 20, 21, 0, NULL, NULL, 1),
(89, 'Gestión de Cobranza', 'Ticket de cobranza', 'gestion-de-cobranza', 1, 2, 3, 0, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `color`
--

CREATE TABLE `color` (
  `color_id` int(11) NOT NULL,
  `color` varchar(7) NOT NULL,
  `order` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `slug` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `color`
--

INSERT INTO `color` (`color_id`, `color`, `order`, `name`, `slug`) VALUES
(1, '#1DC268', 0, 'Verde', 'green'),
(2, '#FFBC60', 2, 'Amarillo', 'yellow'),
(3, '#FF2C13', 4, 'Rojo', 'red'),
(4, '#2774E8', 1, 'Azul', 'blue'),
(5, '#F56D1F', 3, 'Naranja', 'orange'),
(6, '#691CF5', 5, 'Violeta', 'violet');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `history`
--

CREATE TABLE `history` (
  `history_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `datetime` int(11) DEFAULT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `observation`
--

CREATE TABLE `observation` (
  `observation_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order` int(11) DEFAULT '1',
  `title` varchar(45) NOT NULL,
  `description` text NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `datetime` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `schema`
--

CREATE TABLE `schema` (
  `schema_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `class` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `schema`
--

INSERT INTO `schema` (`schema_id`, `name`, `class`) VALUES
(1, 'Default', 'app\\modules\\ticket\\components\\schemas\\SchemaDefault'),
(2, 'Cobranza', 'app\\modules\\ticket\\components\\schemas\\SchemaCobranza');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `schema_has_status`
--

CREATE TABLE `schema_has_status` (
  `schema_has_status_id` int(11) NOT NULL,
  `schema_id` int(11) DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `schema_has_status`
--

INSERT INTO `schema_has_status` (`schema_has_status_id`, `schema_id`, `status_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 2, 7),
(8, 2, 8),
(9, 2, 9),
(11, 2, 11),
(12, 2, 12),
(13, 2, 13),
(14, 2, 14),
(15, 2, 15),
(16, 2, 16),
(17, 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `status`
--

CREATE TABLE `status` (
  `status_id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `description` text,
  `is_open` int(11) DEFAULT NULL,
  `generate_action` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `status`
--

INSERT INTO `status` (`status_id`, `name`, `description`, `is_open`, `generate_action`) VALUES
(1, 'nuevo', NULL, 1, 0),
(2, 'en curso (asignado)', NULL, 1, 0),
(3, 'en curso (planificado)', NULL, 1, 0),
(4, 'en espera', NULL, 1, 0),
(5, 'cerrado (resuelto)', NULL, 0, 0),
(6, 'cerrado (no resuelto)', NULL, 0, 0),
(7, 'Compromiso de pago', 'Se compromete a realizar el pago', 1, 0),
(8, 'Extensión', 'Extensión de pago', 1, 0),
(9, 'Informado', 'Ha sido informado', 1, 0),
(11, 'Plan de pago', 'Se realiza plan de pago', 1, 0),
(12, 'Problemas técnicos', 'Presenta problemas técnicos', 1, 0),
(13, 'Pago parcial', 'Se realiza un pago parcial', 1, 0),
(14, 'Tel erróneo sin comunicación', 'No es posible la comunicación', 1, 0),
(15, 'Pagó', 'Informa un pago realizado', 0, 0),
(16, 'Pago 1 de 2', 'Informa primer cuota pagada de dos cuotas totales', 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `status_has_action`
--

CREATE TABLE `status_has_action` (
  `status_has_action_id` int(11) NOT NULL,
  `status_id` int(11) DEFAULT NULL,
  `action_id` int(11) DEFAULT NULL,
  `text_1` varchar(255) DEFAULT NULL,
  `text_2` text,
  `ticket_category_id` int(11) DEFAULT NULL,
  `task_category_id` int(11) DEFAULT NULL,
  `task_type_id` int(11) DEFAULT NULL,
  `ticket_status_id` int(11) DEFAULT NULL,
  `task_status_id` int(11) DEFAULT NULL,
  `task_priority` int(11) DEFAULT NULL,
  `task_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ticket`
--

CREATE TABLE `ticket` (
  `ticket_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `color_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `finish_date` date DEFAULT NULL,
  `start_datetime` int(11) DEFAULT NULL,
  `update_datetime` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `number` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) DEFAULT NULL,
  `contract_id` int(11) DEFAULT NULL,
  `external_id` int(11) DEFAULT NULL,
  `external_tag_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `type`
--

CREATE TABLE `type` (
  `type_id` int(11) NOT NULL,
  `user_group_id` int(11) DEFAULT NULL,
  `name` varchar(45) NOT NULL,
  `description` text,
  `slug` varchar(45) NOT NULL,
  `duration` time DEFAULT '02:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `action`
--
ALTER TABLE `action`
  ADD PRIMARY KEY (`action_id`);

--
-- Indices de la tabla `assignation`
--
ALTER TABLE `assignation`
  ADD PRIMARY KEY (`assignation_id`),
  ADD KEY `fk_assignation_ticket1_idx` (`ticket_id`);

--
-- Indices de la tabla `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`),
  ADD KEY `fk_category_category1_idx` (`parent_id`),
  ADD KEY `fk_category_schema_id` (`schema_id`);

--
-- Indices de la tabla `color`
--
ALTER TABLE `color`
  ADD PRIMARY KEY (`color_id`);

--
-- Indices de la tabla `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `fk_history_ticket1_idx` (`ticket_id`);

--
-- Indices de la tabla `observation`
--
ALTER TABLE `observation`
  ADD PRIMARY KEY (`observation_id`),
  ADD KEY `fk_observation_ticket1_idx` (`ticket_id`);

--
-- Indices de la tabla `schema`
--
ALTER TABLE `schema`
  ADD PRIMARY KEY (`schema_id`);

--
-- Indices de la tabla `schema_has_status`
--
ALTER TABLE `schema_has_status`
  ADD PRIMARY KEY (`schema_has_status_id`),
  ADD KEY `fk_schema_has_status_schema_id` (`schema_id`),
  ADD KEY `fk_schema_has_status_status_id` (`status_id`);

--
-- Indices de la tabla `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`status_id`);

--
-- Indices de la tabla `status_has_action`
--
ALTER TABLE `status_has_action`
  ADD PRIMARY KEY (`status_has_action_id`),
  ADD KEY `fk_status_has_action_status_id` (`status_id`),
  ADD KEY `fk_status_has_action_action_id` (`action_id`),
  ADD KEY `fk_status_has_action_ticket_category_id` (`ticket_category_id`);

--
-- Indices de la tabla `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `fk_ticket_status_idx` (`status_id`),
  ADD KEY `fk_ticket_color1_idx` (`color_id`),
  ADD KEY `fk_ticket_category1_idx` (`category_id`);

--
-- Indices de la tabla `type`
--
ALTER TABLE `type`
  ADD PRIMARY KEY (`type_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `action`
--
ALTER TABLE `action`
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `assignation`
--
ALTER TABLE `assignation`
  MODIFY `assignation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT de la tabla `color`
--
ALTER TABLE `color`
  MODIFY `color_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `history`
--
ALTER TABLE `history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `observation`
--
ALTER TABLE `observation`
  MODIFY `observation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `schema`
--
ALTER TABLE `schema`
  MODIFY `schema_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `schema_has_status`
--
ALTER TABLE `schema_has_status`
  MODIFY `schema_has_status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `status`
--
ALTER TABLE `status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `status_has_action`
--
ALTER TABLE `status_has_action`
  MODIFY `status_has_action_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ticket`
--
ALTER TABLE `ticket`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `type`
--
ALTER TABLE `type`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `assignation`
--
ALTER TABLE `assignation`
  ADD CONSTRAINT `fk_assignation_ticket1` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`ticket_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `category`
--
ALTER TABLE `category`
  ADD CONSTRAINT `fk_category_category1` FOREIGN KEY (`parent_id`) REFERENCES `category` (`category_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_category_schema_id` FOREIGN KEY (`schema_id`) REFERENCES `schema` (`schema_id`);

--
-- Filtros para la tabla `history`
--
ALTER TABLE `history`
  ADD CONSTRAINT `fk_history_ticket1` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`ticket_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `observation`
--
ALTER TABLE `observation`
  ADD CONSTRAINT `fk_observation_ticket1` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`ticket_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `schema_has_status`
--
ALTER TABLE `schema_has_status`
  ADD CONSTRAINT `fk_schema_has_status_schema_id` FOREIGN KEY (`schema_id`) REFERENCES `schema` (`schema_id`),
  ADD CONSTRAINT `fk_schema_has_status_status_id` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`);

--
-- Filtros para la tabla `status_has_action`
--
ALTER TABLE `status_has_action`
  ADD CONSTRAINT `fk_status_has_action_action_id` FOREIGN KEY (`action_id`) REFERENCES `action` (`action_id`),
  ADD CONSTRAINT `fk_status_has_action_status_id` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`),
  ADD CONSTRAINT `fk_status_has_action_ticket_category_id` FOREIGN KEY (`ticket_category_id`) REFERENCES `category` (`category_id`);

--
-- Filtros para la tabla `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `fk_ticket_category1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_ticket_color1` FOREIGN KEY (`color_id`) REFERENCES `color` (`color_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_ticket_status` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
