<?php

namespace app\modules\ticket\controllers;

use app\modules\sale\models\Customer;
use app\modules\ticket\models\Action;
use app\modules\ticket\models\Assignation;
use app\modules\ticket\models\Category;
use app\modules\ticket\models\Observation;
use app\modules\ticket\models\Status;
use app\modules\ticket\models\TicketManagement;
use app\modules\westnet\components\MesaTicketManager;
use webvimark\modules\UserManagement\models\User;
use Yii;
use app\modules\ticket\models\Ticket;
use \app\modules\ticket\models\search\TicketSearch;
use app\components\web\Controller;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;
use app\modules\config\models\Config;
use yii\web\Response;
use app\modules\ticket\TicketModule;

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

        $observation = new Observation();
        $observation->user_id = \Yii::$app->user->id;
        $observation->ticket_id = $model->ticket_id;

        if ($observation->load(Yii::$app->request->post())) {
            if ($observation->save()){
                $model->externalColorAssignation();
                $observation = new Observation();
                MesaTicketManager::getInstance()->updateRemoteTicket($model);
                Yii::$app->getSession()->setFlash('success', TicketModule::t('app', 'Observation successfully saved!'));
            }
            else
                Yii::$app->getSession()->setFlash('danger', TicketModule::t('app', 'An error ocurred when saving the observation.'));
        }

        //Observations query and pagination
        $query = Observation::find()->where(['ticket_id' => $model->ticket_id]);

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
            Yii::$app->getSession()->setFlash('success', TicketModule::t('app', 'Ticket successfully closed!'));
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
            Yii::$app->getSession()->setFlash('success', TicketModule::t('app', 'Ticket successfully reopened!'));
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

    /**
     * Crea tickets para todos los customer dados y los asigna al user indicado
     */
    public function actionCreateAndAssignUser()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $data = Yii::$app->request->post();

        if (!isset($data['customer_codes']) || !isset($data['title']) || !isset($data['user_id']) || !isset($data['category_id'])) {
            throw new BadRequestHttpException('Customer Codes, Title, User ID and Category ID are required');
        }

        $category = Category::findOne($data['category_id']);
        $customer_codes = explode(',', $data['customer_codes']);
        $status = true;
        foreach($customer_codes as $code) {
            $customer = Customer::findOne(['code' => $code]);
            $ticket = new Ticket([
                'title' => $data['title'],
                'user_id' => Yii::$app->user->getId(),
                'category_id' => $category->category_id,
                'customer_id' => $customer->customer_id,
                'status_id' => 1,
                'content' => $data['title']
            ]);

            if(!$ticket->save() || !Ticket::assignTicketToUser($ticket->ticket_id, $data['user_id'])){
                $status = false;
            }
        }

        return [ 'status' => $status ? 'success' : 'error' ];
    }

    /**
     * @return array
     * Verifica si el customer tiene un ticket que pertenezca a la cateoria de cobranza, si tiene indica el estado en el que está
     */
    public function actionCustomersHasCategoryTicket()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $customer_codes = Yii::$app->request->post('customer_codes');
        $response = [];
        $category = Category::findOne(Yii::$app->request->post('category_id'));

        foreach ($customer_codes as $code) {
            $response[] = Customer::hasCategoryTicket($code, $category->category_id, true);
        }

        return $response;
    }

    public function actionCollectionTickets()
    {

        $this->layout = '/fluid';
        $search = new TicketSearch();
        $search->setScenario('wideSearch');
        $params = Yii::$app->request->post();

        $category = Category::findOne(Config::getValue('cobranza_category_id'));

        if (empty($category)) {
            throw new BadRequestHttpException('Categoria de Cobranza no encontrada');
        }

        $search->category_id = $category->category_id;

        if (!isset($params['TicketSearch']['show_all'])){
            $search->show_all= true;
        }

        if (!User::hasRole('collection_manager')){
            $search->user_id = Yii::$app->user->id;
        }

        $dataProvider = $search->search($params);

        return $this->render('collection_tickets', ['searchModel' => $search, 'dataProvider' => $dataProvider]);

    }

    public function actionInstallationsTickets()
    {

        $this->layout = '/fluid';
        $search = new TicketSearch();
        $search->setScenario('wideSearch');
        $params = Yii::$app->request->post();

        $category = Category::findOne(Config::getValue('installations_category_id'));
        $category2 = Config::getValue('ticket_category_gestion_ads');

        if (empty($category)) {
            throw new BadRequestHttpException('Categoria de Instalaciones no encontrada');
        }

        $search->categories = [$category->category_id, $category2];

        if (!isset($params['TicketSearch']['show_all'])){
            $search->show_all= true;
        }
        if (!User::hasRole('installations_manager')){
            $search->user_id = Yii::$app->user->id;
        }

        $dataProvider = $search->search($params);

        return $this->render('installation_tickets', ['searchModel' => $search, 'dataProvider' => $dataProvider]);

    }

    /**
     * @return string
     * @throws BadRequestHttpException
     * Muestra un listado de tickets de  la categoría solicitud de edición de tickets.
     */
    public function actionContactEditionTickets()
    {
        $this->layout = '/fluid';
        $search = new TicketSearch();
        $search->setScenario('wideSearch');
        $search->status_id = null;

        $category = Category::findOne(Config::getValue('ticket-category-edicion-de-datos-id'));

        if (empty($category)) {
            throw new BadRequestHttpException('Categoría de Solicitud de edición de datos no encontrada. Verifique configuración');
        }

        $search->category_id = $category->category_id;
        $dataProvider = $search->search(Yii::$app->request->getQueryParams());

        return $this->render('request-data-edition-tickets', ['searchModel' => $search, 'dataProvider' => $dataProvider]);
    }

    public function actionGetObservations($id)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = $this->findModel($id);

            $dataProvider = new ActiveDataProvider(['query' => $model->getObservations()]);
            $dataProviderTicketManagement = new ActiveDataProvider([
                'query' => $model->getTicketManagements()
            ]);

            $observations = $this->renderAjax('_observations', [
                'dataProvider' => $dataProvider,
                'dataProviderTicketManagement' => $dataProviderTicketManagement,
                'model' => $model
            ]);

            return [
                'title' => Yii::t('app','Ticket') . ': '. $model->title . ' - ' . Yii::t('app','Observations'),
                'observations' => $observations
            ];

        }

        throw new NotFoundHttpException('Page Not Found');

    }


    public function actionGetObservationForm($ticket_id)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = new Observation();
            $form = $this->renderPartial('../observation/_new-observation', ['model' => $model, 'ticket_id' => $ticket_id]);

            return [
                'form' => $form
            ];
        }

        throw new NotFoundHttpException('Page Not Found');
    }

    /**
     * Devuelve el formulario de creacion de gestion de ticket
     */
    public function actionGetManagementForm($ticket_id, $observation_id)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = new TicketManagement();
            $form = $this->renderPartial('_new-ticket-management', ['model' => $model, 'ticket_id' => $ticket_id, 'observation_id' => $observation_id]);

            return [
                'form' => $form
            ];
        }

        throw new NotFoundHttpException('Page Not Found');
    }

    /**
     * @param $step_id
     * @param $template_id
     * @return array
     * Permite editar por ajax el estado de un ticket
     */
    public function actionEditStatus($ticket_id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $hasEditable = Yii::$app->request->post('hasEditable');

        if ($hasEditable) {
            $model = Ticket::findOne($ticket_id);
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return ['output' => $model->status->name, 'message' => ''];
            } else {
                return ['output' => '', 'message' => $model->getErrors()];
            }
        }
    }

    /**
     * @param $step_id
     * @param $template_id
     * @return array
     * Permite editar por ajax el estado de un ticket
     */
    public function actionAssignTicketToUser()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $ticket_id = Yii::$app->request->post('ticket_id');
        $user_id = Yii::$app->request->post('users_id');

        $ticket = Ticket::findOne($ticket_id);

            if(!$ticket->isAssignatedUser($user_id)) {
                Ticket::assignTicketToUser($ticket_id, $user_id);
            }

            Ticket::deleteAllAssignations($ticket->ticket_id, [$user_id]);

        return [
            'status' => 'success'
        ];
    }

    /**
     * @param $status_id
     * @return array
     * Indica si un status esta asociado a una accion de tipo event
     */
    public function actionStatusHasEventAction($status_id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $status = Status::findOne($status_id);

        return [
            'status' => $status ? 'success' : 'error',
            'has_event_action' => $status ? $status->hasEventAction() : false
        ];
    }

    /**
     * @param $ticket_id
     * @return Response
     * @throws NotFoundHttpException
     * Crea una gestión para el ticket
     */
    public function actionAddTicketManagement($ticket_id, $redirect)
    {
        $ticket = $this->findModel($ticket_id);

        if($ticket->addTicketManagement(Yii::$app->user->id)) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Ticket management registered successfully'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Ticket management registered successfully'));
        }

        return $this->redirect([$redirect]);
    }


    public function actionCloseCollectionTicketsByPeriod(){

        $data = Yii::$app->request->get();

        $search = new TicketSearch();
        $search->category_id = Config::getValue('cobranza_category_id');
        $query = $search->searchClosedByPeriodAndStatus($data);


        if ($query->exists()) {
            $count = $query->count();
            $status = Status::findOne(['name' => 'Cerrado (Por Jefe de Cobranza)']);

            foreach ($query->all() as $ticket) {
                $ticket->updateAttributes(['status_id' => $status->status_id]);
            }

            Yii::$app->session->addFlash('success', Yii::t('app','{count} tickets has been closed', ['count' => $count]));
            return $this->redirect(['collection-tickets']);
        }

        Yii::$app->session->addFlash('warning', Yii::t('app','Can`t found tickets to close'));

        return $this->redirect(['collection-tickets']);
    }

    /**
     * Renderiza un gráfico con la cantidad de tickets.
     */
    public function actionReport()
    {
        $searchModel = new TicketSearch();
        $searchModel->status_id = null;
        $data = $searchModel->searchReport(Yii::$app->request->getQueryParams());
        $datas = [];
        $cols = [];

        foreach($data as $item) {
            $cols[] = (new \DateTime($item['periodo'] . '-01'))->format('m-Y');
            $datas[] = $item['qty'];
        }

        return $this->render('report', [
            'searchModel' => $searchModel,
            'cols' => $cols,
            'data' => $datas,
            'colors' => 'green'
        ]);
    }

}
