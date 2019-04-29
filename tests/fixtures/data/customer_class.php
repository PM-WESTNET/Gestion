<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 13/12/18
 * Time: 17:19
 */

$customer_class = [
    ['customer_class_id' => 1, 'name' => 'Basico', 'code_ext' => '1', 'is_invoiced' => '1', 'tolerance_days' => '1', 'colour' => '#3434f1', 'percentage_bill' => '100', 'days_duration' => '5', 'service_enabled' => '1', 'percentage_tolerance_debt' => '10', 'status' => 'enabled'],
    ['customer_class_id' => 2, 'name' => 'Free', 'code_ext' => '2', 'is_invoiced' => '0', 'tolerance_days' => '100', 'colour' => '#a3e3a3', 'percentage_bill' => '0', 'days_duration' => '100', 'service_enabled' => '1', 'percentage_tolerance_debt' => '100', 'status' => 'enabled'],
    ['customer_class_id' => 3, 'name' => 'VIP', 'code_ext' => '3', 'is_invoiced' => '1', 'tolerance_days' => '60', 'colour' => '#e87ae8', 'percentage_bill' => '100', 'days_duration' => '110', 'service_enabled' => '1', 'percentage_tolerance_debt' => '6', 'status' => 'enabled'],
    ['customer_class_id' => 4, 'name' => 'Mantenimiento', 'code_ext' => '4', 'is_invoiced' => '1', 'tolerance_days' => '60', 'colour' => '#ff0000', 'percentage_bill' => '50', 'days_duration' => '60', 'service_enabled' => '1', 'percentage_tolerance_debt' => NULL, 'status' => 'enabled'],
    ['customer_class_id' => 5, 'name' => 'Sin categoría', 'code_ext' => '4', 'is_invoiced' => '1', 'tolerance_days' => '60', 'colour' => '#ff0000', 'percentage_bill' => '100', 'days_duration' => '60', 'service_enabled' => '1', 'percentage_tolerance_debt' => NULL, 'status' => 'enabled'],
];

return $customer_class;