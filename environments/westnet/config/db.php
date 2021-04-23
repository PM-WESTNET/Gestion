<?php

return [
    'db' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=gestion-data;dbname=gestion_westnet',
        'username' => 'root',
        'password' => 'secret',
        'charset' => 'utf8',
    ],
    'dbafip' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=gestion-data;dbname=gestion_afip',
        'username' => 'root',
        'password' => 'secret',
        'charset' => 'utf8',
    ],
    'dbconfig' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=gestion-data;dbname=gestion_config',
        'username' => 'root',
        'password' => 'secret',
        'charset' => 'utf8',
    ],
    'dbagenda' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=gestion-data;dbname=gestion_agenda',
        'username' => 'root',
        'password' => 'secret',
        'charset' => 'utf8',
    ],
    'dbticket' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=gestion-data;dbname=gestion_ticket',
        'username' => 'root',
        'password' => 'secret',
        'charset' => 'utf8',
    ],
    'dbecopago' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=gestion-data;dbname=gestion_ecopago',
        'username' => 'root',
        'password' => 'secret',
        'charset' => 'utf8',
    ],
    'dbnotifications' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=gestion-data;dbname=gestion_notifications',
        'username' => 'root',
        'password' => 'secret',
        'charset' => 'utf8',
    ],
    'dblog' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=gestion-data;dbname=gestion_log',
        'username' => 'root',
        'password' => 'secret',
        'charset' => 'utf8',
    ],
    'dbmedia' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=gestion-data;dbname=gestion_media',
        'username' => 'root',
        'password' => 'secret',
        'charset' => 'utf8',
    ],
    'dbbackups' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=gestion-data;dbname=rex-backups',
        'username' => 'root',
        'password' => 'secret',
        'charset' => 'utf8',
    ],
];
