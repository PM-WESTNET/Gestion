<?php

namespace app\modules\accounting\controllers;

use app\components\web\Controller;
use app\modules\accounting\models\MoneyBoxHasOperationType;
use Yii;
use app\modules\accounting\models\MoneyBox;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MoneyBoxController implements the CRUD actions for MoneyBox model.
 */
class MoneyBoxController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ]);
    }

    /**
     * Lists all MoneyBox models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => MoneyBox::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MoneyBox model.
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
     * Creates a new MoneyBox model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MoneyBox();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->money_box_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing MoneyBox model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->money_box_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing MoneyBox model.
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
     * Finds the MoneyBox model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MoneyBox the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MoneyBox::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Agrega un item movimiento
     * @param $id
     */
    public function actionAddOperationType($money_box_id=null, $money_box_has_operation_type_id=null)
    {
        $model = $this->findModel($money_box_id);

        $item = new MoneyBoxHasOperationType();
        $id = $money_box_has_operation_type_id ? $money_box_has_operation_type_id : Yii::$app->request->post('MoneyBoxHasOperationType')['money_box_has_operation_type_id'];
        if($id) {
            $item = MoneyBoxHasOperationType::findOne(['money_box_has_operation_type_id'=>$id]);
        }
        if ($item->load(Yii::$app->request->post()) ) {
            if($id) {
                $item->money_box_has_operation_type_id = $id;
                $item->updateAttributes(['code', 'operation_type_id', 'money_box_id', 'account_id', 'money_box_account_id']);
            } else {
                $item->save();
            }
            $item = new MoneyBoxHasOperationType();
        }

        return $this->renderAjax('_form-operation-type', [
            'model' => $model,
            'item'  => $item
        ]);
    }

    /**
     * Se listan los items del movimiento.
     *
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionListOperationType($money_box_id)
    {
        $model = $this->findModel($money_box_id);

        $items = new ActiveDataProvider([
            'query' => $model->getMoneyBoxHasOperationTypes()
        ]);

        return $this->renderAjax('_form-list-operation-type', [
            'model' => $model,
            'items' => $items
        ]);
    }


    /**
     * Borra un item movimiento
     *
     * @param $id
     * @param $account_movement_item_id
     */
    public function actionDeleteOperationType($money_box_has_operation_type_id)
    {
        $item = MoneyBoxHasOperationType::findOne( $money_box_has_operation_type_id);
        $money_box_id = $item->money_box_id;
        if($item) {
            $item->delete();
        }
        return $this->actionListOperationType($money_box_id);
    }
}
