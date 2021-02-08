<?php

return [
    
    'user' => [
        'class' => '\webvimark\modules\UserManagement\models\User',
        'idAttribute' => 'id'
    ],
    
    'task' => [
        'default_status' => '1',
        'default_priority' => '2',
        'work_hours_quantity' => '10:00',
        'work_hours_start' => '08:00',
        'work_hours_end' => '18:00',
    ],
    
    'check_expiration_timeout' => 28800, //8 Horas
    'check_expiration_timeout_test' => 10,
    'check_expiration_on_login' => true
    
];
