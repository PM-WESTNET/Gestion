<?php

namespace app\modules\sale\controllers;

use app\components\web\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
