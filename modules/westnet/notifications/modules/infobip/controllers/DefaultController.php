<?php

namespace app\modules\westnet\notifications\modules\infobip\controllers;

use app\modules\westnet\notifications\modules\infobip\models\InfobipResponseSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

/**
 * Default controller for the `infobip` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $infobipResponseSearch = new InfobipResponseSearch();

        $dataProvider = $infobipResponseSearch->search(\Yii::$app->request->get());

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }
}
