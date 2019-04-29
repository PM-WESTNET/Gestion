SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE `generic_type` (
  `service` varchar(10) NOT NULL,
  `type` varchar(50) NOT NULL,
  `code` varchar(10) NOT NULL,
  `description` varchar(200) NOT NULL,
  `datefrom` date DEFAULT NULL,
  `dateto` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `message_log` (
  `error_log_id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `description` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `money_quotation` (
  `money_quotation_id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `price` double NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `point_of_sale` (
  `point_of_sale_id` int(11) NOT NULL,
  `number` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `blocked` tinyint(4) DEFAULT NULL,
  `dateto` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `generic_type`
  ADD PRIMARY KEY (`service`,`type`,`code`);

ALTER TABLE `message_log`
  ADD PRIMARY KEY (`error_log_id`);

ALTER TABLE `money_quotation`
  ADD PRIMARY KEY (`money_quotation_id`);

ALTER TABLE `point_of_sale`
  ADD PRIMARY KEY (`point_of_sale_id`);


ALTER TABLE `message_log`
  MODIFY `error_log_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `money_quotation`
  MODIFY `money_quotation_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `point_of_sale`
  MODIFY `point_of_sale_id` int(11) NOT NULL AUTO_INCREMENT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
