<?php

namespace app\modules\mobileapp\controllers;

use app\modules\mobileapp\v1\models\MobilePush;
use app\modules\mobileapp\v1\models\MobilePushHasUserApp;
use app\modules\mobileapp\v1\models\search\MobilePushSearch;
use app\components\web\Controller;
use Yii;

class MobilePushHasUserAppController extends Controller
{

    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Render the view of the model
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id)
        ]);
    }

    /**
     * Finds the MobilePushHasUserApp model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AppFailedRegister the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MobilePushHasUserApp::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
