SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `name` varchar(45) CHARACTER SET latin1 NOT NULL,
  `status` enum('enabled','disabled') CHARACTER SET latin1 DEFAULT NULL,
  `superadmin` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
(18, 'Pagomiscuentas', 'enabled', NULL),
(19, 'Mobile App', 'enabled', NULL),
(20, 'ADS', 'enabled', NULL);

CREATE TABLE `config` (
  `config_id` int(11) NOT NULL,
  `value` text CHARACTER SET latin1,
  `item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `config` (`config_id`, `value`, `item_id`) VALUES
(1, '4', 1),
(2, '1', 2),
(3, '1', 8),
(4, '28800', 9),
(5, '10:00', 12),
(6, '1', 19),
(7, '', 4),
(8, '0', 6),
(9, '0', 7),
(10, '1', 32),
(11, '14', 3),
(12, '1', 5),
(13, '5', 33),
(14, '6', 62),
(15, 'idlipbhoabgfdjkbpicgjjidfmgfcnmj', 26);

CREATE TABLE `item` (
  `item_id` int(11) NOT NULL,
  `attr` varchar(45) CHARACTER SET latin1 NOT NULL,
  `type` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `default` text,
  `label` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `description` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `multiple` tinyint(1) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `superadmin` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
(76, 'pagomiscuentas-payment-method', 'textInput', '9', 'Metodo de pago por defecto para Pagomiscuenta', 'Metodo de pago por defecto para Pagomiscuentas', 0, 18, 1),
(77, 'mesa_negative_survey_id', 'textInput', '82', 'Categoria de Relevamiento Negativo', '', 0, 13, 0),
(78, 'private_token', 'textInput', '123456', 'Token para peticiones a la Api de mobile app', '', 0, 19, 1),
(79, 'validation_code_expire', 'textInput', '10', 'Duración del código de validación', 'Tiempo de duracion en minutos del código de validación de los usuarios de la app', 0, 19, 1),
(80, 'auth_token_duration', 'textInput', '30', 'Duración del Auth Token', 'Tiempo de duración en dias del Auth Token de los usuarios de la app', 0, 19, 1),
(81, 'one_signal_app_id', 'textInput', '', 'App ID de One Signal', '', 0, 19, 1),
(82, 'one_signal_rest_key', 'textInput', '', 'Rest Api Key de One Signal', '', 0, 19, 1),
(83, 'invoice_mobile_push_content', 'textInput', 'Su nueva factura ya está disponible', 'Texto para notificación al facturar', 'Texto que se mostrara en la notificación de la app cuando se genere una factura', 0, 19, 1),
(84, 'sms_api_username', 'textInput', 'quoma', 'Usuario Api SMS', '', 0, 19, 1),
(85, 'sms_api_password', 'textInput', 'quoma2018', 'Password Api SMS', '', 0, 19, 1),
(86, 'sms_api_url', 'textInput', '', 'Url Api SMS', 'Url al endpoint de la api para enviar sms.', 0, 19, 1),
(87, 'sms_validation_content', 'textInput', 'Gracias por registrarse en nuestra app. Su código de validación es: {code}', 'Contenido de SMS de validación', 'El contenido deberá tener la referencia \"{code}\" en el lugar donde debe ir el código', 0, 19, 1),
(88, 'enable_send_sms', 'checkbox', '0', 'Habilitar envío de SMS', 'Habilita o deshabilita el envio de sms de validación', 0, 19, 1),
(89, 'ecopagos_company_id', 'textInput', '0', 'ID Company con ecopagos', 'ID de la empresa que usa los ecopagos', 0, 19, 1),
(90, 'mesa_category_low_reason', 'textInput', '15', 'Categoria princiapal de Baja en Mesa', '', 0, 9, 0),
(91, 'justification_length', 'textInput', '100', 'Cantidad de caracteres', 'Cantidad de caracteres mínimos para la justificación de una reimpresion o cancelación', 0, 10, 0),
(92, 'companies_without_bills', 'textInput', '', 'Ids de Empresas sin Facturación', 'Ids de Empresas sin Facturación', 0, 2, 1),
(93, 'ads-title', 'textInput', 'SR CLIENTE LEA ATENTAMENTE LOS SIGUIENTE PUNTOS:', 'Título', 'Título de mensaje en ADS - Para reemplazar el nombre de la Empresa coloque @Empresa', 0, 20, 0),
(94, 'ads-message', 'html', '  <p>1. La Empresa no realiza per&iacute;odo de prueba alguno, una vez instalado es asumido el compromiso de pago.<br />\n                            2. Plazo m&iacute;nimo de contrato: 6 meses..<br />\n                            3. Los equipamientos instalados (antena, radio, fuente, cables) son en comodato (propiedad de la Empresa @Empresa).<br />\n                            4. Plazo de pago de la primer cuota monto a definir por administraci&oacute;n: de 24 hs..<br />\n                            5. El ancho de banda m&iacute;nimo garantizado (Mbps m&iacute;nima ofrecida) se encuentra detallado en el presente documento.<br />\n                            6. El primer pago debe realizarse como m&aacute;ximo 24 hs despues de realizada la instalacion..<br />\n                            7. En caso de adquirir router WiFi, el mismo tiene un costo adicional y posee una garant&iacute;a de 3 meses desde el d&iacute;a que se instala.</p>', 'Mensaje', 'Mensaje en ADS', 0, 20, 0),
(95, 'ads-contact_technical_service', 'textInput', 'TEL: 02614200997 OPCIÓN 3 / 2615087213 ', 'Teléfonos de contacto del servicio técnico', '', 0, 20, 0),
(96, 'ads-time_technical_service', 'textInput', 'LUN. A VIE. 09:00 A 17:00 HS – SAB 09:00 A 13:00 HS', 'Horarios de atención del servicio técnico', '', 0, 20, 0),
(97, 'ads-comercial-office', 'textInput', 'TEL. 0261 4 200997 OPCIÓN 1 / 261 6547474', 'Datos de oficina comercial', '', 0, 20, 0),
(98, 'ads-email_technical_service', 'textInput', 'soporte@westnet.com.ar', 'Mail del servicio técnico', '', 0, 20, 0);

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


ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

ALTER TABLE `config`
  ADD PRIMARY KEY (`config_id`),
  ADD KEY `fk_config_item_idx` (`item_id`);

ALTER TABLE `item`
  ADD PRIMARY KEY (`item_id`),
  ADD UNIQUE KEY `attr` (`attr`),
  ADD KEY `fk_item_category1` (`category_id`);

ALTER TABLE `rule`
  ADD PRIMARY KEY (`rule_id`),
  ADD KEY `fk_rule_item1_idx` (`item_id`);


ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `config`
  MODIFY `config_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `rule`
  MODIFY `rule_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `config`
  ADD CONSTRAINT `fk_config_item_idx` FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`);

ALTER TABLE `item`
  ADD CONSTRAINT `fk_item_category1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`);

ALTER TABLE `rule`
  ADD CONSTRAINT `fk_rule_item1_idx` FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
