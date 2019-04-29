<?php

return [

    'user' => [
        'class' => '\webvimark\modules\UserManagement\models\User',
        'idAttribute' => 'id'
    ],
    
    //Default payment method
    'payment_method' => 'Contado',
    
    //Ecopago chrome printing app
    'chrome_app_id' => 'hkjdkiaahbmeaojdjiaijemnchnemjpi',
];
