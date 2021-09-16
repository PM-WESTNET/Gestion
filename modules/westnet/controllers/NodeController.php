<?php

namespace app\modules\westnet\controllers;

use app\modules\westnet\components\ConnectionService;
use app\modules\westnet\models\Connection;
use app\modules\westnet\models\NodeHasCompanies;
use app\modules\westnet\models\search\ConnectionSearch;
use app\modules\westnet\models\Server;
use app\modules\sale\modules\contract\models\Contract;
use Yii;
use app\modules\westnet\models\Node;
use app\modules\westnet\models\NatServer;
use app\modules\westnet\models\search\NodeSearch;
use app\components\web\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\westnet\models\IpRange;
use yii\helpers\ArrayHelper;

/**
 * NodeController implements the CRUD actions for Node model.
 */
class NodeController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Lists all Node models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = '/fluid';$searchModel = new NodeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Node model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {

        $search = new NodeSearch();
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
            'search' => $search
        ]);
    }

    /**
     * Creates a new Node model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Node();
        $list_nat_servers = ArrayHelper::map(NatServer::findNatServerAll(), 'nat_server_id', 'description' );

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->node_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'list_nat_servers' => $list_nat_servers,
            ]);
        }
    }

    /**
     * Updates an existing Node model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $list_nat_servers = ArrayHelper::map(NatServer::findNatServerAll(), 'nat_server_id', 'description' );

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->node_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'list_nat_servers' => $list_nat_servers,
            ]);
        }
    }

    /**
     * Deletes an existing Node model.
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
     * Deletes an existing Node model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionAssignedIp()
    {
        $searchModel = new ConnectionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('assigned-ip',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds the Node model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Node the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Node::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Funcion que retorna los posibles nodos padre, dependiendo del servidor enviado
     */
    public function actionParentNodes()
    {
        $out = [];
        $params = Yii::$app->request->post('depdrop_all_params');
        $server_id = ($params['server_id'] ? $params['server_id'] : null);
        $node_id = ( array_key_exists('node_id', $params) &&  $params['node_id'] ? $params['node_id'] : null);
        if ($server_id) {
            $query = Node::find()
                ->select(['node_id as id', 'name as name'])
                ->where(['=', 'server_id', $server_id]);
            if ($node_id){
                $query->andWhere(['not', ['node_id' => $node_id]]);
            }
            $out = $query->asArray()->all();
            echo Json::encode(['output'=>$out, 'selected'=>'']);
        } else {
            echo Json::encode(['output'=>'', 'selected'=>'']);
        }
    }
    
    public function actionAllNodes()
    {
        if (Yii::$app->request->isAjax) {
            $query = Node::find();
            $query->select(['node.node_id', 'concat(node.name, \' - \', s.name) as name', 'node.subnet'])
                  ->leftJoin('server s', 'node.server_id = s.server_id');
            $nodes= $query->all();
            Yii::$app->response->format= \yii\web\Response::FORMAT_JSON;
            return $nodes;
        }else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionChangeServer()
    {
        set_time_limit(0);
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = 'json';
            $node_id = Yii::$app->request->post('node_id');
            $server_id = Yii::$app->request->post('server_id');

            if($node_id && $server_id) {
                error_log($node_id . " && " . $server_id);

                $service = new ConnectionService();
                $service->changeServer($node_id, $server_id);

                return [
                    'status' => 'ok'
                ];
            }
        }
    }

    public function actionLoadChangeServer($node_id)
    {
        $model = $this->findModel($node_id);
        $servers = Server::find()->where('server_id not in (' . $model->server_id . ')')->all();

        $to_process = Connection::find()->where('ip4_1<>0')->andWhere(['node_id'=>$node_id])->count();

        return $this->renderAjax('_form-change-server.php', [
            'model' => $model,
            'servers' => $servers,
            'to_process' => $to_process
        ]);
    }


    /**
     * Retorna el estado del proceso actual.
     * @return array
     */
    public function actionGetProcess()
    {
        Yii::$app->response->format = 'json';

        $process = Yii::$app->request->post('process');

        return Yii::$app->session->get($process, [
            'total' => 0,
            'qty'   => 0
        ]);

    }

    public function actionSyncNode($id){
        $customers_by_node = Contract::findContractsByNode($id);

        foreach ($customers_by_node as $key => $value) {
            if ($value->updateOnISP()) 
                Yii::$app->session->addFlash('success', Yii::t('app','Contract updated on ISP successfull'));

            else
                Yii::$app->session->addFlash('error', Yii::t('app','Errors occurred at update contract on ISP'));  
        }
        
        return $this->redirect('index');
    }
}
