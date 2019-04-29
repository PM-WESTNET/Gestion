<?php

return [
    
    'user' => [
        'class' => '\webvimark\modules\UserManagement\models\User',
        'idAttribute' => 'id'
    ],
    
    'ticket' => [
        'default_status' => '1',
    ],
    
    //Expiration for open tickets (in days)
    'expiration_timeout' => 10,
    
    //Pagination limit for observations
    'pagination_limit' => 5
    
];
