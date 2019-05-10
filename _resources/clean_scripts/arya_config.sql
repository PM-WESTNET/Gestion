-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Servidor: westnet-data
-- Tiempo de generación: 15-04-2019 a las 16:28:25
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
-- Base de datos: `arya_config`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `name` varchar(45) CHARACTER SET latin1 NOT NULL,
  `status` enum('enabled','disabled') CHARACTER SET latin1 DEFAULT NULL,
  `superadmin` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `category`
--

INSERT INTO `category` (`category_id`, `name`, `status`, `superadmin`) VALUES
(1, 'Contabilidad', 'enabled', 0),
(2, 'Comprobantes', 'enabled', 0),
(3, 'Gestión de Stock', 'enabled', 0),
(4, 'Agenda', 'enabled', 0),
(5, 'Media', 'enabled', 0),
(6, 'Productos', 'enabled', 0),
(7, 'Customer', 'enabled', 0),
(8, 'Sequre', 'enabled', 0),
(9, 'Westnet', 'enabled', 0),
(10, 'Ecopago', 'enabled', 0),
(11, 'General', 'enabled', 0),
(12, 'Socios', 'enabled', 0),
(13, 'Ticket', 'enabled', 0),
(16, 'Notificaciones por Correo', 'enabled', NULL),
(17, 'Vendedores', 'enabled', NULL),
(19, 'ADS', 'enabled', NULL),
(20, 'Pagomiscuentas', 'enabled', NULL),
(21, 'Mobile App', 'enabled', NULL),
(22, 'Notificaciones', 'enabled', 0),
(23, 'Socio', 'enabled', NULL),
(25, 'Sequre', 'enabled', NULL),
(26, 'Socio', 'enabled', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config`
--

CREATE TABLE `config` (
  `config_id` int(11) NOT NULL,
  `value` text CHARACTER SET latin1,
  `item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `config`
--

INSERT INTO `config` (`config_id`, `value`, `item_id`) VALUES
(1, '4', 1),
(2, '1', 2),
(3, '1', 8),
(4, '28800', 9),
(5, '10:00', 12),
(6, '1', 6),
(7, '14', 3),
(8, '1', 4),
(9, '1', 5),
(10, '1', 20),
(11, '0', 19),
(12, '35', 22),
(13, '96', 23),
(14, '100', 21),
(15, '0', 7),
(16, 'Ecopago', 24),
(17, 'cajas_ecopago', 25),
(18, 'idlipbhoabgfdjkbpicgjjidfmgfcnmj', 26),
(19, '2', 29),
(20, '3', 30),
(21, '3', 31),
(22, '10', 32),
(23, '5', 33),
(24, '1', 34),
(25, '5', 35),
(27, '10000', 38),
(28, '6', 39),
(29, '08:00', 10),
(30, '18:00', 11),
(31, '200', 13),
(32, '200', 14),
(33, '1920', 15),
(34, '1920', 16),
(35, '1', 18),
(36, '0261 4 200997 - WhatsApp: 261 5087213', 41),
(37, '0261 4 200997 - WhatsApp: 261 6547474', 42),
(38, '0261 4 294321 - WhatsApp: 261 6607841', 43),
(39, 'Westnet le informa', 44),
(40, '15', 45),
(41, '31', 46),
(42, '1', 49),
(43, '3', 50),
(44, '9900005', 51),
(45, '20', 52),
(46, '1', 54),
(47, 'http://westnet-pdf', 56),
(48, '5001', 57),
(49, '4', 55),
(50, '6', 58),
(51, '9', 59),
(52, '8', 60),
(53, '1', 61),
(54, '3', 53),
(55, '0', 63),
(56, '4', 62),
(57, '2', 64),
(58, '10', 68),
(59, '2', 69),
(60, '92', 71),
(61, '2615070672', 70),
(62, '42', 72),
(63, '34', 73),
(64, '22,93', 74),
(65, '6', 75),
(66, '100', 76),
(68, 'SR CLIENTE LEA ATENTAMENTE LOS SIGUIENTE PUNTOS:', 78),
(69, '1). La empresa No realiza ensayos, una vez instalado el servicio de Internet se asume el compromiso de pago.<br>                                                             2). El Plazo m&iacute;nimo del contrato es de 6 meses.<br>                                                             3). Los equipos instalados (Antena, radio, fuente, cables y Router) Son en <u><strong>COMODATO</strong></u>, y<strong> </strong>son propiedad de la Empresa <u>Westnet</u>.<br>                                                             4). El ancho de banda m&iacute;nimo garantizado (Mbps m&iacute;nima ofrecida) se encuentra detallado en el presente documento.<br>                                                             5). El Importe del primer comprobante es informado con antelaci&oacute;n por el sector de Administraci&oacute;n.<br>                                                             6). El plazo m&aacute;ximo para realizar el primer pago es de 24 Hs. Posterior a realizada la Instalaci&oacute;n.<br>                                                             7). En caso de adquirir un Router adicional al de la instalaci&oacute;n, el cliente tiene que abonar por &uacute;nica vez el valor del mismo.<br>                                ', 79),
(70, '', 81),
(71, '15', 82),
(72, 'Su nueva factura ya está disponible', 88),
(82, 'LUN. A VIE. 09:00 A 17:00 HS – SAB 09:00 A 13:00 HS', 116),
(85, '123456', 83),
(86, '10', 84),
(87, '30', 85),
(88, 'quoma', 89),
(89, 'quoma2018', 90),
(91, 'Gracias por registrarse en nuestra app. Su código de validación es: {code}', 92),
(92, '1', 93),
(93, '4', 94),
(94, 'http://mesa.westnet.com.ar/', 37),
(95, '', 86),
(96, 'quoma', 119),
(97, 'quoma2018', 120),
(98, 'http://207.38.89.147:8443/bin/send.json?', 121),
(99, 'app\\modules\\invoice\\components\\einvoice\\afip\\fev1\\Fev1', 132),
(100, '', 87),
(101, '', 91),
(102, '9 de julio 1257 Of.108  - P10', 133),
(104, '1', 135),
(105, '1', 136),
(106, '101', 138),
(107, '20', 145),
(108, '13', 146),
(109, '13', 147),
(110, '14', 148),
(111, '14', 149),
(112, '10', 154),
(113, '8', 152),
(114, '4', 150),
(115, '2', 151),
(116, '10', 153);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `item`
--

CREATE TABLE `item` (
  `item_id` int(11) NOT NULL,
  `attr` varchar(45) CHARACTER SET latin1 NOT NULL,
  `type` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `default` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `label` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `description` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `multiple` tinyint(1) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `superadmin` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `item`
--

INSERT INTO `item` (`item_id`, `attr`, `type`, `default`, `label`, `description`, `multiple`, `category_id`, `superadmin`) VALUES
(1, 'payment_method_bank', 'textInput', '', 'Metodo de pago que utiliza Bancos', '', 0, 1, 0),
(2, 'money_box_bank', 'textInput', '', 'Tipo de Entidad Monetaria tipo Banco', '', 0, 1, 1),
(3, 'bill_default_expiration_days', 'textInput', '14', 'Días por defecto para vencimiento de orden de', '', 0, 2, 0),
(4, 'force_customer_company', 'checkbox', '', 'Forzar la utilización de la empresa asociada ', '', 0, 2, 0),
(5, 'show_delivery_note_verification_column', 'checkbox', '1', 'Mostrar columna de verificación en remito', '', 0, 2, 0),
(6, 'enable_secondary_stock', 'checkbox', '1', 'Habilitar inventario secundario', '', 0, 3, 0),
(7, 'strict_stock', 'checkbox', '1', 'Stock estricto (no se permite stock negativo)', '', 0, 3, 0),
(8, 'check_expiration_on_login', 'checkbox', '1', 'Revisar tareas vencidas al iniciar sesión', 'Indica si se revisarán las tareas vencidas de un usuario cuando loguee o no.', 0, 4, 1),
(9, 'check_expiration_timeout', 'textInput', '28800', 'Timeout para revisión de tareas vencidas (s)', 'Timeout para revisión de tareas vencidas (en segundos): 28800s por defecto', 0, 4, 1),
(10, 'work_hours_start', 'textInput', '08:00', 'Hora de inicio de día laboral', 'Indica la hora de inicio de un día laboral (formato H:i)', 0, 4, 1),
(11, 'work_hours_end', 'textInput', '18:00', 'Hora de fin de día laboral', 'Indica la hora de fin de un día laboral (formato H:i)', 0, 4, 1),
(12, 'work_hours_quantity', 'textInput', '10:00', 'Cantidad de horas laborables en un día', 'Cantidad de horas laborables en un día habil (Formato H:i, i.e. 10 horas laborables => 10:00)', 0, 4, 1),
(13, 'image_min_width', 'textInput', '200', 'Ancho mínimo de imagenes en pixeles', '', 0, 5, 0),
(14, 'image_min_height', 'textInput', '200', 'Alto mínimo de imagenes en pixeles', '', 0, 5, 0),
(15, 'image_max_width', 'textInput', '1920', 'Ancho máximo de imagenes en pixeles', '', 0, 5, 0),
(16, 'image_max_height', 'textInput', '1920', 'Alto máximo de imagenes en pixeles', '', 0, 5, 0),
(17, 'image_quality', 'textInput', '0.8', 'Calidad de imagen', 'Valor entre 0 y 1, siendo 1 la máxima calidad', 0, 5, 0),
(18, 'image_thumbnail_mode_inset', 'checkbox', '1', 'Al generar miniatura, mantener relación de as', '', 0, 5, 0),
(19, 'sale_products_list_view', 'textInput', '1', '¿Mostrar imágenes en la lista de productos al', '', 0, 6, 0),
(20, 'customer_address_required', 'textInput', '1', 'Requerir domicilio en carga de cliente', '', 0, 7, 0),
(21, 'default_ceil_dfl_percent', 'textInput', '30', 'Porcentaje de tráfico P2P', '', 0, 8, 0),
(22, 'default_cir', 'textInput', '35', 'Cir', '', 0, 9, 0),
(23, 'annual_availability', 'textInput', '96', 'Disponibilidad Anual', '', 0, 9, 0),
(24, 'payment_method', 'textInput', 'Contado', 'Método de pago', 'Método de pago utilizado por defecto para pagos de Ecopagos', 0, 10, 1),
(25, 'money_box_type', 'textInput', 'cajas_ecopago', 'Tipo de entidad bancaria', 'Tipo de entidad bancaria utilizada para mostrar a que entidades bancarias rendir dinero de cierres de lote', 0, 10, 1),
(26, 'chrome_print_app', 'textInput', 'idlipbhoabgfdjkbpicgjjidfmgfcnmj', 'ID App para Google Chrome (Manejador de impre', 'ID de la app para Google Chrome que se utiliza para realizar las impresiones en las ticketeras. Es necesario que este ID sea valido y sea el mismo que provee la instalacion de la app en el explorador Chrome (en vista de Extensiones este ID es visible).', 0, 10, 1),
(29, 'payment_method_paycheck', 'textInput', '0', 'Metodo de pago - Cheque', '', 0, 1, 0),
(30, 'partner_payment_account', 'textInput', '', 'Cuenta de Cobro', '', 0, 12, 0),
(31, 'partner_provider_payment_account', 'textInput', '', 'Cuenta de Pago', '', 0, 12, 0),
(32, 'expiration_timeout', 'textInput', '10', 'Timeout para cierre automático de tickets', 'Timeout para cerrar automáticamente los tickets abiertos (en días): 10 días por defecto', 0, 13, 1),
(33, 'pagination_limit', 'textInput', '5', 'Límite de elementos para cada página de Ticke', 'Setea el límite que las páginas utilizadas en Ticket (principalmente listado de observaciones) muestran', 0, 13, 1),
(34, 'default_unit_id', 'textInput', '1', 'Unidad por defecto..', '', 0, 6, 0),
(35, 'default_tax_rate_code', 'textInput', '5', 'Código AFIP de tasa impositiva por defecto', '', 0, 2, 0),
(37, 'mesa_server_address', 'textInput', 'http://localhost/', 'Direccion del servidor de mesa.', '', 0, 9, 0),
(38, 'ecopago_payout_limit', 'textInput', '0', 'Limite de pago.', '', 0, 10, 0),
(39, 'ecopago_money_box_id', 'textInput', '9', 'ecopago_money_box_id', '', 0, 10, 0),
(41, 'phone-st', 'textInput', '0261 4 200997 - WhatsApp: 261 5087213', 'Teléfono servicio técnico', '', 0, 16, 0),
(42, 'phone-admin', 'textInput', '0261 4 200997 - WhatsApp: 261 6547474', 'Teléfono administración', '', 0, 16, 0),
(43, 'phone-sellers', 'textInput', '0261 4 294321 - WhatsApp: 261 6607841', 'Teléfono administración', '', 0, 16, 0),
(44, 'mail-top-title', 'textInput', 'Westnet le informa', 'Ante título mailing', '', 0, 16, 0),
(45, 'bill_due_day', 'textInput', '', 'Dia de vencimiento de factura.', '', 0, 9, 0),
(46, 'instalation_category_id', 'textInput', '', 'Categoria de instalacion en Mesa.', '', 0, 9, 0),
(49, 'payment_method_cash', 'textInput', '1', 'Metodo de pago - Contado', '', 0, 1, 0),
(50, 'money_box_smallbox', 'textInput', '3', 'Tipo de cuenta CAJA', 'Tipo de cuenta monetaria CAJA', 0, 1, 0),
(51, 'max_number_ads_empty', 'textInput', '0', 'Numero de ultimo ADS impreso', '', 0, 11, 1),
(52, 'contract_days_for_invoice_next_month', 'textInput', '10', 'Dias del mes que posterga facturacion', '', 0, 9, 0),
(53, 'payed_months_before_penalty', 'textInput', '3', 'Meses con cliente activo requeridos', 'Cantidad de meses que un cliente debe pagar para que el vendedor no sea sancionado', 0, 17, 0),
(54, 'months-without-increase', 'textInput', '4', 'Meses sin aplicar aumento a clientes', 'Cantidad de meses durante lo cuales no se debe aumentar la tarifa de un cliente', 0, 9, 0),
(55, 'ecopago_batch_closure_company_id', 'textInput', '1', 'Empresa para rendiciones.', 'Empresa a la que se le asocian los movimientos de la rendicion.', 0, 10, 1),
(56, 'wkhtmltopdf_docker_host', 'textInput', 'http://westnet-pdf/', 'Host para servicio wkhtmltopdf ', '', 0, 11, 1),
(57, 'wkhtmltopdf_docker_port', 'textInput', '5001', 'Puerto para servicio wkhtmltopdf ', '', 0, 11, 1),
(58, 'ecopago_batch_closure_bill_type_id', 'textInput', '1', 'Tipo de comprobante de Factura Ecopago.', 'Tipo de comprobante con el que facturan los ecopagos.', 0, 10, 1),
(59, 'ecopago_batch_closure_debit_type_id', 'textInput', '1', 'Tipo de comprobante de Nota de debito Ecopago', 'Tipo de comprobante con el que se hacen las notas de debito de ecopagos.', 0, 10, 1),
(60, 'ecopago_batch_closure_credit_type_id', 'textInput', '1', 'Tipo de comprobante de Nota de Credito Ecopag', 'Tipo de comprobante con el que se hacen las notas de credito de ecopagos.', 0, 10, 1),
(61, 'ticket_new_status_id', 'textInput', '20', 'Id del estado Nuevo de ticket.', '', 0, 13, 0),
(62, 'times_forced_conn_month', 'textInput', '2', 'Cantidad de veces que se puede forzar una con', '', 0, 9, 1),
(63, 'app_testing', 'checkbox', '', 'Está la aplicación en modo testing?', '', 0, 11, 1),
(64, 'new_contracts_days', 'textInput', '0', 'Dias de contrato nuevo.', '', 0, 9, 1),
(68, 'credit-bill-category-id', 'textInput', '10', 'ID Categoria de ticket de nota de credito', 'Indica el ID de la categoria de nota de credito', 0, 13, 1),
(69, 'bill-category-id', 'textInput', '0', 'ID Categoria de ticket de factura', 'Indica el ID de la categoria de factura', 0, 13, 1),
(70, 'teleprom_testing_number', 'textInput', '', 'Nro de telefono para SMS de Prueba.', '', 0, 9, 0),
(71, 'parent_outflow_account', 'textInput', '', 'Cuenta de Egreso por defecto.', '', 0, 1, 0),
(72, 'referenced_discount', 'textInput', '0', 'Descuento por defecto para refenciados', 'Descuento por defecto para refenciados', 0, 9, 1),
(73, '815_ciudad_id', 'textInput', '34', 'Id de la ciudad por defecto en 815', '', 0, 9, 0),
(74, 'router_product_id', 'textInput', '22', 'Id Del producto Router', '', 0, 9, 0),
(75, 'cupon_bill_types', 'textInput', '6', 'Ids de Comprobantes que imprime Cupon', 'Ids de Comprobantes que imprime Cupon', 0, 2, 1),
(76, 'justification_length', 'textInput', '100', 'Cantidad de caracteres', 'Cantidad de caracteres mínimos para la justificación de una reimpresion o cancelación', 0, 10, 0),
(78, 'ads-title', 'textInput', 'SR CLIENTE LEA ATENTAMENTE LOS SIGUIENTE PUNTOS:', 'Título', 'Título de mensaje en ADS', 0, 19, 0),
(79, 'ads-message', 'textInput', '1. La Empresa no realiza período de prueba alguno, una vez instalado es asumido el compromiso de pago.                        2. Plazo mínimo de contrato: 6 meses..\r\n                            3. Los equipamientos instalados (antena, radio, fuente, c', 'Mensaje', 'Mensaje en ADS', 0, 19, 0),
(80, 'pagomiscuentas-payment-method', 'textInput', '9', 'Metodo de pago por defecto para Pagomiscuenta', 'Metodo de pago por defecto para Pagomiscuentas', 0, 20, 1),
(81, 'companies_without_bills', 'textInput', '', 'Ids de Empresas sin Facturación', 'Ids de Empresas sin Facturación', 0, 2, 1),
(82, 'mesa_category_low_reason', 'textInput', '15', 'Categoria princiapal de Baja en Mesa', '', 0, 9, 0),
(83, 'private_token', 'textInput', '123456', 'Token para peticiones a la Api de mobile app', '', 0, 21, 1),
(84, 'validation_code_expire', 'textInput', '10', 'Duración del código de validación', 'Tiempo de duracion en minutos del código de validación de los usuarios de la app', 0, 21, 1),
(85, 'auth_token_duration', 'textInput', '30', 'Duración del Auth Token', 'Tiempo de duración en dias del Auth Token de los usuarios de la app', 0, 21, 1),
(86, 'one_signal_app_id', 'textInput', '', 'App ID de One Signal', '', 0, 21, 1),
(87, 'one_signal_rest_key', 'textInput', '', 'Rest Api Key de One Signal', '', 0, 21, 1),
(88, 'invoice_mobile_push_content', 'textInput', 'Su nueva factura ya está disponible', 'Texto para notificación al facturar', 'Texto que se mostrara en la notificación de la app cuando se genere una factura', 0, 21, 1),
(89, 'sms_api_username', 'textInput', 'quoma', 'Usuario Api SMS', '', 0, 21, 1),
(90, 'sms_api_password', 'textInput', 'quoma2018', 'Password Api SMS', '', 0, 21, 1),
(91, 'sms_api_url', 'textInput', '', 'Url Api SMS', 'Url al endpoint de la api para enviar sms.', 0, 21, 1),
(92, 'sms_validation_content', 'textInput', 'Gracias por registrarse en nuestra app. Su código de validación es: {code}', 'Contenido de SMS de validación', 'El contenido deberá tener la referencia \"{code}\" en el lugar donde debe ir el código', 0, 21, 1),
(93, 'enable_send_sms', 'checkbox', '0', 'Habilitar envío de SMS', 'Habilita o deshabilita el envio de sms de validación', 0, 21, 1),
(94, 'ecopagos_company_id', 'textInput', '0', 'ID Company con ecopagos', 'ID de la empresa que usa los ecopagos', 0, 21, 1),
(116, 'ads-time_technical_service', 'textInput', 'LUN. A VIE. 09:00 A 17:00 HS – SAB 09:00 A 13:00 HS', 'Horarios de atención del servicio técnico', '', 0, 19, 0),
(119, 'integratech_username', 'textInput', '', 'Nombre de usuario de Itegratech', 'Nombre de usuario para el servicio de integratech', 0, 22, 0),
(120, 'integratech_password', 'textInput', '', 'Contraseña de usuario de Itegratech', 'Contraseña de usuario para el servicio de integratech', 0, 22, 0),
(121, 'integratech_url', 'textInput', '', 'Url de servicio Itegratech', 'Url de servicio de Integratech desde donde se van a hacer envios de los SMS', 0, 22, 0),
(132, 'receipt_invoice_class', 'textInput', '1', 'Clase invoice', 'Clase que va a usar para presentar recibos en AFIP', 0, 2, 1),
(133, 'general_address', 'textInput', '9 de julio 1257 Of.108  - P10', 'Dirección genérica, usada por ejemplo en los ', '', 0, 11, 0),
(135, 'add_retenciones_into_in_out_report', 'checkbox', '1', '¿Añadir item \'Retenciones\' en reporte?', 'Indica si se va a añadir el item \'Retenciones\' en el reporte \'Ingresos y Egresos\'', 0, 11, 0),
(136, 'is_developer_mode', 'checkbox', '0', '¿Esta en modo de desarrollador?', 'Si esta activado evita ciertas acciones como la comunicacion con mesa al momento de crear un contrato', 0, 11, 0),
(137, 'require_update_customer_data', 'textInput', '24', 'Indica cada cuántos meses se va a requerir un', 'Indica cada cuántos meses va a requerir una actualización de los datos del cliente. Tiempo expresado en meses', 0, 7, 0),
(138, 'cobranza_category_id', 'textInput', '101', 'Categoria de Cobranzas', 'Indica la categoria de tickets de cobranza', 0, 13, 0),
(143, 'extend_payment_product_id', 'textInput', '0', 'ID Producto para extensión de pago', 'Producto para asignarle al adicional que se crea al realizar el forzado de la conexión', 0, 6, 0),
(144, 'technical_service_phone', 'textInput', '123456', 'asss', 'sss', NULL, 11, NULL),
(145, 'notification-replace-@Nombre', 'textInput', '20', 'Indica cantidad de caracteres @Nombre', 'Indica cada cuántos caracteres va a significar este campo al momento del reemplazo', 0, 22, 0),
(146, 'notification-replace-@Telefono1', 'textInput', '13', 'Indica cantidad de caracteres @Telefono1', 'Indica cada cuántos caracteres va a significar este campo al momento del reemplazo', 0, 22, 0),
(147, 'notification-replace-@Telefono2', 'textInput', '13', 'Indica cantidad de caracteres @Telefono2', 'Indica cada cuántos caracteres va a significar este campo al momento del reemplazo', 0, 22, 0),
(148, 'notification-replace-@Codigo', 'textInput', '14', 'Indica cantidad de caracteres @Codigo', 'Indica cada cuántos caracteres va a significar este campo al momento del reemplazo', 0, 22, 0),
(149, 'notification-replace-@CodigoDePago', 'textInput', '14', 'Indica cantidad de caracteres', 'Indica cada cuántos caracteres va a significar este campo al momento del reemplazo', 0, 22, 0),
(150, 'notification-replace-@CodigoEmpresa', 'textInput', '4', 'Indica cantidad de caracteres @CodigoEmpresa', 'Indica cada cuántos caracteres va a significar este campo al momento del reemplazo', 0, 22, 0),
(151, 'notification-replace-@FacturasAdeudadas', 'textInput', '2', 'Indica cantidad de caracteres @FacturasAdeuda', 'Indica cada cuántos caracteres va a significar este campo al momento del reemplazo', 0, 22, 0),
(152, 'notification-replace-@Saldo', 'textInput', '8', 'Indica cantidad de caracteres @Saldo', 'Indica cada cuántos caracteres va a significar este campo al momento del reemplazo', 0, 22, 0),
(153, 'notification-replace-@Categoria', 'textInput', '10', 'Indica cantidad de caracteres @Categoria', 'Indica cada cuántos caracteres va a significar este campo al momento del reemplazo', 0, 22, 0),
(154, 'notification-replace-@Nodo', 'textInput', '10', 'Indica cantidad de caracteres @Nodo', 'Indica cada cuántos caracteres va a significar este campo al momento del reemplazo', 0, 22, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rule`
--

CREATE TABLE `rule` (
  `rule_id` int(11) NOT NULL,
  `message` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `max` double DEFAULT NULL,
  `min` double DEFAULT NULL,
  `pattern` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `format` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `targetAttribute` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `targetClass` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `item_id` int(11) NOT NULL,
  `validator` varchar(45) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `rule`
--

INSERT INTO `rule` (`rule_id`, `message`, `max`, `min`, `pattern`, `format`, `targetAttribute`, `targetClass`, `item_id`, `validator`) VALUES
(1, '', NULL, 0, NULL, NULL, NULL, NULL, 3, 'integer'),
(2, '', 4000, 1, NULL, NULL, NULL, NULL, 13, 'integer'),
(3, '', 4000, 1, NULL, NULL, NULL, NULL, 14, 'integer'),
(4, '', 4000, 1, NULL, NULL, NULL, NULL, 15, 'integer'),
(5, '', 5000, 1, NULL, NULL, NULL, NULL, 16, 'integer'),
(6, '', 5000, 1, NULL, NULL, NULL, NULL, 17, 'integer'),
(7, '', 1, 0, NULL, NULL, NULL, NULL, 18, 'double'),
(8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 24, 'string'),
(9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 24, 'required'),
(10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'string'),
(11, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25, 'required'),
(12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 26, 'string'),
(13, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 26, 'required'),
(14, '', 100, 1, '', '', '', '', 32, 'number'),
(15, '', NULL, NULL, '', '', '', '', 32, 'required'),
(16, '', 100, 1, '', '', '', '', 33, 'number'),
(17, '', NULL, NULL, '', '', '', '', 33, 'required');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indices de la tabla `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`config_id`),
  ADD KEY `fk_config_item_idx` (`item_id`);

--
-- Indices de la tabla `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`item_id`),
  ADD UNIQUE KEY `attr` (`attr`),
  ADD KEY `fk_item_category1` (`category_id`);

--
-- Indices de la tabla `rule`
--
ALTER TABLE `rule`
  ADD PRIMARY KEY (`rule_id`),
  ADD KEY `fk_rule_item1_idx` (`item_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `config`
--
ALTER TABLE `config`
  MODIFY `config_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT de la tabla `item`
--
ALTER TABLE `item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;

--
-- AUTO_INCREMENT de la tabla `rule`
--
ALTER TABLE `rule`
  MODIFY `rule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `config`
--
ALTER TABLE `config`
  ADD CONSTRAINT `fk_config_item_idx` FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`);

--
-- Filtros para la tabla `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `fk_item_category1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`);

--
-- Filtros para la tabla `rule`
--
ALTER TABLE `rule`
  ADD CONSTRAINT `fk_rule_item1_idx` FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
