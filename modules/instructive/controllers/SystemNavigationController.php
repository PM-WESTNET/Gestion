<?php

namespace app\modules\instructive\controllers;

use app\components\web\Controller;
/**
 * Gives the actions for the system navigation module
 */
class SystemNavigationController extends Controller
{
  

    /**
     * Goes to the tutorial view for this app.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

}
