SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE `assignation` (
  `assignation_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `user_id` varchar(45) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `external_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `slug` varchar(45) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `lft` int(11) DEFAULT NULL,
  `rgt` int(11) DEFAULT NULL,
  `notify` int(11) DEFAULT NULL,
  `external_user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `category` (`category_id`, `name`, `description`, `slug`, `parent_id`, `lft`, `rgt`, `notify`, `external_user_id`) VALUES
(1, 'Administrativo', 'Administrativo', 'admin', NULL, 1, 2, 0, NULL),
(2, 'Técnico', 'Técnico', 'tecnico', NULL, 3, 4, 0, NULL);

CREATE TABLE `color` (
  `color_id` int(11) NOT NULL,
  `color` varchar(7) NOT NULL,
  `order` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `slug` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `color` (`color_id`, `color`, `order`, `name`, `slug`) VALUES
(1, '#FF2C13', 4, 'Rojo', 'red'),
(2, '#2774E8', 0, 'Azul', 'blue'),
(3, '#1DC268', 1, 'Verde', 'green'),
(4, '#FFBC60', 2, 'Amarillo', 'yellow'),
(5, '#F56D1F', 3, 'Naranja', 'orange'),
(6, '#691CF5', 5, 'Violeta', 'violet');

CREATE TABLE `history` (
  `history_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `datetime` int(11) DEFAULT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `status` (
  `status_id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `description` text,
  `is_open` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `status` (`status_id`, `name`, `description`, `is_open`) VALUES
(1, 'Activo', 'Ticket activo', 1),
(2, 'Cerrado', 'Ticket cerrado. No puede modificarse', 0);

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
  `external_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `type` (
  `type_id` int(11) NOT NULL,
  `user_group_id` int(11) DEFAULT NULL,
  `name` varchar(45) NOT NULL,
  `description` text,
  `slug` varchar(45) NOT NULL,
  `duration` time DEFAULT '02:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `assignation`
  ADD PRIMARY KEY (`assignation_id`),
  ADD KEY `fk_assignation_ticket1_idx` (`ticket_id`);

ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`),
  ADD KEY `fk_category_category1_idx` (`parent_id`);

ALTER TABLE `color`
  ADD PRIMARY KEY (`color_id`);

ALTER TABLE `history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `fk_history_ticket1_idx` (`ticket_id`);

ALTER TABLE `observation`
  ADD PRIMARY KEY (`observation_id`),
  ADD KEY `fk_observation_ticket1_idx` (`ticket_id`);

ALTER TABLE `status`
  ADD PRIMARY KEY (`status_id`);

ALTER TABLE `ticket`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `fk_ticket_status_idx` (`status_id`),
  ADD KEY `fk_ticket_color1_idx` (`color_id`),
  ADD KEY `fk_ticket_category1_idx` (`category_id`);

ALTER TABLE `type`
  ADD PRIMARY KEY (`type_id`);


ALTER TABLE `assignation`
  MODIFY `assignation_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `color`
  MODIFY `color_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `observation`
  MODIFY `observation_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ticket`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `type`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `assignation`
  ADD CONSTRAINT `fk_assignation_ticket1` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`ticket_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `category`
  ADD CONSTRAINT `fk_category_category1` FOREIGN KEY (`parent_id`) REFERENCES `category` (`category_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `history`
  ADD CONSTRAINT `fk_history_ticket1` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`ticket_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `observation`
  ADD CONSTRAINT `fk_observation_ticket1` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`ticket_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `ticket`
  ADD CONSTRAINT `fk_ticket_category1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_ticket_color1` FOREIGN KEY (`color_id`) REFERENCES `color` (`color_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_ticket_status` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
