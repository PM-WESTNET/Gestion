
--
-- Volcado de datos para la tabla `invoice_class`
--

INSERT INTO `invoice_class` (`invoice_class_id`, `class`, `name`) VALUES
(1, 'app\\modules\\invoice\\components\\einvoice\\afip\\fev1\\Fev1', 'Fev 1 Afip');

--
-- Volcado de datos para la tabla `bill_type`
--

INSERT INTO `bill_type` (`bill_type_id`, `name`, `code`, `type`, `view`, `multiplier`, `customer_required`, `invoice_class_id`) VALUES
(1, 'Factura A', 1, '', 'default', 1, 1, 1),
(2, 'Factura B', 6, '', 'final', 1, 0, 1),
(3, 'Nota de Débito A ', 2, '', 'default', 1, 1, 1),
(4, 'Nota de Crédito A ', 3, '', 'default', -1, 1, 1),
(5, 'Nota de Débito B ', 7, '', 'final', 1, 0, 1),
(6, 'Nota de Crédito B', 8, '', 'final', -1, 0, 1),
(7, 'Presupuesto', 105, '', 'default', 0, 0, null);

--
-- Volcado de datos para la tabla `currency`
--

INSERT INTO `currency` (`currency_id`, `name`, `iso`, `rate`, `status`, `code`) VALUES
(1, 'Peso', 'ARS', 1, 'enabled', 'ARS'),
(3, 'Dolar estado unidense', 'USD', 13, 'enabled', 'USD');

--
-- Volcado de datos para la tabla `document_type`
--

INSERT INTO `document_type` (`document_type_id`, `name`, `code`, `regex`) VALUES
(1, 'CUIT', 80, ''),
(2, 'DNI', 96, ''),
(3, 'LE', 89, ''),
(4, 'LC', 90, ''),
(5, 'CI Extranjera ', 91, ''),
(6, 'Pasaporte', 94, '');

--
-- Volcado de datos para la tabla `tax_condition`
--

INSERT INTO `tax_condition` (`tax_condition_id`, `name`, `document_type_id`, `exempt`) VALUES
(1, 'IVA Inscripto', 1, 0),
(2, 'Consumidor Final', 2, 0),
(3, 'Exento', 1, 1);

--
-- Volcado de datos para la tabla `tax_condition_has_bill_type`
--

INSERT INTO `tax_condition_has_bill_type` (`tax_condition_id`, `bill_type_id`, `order`) VALUES
(1, 1, NULL),
(1, 3, NULL),
(1, 4, NULL),
(1, 7, NULL),
(2, 2, NULL),
(2, 5, NULL),
(2, 6, NULL),
(2, 7, NULL),
(3, 2, NULL),
(3, 5, NULL),
(3, 6, NULL),
(3, 7, NULL);

--
-- Volcado de datos para la tabla `company`
--

INSERT INTO `company` (`company_id`, `name`, `status`, `tax_identification`, `address`, `phone`, `email`, `parent_id`, `certificate`, `key`, `create_timestamp`, `tax_condition_id`, `start`, `iibb`, `default`) VALUES
(1, 'ACME', 'enabled', '30537466525', '', '', '', NULL, '', '', 1441382211, 1, NULL, NULL, 1),
(2, 'Metro', 'enabled', '30693683838', '', '', '', NULL, '', '', 1441382211, 1, NULL, NULL, 1);

--
-- Volcado de datos para la tabla `company_has_bill_type`
--

INSERT INTO `company_has_bill_type` (`company_id`, `bill_type_id`) VALUES
(1, 1),
(1, 3),
(1, 4),
(1, 7),
(2, 2),
(2, 5),
(2, 6),
(2, 7);

--
-- Volcado de datos para la tabla `point_of_sale`
--

INSERT INTO `point_of_sale` (`point_of_sale_id`, `name`, `number`, `status`, `description`, `company_id`, `default`) VALUES
(1, 'Central', 1, 'enabled', 'Casa central', 1, 1);

--
-- Volcado de datos para la tabla `customer_category`
--

INSERT INTO `customer_category` (`customer_category_id`, `name`, `status`, `parent_id`) VALUES
(1, 'Particular', 'enabled', NULL),
(2, 'Empresa', 'enabled', NULL);

--
-- Volcado de datos para la tabla `customer`
--

INSERT INTO `customer` (`customer_id`, `name`, `lastname`, `document_number`, `sex`, `email`, `phone`, `address`, `status`, `document_type_id`, `account_id`, `company_id`, `tax_condition_id`) VALUES
(1, 'Juan', 'Garcia', '23123456790', NULL, '', '', '', 'enabled', 1, 115, 1, 1),
(2, 'José', 'Gómez', '12345678', NULL, '', '', '', 'enabled', 1, 115, 2, 2);

--
-- Volcado de datos para la tabla `money_box_type`
--

INSERT INTO `money_box_type` (`money_box_type_id`, `name`, `code`) VALUES
(1, 'Banco', 'B');

--
-- Volcado de datos para la tabla `payment_method`
--

INSERT INTO `payment_method` (`payment_method_id`, `name`, `status`, `register_number`, `type`) VALUES
(1, 'Contado', 'enabled', 0, 'exchanging'),
(2, 'Tarjeta de Crédito', 'enabled', 0, 'provisioning'),
(3, 'Tarjeta de Débito', 'enabled', 0, 'provisioning'),
(4, 'Cuenta Corriente ', 'enabled', 0, 'account'),
(5, 'Cheques', 'enabled', 1, 'provisioning');

--
-- Volcado de datos para la tabla `profile_class`
--

INSERT INTO `profile_class` (`profile_class_id`, `name`, `data_type`, `data_max`, `data_min`, `pattern`, `status`, `order`, `multiple`, `default`, `hint`, `searchable`) VALUES
(1, 'Código de Provincia', 'textInput', 0, '0', '', 'enabled', 0, 0, '', 'Código de Filtrado de provincias', 1);

--
-- Volcado de datos para la tabla `tax`
--

INSERT INTO `tax` (`tax_id`, `name`, `slug`, `required`) VALUES
(1, 'IVA', 'iva', 1);

--
-- Volcado de datos para la tabla `tax_rate`
--

INSERT INTO `tax_rate` (`tax_rate_id`, `pct`, `tax_id`, `code`) VALUES
(1, 0.21, 1, 5);

--
-- Volcado de datos para la tabla `unit`
--

INSERT INTO `unit` (`unit_id`, `name`, `type`, `symbol`, `symbol_position`, `code`) VALUES
(1, 'Cantidad', 'int', 'c.', 'suffix', 0);

--
-- Volcado de datos para la tabla `product`
--

INSERT INTO `product` (`product_id`, `name`, `system`, `code`, `description`, `status`, `balance`, `create_timestamp`, `update_timestamp`, `unit_id`, `type`, `uid`) VALUES
(1, 'Manzana', 'manzana', '55e85a524c969', '', 'enabled', NULL, 1441290834, 1441290834, 1, 'product', NULL);

--
-- Volcado de datos para la tabla `product_price`
--

INSERT INTO `product_price` (`product_price_id`, `net_price`, `taxes`, `date`, `time`, `timestamp`, `exp_timestamp`, `exp_date`, `exp_time`, `update_timestamp`, `status`, `product_id`, `purchase_price`) VALUES
(1, NULL, 0, '2015-09-03', '18:15:00', 1441304123, -1, NULL, NULL, NULL, 'updated', 1, NULL);
