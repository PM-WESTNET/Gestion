<?php

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

\Yii::$container->set('yii\widgets\LinkPager', [
    'firstPageLabel' => "<span class='glyphicon glyphicon-fast-backward'></span>",
    'prevPageLabel' => "<span class='glyphicon glyphicon-chevron-left'></span>",
    'nextPageLabel' => "<span class='glyphicon glyphicon-chevron-right'></span>",
    'lastPageLabel' => "<span class='glyphicon glyphicon-fast-forward'></span>",
]);

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'language' => 'es-AR',
    'bootstrap' => ['log'],
    'extensions' => require(__DIR__ . '/../vendor/yiisoft/extensions.php'),
    'components' => [
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'js' => [
                        YII_ENV_PROD ? 'jquery.min.js' : 'jquery.js'
                    ]
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => [
                        YII_ENV_PROD ? 'css/bootstrap.min.css' : 'css/bootstrap.css',
                    ]
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'js' => [
                        YII_ENV_PROD ? 'js/bootstrap.min.js' : 'js/bootstrap.js',
                    ]
                ]
            ],
        ],
        'assetManager' => [
            'linkAssets' => true,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mail' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'log' => [
            'flushInterval' => 1,
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['facturacion'],
                    'logVars' => [],
                    'exportInterval' => 1,
                    'logFile' => '@runtime/logs/app_facturacion.log'
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['Active_Contract'],
                    'logVars' => [],
                    'exportInterval' => 1,
                    'logFile' => '@runtime/logs/app_active_contract.log'

                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['browser-notification-customers'],
                    'logVars' => [],
                    'exportInterval' => 1,
                    'logFile' => '@runtime/logs/browser-notification-customers.log'

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
                'notifications' => [
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
                'log' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'fileMap' => [
                        'app' => 'modules/log/messages/app.php',
                        'log' => 'modules/log/messages/log.php'
                    ],
                ],
            ],
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'dateFormat' => 'php:d-m-Y',
            'datetimeFormat' => 'php:d-m-Y H:i:s',
            'timeFormat' => 'php:H:i:s',
            'decimalSeparator' => ',',
            'thousandSeparator' => '.',
            'currencyCode' => 'ARS',
        ],
        'request' => [
            'cookieValidationKey' => '$4R/4-00034',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'response' => [
            'formatters' => [
                'pdf' => [
                    'class' => 'boundstate\htmlconverter\PdfResponseFormatter',
                ],
            ]
        ],
        'htmlToPdf' => [
            'class' => 'boundstate\htmlconverter\HtmlToPdfConverter',
            'bin' => '/usr/local/bin/wkhtmltopdf.sh',
            // (see http://wkhtmltopdf.org/usage/wkhtmltopdf.txt)
            'options' => [
                'print-media-type',
                'disable-smart-shrinking',
                'no-outline',
                'page-size' => 'A4',
                'load-error-handling' => 'ignore',
                'load-media-error-handling' => 'ignore'
            ],
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'ivr' => [
                    'class' => 'yii\authclient\OAuth2',
                    'clientId' => 'ivr_user',
                    'clientSecret' => '4kjaw4a0d0ks09sdfi9ersj23i4l2309aid09qe',
                    'tokenUrl' => 'https://gestion.westnet.com.ar/index.php?r=ivr/v1/auth/token',
                    'authUrl' => 'https://gestion.westnet.com.ar/index.php?r=ivr/v1/auth/login',
                    'apiBaseUrl' => 'https://gestion.westnet.com.ar/index.php?r=ivr/v1/',
                ],
            ],
        ],
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
        'isp' => [
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
        'log' => [
            'class' => 'app\modules\log\LogModule',
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
        'mobileapp' => [
            'class' => 'app\modules\mobileapp\MobileAppApiModule',
            'modules' => [
                'v1' => [
                    'class' => 'app\modules\mobileapp\v1\V1Module',
                ],
            ],
        ],
        'mailing' => [
            'class' => 'quoma\modules\mailing\MailingModule',
        ],
        'pagomiscuentas' => [
            'class' => 'app\modules\pagomiscuentas\PagomiscuentasModule',
        ],
        'notifications' => [
            'class' => 'app\modules\westnet\NotificationsModule',
            'modules' => [
                'v1' => [
                    'class' => 'app\modules\westnet\notifications\integratech\v1\V1Module',
                ],
            ]
        ],
        'automaticdebit' => [
            'class' => 'app\modules\automaticdebit\AutomaticDebitModule'
        ],
        'instructive' => [
            'class' => 'app\modules\instructive\InstructiveModule',
        ],
        'automatic_debit' => [
            'class' => 'app\module\automatic_debit\AutomaticDebit',
        ],
        'ivr' =>  [
            'class' => 'app\modules\ivr\IvrModule',
            'modules' => [
                'v1' => [
                    'class' => 'app\modules\ivr\v1\V1Module'
                ]
            ]
        ],
        'employee' => [
            'class' => 'app\modules\employee\EmployeeModule',
        ],
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
        'controllerNamespace' => 'app\templates\controllers',
        'controllerMap' => [
            'default' => 'app\templates\controllers\QiiController'
        ],
        'generators' => [
            'crud' => [
                'class' => 'app\templates\generators\crud\Generator',
                'templates' => ['quoma-crud' => '@app/templates/generators/crud/default']
            ],
            'model' => [
                'class' => 'app\templates\generators\model\Generator',
                'templates' => ['quoma-model' => '@app/templates/generators/model/default']
            ],
            'controller' => [
                'class' => 'app\templates\generators\controller\Generator',
                'templates' => ['quoma-controller' => '@app/templates/generators/controller/default']
            ],
        ]
    ];

    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*']
    ];
    
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
        'generators' => [
            'crud'   => [
                'class'     => 'yii\gii\generators\crud\Generator',
                'templates' => ['arya-crud' => '@app/templates/generators/crud/default']
            ]
        ]
    ];
//    $config['modules']['gii'] = [
//        'class' => 'yii\gii\Module',
//        'allowedIPs' => ['127.0.0.1', '::1'],
//    ];

}

return $config;
