<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');
Yii::setAlias('@webvimark', dirname(__DIR__) . '/modules/UserManagement/controllers');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

return [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'agenda',
        'ticket',
        'zone',
        'westnet',
        'accounting',
        'reports'
    ],
    'controllerNamespace' => 'app\commands',
    'extensions' => require(__DIR__ . '/../vendor/yiisoft/extensions.php'),
    'components' => [
        'user' => [
            'class' => 'webvimark\modules\UserManagement\components\UserConfig',
            // Comment this if you don't want to record user logins
            'on afterLogin' => function($event) {
                if(app\modules\westnet\ecopagos\frontend\helpers\UserHelper::isCashier()){
                    header('Location: '. \yii\helpers\Url::to(['/westnet/ecopagos/frontend/site/index', true]));
                    die();
                }
                \webvimark\modules\UserManagement\models\UserVisitLog::newVisitor($event->identity->id);
            }
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db['db'],
        'dbafip' => $db['dbafip'],
        'dbconfig' => $db['dbconfig'],
        'dbagenda' => $db['dbagenda'],
        'dbticket' => $db['dbticket'],
        'dbecopago' => $db['dbecopago'],
        'dbnotifications' => $db['dbnotifications'],
        'dblog' => $db['dblog'],
        'dbmedia' => $db['dbmedia'],
        'i18n' => [
            'translations' => [
                'app' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                ],
                'help' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                ],
                'import' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                ],
                'afip' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                ],
                'accounting' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                ],
                'paycheck' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                ],
                'westnet' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                ],
                'ticket' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                ],
                'partner' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                ],
                'report' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                ],
                'pagomiscuentas' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                ],
                'modules/user-management/*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'en',
                    'fileMap' => [
                        'modules/user-management/back' => 'modules/user-management/back.php',
                        'modules/user-management/front' => 'modules/user-management/front.php',
                    ],
                ],
            ]
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'dateFormat' => 'php:d-m-Y',
            'datetimeFormat' => 'php:d-m-Y H:i:s',
            'timeFormat' => 'php:H:i:s',
            'decimalSeparator' => ',',
            'thousandSeparator' => '.',
            //'currencyCode' => 'ARS',
        ],
        'mail' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'mutex' => [
            'class' => 'yii\mutex\FileMutex'
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'baseUrl' => 'https://gestion.westnet.com.ar',
            'hostInfo' => 'https://gestion.westnet.com.ar',

        ]
    ],
    'modules' => [
        'sale' => [
            'class' => 'app\modules\sale\SaleModule'
        ],
        'zone' => [
            'class' => 'app\modules\zone\ZoneModule'
        ],
        'gridview' => [
            'class' => '\kartik\grid\Module',
        ],
        'import' => [
            'class' => 'app\modules\import\ImportModule',
        ],
        'checkout' => [
            'class' => 'app\modules\checkout\CheckoutModule',
        ],
        'provider' => [
            'class' => 'app\modules\provider\ProviderModule',
        ],
        'backup' => [
            'class' => 'app\modules\backup\BackupModule',
        ],
        'invoice' => [
            'class' => 'app\modules\invoice\InvoiceModule',
        ],
        'media' => [
            'class' => 'app\modules\media\MediaModule',
        ],
        'accounting' => [
            'class' => 'app\modules\accounting\AccountingModule',
        ],
        'westnet' => [
            'class' => 'app\modules\westnet\WestnetModule',
        ],
        'afip' => [
            'class' => 'app\modules\afip\AfipModule',
        ],
        'paycheck' => [
            'class' => 'app\modules\paycheck\PaycheckModule',
        ],
        'ecopagos' => [
            'class' => 'app\modules\westnet\ecopagos\EcopagosModule',
        ],
        'partner' => [
            'class' => 'app\modules\partner\PartnerModule',
        ],
        'reports' => [
            'class' => 'app\modules\westnet\reports\ReportsModule',
        ],
        'user-management' => [
            'class' => 'webvimark\modules\UserManagement\UserManagementModule',
            // Here you can set your handler to change layout for any controller or action
            // Tip: you can use this event in any module
            'on beforeAction' => function(yii\base\ActionEvent $event) {
                if ($event->action->uniqueId == 'user-management/auth/login') {
                    $event->action->controller->layout = '/login';
                };
            },
        ],
        'config' => [
            'class' => 'app\modules\config\ConfigModule'
        ],
        'agenda' => [
            'class' => 'app\modules\agenda\AgendaModule',
        ],
        'sequre' => [
            'class' => 'app\modules\westnet\sequre\SequreModule',
        ],
        'ticket' => [
            'class' => 'app\modules\ticket\TicketModule',
        ],
        'mailing' => [
            'class' => 'quoma\modules\mailing\MailingModule',
        ],
        'pagomiscuentas' => [
            'class' => 'app\modules\pagomiscuentas\PagomiscuentasModule',
        ]
    ],

    'params' => $params,
];
