SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE `account` (
  `account_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `is_usable` tinyint(1) DEFAULT NULL,
  `code` varchar(45) DEFAULT NULL,
  `lft` int(11) DEFAULT NULL,
  `rgt` int(11) DEFAULT NULL,
  `parent_account_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `account` (`account_id`, `name`, `is_usable`, `code`, `lft`, `rgt`, `parent_account_id`) VALUES
(112, 'ACTIVO', 0, '1', 1, 68, NULL),
(113, 'ACTIVO CORRIENTE', 0, '1.1', 2, 45, 112),
(114, 'DISPONIBILIDADES', 0, '1.1.1', 3, 16, 113),
(115, 'Caja pesos', 1, '1.1.1.1', 4, 5, 114),
(116, 'Caja Moneda Extranjera', 1, '1.1.1.2', 6, 7, 114),
(117, 'Fondo fijo', 1, '1.1.1.3', 8, 9, 114),
(118, 'Banco', 1, '1.1.1.4', 10, 11, 114),
(119, 'Valores a depositar', 1, '1.1.1.5', 12, 13, 114),
(120, 'CUENTAS POR COBRAR', 0, '1.1.2', 17, 22, 113),
(121, 'Deudores por ventas', 1, '1.1.2.1', 18, 19, 120),
(122, 'Deudores morosos', 1, '1.1.2.2', 20, 21, 120),
(123, 'OTROS CRÉDITOS', 0, '1.1.3', 23, 38, 113),
(124, 'Retencion impuestos a las ganancias', 1, '1.1.3.1', 24, 25, 123),
(125, 'Anticipo impuestos a las ganancias', 1, '1.1.3.2', 26, 27, 123),
(126, 'IVA crédito fiscal 21%', 1, '1.1.3.3', 28, 29, 123),
(127, 'IVA crédito fiscal 10.5 %', 1, '1.1.3.4', 30, 31, 123),
(128, 'Retencion IVA', 1, '1.1.3.5', 32, 33, 123),
(129, 'Retencion ingresos brutos', 1, '1.1.3.6', 34, 35, 123),
(130, 'Saldo a favor ingresos a las ganancias', 1, '1.1.3.7', 36, 37, 123),
(131, 'BIENES DE CAMBIO', 0, '1.1.4', 39, 44, 113),
(132, 'Equipos', 1, '1.1.4.1', 40, 41, 131),
(133, 'Equipos en Demo', 1, '1.1.4.2', 42, 43, 131),
(134, 'ACTIVO NO CORRIENTE', 0, '1.2', 46, 67, 112),
(135, 'BIENES DE USO', 0, '1.2.1', 47, 56, 134),
(136, 'Equipos computación', 1, '1.2.1.1', 48, 49, 135),
(137, 'Software', 1, '1.2.1.2', 50, 51, 135),
(138, 'Instalaciones', 1, '1.2.1.3', 52, 53, 135),
(139, 'Muebles y útiles', 1, '1.2.1.4', 54, 55, 135),
(140, 'AMORTIZACIONES ACUMULADAS BIENES DE USO', 0, '1.2.2', 57, 66, 134),
(141, 'Amortización acumulada equipos computación', 1, '1.2.2.1', 58, 59, 140),
(142, 'Amortización acumulada software', 1, '1.2.2.2', 60, 61, 140),
(143, 'Amortización acumulada instalaciones', 1, '1.2.2.3', 62, 63, 140),
(144, 'Amortización acumulada muebles y útiles', 1, '1.2.2.4', 64, 65, 140),
(145, 'PASIVO', 0, '2', 69, 120, NULL),
(146, 'PASIVO CORRIENTE', 0, '2.1', 70, 109, 145),
(147, 'DEUDAS SOCIALES', 0, '2.1.1', 71, 84, 146),
(148, 'Sueldos a pagar', 1, '2.1.1.1', 72, 73, 147),
(149, 'Cargas sociales a pagar', 1, '2.1.1.2', 74, 75, 147),
(150, 'Provision SAC a pagar', 1, '2.1.1.3', 76, 77, 147),
(151, 'Provision SAC cargas sociales a pagar', 1, '2.1.1.4', 78, 79, 147),
(152, 'Beneficios al personal', 1, '2.1.1.5', 80, 81, 147),
(153, 'Previsión Despidos', 1, '2.1.1.6', 82, 83, 147),
(154, 'DEUDAS FISCALES', 0, '2.1.2', 85, 98, 146),
(155, 'Ingresos brutos a pagar', 1, '2.1.2.1', 86, 87, 154),
(156, 'IVA débito fiscal 21%', 1, '2.1.2.2', 88, 89, 154),
(157, 'IVA débito fiscal 10.5%', 1, '2.1.2.3', 90, 91, 154),
(158, 'IVA a pagar', 1, '2.1.2.4', 92, 93, 154),
(159, 'Retención ganancias a depositar', 1, '2.1.2.5', 94, 95, 154),
(160, 'Provision impuesto a las ganancias', 1, '2.1.2.6', 96, 97, 154),
(161, 'DEUDAS COMERCIALES', 0, '2.1.3', 99, 104, 146),
(162, 'Proveedores', 1, '2.1.3.1', 100, 101, 161),
(163, 'Anticipos de clientes', 1, '2.1.3.2', 102, 103, 161),
(164, 'PROVISIONES VARIAS', 0, '2.1.4', 105, 108, 146),
(165, 'Provision gastos', 1, '2.1.4.1', 106, 107, 164),
(166, 'PASIVO NO CORRIENTE', 0, '2.2', 110, 119, 145),
(167, 'DEUDAS COMERCIALES A LARGO PLAZO', 0, '2.2.1', 111, 114, 166),
(168, 'Deudas proveedores a largo plazo', 1, '2.2.1.1', 112, 113, 167),
(169, 'OTRAS DEUDAS', 0, '2.2.2', 115, 118, 166),
(170, 'Deudas misc', 1, '2.2.2.1', 116, 117, 169),
(171, 'PATRIMONIO NETO', 0, '3', 121, 132, NULL),
(172, 'Capital', 1, '3.1', 122, 123, 171),
(173, 'Aporte irrevocable ', 1, '3.2', 124, 125, 171),
(174, 'Reserva legal', 1, '3.3', 126, 127, 171),
(175, 'Resultados no asignados', 1, '3.4', 128, 129, 171),
(176, 'Resultado del ejercicio', 1, '3.5', 130, 131, 171),
(177, 'INGRESOS', 0, '4', 133, 150, NULL),
(178, 'INGRESOS OPERATIVOS', 0, '4.1', 134, 145, 177),
(179, 'VENTAS', 0, '4.1.1', 135, 142, 178),
(180, 'Ingresos por ventas', 1, '4.1.1.1', 136, 137, 179),
(181, 'Venta de equipos', 1, '4.1.1.2', 138, 139, 179),
(182, 'Venta servicios misc', 1, '4.1.1.3', 140, 141, 179),
(183, 'OTROS INGRESOS', 0, '4.1.2', 143, 144, 178),
(184, 'INGRESOS FINANCIEROS', 0, '4.2', 146, 149, 177),
(185, 'Resultado de inversiones financieras', 1, '4.2.1', 147, 148, 184),
(186, 'GASTOS', 0, '5', 151, 222, NULL),
(187, 'GASTOS OPERATIVOS', 0, '5.1', 152, 209, 186),
(188, 'GASTOS DE PRODUCCION', 0, '5.1.1', 153, 164, 187),
(189, 'Gastos operativos general', 1, '5.1.1.1', 154, 155, 188),
(190, 'Haberes', 1, '5.1.1.2', 156, 157, 188),
(191, 'Cargas Sociales', 1, '5.1.1.3', 158, 159, 188),
(192, 'Costo de equipos', 1, '5.1.1.4', 160, 161, 188),
(193, 'Materiales', 1, '5.1.1.5', 162, 163, 188),
(194, 'GASTOS COMERCIALES', 0, '5.1.2', 165, 174, 187),
(195, 'Haberes comerciales', 1, '5.1.2.1', 166, 167, 194),
(196, 'Cargas sociales comerciales', 1, '5.1.2.2', 168, 169, 194),
(197, 'Comisiones', 1, '5.1.2.3', 170, 171, 194),
(198, 'Publicidad', 1, '5.1.2.4', 172, 173, 194),
(199, 'GASTOS DE ADMINISTRACION', 0, '5.1.3', 175, 196, 187),
(200, 'Haberes administrativos', 1, '5.1.3.1', 176, 177, 199),
(201, 'Cargas sociales administrativas', 1, '5.1.3.2', 178, 179, 199),
(202, 'Honorarios', 1, '5.1.3.3', 180, 181, 199),
(203, 'Alquileres', 1, '5.1.3.4', 182, 183, 199),
(204, 'Servicios de luz, agua, etc', 1, '5.1.3.5', 184, 185, 199),
(205, 'Mantenimiento y limpieza', 1, '5.1.3.6', 186, 187, 199),
(206, 'Artículos librería', 1, '5.1.3.7', 188, 189, 199),
(207, 'Cadetería y correo', 1, '5.1.3.8', 190, 191, 199),
(208, 'Seguros', 1, '5.1.3.9', 192, 193, 199),
(209, 'Diferencias por Redondeo', 1, '5.1.3.10', 194, 195, 199),
(210, 'GASTOS FISCALES', 0, '5.1.4', 197, 204, 187),
(211, 'Impuestos ingresos brutos', 1, '5.1.4.1', 198, 199, 210),
(212, 'Impuestos a las ganancias', 1, '5.1.4.2', 200, 201, 210),
(213, 'Impuestos transacciones bancarias', 1, '5.1.4.3', 202, 203, 210),
(214, 'OTROS GASTOS ', 0, '5.1.5', 205, 208, 187),
(215, 'Intereses y recargos', 1, '5.1.5.1', 206, 207, 214),
(216, 'GASTOS NO OPERATIVOS', 0, '5.2', 210, 221, 186),
(217, 'AMORTIZACIONES', 0, '5.2.1', 211, 220, 216),
(218, 'Amortizaciones instalaciones', 1, '5.2.1.1', 212, 213, 217),
(219, 'Amortizaciones equipos de computación', 1, '5.2.1.2', 214, 215, 217),
(220, 'Amortizacion software', 1, '5.2.1.3', 216, 217, 217),
(221, 'Amortizacion muebles y útiles', 1, '5.2.1.4', 218, 219, 217),
(222, 'Cuenta Corriente', 1, '1.1.1.6', 14, 15, 217);

CREATE TABLE `accounting_period` (
  `accounting_period_id` int(11) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `number` int(11) DEFAULT NULL,
  `status` enum('open','closed') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `accounting_period` (`accounting_period_id`, `name`, `date_from`, `date_to`, `number`, `status`) VALUES
(1, '2015', '2015-01-01', '2015-12-31', 1, 'closed'),
(2, '2016', '2016-01-01', '2016-12-31', 2, 'open');

CREATE TABLE `account_config` (
  `account_config_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `class` varchar(250) NOT NULL,
  `classMovement` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `account_config` (`account_config_id`, `name`, `class`, `classMovement`) VALUES
(1, 'Facturar', 'app\\modules\\sale\\models\\Bill', 'app\\modules\\accounting\\components\\impl\\BillMovement'),
(2, 'Cobro', 'app\\modules\\checkout\\models\\Payment', 'app\\modules\\accounting\\components\\impl\\PaymentMovement'),
(3, 'Factura Proveedores', 'app\\modules\\provider\\models\\ProviderBill', 'app\\modules\\accounting\\components\\impl\\ProviderBillMovement'),
(4, 'Pago a Proveedores', 'app\\modules\\provider\\models\\ProviderPayment', 'app\\modules\\accounting\\components\\impl\\ProviderPaymentMovement');

CREATE TABLE `account_config_has_account` (
  `account_config_has_account_id` int(11) NOT NULL,
  `account_config_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `is_debit` tinyint(1) DEFAULT NULL,
  `attrib` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `account_config_has_account` (`account_config_has_account_id`, `account_config_id`, `account_id`, `is_debit`, `attrib`) VALUES
(1, 1, 121, 1, 'total'),
(2, 1, 126, 0, '1'),
(3, 1, 180, 0, 'rest'),
(4, 2, 115, 1, '1'),
(5, 2, 121, 0, 'total'),
(6, 3, 121, 0, 'total'),
(7, 3, 126, 1, '1'),
(8, 3, 162, 1, 'rest'),
(9, 4, 115, 0, '1'),
(10, 4, 121, 1, 'total');

CREATE TABLE `account_movement` (
  `account_movement_id` int(11) NOT NULL,
  `description` varchar(150) DEFAULT NULL,
  `status` enum('draft','closed','broken') NOT NULL DEFAULT 'draft',
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `accounting_period_id` int(11) NOT NULL,
  `partner_distribution_model_id` int(11) DEFAULT NULL,
  `daily_money_box_account_id` int(11) DEFAULT NULL,
  `check` tinyint(1) DEFAULT '0',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `creator_user_id` int(11) DEFAULT NULL,
  `updater_user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `account_movement_item` (
  `account_movement_item_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `account_movement_id` int(11) NOT NULL,
  `debit` double DEFAULT NULL,
  `credit` double DEFAULT NULL,
  `status` enum('draft','closed','conciled') NOT NULL DEFAULT 'draft',
  `check` tinyint(1) DEFAULT '0',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `creator_user_id` int(11) DEFAULT NULL,
  `updater_user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `account_movement_relation` (
  `account_movement_relation_id` int(11) NOT NULL,
  `class` varchar(100) NOT NULL,
  `model_id` int(11) NOT NULL,
  `account_movement_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `address` (
  `address_id` int(11) NOT NULL,
  `street` varchar(100) DEFAULT NULL,
  `between_street_1` varchar(100) DEFAULT NULL,
  `between_street_2` varchar(100) DEFAULT NULL,
  `number` varchar(45) DEFAULT NULL,
  `block` varchar(45) DEFAULT NULL,
  `house` varchar(45) DEFAULT NULL,
  `floor` int(11) DEFAULT NULL,
  `department` varchar(45) DEFAULT NULL,
  `tower` varchar(45) DEFAULT NULL,
  `zone_id` int(11) DEFAULT NULL,
  `geocode` varchar(100) DEFAULT NULL,
  `indications` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `address` (`address_id`, `street`, `between_street_1`, `between_street_2`, `number`, `block`, `house`, `floor`, `department`, `tower`, `zone_id`, `geocode`, `indications`) VALUES
(1, 'San Martín', '', '', NULL, '', '', NULL, '', '', 2, NULL, NULL),
(2, 'San Martín', '', '', NULL, '', '', NULL, '', '', 3, NULL, NULL),
(3, 'San Martín', '', '', NULL, '', '', NULL, '', '', 3, NULL, NULL),
(4, 'San Martín', '', '', NULL, '', '', NULL, '', '', 3, NULL, NULL),
(5, 'San Martín', '', '', NULL, '', '', NULL, '', '', 3, NULL, NULL),
(6, 'San Martín', '', '', NULL, '', '', NULL, '', '', 3, NULL, NULL),
(7, 'San Martín', '', '', NULL, '', '', NULL, '', '', 3, NULL, NULL);

CREATE TABLE `app_failed_register` (
  `app_failed_register_id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `document_type` varchar(45) NOT NULL,
  `document_number` varchar(45) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(45) NOT NULL,
  `status` enum('pending','closed') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
('Administrativos', 3, 1457013591),
('cashier', 4, 1450878089),
('seller', 6, 1456933178),
('technical', 2, 1456933178);

CREATE TABLE `auth_item` (
  `name` varchar(64) NOT NULL,
  `type` int(11) NOT NULL,
  `description` text,
  `rule_name` varchar(64) DEFAULT NULL,
  `data` text,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `group_code` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`, `group_code`) VALUES
('/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account-config/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account-config/add-account', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account-config/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account-config/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account-config/delete-account', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account-config/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account-config/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account-config/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account-movement/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account-movement/add-item', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account-movement/close', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account-movement/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account-movement/daily-box-create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account-movement/daily-box-update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account-movement/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account-movement/delete-item', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account-movement/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account-movement/list-items', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account-movement/resume', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account-movement/small-box-create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account-movement/small-box-update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account-movement/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account-movement/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account/listtreeaccounts', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account/moveaccount', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/account/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/accounting-period/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/accounting-period/close', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/accounting-period/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/accounting-period/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/accounting-period/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/accounting-period/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/accounting-period/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/conciliation/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/conciliation/close', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/conciliation/conciliar', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/conciliation/conciliate', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/conciliation/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/conciliation/deconciliate', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/conciliation/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/conciliation/get-resume-items', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/conciliation/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/conciliation/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/conciliation/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/conciliation/view-conciliation', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box-account/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box-account/close-daily-box', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box-account/close-small-box', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box-account/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box-account/daily-box-movements', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box-account/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box-account/export', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/accounting/money-box-account/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box-account/moneyboxaccounts', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box-account/movements', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box-account/open-small-box', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box-account/small-box-index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box-account/small-box-movements', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box-account/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box-account/validate-type', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box-account/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box-type/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box-type/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box-type/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box-type/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box-type/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box-type/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box/add-operation-type', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box/delete-operation-type', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box/list-operation-type', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/money-box/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/operation-type/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/operation-type/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/operation-type/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/operation-type/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/operation-type/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/operation-type/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/resume/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/resume/add-item', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/resume/change-state', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/resume/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/resume/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/resume/delete-item', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/resume/details', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/resume/download-resume', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/accounting/resume/import-resume', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/resume/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/resume/resume-by-account', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/resume/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/accounting/resume/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/afip/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/afip/taxes-book/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/afip/taxes-book/add-bill', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/afip/taxes-book/add-buy-bills', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/afip/taxes-book/add-sale-bills', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/afip/taxes-book/buy', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/afip/taxes-book/buy-bills', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/afip/taxes-book/buy-bills-added', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/afip/taxes-book/buy-bills-totals', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/afip/taxes-book/close', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/afip/taxes-book/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/afip/taxes-book/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/afip/taxes-book/export-excel', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/afip/taxes-book/print', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/afip/taxes-book/remove-bill', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/afip/taxes-book/sale', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/afip/taxes-book/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/afip/taxes-book/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/category/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/category/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/category/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/category/fetch-category', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/category/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/category/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/category/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/default/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/default/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/default/update-agenda', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/event-type/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/event-type/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/event-type/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/event-type/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/event-type/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/event-type/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/event/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/event/build-note', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/event/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/event/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/event/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/event/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/event/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/notification/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/notification/batch-change-status', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/notification/change-status', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/notification/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/notification/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/notification/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/notification/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/notification/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/status/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/status/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/status/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/status/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/status/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/status/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/task-type/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/task-type/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/task-type/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/task-type/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/task-type/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/task-type/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/task/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/task/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/task/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/task/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/task/postpone-task', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/task/quick-create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/task/quick-update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/task/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/task/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/user-group/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/user-group/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/user-group/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/user-group/get-user-by-username', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/user-group/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/user-group/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/agenda/user-group/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/backup/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/backup/default/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/backup/default/clean', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/backup/default/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/backup/default/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/backup/default/download', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/backup/default/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/backup/default/restore', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/backup/default/syncdown', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/backup/default/upload', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/default/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment-method/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment-method/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment-method/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment-method/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment-method/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment-method/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment-plan/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment-plan/cancel', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment-plan/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment-plan/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment-plan/list', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment-receipt/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment-receipt/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment-receipt/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment-receipt/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment-receipt/pdf', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment-receipt/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment-receipt/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment/add-bill', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment/add-item', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment/apply', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment/cancel-payment', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment/close', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment/confirm-file', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment/create-debt', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment/current-account', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment/delete-bill', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment/delete-item', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment/open-bill', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment/pagofacil-payment-view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment/pagofacil-payments-import', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment/pagofacil-payments-index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment/pay-bill', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment/pdf', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment/remove-bill', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/checkout/payment/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/category/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/category/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/category/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/category/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/category/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/category/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/config/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/config/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/config/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/config/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/config/reset', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/config/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/config/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/default/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/default/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/item/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/item/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/item/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/item/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/item/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/item/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/rule/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/rule/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/rule/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/rule/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/rule/load-attributes', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/rule/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/config/rule/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/batch-closure/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/batch-closure/cancel', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/batch-closure/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/batch-closure/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/batch-closure/render', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/batch-closure/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/cashier/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/cashier/add-cashier', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/cashier/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/cashier/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/cashier/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/cashier/list-by-ecopago', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/cashier/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/cashier/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/collector/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/collector/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/collector/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/collector/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/collector/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/collector/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/commission/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/commission/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/commission/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/commission/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/commission/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/commission/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/daily-closure/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/daily-closure/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/daily-closure/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/daily-closure/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/daily-closure/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/daily-closure/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/default/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/default/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/ecopago/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/ecopago/collectors', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/ecopago/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/ecopago/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/ecopago/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/ecopago/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/ecopago/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/batch-closure/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/batch-closure/cancel', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/batch-closure/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/batch-closure/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/batch-closure/get-preview', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/batch-closure/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/batch-closure/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/batch-closure/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/batch-closure/view-payouts', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/cashier/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/cashier/change-password', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/collector/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/collector/get-collector-info', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/credential/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/credential/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/credential/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/credential/reprint-ask', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/credential/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/customer/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/customer/get-customer-info', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/customer/payout-history', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/daily-closure/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/daily-closure/cancel', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/daily-closure/close', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/daily-closure/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/daily-closure/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/daily-closure/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/daily-closure/open-cash-register', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/daily-closure/preview', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/daily-closure/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/payout/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/payout/ajax-info', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/payout/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/payout/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/payout/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/payout/print', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/payout/reverse', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/payout/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/period-closure/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/period-closure/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/period-closure/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/period-closure/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/period-closure/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/period-closure/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/site/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/site/captcha', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/site/error', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/site/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/site/print-instructions', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/withdrawal/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/withdrawal/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/withdrawal/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/withdrawal/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/withdrawal/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/frontend/withdrawal/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/payout/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/payout/ajax-info', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/payout/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/payout/daily-payout-list', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/payout/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/payout/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/payout/payout-list', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/payout/reverse', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/payout/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/payout/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/period-closure/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/period-closure/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/period-closure/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/period-closure/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/period-closure/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/period-closure/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/status/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/status/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/status/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/status/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/status/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/status/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/withdrawal/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/withdrawal/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/withdrawal/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/withdrawal/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/withdrawal/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ecopagos/withdrawal/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/gridview/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/gridview/export/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/gridview/export/download', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/import/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/import/importer/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/import/importer/import', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/invoice/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/invoice/default/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/invoice/default/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/invoice/messages/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/invoice/messages/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/invoice/migracionafip/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/invoice/migracionafip/comprobante', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/invoice/migracionafip/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/invoice/migracionafip/migrar', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/log/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/log/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/log/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/media/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/media/default/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/media/default/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/media/image/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/media/image/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/media/image/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/media/image/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/media/image/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/media/image/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/partner/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/partner/liquidation/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/partner/liquidation/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/partner/liquidation/liquidate', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/partner/partner-distribution-model/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/partner/partner-distribution-model/add-partner', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/partner/partner-distribution-model/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/partner/partner-distribution-model/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/partner/partner-distribution-model/delete-partner', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/partner/partner-distribution-model/get-by-company', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/partner/partner-distribution-model/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/partner/partner-distribution-model/list-partner', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/partner/partner-distribution-model/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/partner/partner-distribution-model/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/partner/partner/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/partner/partner/account', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/partner/partner/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/partner/partner/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/partner/partner/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/partner/partner/input', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/partner/partner/show-account-detail', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/partner/partner/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/partner/partner/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/partner/partner/withdraw', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/paycheck/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/paycheck/checkbook/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/paycheck/checkbook/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/paycheck/checkbook/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/paycheck/checkbook/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/paycheck/checkbook/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/paycheck/checkbook/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/paycheck/paycheck/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/paycheck/paycheck/change-state', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/paycheck/paycheck/checkbooks', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/paycheck/paycheck/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/paycheck/paycheck/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/paycheck/paycheck/encartera', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/paycheck/paycheck/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/paycheck/paycheck/select-paycheck', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/paycheck/paycheck/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/paycheck/paycheck/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/default/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider-bill/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider-bill/add-item', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider-bill/add-tax', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider-bill/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider-bill/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider-bill/delete-item', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider-bill/delete-tax', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider-bill/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider-bill/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider-bill/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider-payment/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider-payment/add-bill', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider-payment/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider-payment/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider-payment/delete-bill', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider-payment/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider-payment/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider-payment/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider/current-account', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider/debts', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider/find-by-name', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/provider/provider/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/address/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/address/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/address/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/address/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/address/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/address/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/bill/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/bill/close', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/bill/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/bill/group', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/bill/import', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/bill/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/bill/options', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/bill/types', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/bill/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/category/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/category/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/category/options', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/category/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/company/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/company/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/company/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/company/options', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/company/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/customer/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/customer/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/customer/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/customer/options', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/customer/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/default/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/default/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/default/login', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/product/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/product/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/product/options', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/api/product/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/batch-invoice/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/batch-invoice/bill-type', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/batch-invoice/close-invoices', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/batch-invoice/close-invoices-index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/batch-invoice/get-process', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/batch-invoice/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/batch-invoice/invoice', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill-type/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill-type/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill-type/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill-type/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill-type/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill-type/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill/add-product', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill/barcode', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill/close', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill/delete-detail', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill/email', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill/generate', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill/group', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill/handwrite-detail', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill/history', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill/invoice-customer', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill/open', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill/pdf', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill/remove-customer', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill/resend', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill/search-customer', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill/search-product', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill/select-customer', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill/update-qty', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/bill/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/category/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/category/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/category/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/category/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/category/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/category/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/company/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/company/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/company/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/company/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/company/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/company/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract-detail/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract-detail/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract-detail/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract-detail/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract-detail/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract-detail/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract/active-contract', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract/active-contract-again', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/sale/contract/contract/active-new-items', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract/add-contract-detail', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract/cancel-contract', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract/change-company', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract/change-ip', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/sale/contract/contract/change-node', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract/change-status-contract-detail', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract/funding-plans', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract/history', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract/list-contracts', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract/low-process-contract', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/sale/contract/contract/rejected-service', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/sale/contract/contract/remove-contract-detail', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract/set-tentative-node', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/sale/contract/contract/show-additionals', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract/update-connection', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract/vendor-list', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/contract/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/plan-feature/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/plan-feature/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/plan-feature/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/plan-feature/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/plan-feature/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/plan-feature/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/plan/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/plan/batch-updater', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/plan/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/plan/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/plan/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/plan/price-history', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/plan/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/plan/update-price', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/plan/update-prices', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/contract/plan/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/currency/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/currency/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/currency/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/currency/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/currency/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/currency/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer-category/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer-category/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer-category/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer-category/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer-category/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer-category/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer-class/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer-class/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer-class/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer-class/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer-class/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer-class/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer-has-discount/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer-has-discount/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer-has-discount/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer-has-discount/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer-has-discount/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer-has-discount/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer-log/*', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/sale/customer-log/create', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/sale/customer-log/delete', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/sale/customer-log/index', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/sale/customer-log/update', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/sale/customer-log/view', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/sale/customer/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer/barcode', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer/change-company', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer/classhistory', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer/create-embed', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer/createcontract', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer/customer-carnet', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer/customer-tickets', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer/debtors', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer/discounts', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer/find-by-name', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer/pending-installations', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer/positive-balance-customers', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/sale/customer/search', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer/sell', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/customer/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/default/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/default/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/discount/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/discount/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/discount/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/discount/discount-by-product', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/discount/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/discount/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/discount/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/document-type/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/document-type/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/document-type/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/document-type/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/document-type/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/document-type/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/funding-plan/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/funding-plan/add-funding-product', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/funding-plan/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/funding-plan/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/funding-plan/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/funding-plan/totals', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/funding-plan/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/funding-plan/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/invoice-class/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/invoice-class/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/invoice-class/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/invoice-class/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/invoice-class/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/invoice-class/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/invoice/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/point-of-sale/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/point-of-sale/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/point-of-sale/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/point-of-sale/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/point-of-sale/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/point-of-sale/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product-price/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product-price/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product-price/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product-price/graph', 3, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`, `group_code`) VALUES
('/sale/product-price/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product-price/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product-price/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product-to-invoice/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product-to-invoice/activate', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product-to-invoice/cancel', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product-to-invoice/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product-to-invoice/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product-to-invoice/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product/barcode', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product/batch-updater', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product/price-history', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product/print-barcodes', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product/stock', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product/update-price', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product/update-prices', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product/update-stock', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/product/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/profile-class/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/profile-class/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/profile-class/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/profile-class/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/profile-class/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/profile-class/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/stock-movement/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/stock-movement/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/stock-movement/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/stock-movement/graph', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/stock-movement/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/stock-movement/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/tax-condition/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/tax-condition/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/tax-condition/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/tax-condition/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/tax-condition/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/tax-condition/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/tax-rate/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/tax-rate/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/tax-rate/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/tax-rate/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/tax-rate/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/tax-rate/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/tax/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/tax/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/tax/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/tax/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/tax/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/tax/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/unit/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/unit/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/unit/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/unit/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/unit/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sale/unit/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/sequre/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/site/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/site/about', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/site/captcha', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/site/contact', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/site/error', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/site/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/site/login', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/site/logout', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/test/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/test/all-buttons', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/test/captcha', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/test/error', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/test/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/category/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/category/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/category/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/category/get-external-users', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/category/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/category/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/category/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/color/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/color/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/color/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/color/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/color/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/color/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/customer/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/customer/get-customer-info', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/customer/ticket-history', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/default/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/default/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/observation/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/observation/build-observation', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/observation/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/status/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/status/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/status/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/status/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/status/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/status/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/ticket/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/ticket/close', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/ticket/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/ticket/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/ticket/history', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/ticket/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/ticket/list', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/ticket/observation', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/ticket/open-tickets', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/ticket/reopen', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/ticket/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/ticket/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/type/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/type/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/type/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/type/get-categories', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/type/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/type/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/ticket/type/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/auth-item-group/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/auth-item-group/bulk-activate', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/auth-item-group/bulk-deactivate', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/auth-item-group/bulk-delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/auth-item-group/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/auth-item-group/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/auth-item-group/grid-page-size', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/auth-item-group/grid-sort', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/auth-item-group/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/auth-item-group/toggle-attribute', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/auth-item-group/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/auth-item-group/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/auth/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/auth/captcha', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/auth/change-own-password', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/auth/confirm-email', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/auth/confirm-email-receive', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/auth/confirm-registration-email', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/auth/login', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/auth/logout', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/auth/password-recovery', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/auth/password-recovery-receive', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/auth/registration', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/permission/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/permission/bulk-activate', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/permission/bulk-deactivate', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/permission/bulk-delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/permission/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/permission/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/permission/grid-page-size', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/permission/grid-sort', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/permission/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/permission/refresh-routes', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/permission/set-child-permissions', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/permission/set-child-routes', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/permission/toggle-attribute', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/permission/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/permission/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/role/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/role/bulk-activate', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/role/bulk-deactivate', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/role/bulk-delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/role/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/role/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/role/grid-page-size', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/role/grid-sort', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/role/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/role/set-child-permissions', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/role/set-child-roles', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/role/toggle-attribute', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/role/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/role/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user-permission/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user-permission/set', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user-permission/set-roles', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user-visit-log/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user-visit-log/bulk-activate', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user-visit-log/bulk-deactivate', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user-visit-log/bulk-delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user-visit-log/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user-visit-log/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user-visit-log/grid-page-size', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user-visit-log/grid-sort', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user-visit-log/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user-visit-log/toggle-attribute', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user-visit-log/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user-visit-log/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user/bulk-activate', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user/bulk-deactivate', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user/bulk-delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user/change-password', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user/grid-page-size', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user/grid-sort', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user/toggle-attribute', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/user-management/user/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ads/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ads/barcode', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ads/print', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ads/print-ads-by-batch', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/westnet/ads/print-empty-ads', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ads/print-one-empty-ads', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/westnet/api/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/api/contract/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/api/contract/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/api/contract/find-by-node', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/api/contract/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/api/contract/list-by-id', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/api/contract/mora', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/api/contract/options', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/api/contract/set-tentative-node', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/westnet/api/contract/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/api/customer/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/api/customer/account', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/api/customer/bill-pdf', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/westnet/api/customer/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/api/customer/find-by-category', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/westnet/api/customer/find-by-contract', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/api/customer/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/api/customer/list-by-id', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/api/customer/options', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/api/customer/update-email-geocode', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/api/customer/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/api/zone/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/api/zone/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/api/zone/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/api/zone/options', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/api/zone/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/batch/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/batch/company-to-customer', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/batch/company-to-customer-assign', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/batch/discount-to-customer', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/batch/discount-to-customer-assign', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/batch/get-process', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/batch/plan-to-customer-assign', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/batch/plans-to-customer', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/connection-forced-historial/*', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/westnet/connection-forced-historial/create', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/westnet/connection-forced-historial/delete', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/westnet/connection-forced-historial/index', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/westnet/connection-forced-historial/update', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/westnet/connection-forced-historial/view', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/westnet/connection/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/connection/disable', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/connection/enable', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/connection/force', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/batch-closure/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/batch-closure/cancel', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/batch-closure/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/batch-closure/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/batch-closure/render', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/batch-closure/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/cashier/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/cashier/add-cashier', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/cashier/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/cashier/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/cashier/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/cashier/list-by-ecopago', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/cashier/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/cashier/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/collector/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/collector/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/collector/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/collector/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/collector/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/collector/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/commission/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/commission/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/commission/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/commission/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/commission/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/commission/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/daily-closure/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/daily-closure/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/daily-closure/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/daily-closure/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/daily-closure/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/daily-closure/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/default/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/default/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/ecopago/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/ecopago/collectors', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/ecopago/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/ecopago/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/ecopago/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/ecopago/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/ecopago/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/batch-closure/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/batch-closure/cancel', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/batch-closure/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/batch-closure/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/batch-closure/get-preview', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/batch-closure/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/batch-closure/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/batch-closure/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/batch-closure/view-payouts', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/cashier/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/cashier/change-password', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/collector/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/collector/get-collector-info', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/credential/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/credential/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/credential/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/credential/reprint-ask', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/credential/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/customer/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/customer/get-customer-info', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/customer/payout-history', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/daily-closure/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/daily-closure/cancel', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/daily-closure/close', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/daily-closure/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/daily-closure/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/daily-closure/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/daily-closure/open-cash-register', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/daily-closure/preview', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/daily-closure/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/payout/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/payout/ajax-info', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/payout/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/payout/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/payout/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/payout/print', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/payout/reverse', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/payout/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/period-closure/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/period-closure/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/period-closure/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/period-closure/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/period-closure/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/period-closure/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/site/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/site/captcha', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/site/error', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/site/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/site/print-instructions', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/withdrawal/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/withdrawal/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/withdrawal/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/withdrawal/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/withdrawal/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/frontend/withdrawal/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/payout/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/payout/ajax-info', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/payout/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/payout/daily-payout-list', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/payout/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/payout/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/payout/payout-list', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/payout/reverse', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/payout/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/payout/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/period-closure/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/period-closure/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/period-closure/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/period-closure/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/period-closure/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/period-closure/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/status/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/status/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/status/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/status/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/status/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/status/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/withdrawal/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/withdrawal/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/withdrawal/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/withdrawal/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/withdrawal/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ecopagos/withdrawal/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/empty-ads/*', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/westnet/empty-ads/index', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/westnet/empty-ads/search-ads', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/westnet/ip-range/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ip-range/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ip-range/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ip-range/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ip-range/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/ip-range/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/node/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/node/all-nodes', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/node/assigned-ip', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/node/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/node/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/node/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/node/parent-nodes', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/node/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/node/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/destinatary/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/destinatary/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/destinatary/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/destinatary/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/destinatary/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/destinatary/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/notification/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/notification/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/notification/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/notification/destinataries', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/notification/export', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/notification/get-period-times', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/notification/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/notification/load-scheduler-form', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/notification/send', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/notification/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/notification/update-status', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/notification/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/notification/wizard', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/transport/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/transport/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/transport/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/transport/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/transport/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/notifications/transport/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/product-commission/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/product-commission/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/product-commission/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/product-commission/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/product-commission/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/product-commission/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/sequre/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/server/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/server/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/server/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/server/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/server/move-customers', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/server/restore-customers', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/server/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/server/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/site/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/site/captcha', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/site/error', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/site/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/user-vendor/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/user-vendor/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/user-vendor/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/user-vendor/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/user-vendor/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/user-vendor/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/vendor-commission/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/vendor-commission/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/vendor-commission/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/vendor-commission/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/vendor-commission/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/vendor-commission/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/vendor-liquidation-item/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/vendor-liquidation-item/cancel', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/vendor-liquidation-item/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/vendor-liquidation-item/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/vendor-liquidation-item/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/vendor-liquidation-item/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/vendor-liquidation-item/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/vendor-liquidation/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/vendor-liquidation/batch', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/vendor-liquidation/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/vendor-liquidation/create-bill', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/westnet/vendor-liquidation/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/vendor-liquidation/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/vendor-liquidation/preview', 3, NULL, NULL, NULL, 1476375934, 1476375934, NULL),
('/westnet/vendor-liquidation/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/westnet/vendor-liquidation/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/zone/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/zone/zone/*', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/zone/zone/create', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/zone/zone/delete', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/zone/zone/full-zone', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/zone/zone/index', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/zone/zone/update', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/zone/zone/view', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('/zone/zone/zones-by-name', 3, NULL, NULL, NULL, NULL, NULL, NULL),
('account-config', 2, 'Configuración de cuentas', NULL, NULL, NULL, NULL, 'Contabilidad'),
('accounting-account', 2, 'Plan de Cuentas', NULL, NULL, NULL, NULL, 'Contabilidad'),
('accounting-account-movement', 2, 'Movimiento de cuentas', NULL, NULL, NULL, NULL, 'Contabilidad'),
('accounting-accounting period', 2, 'Períodos Contables', NULL, NULL, NULL, NULL, 'Contabilidad'),
('accounting-conciliation', 2, 'Conciliación Bancaria', NULL, NULL, NULL, NULL, 'Contabilidad'),
('accounting-monex box account', 2, 'Cuentas Monetarias', NULL, NULL, NULL, NULL, 'Contabilidad'),
('accounting-money-box', 2, 'Entidades Monetarias', NULL, NULL, NULL, NULL, 'Contabilidad'),
('accounting-money-box-type', 2, 'Tipos de Entidad Monetaria', NULL, NULL, NULL, NULL, 'Contabilidad'),
('accounting-operation-type', 2, 'Tipos de Operaciones', NULL, NULL, NULL, NULL, 'Contabilidad'),
('accounting-resume', 2, 'Resúmenes bancarios', NULL, NULL, NULL, NULL, 'Contabilidad'),
('active-contract', 2, 'Activar Contrato', NULL, NULL, NULL, NULL, 'Contratos'),
('active-new-item-contract', 2, 'Activar ítem de contrato', NULL, NULL, NULL, NULL, 'Contratos'),
('actualizar-clientes', 2, 'Actualizar Clientes', NULL, NULL, NULL, NULL, 'Clientes'),
('admin', 1, 'Admin', NULL, NULL, NULL, NULL, NULL),
('Administrativo Full', 1, 'Administrativo + Facturación', NULL, NULL, NULL, NULL, NULL),
('Administrativos', 1, 'Administrativo', NULL, NULL, NULL, NULL, NULL),
('ads-create', 2, 'Generar ADS', NULL, NULL, NULL, NULL, 'Contratos'),
('afip-taxes-book', 2, 'Libros IVA Venta y Compra', NULL, NULL, NULL, NULL, 'Contabilidad'),
('agenda-default', 2, 'Agenda Default', NULL, NULL, NULL, NULL, 'Agenda'),
('agenda-event-type', 2, 'Tipos de Eventos', NULL, NULL, NULL, NULL, 'Agenda'),
('agregar-producto', 2, 'Agregar producto a factura', NULL, NULL, NULL, NULL, 'Facturación'),
('assigned-ip', 2, 'Listado de IP asignadas', NULL, NULL, NULL, NULL, 'Nodos'),
('assignRolesToUsers', 2, 'Assign roles to users', NULL, NULL, NULL, NULL, 'userManagement'),
('backup', 2, 'Backup del Sitio', NULL, NULL, NULL, NULL, 'userCommonPermissions'),
('batch-invoice', 2, 'Facturación por Lotes', NULL, NULL, NULL, NULL, 'Facturación'),
('batch-invoice-rol', 1, 'Facturación (Incluye Fact. por Lotes)', NULL, NULL, NULL, NULL, NULL),
('bindUserToIp', 2, 'Bind user to IP', NULL, NULL, NULL, NULL, 'userManagement'),
('cancel-contract', 2, 'Cancelar Contrato', NULL, NULL, NULL, NULL, 'Contratos'),
('cashier', 1, 'Cajero Ecopago', NULL, NULL, NULL, NULL, NULL),
('category-planes', 2, 'Categorías de Planes', NULL, NULL, NULL, NULL, 'Planes'),
('change-empresa', 2, 'Cambiar Empresa', NULL, NULL, NULL, NULL, 'Clientes'),
('change-node-customer', 2, 'Cambiar Nodo a Cliente', NULL, NULL, NULL, NULL, 'Nodos'),
('changeOwnPassword', 2, 'Change own password', NULL, NULL, NULL, NULL, 'userCommonPermissions'),
('changeUserPassword', 2, 'Change user password', NULL, NULL, NULL, NULL, 'userManagement'),
('checkout-payment', 2, 'Generar Pago', NULL, NULL, NULL, NULL, 'Payment'),
('checkout-payment-create', 2, 'Alta de Pago Manual', NULL, NULL, NULL, NULL, 'Clientes'),
('checkout-payment-current-account', 2, 'Cuenta Corriente de Clientes', NULL, NULL, NULL, NULL, 'Clientes'),
('checkout-payment-method', 2, 'Medios de pago', NULL, NULL, NULL, NULL, 'Payment'),
('checkout-payment-plan-index', 2, 'Plan de Pago', NULL, NULL, NULL, NULL, 'Clientes'),
('Cobranzas', 1, 'Cobranzas', NULL, NULL, NULL, NULL, NULL),
('cobros manuales', 1, 'Cobro en oficina', NULL, NULL, NULL, NULL, NULL),
('commonPermission', 2, 'Common permission', NULL, NULL, NULL, NULL, NULL),
('Contable', 1, 'Contable', NULL, NULL, NULL, NULL, NULL),
('contract-view', 2, 'Ver Contrato', NULL, NULL, NULL, NULL, 'Contratos'),
('Contratos', 2, 'Add Contract Detail', NULL, NULL, NULL, NULL, 'Contratos'),
('create-contract-detail', 2, 'Crear Detalle de contrato', NULL, NULL, NULL, NULL, 'Contratos'),
('create-customer', 2, 'Alta Cliente', NULL, NULL, NULL, NULL, 'Clientes'),
('create-node', 2, 'Alta Nodo', NULL, NULL, NULL, NULL, 'Nodos'),
('createUsers', 2, 'Create users', NULL, NULL, NULL, NULL, 'userManagement'),
('customer-debtors', 2, 'Deudores', NULL, NULL, NULL, NULL, 'Clientes'),
('customer-index', 2, 'Ver Clientes', NULL, NULL, NULL, NULL, 'Clientes'),
('customer-zones', 2, 'ABM Zonas de Clientes', NULL, NULL, NULL, NULL, 'Clientes'),
('deleteUsers', 2, 'Delete users', NULL, NULL, NULL, NULL, 'userManagement'),
('ecooff', 1, 'Ecopago Oficina', NULL, NULL, NULL, NULL, NULL),
('ecopago-daily-closure', 2, 'Cierre Diario de Ecopago (Ver)', NULL, NULL, NULL, NULL, 'Ecopagos'),
('ecopagos-batch-closure', 2, 'Cierre de Lote de Ecopagos', NULL, NULL, NULL, NULL, 'Ecopagos'),
('ecopagos-vew', 2, 'Ecopagos (Index/View/Create)', NULL, NULL, NULL, NULL, 'Ecopagos'),
('editUserEmail', 2, 'Edit user email', NULL, NULL, NULL, NULL, 'userManagement'),
('editUsers', 2, 'Edit users', NULL, NULL, NULL, NULL, 'userManagement'),
('Gestión de Bajas', 1, 'Gestión de Bajas', NULL, NULL, NULL, NULL, NULL),
('gestor api', 1, 'Gestor de APIs', NULL, NULL, NULL, NULL, NULL),
('grid-exportar', 2, 'Grid de Exportar', NULL, NULL, NULL, NULL, 'userCommonPermissions'),
('historial-categorías-cliente', 2, 'Historial de categorías de un cliente', NULL, NULL, NULL, NULL, 'Clientes'),
('Index-node-view', 2, 'Ver Nodos (Módulo Westnet)', NULL, NULL, NULL, NULL, 'Nodos'),
('mi-agenda (sin posibilidad de edición)', 2, 'Mi Agenda (Solo index)', NULL, NULL, NULL, NULL, 'Agenda'),
('notification-full', 2, 'Notificaciones', NULL, NULL, NULL, NULL, 'Planes'),
('pago-facil', 2, 'Pagos de Pago Fácil', NULL, NULL, NULL, NULL, 'Payment'),
('partner-account', 2, 'Ver Cuenta de Socio', NULL, NULL, NULL, NULL, 'Socios'),
('partner-create', 2, 'Crear Socio', NULL, NULL, NULL, NULL, 'Socios'),
('partner-delete', 2, 'Eliminar Socio', NULL, NULL, NULL, NULL, 'Socios'),
('partner-liquidation', 2, 'Liquidación Societaria', NULL, NULL, NULL, NULL, 'Socios'),
('partner-partner', 2, 'Listado de Socios', NULL, NULL, NULL, NULL, 'Socios'),
('partner-partner-distributiion-model', 2, 'Modelo de Distribución Societaria', NULL, NULL, NULL, NULL, 'Socios'),
('partner-update', 2, 'Actualizar Socio', NULL, NULL, NULL, NULL, 'Socios'),
('paycheck-paybook', 2, 'ABM Chequeras', NULL, NULL, NULL, NULL, 'Contabilidad'),
('paycheck-paycheck', 2, 'ABM Cheques', NULL, NULL, NULL, NULL, 'Contabilidad'),
('pending-instalation', 2, 'Instalaciones Pendientes', NULL, NULL, NULL, NULL, 'Clientes'),
('product-to-invoice', 2, 'Productos a facturar', NULL, NULL, NULL, NULL, 'Contratos'),
('provider-account-full', 2, 'Gestión de Cuentas de Proveedores', NULL, NULL, NULL, NULL, 'provider'),
('provider-bill-full', 2, 'ABM Comprobantes de Proveedores', NULL, NULL, NULL, NULL, 'provider'),
('provider-provider-create', 2, 'Crear Proveedor', NULL, NULL, NULL, NULL, 'provider'),
('provider-provider-delete', 2, 'Borrar Proveedor', NULL, NULL, NULL, NULL, 'provider'),
('provider-provider-index', 2, 'Listado de Proveedores', NULL, NULL, NULL, NULL, 'provider'),
('provider-provider-payment-full', 2, 'ABM Pagos de Proveedor', NULL, NULL, NULL, NULL, 'provider'),
('provider-provider-update', 2, 'Actualizar Proveedor', NULL, NULL, NULL, NULL, 'provider'),
('remove-contract-detail', 2, 'Remove contract Detail', NULL, NULL, NULL, NULL, 'Contratos'),
('sale-address', 2, 'Gestión de Dirección', NULL, NULL, NULL, NULL, 'Facturación'),
('sale-bill', 2, 'Generar Factura', NULL, NULL, NULL, NULL, 'Facturación'),
('sale-bill-history', 2, 'Resumen de comprobantes', NULL, NULL, NULL, NULL, 'Contabilidad'),
('sale-contract-contract-list-contract', 2, 'Listado de contratos (Para generar tickets)', NULL, NULL, NULL, NULL, 'Tickets'),
('sale-contract-plan-full', 2, 'Gestión de Planes (Incluye cambio de precio de plan)', NULL, NULL, NULL, NULL, 'Planes'),
('sale-customer-customer-carnet', 2, 'Credencial del Cliente', NULL, NULL, NULL, NULL, 'Clientes'),
('sale-customer-customer-ticket', 2, 'Tickets del cliente', NULL, NULL, NULL, NULL, 'Clientes'),
('sale-customer-has-discount', 2, 'ABM Descuentos de Cliente', NULL, NULL, NULL, NULL, 'Clientes'),
('sale-disccount-disccount--by-product', 2, 'Lista de Descuentos (Alta de nuevo cliente)', NULL, NULL, NULL, NULL, 'Clientes'),
('sale-discount', 2, 'ABM Tipos de Descuentos', NULL, NULL, NULL, NULL, 'Contabilidad'),
('seller', 1, 'Vendedor', NULL, NULL, NULL, NULL, NULL),
('Seller-permissions', 2, 'Seller', NULL, NULL, NULL, NULL, 'seller'),
('site-index', 2, 'Site Index', NULL, NULL, NULL, NULL, 'userCommonPermissions'),
('socios-full', 2, 'Socios FULL', NULL, NULL, NULL, NULL, 'Socios'),
('Socios-Prueba', 2, 'Socios Completo (Para borrar)', NULL, NULL, NULL, NULL, 'Socios'),
('stock-movement-full', 2, 'Movimientos de Stock', NULL, NULL, NULL, NULL, 'Productos'),
('task-ABM', 2, 'ABM Tarea', NULL, NULL, NULL, NULL, 'Agenda'),
('Task-category-ABM', 2, 'ABM Categorías de Tarea', NULL, NULL, NULL, NULL, 'Agenda'),
('technical', 1, 'Técnico', NULL, NULL, NULL, NULL, NULL),
('tecnico-create-clientes', 1, 'Tecnico-Alta Clientes', NULL, NULL, NULL, NULL, NULL),
('tecnico-jefe', 1, 'Tecnico Jefe', NULL, NULL, NULL, NULL, NULL),
('ticket-customer-full', 2, 'Historial de Tickets de un cliente', NULL, NULL, NULL, NULL, 'Tickets'),
('ticket-index', 2, 'Ticket Index', NULL, NULL, NULL, NULL, 'Tickets'),
('ticket-observation', 2, 'Generar Observación de Ticket', NULL, NULL, NULL, NULL, 'Tickets'),
('ticket-status', 2, 'Ver Estado de Tickets', NULL, NULL, NULL, NULL, 'Tickets'),
('ticket-ticket', 2, 'Generar Ticket', NULL, NULL, NULL, NULL, 'Tickets'),
('ticket-view', 2, 'Ver Tipos de Tickets', NULL, NULL, NULL, NULL, 'Tickets'),
('tickets-category-full', 2, 'ABM Categorías de Tickets', NULL, NULL, NULL, NULL, 'Tickets'),
('tickets-color-full', 2, 'ABM Colores de Tickets', NULL, NULL, NULL, NULL, 'Tickets'),
('tickets-status-full', 2, 'ABM Estado de Tickets', NULL, NULL, NULL, NULL, 'Tickets'),
('tickets-type', 2, 'ABM Tipos de Tickets', NULL, NULL, NULL, NULL, 'Tickets'),
('update-conection', 2, 'Actualizar Conexión', NULL, NULL, NULL, NULL, 'Contratos'),
('update-contract', 2, 'Actualizar Contracto', NULL, NULL, NULL, NULL, 'Contratos'),
('update-contract-detail', 2, 'Actualizar Detalle de contrato', NULL, NULL, NULL, NULL, 'Contratos'),
('update-zone', 2, 'Actualizar Zonas', NULL, NULL, NULL, NULL, 'seller'),
('user-can-create-bill', 2, 'User can create invoices', NULL, NULL, NULL, NULL, 'Facturación'),
('user-can-create-budget', 2, 'User can create budgets', NULL, NULL, NULL, NULL, 'Facturación'),
('user-can-create-credit', 2, 'User can create credit notes', NULL, NULL, NULL, NULL, 'Facturación'),
('user-can-create-debit', 2, 'User can create debit notes', NULL, NULL, NULL, NULL, 'Facturación'),
('user-can-create-delivery-note', 2, 'User can create delivery notes', NULL, NULL, NULL, NULL, 'Facturación'),
('user-can-create-order', 2, 'User can create orders', NULL, NULL, NULL, NULL, 'Facturación'),
('user-can-delete-bill', 2, 'User can delete invoices', NULL, NULL, NULL, NULL, 'Facturación'),
('user-can-delete-budget', 2, 'User can delete budgets', NULL, NULL, NULL, NULL, 'Facturación'),
('user-can-delete-credit', 2, 'User can delete credit notes', NULL, NULL, NULL, NULL, 'Facturación'),
('user-can-delete-debit', 2, 'User can delete debit notes', NULL, NULL, NULL, NULL, 'Facturación'),
('user-can-delete-delivery-note', 2, 'User can delete delivery notes', NULL, NULL, NULL, NULL, 'Facturación'),
('user-can-delete-order', 2, 'User can delete orders', NULL, NULL, NULL, NULL, 'Facturación'),
('user-can-open-bill', 2, 'User can open invoices', NULL, NULL, NULL, NULL, 'Facturación'),
('user-can-open-budget', 2, 'User can open budgets', NULL, NULL, NULL, NULL, 'Facturación'),
('user-can-open-credit', 2, 'User can open credit notes', NULL, NULL, NULL, NULL, 'Facturación'),
('user-can-open-debit', 2, 'User can open debit notes', NULL, NULL, NULL, NULL, 'Facturación'),
('user-can-open-delivery-note', 2, 'User can open delivery notes', NULL, NULL, NULL, NULL, 'Facturación'),
('user-can-open-order', 2, 'User can open orders', NULL, NULL, NULL, NULL, 'Facturación'),
('user-can-select-vendor', 2, 'Usuario puede seleccionar vendedor', NULL, NULL, NULL, NULL, 'Administrative'),
('user-can-update-bill', 2, 'User can update invoices', NULL, NULL, NULL, NULL, 'Facturación'),
('user-can-update-budget', 2, 'User can update budgets', NULL, NULL, NULL, NULL, 'Facturación'),
('user-can-update-credit', 2, 'User can update credit notes', NULL, NULL, NULL, NULL, 'Facturación'),
('user-can-update-debit', 2, 'User can update debit notes', NULL, NULL, NULL, NULL, 'Facturación'),
('user-can-update-delivery-note', 2, 'User can update delivery notes', NULL, NULL, NULL, NULL, 'Facturación'),
('user-can-update-order', 2, 'User can update orders', NULL, NULL, NULL, NULL, 'Facturación'),
('Vendedor en Oficina', 1, 'Vendedor en Oficina', NULL, NULL, NULL, NULL, NULL),
('ver facturas', 2, 'Ver facturas', NULL, NULL, NULL, NULL, 'Facturación'),
('view-category-ticket', 2, 'Ver Categorías de Tickets', NULL, NULL, NULL, NULL, 'Tickets'),
('view-tickets-color', 2, 'Ver Colores de Tickets', NULL, NULL, NULL, NULL, 'Tickets'),
('viewRegistrationIp', 2, 'View registration IP', NULL, NULL, NULL, NULL, 'userManagement'),
('viewUserEmail', 2, 'View user email', NULL, NULL, NULL, NULL, 'userManagement'),
('viewUserRoles', 2, 'View user roles', NULL, NULL, NULL, NULL, 'userManagement'),
('viewUsers', 2, 'View users', NULL, NULL, NULL, NULL, 'userManagement'),
('viewVisitLog', 2, 'View visit log', NULL, NULL, NULL, NULL, 'userManagement'),
('view_payout_ecopagos', 2, 'Ecopagos - Ver Pagos', NULL, NULL, NULL, NULL, 'Ecopagos'),
('westnet-api', 2, 'Westnet API', NULL, NULL, NULL, NULL, 'API'),
('westnet-comision-full', 2, 'Comisión de Vendedores', NULL, NULL, NULL, NULL, 'Administrative'),
('westnet-user-vendor', 2, 'Westnet -> Vendedores', NULL, NULL, NULL, NULL, 'Administrative'),
('westnet-vendor-liquidation', 2, 'Liquidación de vendedores', NULL, NULL, NULL, NULL, 'Administrative'),
('westnet-vendor-liquidation-item', 2, 'Liquidación de vendedores ítem', NULL, NULL, NULL, NULL, 'Administrative'),
('westnetEcopagoFrontend', 2, 'Westnet ecopago frontend', NULL, NULL, NULL, NULL, NULL),
('zone-zone-full', 2, 'Ver zonas', NULL, NULL, NULL, NULL, 'Clientes');

CREATE TABLE `auth_item_child` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `auth_item_child` (`parent`, `child`) VALUES
('account-config', '/accounting/account-config/*'),
('accounting-account', '/accounting/account/*'),
('accounting-account', '/accounting/account/create'),
('accounting-account', '/accounting/account/delete'),
('accounting-account', '/accounting/account/index'),
('accounting-account', '/accounting/account/listtreeaccounts'),
('accounting-account', '/accounting/account/moveaccount'),
('accounting-account', '/accounting/account/update'),
('accounting-account', '/accounting/account/view'),
('accounting-account-movement', '/accounting/account-movement/*'),
('accounting-accounting period', '/accounting/accounting-period/*'),
('accounting-conciliation', '/accounting/conciliation/*'),
('accounting-conciliation', '/accounting/conciliation/close'),
('accounting-conciliation', '/accounting/conciliation/conciliar'),
('accounting-conciliation', '/accounting/conciliation/conciliate'),
('accounting-conciliation', '/accounting/conciliation/create'),
('accounting-conciliation', '/accounting/conciliation/deconciliate'),
('accounting-conciliation', '/accounting/conciliation/delete'),
('accounting-conciliation', '/accounting/conciliation/get-resume-items'),
('accounting-conciliation', '/accounting/conciliation/index'),
('accounting-conciliation', '/accounting/conciliation/update'),
('accounting-conciliation', '/accounting/conciliation/view'),
('accounting-conciliation', '/accounting/conciliation/view-conciliation'),
('accounting-monex box account', '/accounting/money-box-account/*'),
('accounting-money-box', '/accounting/money-box/*'),
('accounting-money-box-type', '/accounting/money-box-type/*'),
('accounting-operation-type', '/accounting/operation-type/*'),
('accounting-operation-type', '/accounting/operation-type/create'),
('accounting-operation-type', '/accounting/operation-type/delete'),
('accounting-operation-type', '/accounting/operation-type/index'),
('accounting-operation-type', '/accounting/operation-type/update'),
('accounting-operation-type', '/accounting/operation-type/view'),
('accounting-resume', '/accounting/resume/*'),
('active-contract', '/sale/contract/contract/active-contract'),
('active-contract', '/westnet/connection/*'),
('active-contract', '/westnet/connection/disable'),
('active-contract', '/westnet/connection/enable'),
('active-contract', '/westnet/connection/force'),
('active-contract', '/westnet/node/index'),
('active-contract', '/westnet/node/view'),
('active-new-item-contract', '/sale/contract/contract/active-new-items'),
('actualizar-clientes', '/sale/customer/update'),
('admin', 'Administrativo Full'),
('admin', 'Administrativos'),
('admin', 'agenda-default'),
('admin', 'agenda-event-type'),
('Admin', 'assignRolesToUsers'),
('admin', 'backup'),
('admin', 'batch-invoice-rol'),
('admin', 'bindUserToIp'),
('admin', 'cashier'),
('admin', 'category-planes'),
('Admin', 'changeOwnPassword'),
('Admin', 'changeUserPassword'),
('admin', 'cobros manuales'),
('admin', 'Contable'),
('admin', 'create-customer'),
('admin', 'create-node'),
('Admin', 'createUsers'),
('Admin', 'deleteUsers'),
('Admin', 'editUsers'),
('admin', 'partner-account'),
('admin', 'partner-create'),
('admin', 'partner-delete'),
('admin', 'partner-liquidation'),
('admin', 'partner-partner'),
('admin', 'partner-partner-distributiion-model'),
('admin', 'partner-update'),
('admin', 'sale-address'),
('admin', 'sale-customer-has-discount'),
('admin', 'seller'),
('admin', 'socios-full'),
('admin', 'Socios-Prueba'),
('admin', 'Task-category-ABM'),
('admin', 'technical'),
('admin', 'tickets-category-full'),
('admin', 'tickets-color-full'),
('admin', 'tickets-status-full'),
('admin', 'tickets-type'),
('admin', 'viewVisitLog'),
('admin', 'view_payout_ecopagos'),
('admin', 'westnet-api'),
('Administrativo Full', 'actualizar-clientes'),
('Administrativo Full', 'Administrativos'),
('Administrativo Full', 'agenda-default'),
('Administrativo Full', 'agregar-producto'),
('Administrativo Full', 'batch-invoice'),
('Administrativo Full', 'change-empresa'),
('Administrativo Full', 'changeOwnPassword'),
('Administrativo Full', 'checkout-payment'),
('Administrativo Full', 'checkout-payment-create'),
('Administrativo Full', 'checkout-payment-current-account'),
('Administrativo Full', 'checkout-payment-method'),
('Administrativo Full', 'checkout-payment-plan-index'),
('Administrativo Full', 'contract-view'),
('Administrativo Full', 'Contratos'),
('Administrativo Full', 'create-customer'),
('Administrativo Full', 'customer-debtors'),
('Administrativo Full', 'customer-index'),
('Administrativo Full', 'ecopago-daily-closure'),
('Administrativo Full', 'ecopagos-batch-closure'),
('Administrativo Full', 'ecopagos-vew'),
('Administrativo Full', 'grid-exportar'),
('Administrativo Full', 'historial-categorías-cliente'),
('Administrativo Full', 'mi-agenda (sin posibilidad de edición)'),
('Administrativo Full', 'pago-facil'),
('Administrativo Full', 'partner-partner-distributiion-model'),
('Administrativo Full', 'remove-contract-detail'),
('Administrativo Full', 'sale-bill'),
('Administrativo Full', 'sale-contract-contract-list-contract'),
('Administrativo Full', 'sale-customer-customer-carnet'),
('Administrativo Full', 'sale-customer-has-discount'),
('Administrativo Full', 'sale-disccount-disccount--by-product'),
('Administrativo Full', 'site-index'),
('Administrativo Full', 'stock-movement-full'),
('Administrativo Full', 'ticket-customer-full'),
('Administrativo Full', 'ticket-index'),
('Administrativo Full', 'ticket-observation'),
('Administrativo Full', 'ticket-status'),
('Administrativo Full', 'ticket-ticket'),
('Administrativo Full', 'ticket-view'),
('Administrativo Full', 'user-can-create-bill'),
('Administrativo Full', 'user-can-create-budget'),
('Administrativo Full', 'user-can-create-credit'),
('Administrativo Full', 'user-can-create-debit'),
('Administrativo Full', 'user-can-create-delivery-note'),
('Administrativo Full', 'user-can-create-order'),
('Administrativo Full', 'user-can-delete-bill'),
('Administrativo Full', 'user-can-delete-budget'),
('Administrativo Full', 'user-can-delete-credit'),
('Administrativo Full', 'user-can-delete-debit'),
('Administrativo Full', 'user-can-delete-delivery-note'),
('Administrativo Full', 'user-can-delete-order'),
('Administrativo Full', 'user-can-open-bill'),
('Administrativo Full', 'user-can-open-budget'),
('Administrativo Full', 'user-can-open-credit'),
('Administrativo Full', 'user-can-open-debit'),
('Administrativo Full', 'user-can-open-delivery-note'),
('Administrativo Full', 'user-can-open-order'),
('Administrativo Full', 'user-can-select-vendor'),
('Administrativo Full', 'user-can-update-bill'),
('Administrativo Full', 'user-can-update-budget'),
('Administrativo Full', 'user-can-update-credit'),
('Administrativo Full', 'user-can-update-debit'),
('Administrativo Full', 'user-can-update-delivery-note'),
('Administrativo Full', 'user-can-update-order'),
('Administrativo Full', 'ver facturas'),
('Administrativo Full', 'view_payout_ecopagos'),
('Administrativo Full', 'westnet-comision-full'),
('Administrativo Full', 'westnet-user-vendor'),
('Administrativo Full', 'westnet-vendor-liquidation'),
('Administrativo Full', 'westnet-vendor-liquidation-item'),
('Administrativos', 'accounting-monex box account'),
('Administrativos', 'active-contract'),
('Administrativos', 'active-new-item-contract'),
('Administrativos', 'actualizar-clientes'),
('Administrativos', 'agenda-default'),
('Administrativos', 'agenda-event-type'),
('Administrativos', 'cancel-contract'),
('Administrativos', 'change-empresa'),
('Administrativos', 'changeOwnPassword'),
('Administrativos', 'checkout-payment'),
('Administrativos', 'checkout-payment-current-account'),
('Administrativos', 'checkout-payment-plan-index'),
('Administrativos', 'contract-view'),
('Administrativos', 'Contratos'),
('Administrativos', 'create-contract-detail'),
('Administrativos', 'customer-debtors'),
('Administrativos', 'customer-index'),
('Administrativos', 'grid-exportar'),
('Administrativos', 'mi-agenda (sin posibilidad de edición)'),
('Administrativos', 'partner-partner-distributiion-model'),
('Administrativos', 'paycheck-paycheck'),
('Administrativos', 'product-to-invoice'),
('Administrativos', 'sale-customer-customer-carnet'),
('Administrativos', 'sale-customer-customer-ticket'),
('Administrativos', 'site-index'),
('Administrativos', 'task-ABM'),
('Administrativos', 'ticket-customer-full'),
('Administrativos', 'ticket-index'),
('Administrativos', 'ticket-observation'),
('Administrativos', 'ticket-status'),
('Administrativos', 'ticket-ticket'),
('Administrativos', 'ticket-view'),
('Administrativos', 'update-conection'),
('Administrativos', 'update-contract'),
('Administrativos', 'update-contract-detail'),
('Administrativos', 'user-can-create-credit'),
('Administrativos', 'ver facturas'),
('Administrativos', 'view-category-ticket'),
('Administrativos', 'view-tickets-color'),
('Administrativos', 'zone-zone-full'),
('ads-create', '/westnet/ads/*'),
('ads-create', '/westnet/ads/print'),
('afip-taxes-book', '/afip/taxes-book/*'),
('agenda-default', '/agenda/default/*'),
('agenda-default', '/agenda/default/index'),
('agenda-default', '/agenda/default/update-agenda'),
('agregar-producto', '/sale/bill/add-product'),
('assigned-ip', '/westnet/node/assigned-ip'),
('assignRolesToUsers', '/user-management/user-permission/set'),
('assignRolesToUsers', '/user-management/user-permission/set-roles'),
('assignRolesToUsers', 'viewUserRoles'),
('assignRolesToUsers', 'viewUsers'),
('backup', '/backup/default/*'),
('batch-invoice', '/sale/batch-invoice/*'),
('batch-invoice', '/sale/batch-invoice/bill-type'),
('batch-invoice', '/sale/batch-invoice/close-invoices'),
('batch-invoice', '/sale/batch-invoice/close-invoices-index'),
('batch-invoice', '/sale/batch-invoice/get-process'),
('batch-invoice', '/sale/batch-invoice/index'),
('batch-invoice', '/sale/batch-invoice/invoice'),
('batch-invoice-rol', 'agregar-producto'),
('batch-invoice-rol', 'batch-invoice'),
('batch-invoice-rol', 'changeOwnPassword'),
('batch-invoice-rol', 'ecopago-daily-closure'),
('batch-invoice-rol', 'ecopagos-batch-closure'),
('batch-invoice-rol', 'ecopagos-vew'),
('batch-invoice-rol', 'sale-bill'),
('batch-invoice-rol', 'site-index'),
('batch-invoice-rol', 'user-can-create-bill'),
('batch-invoice-rol', 'user-can-create-budget'),
('batch-invoice-rol', 'user-can-create-credit'),
('batch-invoice-rol', 'user-can-create-debit'),
('batch-invoice-rol', 'user-can-create-delivery-note'),
('batch-invoice-rol', 'user-can-create-order'),
('batch-invoice-rol', 'user-can-delete-bill'),
('batch-invoice-rol', 'user-can-delete-budget'),
('batch-invoice-rol', 'user-can-delete-credit'),
('batch-invoice-rol', 'user-can-delete-debit'),
('batch-invoice-rol', 'user-can-delete-delivery-note'),
('batch-invoice-rol', 'user-can-delete-order'),
('batch-invoice-rol', 'user-can-open-bill'),
('batch-invoice-rol', 'user-can-open-budget'),
('batch-invoice-rol', 'user-can-open-credit'),
('batch-invoice-rol', 'user-can-open-debit'),
('batch-invoice-rol', 'user-can-open-delivery-note'),
('batch-invoice-rol', 'user-can-open-order'),
('batch-invoice-rol', 'user-can-update-bill'),
('batch-invoice-rol', 'user-can-update-budget'),
('batch-invoice-rol', 'user-can-update-credit'),
('batch-invoice-rol', 'user-can-update-debit'),
('batch-invoice-rol', 'user-can-update-delivery-note'),
('batch-invoice-rol', 'user-can-update-order'),
('batch-invoice-rol', 'ver facturas'),
('cancel-contract', '/sale/contract/contract/cancel-contract'),
('cashier', 'changeOwnPassword'),
('cashier', 'site-index'),
('cashier', 'ver facturas'),
('cashier', 'westnetEcopagoFrontend'),
('category-planes', '/sale/category/*'),
('category-planes', '/sale/category/create'),
('category-planes', '/sale/category/delete'),
('category-planes', '/sale/category/index'),
('category-planes', '/sale/category/update'),
('category-planes', '/sale/category/view'),
('change-empresa', '/sale/customer/change-company'),
('change-node-customer', '/sale/contract/contract/change-node'),
('changeOwnPassword', '/user-management/auth/change-own-password'),
('changeUserPassword', '/user-management/user/change-password'),
('changeUserPassword', 'viewUsers'),
('checkout-payment', '/checkout/payment-receipt/*'),
('checkout-payment', '/checkout/payment/*'),
('checkout-payment-create', '/accounting/money-box-account/moneyboxaccounts'),
('checkout-payment-create', '/checkout/payment/add-item'),
('checkout-payment-create', '/checkout/payment/close'),
('checkout-payment-create', '/checkout/payment/create'),
('checkout-payment-create', '/checkout/payment/delete'),
('checkout-payment-create', '/checkout/payment/update'),
('checkout-payment-create', '/checkout/payment/view'),
('checkout-payment-create', '/partner/partner-distribution-model/get-by-company'),
('checkout-payment-create', '/paycheck/paycheck/create'),
('checkout-payment-create', '/paycheck/paycheck/encartera'),
('checkout-payment-current-account', '/checkout/payment/current-account'),
('checkout-payment-method', '/checkout/payment-method/*'),
('checkout-payment-plan-index', '/checkout/payment-plan/*'),
('checkout-payment-plan-index', '/checkout/payment-plan/cancel'),
('checkout-payment-plan-index', '/checkout/payment-plan/create'),
('checkout-payment-plan-index', '/checkout/payment-plan/index'),
('checkout-payment-plan-index', '/checkout/payment-plan/list'),
('Cobranzas', 'Administrativos'),
('Cobranzas', 'change-empresa'),
('Cobranzas', 'checkout-payment-plan-index'),
('Cobranzas', 'cobros manuales'),
('Cobranzas', 'customer-debtors'),
('Cobranzas', 'sale-customer-customer-carnet'),
('Cobranzas', 'user-can-create-credit'),
('Cobranzas', 'ver facturas'),
('cobros manuales', 'active-contract'),
('cobros manuales', 'actualizar-clientes'),
('cobros manuales', 'agenda-default'),
('cobros manuales', 'checkout-payment-create'),
('cobros manuales', 'checkout-payment-current-account'),
('cobros manuales', 'contract-view'),
('cobros manuales', 'customer-debtors'),
('cobros manuales', 'customer-index'),
('cobros manuales', 'mi-agenda (sin posibilidad de edición)'),
('cobros manuales', 'sale-contract-contract-list-contract'),
('cobros manuales', 'sale-disccount-disccount--by-product'),
('cobros manuales', 'site-index'),
('cobros manuales', 'task-ABM'),
('cobros manuales', 'ticket-customer-full'),
('cobros manuales', 'ticket-index'),
('cobros manuales', 'ticket-observation'),
('cobros manuales', 'ticket-status'),
('cobros manuales', 'ticket-ticket'),
('cobros manuales', 'update-conection'),
('cobros manuales', 'update-contract'),
('cobros manuales', 'ver facturas'),
('cobros manuales', 'zone-zone-full'),
('Contable', 'account-config'),
('Contable', 'accounting-account'),
('Contable', 'accounting-account-movement'),
('Contable', 'accounting-accounting period'),
('Contable', 'accounting-conciliation'),
('Contable', 'accounting-monex box account'),
('Contable', 'accounting-money-box'),
('Contable', 'accounting-money-box-type'),
('Contable', 'accounting-operation-type'),
('Contable', 'accounting-resume'),
('Contable', 'active-contract'),
('Contable', 'Administrativo Full'),
('Contable', 'ads-create'),
('Contable', 'afip-taxes-book'),
('Contable', 'agenda-default'),
('Contable', 'agenda-event-type'),
('Contable', 'assigned-ip'),
('Contable', 'batch-invoice'),
('Contable', 'category-planes'),
('Contable', 'change-node-customer'),
('Contable', 'changeOwnPassword'),
('Contable', 'changeUserPassword'),
('Contable', 'checkout-payment'),
('Contable', 'checkout-payment-create'),
('Contable', 'checkout-payment-current-account'),
('Contable', 'checkout-payment-method'),
('Contable', 'checkout-payment-plan-index'),
('Contable', 'contract-view'),
('Contable', 'customer-debtors'),
('Contable', 'customer-index'),
('Contable', 'customer-zones'),
('Contable', 'editUserEmail'),
('Contable', 'editUsers'),
('Contable', 'grid-exportar'),
('Contable', 'Index-node-view'),
('Contable', 'mi-agenda (sin posibilidad de edición)'),
('Contable', 'notification-full'),
('Contable', 'paycheck-paybook'),
('Contable', 'paycheck-paycheck'),
('Contable', 'provider-account-full'),
('Contable', 'provider-bill-full'),
('Contable', 'provider-provider-create'),
('Contable', 'provider-provider-delete'),
('Contable', 'provider-provider-index'),
('Contable', 'provider-provider-payment-full'),
('Contable', 'provider-provider-update'),
('Contable', 'sale-bill-history'),
('Contable', 'sale-contract-plan-full'),
('Contable', 'sale-discount'),
('Contable', 'site-index'),
('Contable', 'task-ABM'),
('Contable', 'ticket-customer-full'),
('Contable', 'ticket-index'),
('Contable', 'ticket-observation'),
('Contable', 'ticket-status'),
('Contable', 'ticket-ticket'),
('Contable', 'ticket-view'),
('Contable', 'update-conection'),
('Contable', 'update-contract'),
('Contable', 'user-can-create-bill'),
('Contable', 'user-can-create-budget'),
('Contable', 'user-can-create-credit'),
('Contable', 'user-can-create-debit'),
('Contable', 'user-can-create-delivery-note'),
('Contable', 'user-can-create-order'),
('Contable', 'user-can-delete-bill'),
('Contable', 'user-can-delete-budget'),
('Contable', 'user-can-delete-credit'),
('Contable', 'user-can-delete-debit'),
('Contable', 'user-can-delete-delivery-note'),
('Contable', 'user-can-delete-order'),
('Contable', 'user-can-open-bill'),
('Contable', 'user-can-open-budget'),
('Contable', 'user-can-open-credit'),
('Contable', 'user-can-open-debit'),
('Contable', 'user-can-open-delivery-note'),
('Contable', 'user-can-open-order'),
('Contable', 'user-can-update-bill'),
('Contable', 'user-can-update-budget'),
('Contable', 'user-can-update-credit'),
('Contable', 'user-can-update-debit'),
('Contable', 'user-can-update-delivery-note'),
('Contable', 'user-can-update-order'),
('Contable', 'view-category-ticket'),
('Contable', 'view-tickets-color'),
('Contable', 'viewRegistrationIp'),
('Contable', 'viewUserEmail'),
('Contable', 'viewUserRoles'),
('Contable', 'viewUsers'),
('contract-view', '/sale/contract/contract/view'),
('Contratos', '/sale/contract/contract/add-contract-detail'),
('create-contract-detail', '/sale/contract/contract-detail/create'),
('create-customer', '/sale/contract/contract-detail/*'),
('create-customer', '/sale/contract/contract/*'),
('create-customer', '/sale/contract/plan-feature/*'),
('create-customer', '/sale/contract/plan/*'),
('create-customer', '/sale/customer/create'),
('create-customer', '/zone/zone/zones-by-name'),
('create-node', '/westnet/node/*'),
('createUsers', '/user-management/user/create'),
('createUsers', 'viewUsers'),
('customer-debtors', '/sale/customer/debtors'),
('customer-index', '/sale/customer/find-by-name'),
('customer-index', '/sale/customer/index'),
('customer-index', '/sale/customer/search'),
('customer-index', '/sale/customer/view'),
('customer-zones', '/zone/zone/*'),
('deleteUsers', '/user-management/user/bulk-delete'),
('deleteUsers', '/user-management/user/delete'),
('deleteUsers', 'viewUsers'),
('ecooff', 'ecopago-daily-closure'),
('ecooff', 'ecopagos-batch-closure'),
('ecooff', 'ecopagos-vew'),
('ecooff', 'provider-bill-full'),
('ecooff', 'provider-provider-payment-full'),
('ecooff', 'view_payout_ecopagos'),
('ecopago-daily-closure', '/ecopagos/daily-closure/index'),
('ecopago-daily-closure', '/ecopagos/daily-closure/view'),
('ecopagos-batch-closure', '/ecopagos/batch-closure/index'),
('ecopagos-batch-closure', '/ecopagos/batch-closure/view'),
('ecopagos-vew', '/ecopagos/ecopago/index'),
('ecopagos-vew', '/ecopagos/ecopago/view'),
('ecopagos-vew', '/westnet/ecopagos/batch-closure/*'),
('ecopagos-vew', '/westnet/ecopagos/cashier/*'),
('ecopagos-vew', '/westnet/ecopagos/collector/*'),
('ecopagos-vew', '/westnet/ecopagos/commission/*'),
('ecopagos-vew', '/westnet/ecopagos/daily-closure/*'),
('ecopagos-vew', '/westnet/ecopagos/default/*'),
('ecopagos-vew', '/westnet/ecopagos/ecopago/*'),
('editUserEmail', 'viewUserEmail'),
('editUsers', '/user-management/user/bulk-activate'),
('editUsers', '/user-management/user/bulk-deactivate'),
('editUsers', '/user-management/user/update'),
('editUsers', 'viewUsers'),
('Gestión de Bajas', 'checkout-payment'),
('Gestión de Bajas', 'checkout-payment-plan-index'),
('Gestión de Bajas', 'user-can-create-credit'),
('Gestión de Bajas', 'user-can-create-debit'),
('gestor api', 'westnet-api'),
('grid-exportar', '/gridview/export/*'),
('grid-exportar', '/gridview/export/download'),
('grid-exportar', '/user-management/auth-item-group/grid-page-size'),
('grid-exportar', '/user-management/auth-item-group/grid-sort'),
('grid-exportar', '/user-management/permission/grid-page-size'),
('grid-exportar', '/user-management/permission/grid-sort'),
('grid-exportar', '/user-management/role/grid-page-size'),
('grid-exportar', '/user-management/role/grid-sort'),
('grid-exportar', '/user-management/user-visit-log/grid-page-size'),
('grid-exportar', '/user-management/user-visit-log/grid-sort'),
('grid-exportar', '/user-management/user/grid-page-size'),
('grid-exportar', '/user-management/user/grid-sort'),
('historial-categorías-cliente', '/sale/customer/classhistory'),
('Index-node-view', '/westnet/node/all-nodes'),
('Index-node-view', '/westnet/node/assigned-ip'),
('mi-agenda (sin posibilidad de edición)', '/agenda/default/index'),
('mi-agenda (sin posibilidad de edición)', '/agenda/event/index'),
('mi-agenda (sin posibilidad de edición)', '/agenda/event/view'),
('mi-agenda (sin posibilidad de edición)', '/agenda/notification/create'),
('mi-agenda (sin posibilidad de edición)', '/agenda/notification/index'),
('mi-agenda (sin posibilidad de edición)', '/agenda/notification/view'),
('notification-full', '/westnet/notifications/*'),
('notification-full', '/westnet/notifications/destinatary/*'),
('notification-full', '/westnet/notifications/notification/*'),
('notification-full', '/westnet/notifications/transport/*'),
('pago-facil', '/checkout/payment/pagofacil-payment-view'),
('pago-facil', '/checkout/payment/pagofacil-payments-import'),
('pago-facil', '/checkout/payment/pagofacil-payments-index'),
('partner-account', '/partner/partner/account'),
('partner-account', '/partner/partner/show-account-detail'),
('partner-create', '/partner/partner/create'),
('partner-delete', '/partner/partner/delete'),
('partner-liquidation', '/partner/liquidation/*'),
('partner-liquidation', '/partner/liquidation/index'),
('partner-liquidation', '/partner/liquidation/liquidate'),
('partner-partner', '/partner/partner/*'),
('partner-partner', '/partner/partner/index'),
('partner-partner', '/partner/partner/view'),
('partner-partner-distributiion-model', '/partner/partner-distribution-model/*'),
('partner-partner-distributiion-model', '/partner/partner-distribution-model/add-partner'),
('partner-partner-distributiion-model', '/partner/partner-distribution-model/create'),
('partner-partner-distributiion-model', '/partner/partner-distribution-model/delete'),
('partner-partner-distributiion-model', '/partner/partner-distribution-model/delete-partner'),
('partner-partner-distributiion-model', '/partner/partner-distribution-model/get-by-company'),
('partner-partner-distributiion-model', '/partner/partner-distribution-model/index'),
('partner-partner-distributiion-model', '/partner/partner-distribution-model/list-partner'),
('partner-partner-distributiion-model', '/partner/partner-distribution-model/update'),
('partner-partner-distributiion-model', '/partner/partner-distribution-model/view'),
('partner-update', '/partner/partner/update'),
('paycheck-paybook', '/paycheck/checkbook/*'),
('paycheck-paybook', '/paycheck/checkbook/create'),
('paycheck-paybook', '/paycheck/checkbook/delete'),
('paycheck-paybook', '/paycheck/checkbook/index'),
('paycheck-paybook', '/paycheck/checkbook/update'),
('paycheck-paybook', '/paycheck/checkbook/view'),
('paycheck-paycheck', '/paycheck/paycheck/*'),
('paycheck-paycheck', '/paycheck/paycheck/change-state'),
('paycheck-paycheck', '/paycheck/paycheck/checkbooks'),
('paycheck-paycheck', '/paycheck/paycheck/create'),
('paycheck-paycheck', '/paycheck/paycheck/delete'),
('paycheck-paycheck', '/paycheck/paycheck/encartera'),
('paycheck-paycheck', '/paycheck/paycheck/index'),
('paycheck-paycheck', '/paycheck/paycheck/select-paycheck'),
('paycheck-paycheck', '/paycheck/paycheck/update'),
('paycheck-paycheck', '/paycheck/paycheck/view'),
('pending-instalation', '/sale/customer/pending-installations'),
('product-to-invoice', '/sale/product-to-invoice/*'),
('product-to-invoice', '/sale/product-to-invoice/activate'),
('product-to-invoice', '/sale/product-to-invoice/cancel'),
('product-to-invoice', '/sale/product-to-invoice/index'),
('product-to-invoice', '/sale/product-to-invoice/update'),
('product-to-invoice', '/sale/product-to-invoice/view'),
('provider-account-full', '/provider/provider/current-account'),
('provider-account-full', '/provider/provider/debts'),
('provider-account-full', '/provider/provider/find-by-name'),
('provider-bill-full', '/provider/provider-bill/add-item'),
('provider-bill-full', '/provider/provider-bill/add-tax'),
('provider-bill-full', '/provider/provider-bill/create'),
('provider-bill-full', '/provider/provider-bill/delete'),
('provider-bill-full', '/provider/provider-bill/delete-item'),
('provider-bill-full', '/provider/provider-bill/delete-tax'),
('provider-bill-full', '/provider/provider-bill/index'),
('provider-bill-full', '/provider/provider-bill/update'),
('provider-bill-full', '/provider/provider-bill/view'),
('provider-provider-create', '/provider/provider/create'),
('provider-provider-delete', '/provider/provider/delete'),
('provider-provider-index', '/provider/provider/index'),
('provider-provider-index', '/provider/provider/view'),
('provider-provider-payment-full', '/provider/provider-payment/*'),
('provider-provider-update', '/provider/provider/update'),
('remove-contract-detail', '/sale/contract/contract/remove-contract-detail'),
('sale-address', '/sale/address/*'),
('sale-address', '/sale/address/create'),
('sale-address', '/sale/address/delete'),
('sale-address', '/sale/address/index'),
('sale-address', '/sale/address/update'),
('sale-address', '/sale/address/view'),
('sale-bill', '/sale/bill/barcode'),
('sale-bill', '/sale/bill/close'),
('sale-bill', '/sale/bill/create'),
('sale-bill', '/sale/bill/delete'),
('sale-bill', '/sale/bill/delete-detail'),
('sale-bill', '/sale/bill/email'),
('sale-bill', '/sale/bill/generate'),
('sale-bill', '/sale/bill/group'),
('sale-bill', '/sale/bill/handwrite-detail'),
('sale-bill', '/sale/bill/index'),
('sale-bill', '/sale/bill/open'),
('sale-bill', '/sale/bill/pdf'),
('sale-bill', '/sale/bill/remove-customer'),
('sale-bill', '/sale/bill/resend'),
('sale-bill', '/sale/bill/search-customer'),
('sale-bill', '/sale/bill/search-product'),
('sale-bill', '/sale/bill/select-customer'),
('sale-bill', '/sale/bill/update'),
('sale-bill', '/sale/bill/update-qty'),
('sale-bill', '/sale/bill/view'),
('sale-bill-history', '/sale/bill/history'),
('sale-contract-contract-list-contract', '/sale/contract/contract/list-contracts'),
('sale-contract-plan-full', '/sale/contract/plan/*'),
('sale-contract-plan-full', '/sale/contract/plan/batch-updater'),
('sale-contract-plan-full', '/sale/contract/plan/create'),
('sale-contract-plan-full', '/sale/contract/plan/delete'),
('sale-contract-plan-full', '/sale/contract/plan/index'),
('sale-contract-plan-full', '/sale/contract/plan/price-history'),
('sale-contract-plan-full', '/sale/contract/plan/update'),
('sale-contract-plan-full', '/sale/contract/plan/update-price'),
('sale-contract-plan-full', '/sale/contract/plan/update-prices'),
('sale-contract-plan-full', '/sale/contract/plan/view'),
('sale-customer-customer-carnet', '/sale/customer/customer-carnet'),
('sale-customer-customer-ticket', '/sale/customer/customer-tickets'),
('sale-customer-has-discount', '/sale/customer-has-discount/*'),
('sale-customer-has-discount', '/sale/customer-has-discount/create'),
('sale-customer-has-discount', '/sale/customer-has-discount/delete'),
('sale-customer-has-discount', '/sale/customer-has-discount/index'),
('sale-customer-has-discount', '/sale/customer-has-discount/update'),
('sale-customer-has-discount', '/sale/customer-has-discount/view'),
('sale-disccount-disccount--by-product', '/sale/discount/discount-by-product'),
('sale-discount', '/sale/discount/*'),
('sale-discount', '/sale/discount/create'),
('sale-discount', '/sale/discount/delete'),
('sale-discount', '/sale/discount/discount-by-product'),
('sale-discount', '/sale/discount/index'),
('sale-discount', '/sale/discount/update'),
('sale-discount', '/sale/discount/view'),
('sale-discount', '/westnet/batch/discount-to-customer'),
('sale-discount', '/westnet/batch/discount-to-customer-assign'),
('seller', 'changeOwnPassword'),
('seller', 'create-contract-detail'),
('seller', 'customer-zones'),
('seller', 'Seller-permissions'),
('seller', 'site-index'),
('seller', 'update-contract-detail'),
('seller', 'update-zone'),
('seller', 'ver facturas'),
('seller', 'zone-zone-full'),
('Seller-permissions', '/sale/contract/contract/add-contract-detail'),
('Seller-permissions', '/sale/contract/contract/create'),
('Seller-permissions', '/sale/contract/contract/funding-plans'),
('Seller-permissions', '/sale/contract/contract/remove-contract-detail'),
('Seller-permissions', '/sale/contract/contract/show-additionals'),
('Seller-permissions', '/sale/contract/contract/update'),
('Seller-permissions', '/sale/contract/contract/vendor-list'),
('Seller-permissions', '/sale/contract/contract/view'),
('Seller-permissions', '/sale/customer/create'),
('Seller-permissions', '/sale/customer/createcontract'),
('Seller-permissions', '/sale/customer/find-by-name'),
('Seller-permissions', '/sale/customer/sell'),
('Seller-permissions', '/sale/customer/view'),
('Seller-permissions', '/sale/discount/discount-by-product'),
('site-index', '/site/index'),
('socios-full', '/partner/*'),
('socios-full', '/partner/liquidation/*'),
('socios-full', '/partner/liquidation/index'),
('socios-full', '/partner/liquidation/liquidate'),
('socios-full', '/partner/partner-distribution-model/*'),
('socios-full', '/partner/partner-distribution-model/add-partner'),
('socios-full', '/partner/partner-distribution-model/create'),
('socios-full', '/partner/partner-distribution-model/delete'),
('socios-full', '/partner/partner-distribution-model/delete-partner'),
('socios-full', '/partner/partner-distribution-model/get-by-company'),
('socios-full', '/partner/partner-distribution-model/index'),
('socios-full', '/partner/partner-distribution-model/list-partner'),
('socios-full', '/partner/partner-distribution-model/update'),
('socios-full', '/partner/partner-distribution-model/view'),
('socios-full', '/partner/partner/*'),
('socios-full', '/partner/partner/account'),
('socios-full', '/partner/partner/create'),
('socios-full', '/partner/partner/delete'),
('socios-full', '/partner/partner/index'),
('socios-full', '/partner/partner/show-account-detail'),
('socios-full', '/partner/partner/update'),
('socios-full', '/partner/partner/view'),
('Socios-Prueba', '/partner/*'),
('Socios-Prueba', '/partner/liquidation/*'),
('Socios-Prueba', '/partner/liquidation/index'),
('Socios-Prueba', '/partner/liquidation/liquidate'),
('Socios-Prueba', '/partner/partner-distribution-model/*'),
('Socios-Prueba', '/partner/partner-distribution-model/add-partner'),
('Socios-Prueba', '/partner/partner-distribution-model/create'),
('Socios-Prueba', '/partner/partner-distribution-model/delete'),
('Socios-Prueba', '/partner/partner-distribution-model/delete-partner'),
('Socios-Prueba', '/partner/partner-distribution-model/get-by-company'),
('Socios-Prueba', '/partner/partner-distribution-model/index'),
('Socios-Prueba', '/partner/partner-distribution-model/list-partner'),
('Socios-Prueba', '/partner/partner-distribution-model/update'),
('Socios-Prueba', '/partner/partner-distribution-model/view'),
('Socios-Prueba', '/partner/partner/*'),
('Socios-Prueba', '/partner/partner/account'),
('Socios-Prueba', '/partner/partner/create'),
('Socios-Prueba', '/partner/partner/delete'),
('Socios-Prueba', '/partner/partner/index'),
('Socios-Prueba', '/partner/partner/show-account-detail'),
('Socios-Prueba', '/partner/partner/update'),
('Socios-Prueba', '/partner/partner/view'),
('stock-movement-full', '/sale/stock-movement/*'),
('stock-movement-full', '/sale/stock-movement/create'),
('stock-movement-full', '/sale/stock-movement/delete'),
('stock-movement-full', '/sale/stock-movement/graph'),
('stock-movement-full', '/sale/stock-movement/index'),
('stock-movement-full', '/sale/stock-movement/view'),
('task-ABM', '/agenda/task/*'),
('task-ABM', '/agenda/task/create'),
('task-ABM', '/agenda/task/delete'),
('task-ABM', '/agenda/task/index'),
('task-ABM', '/agenda/task/postpone-task'),
('task-ABM', '/agenda/task/quick-create'),
('task-ABM', '/agenda/task/quick-update'),
('task-ABM', '/agenda/task/update'),
('task-ABM', '/agenda/task/view'),
('Task-category-ABM', '/agenda/category/*'),
('Task-category-ABM', '/agenda/category/create'),
('Task-category-ABM', '/agenda/category/delete'),
('Task-category-ABM', '/agenda/category/fetch-category'),
('Task-category-ABM', '/agenda/category/index'),
('Task-category-ABM', '/agenda/category/update'),
('Task-category-ABM', '/agenda/category/view'),
('technical', 'active-contract'),
('technical', 'ads-create'),
('technical', 'assigned-ip'),
('technical', 'change-node-customer'),
('technical', 'changeOwnPassword'),
('technical', 'contract-view'),
('technical', 'customer-index'),
('technical', 'customer-zones'),
('technical', 'Index-node-view'),
('technical', 'mi-agenda (sin posibilidad de edición)'),
('technical', 'pending-instalation'),
('technical', 'site-index'),
('technical', 'task-ABM'),
('technical', 'update-conection'),
('technical', 'update-contract'),
('technical', 'user-can-select-vendor'),
('technical', 'ver facturas'),
('tecnico-create-clientes', 'actualizar-clientes'),
('tecnico-create-clientes', 'create-contract-detail'),
('tecnico-create-clientes', 'create-customer'),
('tecnico-create-clientes', 'customer-index'),
('tecnico-create-clientes', 'sale-customer-customer-ticket'),
('tecnico-create-clientes', 'update-contract-detail'),
('tecnico-jefe', 'assigned-ip'),
('tecnico-jefe', 'change-node-customer'),
('tecnico-jefe', 'create-node'),
('tecnico-jefe', 'Index-node-view'),
('ticket-customer-full', '/ticket/customer/*'),
('ticket-customer-full', '/ticket/customer/get-customer-info'),
('ticket-customer-full', '/ticket/customer/ticket-history'),
('ticket-index', '/ticket/default/*'),
('ticket-index', '/ticket/default/index'),
('ticket-observation', '/ticket/observation/*'),
('ticket-observation', '/ticket/observation/build-observation'),
('ticket-observation', '/ticket/observation/index'),
('ticket-observation', '/ticket/ticket/observation'),
('ticket-observation', '/ticket/ticket/open-tickets'),
('ticket-status', '/ticket/status/index'),
('ticket-status', '/ticket/status/view'),
('ticket-ticket', '/ticket/customer/get-customer-info'),
('ticket-ticket', '/ticket/customer/ticket-history'),
('ticket-ticket', '/ticket/ticket/*'),
('ticket-ticket', '/ticket/ticket/close'),
('ticket-ticket', '/ticket/ticket/create'),
('ticket-ticket', '/ticket/ticket/delete'),
('ticket-ticket', '/ticket/ticket/history'),
('ticket-ticket', '/ticket/ticket/index'),
('ticket-ticket', '/ticket/ticket/list'),
('ticket-ticket', '/ticket/ticket/observation'),
('ticket-ticket', '/ticket/ticket/open-tickets'),
('ticket-ticket', '/ticket/ticket/reopen'),
('ticket-ticket', '/ticket/ticket/update'),
('ticket-ticket', '/ticket/ticket/view'),
('ticket-view', '/ticket/type/index'),
('ticket-view', '/ticket/type/view'),
('tickets-category-full', '/ticket/category/create'),
('tickets-category-full', '/ticket/category/delete'),
('tickets-category-full', '/ticket/category/index'),
('tickets-category-full', '/ticket/category/update'),
('tickets-category-full', '/ticket/category/view'),
('tickets-color-full', '/ticket/color/*'),
('tickets-color-full', '/ticket/color/create'),
('tickets-color-full', '/ticket/color/delete'),
('tickets-color-full', '/ticket/color/index'),
('tickets-color-full', '/ticket/color/update'),
('tickets-color-full', '/ticket/color/view'),
('tickets-status-full', '/ticket/status/*'),
('tickets-status-full', '/ticket/status/create'),
('tickets-status-full', '/ticket/status/delete'),
('tickets-status-full', '/ticket/status/index'),
('tickets-status-full', '/ticket/status/update'),
('tickets-status-full', '/ticket/status/view'),
('tickets-type', '/ticket/type/*'),
('tickets-type', '/ticket/type/create'),
('tickets-type', '/ticket/type/delete'),
('tickets-type', '/ticket/type/get-categories'),
('tickets-type', '/ticket/type/index'),
('tickets-type', '/ticket/type/update'),
('tickets-type', '/ticket/type/view'),
('update-conection', '/sale/contract/contract/update-connection'),
('update-contract', '/sale/contract/contract/change-status-contract-detail'),
('update-contract', '/sale/contract/contract/funding-plans'),
('update-contract', '/sale/contract/contract/index'),
('update-contract', '/sale/contract/contract/show-additionals'),
('update-contract', '/sale/contract/contract/update'),
('update-contract', '/sale/contract/contract/view'),
('update-contract-detail', '/sale/contract/contract-detail/update'),
('update-zone', '/zone/zone/create'),
('update-zone', '/zone/zone/index'),
('update-zone', '/zone/zone/update'),
('update-zone', '/zone/zone/view'),
('Vendedor en Oficina', 'active-contract'),
('Vendedor en Oficina', 'active-new-item-contract'),
('Vendedor en Oficina', 'actualizar-clientes'),
('Vendedor en Oficina', 'cancel-contract'),
('Vendedor en Oficina', 'change-empresa'),
('Vendedor en Oficina', 'checkout-payment-current-account'),
('Vendedor en Oficina', 'contract-view'),
('Vendedor en Oficina', 'create-contract-detail'),
('Vendedor en Oficina', 'customer-debtors'),
('Vendedor en Oficina', 'historial-categorías-cliente'),
('Vendedor en Oficina', 'pending-instalation'),
('Vendedor en Oficina', 'product-to-invoice'),
('Vendedor en Oficina', 'sale-customer-customer-carnet'),
('Vendedor en Oficina', 'sale-customer-customer-ticket'),
('Vendedor en Oficina', 'update-conection'),
('Vendedor en Oficina', 'update-contract'),
('Vendedor en Oficina', 'update-contract-detail'),
('ver facturas', '/sale/bill/pdf'),
('ver facturas', '/sale/bill/view'),
('view-category-ticket', '/ticket/category/index'),
('view-category-ticket', '/ticket/category/view'),
('view-tickets-color', '/ticket/color/index'),
('view-tickets-color', '/ticket/color/view'),
('viewUsers', '/user-management/user/grid-page-size'),
('viewUsers', '/user-management/user/index'),
('viewUsers', '/user-management/user/view'),
('viewVisitLog', '/user-management/user-visit-log/grid-page-size'),
('viewVisitLog', '/user-management/user-visit-log/index'),
('viewVisitLog', '/user-management/user-visit-log/view'),
('view_payout_ecopagos', '/westnet/ecopagos/payout/*'),
('view_payout_ecopagos', '/westnet/ecopagos/payout/index'),
('view_payout_ecopagos', '/westnet/ecopagos/payout/payout-list'),
('view_payout_ecopagos', '/westnet/ecopagos/payout/view'),
('westnet-api', '/westnet/api/*'),
('westnet-api', '/westnet/api/contract/*'),
('westnet-api', '/westnet/api/contract/create'),
('westnet-api', '/westnet/api/contract/index'),
('westnet-api', '/westnet/api/contract/list-by-id'),
('westnet-api', '/westnet/api/contract/mora'),
('westnet-api', '/westnet/api/contract/options'),
('westnet-api', '/westnet/api/contract/view'),
('westnet-api', '/westnet/api/customer/*'),
('westnet-api', '/westnet/api/customer/create'),
('westnet-api', '/westnet/api/customer/index'),
('westnet-api', '/westnet/api/customer/list-by-id'),
('westnet-api', '/westnet/api/customer/options'),
('westnet-api', '/westnet/api/customer/update-email-geocode'),
('westnet-api', '/westnet/api/customer/view'),
('westnet-api', '/westnet/api/zone/*'),
('westnet-api', '/westnet/api/zone/create'),
('westnet-api', '/westnet/api/zone/index'),
('westnet-api', '/westnet/api/zone/options'),
('westnet-api', '/westnet/api/zone/view'),
('westnet-comision-full', '/westnet/vendor-commission/*'),
('westnet-comision-full', '/westnet/vendor-commission/create'),
('westnet-comision-full', '/westnet/vendor-commission/delete'),
('westnet-comision-full', '/westnet/vendor-commission/index'),
('westnet-comision-full', '/westnet/vendor-commission/update'),
('westnet-comision-full', '/westnet/vendor-commission/view'),
('westnet-user-vendor', '/westnet/user-vendor/*'),
('westnet-user-vendor', '/westnet/user-vendor/create'),
('westnet-user-vendor', '/westnet/user-vendor/delete'),
('westnet-user-vendor', '/westnet/user-vendor/index'),
('westnet-user-vendor', '/westnet/user-vendor/update'),
('westnet-user-vendor', '/westnet/user-vendor/view'),
('westnet-vendor-liquidation', '/westnet/vendor-liquidation/*'),
('westnet-vendor-liquidation', '/westnet/vendor-liquidation/batch'),
('westnet-vendor-liquidation', '/westnet/vendor-liquidation/create'),
('westnet-vendor-liquidation', '/westnet/vendor-liquidation/delete'),
('westnet-vendor-liquidation', '/westnet/vendor-liquidation/index'),
('westnet-vendor-liquidation', '/westnet/vendor-liquidation/update'),
('westnet-vendor-liquidation', '/westnet/vendor-liquidation/view'),
('westnet-vendor-liquidation-item', '/westnet/vendor-liquidation-item/*'),
('westnet-vendor-liquidation-item', '/westnet/vendor-liquidation-item/create'),
('westnet-vendor-liquidation-item', '/westnet/vendor-liquidation-item/delete'),
('westnet-vendor-liquidation-item', '/westnet/vendor-liquidation-item/index'),
('westnet-vendor-liquidation-item', '/westnet/vendor-liquidation-item/update'),
('westnet-vendor-liquidation-item', '/westnet/vendor-liquidation-item/view'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/batch-closure/*'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/batch-closure/cancel'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/batch-closure/create'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/batch-closure/delete'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/batch-closure/get-preview'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/batch-closure/index'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/batch-closure/update'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/batch-closure/view'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/batch-closure/view-payouts'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/cashier/change-password'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/collector/get-collector-info'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/credential/*'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/credential/delete'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/credential/index'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/credential/reprint-ask'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/credential/view'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/customer/get-customer-info'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/customer/payout-history'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/daily-closure/*'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/daily-closure/cancel'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/daily-closure/close'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/daily-closure/create'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/daily-closure/delete'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/daily-closure/index'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/daily-closure/open-cash-register'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/daily-closure/preview'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/daily-closure/view'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/payout/*'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/payout/ajax-info'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/payout/create'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/payout/delete'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/payout/index'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/payout/print'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/payout/reverse'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/payout/view'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/site/*'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/site/captcha'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/site/error'),
('westnetEcopagoFrontend', '/westnet/ecopagos/frontend/site/index'),
('zone-zone-full', '/zone/zone/full-zone'),
('zone-zone-full', '/zone/zone/zones-by-name');

CREATE TABLE `auth_item_group` (
  `code` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `auth_item_group` (`code`, `name`, `created_at`, `updated_at`) VALUES
('Administrative', 'Administrative', 1464666919, 1464666919),
('Agenda', 'Agenda', 1466874310, 1466874310),
('API', 'API', 1468855297, 1468855297),
('Clientes', 'Clientes', 1466683371, 1466683371),
('Contabilidad', 'Contabilidad', 1467137555, 1467137555),
('contable', 'Contable', 1464668219, 1466975967),
('Contratos', 'Contratos', 1466683872, 1466683872),
('Ecopagos', 'Ecopagos', 1466898924, 1466898924),
('Facturación', 'Facturación', 1457013353, 1466948725),
('Nodos', 'Nodos', 1464665018, 1466874301),
('Payment', 'Pagos', 1467143151, 1467143151),
('Planes', 'Planes', 1466984552, 1466984552),
('Productos', 'Productos', 1467035417, 1467035417),
('provider', 'Proveedores', 1467286973, 1467286973),
('seller', 'seller', 1464666321, 1464666321),
('Socios', 'Socios', 1466949822, 1466949822),
('Tickets', 'Tickets', 1467113989, 1467113989),
('userCommonPermissions', 'Permisos Generales', 1441734314, 1467140077),
('userManagement', 'User management', 1441734313, 1467141020);

CREATE TABLE `auth_rule` (
  `name` varchar(64) NOT NULL,
  `data` text,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `auth_token` (
  `auth_token_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expire_timestamp` int(11) NOT NULL,
  `user_app_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `bill` (
  `bill_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `number` int(11) DEFAULT NULL,
  `currency` varchar(45) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `total` double DEFAULT NULL,
  `taxes` double DEFAULT NULL,
  `status` enum('draft','completed','closed') NOT NULL DEFAULT 'draft',
  `customer_id` int(11) DEFAULT NULL,
  `payed` tinyint(1) DEFAULT NULL,
  `currency_id` int(11) NOT NULL,
  `bill_type_id` int(11) NOT NULL,
  `ein` varchar(45) DEFAULT NULL,
  `ein_expiration` date DEFAULT NULL,
  `observation` varchar(250) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `class` varchar(100) DEFAULT NULL,
  `expiration` date DEFAULT NULL,
  `expiration_timestamp` int(11) DEFAULT NULL,
  `footprint` varchar(26) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `partner_distribution_model_id` int(11) DEFAULT NULL,
  `point_of_sale_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `creator_user_id` int(11) DEFAULT NULL,
  `updater_user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `bill` (`bill_id`, `date`, `time`, `timestamp`, `number`, `currency`, `amount`, `total`, `taxes`, `status`, `customer_id`, `payed`, `currency_id`, `bill_type_id`, `ein`, `ein_expiration`, `observation`, `company_id`, `class`, `expiration`, `expiration_timestamp`, `footprint`, `active`, `user_id`, `partner_distribution_model_id`, `created_at`, `updated_at`, `creator_user_id`, `updater_user_id`, `point_of_sale_id`) VALUES
(1, '2015-01-01', '12:00:00', 1420124400, 1, NULL, 1230, 1488.3, 258.3, 'draft', 1, 0, 1, 1, NULL, NULL, NULL, 1, 'app\\modules\\sale\\models\\bills\\Bill', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(2, '2015-02-01', '12:00:00', 1422802800, 2, NULL, 2030, 2456.3, 483, 'draft', 1, 0, 1, 1, NULL, NULL, NULL, 1, 'app\\modules\\sale\\models\\bills\\Bill', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(3, '2015-03-01', '12:00:00', 1425222000, 3, NULL, 3260, 3944.6, 684.6, 'draft', 1, 0, 1, 1, NULL, NULL, NULL, 1, 'app\\modules\\sale\\models\\bills\\Bill', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(4, '2015-04-01', '12:00:00', 1427900400, 4, NULL, 1230, 1488.3, 258.3, 'completed', 1, 1, 1, 1, NULL, NULL, NULL, 1, 'app\\modules\\sale\\models\\bills\\Bill', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(5, '2015-05-01', '12:00:00', 1430492400, 5, NULL, 2030, 2456.3, 483, 'completed', 1, 1, 1, 1, NULL, NULL, NULL, 1, 'app\\modules\\sale\\models\\bills\\Bill', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(6, '2015-06-01', '12:00:00', 1433170800, 6, NULL, 3260, 3944.6, 684.6, 'completed', 1, 1, 1, 1, NULL, NULL, NULL, 1, 'app\\modules\\sale\\models\\bills\\Bill', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(7, '2015-12-04', '05:06:00', 1449248780, 7, NULL, 144, 174.24, 30.24, 'closed', 1, 0, 1, 1, NULL, NULL, NULL, 1, 'app\\modules\\sale\\models\\bills\\Bill', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(8, '2015-04-01', '12:00:00', 1427900400, 8, NULL, 1230, 1488.3, 258.3, 'completed', 2, 0, 1, 2, NULL, NULL, NULL, 1, 'app\\modules\\sale\\models\\bills\\Bill', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(9, '2015-05-01', '12:00:00', 1430492400, 9, NULL, 2030, 2456.3, 483, 'completed', 2, 0, 1, 2, NULL, NULL, NULL, 1, 'app\\modules\\sale\\models\\bills\\Bill', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(10, '2015-03-01', '12:00:00', 1425222000, 10, NULL, 1230, 1488.3, 258.3, 'closed', 6, 0, 1, 2, NULL, NULL, NULL, 1, 'app\\modules\\sale\\models\\bills\\Bill', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(11, '2015-04-01', '12:00:00', 1427900400, 11, NULL, 2030, 2456.3, 483, 'closed', 6, 0, 1, 2, NULL, NULL, NULL, 1, 'app\\modules\\sale\\models\\bills\\Bill', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(12, '2015-05-01', '12:00:00', 1430492400, 12, NULL, 1230, 1488.3, 258.3, 'closed', 6, 0, 1, 2, NULL, NULL, NULL, 1, 'app\\modules\\sale\\models\\bills\\Bill', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(13, '2015-06-01', '12:00:00', 1433170800, 13, NULL, 2030, 2456.3, 483, 'closed', 6, 0, 1, 2, NULL, NULL, NULL, 1, 'app\\modules\\sale\\models\\bills\\Bill', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 1);

CREATE TABLE `bill_detail` (
  `bill_detail_id` int(11) NOT NULL,
  `unit_net_price` double DEFAULT NULL,
  `unit_final_price` double DEFAULT NULL,
  `concept` varchar(255) DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `secondary_qty` double DEFAULT NULL,
  `line_subtotal` double DEFAULT NULL,
  `line_total` double DEFAULT NULL,
  `bill_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  `discount_id` int(11) DEFAULT NULL,
  `unit_net_discount` double DEFAULT NULL,
  `unit_id` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `creator_user_id` int(11) DEFAULT NULL,
  `updater_user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `bill_detail` (`bill_detail_id`, `unit_net_price`, `unit_final_price`, `concept`, `qty`, `secondary_qty`, `line_subtotal`, `line_total`, `bill_id`, `product_id`, `type`, `discount_id`, `unit_net_discount`, `unit_id`, `created_at`, `updated_at`, `creator_user_id`, `updater_user_id`) VALUES
(1, 12.3, 14.883, 'Manzana', 100, NULL, 1230, 1488.3, 1, 1, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL),
(2, 20.3, 24.563, 'Pera', 100, NULL, 2030, 2456.3, 2, 2, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL),
(3, 12.3, 14.883, 'Manzana', 100, NULL, 1230, 1488.3, 3, 1, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL),
(4, 20.3, 24.563, 'Pera', 100, NULL, 2030, 2456.3, 3, 2, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL),
(5, 12.3, 14.883, 'Manzana', 100, NULL, 1230, 1488.3, 4, 1, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL),
(6, 20.3, 24.563, 'Pera', 100, NULL, 2030, 2456.3, 5, 2, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL),
(7, 12.3, 14.883, 'Manzana', 100, NULL, 1230, 1488.3, 6, 1, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL),
(8, 20.3, 24.563, 'Pera', 100, NULL, 2030, 2456.3, 6, 2, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL),
(9, 12.3, 14.883, 'Manzana', 100, NULL, 1230, 1488.3, 8, 1, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL),
(10, 20.3, 24.563, 'Pera', 100, NULL, 2030, 2456.3, 9, 2, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL),
(11, 12.3, 14.883, 'Manzana', 100, NULL, 1230, 1488.3, 10, 1, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL),
(12, 20.3, 24.563, 'Pera', 100, NULL, 2030, 2456.3, 11, 2, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL),
(13, 12.3, 14.883, 'Manzana', 100, NULL, 1230, 1488.3, 12, 1, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL),
(14, 20.3, 24.563, 'Pera', 100, NULL, 2030, 2456.3, 13, 2, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL);

CREATE TABLE `bill_has_payment` (
  `bill_has_payment_id` int(11) NOT NULL,
  `bill_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `amount` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `bill_type` (
  `bill_type_id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `code` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `view` varchar(45) DEFAULT NULL,
  `multiplier` int(11) NOT NULL,
  `customer_required` tinyint(1) DEFAULT NULL,
  `invoice_class_id` int(11) DEFAULT NULL,
  `class` varchar(100) DEFAULT NULL,
  `startable` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `bill_type` (`bill_type_id`, `name`, `code`, `type`, `view`, `multiplier`, `customer_required`, `invoice_class_id`, `class`, `startable`) VALUES
(1, 'Factura A', 1, '', 'default', 1, 1, 1, 'app\\modules\\sale\\models\\bills\\Bill', 1),
(2, 'Factura B', 6, '', 'final', 1, 1, 1, 'app\\modules\\sale\\models\\bills\\Bill', 1),
(3, 'Factura C', 11, '', 'final', 1, 1, NULL, 'app\\modules\\sale\\models\\bills\\Bill', 1),
(4, 'Nota Crédito A', 3, '', 'default', -1, 1, 1, 'app\\modules\\sale\\models\\bills\\Credit', 1),
(5, 'Nota Crédito B', 8, '', 'final', -1, 1, 1, 'app\\modules\\sale\\models\\bills\\Credit', 1),
(6, 'Nota Crédito C', 13, '', 'final', -1, 1, NULL, 'app\\modules\\sale\\models\\bills\\Credit', 1),
(7, 'Nota Débito A', 2, '', 'default', 1, 1, 1, 'app\\modules\\sale\\models\\bills\\Debit', 1),
(8, 'Nota Débito B', 7, '', 'final', 1, 1, 1, 'app\\modules\\sale\\models\\bills\\Debit', 1),
(9, 'Nota Débito C', 12, '', 'final', 1, 1, NULL, 'app\\modules\\sale\\models\\bills\\Debit', 1),
(10, 'Pedido', NULL, '', 'default', 0, 1, NULL, 'app\\modules\\sale\\models\\bills\\Order', 1),
(11, 'Presupuesto', NULL, '', 'default', 0, 1, NULL, 'app\\modules\\sale\\models\\bills\\Budget', 1),
(12, 'Remito', NULL, '', 'default', 0, 1, NULL, 'app\\modules\\sale\\models\\bills\\DeliveryNote', 1);

CREATE TABLE `bill_type_has_bill_type` (
  `parent_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `bill_type_has_bill_type` (`parent_id`, `child_id`) VALUES
(10, 1),
(10, 2),
(10, 12),
(11, 1),
(11, 2),
(11, 3),
(11, 10),
(11, 12),
(12, 1),
(12, 2),
(12, 3);

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `status` enum('enabled','disabled') DEFAULT NULL,
  `system` varchar(50) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `category` (`category_id`, `name`, `status`, `system`, `parent_id`) VALUES
(1, 'Planes de Internet Residencial', 'enabled', 'planes-de-internet-residencial', NULL),
(2, 'Planes de Internet Empresa', 'enabled', 'planes-de-internet-empresa', NULL),
(3, 'Plan por defecto para vendedores', 'enabled', 'default-seller-plan', NULL),
(4, 'Plan para vendedores', 'enabled', 'seller-plan', NULL),
(5, 'Producto para vendedores', 'enabled', 'seller-product', NULL),
(6, 'Instalación Empresa', 'enabled', 'instalacion-empresa', NULL),
(7, 'Instalación Residencial', 'enabled', 'instalacion-residencial', NULL);

CREATE TABLE `checkbook` (
  `checkbook_id` int(11) NOT NULL,
  `start_number` int(11) NOT NULL,
  `end_number` int(11) NOT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  `money_box_account_id` int(11) NOT NULL,
  `last_used` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `checkbook` (`checkbook_id`, `start_number`, `end_number`, `enabled`, `money_box_account_id`, `last_used`) VALUES
(1, 1, 100, 1, 1, 0);

CREATE TABLE `company` (
  `company_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` enum('enabled','disabled') DEFAULT NULL,
  `tax_identification` varchar(45) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `certificate` varchar(255) DEFAULT NULL,
  `key` varchar(255) DEFAULT NULL,
  `create_timestamp` int(11) DEFAULT NULL,
  `tax_condition_id` int(11) NOT NULL,
  `start` date DEFAULT NULL,
  `iibb` varchar(45) DEFAULT NULL,
  `default` tinyint(1) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `fantasy_name` varchar(255) NOT NULL,
  `certificate_phrase` varchar(255) DEFAULT NULL,
  `code` varchar(4) NOT NULL,
  `partner_distribution_model_id` int(11) DEFAULT NULL,
  `pagomiscuentas_code` int(11) DEFAULT NULL,
  `technical_service_phone` text,
  `web` varchar(100) DEFAULT NULL,
  `portal_web` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `company` (`company_id`, `name`, `status`, `tax_identification`, `address`, `phone`, `email`, `parent_id`, `certificate`, `key`, `create_timestamp`, `tax_condition_id`, `start`, `iibb`, `default`, `logo`, `fantasy_name`, `certificate_phrase`, `code`, `partner_distribution_model_id`, `web`, `portal_web`, `pagomiscuentas_code`, `technical_service_phone`) VALUES
(1, 'ACME', 'enabled', '23298348004', '', '', '', 4, 'uploads/certificates/23298348004.crt', 'uploads/keys/23298348004.key', 1441382211, 1, '2015-01-01', '', 1, NULL, '', '', '11', 1, '', '', NULL, NULL),
(2, 'Metro', 'enabled', '23298348004', '', '', '', 4, 'uploads/certificates/23298348004.crt', 'uploads/keys/23298348004.key', 1441382211, 1, '2015-01-02', '', 0, NULL, '', '', '22', 1, '', '', NULL, NULL),
(3, 'Minimarket Juanito', 'enabled', '23298348004', '', '', '', 4, 'uploads/certificates/23298348004.crt', 'uploads/keys/23298348004.key', 1441382211, 2, '2015-01-02', '', 0, NULL, '', '', '33', 1, '', '', NULL, NULL),
(4, 'Corporación', 'enabled', '1111111111', '', '', '', NULL, '', '', 1530652814, 1, '1969-12-31', '', 0, '', 'Corporación', '', '2222', 1, '', '', NULL, NULL);

CREATE TABLE `company_has_billing` (
  `company_has_billing_id` int(11) NOT NULL,
  `parent_company_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `bill_type_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `company_has_bill_type` (
  `company_id` int(11) NOT NULL,
  `bill_type_id` int(11) NOT NULL,
  `default` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `company_has_bill_type` (`company_id`, `bill_type_id`, `default`) VALUES
(1, 1, 1),
(1, 2, NULL),
(1, 4, NULL),
(1, 5, NULL),
(1, 7, NULL),
(1, 8, NULL),
(1, 10, NULL),
(1, 11, NULL),
(1, 12, NULL),
(2, 1, 1),
(2, 2, NULL),
(2, 4, NULL),
(2, 5, NULL),
(2, 7, NULL),
(2, 8, NULL),
(2, 11, NULL),
(2, 12, NULL),
(3, 3, 1),
(3, 6, NULL),
(3, 9, NULL);

CREATE TABLE `conciliation` (
  `conciliation_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `date` date DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `status` enum('draft','closed') DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `money_box_account_id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `resume_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `conciliation_item` (
  `conciliation_item_id` int(11) NOT NULL,
  `conciliation_id` int(11) NOT NULL,
  `amount` double DEFAULT NULL,
  `date` date DEFAULT NULL,
  `description` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `conciliation_item_has_account_movement_item` (
  `account_movement_item_id` int(11) NOT NULL,
  `conciliation_item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `conciliation_item_has_resume_item` (
  `conciliation_item_id` int(11) NOT NULL,
  `resume_item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `connection` (
  `connection_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `node_id` int(11) DEFAULT NULL,
  `server_id` int(11) DEFAULT NULL,
  `ip4_1` mediumtext,
  `ip4_2` mediumtext,
  `ip4_public` mediumtext,
  `status` enum('enabled','disabled','forced','low') NOT NULL DEFAULT 'disabled',
  `due_date` date DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `payment_code` bigint(20) DEFAULT NULL,
  `status_account` enum('enabled','disabled','forced','defaulter','clipped','low') DEFAULT NULL,
  `clean` int(11) DEFAULT '0',
  `old_server_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `connection` (`connection_id`, `contract_id`, `node_id`, `server_id`, `ip4_1`, `ip4_2`, `ip4_public`, `status`, `due_date`, `company_id`, `payment_code`, `status_account`, `clean`, `old_server_id`) VALUES
(1, 1, 1, 1, '175970661', NULL, NULL, 'disabled', NULL, 1, '1', NULL, 0, NULL),
(2, 2, 3, 1, '175970661', '0', '0', 'enabled', NULL, 2, '2', NULL, 0, NULL),
(3, 3, 1, 1, '175970661', NULL, NULL, 'disabled', NULL, 1, '3', NULL, 0, NULL),
(4, 4, 2, 1, '0', '0', '0', 'low', NULL, 1, '4', 'low', 0, NULL),
(5, 5, 1, 1, '175970661', NULL, NULL, 'disabled', NULL, 1,'5', NULL, 0, NULL),
(6, 6, 1, 1, '175970661', NULL, NULL, 'disabled', NULL, 1, '6', NULL, 0, NULL),
(7, 7, 1, 1, '175970661', NULL, NULL, 'disabled', NULL, 1, '7', NULL, 0, NULL),
(8, 8, 3, 1, '175970661', '0', '0', 'enabled', NULL, 1, '8', NULL, 0, NULL),
(9, 9, 4, 1, '168594572', '0', '0', 'disabled', NULL, 2, '9', 'clipped', 0, NULL);

CREATE TABLE `connection_forced_historial` (
  `connection_forced_historial_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `reason` varchar(500) DEFAULT NULL,
  `connection_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `contract` (
  `contract_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `status` enum('draft','active','inactive','canceled','low-process','low','no-want','negative-survey') DEFAULT 'draft',
  `address_id` int(11) DEFAULT NULL,
  `description` text,
  `vendor_id` int(11) DEFAULT NULL,
  `external_id` int(11) DEFAULT NULL,
  `tentative_node` int(11) DEFAULT NULL,
  `print_ads` tinyint(1) DEFAULT '0',
  `instalation_schedule` enum('in the morning','in the afternoon','all day') DEFAULT NULL,
  `low_date` date DEFAULT NULL,
  `category_low_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `contract` (`contract_id`, `customer_id`, `date`, `to_date`, `from_date`, `status`, `address_id`, `description`, `vendor_id`, `external_id`, `tentative_node`, `instalation_schedule`, `print_ads`, `low_date`, `category_low_id`) VALUES
(1, 1, DATE_FORMAT(NOW() ,'%Y-%m-01'), NULL, DATE_FORMAT(NOW() ,'%Y-%m-01'), 'active', 1, 'Primero', NULL, NULL, NULL, NULL, 0, NULL, NULL),
(2, 2, DATE_FORMAT(NOW() ,'%Y-%m-01'), NULL, DATE_FORMAT(NOW() ,'%Y-%m-01'), 'active', 2, 'Segundo', NULL, NULL, NULL, NULL, 0, NULL, NULL),
(3, 3, DATE_FORMAT(NOW() ,'%Y-%m-01'), NULL, DATE_FORMAT(NOW() ,'%Y-%m-01'), 'active', 3, 'Tercero', NULL, NULL, NULL, NULL, 0, NULL, NULL),
(4, 4, DATE_FORMAT(NOW() ,'%Y-%m-01'), NULL, DATE_FORMAT(NOW() ,'%Y-%m-01'), 'active', 4, 'Cuarto', NULL, NULL, NULL, NULL, 0, NULL, NULL),
(5, 5, DATE_FORMAT(NOW() ,'%Y-%m-01'), NULL, DATE_FORMAT(NOW() ,'%Y-%m-01'), 'active', 5, 'Quinto', NULL, NULL, NULL, NULL, 0, NULL, NULL),
(6, 1, DATE_FORMAT(NOW() ,'%Y-%m-01'), NULL, DATE_FORMAT(NOW() ,'%Y-%m-01'), 'draft', 1, 'Sexto (draft)', NULL, NULL, NULL, NULL, 0, NULL, NULL),
(7, 5, DATE_FORMAT(NOW() ,'%Y-%m-01'), NULL, DATE_FORMAT(NOW() ,'%Y-%m-01'), 'active', 5, 'Séptimo', NULL, NULL, NULL, NULL, 0, NULL, NULL),
(8, 1, DATE_FORMAT(NOW() ,'%Y-%m-25'), NULL, DATE_FORMAT(NOW() ,'%Y-%m-25'), 'active', 5, 'Octavo', NULL, NULL, NULL, NULL, 0, NULL, NULL),
(9, 6, DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 2 MONTH) ,'%Y-%m-25'), NULL, DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 2 MONTH) ,'%Y-%m-25'), 'low-process', 7, 'Noveno', NULL, NULL, NULL, NULL, 0, NULL, NULL);

CREATE TABLE `contract_detail` (
  `contract_detail_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `status` enum('draft','active','canceled','low','low-process') NOT NULL DEFAULT 'draft',
  `funding_plan_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `discount_id` int(11) DEFAULT NULL,
  `count` float NOT NULL DEFAULT '0',
  `vendor_id` int(11) DEFAULT NULL,
  `applied` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `contract_detail` (`contract_detail_id`, `contract_id`, `product_id`, `from_date`, `to_date`, `status`, `funding_plan_id`, `date`, `discount_id`, `count`, `vendor_id`, `applied`) VALUES
(1, 1, 3, DATE_FORMAT(NOW() ,'%Y-%m-01'), NULL, 'active', NULL, DATE_FORMAT(NOW() ,'%Y-%m-01'), NULL, 1, NULL, 0),
(2, 2, 4, DATE_FORMAT(NOW() ,'%Y-%m-01'), NULL, 'active', NULL, DATE_FORMAT(NOW() ,'%Y-%m-01'), NULL, 1, NULL, 0),
(3, 3, 5, DATE_FORMAT(NOW() ,'%Y-%m-01'), NULL, 'active', NULL, DATE_FORMAT(NOW() ,'%Y-%m-01'), NULL, 1, NULL, 0),
(4, 4, 3, DATE_FORMAT(NOW() ,'%Y-%m-01'), NULL, 'active', NULL, DATE_FORMAT(NOW() ,'%Y-%m-01'), NULL, 1, NULL, 0),
(5, 5, 4, DATE_FORMAT(NOW() ,'%Y-%m-01'), NULL, 'active', NULL, DATE_FORMAT(NOW() ,'%Y-%m-01'), NULL, 1, NULL, 0),
(6, 2, 6, DATE_FORMAT(NOW() ,'%Y-%m-01'), LAST_DAY(DATE_ADD(NOW(), INTERVAL 3 MONTH)), 'active', 1, DATE_FORMAT(NOW() ,'%Y-%m-01'), NULL, 2, NULL, 0),
(7, 6, 3, DATE_FORMAT(NOW() ,'%Y-%m-01'), NULL, 'draft', NULL, DATE_FORMAT(NOW() ,'%Y-%m-01'), NULL, 1, NULL, 0),
(8, 7, 3, DATE_FORMAT(NOW() ,'%Y-%m-01'), NULL, 'active', NULL, DATE_FORMAT(NOW() ,'%Y-%m-01'), NULL, 1, NULL, 0),
(9, 8, 4, DATE_FORMAT(NOW() ,'%Y-%m-01'), NULL, 'active', NULL, DATE_FORMAT(NOW() ,'%Y-%m-01'), NULL, 1, NULL, 0),
(10, 9, 3, DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 3 MONTH) ,'%Y-%m-25'), NULL, 'active', NULL, DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 3 MONTH) ,'%Y-%m-25'), NULL, 1, NULL, 0),
(11, 9, 8, DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 3 MONTH) ,'%Y-%m-25'), NULL, 'active', NULL, DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 3 MONTH) ,'%Y-%m-25'), NULL, 1, NULL, 0);

CREATE TABLE `contract_detail_log` (
  `contract_detail_log_id` int(11) NOT NULL,
  `contract_detail_id` int(11) NOT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `status` enum('draft','active','canceled') DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `funding_plan_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `discount_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `contract_log` (
  `contract_log_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `status` enum('draft','active','canceled') DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `contract_status` (
  `contract_status_id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `currency` (
  `currency_id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `iso` varchar(5) DEFAULT NULL,
  `rate` double DEFAULT NULL,
  `status` enum('enabled','disabled') DEFAULT NULL,
  `code` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `currency` (`currency_id`, `name`, `iso`, `rate`, `status`, `code`) VALUES
(1, 'Peso', 'ARS', 1, 'enabled', 'ARS'),
(2, 'Dolar estado unidense', 'USD', 13, 'enabled', 'USD');

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `lastname` varchar(150) DEFAULT NULL,
  `document_number` varchar(45) DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `status` enum('enabled','disabled','blocked') DEFAULT NULL,
  `document_type_id` int(11) DEFAULT NULL,
  `tax_condition_id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `email2` varchar(45) DEFAULT NULL,
  `phone2` varchar(45) DEFAULT NULL,
  `phone3` varchar(45) DEFAULT NULL,
  `customer_reference_id` int(11) DEFAULT NULL,
  `code` bigint(20) DEFAULT NULL,
  `payment_code` varchar(20) DEFAULT '0',
  `publicity_shape` enum('banner','poster','web','other_customer','facebook','street_banner','magazine','door_to_door','competition','brochure') DEFAULT NULL,
  `screen_notification` tinyint(1) DEFAULT '0',
  `sms_notification` tinyint(1) DEFAULT '0',
  `email_notification` tinyint(1) DEFAULT '0',
  `sms_fields_notifications` varchar(45) DEFAULT NULL,
  `email_fields_notifications` varchar(45) DEFAULT NULL,
  `anchor_customer` tinyint(1) DEFAULT '0',
  `needs_bill` tinyint(1) DEFAULT '0',
  `parent_company_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `customer` (`customer_id`, `name`, `lastname`, `document_number`, `sex`, `email`, `phone`, `status`, `document_type_id`, `tax_condition_id`, `account_id`, `company_id`, `address_id`, `email2`, `phone2`, `phone3`, `customer_reference_id`, `code`, `payment_code`, `publicity_shape`, `screen_notification`, `sms_notification`, `email_notification`, `sms_fields_notifications`, `email_fields_notifications`, `anchor_customer`, `needs_bill`, `parent_company_id`) VALUES
(1, 'Juan', 'Garcia', '23123456790', NULL, 'juan@garcia.com', '261-558 6945', 'enabled', 1, 1, 115, 1, 1, '', '', NULL, NULL, 9999123, '123', NULL, 0, 0, 0, NULL, NULL, 0, 0, NULL),
(2, 'José', 'Gómez', '12345678', NULL, 'jose@gomez.edu', '(11) 4596 6962', 'enabled', 2, 3, 115, 2, 2, NULL, NULL, NULL, NULL, 9999234, '234', NULL, 0, 0, 0, NULL, NULL, 0, 0, NULL),
(3, 'Ana', 'A', '23123456790', NULL, 'ana@a.edu', '(11) 4596 6962', 'enabled', 1, 1, 115, 1, 3, NULL, NULL, NULL, NULL, 9999345, '345', NULL, 0, 0, 0, NULL, NULL, 0, 0, NULL),
(4, 'Betina', 'B', '23123456790', NULL, 'betina@b.edu', '(11) 4596 6962', 'enabled', 1, 1, 115, 1, 4, NULL, NULL, NULL, NULL, 9999456, '456', NULL, 0, 0, 0, NULL, NULL, 0, 0, NULL),
(5, 'César SRL', 'C', '23123456790', NULL, 'cesar@c.edu', '(11) 4596 6962', 'enabled', 1, 1, 115, 1, 5, NULL, NULL, NULL, NULL, 9999567, '567', NULL, 0, 0, 0, NULL, NULL, 0, 0, NULL),
(6, 'Daniela', 'D', '11-12345678-9', NULL, 'daniel@d.org', '123456798', 'blocked', 1, 4, NULL, 2, 7, '', '', '', NULL, 9999678, '568', NULL, 0, 0, 0, NULL, NULL, 0, 0, NULL);

CREATE TABLE `customer_category` (
  `customer_category_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` enum('enabled','disabled') DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `customer_category` (`customer_category_id`, `name`, `status`, `parent_id`) VALUES
(1, 'Particular', 'enabled', NULL),
(2, 'Empresa', 'enabled', NULL);

CREATE TABLE `customer_category_has_customer` (
  `customer_category_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `date_updated` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `customer_category_has_customer` (`customer_category_id`, `customer_id`, `date_updated`) VALUES
(1, 1, 1451935639),
(1, 2, 1451935673),
(1, 3, 1451935401),
(1, 4, 1451935699),
(1, 6, 1476453170),
(2, 5, 1451935720);

CREATE TABLE `customer_class` (
  `customer_class_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `code_ext` int(11) DEFAULT NULL,
  `is_invoiced` tinyint(1) DEFAULT NULL,
  `tolerance_days` int(11) DEFAULT NULL,
  `colour` varchar(54) DEFAULT NULL,
  `percentage_bill` int(11) DEFAULT NULL,
  `days_duration` int(11) DEFAULT NULL,
  `service_enabled` tinyint(1) DEFAULT NULL,
  `percentage_tolerance_debt` int(11) DEFAULT NULL,
  `status` enum('enabled','disabled') DEFAULT 'enabled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `customer_class` (`customer_class_id`, `name`, `code_ext`, `is_invoiced`, `tolerance_days`, `colour`, `percentage_bill`, `days_duration`, `service_enabled`, `percentage_tolerance_debt`, `status`) VALUES
(1, 'Básico', 1, 1, 1, '#274e13', 100, 1, NULL, 10, 'enabled'),
(2, 'VIP', 1, 1, 30, '#4b1130', 75, 3, 1, 80, 'enabled'),
(3, 'Free', 1, 0, 1, '#073764', 0, 1, NULL, 0, 'enabled'),
(4, 'Mantenimiento', 2, 1, 20, '#e06767', 25, 30, NULL, 0, 'enabled');

CREATE TABLE `customer_class_has_customer` (
  `customer_class_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `date_updated` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `customer_class_has_customer` (`customer_class_id`, `customer_id`, `date_updated`) VALUES
(1, 1, 1451935639),
(1, 2, 1451935673),
(1, 6, 1476453582),
(2, 3, 1451935401),
(3, 4, 1476453574),
(4, 5, 1451935720);

CREATE TABLE `customer_has_discount` (
  `cutomer_has_discount_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `discount_id` int(11) NOT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `status` enum('enabled','disabled') DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `customer_log` (
  `customer_log_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `before_value` varchar(450) DEFAULT NULL,
  `new_value` varchar(450) DEFAULT NULL,
  `date` datetime NOT NULL,
  `customer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `observations` varchar(300) DEFAULT NULL,
  `object_id` int(11) NOT NULL,
  `class_name` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `debt_evolution` (
  `debt_evolution_id` int(11) NOT NULL,
  `period` date DEFAULT NULL,
  `invoice_1` int(11) DEFAULT '0',
  `invoice_2` int(11) DEFAULT '0',
  `invoice_3` int(11) DEFAULT '0',
  `invoice_4` int(11) DEFAULT '0',
  `invoice_5` int(11) DEFAULT '0',
  `invoice_6` int(11) DEFAULT '0',
  `invoice_7` int(11) DEFAULT '0',
  `invoice_8` int(11) DEFAULT '0',
  `invoice_9` int(11) DEFAULT '0',
  `invoice_10` int(11) DEFAULT '0',
  `invoice_x` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `discount` (
  `discount_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` enum('enabled','disabled') DEFAULT NULL,
  `type` enum('fixed','percentage') DEFAULT NULL,
  `value` double DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `periods` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `apply_to` enum('customer','product') DEFAULT NULL,
  `value_from` enum('total','product') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `discount` (`discount_id`, `name`, `status`, `type`, `value`, `from_date`, `to_date`, `periods`, `product_id`, `apply_to`, `value_from`) VALUES
(1, 'Referenciado', 'enabled', 'fixed', 10, '2017-01-01', '2020-12-31', 1, NULL, 'customer', 'total');

CREATE TABLE `discount_event` (
  `discount_event_id` int(11) NOT NULL,
  `title` varchar(45) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `exp_date` date DEFAULT NULL,
  `exp_time` time DEFAULT NULL,
  `exp_datetime` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `status` enum('enabled','disabled','soldout') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `document_type` (
  `document_type_id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `code` int(11) DEFAULT NULL,
  `regex` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `document_type` (`document_type_id`, `name`, `code`, `regex`) VALUES
(1, 'CUIT', 80, ''),
(2, 'DNI', 96, ''),
(3, 'LE', 89, ''),
(4, 'LC', 90, ''),
(5, 'CI Extranjera ', 91, ''),
(6, 'Pasaporte', 94, '');

CREATE TABLE `email_transport` (
  `email_transport_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `from_email` varchar(50) NOT NULL,
  `transport` varchar(100) NOT NULL,
  `host` varchar(50) NOT NULL,
  `port` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `encryption` varchar(10) DEFAULT NULL,
  `layout` varchar(100) DEFAULT NULL,
  `relation_class` varchar(100) DEFAULT NULL,
  `relation_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `empty_ads` (
  `empty_ads_id` int(11) NOT NULL,
  `code` bigint(20) NOT NULL,
  `payment_code` varchar(20) NOT NULL,
  `node_id` int(11) NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '0',
  `company_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `funding_plan` (
  `funding_plan_id` int(11) NOT NULL,
  `qty_payments` int(11) DEFAULT NULL,
  `amount_payment` double DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `status` enum('enabled','disabled') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `funding_plan` (`funding_plan_id`, `qty_payments`, `amount_payment`, `product_id`, `status`) VALUES
(1, 3, 400, 6, 'enabled');

CREATE TABLE `invoice_class` (
  `invoice_class_id` int(11) NOT NULL,
  `class` varchar(255) NOT NULL,
  `name` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `invoice_class` (`invoice_class_id`, `class`, `name`) VALUES
(1, 'app\\modules\\invoice\\components\\einvoice\\afip\\fev1\\Fev1', 'Fev 1 Afip');

CREATE TABLE `ip_range` (
  `ip_range_id` int(11) NOT NULL,
  `ip_start` int(11) DEFAULT NULL,
  `ip_end` int(11) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `node_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `ip_range` (`ip_range_id`, `ip_start`, `ip_end`, `status`, `node_id`) VALUES
(1, 169149186, 169213694, NULL, 1),
(2, 169214722, 169279230, NULL, 2),
(3, 169804546, 169869054, NULL, 3),
(4, 169870082, 169934590, NULL, 4);

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1464379186),
('m150804_184839_accounts', 1467228978),
('m150818_129999_config_create_database', 1465480206),
('m150818_130000_create_task_type_table', 1465480221),
('m150818_130001_create_status_table', 1465480221),
('m150818_130003_create_category_table', 1465480221),
('m150818_130004_create_event_type_table', 1465480222),
('m150818_130005_create_user_group_table', 1465480222),
('m150818_130006_create_user_group_has_user_table', 1465480223),
('m150818_130007_create_task_table', 1465480225),
('m150818_130008_create_notification_table', 1465480226),
('m150818_130009_create_event_table', 1465480227),
('m150818_130009_insert_values', 1465480227),
('m150828_201927_config_create_database', 1464379239),
('m150828_201928_config_create_category_table', 1464379252),
('m150828_202305_config_create_item_table', 1464379283),
('m150828_203927_config_create_config_table', 1464379291),
('m150828_204134_config_create_rule_table', 1464379299),
('m150828_204234_insert_agenda_values', 1464379306),
('m150828_204235_insert_ticket_values', 1464379314),
('m150903_140845_company_version_15', 1467228978),
('m150919_215034_config_order', 1464379321),
('m150919_215035_config_stock_managment', 1464379328),
('m151009_200611_westnet_accounts', 1467228978),
('m151020_135813_update_values', 1465480227),
('m151111_040517_paycheck_accounting', 1467228978),
('m151116_201217_config', 1464379335),
('m151116_201220_config_product_media', 1464379343),
('m151120_185859_config_customer', 1467228978),
('m151201_120418_ecopago_assignment', 1467130126),
('m151201_121568_ecopago_config', 1467130126),
('m151201_131568_ecopago_back_account', 1467130126),
('m160202_210510_sequre', 1466607612),
('m160216_175243_westnetConfig', 1466607612),
('m160216_181004_perfiles', 1466607612),
('m160229_134758_operation_type', 1467228978),
('m160229_145922_account_config', 1467228978),
('m160229_163105_bancos', 1467228978),
('m160407_164905_payment_methods', 1467228978),
('m160407_184147_payment_methods', 1475518739),
('m160416_111419_zone_recursion', 1467228978),
('m160425_174229_config_contract_days_for_invoice_next_month', 1475518739),
('m160511_190825_partner_config', 1475520454),
('m160524_155847_money_box_bank', 1467228978),
('m160527_194258_default_unit', 1467228978),
('m160531_035040_default_tax_rate_code', 1467228978),
('m160608_195958_afip_certificates_in_relative_path', 1467228995),
('m160609_180257_ecopago_payout_limit', 1467130402),
('m160615_195300_config_mesa_server', 1466607625),
('m160711_230119_default_seller_plan_category', 1468612318),
('m160715_204452_small_money_box', 1468850785),
('m160715_215742_small_money_box_movement', 1468850794),
('m160718_150001_mailing_config', 1468915855),
('m160718_185311_bill_due_day', 1468915855),
('m160720_035645_instalation_category_id', 1469016544),
('m160720_131414_money_box_smallbox_config', 1469027421),
('m160720_171337_vendor_selection_permission', 1469035965),
('m160721_194639_contract_detail_vendor_id', 1469893701),
('m160722_204414_vendor_commission_table', 1469893706),
('m160722_204536_vendor_commission_parameters', 1469893706),
('m160728_174607_max_number_ads_empty', 1469824058),
('m160728_234809_vendor_comission_init', 1469893707),
('m160729_002417_product_commission', 1469893708),
('m160731_222124_plan_increase_config', 1470076305),
('m160801_193755_contract_detail_applied', 1470087632),
('m160801_195431_contract_detail_count_property', 1470087637),
('m160801_221952_set_contract_detail_count_to_1', 1470090953),
('m160804_163749_customer_payment_code', 1471350758),
('m160810_142405_partner_modifications', 1471350761),
('m160811_181406_ecopago_batch_closure_company', 1471350762),
('m160811_193003_ecopago_provider', 1471454936),
('m160811_203137_pdf_service_config', 1471350762),
('m160811_203235_customer_log_table', 1471350763),
('m160816_155243_batch_closure_bill_types', 1471454936),
('m160818_164222_ticket_status_nuevo', 1471956252),
('m160818_185357_vendor_ticket_user', 1471956234),
('m160819_190943_low_process_contract_status', 1472820283),
('m160822_131834_account_movement_relation', 1472670690),
('m160824_140348_address_number_to_string', 1472061260),
('m160824_175741_add_tentative_node', 1472132548),
('m160824_181739_add_destinatary_filtered_by_contract_age', 1472132549),
('m160829_131250_customer_add_publicity_shape', 1474375298),
('m160829_160451_contract_instalation_schedule', 1474375300),
('m160830_134402_connection_forced_history_table', 1473700639),
('m160830_151949_times_forced_conn_month_config', 1473700639),
('m160902_132046_product_to_invoice_qty', 1473093379),
('m160908_205829_new_contract_days', 1474394375),
('m160909_142259_app_testing_config_item', 1473700639),
('m160912_175337_new_contracts_days', 1473703146),
('m160913_140942_contract_and_connection_new_status', 1473860598),
('m160914_133230_empty_ads_table', 1474929987),
('m160915_124740_pago_facil_payment_method', 1473949780),
('m160920_152151_contract_installation_schedule_all_day', 1474385532),
('m160920_161859_notification_destinatary_debts', 1474394380),
('m160920_175622_no_want_and_negative_survey_contract_status', 1474929987),
('m160920_205533_contract_print_ads_attribute', 1474929989),
('m160921_134215_seller_product_category', 1474640133),
('m160922_130633_notifications_filters', 1476369208),
('m160927_200147_taxes_book_number', 1475519620),
('m160930_130238_vendor_provider', 1475519621),
('m161005_184155_account_movement_broken', 1478012029),
('m161006_185008_add_account_movement_check', 1476280957),
('m161006_201613_bills_config_ticket', 1476280957),
('m161013_191754_vendor_liquidation_billed_status', 1476389498),
('m161017_173825_brochure_publicity_shape', 1476737887),
('m161018_153947_correccion_adicionales_instalacion', 1476883010),
('m161024_130002_customer_notification_way', 1477413285),
('m161024_182210_set_notifications_way', 1477413286),
('m161025_115104_teleprom_testing_number', 1478012029),
('m161025_132523_customer_class_status', 1477413287),
('m161026_151356_parent_outflow_account', 1478012029),
('m161101_134441_payment_plan_update_items', 1478012029),
('m161101_185246_geocode_correction', 1478110307),
('m161101_200016_customer_log_correction', 1478110416),
('m161102_151856_tax_condition_has_document_type', 1478117364),
('m161109_145538_referenced_discount', 1478863213),
('m161110_134806_provider_payments', 1479925681),
('m170118_143202_westnet_server_fields', 1489580965),
('m170214_155350_partner_liquidation_columns', 1487689241),
('m170313_170915_contract_detail_low', 1489580968),
('m170512_170013_parametro_ciudad_815', 1494611189),
('m170519_174746_customer_parent_company', 1540305950),
('m170529_115241_connection_clean', 1496753673),
('m170727_104314_router_config', 1501159009),
('m170803_142824_customer_log_modify_fields', 1501772025),
('m170825_155254_company_data', 1540305950),
('m170828_120337_db_init', 1540305942),
('m170831_144515_company_notification', 1540305944),
('m170905_190123_debt_evolution_table', 1540305944),
('m170906_155731_low_reason', 1540305944),
('m170906_192130_contract_low', 1540305945),
('m171106_153824_update_connections', 1540305952),
('m171113_131806_discount_description', 1511821849),
('m171124_142723_fix_recomendation_discount_to_date', 1511821849),
('m171218_195657_tax_condition_buy', 1514326265),
('m180115_160453_modifiers', 1540306444),
('m180126_135039_cupon_bill_type', 1517246951),
('m180219_144308_add_fk_connection_forced_historial_user', 1519052836),
('m180504_163741_task_slug', 1540305945),
('m180504_195034_mobile_app_tables', 1534868297),
('m180504_200347_mobile_app_private_token_param', 1534868297),
('m180507_150244_validation_code_expire_param', 1534868297),
('m180508_131844_auth_token_status_column_expire_timestamp_to', 1534868298),
('m180508_143836_auth_token_duration_param', 1534868298),
('m180514_134030_one_signal_params', 1534868298),
('m180514_152616_mobile_push_table', 1534868299),
('m180514_180410_mobile_push_transport_create', 1534868300),
('m180514_214025_invoice_mobile_push_content_param', 1534868300),
('m180517_143223_sms_api_params', 1534868300),
('m180517_184555_sms_validation_content', 1534868300),
('m180517_192902_enable_send_sms', 1534868300),
('m180522_194750_ecopagos_company_param', 1534868300),
('m180523_150918_remove_facebook_id_and_google_id', 1534868301),
('m180611_125808_company_pagomiscuentas', 1531319043),
('m180611_151618_pagomiscuentas', 1531319061),
('m180614_155650_config', 1531319061),
('m180619_143155_document_number_column_in_user_app', 1534868301),
('m180619_173117_destinatary_column_in_user_app', 1534868301),
('m180625_143538_app_failed_register_table', 1534868302),
('m180627_150844_ecopago_add_justification_table', 1540306444),
('m180628_174551_ecopago_add_date_into_justification_table', 1540306444),
('m180703_122814_config_item_length_for_justification', 1540305948),
('m180711_171515_customer_needs_bill', 1540305946),
('m180712_124616_companies_without_bills', 1540306444),
('m180727_132154_payment_amount', 1532700080),
('m180727_155960_add_configuration_item_for_ads_message', 1540306444),
('m180806_184547_lastname_not_required_in_app_failed_register', 1534868302),
('m180814_192805_mobile_push_content_to_text', 1534954554),
('m180814_200205_mobile_push_has_user_app_change_schema', 1534954555),
('m180815_141257_type_column_mobile_push', 1534954555),
('m180815_183926_customer_code_not_required_in_app_failed_register', 1534954555),
('m180815_203707_customer_anchor', 1534868312),
('m180816_181119_app_failed_register_remove_fields', 1534954556),
('m180817_114217_add_geoposition_fields_into_node_table', 1540305946),
('m180817_153210_add_configuration_items_for_ads', 1540306444),
('m180823_121611_data_enter_node_geoposition', 1540305946),
('m180827_122118_sanatize_provider_bill', 1540306445),
('m180830_110318_add_electronic_billing_field_into_point_of_sale', 1538647353),
('m180830_113513_add_point_of_sale_field_into_bill', 1538647488),
('m180905_184358_config_ads_message_to_html', 1540306445),
('m180912_143001_consumidor_final', 1540306445),
('m181003_163147_node_code_ecopago', 1540306445),
('m181008_135536_billing_company', 1540306445),
('m181017_123022_remove_config_item_from_ads_category', 1540305946),
('m181017_142712_add_technical_phone_into_company', 1540305946);

CREATE TABLE `mobile_push` (
  `mobile_push_id` int(11) NOT NULL,
  `title` varchar(45) NOT NULL,
  `content` text,
  `status` enum('draft','pending','sended') NOT NULL,
  `send_timestamp` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `type` enum('default','invoice') DEFAULT 'default'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mobile_push_has_user_app` (
  `mobile_push_has_user_app_id` int(11) NOT NULL,
  `user_app_id` int(11) NOT NULL,
  `mobile_push_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `money_box` (
  `money_box_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `money_box_type_id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `money_box` (`money_box_id`, `name`, `money_box_type_id`, `account_id`) VALUES
(1, 'ABN AMRO', 1, 118),
(2, 'American Express Bank', 1, 118),
(3, 'BACS', 1, 118),
(4, 'Banco B.I. Creditanstalt', 1, 118),
(5, 'Banco Bradesco', 1, 118),
(6, 'Banco Cetelem', 1, 118),
(7, 'Banco Ciudad', 1, 118),
(8, 'Banco CMF', 1, 118),
(9, 'Banco Cofidis', 1, 118),
(10, 'Banco Columbia', 1, 118),
(11, 'Banco Comafi', 1, 118),
(12, 'Banco Credicoop', 1, 118),
(13, 'Banco de Córdoba', 1, 118),
(14, 'Banco de Corrientes', 1, 118),
(15, 'Banco de Formosa', 1, 118),
(16, 'Banco de La Pampa', 1, 118),
(17, 'Banco de San Juan', 1, 118),
(18, 'Banco de Santiago del Estero', 1, 118),
(19, 'Banco de Servicios Financieros', 1, 118),
(20, 'Banco de Servicios y Transacciones', 1, 118),
(21, 'Banco de Tierra del Fuego', 1, 118),
(22, 'Banco de Valores', 1, 118),
(23, 'Banco del Chubut', 1, 118),
(24, 'Banco del Sol', 1, 118),
(25, 'Banco del Tucumán', 1, 118),
(26, 'Banco del Uruguay', 1, 118),
(27, 'Banco do Brasil', 1, 118),
(28, 'Banco Finansur', 1, 118),
(29, 'Banco Galicia', 1, 118),
(30, 'Banco Hipotecario', 1, 118),
(31, 'Banco Industrial', 1, 118),
(32, 'Banco Itaú', 1, 118),
(33, 'Banco Julio', 1, 118),
(34, 'Banco Macro', 1, 118),
(35, 'Banco Mariva', 1, 118),
(36, 'Banco Masventas', 1, 118),
(37, 'Banco Meridian', 1, 118),
(38, 'Banco Municipal de Rosario', 1, 118),
(39, 'Banco Nación', 1, 118),
(40, 'Banco Patagonia', 1, 118),
(41, 'Banco Piano', 1, 118),
(42, 'Banco Provincia', 1, 118),
(43, 'Banco Provincia del Neuquén', 1, 118),
(44, 'Banco Regional de Cuyo', 1, 118),
(45, 'Banco Roela', 1, 118),
(46, 'Banco Saenz', 1, 118),
(47, 'Banco Santa Cruz', 1, 118),
(48, 'Banco Santander Río', 1, 118),
(49, 'Banco Supervielle', 1, 118),
(50, 'Bank of America', 1, 118),
(51, 'Bank of Tokyo-Mitsubishi UFJ', 1, 118),
(52, 'BBVA Banco Francés', 1, 118),
(53, 'BICE', 1, 118),
(54, 'BNP Paribas', 1, 118),
(55, 'Citibank', 1, 118),
(56, 'Deutsche Bank', 1, 118),
(57, 'HSBC Bank', 1, 118),
(58, 'ICBC', 1, 118),
(59, 'JPMorgan', 1, 118),
(60, 'MBA Lazard Banco De Inversiones', 1, 118),
(61, 'Nuevo Banco de Entre Ríos', 1, 118),
(62, 'Nuevo Banco de La Rioja', 1, 118),
(63, 'Nuevo Banco de Santa Fe', 1, 118),
(64, 'Nuevo Banco del Chaco', 1, 118),
(65, 'RCI Banque', 1, 118),
(66, 'Creditazo', 2, 118),
(67, 'Mutual Money Ya', 2, 118),
(68, 'Martínez', 3, 115),
(69, 'Alameda', 3, 115),
(70, 'Westnet', 4, 115);

CREATE TABLE `money_box_account` (
  `money_box_account_id` int(11) NOT NULL,
  `number` varchar(45) NOT NULL,
  `enable` tinyint(1) DEFAULT NULL,
  `money_box_id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `currency_id` int(11) NOT NULL,
  `small_box` tinyint(1) DEFAULT NULL,
  `daily_box_last_closing_date` date DEFAULT NULL,
  `daily_box_last_closing_time` time DEFAULT NULL,
  `type` enum('common','daily','small') NOT NULL DEFAULT 'common'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `money_box_account` (`money_box_account_id`, `number`, `enable`, `money_box_id`, `company_id`, `account_id`, `currency_id`, `small_box`, `daily_box_last_closing_date`, `daily_box_last_closing_time`, `type`) VALUES
(1, '1234', 1, 44, 1, 118, 1, NULL, NULL, NULL, 'common'),
(2, 'Centro', 1, 70, 1, 118, 1, 1, NULL, NULL, 'common'),
(3, 'Godoy Cruz', 1, 70, 1, 115, 1, 1, NULL, NULL, 'common');

CREATE TABLE `money_box_has_operation_type` (
  `money_box_has_operation_type_id` int(11) NOT NULL,
  `operation_type_id` int(11) NOT NULL,
  `money_box_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `money_box_account_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `money_box_type` (
  `money_box_type_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `code` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `money_box_type` (`money_box_type_id`, `name`, `code`) VALUES
(1, 'Banco', 'BANCARIA'),
(2, 'Financiera', 'FINANCIERA'),
(3, 'Ecopago', 'ECOPAGO'),
(4, 'Caja', 'CAJA');

CREATE TABLE `node` (
  `node_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `zone_id` int(11) DEFAULT NULL,
  `status` enum('enabled','disabled') DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `subnet` int(11) NOT NULL,
  `server_id` int(11) NOT NULL,
  `parent_node_id` int(11) DEFAULT NULL,
  `geocode` text,
  `has_ecopago_close` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `node` (`node_id`, `name`, `zone_id`, `status`, `subnet`, `server_id`, `parent_node_id`, `geocode`, `has_ecopago_close`) VALUES
(1, 'Origen', 2, 'enabled', 21, 1, NULL, '-32.898856875223686,-68.81966524788515', NULL),
(2, 'Destino', 2, 'enabled', 22, 1, NULL, '-32.89900100726875,-68.81984763809817', NULL),
(3, 'Norte', 2, 'enabled', 31, 1, NULL, '-32.776673020667204,-68.34461711904908', NULL),
(4, 'Sur', 2, 'enabled', 32, 1, NULL, '-32.84123897177021,-68.69536474603274', NULL);

CREATE TABLE `node_has_ecopago` (
  `node_id` int(11) NOT NULL,
  `ecopago_id` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `node_has_ecopago` (`node_id`, `ecopago_id`) VALUES
(1, '1'),
(1, '2');

CREATE TABLE `operation_type` (
  `operation_type_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `is_debit` tinyint(1) DEFAULT NULL,
  `code` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `pagomiscuentas_file` (
  `pagomiscuentas_file_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `file` varchar(256) DEFAULT NULL,
  `path` varchar(256) DEFAULT NULL,
  `company_id` int(11) NOT NULL,
  `type` enum('bill','payment') DEFAULT NULL,
  `status` enum('draft','closed') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `pagomiscuentas_file_has_bill` (
  `pagomiscuentas_file_has_bill_id` int(11) NOT NULL,
  `pagomiscuentas_file_id` int(11) NOT NULL,
  `bill_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `pagomiscuentas_file_has_payment` (
  `pagomiscuentas_file_has_payment_id` int(11) NOT NULL,
  `pagomiscuentas_file_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `pago_facil_payment` (
  `pago_facil_payment_id` int(11) NOT NULL,
  `pago_facil_transmition_file_pago_facil_transmition_file_id` int(11) NOT NULL,
  `payment_payment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `pago_facil_transmition_file` (
  `pago_facil_transmition_file_id` int(11) NOT NULL,
  `header_file` varchar(256) NOT NULL,
  `upload_date` date NOT NULL,
  `money_box_account_id` int(11) NOT NULL,
  `total` double NOT NULL,
  `money_box_id` int(11) NOT NULL,
  `file_name` varchar(45) NOT NULL,
  `status` enum('draft','closed') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `partner` (
  `partner_id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `account_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `partner` (`partner_id`, `name`, `account_id`) VALUES
(1, 'MAURICIO', 117),
(2, 'GABRIEL', 117);

CREATE TABLE `partner_distribution_model` (
  `partner_distribution_model_id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `company_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `partner_distribution_model` (`partner_distribution_model_id`, `name`, `company_id`) VALUES
(1, 'Distribución', 1);

CREATE TABLE `partner_distribution_model_has_partner` (
  `partner_distribution_model_has_partner_id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `partner_distribution_model_id` int(11) NOT NULL,
  `percentage` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `partner_distribution_model_has_partner` (`partner_distribution_model_has_partner_id`, `partner_id`, `partner_distribution_model_id`, `percentage`) VALUES
(1, 1, 1, 40),
(2, 2, 1, 60);

CREATE TABLE `partner_liquidation` (
  `partner_liquidation_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `partner_distribution_model_has_partner_id` int(11) NOT NULL,
  `debit` double DEFAULT '0',
  `credit` double DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `partner_liquidation_movement` (
  `partner_liquidation_movement_id` int(11) NOT NULL,
  `partner_liquidation_id` int(11) NOT NULL,
  `class` varchar(255) NOT NULL,
  `model_id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `paycheck` (
  `paycheck_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `number` varchar(45) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `document_number` varchar(45) DEFAULT NULL,
  `status` enum('created','commited','received','canceled','cashed','rejected','returned','deposited') DEFAULT NULL,
  `business_name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_own` tinyint(1) DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `checkbook_id` int(11) DEFAULT NULL,
  `money_box_id` int(11) DEFAULT NULL,
  `crossed` tinyint(1) DEFAULT NULL,
  `to_order` tinyint(1) DEFAULT NULL,
  `money_box_account_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `paycheck_log` (
  `paycheck_log_id` int(11) NOT NULL,
  `paycheck_id` int(11) NOT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `status` enum('created','commited','received','canceled','cashed','rejected','returned','deposited') DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `money_box_account_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `amount` double NOT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `concept` varchar(255) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `number` varchar(45) DEFAULT NULL,
  `balance` double DEFAULT NULL,
  `status` enum('draft','closed','tabulated','conciled','cancelled') NOT NULL DEFAULT 'draft',
  `company_id` int(11) DEFAULT NULL,
  `partner_distribution_model_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `payment_item` (
  `payment_item_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `description` varchar(150) DEFAULT NULL,
  `number` varchar(45) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `payment_method_id` int(11) NOT NULL,
  `paycheck_id` int(11) DEFAULT NULL,
  `money_box_account_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `payment_method` (
  `payment_method_id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `status` enum('enabled','disabled') DEFAULT NULL,
  `register_number` tinyint(1) DEFAULT NULL,
  `type` enum('exchanging','provisioning','account') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `payment_method` (`payment_method_id`, `name`, `status`, `register_number`, `type`) VALUES
(1, 'Contado', 'enabled', 0, 'exchanging'),
(2, 'Tarjeta de Crédito', 'enabled', 0, 'provisioning'),
(3, 'Tarjeta de Débito', 'enabled', 0, 'provisioning'),
(4, 'Cuenta Corriente ', 'enabled', 0, 'account'),
(5, 'Cheques', 'enabled', 1, 'provisioning'),
(6, 'Pago Fácil', 'enabled', 1, 'exchanging');

CREATE TABLE `payment_plan` (
  `payment_plan_id` int(11) NOT NULL,
  `from_date` date NOT NULL,
  `status` enum('active','canceled','completed') NOT NULL DEFAULT 'active',
  `fee` int(11) NOT NULL,
  `original_amount` double NOT NULL,
  `final_amount` double NOT NULL,
  `payment_plan_amount` double NOT NULL,
  `apply` int(11) DEFAULT NULL,
  `value_applied` double DEFAULT NULL,
  `customer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `plan_feature` (
  `plan_feature_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('radiobutton','checkbox') DEFAULT NULL,
  `description` text,
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `plan_feature` (`plan_feature_id`, `name`, `type`, `description`, `parent_id`) VALUES
(1, 'Activación', 'checkbox', '', NULL),
(2, 'Inmediata', '', '', 1),
(3, 'Bonificada', '', '', 1),
(4, 'Velocidad', 'radiobutton', '', NULL),
(5, 'Rápida', '', '', 4),
(6, 'Lenta', '', '', 4);

CREATE TABLE `point_of_sale` (
  `point_of_sale_id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `number` int(11) NOT NULL,
  `status` enum('enabled','disabled') DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `company_id` int(11) NOT NULL,
  `default` tinyint(1) DEFAULT NULL,
  `electronic_billing` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `point_of_sale` (`point_of_sale_id`, `name`, `number`, `status`, `description`, `company_id`, `default`, `electronic_billing`) VALUES
(1, 'Auto', 1, 'enabled', '', 1, 1, 1),
(2, 'Auto', 1, 'enabled', '', 2, 1, 1),
(3, 'Auto', 1, 'enabled', '', 3, 1, 1),
(4, 'Invalid', 0, 'disabled', '', 4, 1, 1),
(5, 'Manual', 2, 'enabled', '', 1, 0, 0),
(6, 'Manual', 2, 'enabled', '', 2, 0, 0),
(7, 'Manual', 2, 'enabled', '', 3, 0, 0);

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `system` varchar(100) DEFAULT NULL,
  `code` varchar(45) DEFAULT NULL,
  `description` text,
  `status` enum('enabled','disabled') DEFAULT NULL,
  `balance` double DEFAULT NULL,
  `secondary_balance` double DEFAULT NULL,
  `create_timestamp` int(11) DEFAULT NULL,
  `update_timestamp` int(11) DEFAULT NULL,
  `unit_id` int(11) NOT NULL,
  `secondary_unit_id` int(11) DEFAULT NULL,
  `type` enum('service','product','plan') DEFAULT 'product',
  `uid` varchar(45) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `product_commission_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `product` (`product_id`, `name`, `system`, `code`, `description`, `status`, `balance`, `secondary_balance`, `create_timestamp`, `update_timestamp`, `unit_id`, `secondary_unit_id`, `type`, `uid`, `account_id`, `product_commission_id`) VALUES
(1, 'Manzana', 'manzana', '55e85a524c969', 'Malus domestica', 'enabled', 100, 90, 1441290834, 1441290834, 1, 2, 'product', NULL, 115, NULL),
(2, 'Pera', 'pera', '55f0258a9893c', 'Pyrus cordata', 'enabled', 90, 80, 1441290834, 1449085727, 1, 2, 'product', NULL, 115, NULL),
(3, 'Bronze', 'bronce', '568eb49e896e5', 'Plan Bronze - 1Mbps', 'enabled', NULL, NULL, 1441290834, 1441290834, 1, NULL, 'plan', NULL, 115, NULL),
(4, 'Silver', 'silver', '568eb49e896e6', 'Plan Silver - 10Mbps', 'enabled', NULL, NULL, 1441290834, 1441290834, 1, NULL, 'plan', NULL, 115, NULL),
(5, 'Gold', 'gold', '568eb49e896e7', 'Plan Gold - 100Mbps', 'enabled', NULL, NULL, 1441290834, 1441290834, 1, NULL, 'plan', NULL, 115, NULL),
(6, 'Router', 'router', '568eb49e896e4', '', 'enabled', NULL, NULL, 1452192926, 1452192926, 2, NULL, 'product', NULL, 115, NULL),
(7, 'Kiwi', 'kiwi', '55f0258a9893d', 'Actinidia deliciosa', 'enabled', 30, NULL, 1441290834, 1449085727, 1, NULL, 'product', NULL, 115, NULL),
(8, 'Instalación', 'instalacion', '57fd2e9fbb786', '', 'enabled', NULL, NULL, 1476210335, 1476210352, 2, NULL, 'product', NULL, NULL, NULL);

CREATE TABLE `product_commission` (
  `product_commission_id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `percentage` double DEFAULT NULL,
  `value` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `product_discount` (
  `product_discount_id` int(11) NOT NULL,
  `qty` float DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `exp_date` date DEFAULT NULL,
  `exp_time` time DEFAULT NULL,
  `exp_timestamp` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `discount_event_id` int(11) DEFAULT NULL,
  `status` enum('enabled','disabled','soldout') DEFAULT NULL,
  `limit` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `product_has_category` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `product_has_category` (`product_id`, `category_id`, `order`) VALUES
(3, 1, NULL),
(3, 2, NULL),
(3, 3, NULL),
(3, 4, NULL),
(4, 1, NULL),
(4, 2, NULL),
(4, 4, NULL),
(5, 1, NULL),
(5, 2, NULL),
(5, 4, NULL),
(6, 5, NULL),
(8, 6, NULL),
(8, 7, NULL);

CREATE TABLE `product_has_plan_feature` (
  `product_id` int(11) NOT NULL,
  `plan_feature_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `product_has_tax_rate` (
  `product_id` int(11) NOT NULL,
  `tax_rate_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `product_has_tax_rate` (`product_id`, `tax_rate_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(8, 1);

CREATE TABLE `product_price` (
  `product_price_id` int(11) NOT NULL,
  `net_price` double DEFAULT NULL,
  `taxes` double DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `exp_timestamp` int(11) DEFAULT NULL,
  `exp_date` date DEFAULT NULL,
  `exp_time` time DEFAULT NULL,
  `update_timestamp` int(11) DEFAULT NULL,
  `status` enum('updated','outdated') DEFAULT 'updated',
  `product_id` int(11) NOT NULL,
  `purchase_price` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `product_price` (`product_price_id`, `net_price`, `taxes`, `date`, `time`, `timestamp`, `exp_timestamp`, `exp_date`, `exp_time`, `update_timestamp`, `status`, `product_id`, `purchase_price`) VALUES
(1, 10, 2.1, '2015-09-03', '18:15:00', 1441304123, -1, NULL, NULL, NULL, 'updated', 1, NULL),
(2, 12, 2.52, '2015-09-03', '18:15:00', 1441304123, -1, NULL, NULL, NULL, 'updated', 2, NULL),
(3, 100, 21, '2015-09-03', '18:15:00', 1441304123, -1, NULL, NULL, NULL, 'updated', 3, NULL),
(4, 600, 126, '2015-09-03', '18:15:00', 1441304123, -1, NULL, NULL, NULL, 'updated', 4, NULL),
(5, 3000, 630, '2015-09-03', '18:15:00', 1441304123, -1, NULL, NULL, NULL, 'updated', 5, NULL),
(6, 1000, 210, '2016-01-07', '18:55:00', 1452192926, -1, NULL, NULL, NULL, 'updated', 6, NULL),
(7, 0, 0, '2015-09-03', '18:15:00', 1441304123, -1, NULL, NULL, NULL, 'updated', 7, NULL),
(8, 300, 63, '2016-10-11', '15:25:00', 1476210335, -1, NULL, NULL, NULL, 'updated', 8, NULL);

CREATE TABLE `product_to_invoice` (
  `product_to_invoice_id` int(11) NOT NULL,
  `contract_detail_id` int(11) DEFAULT NULL,
  `funding_plan_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `period` date DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `status` enum('active','consumed','canceled') DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `discount_id` int(11) DEFAULT NULL,
  `payment_plan_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `profile` (
  `profile_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `value` text,
  `create_timestamp` int(11) DEFAULT NULL,
  `update_timestamp` int(11) DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  `profile_class_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `profile_class` (
  `profile_class_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `data_type` varchar(45) DEFAULT NULL,
  `data_max` int(11) DEFAULT NULL,
  `data_min` varchar(45) DEFAULT NULL,
  `pattern` varchar(45) DEFAULT NULL,
  `status` enum('enabled','disabled') DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `multiple` tinyint(1) DEFAULT NULL,
  `default` varchar(45) DEFAULT NULL,
  `hint` varchar(255) DEFAULT NULL,
  `searchable` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `profile_class` (`profile_class_id`, `name`, `data_type`, `data_max`, `data_min`, `pattern`, `status`, `order`, `multiple`, `default`, `hint`, `searchable`) VALUES
(1, 'Consumidor Final', 'checkbox', NULL, '', '', 'enabled', 1, NULL, NULL, '', 0);

CREATE TABLE `provider` (
  `provider_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `business_name` varchar(255) DEFAULT NULL,
  `tax_identification` varchar(45) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `bill_type` varchar(1) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `phone2` varchar(45) DEFAULT NULL,
  `description` text,
  `account_id` int(11) DEFAULT NULL,
  `tax_condition_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `provider` (`provider_id`, `name`, `business_name`, `tax_identification`, `address`, `bill_type`, `phone`, `phone2`, `description`, `account_id`, `tax_condition_id`) VALUES
(1, 'Vea', 'Vea S.A.', '30-50450954-7', '', 'A', '', '', '', NULL, 1),
(2, 'Ecopago Martinez', '', '12345678', '', 'A', '', '', '', 162, 3),
(3, 'Ecopago Alameda', '', '12345678', '', 'A', '', '', '', 162, 3);

CREATE TABLE `provider_bill` (
  `provider_bill_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `type` varchar(1) DEFAULT NULL,
  `number` varchar(45) DEFAULT NULL,
  `net` double DEFAULT NULL,
  `taxes` double DEFAULT NULL,
  `total` double DEFAULT NULL,
  `provider_id` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `balance` double DEFAULT NULL,
  `bill_type_id` int(11) NOT NULL,
  `status` varchar(45) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `partner_distribution_model_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `creator_user_id` int(11) DEFAULT NULL,
  `updater_user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `provider_bill_has_provider_payment` (
  `provider_bill_id` int(11) NOT NULL,
  `provider_payment_id` int(11) NOT NULL,
  `amount` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `provider_bill_has_tax_rate` (
  `provider_bill_id` int(11) NOT NULL,
  `tax_rate_id` int(11) NOT NULL,
  `amount` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `provider_bill_item` (
  `provider_bill_item_id` int(11) NOT NULL,
  `provider_bill_id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `creator_user_id` int(11) DEFAULT NULL,
  `updater_user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `provider_payment` (
  `provider_payment_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `balance` double DEFAULT NULL,
  `provider_id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `status` enum('created','closed','conciled') NOT NULL DEFAULT 'created',
  `partner_distribution_model_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `provider_payment_item` (
  `provider_payment_item_id` int(11) NOT NULL,
  `provider_payment_id` int(11) NOT NULL,
  `description` varchar(255) DEFAULT '',
  `number` varchar(45) DEFAULT '',
  `amount` double DEFAULT NULL,
  `payment_method_id` int(11) DEFAULT NULL,
  `paycheck_id` int(11) DEFAULT NULL,
  `money_box_account_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `resume` (
  `resume_id` int(11) NOT NULL,
  `money_box_account_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `date` date DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `status` enum('draft','closed','canceled','conciled') DEFAULT NULL,
  `balance_initial` double DEFAULT NULL,
  `balance_final` double DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `resume_item` (
  `resume_item_id` int(11) NOT NULL,
  `resume_id` int(11) DEFAULT NULL,
  `description` varchar(150) DEFAULT NULL,
  `reference` varchar(45) DEFAULT NULL,
  `code` varchar(45) DEFAULT NULL,
  `debit` double DEFAULT NULL,
  `credit` double DEFAULT NULL,
  `status` enum('draft','closed','conciled') DEFAULT NULL,
  `date` date DEFAULT NULL,
  `money_box_has_operation_type_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `server` (
  `server_id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `status` enum('enabled','disabled') DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `user` varchar(255) DEFAULT '',
  `password` varchar(255) DEFAULT '',
  `class` varchar(255) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `server` (`server_id`, `name`, `status`, `url`, `token`, `user`, `password`, `class`) VALUES
(1, 'Principal', 'enabled', 'https://190.15.200.10:8443/', '', '', '', ''),
(2, 'Respaldo', 'enabled', NULL, NULL, '', '', '');

CREATE TABLE `stock_movement` (
  `stock_movement_id` int(11) NOT NULL,
  `type` enum('in','out','r_in','r_out') DEFAULT NULL,
  `concept` varchar(255) DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `stock` double DEFAULT NULL,
  `avaible_stock` double DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `bill_detail_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `expiration` date DEFAULT NULL,
  `expiration_timestamp` int(11) DEFAULT NULL,
  `secondary_qty` double DEFAULT NULL,
  `secondary_stock` double DEFAULT NULL,
  `secondary_avaible_stock` double DEFAULT NULL,
  `balance` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tax` (
  `tax_id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `slug` varchar(45) DEFAULT NULL,
  `required` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `tax` (`tax_id`, `name`, `slug`, `required`) VALUES
(1, 'IVA', 'iva', 0),
(2, 'Ingresos Brutos', 'ingresos-brutos', 0),
(3, 'Cptos no Grav.', 'cptos-no-grav', 0),
(4, 'Percep. IVA', 'percep-iva', 0),
(5, 'Percep. Ing. B.', 'percep-ing-b', 0),
(6, 'Retenc. IVA', 'retenc-iva', 0),
(7, 'Retenc. Ing. B.', 'retenc-ing-b', 0),
(8, 'Retenc. Gan.', 'retenc-gan', 0),
(9, 'IVA Otros', 'iva-otros', 0);

CREATE TABLE `taxes_book` (
  `taxes_book_id` int(11) NOT NULL,
  `type` enum('sale','buy') DEFAULT NULL,
  `status` enum('draft','closed') DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `number` varchar(20) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `period` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `taxes_book_item` (
  `taxes_book_item_id` int(11) NOT NULL,
  `page` int(11) NOT NULL,
  `taxes_book_id` int(11) NOT NULL,
  `bill_id` int(11) DEFAULT NULL,
  `provider_bill_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tax_condition` (
  `tax_condition_id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `exempt` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `tax_condition` (`tax_condition_id`, `name`, `exempt`) VALUES
(1, 'IVA Inscripto', 0),
(2, 'IVA No inscripto', 0),
(3, 'Consumidor Final', 0),
(4, 'Exento', 1),
(5, 'Monotributista', 0);

CREATE TABLE `tax_condition_has_bill_type` (
  `tax_condition_id` int(11) NOT NULL,
  `bill_type_id` int(11) NOT NULL,
  `order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `tax_condition_has_bill_type` (`tax_condition_id`, `bill_type_id`, `order`) VALUES
(1, 1, NULL),
(1, 4, NULL),
(1, 7, NULL),
(1, 10, NULL),
(1, 11, NULL),
(1, 12, NULL),
(2, 2, NULL),
(2, 5, NULL),
(2, 8, NULL),
(2, 10, NULL),
(2, 11, NULL),
(2, 12, NULL),
(3, 2, NULL),
(3, 5, NULL),
(3, 8, NULL),
(3, 10, NULL),
(3, 11, NULL),
(3, 12, NULL),
(4, 2, NULL),
(4, 5, NULL),
(4, 8, NULL),
(4, 10, NULL),
(4, 11, NULL),
(4, 12, NULL),
(5, 2, NULL),
(5, 5, NULL),
(5, 8, NULL),
(5, 10, NULL),
(5, 11, NULL),
(5, 12, NULL);

CREATE TABLE `tax_condition_has_bill_type_buy` (
  `tax_condition_id` int(11) NOT NULL,
  `bill_type_id` int(11) NOT NULL,
  `order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `tax_condition_has_bill_type_buy` (`tax_condition_id`, `bill_type_id`, `order`) VALUES
(1, 1, NULL),
(1, 4, NULL),
(1, 7, NULL),
(1, 10, NULL),
(1, 11, NULL),
(1, 12, NULL),
(2, 3, NULL),
(2, 6, NULL),
(2, 9, NULL),
(2, 10, NULL),
(2, 11, NULL),
(2, 12, NULL),
(4, 3, NULL),
(4, 6, NULL),
(4, 9, NULL),
(4, 10, NULL),
(4, 11, NULL),
(4, 12, NULL),
(5, 3, NULL),
(5, 6, NULL),
(5, 9, NULL),
(5, 10, NULL),
(5, 11, NULL),
(5, 12, NULL);

CREATE TABLE `tax_condition_has_document_type` (
  `tax_condition_id` int(11) NOT NULL,
  `document_type_document_type_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `tax_condition_has_document_type` (`tax_condition_id`, `document_type_document_type_id`) VALUES
(1, 1),
(2, 1),
(3, 2),
(4, 1),
(5, 1);

CREATE TABLE `tax_rate` (
  `tax_rate_id` int(11) NOT NULL,
  `pct` double DEFAULT NULL,
  `tax_id` int(11) NOT NULL,
  `code` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `tax_rate` (`tax_rate_id`, `pct`, `tax_id`, `code`) VALUES
(1, 0.21, 1, 5),
(2, 0.105, 1, 4),
(3, 0.27, 1, 6),
(4, 0.06, 2, NULL),
(5, 0, 4, NULL),
(6, 0, 3, NULL),
(8, 0, 5, NULL),
(9, 0, 6, NULL),
(10, 0, 7, NULL),
(11, 0, 8, NULL),
(12, 0, 9, NULL),
(13, 0, 1, 3),
(14, 0.05, 1, 8),
(15, 0.025, 1, 9);

CREATE TABLE `unit` (
  `unit_id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `type` enum('int','float') DEFAULT NULL,
  `symbol` varchar(10) DEFAULT NULL,
  `symbol_position` enum('prefix','suffix') DEFAULT NULL,
  `code` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `unit` (`unit_id`, `name`, `type`, `symbol`, `symbol_position`, `code`) VALUES
(1, 'Kilogramos', 'float', 'kg', 'suffix', 0),
(2, 'Unidades', 'int', 'u', 'suffix', 0);

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `auth_key` varchar(32) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `confirmation_token` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `superadmin` smallint(6) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `registration_ip` varchar(15) DEFAULT NULL,
  `bind_to_ip` varchar(255) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `email_confirmed` smallint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `confirmation_token`, `status`, `superadmin`, `created_at`, `updated_at`, `registration_ip`, `bind_to_ip`, `email`, `email_confirmed`) VALUES
(1, 'superadmin', 'Q8RhDrp0Plst13_fYVp4oI6X_i-Gjc0a', '$2y$13$79neLlgzDm/lVXQ3.3b8uu0Bg5qe.0DrPaOsOMPbttyUsQYKSniLG', NULL, 1, 1, 1441734311, 1441734734, NULL, '', NULL, 0),
(2, 'user', 'TW7hMEM7ISPhi4MNVF94_c0v32E-8OVR', '$2y$13$F3hzGNdz0p6iFklr1rBZ3O6y9yj6ROv0.SIeGyzrlQ6SFWynajxM2', NULL, 1, 0, 1442926357, 1457029597, '127.0.0.1', '', 'user@fake.com', 1),
(3, 'admin', 'EPIqFHU7Ts3Vb2sFZDj2ZfJ6GQypX_4P', '$2y$13$J3qO/vUFinMVVMS35.i5yeiMJbSa5Zj3DowL3SpqZn94N25osb1fK', NULL, 1, 0, 1457013586, 1457013586, '127.0.0.1', '', '', 1),
(4, 'diego', 'p7rc3nSPuoMMECn6KpYV337ZOMFLHsqw', '$2y$13$AxelVl3ltJofy8vILngp/eniRJnxRiORhDFKEBmjtO5869qtxifOa', NULL, 1, 0, 1449859420, 1449859420, '127.0.0.1', NULL, NULL, 0),
(5, 'roleless_user', '9_MqqyiV3ayIamgsDjjoNALigscbhU8s', '$2y$13$rYqDyV9sJJOgJOFJXMJ4QOSgMjq.Z/m0vvv/CmUL.Hkjp7vpGeUh.', NULL, 1, 0, 1459531568, 1459531568, '::1', '', '', 1),
(6, 'vendor', 'lh46YCEeZ9CkUUca4sChhoU-34n6moaI', '$2y$13$DUH2U0g5HfWnejQxEUfY1.d1VGw9BlgMPZu9yQD8aBE3M7VwG//ey', NULL, 1, 0, 1470839845, 1470839845, '172.17.0.1', '', 'vendor@vendor.com', 0);

CREATE TABLE `user_app` (
  `user_app_id` int(11) NOT NULL,
  `email` varchar(45) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `status` enum('pending','active','disable') NOT NULL DEFAULT 'pending',
  `player_id` varchar(255) DEFAULT NULL,
  `document_number` varchar(45) DEFAULT NULL,
  `destinatary` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_app_has_customer` (
  `user_app_has_customer_id` int(11) NOT NULL,
  `user_app_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `customer_code` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_visit_log` (
  `id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `language` char(2) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `visit_time` int(11) NOT NULL,
  `browser` varchar(30) DEFAULT NULL,
  `os` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `validation_code` (
  `validation_code_id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `expire_timestamp` int(11) NOT NULL,
  `user_app_has_customer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `vendor` (
  `vendor_id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `lastname` varchar(45) DEFAULT NULL,
  `document_type_id` int(11) DEFAULT NULL,
  `document_number` varchar(45) DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `vendor_commission_id` int(11) NOT NULL,
  `external_user_id` int(11) NOT NULL,
  `provider_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `vendor` (`vendor_id`, `name`, `lastname`, `document_type_id`, `document_number`, `sex`, `address_id`, `account_id`, `phone`, `user_id`, `vendor_commission_id`, `external_user_id`, `provider_id`) VALUES
(1, 'Vendor', 'V', 2, '36999999', NULL, 6, 202, '', 6, 1, 0, NULL);

CREATE TABLE `vendor_commission` (
  `vendor_commission_id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `percentage` double DEFAULT NULL,
  `value` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `vendor_commission` (`vendor_commission_id`, `name`, `percentage`, `value`) VALUES
(1, 'Entry', 1, NULL),
(2, 'Basic', 5, NULL),
(3, 'VIV', 6, NULL);

CREATE TABLE `vendor_liquidation` (
  `vendor_liquidation_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `period` date DEFAULT NULL,
  `status` enum('draft','payed','cancelled','billed') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `vendor_liquidation_item` (
  `vendor_liquidation_item_id` int(11) NOT NULL,
  `vendor_liquidation_id` int(11) NOT NULL,
  `bill_id` int(11) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `contract_detail_id` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `zone` (
  `zone_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `create_timestamp` int(11) DEFAULT NULL,
  `update_timestamp` int(11) DEFAULT NULL,
  `status` enum('enabled','disabled') DEFAULT NULL,
  `system` varchar(100) DEFAULT NULL,
  `type` varchar(60) DEFAULT NULL,
  `postal_code` int(11) DEFAULT NULL,
  `lft` int(11) DEFAULT NULL,
  `rgt` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `zone` (`zone_id`, `name`, `parent_id`, `create_timestamp`, `update_timestamp`, `status`, `system`, `type`, `postal_code`, `lft`, `rgt`) VALUES
(1, 'Mendoza', NULL, 1441982156, NULL, 'enabled', 'mendoza', 'locality', 5500, 1, 6),
(2, 'Centro', 1, 1441982156, NULL, 'enabled', 'centro', 'zone', 5500, 2, 3),
(3, '4ta sección', 1, 1441982156, NULL, 'enabled', '4ta-seccion', 'zone', 5500, 4, 5),
(4, 'San Juan', NULL, 1463755618, NULL, 'enabled', 'san-juan', 'state', 3344, 7, 8);


ALTER TABLE `account`
  ADD PRIMARY KEY (`account_id`),
  ADD KEY `fk_account_account1_idx` (`parent_account_id`);

ALTER TABLE `accounting_period`
  ADD PRIMARY KEY (`accounting_period_id`);

ALTER TABLE `account_config`
  ADD PRIMARY KEY (`account_config_id`);

ALTER TABLE `account_config_has_account`
  ADD PRIMARY KEY (`account_config_has_account_id`),
  ADD KEY `fk_account_config_has_account_account1_idx` (`account_id`),
  ADD KEY `fk_account_config_has_account_account_config1_idx` (`account_config_id`);

ALTER TABLE `account_movement`
  ADD PRIMARY KEY (`account_movement_id`),
  ADD KEY `fk_account_movement_accounting_period1_idx` (`accounting_period_id`),
  ADD KEY `ix_account_movement_company_id` (`company_id`),
  ADD KEY `fk_account_movement_partner_distribution_model1_idx` (`partner_distribution_model_id`),
  ADD KEY `fk_account_movement_id` (`daily_money_box_account_id`);

ALTER TABLE `account_movement_item`
  ADD PRIMARY KEY (`account_movement_item_id`),
  ADD KEY `fk_account_movement_item_account1_idx` (`account_id`),
  ADD KEY `fk_account_movement_item_account_movement1_idx` (`account_movement_id`);

ALTER TABLE `account_movement_relation`
  ADD PRIMARY KEY (`account_movement_relation_id`),
  ADD KEY `ix_account_movement_relation_id` (`class`,`model_id`),
  ADD KEY `fk_account_movement_relation_account_movement1_idx` (`account_movement_id`);

ALTER TABLE `address`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `fk_address_zone_idx` (`zone_id`);

ALTER TABLE `app_failed_register`
  ADD PRIMARY KEY (`app_failed_register_id`);

ALTER TABLE `auth_assignment`
  ADD PRIMARY KEY (`item_name`,`user_id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `auth_item`
  ADD PRIMARY KEY (`name`),
  ADD KEY `rule_name` (`rule_name`),
  ADD KEY `idx-auth_item-type` (`type`),
  ADD KEY `fk_auth_item_group_code` (`group_code`);

ALTER TABLE `auth_item_child`
  ADD PRIMARY KEY (`parent`,`child`),
  ADD KEY `child` (`child`);

ALTER TABLE `auth_item_group`
  ADD PRIMARY KEY (`code`);

ALTER TABLE `auth_rule`
  ADD PRIMARY KEY (`name`);

ALTER TABLE `auth_token`
  ADD PRIMARY KEY (`auth_token_id`),
  ADD KEY `fk_auth_token_user_app1_idx` (`user_app_id`);

ALTER TABLE `bill`
  ADD PRIMARY KEY (`bill_id`),
  ADD KEY `fk_bill_customer1_idx` (`customer_id`),
  ADD KEY `fk_bill_currency1_idx` (`currency_id`),
  ADD KEY `fk_bill_bill_type1_idx` (`bill_type_id`),
  ADD KEY `ix_bill_company_id` (`company_id`),
  ADD KEY `ix_bill_footprint` (`footprint`),
  ADD KEY `ix_user_id` (`user_id`),
  ADD KEY `fk_bill_partner_distribution_model1_idx` (`partner_distribution_model_id`),
  ADD KEY `ix_bill_type_status_client` (`bill_type_id`,`status`,`company_id`),
  ADD KEY `index_date` (`date`),
  ADD KEY `index_timestamp` (`timestamp`),
  ADD KEY `idx_bill_number` (`number`),
  ADD KEY `idx_bill_type_status_client` (`bill_type_id`,`status`,`company_id`),
  ADD KEY `idx_bill_date` (`date`),
  ADD KEY `idx_bill_timestamp` (`timestamp`);

ALTER TABLE `bill_detail`
  ADD PRIMARY KEY (`bill_detail_id`),
  ADD KEY `fk_bill_detail_bill1_idx` (`bill_id`),
  ADD KEY `fk_bill_detail_product1_idx` (`product_id`),
  ADD KEY `fk_bill_detail_discount1_idx` (`discount_id`),
  ADD KEY `fk_bill_detail_unit1_idx` (`unit_id`);

ALTER TABLE `bill_has_payment`
  ADD PRIMARY KEY (`bill_has_payment_id`),
  ADD KEY `fk_bill_has_payment_bill1_idx` (`bill_id`),
  ADD KEY `fk_bill_has_payment_payment1_idx` (`payment_id`);

ALTER TABLE `bill_type`
  ADD PRIMARY KEY (`bill_type_id`),
  ADD KEY `fk_bill_type_invoice_class1_idx` (`invoice_class_id`);

ALTER TABLE `bill_type_has_bill_type`
  ADD PRIMARY KEY (`parent_id`,`child_id`),
  ADD KEY `fk_bill_type_has_bill_type_bill_type2_idx` (`child_id`),
  ADD KEY `fk_bill_type_has_bill_type_bill_type1_idx` (`parent_id`);

ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`),
  ADD KEY `fk_category_category1_idx` (`parent_id`);

ALTER TABLE `checkbook`
  ADD PRIMARY KEY (`checkbook_id`),
  ADD KEY `fk_checkbook_money_box_account1_idx_idx` (`money_box_account_id`);

ALTER TABLE `company`
  ADD PRIMARY KEY (`company_id`),
  ADD KEY `fk_company_company1_idx` (`parent_id`),
  ADD KEY `fk_company_tax_condition1_idx` (`tax_condition_id`),
  ADD KEY `fk_company_partner_distribution_model1_idx` (`partner_distribution_model_id`);

ALTER TABLE `company_has_billing`
  ADD PRIMARY KEY (`company_has_billing_id`),
  ADD KEY `company_has_billing_company_company_id_fk` (`parent_company_id`),
  ADD KEY `company_has_billing_company_company_id_fk_2` (`company_id`),
  ADD KEY `company_has_billing_bill_type_bill_type_id_fk` (`bill_type_id`);

ALTER TABLE `company_has_bill_type`
  ADD PRIMARY KEY (`company_id`,`bill_type_id`),
  ADD KEY `fk_company_has_bill_type_bill_type1_idx` (`bill_type_id`),
  ADD KEY `fk_company_has_bill_type_company1_idx` (`company_id`);

ALTER TABLE `conciliation`
  ADD PRIMARY KEY (`conciliation_id`),
  ADD KEY `fk_conciliation_money_box_account1_idx` (`money_box_account_id`),
  ADD KEY `fk_conciliation_resume1_idx` (`resume_id`),
  ADD KEY `ix_conciliation_company_id` (`company_id`);

ALTER TABLE `conciliation_item`
  ADD PRIMARY KEY (`conciliation_item_id`),
  ADD KEY `fk_conciliation_item_conciliation1_idx` (`conciliation_id`);

ALTER TABLE `conciliation_item_has_account_movement_item`
  ADD PRIMARY KEY (`account_movement_item_id`,`conciliation_item_id`),
  ADD KEY `fk_conciliation_item_has_account_movement_item_conciliation_idx` (`conciliation_item_id`);

ALTER TABLE `conciliation_item_has_resume_item`
  ADD PRIMARY KEY (`conciliation_item_id`,`resume_item_id`),
  ADD KEY `fk_conciliation_item_has_resume_item_resume_item1_idx` (`resume_item_id`);

ALTER TABLE `connection`
  ADD PRIMARY KEY (`connection_id`),
  ADD KEY `fk_connection_contract1_idx` (`contract_id`),
  ADD KEY `fk_connection_node1_idx` (`node_id`),
  ADD KEY `fk_connection_server1_idx` (`server_id`),
  ADD KEY `ix_connection_company_idx` (`company_id`);

ALTER TABLE `connection_forced_historial`
  ADD PRIMARY KEY (`connection_forced_historial_id`),
  ADD KEY `fk_connection_forced_historial_connection1_idx` (`connection_id`),
  ADD KEY `fk_connection_forced_historial_user` (`user_id`);

ALTER TABLE `contract`
  ADD PRIMARY KEY (`contract_id`),
  ADD KEY `fk_contract_customer_idx` (`customer_id`),
  ADD KEY `fk_contract_address_idx` (`address_id`),
  ADD KEY `fk_contract_vendor1_idx` (`vendor_id`);

ALTER TABLE `contract_detail`
  ADD PRIMARY KEY (`contract_detail_id`),
  ADD KEY `fk_contract_detail_product_idx` (`product_id`),
  ADD KEY `fk_contract_detail_funding_plan1_idx` (`funding_plan_id`),
  ADD KEY `fk_contract_detail_contract` (`contract_id`),
  ADD KEY `fk_contract_detail_discount1_idx` (`discount_id`),
  ADD KEY `fk_contract_detail_vendor1_idx` (`vendor_id`);

ALTER TABLE `contract_detail_log`
  ADD PRIMARY KEY (`contract_detail_log_id`),
  ADD KEY `fk_contract_detail_log_contract_detail1_idx` (`contract_detail_id`),
  ADD KEY `fk_contract_detail_log_product1_idx` (`product_id`),
  ADD KEY `fk_contract_detail_log_funding_plan1_idx` (`funding_plan_id`),
  ADD KEY `fk_contract_detail_log_discount1_idx` (`discount_id`);

ALTER TABLE `contract_log`
  ADD PRIMARY KEY (`contract_log_id`),
  ADD KEY `fk_contract_log_contract1_idx` (`contract_id`);

ALTER TABLE `contract_status`
  ADD PRIMARY KEY (`contract_status_id`);

ALTER TABLE `currency`
  ADD PRIMARY KEY (`currency_id`);

ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `code_UNIQUE` (`code`),
  ADD UNIQUE KEY `payment_code_UNIQUE` (`payment_code`),
  ADD KEY `fk_customer_document_type1_idx` (`document_type_id`),
  ADD KEY `fk_customer_tax_condition_idx` (`tax_condition_id`),
  ADD KEY `fk_customer_account1_idx` (`account_id`),
  ADD KEY `ix_customer_company_id` (`company_id`),
  ADD KEY `fk_customer_address_idx` (`address_id`),
  ADD KEY `fk_customer_customer1_idx` (`customer_reference_id`),
  ADD KEY `ix_customer_parent_customer_id` (`parent_company_id`);

ALTER TABLE `customer_category`
  ADD PRIMARY KEY (`customer_category_id`),
  ADD KEY `fk_customer_category_1_idx` (`parent_id`);

ALTER TABLE `customer_category_has_customer`
  ADD PRIMARY KEY (`customer_category_id`,`customer_id`,`date_updated`),
  ADD KEY `fk_customer_category2_has_customer_1_idx` (`customer_id`);

ALTER TABLE `customer_class`
  ADD PRIMARY KEY (`customer_class_id`);

ALTER TABLE `customer_class_has_customer`
  ADD PRIMARY KEY (`customer_class_id`,`customer_id`,`date_updated`),
  ADD KEY `fk_customer_category_has_customer_1_idx` (`customer_id`);

ALTER TABLE `customer_has_discount`
  ADD PRIMARY KEY (`cutomer_has_discount_id`),
  ADD KEY `fk_customer_has_discount_discount1_idx` (`discount_id`),
  ADD KEY `fk_customer_has_discount_customer1_idx` (`customer_id`);

ALTER TABLE `customer_log`
  ADD PRIMARY KEY (`customer_log_id`),
  ADD KEY `fk_customer_log_customer1_idx` (`customer_id`);

ALTER TABLE `debt_evolution`
  ADD PRIMARY KEY (`debt_evolution_id`);

ALTER TABLE `discount`
  ADD PRIMARY KEY (`discount_id`),
  ADD KEY `fk_discount_product1_idx` (`product_id`);

ALTER TABLE `discount_event`
  ADD PRIMARY KEY (`discount_event_id`);

ALTER TABLE `document_type`
  ADD PRIMARY KEY (`document_type_id`);

ALTER TABLE `email_transport`
  ADD PRIMARY KEY (`email_transport_id`),
  ADD UNIQUE KEY `email_transport_name_uindex` (`name`);

ALTER TABLE `empty_ads`
  ADD PRIMARY KEY (`empty_ads_id`),
  ADD UNIQUE KEY `code_UNIQUE` (`code`);

ALTER TABLE `funding_plan`
  ADD PRIMARY KEY (`funding_plan_id`),
  ADD KEY `fk_funding_plan_product1_idx` (`product_id`);

ALTER TABLE `invoice_class`
  ADD PRIMARY KEY (`invoice_class_id`);

ALTER TABLE `ip_range`
  ADD PRIMARY KEY (`ip_range_id`),
  ADD KEY `fk_rank_ip_1_idx` (`node_id`);

ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

ALTER TABLE `mobile_push`
  ADD PRIMARY KEY (`mobile_push_id`);

ALTER TABLE `mobile_push_has_user_app`
  ADD PRIMARY KEY (`mobile_push_has_user_app_id`),
  ADD KEY `fk_mobile_push_has_user_app_user_app2_idx` (`user_app_id`),
  ADD KEY `fk_mobile_push_has_user_app_mobile_push2_idx` (`mobile_push_id`);

ALTER TABLE `money_box`
  ADD PRIMARY KEY (`money_box_id`),
  ADD KEY `fk_money_box_money_box_type1_idx` (`money_box_type_id`),
  ADD KEY `fk_money_box_account1_idx` (`account_id`);

ALTER TABLE `money_box_account`
  ADD PRIMARY KEY (`money_box_account_id`),
  ADD KEY `fk_bank_account_bank1_idx` (`money_box_id`),
  ADD KEY `ix_money_box_account_company_id` (`company_id`),
  ADD KEY `fk_money_box_account_account1_idx` (`account_id`),
  ADD KEY `fk_money_box_account_currency1_idx` (`currency_id`);

ALTER TABLE `money_box_has_operation_type`
  ADD PRIMARY KEY (`money_box_has_operation_type_id`),
  ADD UNIQUE KEY `ix_money_box_id_operation_type_id_UNIQUE` (`operation_type_id`,`money_box_id`,`account_id`),
  ADD KEY `fk_money_box_has_operation_type_operation_type1_idx` (`operation_type_id`),
  ADD KEY `fk_money_box_has_operation_type_money_box1_idx` (`money_box_id`),
  ADD KEY `fk_money_box_has_operation_type_account1_idx` (`account_id`),
  ADD KEY `fk_money_box_has_operation_type_money_box_account1_idx` (`money_box_account_id`);

ALTER TABLE `money_box_type`
  ADD PRIMARY KEY (`money_box_type_id`);

ALTER TABLE `node`
  ADD PRIMARY KEY (`node_id`),
  ADD UNIQUE KEY `subnet_UNIQUE` (`subnet`),
  ADD KEY `fk_node_1_idx` (`zone_id`),
  ADD KEY `fk_node_2_idx` (`company_id`),
  ADD KEY `fk_node_server1_idx` (`server_id`),
  ADD KEY `fk_node_node1_idx` (`parent_node_id`);

ALTER TABLE `node_has_ecopago`
  ADD PRIMARY KEY (`ecopago_id`,`node_id`),
  ADD KEY `fk_node_has_ecopago_node1_idx` (`node_id`);

ALTER TABLE `operation_type`
  ADD PRIMARY KEY (`operation_type_id`);

ALTER TABLE `pagomiscuentas_file`
  ADD PRIMARY KEY (`pagomiscuentas_file_id`),
  ADD KEY `pagomiscuentas_file_company_company_id_fk` (`company_id`);

ALTER TABLE `pagomiscuentas_file_has_bill`
  ADD PRIMARY KEY (`pagomiscuentas_file_has_bill_id`),
  ADD KEY `pagomiscuentas_file_has_bill_fk` (`bill_id`);

ALTER TABLE `pagomiscuentas_file_has_payment`
  ADD PRIMARY KEY (`pagomiscuentas_file_has_payment_id`),
  ADD KEY `pagomiscuentas_file_has_payment_fk` (`payment_id`);

ALTER TABLE `pago_facil_payment`
  ADD PRIMARY KEY (`pago_facil_payment_id`),
  ADD KEY `fk_pago_facil_payment_pago_facil_transmition_file1_idx` (`pago_facil_transmition_file_pago_facil_transmition_file_id`),
  ADD KEY `fk_pago_facil_payment_payment1_idx` (`payment_payment_id`);

ALTER TABLE `pago_facil_transmition_file`
  ADD PRIMARY KEY (`pago_facil_transmition_file_id`);

ALTER TABLE `partner`
  ADD PRIMARY KEY (`partner_id`),
  ADD KEY `fk_partner_account1_idx` (`account_id`);

ALTER TABLE `partner_distribution_model`
  ADD PRIMARY KEY (`partner_distribution_model_id`),
  ADD KEY `fk_partner_distribution_model_company1_idx` (`company_id`);

ALTER TABLE `partner_distribution_model_has_partner`
  ADD PRIMARY KEY (`partner_distribution_model_has_partner_id`),
  ADD KEY `fk_partner_has_company_partner1_idx` (`partner_id`),
  ADD KEY `fk_partner_has_company_partner_distribution_model1_idx` (`partner_distribution_model_id`);

ALTER TABLE `partner_liquidation`
  ADD PRIMARY KEY (`partner_liquidation_id`),
  ADD KEY `fk_partner_liquidation_partner_distribution_model_has_partn_idx` (`partner_distribution_model_has_partner_id`);

ALTER TABLE `partner_liquidation_movement`
  ADD PRIMARY KEY (`partner_liquidation_movement_id`),
  ADD KEY `partner_liquidation_id` (`partner_liquidation_id`),
  ADD KEY `ix_partner_liquidation_movement_index` (`class`,`model_id`,`type`);

ALTER TABLE `paycheck`
  ADD PRIMARY KEY (`paycheck_id`),
  ADD KEY `fk_paycheck_checkbook1_idx` (`checkbook_id`),
  ADD KEY `fk_paycheck_money_box1_idx` (`money_box_id`),
  ADD KEY `fk_paycheck_money_box_account1_idx` (`money_box_account_id`);

ALTER TABLE `paycheck_log`
  ADD PRIMARY KEY (`paycheck_log_id`),
  ADD KEY `fk_paycheck_log_paycheck1_idx` (`paycheck_id`),
  ADD KEY `fk_paycheck_log_money_box_account1_idx` (`money_box_account_id`);

ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `ix_payment_comany_id` (`company_id`),
  ADD KEY `fk_payment_customer1_idx` (`customer_id`),
  ADD KEY `fk_payment_partner_distribution_model1_idx` (`partner_distribution_model_id`);

ALTER TABLE `payment_item`
  ADD PRIMARY KEY (`payment_item_id`),
  ADD KEY `fk_payment_item_payment1_idx` (`payment_id`),
  ADD KEY `fk_payment_item_payment_method1_idx` (`payment_method_id`),
  ADD KEY `fk_payment_item_paycheck1_idx` (`paycheck_id`),
  ADD KEY `fk_payment_item_money_box_account1_idx` (`money_box_account_id`);

ALTER TABLE `payment_method`
  ADD PRIMARY KEY (`payment_method_id`);

ALTER TABLE `payment_plan`
  ADD PRIMARY KEY (`payment_plan_id`),
  ADD KEY `fk_payment_plan_customer1_idx` (`customer_id`);

ALTER TABLE `plan_feature`
  ADD PRIMARY KEY (`plan_feature_id`),
  ADD KEY `fk_plan_feature_1_idx` (`parent_id`);

ALTER TABLE `point_of_sale`
  ADD PRIMARY KEY (`point_of_sale_id`),
  ADD KEY `ix_point_of_sale_company_id` (`company_id`);

ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `fk_product_unit1_idx` (`unit_id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `fk_product_account1_idx` (`account_id`),
  ADD KEY `fk_product_unit2_idx` (`secondary_unit_id`),
  ADD KEY `fk_product_product_commission1_idx` (`product_commission_id`);

ALTER TABLE `product_commission`
  ADD PRIMARY KEY (`product_commission_id`);

ALTER TABLE `product_discount`
  ADD PRIMARY KEY (`product_discount_id`),
  ADD KEY `fk_product_discount_product1_idx` (`product_id`),
  ADD KEY `fk_product_discount_discount_event1_idx` (`discount_event_id`);

ALTER TABLE `product_has_category`
  ADD PRIMARY KEY (`product_id`,`category_id`),
  ADD KEY `fk_product_has_category_category1_idx` (`category_id`),
  ADD KEY `fk_product_has_category_product_idx` (`product_id`);

ALTER TABLE `product_has_plan_feature`
  ADD PRIMARY KEY (`product_id`,`plan_feature_id`),
  ADD KEY `fk_product_has_plan_feature_plan_feature_idx` (`plan_feature_id`),
  ADD KEY `fk_product_has_plan_feature_product_idx` (`product_id`);

ALTER TABLE `product_has_tax_rate`
  ADD PRIMARY KEY (`product_id`,`tax_rate_id`),
  ADD KEY `fk_product_has_tax_rate_tax_rate1_idx` (`tax_rate_id`),
  ADD KEY `fk_product_has_tax_rate_product1_idx` (`product_id`);

ALTER TABLE `product_price`
  ADD PRIMARY KEY (`product_price_id`),
  ADD KEY `fk_product_price_product1_idx` (`product_id`);

ALTER TABLE `product_to_invoice`
  ADD PRIMARY KEY (`product_to_invoice_id`),
  ADD KEY `fk_product_to_invoice_contract_detail1_idx` (`contract_detail_id`),
  ADD KEY `fk_product_to_invoice_funding_plan1_idx` (`funding_plan_id`),
  ADD KEY `fk_product_to_invoice_discount1_idx` (`discount_id`),
  ADD KEY `fk_product_to_invoice_payment_plan1_idx` (`payment_plan_id`),
  ADD KEY `fk_product_to_invoice_customer1_idx` (`customer_id`);

ALTER TABLE `profile`
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `fk_profile_customer1_idx` (`customer_id`),
  ADD KEY `fk_profile_profile_class1_idx` (`profile_class_id`);

ALTER TABLE `profile_class`
  ADD PRIMARY KEY (`profile_class_id`);

ALTER TABLE `provider`
  ADD PRIMARY KEY (`provider_id`),
  ADD KEY `fk_provider_account1_idx` (`account_id`),
  ADD KEY `fk_provider_tax_condition1_idx` (`tax_condition_id`);

ALTER TABLE `provider_bill`
  ADD PRIMARY KEY (`provider_bill_id`),
  ADD KEY `fk_provider_bill_provider1_idx` (`provider_id`),
  ADD KEY `fk_provider_bill_bill_type1_idx` (`bill_type_id`),
  ADD KEY `ix_provider_bill_company_id` (`company_id`),
  ADD KEY `fk_provider_bill_partner_distribution_model1_idx` (`partner_distribution_model_id`);

ALTER TABLE `provider_bill_has_provider_payment`
  ADD PRIMARY KEY (`provider_bill_id`,`provider_payment_id`),
  ADD KEY `fk_provider_bill_has_provider_payment_provider_payment1_idx` (`provider_payment_id`),
  ADD KEY `fk_provider_bill_has_provider_payment_provider_bill1_idx` (`provider_bill_id`);

ALTER TABLE `provider_bill_has_tax_rate`
  ADD PRIMARY KEY (`provider_bill_id`,`tax_rate_id`),
  ADD KEY `fk_provider_bill_has_tax_rate_tax_rate1_idx` (`tax_rate_id`),
  ADD KEY `fk_provider_bill_has_tax_rate_provider_bill1_idx` (`provider_bill_id`);

ALTER TABLE `provider_bill_item`
  ADD PRIMARY KEY (`provider_bill_item_id`),
  ADD KEY `fk_provider_bill_item_provider_bill1_idx` (`provider_bill_id`),
  ADD KEY `fk_provider_bill_item_account1_idx` (`account_id`);

ALTER TABLE `provider_payment`
  ADD PRIMARY KEY (`provider_payment_id`),
  ADD KEY `fk_provider_payment_provider1_idx` (`provider_id`),
  ADD KEY `ix_provider_payment_company_id` (`company_id`),
  ADD KEY `fk_provider_payment_partner_distribution_model1_idx` (`partner_distribution_model_id`);

ALTER TABLE `provider_payment_item`
  ADD PRIMARY KEY (`provider_payment_item_id`),
  ADD KEY `provider_payment_item_provider_payment_provider_payment_id_fk` (`provider_payment_id`),
  ADD KEY `provider_payment_item_payment_method_payment_method_id_fk` (`payment_method_id`),
  ADD KEY `provider_payment_item_paycheck_paycheck_id_fk` (`paycheck_id`),
  ADD KEY `provider_payment_item_money_box_account_money_box_account_id_fk` (`money_box_account_id`);

ALTER TABLE `resume`
  ADD PRIMARY KEY (`resume_id`),
  ADD KEY `fk_resume_money_box_account1_idx` (`money_box_account_id`),
  ADD KEY `ix_resume_company_id` (`company_id`);

ALTER TABLE `resume_item`
  ADD PRIMARY KEY (`resume_item_id`),
  ADD KEY `fk_resume_item_resume1_idx` (`resume_id`),
  ADD KEY `fk_resume_item_money_box_has_operation_type1_idx` (`money_box_has_operation_type_id`);

ALTER TABLE `server`
  ADD PRIMARY KEY (`server_id`);

ALTER TABLE `stock_movement`
  ADD PRIMARY KEY (`stock_movement_id`),
  ADD KEY `fk_stock_movement_product1_idx` (`product_id`),
  ADD KEY `fk_stock_movement_bill_detail1_idx` (`bill_detail_id`),
  ADD KEY `ix_stock_movement_company_id` (`company_id`);

ALTER TABLE `tax`
  ADD PRIMARY KEY (`tax_id`);

ALTER TABLE `taxes_book`
  ADD PRIMARY KEY (`taxes_book_id`),
  ADD KEY `ix_taxes_book_company_id` (`company_id`);

ALTER TABLE `taxes_book_item`
  ADD PRIMARY KEY (`taxes_book_item_id`),
  ADD KEY `fk_taxes_book_item_taxes_book1_idx` (`taxes_book_id`),
  ADD KEY `fk_taxes_book_item_bill1_idx` (`bill_id`),
  ADD KEY `fk_taxes_book_item_provider_bill1_idx` (`provider_bill_id`);

ALTER TABLE `tax_condition`
  ADD PRIMARY KEY (`tax_condition_id`);

ALTER TABLE `tax_condition_has_bill_type`
  ADD PRIMARY KEY (`tax_condition_id`,`bill_type_id`),
  ADD KEY `fk_tax_condition_has_bill_type_bill_type1_idx` (`bill_type_id`),
  ADD KEY `fk_tax_condition_has_bill_type_tax_condition1_idx` (`tax_condition_id`);

ALTER TABLE `tax_condition_has_bill_type_buy`
  ADD PRIMARY KEY (`tax_condition_id`,`bill_type_id`),
  ADD KEY `fk_tax_condition_has_bill_type_buy_bill_type1_idx` (`bill_type_id`),
  ADD KEY `fk_tax_condition_has_bill_type_buy_tax_condition1_idx` (`tax_condition_id`);

ALTER TABLE `tax_condition_has_document_type`
  ADD PRIMARY KEY (`tax_condition_id`,`document_type_document_type_id`),
  ADD KEY `fk_tax_condition_has_document_type_document_type1_idx` (`document_type_document_type_id`),
  ADD KEY `fk_tax_condition_has_document_type_tax_condition1_idx` (`tax_condition_id`);

ALTER TABLE `tax_rate`
  ADD PRIMARY KEY (`tax_rate_id`),
  ADD KEY `fk_tax_rate_tax1_idx` (`tax_id`);

ALTER TABLE `unit`
  ADD PRIMARY KEY (`unit_id`);

ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `user_app`
  ADD PRIMARY KEY (`user_app_id`);

ALTER TABLE `user_app_has_customer`
  ADD PRIMARY KEY (`user_app_has_customer_id`),
  ADD KEY `fk_user_app_has_customer_customer1_idx` (`customer_id`),
  ADD KEY `fk_user_app_has_customer_user_app1_idx` (`user_app_id`);

ALTER TABLE `user_visit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `validation_code`
  ADD PRIMARY KEY (`validation_code_id`),
  ADD KEY `fk_validation_code_user_app_has_customer1_idx` (`user_app_has_customer_id`);

ALTER TABLE `vendor`
  ADD PRIMARY KEY (`vendor_id`),
  ADD KEY `fk_vendor_1_idx` (`account_id`),
  ADD KEY `fk_vendor_2_idx` (`address_id`),
  ADD KEY `fk_vendor_3_idx` (`document_type_id`),
  ADD KEY `fk_vendor_vendor_commission1` (`vendor_commission_id`),
  ADD KEY `vendor_provider_id_fk` (`provider_id`);

ALTER TABLE `vendor_commission`
  ADD PRIMARY KEY (`vendor_commission_id`);

ALTER TABLE `vendor_liquidation`
  ADD PRIMARY KEY (`vendor_liquidation_id`),
  ADD KEY `fk_vendor_liquidation_vendor1_idx` (`vendor_id`);

ALTER TABLE `vendor_liquidation_item`
  ADD PRIMARY KEY (`vendor_liquidation_item_id`),
  ADD KEY `fk_vendor_liquidation_item_bill1_idx` (`bill_id`),
  ADD KEY `fk_vendor_liquidation_item_vendor_liquidation1_idx` (`vendor_liquidation_id`),
  ADD KEY `fk_vendor_liquidation_item_contract_detail1` (`contract_detail_id`);

ALTER TABLE `zone`
  ADD PRIMARY KEY (`zone_id`),
  ADD KEY `fk_zone_1_idx` (`parent_id`);


ALTER TABLE `account`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `accounting_period`
  MODIFY `accounting_period_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `account_config`
  MODIFY `account_config_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `account_config_has_account`
  MODIFY `account_config_has_account_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `account_movement`
  MODIFY `account_movement_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `account_movement_item`
  MODIFY `account_movement_item_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `account_movement_relation`
  MODIFY `account_movement_relation_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `address`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `app_failed_register`
  MODIFY `app_failed_register_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `auth_token`
  MODIFY `auth_token_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `bill`
  MODIFY `bill_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `bill_detail`
  MODIFY `bill_detail_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `bill_has_payment`
  MODIFY `bill_has_payment_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `bill_type`
  MODIFY `bill_type_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `checkbook`
  MODIFY `checkbook_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `company`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `company_has_billing`
  MODIFY `company_has_billing_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `conciliation`
  MODIFY `conciliation_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `conciliation_item`
  MODIFY `conciliation_item_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `connection`
  MODIFY `connection_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `connection_forced_historial`
  MODIFY `connection_forced_historial_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `contract`
  MODIFY `contract_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `contract_detail`
  MODIFY `contract_detail_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `contract_detail_log`
  MODIFY `contract_detail_log_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `contract_log`
  MODIFY `contract_log_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `contract_status`
  MODIFY `contract_status_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `currency`
  MODIFY `currency_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `customer_category`
  MODIFY `customer_category_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `customer_class`
  MODIFY `customer_class_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `customer_has_discount`
  MODIFY `cutomer_has_discount_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `customer_log`
  MODIFY `customer_log_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `debt_evolution`
  MODIFY `debt_evolution_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `discount`
  MODIFY `discount_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `discount_event`
  MODIFY `discount_event_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `document_type`
  MODIFY `document_type_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `email_transport`
  MODIFY `email_transport_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `empty_ads`
  MODIFY `empty_ads_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `funding_plan`
  MODIFY `funding_plan_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `invoice_class`
  MODIFY `invoice_class_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ip_range`
  MODIFY `ip_range_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `mobile_push`
  MODIFY `mobile_push_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `mobile_push_has_user_app`
  MODIFY `mobile_push_has_user_app_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `money_box`
  MODIFY `money_box_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `money_box_account`
  MODIFY `money_box_account_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `money_box_has_operation_type`
  MODIFY `money_box_has_operation_type_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `money_box_type`
  MODIFY `money_box_type_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `node`
  MODIFY `node_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `operation_type`
  MODIFY `operation_type_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `pagomiscuentas_file`
  MODIFY `pagomiscuentas_file_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `pagomiscuentas_file_has_bill`
  MODIFY `pagomiscuentas_file_has_bill_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `pagomiscuentas_file_has_payment`
  MODIFY `pagomiscuentas_file_has_payment_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `pago_facil_payment`
  MODIFY `pago_facil_payment_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `pago_facil_transmition_file`
  MODIFY `pago_facil_transmition_file_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `partner`
  MODIFY `partner_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `partner_distribution_model`
  MODIFY `partner_distribution_model_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `partner_distribution_model_has_partner`
  MODIFY `partner_distribution_model_has_partner_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `partner_liquidation`
  MODIFY `partner_liquidation_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `partner_liquidation_movement`
  MODIFY `partner_liquidation_movement_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `paycheck`
  MODIFY `paycheck_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `paycheck_log`
  MODIFY `paycheck_log_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `payment_item`
  MODIFY `payment_item_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `payment_method`
  MODIFY `payment_method_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `payment_plan`
  MODIFY `payment_plan_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `plan_feature`
  MODIFY `plan_feature_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `point_of_sale`
  MODIFY `point_of_sale_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `product_commission`
  MODIFY `product_commission_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `product_discount`
  MODIFY `product_discount_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `product_price`
  MODIFY `product_price_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `product_to_invoice`
  MODIFY `product_to_invoice_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `profile`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `profile_class`
  MODIFY `profile_class_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `provider`
  MODIFY `provider_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `provider_bill`
  MODIFY `provider_bill_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `provider_bill_item`
  MODIFY `provider_bill_item_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `provider_payment`
  MODIFY `provider_payment_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `provider_payment_item`
  MODIFY `provider_payment_item_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `resume`
  MODIFY `resume_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `resume_item`
  MODIFY `resume_item_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `server`
  MODIFY `server_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `stock_movement`
  MODIFY `stock_movement_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `tax`
  MODIFY `tax_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `taxes_book`
  MODIFY `taxes_book_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `taxes_book_item`
  MODIFY `taxes_book_item_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `tax_condition`
  MODIFY `tax_condition_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `tax_rate`
  MODIFY `tax_rate_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `unit`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `user_app`
  MODIFY `user_app_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `user_app_has_customer`
  MODIFY `user_app_has_customer_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `user_visit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `validation_code`
  MODIFY `validation_code_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `vendor`
  MODIFY `vendor_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `vendor_commission`
  MODIFY `vendor_commission_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `vendor_liquidation`
  MODIFY `vendor_liquidation_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `vendor_liquidation_item`
  MODIFY `vendor_liquidation_item_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `zone`
  MODIFY `zone_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `account`
  ADD CONSTRAINT `fk_account_account1` FOREIGN KEY (`parent_account_id`) REFERENCES `account` (`account_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `account_config_has_account`
  ADD CONSTRAINT `fk_account_config_has_account_account1` FOREIGN KEY (`account_id`) REFERENCES `account` (`account_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_account_config_has_account_account_config1` FOREIGN KEY (`account_config_id`) REFERENCES `account_config` (`account_config_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `account_movement`
  ADD CONSTRAINT `fk_account_movement_accounting_period1` FOREIGN KEY (`accounting_period_id`) REFERENCES `accounting_period` (`accounting_period_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_account_movement_id` FOREIGN KEY (`daily_money_box_account_id`) REFERENCES `money_box_account` (`money_box_account_id`),
  ADD CONSTRAINT `fk_account_movement_partner_distribution_model1` FOREIGN KEY (`partner_distribution_model_id`) REFERENCES `partner_distribution_model` (`partner_distribution_model_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `account_movement_item`
  ADD CONSTRAINT `fk_account_movement_item_account1` FOREIGN KEY (`account_id`) REFERENCES `account` (`account_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_account_movement_item_account_movement1` FOREIGN KEY (`account_movement_id`) REFERENCES `account_movement` (`account_movement_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `account_movement_relation`
  ADD CONSTRAINT `fk_account_movement_relation_account_movement1` FOREIGN KEY (`account_movement_id`) REFERENCES `account_movement` (`account_movement_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `address`
  ADD CONSTRAINT `fk_address_zone` FOREIGN KEY (`zone_id`) REFERENCES `zone` (`zone_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `auth_assignment`
  ADD CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auth_assignment_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `auth_item`
  ADD CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_auth_item_group_code` FOREIGN KEY (`group_code`) REFERENCES `auth_item_group` (`code`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `auth_item_child`
  ADD CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `auth_token`
  ADD CONSTRAINT `fk_auth_token_user_app1` FOREIGN KEY (`user_app_id`) REFERENCES `user_app` (`user_app_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `bill`
  ADD CONSTRAINT `fk_bill_bill_type1` FOREIGN KEY (`bill_type_id`) REFERENCES `bill_type` (`bill_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_bill_currency1` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`currency_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_bill_partner_distribution_model1` FOREIGN KEY (`partner_distribution_model_id`) REFERENCES `partner_distribution_model` (`partner_distribution_model_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `bill_detail`
  ADD CONSTRAINT `fk_bill_detail_bill1` FOREIGN KEY (`bill_id`) REFERENCES `bill` (`bill_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_bill_detail_discount1` FOREIGN KEY (`discount_id`) REFERENCES `discount` (`discount_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_bill_detail_product1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_bill_detail_unit1` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`unit_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `bill_has_payment`
  ADD CONSTRAINT `fk_bill_has_payment_bill1` FOREIGN KEY (`bill_id`) REFERENCES `bill` (`bill_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_bill_has_payment_payment1` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`payment_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `bill_type`
  ADD CONSTRAINT `fk_bill_type_invoice_class1` FOREIGN KEY (`invoice_class_id`) REFERENCES `invoice_class` (`invoice_class_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `bill_type_has_bill_type`
  ADD CONSTRAINT `fk_bill_type_has_bill_type_bill_type1` FOREIGN KEY (`parent_id`) REFERENCES `bill_type` (`bill_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_bill_type_has_bill_type_bill_type2` FOREIGN KEY (`child_id`) REFERENCES `bill_type` (`bill_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `category`
  ADD CONSTRAINT `fk_category_category1` FOREIGN KEY (`parent_id`) REFERENCES `category` (`category_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `checkbook`
  ADD CONSTRAINT `fk_checkbook_money_box_account1_idx` FOREIGN KEY (`money_box_account_id`) REFERENCES `money_box_account` (`money_box_account_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `company`
  ADD CONSTRAINT `fk_company_company1` FOREIGN KEY (`parent_id`) REFERENCES `company` (`company_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_company_partner_distribution_model1` FOREIGN KEY (`partner_distribution_model_id`) REFERENCES `partner_distribution_model` (`partner_distribution_model_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_company_tax_condition1` FOREIGN KEY (`tax_condition_id`) REFERENCES `tax_condition` (`tax_condition_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `company_has_billing`
  ADD CONSTRAINT `company_has_billing_bill_type_bill_type_id_fk` FOREIGN KEY (`bill_type_id`) REFERENCES `bill_type` (`bill_type_id`),
  ADD CONSTRAINT `company_has_billing_company_company_id_fk` FOREIGN KEY (`parent_company_id`) REFERENCES `company` (`company_id`),
  ADD CONSTRAINT `company_has_billing_company_company_id_fk_2` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`);

ALTER TABLE `company_has_bill_type`
  ADD CONSTRAINT `fk_company_has_bill_type_bill_type1` FOREIGN KEY (`bill_type_id`) REFERENCES `bill_type` (`bill_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_company_has_bill_type_company1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `conciliation`
  ADD CONSTRAINT `fk_conciliation_money_box_account1` FOREIGN KEY (`money_box_account_id`) REFERENCES `money_box_account` (`money_box_account_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_conciliation_resume1` FOREIGN KEY (`resume_id`) REFERENCES `resume` (`resume_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `conciliation_item`
  ADD CONSTRAINT `fk_conciliation_item_conciliation1` FOREIGN KEY (`conciliation_id`) REFERENCES `conciliation` (`conciliation_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `conciliation_item_has_account_movement_item`
  ADD CONSTRAINT `fk_conciliation_item_has_account_movement_item_account_movem1` FOREIGN KEY (`account_movement_item_id`) REFERENCES `account_movement_item` (`account_movement_item_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_conciliation_item_has_account_movement_item_conciliation_1` FOREIGN KEY (`conciliation_item_id`) REFERENCES `conciliation_item` (`conciliation_item_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `conciliation_item_has_resume_item`
  ADD CONSTRAINT `fk_conciliation_item_has_resume_item_conciliation_item1` FOREIGN KEY (`conciliation_item_id`) REFERENCES `conciliation_item` (`conciliation_item_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_conciliation_item_has_resume_item_resume_item1` FOREIGN KEY (`resume_item_id`) REFERENCES `resume_item` (`resume_item_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `connection`
  ADD CONSTRAINT `fk_connection_contract1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_connection_node1` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_connection_server1` FOREIGN KEY (`server_id`) REFERENCES `server` (`server_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `connection_forced_historial`
  ADD CONSTRAINT `fk_connection_forced_historial_connection1` FOREIGN KEY (`connection_id`) REFERENCES `connection` (`connection_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_connection_forced_historial_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

ALTER TABLE `contract`
  ADD CONSTRAINT `fk_contract_address` FOREIGN KEY (`address_id`) REFERENCES `address` (`address_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_contract_customer` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_contract_vendor1` FOREIGN KEY (`vendor_id`) REFERENCES `vendor` (`vendor_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `contract_detail`
  ADD CONSTRAINT `fk_contract_detail_contract` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_contract_detail_discount1` FOREIGN KEY (`discount_id`) REFERENCES `discount` (`discount_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_contract_detail_funding_plan1` FOREIGN KEY (`funding_plan_id`) REFERENCES `funding_plan` (`funding_plan_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_contract_detail_product` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_contract_detail_vendor1` FOREIGN KEY (`vendor_id`) REFERENCES `vendor` (`vendor_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `contract_detail_log`
  ADD CONSTRAINT `fk_contract_detail_log_contract_detail1` FOREIGN KEY (`contract_detail_id`) REFERENCES `contract_detail` (`contract_detail_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_contract_detail_log_discount1` FOREIGN KEY (`discount_id`) REFERENCES `discount` (`discount_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_contract_detail_log_funding_plan1` FOREIGN KEY (`funding_plan_id`) REFERENCES `funding_plan` (`funding_plan_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_contract_detail_log_product1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `contract_log`
  ADD CONSTRAINT `fk_contract_log_contract1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `customer`
  ADD CONSTRAINT `fk_customer_account1` FOREIGN KEY (`account_id`) REFERENCES `account` (`account_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_customer_address` FOREIGN KEY (`address_id`) REFERENCES `address` (`address_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_customer_customer1` FOREIGN KEY (`customer_reference_id`) REFERENCES `customer` (`customer_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_customer_document_type1` FOREIGN KEY (`document_type_id`) REFERENCES `document_type` (`document_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_customer_tax_condition1` FOREIGN KEY (`tax_condition_id`) REFERENCES `tax_condition` (`tax_condition_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `customer_category`
  ADD CONSTRAINT `fk_customer_category_1` FOREIGN KEY (`parent_id`) REFERENCES `customer_category` (`customer_category_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `customer_category_has_customer`
  ADD CONSTRAINT `fk_customer_category2_has_customer_customer_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_customer_category2_has_customer_customer_category_2` FOREIGN KEY (`customer_category_id`) REFERENCES `customer_category` (`customer_category_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `customer_class_has_customer`
  ADD CONSTRAINT `fk_customer_category_has_customer_customer_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_customer_category_has_customer_customer_category_2` FOREIGN KEY (`customer_class_id`) REFERENCES `customer_class` (`customer_class_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `customer_has_discount`
  ADD CONSTRAINT `fk_customer_has_discount_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_customer_has_discount_discount1` FOREIGN KEY (`discount_id`) REFERENCES `discount` (`discount_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `customer_log`
  ADD CONSTRAINT `fk_customer_log_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `discount`
  ADD CONSTRAINT `fk_discount_product1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `funding_plan`
  ADD CONSTRAINT `fk_funding_plan_product1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `ip_range`
  ADD CONSTRAINT `fk_rank_ip_1` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `mobile_push_has_user_app`
  ADD CONSTRAINT `fk_mobile_push_has_user_app_mobile_push2` FOREIGN KEY (`mobile_push_id`) REFERENCES `mobile_push` (`mobile_push_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_mobile_push_has_user_app_user_app2` FOREIGN KEY (`user_app_id`) REFERENCES `user_app` (`user_app_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `money_box`
  ADD CONSTRAINT `fk_money_box_account1` FOREIGN KEY (`account_id`) REFERENCES `account` (`account_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_money_box_money_box_type1` FOREIGN KEY (`money_box_type_id`) REFERENCES `money_box_type` (`money_box_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `money_box_account`
  ADD CONSTRAINT `fk_bank_account_bank1` FOREIGN KEY (`money_box_id`) REFERENCES `money_box` (`money_box_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_money_box_account_account1` FOREIGN KEY (`account_id`) REFERENCES `account` (`account_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_money_box_account_currency1` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`currency_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `money_box_has_operation_type`
  ADD CONSTRAINT `fk_money_box_has_operation_type_account1` FOREIGN KEY (`account_id`) REFERENCES `account` (`account_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_money_box_has_operation_type_money_box1` FOREIGN KEY (`money_box_id`) REFERENCES `money_box` (`money_box_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_money_box_has_operation_type_money_box_account1` FOREIGN KEY (`money_box_account_id`) REFERENCES `money_box_account` (`money_box_account_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_money_box_has_operation_type_operation_type1` FOREIGN KEY (`operation_type_id`) REFERENCES `operation_type` (`operation_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `node`
  ADD CONSTRAINT `fk_node_1` FOREIGN KEY (`zone_id`) REFERENCES `zone` (`zone_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_node_node1` FOREIGN KEY (`parent_node_id`) REFERENCES `node` (`node_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_node_server1` FOREIGN KEY (`server_id`) REFERENCES `server` (`server_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `node_has_ecopago`
  ADD CONSTRAINT `fk_node_has_ecopago_node1` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `pagomiscuentas_file`
  ADD CONSTRAINT `pagomiscuentas_file_company_company_id_fk` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`);

ALTER TABLE `pagomiscuentas_file_has_bill`
  ADD CONSTRAINT `pagomiscuentas_file_has_bill_fk` FOREIGN KEY (`bill_id`) REFERENCES `bill` (`bill_id`);

ALTER TABLE `pagomiscuentas_file_has_payment`
  ADD CONSTRAINT `pagomiscuentas_file_has_payment_fk` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`payment_id`);

ALTER TABLE `pago_facil_payment`
  ADD CONSTRAINT `fk_pago_facil_payment_pago_facil_transmition_file1` FOREIGN KEY (`pago_facil_transmition_file_pago_facil_transmition_file_id`) REFERENCES `pago_facil_transmition_file` (`pago_facil_transmition_file_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_pago_facil_payment_payment1` FOREIGN KEY (`payment_payment_id`) REFERENCES `payment` (`payment_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `partner`
  ADD CONSTRAINT `fk_partner_account1` FOREIGN KEY (`account_id`) REFERENCES `account` (`account_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `partner_distribution_model`
  ADD CONSTRAINT `fk_partner_distribution_model_company1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `partner_distribution_model_has_partner`
  ADD CONSTRAINT `fk_partner_has_company_partner1` FOREIGN KEY (`partner_id`) REFERENCES `partner` (`partner_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_partner_has_company_partner_distribution_model1` FOREIGN KEY (`partner_distribution_model_id`) REFERENCES `partner_distribution_model` (`partner_distribution_model_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `partner_liquidation`
  ADD CONSTRAINT `fk_partner_liquidation_partner_distribution_model_has_partner1` FOREIGN KEY (`partner_distribution_model_has_partner_id`) REFERENCES `partner_distribution_model_has_partner` (`partner_distribution_model_has_partner_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `partner_liquidation_movement`
  ADD CONSTRAINT `partner_liquidation_movement_ibfk_1` FOREIGN KEY (`partner_liquidation_id`) REFERENCES `partner_liquidation` (`partner_liquidation_id`);

ALTER TABLE `paycheck`
  ADD CONSTRAINT `fk_paycheck_checkbook1` FOREIGN KEY (`checkbook_id`) REFERENCES `checkbook` (`checkbook_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_paycheck_money_box1` FOREIGN KEY (`money_box_id`) REFERENCES `money_box` (`money_box_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_paycheck_money_box_account1` FOREIGN KEY (`money_box_account_id`) REFERENCES `money_box_account` (`money_box_account_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `paycheck_log`
  ADD CONSTRAINT `fk_paycheck_log_money_box_account1` FOREIGN KEY (`money_box_account_id`) REFERENCES `money_box_account` (`money_box_account_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_paycheck_log_paycheck1` FOREIGN KEY (`paycheck_id`) REFERENCES `paycheck` (`paycheck_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `payment`
  ADD CONSTRAINT `fk_payment_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_payment_partner_distribution_model1` FOREIGN KEY (`partner_distribution_model_id`) REFERENCES `partner_distribution_model` (`partner_distribution_model_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `payment_item`
  ADD CONSTRAINT `fk_payment_item_money_box_account1` FOREIGN KEY (`money_box_account_id`) REFERENCES `money_box_account` (`money_box_account_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_payment_item_paycheck1` FOREIGN KEY (`paycheck_id`) REFERENCES `paycheck` (`paycheck_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_payment_item_payment1` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`payment_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_payment_item_payment_method1` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_method` (`payment_method_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `payment_plan`
  ADD CONSTRAINT `fk_payment_plan_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `plan_feature`
  ADD CONSTRAINT `fk_plan_feature_1` FOREIGN KEY (`parent_id`) REFERENCES `plan_feature` (`plan_feature_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `point_of_sale`
  ADD CONSTRAINT `fk_sale_point_company1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `product`
  ADD CONSTRAINT `fk_product_account1` FOREIGN KEY (`account_id`) REFERENCES `account` (`account_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_product_product_commission1` FOREIGN KEY (`product_commission_id`) REFERENCES `product_commission` (`product_commission_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_product_unit1` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`unit_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_product_unit2` FOREIGN KEY (`secondary_unit_id`) REFERENCES `unit` (`unit_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `product_discount`
  ADD CONSTRAINT `fk_product_discount_discount_event1` FOREIGN KEY (`discount_event_id`) REFERENCES `discount_event` (`discount_event_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_product_discount_product1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `product_has_category`
  ADD CONSTRAINT `fk_product_has_category_category1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_product_has_category_product` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `product_has_plan_feature`
  ADD CONSTRAINT `fk_product_has_plan_feature_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_product_has_plan_feature_2` FOREIGN KEY (`plan_feature_id`) REFERENCES `plan_feature` (`plan_feature_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `product_has_tax_rate`
  ADD CONSTRAINT `fk_product_has_tax_rate_product1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_product_has_tax_rate_tax_rate1` FOREIGN KEY (`tax_rate_id`) REFERENCES `tax_rate` (`tax_rate_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `product_price`
  ADD CONSTRAINT `fk_product_price_product1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `product_to_invoice`
  ADD CONSTRAINT `fk_product_to_invoice_contract_detail1` FOREIGN KEY (`contract_detail_id`) REFERENCES `contract_detail` (`contract_detail_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_product_to_invoice_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_product_to_invoice_discount1` FOREIGN KEY (`discount_id`) REFERENCES `discount` (`discount_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_product_to_invoice_funding_plan1` FOREIGN KEY (`funding_plan_id`) REFERENCES `funding_plan` (`funding_plan_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_product_to_invoice_payment_plan1` FOREIGN KEY (`payment_plan_id`) REFERENCES `payment_plan` (`payment_plan_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `profile`
  ADD CONSTRAINT `fk_profile_profile_class1` FOREIGN KEY (`profile_class_id`) REFERENCES `profile_class` (`profile_class_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `provider`
  ADD CONSTRAINT `fk_provider_account1` FOREIGN KEY (`account_id`) REFERENCES `account` (`account_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_provider_tax_condition1` FOREIGN KEY (`tax_condition_id`) REFERENCES `tax_condition` (`tax_condition_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `provider_bill`
  ADD CONSTRAINT `fk_provider_bill_bill_type1` FOREIGN KEY (`bill_type_id`) REFERENCES `bill_type` (`bill_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_provider_bill_partner_distribution_model1` FOREIGN KEY (`partner_distribution_model_id`) REFERENCES `partner_distribution_model` (`partner_distribution_model_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_provider_bill_provider1` FOREIGN KEY (`provider_id`) REFERENCES `provider` (`provider_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `provider_bill_has_provider_payment`
  ADD CONSTRAINT `fk_provider_bill_has_provider_payment_provider_bill1` FOREIGN KEY (`provider_bill_id`) REFERENCES `provider_bill` (`provider_bill_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_provider_bill_has_provider_payment_provider_payment1` FOREIGN KEY (`provider_payment_id`) REFERENCES `provider_payment` (`provider_payment_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `provider_bill_has_tax_rate`
  ADD CONSTRAINT `fk_provider_bill_has_tax_rate_provider_bill1` FOREIGN KEY (`provider_bill_id`) REFERENCES `provider_bill` (`provider_bill_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_provider_bill_has_tax_rate_tax_rate1` FOREIGN KEY (`tax_rate_id`) REFERENCES `tax_rate` (`tax_rate_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `provider_bill_item`
  ADD CONSTRAINT `fk_provider_bill_item_account1` FOREIGN KEY (`account_id`) REFERENCES `account` (`account_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_provider_bill_item_provider_bill1` FOREIGN KEY (`provider_bill_id`) REFERENCES `provider_bill` (`provider_bill_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `provider_payment`
  ADD CONSTRAINT `fk_provider_payment_partner_distribution_model1` FOREIGN KEY (`partner_distribution_model_id`) REFERENCES `partner_distribution_model` (`partner_distribution_model_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_provider_payment_provider1` FOREIGN KEY (`provider_id`) REFERENCES `provider` (`provider_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `provider_payment_item`
  ADD CONSTRAINT `provider_payment_item_money_box_account_money_box_account_id_fk` FOREIGN KEY (`money_box_account_id`) REFERENCES `money_box_account` (`money_box_account_id`),
  ADD CONSTRAINT `provider_payment_item_paycheck_paycheck_id_fk` FOREIGN KEY (`paycheck_id`) REFERENCES `paycheck` (`paycheck_id`),
  ADD CONSTRAINT `provider_payment_item_payment_method_payment_method_id_fk` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_method` (`payment_method_id`),
  ADD CONSTRAINT `provider_payment_item_provider_payment_provider_payment_id_fk` FOREIGN KEY (`provider_payment_id`) REFERENCES `provider_payment` (`provider_payment_id`);

ALTER TABLE `resume`
  ADD CONSTRAINT `fk_resume_money_box_account1` FOREIGN KEY (`money_box_account_id`) REFERENCES `money_box_account` (`money_box_account_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `resume_item`
  ADD CONSTRAINT `fk_resume_item_money_box_has_operation_type1` FOREIGN KEY (`money_box_has_operation_type_id`) REFERENCES `money_box_has_operation_type` (`money_box_has_operation_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_resume_item_resume1` FOREIGN KEY (`resume_id`) REFERENCES `resume` (`resume_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `stock_movement`
  ADD CONSTRAINT `fk_stock_movement_bill_detail1` FOREIGN KEY (`bill_detail_id`) REFERENCES `bill_detail` (`bill_detail_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_stock_movement_product1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `taxes_book_item`
  ADD CONSTRAINT `fk_taxes_book_item_bill1` FOREIGN KEY (`bill_id`) REFERENCES `bill` (`bill_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_taxes_book_item_provider_bill1` FOREIGN KEY (`provider_bill_id`) REFERENCES `provider_bill` (`provider_bill_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_taxes_book_item_taxes_book1` FOREIGN KEY (`taxes_book_id`) REFERENCES `taxes_book` (`taxes_book_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `tax_condition_has_bill_type`
  ADD CONSTRAINT `fk_tax_condition_has_bill_type_bill_type1` FOREIGN KEY (`bill_type_id`) REFERENCES `bill_type` (`bill_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_tax_condition_has_bill_type_tax_condition1` FOREIGN KEY (`tax_condition_id`) REFERENCES `tax_condition` (`tax_condition_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `tax_condition_has_bill_type_buy`
  ADD CONSTRAINT `fk_tax_condition_has_bill_type_buy_bill_type1` FOREIGN KEY (`bill_type_id`) REFERENCES `bill_type` (`bill_type_id`),
  ADD CONSTRAINT `fk_tax_condition_has_bill_type_buy_tax_condition1` FOREIGN KEY (`tax_condition_id`) REFERENCES `tax_condition` (`tax_condition_id`);

ALTER TABLE `tax_condition_has_document_type`
  ADD CONSTRAINT `fk_tax_condition_has_document_type_document_type1` FOREIGN KEY (`document_type_document_type_id`) REFERENCES `document_type` (`document_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_tax_condition_has_document_type_tax_condition1` FOREIGN KEY (`tax_condition_id`) REFERENCES `tax_condition` (`tax_condition_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `tax_rate`
  ADD CONSTRAINT `fk_tax_rate_tax1` FOREIGN KEY (`tax_id`) REFERENCES `tax` (`tax_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `user_app_has_customer`
  ADD CONSTRAINT `fk_user_app_has_customer_customer1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_app_has_customer_user_app1` FOREIGN KEY (`user_app_id`) REFERENCES `user_app` (`user_app_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `user_visit_log`
  ADD CONSTRAINT `user_visit_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `validation_code`
  ADD CONSTRAINT `fk_validation_code_user_app_has_customer1` FOREIGN KEY (`user_app_has_customer_id`) REFERENCES `user_app_has_customer` (`user_app_has_customer_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `vendor`
  ADD CONSTRAINT `fk_vendor_1` FOREIGN KEY (`account_id`) REFERENCES `account` (`account_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_vendor_2` FOREIGN KEY (`address_id`) REFERENCES `address` (`address_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_vendor_3` FOREIGN KEY (`document_type_id`) REFERENCES `document_type` (`document_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_vendor_vendor_commission1` FOREIGN KEY (`vendor_commission_id`) REFERENCES `vendor_commission` (`vendor_commission_id`),
  ADD CONSTRAINT `vendor_provider_id_fk` FOREIGN KEY (`provider_id`) REFERENCES `provider` (`provider_id`);

ALTER TABLE `vendor_liquidation`
  ADD CONSTRAINT `fk_vendor_liquidation_vendor1` FOREIGN KEY (`vendor_id`) REFERENCES `vendor` (`vendor_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `vendor_liquidation_item`
  ADD CONSTRAINT `fk_vendor_liquidation_item_bill1` FOREIGN KEY (`bill_id`) REFERENCES `bill` (`bill_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_vendor_liquidation_item_contract_detail1` FOREIGN KEY (`contract_detail_id`) REFERENCES `contract_detail` (`contract_detail_id`),
  ADD CONSTRAINT `fk_vendor_liquidation_item_vendor_liquidation1` FOREIGN KEY (`vendor_liquidation_id`) REFERENCES `vendor_liquidation` (`vendor_liquidation_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `zone`
  ADD CONSTRAINT `fk_zone_zone1` FOREIGN KEY (`parent_id`) REFERENCES `zone` (`zone_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
