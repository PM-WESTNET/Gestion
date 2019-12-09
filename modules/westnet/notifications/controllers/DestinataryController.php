<?php

namespace app\modules\westnet\notifications\controllers;

use app\modules\sale\models\Customer;
use app\modules\sale\models\search\CustomerSearch;
use Yii;
use app\modules\westnet\notifications\NotificationsModule;
use app\modules\westnet\notifications\models\Notification;
use app\modules\westnet\notifications\models\Destinatary;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DestinataryController implements the CRUD actions for Destinatary model.
 */
class DestinataryController extends Controller {

    //public $layout = '@app/views/layouts/no_container';

    public function behaviors() {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Lists all destinatary models for a specific notification
     * @return mixed
     */
    public function actionIndex($notification_id) {

        $notification = Notification::findOne($notification_id);

        if (!empty($notification)) {

            $dataProvider = new ActiveDataProvider([
                'query' => $notification->getDestinataries()
            ]);

            return $this->render('index', [
                'dataProvider' => $dataProvider,
                'notification' => $notification,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('yii', 'Not found.'));
        }
    }

    /**
     * Displays a single Destinatary model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $destinatary = $this->findModel($id);

        $customerQuery = $destinatary->getCustomersQuery( $destinatary->notification->transport->slug != 'sms');

        if ($destinatary->notification->transport->slug === 'email') {
            $customerQuery->andWhere(['email_status' => 'active']);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $customerQuery
        ]);

        return $this->render('view', [
            'dataProvider' => $dataProvider,
            'model' => $destinatary,
        ]);
    }

    /**
     * Creates a new Destinatary model for a specific notification
     */
    public function actionCreate($notification_id) {

        $notification = Notification::findOne($notification_id);

        if (!empty($notification)) {

            $model = new Destinatary();
            $model->notification_id = $notification->notification_id;

            if ($model->load(Yii::$app->request->post())) {

                if ($model->save()) {
                    $this->redirect(['notification/wizard', 'id' => $model->notification_id, 'step' => 3]);
                } else {
                    Yii::$app->session->setFlash("error", NotificationsModule::t('app', 'An error ocurred when saving this destinatary.'));
                }
            } else {

                return $this->render('create', [
                    'model' => $model,
                    'notification' => $notification,
                ]);
            }
        } else {

            Yii::$app->session->setFlash("error", NotificationsModule::t('app', 'Could not find notification. Cannot load any destinataries without a valid notification.'));
            return $this->redirect(['notification/index']);
        }
    }

    /**
     * Updates an existing Destinatary model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->redirect(['view', 'id' => $model->destinatary_id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }
    
    /**
     * Deletes an existing Destinatary model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $model = $this->findModel($id);
        $notification_id = $model->notification->notification_id;
        $model->delete();
        return $this->redirect(['index', 'notification_id' => $notification_id]);
    }

    /**
     * Finds the Destinatary model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Destinatary the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Destinatary::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Busca customer por nombre
     * @param $name
     * @return array
     */
    public function actionFindByName($name, $company_id, $id=null,$normal= true)
    {
        Yii::$app->response->format = 'json';

        if(!is_null($name)) {
            $searchModel = new CustomerSearch;

            $data['results'] = $searchModel->searchByNameAndCompany($name, $company_id)
                ->select(['customer_id as id',
                    "CONCAT(customer.code, ' - ', lastname, ' ', customer.name) as text"])
                ->asArray()->all();
        } else if( $id > 0) {
            $data['results'] = ['id' => $id, 'text' => Customer::find($id)->name];
        }

        return $data;
    }
}
