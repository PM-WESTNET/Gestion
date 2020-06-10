<?php

namespace app\modules\mobileapp\controllers;

use app\modules\mobileapp\v1\models\MobilePush;
use app\modules\mobileapp\v1\models\search\MobilePushSearch;
use app\components\web\Controller;
use Yii;

class MobilePushController extends Controller
{

    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Lists all Company models.
     * @return mixed
     */
    public function actionView($mobile_push_id)
    {
        $searchModel = new MobilePushSearch();
        $dataProvider = $searchModel->searchMobilePushHasUserApp([]/*(!Yii::$app->request->isPost) ? null : Yii::$app->request->post()*/);

        return $this->render('view', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
