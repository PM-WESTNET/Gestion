<?php

return [
    'db' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=westnet',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
    ],
    'dbafip' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=arya_afip',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
    ],
    'dbconfig' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=config',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
    ],
    'dbagenda' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=arya_agenda',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
    ],
    'dbticket' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=arya_ticket',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
    ],
    'dbecopago' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=arya_ecopago',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
    ],
    'dbnotifications' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=arya_notifications',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
    ],
    'dblog' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=arya_log',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
    ],
    'dbmedia' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=media',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
    ],
    'dbteleprom' => [
        'class' => 'yii\db\Connection',
#        'dsn' => 'mysql:host=10.11.101.1;dbname=telepromdb',
        'dsn' => 'mysql:host=172.28.0.10;dbname=telepromdb',
        'username' => 'root',
        'password' => '123456',
        'charset' => 'utf8',
    ]
];
