<?php

namespace app\modules\cobrodigital\controllers;

use app\modules\pagomiscuentas\models\PagomiscuentasFile;
use Yii;
use app\modules\cobrodigital\models\PaymentCardFile;
use app\modules\cobrodigital\models\search\PaymentCardFileSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * PaymentCardFileController implements the CRUD actions for PaymentCardFile model.
 */
class PaymentCardFileController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all PaymentCardFile models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PaymentCardFileSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PaymentCardFile model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $searchModel = new PaymentCardFileSearch();
        $dataProvider = new ActiveDataProvider([
            'query' => $model->getPaymentCards()
        ]);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Creates a new PaymentCardFile model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PaymentCardFile();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if($this->upload($model, 'file_name') && $model->save()){
                return $this->redirect(['view', 'id' => $model->payment_card_file_id]);
            } else {
                var_dump($model->getErrors());die();
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
     * Updates an existing PaymentCardFile model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->payment_card_file_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing PaymentCardFile model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PaymentCardFile model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PaymentCardFile the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PaymentCardFile::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * @param $model
     * @param $attribute
     * @return bool
     * @throws \Exception
     * Sube un archivo
     */
    public function upload($model, $attribute)
    {
        $file = UploadedFile::getInstance($model, $attribute);
        $folder = 'cobrodigital';
        if ($file) {
            var_dump($file);

            $filePath = Yii::$app->params['upload_directory'] . "$folder/". uniqid('file') . '.' . $file->extension;

            var_dump($filePath);

            if (!file_exists(Yii::getAlias('@webroot') . '/' . Yii::$app->params['upload_directory'] . "$folder/")) {
                mkdir(Yii::getAlias('@webroot') . '/' . Yii::$app->params['upload_directory'] . "$folder/", 0775, true);
            }

            $file->saveAs(Yii::getAlias('@webroot') . '/' . $filePath);

            $model->path = $filePath;
            $model->file_name = $file->name;
            $model->upload_date = (new \DateTime('now'))->format('Y-m-d');
            $model->status = PaymentCardFile::STATUS_DRAFT;
            return true;
        } else {
            return false;
        }
    }

    public function actionImport($id) {
        $model = $this->findModel($id);
        try {
            if($model->import()) {
                Yii::$app->session->setFlash('success', Yii::t('cobrodigital', 'File imported successfully'));
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', Yii::t('cobrodigital', 'An error occurred while importing file'));
                return $this->redirect(['index']);
            }
        } catch (\Exception $ex) {
            \Yii::trace($ex);die();
        }

        return $this->redirect(['index']);
    }
}
