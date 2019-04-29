<?php

namespace app\modules\ticket\controllers;

use app\modules\westnet\components\MesaTicketManager;
use Yii;
use app\modules\ticket\models\Ticket;
use \app\modules\ticket\models\search\TicketSearch;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use app\modules\config\models\Config;

/**
 * TicketController implements the CRUD actions for Ticket model.
 */
class TicketController extends Controller 
{

    /**
     * Lists all Ticket models.
     * @return mixed
     */
    public function actionIndex() {

        $this->layout = '@app/views/layouts/main_no_container';

        $searchModel = new TicketSearch();
        $searchModel->scenario = TicketSearch::SCENARIO_WIDE_SEARCH;

        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel
        ]);
    }

    /**
     * Redirects to index with some parameters setted for search porpouses
     */
    public function actionOpenTickets() {

        $status_active = \app\modules\ticket\models\Status::find()->where([
                    'is_open' => true
                ])->all();
        $this->redirect(\yii\helpers\Url::to([
                    'index',
                    'TicketSearch[status_id]' => $status_active
        ]));
    }

    /**
     * List users with open tickets
     * @return type
     */
    public function actionList() {
        $this->layout = '@app/views/layouts/main_no_container';

        $status_active = \app\modules\ticket\models\Status::find()->where([
                    'is_open' => true
                ])->all();

        //Find users with open tickets and returns them as an array (just with customer_id and ticket count)
        $customersWithTickets = Ticket::find()->isStatusOpen()->select([
                    'customer_id',
                    'count' => 'count(ticket_id)'
                ])->groupBy([
                    'customer_id'
                ])->orderBy([
                    'start_date' => SORT_ASC
                ])->asArray()->all();

        return $this->render('active_list', [
                    'customersWithTickets' => $customersWithTickets
        ]);
    }

    /**
     * Displays a single Ticket model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Ticket model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($customer_id = null) {
        $model = new Ticket();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->ticket_id]);
        } else {
            if ($customer_id != null) {
                $model->customer_id= $customer_id;
            }
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
        
        
    }

    /**
     * Updates an existing Ticket model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', \app\modules\ticket\TicketModule::t('app', 'Ticket successfully updated!'));
            return $this->redirect(['view', 'id' => $model->ticket_id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Creates an observation for a ticket model
     * @param integer $id
     * @return mixed
     */
    public function actionObservation($id) {

        $model = $this->findModel($id);

        $observation = new \app\modules\ticket\models\Observation();
        $observation->user_id = \Yii::$app->user->id;
        $observation->ticket_id = $model->ticket_id;

        if ($observation->load(Yii::$app->request->post())) {
            if ($observation->save()){
                $model->externalColorAssignation();
                $observation = new \app\modules\ticket\models\Observation();
                MesaTicketManager::getInstance()->updateRemoteTicket($model);
                Yii::$app->getSession()->setFlash('success', \app\modules\ticket\TicketModule::t('app', 'Observation successfully saved!'));
            }
            else
                Yii::$app->getSession()->setFlash('danger', \app\modules\ticket\TicketModule::t('app', 'An error ocurred when saving the observation.'));
        }

        //Observations query and pagination
        $query = \app\modules\ticket\models\Observation::find()->where(['ticket_id' => $model->ticket_id]);

        $pagination = new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => (!empty(Config::getConfig('expiration_timeout')->value)) ? Config::getConfig('pagination_limit')->value : \Yii::$app->getModule('ticket')->params['pagination_limit'],
        ]);

        $observations = $query->offset($pagination->offset)
                ->limit($pagination->limit)
                ->orderBy([
                    'datetime' => SORT_DESC
                ])
                ->all();
        return $this->render('observation', [
                    'model' => $model,
                    'currentObservations' => $observations,
                    'observation' => $observation,
                    'pagination' => $pagination
        ]);
    }

    /**
     * Closes an active ticket model
     * @param integer $id
     * @return view
     */
    public function actionClose($id) {

        $model = $this->findModel($id);

        if ($model->closeTicket()) {
            Yii::$app->getSession()->setFlash('success', \app\modules\ticket\TicketModule::t('app', 'Ticket successfully closed!'));
        } else {
            
        }
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Reopens a previously closed ticket
     * @param integer $id
     * @return view
     */
    public function actionReopen($id) {

        $model = $this->findModel($id);

        if ($model->reopenTicket()) {
            Yii::$app->getSession()->setFlash('success', \app\modules\ticket\TicketModule::t('app', 'Ticket successfully reopened!'));
        } else {
            
        }

        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Shows the history for a specific ticket
     * @param integer $id
     * @return view
     */
    public function actionHistory($id) {

        $model = $this->findModel($id);
        
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => \app\modules\ticket\models\History::find()
                ->where(['ticket_id' => $id]),
        ]);

        return $this->render('history', [
                    'model' => $this->findModel($id),
                    'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Deletes an existing Ticket model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Ticket model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Ticket the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Ticket::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
