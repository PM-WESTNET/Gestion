<?php

namespace app\modules\westnet\controllers;

use app\components\user\User;
use Yii;
use app\modules\westnet\models\search\VendorSearch;
use app\components\web\Controller;
use yii\db\Transaction;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\sale\models\Address;
use app\modules\westnet\models\Vendor;

/**
 * VendorController implements the CRUD actions for Vendor model.
 */
class VendorController extends Controller {

    public function behaviors() {
        return array_merge(parent::behaviors(), [
        ]);
    }

    /**
     * Lists all Vendor models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new VendorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Displays a single Vendor model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Vendor model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Vendor();
        $address = new Address();
        $user = new User();
        $user->scenario = User::SCENARIO_CREATE;
        $post = Yii::$app->request->post();

        if($model->load($post) && $address->load($post) && $user->load($post)){
            if($model->validate() && $address->validate() && $user->validate()){
                $transaction = new Transaction();
                $transaction = Yii::$app->db->beginTransaction();
                if($address->save() && $user->save()){
                    $model->address_id = $address->address_id;
                    $model->user_id = $user->id;
                    if($model->save()){
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $model->vendor_id]);
                    }
                }
                $transaction->rollBack();
            }
        }

        return $this->render('create', [
            'model' => $model,
            'address' => $address,
            'user' => $user
        ]);
    }

    /**
     * Updates an existing Vendor model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $address = $model->address;
        $user = $model->user;
        $post = Yii::$app->request->post();

        if (empty($address)) {
            $address = new Address();
        }
        if (empty($user)) {
            $user = new User();
        }

        if($model->load($post) && $address->load($post) && $user->load($post)){
            if($model->validate() && $address->validate() && $user->validate()){
                $transaction = new Transaction();
                $transaction = Yii::$app->db->beginTransaction();
                if($address->save() && $user->save()){
                    $model->address_id = $address->address_id;
                    $model->user_id = $user->id;
                    if($model->save()){
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $model->vendor_id]);
                    }
                }
                $transaction->rollBack();
            }
        }

        return $this->render('update', [
            'model' => $model,
            'user' => $user,
            'address' => $address,
        ]);
    }

    /**
     * Deletes an existing Vendor model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Vendor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Vendor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Vendor::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
