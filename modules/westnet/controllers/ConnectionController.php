<?php

namespace app\modules\westnet\controllers;

use app\modules\sale\modules\contract\models\Contract;
use app\modules\westnet\models\Connection;
use Yii;
use app\modules\westnet\models\IpRange;
use app\modules\westnet\models\search\IpRangeSearch;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * Class ConnectionController
 * @package app\modules\westnet\controllers
 */
class ConnectionController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Enable the connection
     * @return mixed
     */
    public function actionEnable($id)
    {
        Yii::$app->response->format = 'json';
        $model = $this->findModel($id);

        $model->status_account = Connection::STATUS_ACCOUNT_ENABLED;
        $model->due_date = null;
        $model->update(false);

        $result = ($model->status_account == Connection::STATUS_ACCOUNT_ENABLED);

        return [
            'status' => ($result ? 'success' : 'error')
        ];
    }

    /**
     * Disable de connection
     * @return mixed
     */
    public function actionDisable($id)
    {
        Yii::$app->response->format = 'json';
        $model = $this->findModel($id);
        $model->status_account = Connection::STATUS_ACCOUNT_DISABLED;
        $model->due_date = null;
        $model->update(false);

        $result = ($model->status_account == Connection::STATUS_ACCOUNT_DISABLED);

        return [
            'status' => ($result ? 'success' : 'error')
        ];
    }

    /**
     * Force the connection
     * @return mixed
     */
    public function actionForce($id)
    {
        /** @var Connection $model */
        $model = $this->findModel($id);
        $result = true;
        if(Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            if($model->canForce()){
                $model->due_date = (new \DateTime(Yii::$app->request->post('due_date')))->format('d-m-Y');
                $model->status_account = Connection::STATUS_ACCOUNT_FORCED;
                $result = $model->update();                
            }else{
                Yii::$app->response->format = 'json';
                return [
                    'status' =>'error',
                    'message' =>  Yii::t('westnet', 'Can`t force this connection becouse this connection has exceeded the limit forced in the month')
                ];
            }
        }
        Yii::$app->response->format = 'json';
        return [
            'status' => ($result ? 'success' : 'error')
        ];
    }


    /**
     * Finds the IpRange model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return IpRange the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Connection::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}