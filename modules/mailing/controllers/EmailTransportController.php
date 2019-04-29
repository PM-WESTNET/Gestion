<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 28/08/17
 * Time: 11:08
 */

namespace app\modules\mailing\controllers;

use app\modules\mailing\components\sender\MailSender;
use app\modules\mailing\models\EmailTransport;
use Yii;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EmailTransportController implements the CRUD actions for EmailTransport model.
 */
class EmailTransportController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all EmailTransport models.
     * @return mixed
     */
    public function actionIndex()
    {

        $dataProvider = new ActiveDataProvider([
            'query' => EmailTransport::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EmailTransport model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new EmailTransport model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EmailTransport();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->email_transport_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EmailTransport model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->email_transport_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EmailTransport model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the EmailTransport model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EmailTransport the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EmailTransport::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param $id
     */
    public function actionTest($id)
    {
        Yii::$app->response->format = 'json';
        $return = [
            'status' => 'ok'
        ];

        $transport = $this->findModel($id);
        /** @var MailSender $sender */
        $sender = MailSender::getInstance($transport->name, $transport->relation_class, $transport->relation_id);
        try {
            $sender->send(
                Yii::$app->request->post('email_to'),
                "Test Message", [
                    'params' =>[],
                    'view'   => '@app/modules/mailing/views/template/test',
                    'layout' => 'layouts/html'
                ]
            );
        } catch(\Exception $ex) {
            $return = [
                'status' => 'ko',
                'message' => $ex->getMessage()
            ];
        }

        return $return;
    }

    public function actionAutocomplete($transport, $term="")
    {
        Yii::$app->response->format = 'json';
        if($transport) {
            $oTrans = new $transport;
            $oRs = $oTrans->findForAutoComplete($term);
            $result = [];
            foreach($oRs as $key => $data) {
                $result[] = [
                    'id' => $key,
                    'text' => $data
                ];
            }
            Yii::debug($oRs);
            return [
                'results' => $result
            ];
        }
        return [];
    }
}