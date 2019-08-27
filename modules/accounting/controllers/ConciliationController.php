<?php

namespace app\modules\accounting\controllers;

use app\modules\accounting\components\CountableMovement;
use app\modules\accounting\models\AccountMovementItem;
use app\modules\accounting\models\ConciliationItem;
use app\modules\accounting\models\ConciliationItemHasResumeItem;
use app\modules\accounting\models\OperationType;
use app\modules\accounting\models\Resume;
use app\modules\accounting\models\ResumeItem;
use app\modules\accounting\models\search\AccountMovementSearch;
use app\modules\accounting\models\search\ResumeSearch;
use Yii;
use app\modules\accounting\models\Conciliation;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ConciliationController implements the CRUD actions for Conciliation model.
 */
class ConciliationController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
        ]);
    }
    
    /**
     * Lists all Conciliation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Conciliation::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Conciliation model.
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
     * Creates a new Conciliation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Conciliation();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['conciliate', 'id' => $model->conciliation_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Conciliation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->conciliation_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Conciliation model.
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
     * Finds the Conciliation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Conciliation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Conciliation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Nuestra una consiliacion para poder ser llenada.
     *
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionConciliate($id, $readOnly=false)
    {
        $model = $this->findModel($id);
        // Busco los movimientos de cuenta en el rango de fechas de la conciliacion.
        // y teniendo en cuenta que no sean movimientos cerrados
        $searchModel = new AccountMovementSearch();
        $searchModel->account_id_from = $model->moneyBoxAccount->account->lft;
        $searchModel->account_id_to = $model->moneyBoxAccount->account->rgt;
        $searchModel->fromDate = Yii::$app->formatter->asDate($model->date_from, 'yyyy-MM-dd');
        $searchModel->toDate = Yii::$app->formatter->asDate($model->date_to, 'yyyy-MM-dd');

        $movementsDataProvider = $searchModel->searchForConciliation([]);

        $totalAccountCredit = $searchModel->totalCredit;
        $totalAccountDebit = $searchModel->totalDebit;

        $conciliatedDataProvider = new ActiveDataProvider([
            'query' => $model->getConciliationItems(),
        ]);


        $operationTypes = ArrayHelper::map(OperationType::find()->all(), 'operation_type_id', 'name');

        $this->layout = '/fluid';
        return $this->render( 'conciliate',[
            'model' => $model,
            'conciliatedDataProvider' => $conciliatedDataProvider,
            'movementsDataProvider' => $movementsDataProvider,
            'totalAccountDebit' => $totalAccountDebit,
            'totalAccountCredit' => $totalAccountCredit,
            'readOnly' =>$readOnly,
            'operationTypes' => $operationTypes
        ]);
    }

    public function actionGetAccountMovements($id, $readOnly=false)
    {
        $model = $this->findModel($id);
        // Busco los movimientos de cuenta en el rango de fechas de la conciliacion.
        // y teniendo en cuenta que no sean movimientos cerrados
        $searchModel = new AccountMovementSearch();
        $searchModel->account_id_from = $model->moneyBoxAccount->account->lft;
        $searchModel->account_id_to = $model->moneyBoxAccount->account->rgt;
        $searchModel->fromDate = Yii::$app->formatter->asDate($model->date_from, 'yyyy-MM-dd');
        $searchModel->toDate = Yii::$app->formatter->asDate($model->date_to, 'yyyy-MM-dd');

        $params = Yii::$app->request->post();

        $movementsDataProvider = $searchModel->searchForConciliation($params);

        return $this->renderAjax('_movements', ['readOnly' => $readOnly, 'movementsDataProvider' => $movementsDataProvider]);
    }

    public function actionGetResumeItems($readOnly=false)
    {
        $resumeItemsDataProvider = null;
        $resumeSearch = new ResumeSearch();
        $params = Yii::$app->request->post();

        $resumeItemsDataProvider = new ActiveDataProvider([
            'query' => $resumeSearch->searchForConciliation($params)
        ]);



        $model = Resume::findOne(['resume_id' => $params['ResumeSearch']['resume_id']]);
        $resumeItemsDataProvider->setPagination(false);
        return $this->renderAjax('_resume_items', [
            'resumeItemsDataProvider' => $resumeItemsDataProvider,
            'model' => $model,
            'readOnly' => $readOnly
        ]);
    }


    /**
     *
     *
     * @param $movements
     * @param $resume_items
     */
    public function actionConciliar($conciliation_id)
    {
        $status = 'error';
        $message = '';
        Yii::$app->response->format = 'json';
        $movements_ids = Yii::$app->request->post('movementItems');
        $resume_items_ids = Yii::$app->request->post('resumeItems');

        $model = $this->findModel($conciliation_id);
        $amount = 0;

        // Busco los totales
        $sumMovements = AccountMovementItem::find()
            ->andWhere(['account_movement_item_id' => $movements_ids])
            ->andWhere(['status' => 'draft'])
            ->sum('COALESCE(debit,0)+COALESCE(credit,0)');

        $sumResume = ResumeItem::find()
            ->andWhere(['resume_item_id' => $resume_items_ids])
            ->andWhere(['status' => 'draft'])
            ->sum('COALESCE(debit,0)+COALESCE(credit,0)');


        // En el caso de que no exista movimiento pero si item en el resumen,
        // Genero Items de la conciliacion
        if (count($movements_ids)==0 && count($resume_items_ids) > 0) {
            // Para cada item del resumen creo un item de la conciliacion
            $item = new ConciliationItem();
            $item->conciliation_id = $conciliation_id;
            $item->date = date('d-m-Y');
            $item->save();
            $operation= null;
            foreach($resume_items_ids as $key => $value) {
                if(($resModel=ResumeItem::findOne($value))!==null) {
                    $operation = $resModel->operationType;
                    $item->addResumeItem($resModel->resume_item_id);
                    $resModel->updateAttributes(['ready' => true]);
                    $status = "success";
                }
            }
            $item->amount += $sumResume + $sumMovements;
            $item->description = $operation->name;
            $item->save();
            Yii::info($item->hasErrors());
        } else {

            // Si son iguales marco todo
            if ($sumMovements == $sumResume) {
                $bOk = true;

                $mMovements = AccountMovementItem::findAll(['account_movement_item_id'=>$movements_ids]);
                $mResumeItems = ResumeItem::findAll(['resume_item_id'=>$resume_items_ids]);

                $item = new ConciliationItem();
                $item->conciliation_id = $conciliation_id;
                $item->amount = $sumResume;
                $item->description = Yii::t('accounting', 'Conciliation of {movement} and {resume}.', ['movement'=>implode(",", $movements_ids), 'resume'=>implode(",", $resume_items_ids)] );
                $item->date = date('d-m-Y');
                $item->save();

                // Asocio los movmientos
                foreach($mMovements as $mov) {
                    $item->addAccountItem($mov->account_movement_item_id);
                    $mov->updateAttributes(['ready' => true]);
                }

                // Asocio los items del resumen
                foreach($mResumeItems as $res) {
                    $item->addResumeItem($res->resume_item_id);
                    $res->updateAttributes(['ready' => true]);
                }

                $item->save();

                $status = 'success';

            } else if ($sumMovements > $sumResume) {
                $message = Yii::t('accounting', 'The movements selected are greater than resume items. ' . $sumMovements ." > " . $sumResume);
            } else if ($sumMovements < $sumResume) {
                $message = Yii::t('accounting', 'The resume items selected are greater than movementes. ' . $sumMovements ." < ". $sumResume);
            }
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    public function actionDeconciliate($conciliation_id)
    {
        $status = 'error';
        $message = '';
        Yii::$app->response->format = 'json';
        $keys = Yii::$app->request->post('keys');

        if (count($keys)>0) {
            foreach ($keys as $key=>$value ) {
                $model = ConciliationItem::findOne($value);

                foreach ($model->resumeItems as $item){
                    $item->updateAttributes(['ready' => false]);
                }

                foreach ($model->accountMovementItems as $item) {
                    $item->updateAttributes(['ready' => false]);
                }


                $model->delete();
            }
            $status = 'success';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    /**
     * Cierra la conciliacion
     *
     * @param $conciliation_id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionClose($conciliation_id)
    {
        $status = 'error';
        $message = '';
        Yii::$app->response->format = 'json';

        $model = $this->findModel($conciliation_id);
        try {
            if ($model->validateBalance()) {
                $model->close();
                $model->resume->changeState('conciled');
                $status = 'success';
                Yii::$app->session->addFlash('success', Yii::t('accounting', 'Conciliation closed.'));
            }else {
                Yii::$app->session->addFlash('error', Yii::t('accounting', 'The balance of account is not equals to balance of resume'));
            }
        } catch(\Exception $ex) {
            Yii::debug($ex->getTraceAsString());
            $message = Yii::t('accounting', $ex->getMessage());
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    /**
     * Muestra la conciliacion
     *
     * @param $conciliation_id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionViewConciliation($conciliation_id)
    {
        $model = $this->findModel($conciliation_id);

        return $this->render('conciliation', [
            'model' => $model
        ]);
    }
}