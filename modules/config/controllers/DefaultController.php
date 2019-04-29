<?php

namespace app\modules\config\controllers;

use app\components\web\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
