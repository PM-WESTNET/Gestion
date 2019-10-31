<?php
$payment_method = [
    ['payment_method_id' => 1,'name' => 'Contado','status' => 'enabled','register_number' => '1','type' => 'exchanging', 'send_ivr' => true],
    ['payment_method_id' => 2,'name' => 'Cheque','status' => 'enabled','register_number' => '2','type' => 'exchanging', 'send_ivr' => true],
    ['payment_method_id' => 3,'name' => 'Transferencia (Por Migración]','status' => 'disabled','register_number' => '0','type' => 'exchanging', 'send_ivr' => false],
    ['payment_method_id' => 4,'name' => 'Transferencia','status' => 'enabled','register_number' => '0','type' => 'exchanging', 'send_ivr' => true],
    ['payment_method_id' => 5,'name' => 'Descuento','status' => 'enabled','register_number' => '5','type' => 'exchanging', 'send_ivr' => false],
    ['payment_method_id' => 6,'name' => 'Ecopago','status' => 'enabled','register_number' => '6','type' => 'exchanging', 'send_ivr' => true],
    ['payment_method_id' => 7,'name' => 'Retención','status' => 'enabled','register_number' => '7','type' => 'exchanging','send_ivr' => false],
    ['payment_method_id' => 8,'name' => 'Pago Facil','status' => 'enabled','register_number' => '1','type' => 'exchanging', 'send_ivr' => true],
    ['payment_method_id' => 9,'name' => 'Lapos Web','status' => 'enabled','register_number' => '1','type' => 'exchanging', 'send_ivr' => true],
    ['payment_method_id' => 10,'name' => 'Pagomiscuentas','status' => 'enabled','register_number' => '0','type' => 'exchanging', 'send_ivr' => true],
    ['payment_method_id' => 11,'name' => 'Lapos','status' => 'enabled','register_number' => '0','type' => 'exchanging', 'send_ivr' => false]
];

return $payment_method;