<?php

$account_config = [
    ['account_config_id' => '1','name' => 'Facturar','class' => 'app\\modules\\sale\\models\\Bill','classMovement' => 'app\modules\accounting\components\impl\BillMovement'],
    ['account_config_id' => '2','name' => 'Cobro','class' => 'app\\modules\\checkout\\models\\Payment','classMovement' => 'app\modules\accounting\components\impl\PaymentMovement'],
    ['account_config_id' => '3','name' => 'Factura Proveedores','class' => 'app\\modules\\provider\\models\\ProviderBill','classMovement' => 'app\modules\accounting\components\impl\ProviderBillMovement'],
    ['account_config_id' => '4','name' => 'Pago a Proveedores','class' => 'app\\modules\\provider\\models\\ProviderPayment','classMovement' => 'app\modules\accounting\components\impl\ProviderPaymentMovement'],
    ['account_config_id' => '5','name' => 'Ecopagos','class' => 'app\\modules\\westnet\\ecopagos\\models\\BatchClosure','classMovement' => 'app\modules\westnet\ecopagos\components\accounting\BatchClosureMovement'],
    ['account_config_id' => '6','name' => 'Cheques','class' => 'app\\modules\\paycheck\\models\\Paycheck','classMovement' => 'app\modules\paycheck\components\PaycheckMovement'],
];


return $account_config;