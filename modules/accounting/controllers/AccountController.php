<?php

namespace app\modules\accounting\controllers;

use app\components\web\Controller;
use Yii;
use app\modules\accounting\models\Account;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AccountController implements the CRUD actions for Account model.
 */
class AccountController extends Controller
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
     * Lists all Account models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Account::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Account model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $this->layout = '//embed';
        return $this->render('_view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Account model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($parent_account_id=0)
    {
        set_time_limit(0);
        $model = new Account();
        $this->layout = '//embed';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->updateTree();
            $model->updateCode();
            return $this->redirect(['view', 'id' => $model->account_id]);
        } else {
            $model->parent_account_id = $parent_account_id;

            return $this->render('_form', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Account model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        set_time_limit(0);
        $model = $this->findModel($id);
        $this->layout = '//embed';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->updateTree();
            $model->updateCode();
            return $this->redirect(['view', 'id' => $model->account_id]);
        } else {
            return $this->render('_form', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Account model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        set_time_limit(0);
        Yii::$app->response->format = 'json';
        $success = "success";
        try {
            $this->findModel($id)->delete();
        } catch(\Exception $ex) {
            $success = "fail";
        }

        return [
            'status' => $success
        ];
    }

    /**
     * Finds the Account model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Account the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Account::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionListtreeaccounts()
    {
        Yii::$app->response->format = 'json';
        $data[] =[
            'id'    => '0',
            'parent'=> '#',
            'text'  => 'Plan de Cuentas'
        ];

        $accounts = Account::getForTree();
        foreach($accounts as $account) {
            $data[] = [
                'id'    => $account->account_id,
                'parent'=> ($account->parent_account_id==null ? "0" : $account->parent_account_id),
                'text'  => $account->name
            ];
        }

        return $data;
    }

    public function actionMoveaccount()
    {
        set_time_limit(0);
        Yii::$app->response->format = 'json';
        $id = Yii::$app->request->post("id");
        $to = Yii::$app->request->post("to");

        $status = "fail";
        try {
            if ($id != $to) {
                $model = $this->findModel($id);
                $model->parent_account_id = ($to==0 ? null : $to);

                if ($model->save()) {
                    $model->updateTree();
                    $model->updateCode();
                    $status = "success";
                }
            }
        } catch (\Exception $ex) {
        }

        return [
            'status' => $status
        ];
    }


}
