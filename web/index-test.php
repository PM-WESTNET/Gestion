<?php

$allowedIPs = ['127.0.0.1', '::1'];

for ($j = 0; $j <= 255; $j++) {
    for ($i = 0; $i <= 255; $i++) {
        // Docker IPs
        $allowedIPs[] = "172.$j.0.$i";
        // Intranet IPs
        $allowedIPs[] = "192.168.$j.$i";
    }
}

// NOTE: Make sure this file is not accessible when deployed to production
if (!in_array(@$_SERVER['REMOTE_ADDR'], $allowedIPs)) {
    die('You are not allowed to access this file.');
}

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require __DIR__ . '/../config/test.php';

(new yii\web\Application($config))->run();
