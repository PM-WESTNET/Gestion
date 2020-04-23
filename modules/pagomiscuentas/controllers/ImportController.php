<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 11/06/18
 * Time: 10:17
 */

namespace app\modules\pagomiscuentas\controllers;


use app\components\web\Controller;
use app\modules\pagomiscuentas\models\PagomiscuentasFile;
use app\modules\pagomiscuentas\models\search\PagomiscuentasFileSearch;
use Yii;
use yii\data\ActiveDataProvider;
use yii\debug\models\timeline\DataProvider;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class ImportController extends Controller
{

    /**
     * Lists all Partner models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->layout = '/fluid';

        $searchModel = new PagomiscuentasFileSearch();
        $searchModel->type = PagomiscuentasFile::TYPE_PAYMENT;
        $dataProvider = new ActiveDataProvider([
            'query' => $searchModel->findByFilter(Yii::$app->request->queryParams),
        ]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Company model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PagomiscuentasFile();
        if ($model->load(Yii::$app->request->post())&& $model->validate()) {
            if($model->upload() && $model->save()){
                return $this->redirect(['import/view', 'id'=>$model->pagomiscuentas_file_id]);
            }
        } else {
            foreach( $model->getErrors() as $error) {
                Yii::$app->session->setFlash("error", Yii::t('app', $error[0]));
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a single PagomisCuentasFile model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $dataProviderPayments = new ActiveDataProvider([
            'query' => $model->getPayments()
        ]);

        $dataProviderCustomers = new ActiveDataProvider([
           'query' => $model->getCustomerInWrongCompany()
        ]);

        return $this->render('view', [
            'model' => $model,
            'dataProviderPayments' => $dataProviderPayments,
            'dataProviderCustomers' => $dataProviderCustomers,
        ]);
    }

    /**
     * Deletes an existing PagomisCuentasFile model.
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
     * Finds the Partner model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PagomiscuentasFile the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PagomiscuentasFile::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionClose($id)
    {
        //Yii::setLogger(new EmptyLogger());
        /** @var PagomiscuentasFile $model */
        $model = $this->findModel($id);
        if($model) {
            try {
                $model->close();
                return $this->actionIndex();
            } catch(\Exception $ex) {
                Yii::$app->session->addFlash('error', $ex->getMessage());
                return $this->actionView($id);
            }
        }
    }
}