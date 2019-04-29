SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE `assignation` (
  `ecopago_id` int(11) NOT NULL,
  `collector_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `datetime` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `assignation` (`ecopago_id`, `collector_id`, `date`, `time`, `datetime`) VALUES
(1, 1, '2015-12-23', '13:24:00', 1450877074);

CREATE TABLE `batch_closure` (
  `batch_closure_id` int(11) NOT NULL,
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
  `money_box_account_id` int(11) DEFAULT NULL,
  `difference` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `batch_closure_has_payout` (
  `batch_closure_id` int(11) NOT NULL,
  `payout_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cashier` (
  `cashier_id` int(11) NOT NULL,
  `address_id` int(11) DEFAULT NULL,
  `ecopago_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `number` varchar(20) NOT NULL,
  `document_number` varchar(20) NOT NULL,
  `document_type` enum('DNI','Otro') NOT NULL DEFAULT 'DNI',
  `user_id` int(11) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `cashier` (`cashier_id`, `address_id`, `ecopago_id`, `username`, `name`, `lastname`, `number`, `document_number`, `document_type`, `user_id`, `status`) VALUES
(1, NULL, 1, '', 'Diego', 'Fernández', '123', '26996558', 'DNI', 4, 'active');

CREATE TABLE `collector` (
  `collector_id` int(11) NOT NULL,
  `address_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `number` varchar(20) NOT NULL,
  `document_number` varchar(20) NOT NULL,
  `document_type` enum('DNI','Otro') NOT NULL DEFAULT 'DNI',
  `password` varchar(255) NOT NULL,
  `limit` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `collector` (`collector_id`, `address_id`, `name`, `lastname`, `number`, `document_number`, `document_type`, `password`, `limit`) VALUES
(1, NULL, 'Carlos', 'Renati', '321', '23123242', 'DNI', '$2y$13$pnr5e7nXUW1wPj6cQugNzeOfgvNqhH0Ixn7wmdzvr0/2odOrODVKi', 2000); -- Password = '321'

CREATE TABLE `commission` (
  `commission_id` int(11) NOT NULL,
  `ecopago_id` int(11) NOT NULL,
  `create_datetime` int(11) NOT NULL,
  `update_datetime` int(11) DEFAULT NULL,
  `type` enum('percentage','fixed') NOT NULL DEFAULT 'percentage',
  `value` double NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `commission` (`commission_id`, `ecopago_id`, `create_datetime`, `update_datetime`, `type`, `value`) VALUES
(1, 1, 1450876974, NULL, 'percentage', 10),
(2, 2, 1450876974, NULL, 'fixed', 100);

CREATE TABLE `credential` (
  `credential_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `cashier_id` int(11) NOT NULL,
  `datetime` int(11) DEFAULT NULL,
  `status` enum('pending','in_progress','completed','canceled') NOT NULL DEFAULT 'pending',
  `customer_number` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `daily_closure` (
  `daily_closure_id` int(11) NOT NULL,
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
  `close_datetime` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ecopago` (
  `ecopago_id` int(11) NOT NULL,
  `address_id` int(11) DEFAULT NULL,
  `status_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `create_datetime` int(11) NOT NULL,
  `update_datetime` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `limit` double DEFAULT NULL,
  `number` varchar(50) DEFAULT NULL,
  `provider_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `ecopago` (`ecopago_id`, `address_id`, `status_id`, `account_id`, `create_datetime`, `update_datetime`, `name`, `description`, `limit`, `number`, `provider_id`) VALUES
(1, NULL, 1, 115, 1449859325, 1536348100, 'Martínez', 'Café Martínez', 100000, '1', 2),
(2, NULL, 1, 115, 1449859325, 1536348175, 'Alameda', 'Alameda', 100000, '2', 3);

CREATE TABLE `justification` (
  `justification_id` int(11) NOT NULL,
  `payout_id` int(11) DEFAULT NULL,
  `cause` varchar(255) DEFAULT NULL,
  `type` enum('reprint','cancellation') DEFAULT NULL,
  `date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `payout` (
  `payout_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `ecopago_id` int(11) NOT NULL,
  `cashier_id` int(11) NOT NULL,
  `customer_number` varchar(45) NOT NULL,
  `amount` double NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `datetime` int(11) NOT NULL,
  `number` varchar(45) NOT NULL DEFAULT '',
  `status` enum('valid','reversed','closed_by_batch','closed') NOT NULL DEFAULT 'valid',
  `batch_closure_id` int(11) DEFAULT NULL,
  `daily_closure_id` int(11) DEFAULT NULL,
  `period_closure_id` int(11) DEFAULT NULL,
  `copy_number` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `period_closure` (
  `period_closure_id` int(11) NOT NULL,
  `datetime` int(11) NOT NULL,
  `cashier_id` int(11) NOT NULL,
  `payment_count` int(11) NOT NULL,
  `first_payout_number` varchar(50) NOT NULL,
  `last_payout_number` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `status` enum('closed','canceled') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `status` (
  `status_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `slug` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `status` (`status_id`, `name`, `description`, `slug`) VALUES
(1, 'Habilitado', 'Ecopago habilitado', 'enabled'),
(2, 'Deshabilitado', 'Ecopago deshabilitado', 'disabled');

CREATE TABLE `withdrawal` (
  `withdrawal_id` int(11) NOT NULL,
  `daily_closure_id` int(11) NOT NULL,
  `cashier_id` int(11) NOT NULL,
  `amount` double NOT NULL,
  `datetime` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `assignation`
  ADD PRIMARY KEY (`ecopago_id`,`collector_id`),
  ADD KEY `fk_ecopago_has_collector_collector1_idx` (`collector_id`),
  ADD KEY `fk_ecopago_has_collector_ecopago1_idx` (`ecopago_id`);

ALTER TABLE `batch_closure`
  ADD PRIMARY KEY (`batch_closure_id`),
  ADD KEY `fk_closing_batch_ecopago1_idx` (`ecopago_id`),
  ADD KEY `fk_closing_batch_collector1_idx` (`collector_id`);

ALTER TABLE `batch_closure_has_payout`
  ADD PRIMARY KEY (`batch_closure_id`,`payout_id`),
  ADD KEY `fk_batch_closure_has_payout_payout1_idx` (`payout_id`),
  ADD KEY `fk_batch_closure_has_payout_batch_closure1_idx` (`batch_closure_id`);

ALTER TABLE `cashier`
  ADD PRIMARY KEY (`cashier_id`),
  ADD KEY `fk_cashier_ecopago1_idx` (`ecopago_id`);

ALTER TABLE `collector`
  ADD PRIMARY KEY (`collector_id`);

ALTER TABLE `commission`
  ADD PRIMARY KEY (`commission_id`),
  ADD KEY `fk_commission_ecopago1_idx` (`ecopago_id`);

ALTER TABLE `credential`
  ADD PRIMARY KEY (`credential_id`),
  ADD KEY `fk_credential_cashier1_idx` (`cashier_id`);

ALTER TABLE `daily_closure`
  ADD PRIMARY KEY (`daily_closure_id`),
  ADD KEY `fk_daily_closure_cashier1_idx` (`cashier_id`),
  ADD KEY `fk_daily_closure_ecopago1_idx` (`ecopago_id`);

ALTER TABLE `ecopago`
  ADD PRIMARY KEY (`ecopago_id`),
  ADD KEY `fk_ecopago_status_idx` (`status_id`),
  ADD KEY `fk_ecopago_provider_id` (`provider_id`);

ALTER TABLE `justification`
  ADD PRIMARY KEY (`justification_id`),
  ADD KEY `fk_payout_id` (`payout_id`);

ALTER TABLE `payout`
  ADD PRIMARY KEY (`payout_id`),
  ADD KEY `fk_payout_ecopago1_idx` (`ecopago_id`),
  ADD KEY `fk_payout_cashier1_idx` (`cashier_id`);

ALTER TABLE `period_closure`
  ADD PRIMARY KEY (`period_closure_id`),
  ADD KEY `fk_period_closure_cashier1_idx` (`cashier_id`);

ALTER TABLE `status`
  ADD PRIMARY KEY (`status_id`);

ALTER TABLE `withdrawal`
  ADD PRIMARY KEY (`withdrawal_id`);


ALTER TABLE `batch_closure`
  MODIFY `batch_closure_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cashier`
  MODIFY `cashier_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `collector`
  MODIFY `collector_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `commission`
  MODIFY `commission_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `credential`
  MODIFY `credential_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `daily_closure`
  MODIFY `daily_closure_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ecopago`
  MODIFY `ecopago_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `justification`
  MODIFY `justification_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `payout`
  MODIFY `payout_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `period_closure`
  MODIFY `period_closure_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `withdrawal`
  MODIFY `withdrawal_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `assignation`
  ADD CONSTRAINT `fk_ecopago_has_collector_collector1` FOREIGN KEY (`collector_id`) REFERENCES `collector` (`collector_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_ecopago_has_collector_ecopago1` FOREIGN KEY (`ecopago_id`) REFERENCES `ecopago` (`ecopago_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `batch_closure`
  ADD CONSTRAINT `fk_closing_batch_collector1` FOREIGN KEY (`collector_id`) REFERENCES `collector` (`collector_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_closing_batch_ecopago1` FOREIGN KEY (`ecopago_id`) REFERENCES `ecopago` (`ecopago_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `batch_closure_has_payout`
  ADD CONSTRAINT `fk_batch_closure_has_payout_batch_closure1` FOREIGN KEY (`batch_closure_id`) REFERENCES `batch_closure` (`batch_closure_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_batch_closure_has_payout_payout1` FOREIGN KEY (`payout_id`) REFERENCES `payout` (`payout_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `cashier`
  ADD CONSTRAINT `fk_cashier_ecopago1` FOREIGN KEY (`ecopago_id`) REFERENCES `ecopago` (`ecopago_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `commission`
  ADD CONSTRAINT `fk_commission_ecopago1` FOREIGN KEY (`ecopago_id`) REFERENCES `ecopago` (`ecopago_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `credential`
  ADD CONSTRAINT `fk_credential_cashier1` FOREIGN KEY (`cashier_id`) REFERENCES `cashier` (`cashier_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `ecopago`
  ADD CONSTRAINT `fk_ecopago_status` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `justification`
  ADD CONSTRAINT `fk_payout_id` FOREIGN KEY (`payout_id`) REFERENCES `payout` (`payout_id`);

ALTER TABLE `payout`
  ADD CONSTRAINT `fk_payout_cashier1` FOREIGN KEY (`cashier_id`) REFERENCES `cashier` (`cashier_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_payout_ecopago1` FOREIGN KEY (`ecopago_id`) REFERENCES `ecopago` (`ecopago_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `period_closure`
  ADD CONSTRAINT `fk_period_closure_cashier1` FOREIGN KEY (`cashier_id`) REFERENCES `cashier` (`cashier_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
