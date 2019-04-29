<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 11/06/18
 * Time: 9:27
 */

namespace app\modules\pagomiscuentas\controllers;

use app\components\helpers\ApacheLogger;
use app\components\helpers\EmptyLogger;
use app\components\web\Controller;
use app\modules\pagomiscuentas\components\Facturas\Facturas;
use app\modules\pagomiscuentas\models\PagomiscuentasFile;
use app\modules\pagomiscuentas\models\search\PagomiscuentasFileSearch;
use app\modules\sale\models\Company;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\web\NotFoundHttpException;

class ExportController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Lists all Partner models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->layout = '/fluid';
        $searchModel = new PagomiscuentasFileSearch();
        $searchModel->type = PagomiscuentasFile::TYPE_BILL;
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
            if($model->save()){
                return $this->redirect(['export/view', 'id'=>$model->pagomiscuentas_file_id]);
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
     * Deletes an existing Partner model.
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
     * Displays a single PagomisCuentasFile model.
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
        Yii::setLogger(new EmptyLogger());
        set_time_limit(0);
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

    public function actionExport($id)
    {
        $model = $this->findModel($id);
        if($model) {
            ob_start();
            $fileName = 'FAC' . $model->company->pagomiscuentas_code .".".(new \DateTime('now'))->format('dmy');
            header('Content-Type: text/plain');
            header('Content-Disposition: attachment;filename="'.$fileName.'"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header ('Cache-Control: cache, must-revalidate');
            header ('Pragma: public');
            try {
                $data = (new PagomiscuentasFileSearch())
                    ->findBillsForExport($model->pagomiscuentas_file_id)->all();

                $facturas = new Facturas([
                    'data' => $data,
                    'company' => $model->company->pagomiscuentas_code
                ]);
                $facturas->parse();
                $facturas->writeFile('php://output');
            } catch( \Exception $ex) {
                error_log($ex->getMessage());
            }
        }
    }
}