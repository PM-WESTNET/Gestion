<?php

namespace app\modules\documentation\controllers;

use app\components\web\Controller;

/**
 * GestionController runs a view for gestion info.
 */
class GestionController extends Controller
{
    public function actionInfo()
    {
        return $this->render('info');
    }

}
