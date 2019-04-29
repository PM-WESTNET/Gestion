<?php

namespace app\modules\invoice\controllers;

use app\components\web\Controller;
use app\modules\invoice\components\einvoice\afip\Migrate;
use app\modules\invoice\components\einvoice\ApiFactory;
use app\modules\sale\models\Bill;
use Yii;

class DefaultController extends Controller
{
    
    public function behaviors() {
        return [
            'access'=> [
                'class' => 'webvimark\modules\UserManagement\components\GhostAccessControl',
            ],
        ];
    }
    
    public function actionIndex()
    {


        return $this->render('index', [
            'result' => []
        ]);
    }
}

