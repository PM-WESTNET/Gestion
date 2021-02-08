<?php

namespace app\modules\accounting\controllers;

use app\components\workflow\Workflow;
use app\modules\accounting\components\ResumeImporter;
use app\modules\accounting\models\MoneyBoxHasOperationType;
use app\modules\accounting\models\ResumeItem;
use app\modules\accounting\models\search\ResumeSearch;
use app\modules\import\components\AbstractCsvImport;
use Yii;
use app\modules\accounting\models\Resume;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * ResumeController implements the CRUD actions for Resume model.
 */
class ResumeController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }

    /**
     * Lists all Resume models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ResumeSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,

        ]);
    }

    /**
     * Displays a single Resume model.
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
     * Creates a new Resume model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Resume();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['details', 'id' => $model->resume_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Resume model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            return $this->redirect(['view', 'id' => $model->resume_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Resume model.
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
     * Finds the Resume model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Resume the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Resume::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Cambia el estado del modelo.
     *
     * @param $id
     * @param $newState
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionChangeState($id, $newState)
    {
        Yii::$app->response->format = 'json';

        $model = $this->findModel($id);
        if($newState == Resume::STATE_CLOSED) {
            $total = $model->getTotal();

            Yii::debug('totales: ' . print_r($total, 1));
            Yii::debug('calculo: '. (($model->balance_initial + (double)$total['credit'] ) - (double)$total['debit']));
            Yii::debug('deberia dar: '. $model->balance_final);

            if( round(($model->balance_initial + $total['credit'] ) - $total['debit'], 2) != $model->balance_final ) {
                Yii::$app->session->setFlash("error", Yii::t('accounting', 'The final balance is not equal to: initial balance + credit - debit.'));
                return $this->redirect(['details', 'id' => $model->resume_id]);
            }
        }

        if (Workflow::changeState($model, $newState)) {
            Yii::$app->session->setFlash("success", Yii::t('accounting', 'The Resume has been {state}.',['state' => Yii::t('accounting', $newState)] ));
            return $this->redirect(['view', 'id' => $model->resume_id]);
        }

        Yii::$app->session->setFlash("error", Yii::t('accounting', 'The {model} can not change to the state {state}.',
            ['model' => Yii::t('accounting', 'Resume'),
             'state' => Yii::t('accounting', $newState),
            ]));
        return $this->redirect(['details', 'id' => $model->resume_id]);
    }

    /**
     * Muestra la pantalla de detalles.
     *
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDetails($id)
    {
        $model = $this->findModel($id);

        $resumeItems = new ActiveDataProvider([
            'query' => $model->getResumeItems()->orderBy(['date'=> SORT_DESC]),
            //'pagination' => false
        ]);

        return $this->render('_details', [
            'model' => $model,
            'resumeItems' => $resumeItems
        ]);
    }

    public function actionAddItem($resume_id)
    {
        $status = 'error';
        Yii::$app->response->format = 'json';

        $model = new ResumeItem();
        $model->resume_id = $resume_id;
        $model->load(Yii::$app->request->post());

        if ( $model->validate() && $model->save()) {
            return [
                'status' => 'success'
            ];
        } else {
            return [
                'status' => 'error',
                'errors' => $model->getErrors()
            ];
        }
    }

    public function actionDeleteItem($resume_item_id)
    {
        Yii::$app->response->format = 'json';
        $status = 'error';
        if ( ResumeItem::findOne($resume_item_id)->delete()) {
            return [
                'status' => 'success'
            ];
        } else {
            return [
                'status' => 'error',
            ];
        }
    }

    public function actionResumeByAccount()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = Yii::$app->request->post('depdrop_parents');
            if ($parents != null) {
                $money_box_account_id = $parents[0];
                if($money_box_account_id) {
                    $out = Resume::findResumeByAccount($money_box_account_id)->asArray()->all();;
                    echo Json::encode(['output'=>$out, 'selected'=>'']);
                    return;
                }
            }
        }
        echo Json::encode(['output'=>'', 'selected'=>'']);
    }

    public function actionImportResume($id)
    {

        $model = $this->findModel($id);
        $model->load(Yii::$app->request->post());
        $this->import($model);


        return $this->actionDetails($id);
    }

    private function import($model){

        $file = UploadedFile::getInstance($model, 'file_import');
        if($file) {
            $dirPath = Yii::$app->params['upload_directory'] . "tmp/";
            $filePath = '/' . $dirPath. uniqid('file_import') . '.' . $file->extension;

            if (!file_exists($dirPath)) {
                mkdir($dirPath, 0775, true);
            }
            $file->saveAs(Yii::getAlias('@webroot') . $filePath);

            $oImporter = new ResumeImporter(Yii::getAlias('@webroot') . $filePath, $model->columns, $model->separator);
            $oImporter->init([
                'resume_id' => $model->resume_id,
                'money_box_account_id' => $model->money_box_account_id,
                'money_box_id' => $model->moneyBoxAccount->money_box_id,
                'account_id' => $model->account_id,
            ]);
            if(!$oImporter->import()) {
                foreach($oImporter->getErrors() as $error) {
                    Yii::$app->session->setFlash('error', $error );
                }
            }

            // Borro el archivo
            unlink(Yii::getAlias('@webroot') . $filePath);
        }
    }

    public function actionDownloadResume()
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="resumen_excel.xls"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');

        $oImporter = new ResumeImporter('', [] );
        $objWriter = $oImporter->getImportExcel();
        $objWriter->save('php://output');
    }
}

