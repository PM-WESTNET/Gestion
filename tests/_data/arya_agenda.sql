SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `default_duration` time NOT NULL,
  `slug` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `category` (`category_id`, `name`, `description`, `default_duration`, `slug`) VALUES
(1, 'Genérica', 'Tarea genérica', '02:00:00', 'generic');

CREATE TABLE `event` (
  `event_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `event_type_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `body` text,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `datetime` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `event_type` (
  `event_type_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `slug` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `event_type` (`event_type_id`, `name`, `description`, `slug`) VALUES
(1, 'Cambio de estado', 'Un usuario realizo un cambio de estado', 'status_change'),
(2, 'Nota agregada', 'Un usuario agrego una nota', 'note_added');

CREATE TABLE `notification` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `status` varchar(100) DEFAULT NULL,
  `datetime` int(11) DEFAULT NULL,
  `reason` text,
  `show` tinyint(1) NOT NULL DEFAULT '1',
  `is_expired_reminder` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `status` (
  `status_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `color` varchar(100) DEFAULT NULL,
  `slug` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `status` (`status_id`, `name`, `description`, `color`, `slug`) VALUES
(1, 'Creada', 'Tarea creada', 'normal', 'created'),
(2, 'Pendiente', 'Tarea pendiente', 'warning', 'pending'),
(3, 'En progreso', 'Tarea en progreso', 'info', 'in_progress'),
(4, 'Detenida', 'Tarea detenida', 'danger', 'stopped'),
(5, 'Completada', 'Tarea completada', 'success', 'completed');

CREATE TABLE `task` (
  `task_id` int(11) NOT NULL,
  `task_type_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `creator_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `datetime` int(11) DEFAULT NULL,
  `priority` int(11) NOT NULL,
  `duration` time NOT NULL,
  `slug` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `task_type` (
  `task_type_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `slug` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `task_type` (`task_type_id`, `name`, `description`, `slug`) VALUES
(1, 'Tarea global', 'Esta tarea sera notificada a todos los usuarios del sistema', 'global'),
(2, 'Tarea por usuario', 'Esta tarea sera notificada a usuarios seleccionados', 'by_user');

CREATE TABLE `user_group` (
  `group_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `descripion` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_group_has_user` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

ALTER TABLE `event`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `fk_task_event` (`task_id`),
  ADD KEY `fk_event_type` (`event_type_id`);

ALTER TABLE `event_type`
  ADD PRIMARY KEY (`event_type_id`);

ALTER TABLE `notification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `fk_task_notification` (`task_id`),
  ADD KEY `ix_notification_user_id` (`user_id`);

ALTER TABLE `status`
  ADD PRIMARY KEY (`status_id`);

ALTER TABLE `task`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `fk_task_type_idx` (`task_type_id`),
  ADD KEY `fk_status_idx` (`status_id`),
  ADD KEY `fk_category_idx` (`category_id`),
  ADD KEY `fk_task_idx` (`parent_id`);

ALTER TABLE `task_type`
  ADD PRIMARY KEY (`task_type_id`);

ALTER TABLE `user_group`
  ADD PRIMARY KEY (`group_id`);

ALTER TABLE `user_group_has_user`
  ADD KEY `fk_user_group` (`group_id`);


ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `event`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `event_type`
  MODIFY `event_type_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `task`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `task_type`
  MODIFY `task_type_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `user_group`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `event`
  ADD CONSTRAINT `fk_event_type` FOREIGN KEY (`event_type_id`) REFERENCES `event_type` (`event_type_id`),
  ADD CONSTRAINT `fk_task_event` FOREIGN KEY (`task_id`) REFERENCES `task` (`task_id`);

ALTER TABLE `notification`
  ADD CONSTRAINT `fk_task_notification` FOREIGN KEY (`task_id`) REFERENCES `task` (`task_id`);

ALTER TABLE `task`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`),
  ADD CONSTRAINT `fk_status` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`),
  ADD CONSTRAINT `fk_task` FOREIGN KEY (`parent_id`) REFERENCES `task` (`task_id`),
  ADD CONSTRAINT `fk_task_type` FOREIGN KEY (`task_type_id`) REFERENCES `task_type` (`task_type_id`);

ALTER TABLE `user_group_has_user`
  ADD CONSTRAINT `fk_user_group` FOREIGN KEY (`group_id`) REFERENCES `user_group` (`group_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
