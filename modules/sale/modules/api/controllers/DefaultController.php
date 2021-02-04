<?php

namespace app\modules\sale\modules\api\controllers;

use yii\rest\Controller;
use yii\web\Response;

use Yii;

class DefaultController extends Controller
{
    
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\ContentNegotiator',
                'only' => ['view', 'index', 'login'],  // in a controller
                // if in a module, use the following IDs for user actions
                // 'only' => ['user/view', 'user/index']
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    'application/xml' => Response::FORMAT_XML,
                ],
            ],
        ];
    }
    
    /**
     * Login para obtener auth_key para utilizar la api
     */
    public function actionLogin()
    {
        
        $model = new \webvimark\modules\UserManagement\models\forms\LoginForm();
        $model->username = Yii::$app->request->post('username');
        $model->password = Yii::$app->request->post('password');

        $model->validate();

        if ( !$model->hasErrors() )
        {
            //$identity = Yii::$app->user->identity;

            return [
                'status' => 'success',
                'auth_key' => $model->getUser()->auth_key,
                'username' => $model->getUser()->username
            ];
        }

        throw new \yii\web\HttpException(401, 'Not authorized.');
        
    }

    
    public function actionIndex()
    {
        
        return [
            'services' => [
                'bill' => [
                    'url' => \yii\helpers\Url::to(['/sale/api/bill']),
                    'actions' => [
                        'types' => [
                            'request' => [
                                'format' => 'get'
                            ],
                            'description' => 'Returns a list of startable bill types.'
                        ],
                        'index' => [
                            'request' => [
                                'format' => 'get',
                                'searchParams' => [
                                    'BillSearch' => [
                                        'bill_type_id' => 'integer',
                                        'fromDate' => 'date',
                                        'toDate' => 'date',
                                        'customer_id' => 'integer',
                                        'company_id' => 'integer',
                                        'status' => 'string',
                                        'user' => 'integer',
                                        'expired' => 'boolean',
                                        'fromAmount' => 'double',
                                        'toAmount' => 'double'
                                    ]
                                ]
                            ],
                            'description' => 'Returns a list of models. All search params are optional.'
                        ],
                        'create' => [
                            'request' => [
                                'format' => 'post',
                                'params' => [
                                    'Bill' => [
                                        'company_id' => 'integer',
                                        'customer_id' => 'integer',
                                        'bill_type_id' => 'integer',
                                        'expiration' => 'date',
                                        'billDetails' => [
                                            'product_id' => 'integer',
                                            'qty' => 'double'
                                        ]
                                    ]
                                ]
                            ],
                            'description' => 'Creates a new Bill model. Expiration is only required in expirable bill types.',
                        ],
                        'view' => [
                            'request' => [
                                'format' => 'get',
                                'params' => [
                                    'id' => 'integer'
                                ]
                            ],
                            'description' => 'Returns a bill details.'
                        ],
                        'group' => [
                            'request' => [
                                'format' => 'get',
                                'params' => [
                                    'footprint' => 'integer'
                                ]
                            ],
                            'description' => 'Returns a list of models with the same footprint.'
                        ],
                        'import' => [
                            'request' => [
                                'format' => 'post',
                                'params' => [
                                    'Bills' => [
                                        [
                                            'customer_id' => 'integer',
                                            'bill_type_id' => 'integer',
                                            'company_id' => 'integer',
                                            'billDetails' => [
                                                [
                                                    'qty' => 'integer',
                                                    'product_id' => 'integer'
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'description' => 'Imports a list of bills.'
                        ]
                    ]
                ],
                'company' => [
                    'url' => \yii\helpers\Url::to(['/sale/api/company']),
                    'actions' => [
                        'index' => [
                            'request' => [
                                'format' => 'get'
                            ],
                            'description' => 'Returns a list of models.'
                        ],
                        'view' => [
                            'request' => [
                                'format' => 'get',
                                'params' => [
                                    'id' => 'integer'
                                ]
                            ],
                            'description' => 'Returns a model details.'
                        ],
                        'create' => [
                            'request' => [
                                'format' => 'post'
                            ],
                            'description' => 'Creates a new model.'
                        ]
                    ]
                ],
                'customer' => [
                    'url' => \yii\helpers\Url::to(['/sale/api/customer']),
                    'actions' => [
                        'index' => [
                            'request' => [
                                'format' => 'get'
                            ],
                            'description' => 'Returns a list of models.'
                        ],
                        'view' => [
                            'request' => [
                                'format' => 'get',
                                'params' => [
                                    'id' => 'integer'
                                ]
                            ],
                            'description' => 'Returns a model details.'
                        ],
                        'create' => [
                            'request' => [
                                'format' => 'post'
                            ],
                            'description' => 'Creates a new model.'
                        ]
                    ]
                ],
                'product' => [
                    'url' => \yii\helpers\Url::to(['/sale/api/product']),
                    'actions' => [
                        'index' => [
                            'request' => [
                                'format' => 'get'
                            ],
                            'description' => 'Returns a list of models.'
                        ],
                        'view' => [
                            'request' => [
                                'format' => 'get',
                                'params' => [
                                    'id' => 'integer'
                                ]
                            ],
                            'description' => 'Returns a model details.'
                        ],
                    ]
                ],
            ]
        ];
    }
}
