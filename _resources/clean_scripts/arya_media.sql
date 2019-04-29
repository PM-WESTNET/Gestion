-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Servidor: westnet-data
-- Tiempo de generación: 15-04-2019 a las 16:28:30
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
-- Base de datos: `arya_media`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `data`
--

CREATE TABLE `data` (
  `data_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `attribute` varchar(45) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  `value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `media`
--

CREATE TABLE `media` (
  `media_id` int(11) NOT NULL,
  `title` varchar(140) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `base_url` varchar(255) DEFAULT NULL,
  `relative_url` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `mime` varchar(45) DEFAULT NULL,
  `size` float DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `extension` varchar(10) DEFAULT NULL,
  `create_date` date DEFAULT NULL,
  `create_time` time DEFAULT NULL,
  `create_timestamp` int(11) DEFAULT NULL,
  `status` enum('enabled','disabled') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_media`
--

CREATE TABLE `model_has_media` (
  `media_id` int(11) NOT NULL,
  `model_id` int(11) NOT NULL,
  `model` varchar(255) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `model_has_media_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sized`
--

CREATE TABLE `sized` (
  `sized_id` int(11) NOT NULL,
  `base_url` varchar(255) DEFAULT NULL,
  `relative_url` varchar(255) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `media_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `data`
--
ALTER TABLE `data`
  ADD PRIMARY KEY (`data_id`),
  ADD KEY `fk_data_media1_idx` (`media_id`);

--
-- Indices de la tabla `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`media_id`);

--
-- Indices de la tabla `model_has_media`
--
ALTER TABLE `model_has_media`
  ADD PRIMARY KEY (`model_has_media_id`),
  ADD KEY `fk_model_has_media_media` (`media_id`);

--
-- Indices de la tabla `sized`
--
ALTER TABLE `sized`
  ADD PRIMARY KEY (`sized_id`),
  ADD KEY `fk_table1_media1_idx` (`media_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `data`
--
ALTER TABLE `data`
  MODIFY `data_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `media`
--
ALTER TABLE `media`
  MODIFY `media_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `model_has_media`
--
ALTER TABLE `model_has_media`
  MODIFY `model_has_media_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sized`
--
ALTER TABLE `sized`
  MODIFY `sized_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `data`
--
ALTER TABLE `data`
  ADD CONSTRAINT `fk_data_media1` FOREIGN KEY (`media_id`) REFERENCES `media` (`media_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `model_has_media`
--
ALTER TABLE `model_has_media`
  ADD CONSTRAINT `fk_model_has_media_media` FOREIGN KEY (`media_id`) REFERENCES `media` (`media_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `sized`
--
ALTER TABLE `sized`
  ADD CONSTRAINT `fk_table1_media1` FOREIGN KEY (`media_id`) REFERENCES `media` (`media_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
