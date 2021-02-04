<?php

namespace app\modules\mobileapp\v1\controllers;

use Yii;
use app\modules\mobileapp\v1\models\AppFailedRegister;
use app\modules\mobileapp\v1\models\AppFailedRegisterSearch;
use app\components\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AppFailedRegisterController implements the CRUD actions for AppFailedRegister model.
 */
class AppFailedRegisterController extends Controller
{

    /**
     * Lists all AppFailedRegister models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AppFailedRegisterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AppFailedRegister model.
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
     * Deletes an existing AppFailedRegister model.
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
     * Finds the AppFailedRegister model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AppFailedRegister the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AppFailedRegister::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionClose($id){

        $model= $this->findModel($id);

        if ($model->updateAttributes(['status' => 'closed'])){
            Yii::$app->session->addFlash('success', 'Register has been closed success');
        }else{
            Yii::$app->session->addFlash('error', 'Register cant be closed');
        }

        return $this->redirect(['index']);
    }
}
