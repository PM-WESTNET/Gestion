<?php

namespace app\modules\partner\controllers;

use app\modules\partner\components\Liquidation;
use app\modules\partner\models\PartnerLiquidation;
use app\modules\partner\models\search\PartnerLiquidationSearch;
use app\modules\partner\models\search\PartnerSearch;
use Yii;
use app\modules\partner\models\Partner;
use yii\data\ActiveDataProvider;
use app\components\web\Controller;
use yii\filters\VerbFilter;

/**
 * PartnerController implements the CRUD actions for Partner model.
 */
class LiquidationController extends Controller
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
        $accounts = (new PartnerLiquidationSearch())->searchAccounts([])->all();
        return $this->render('index', [
            'accounts' => $accounts,
        ]);
    }

    public function actionLiquidate()
    {
        $liquidate = new Liquidation();
        if(!$liquidate->liquidate()){
            foreach($liquidate->getMessages() as $key=>$value) {
                Yii::$app->session->addFlash($value['type'], $value['message']);
            }
        } else {
            Yii::$app->session->addFlash('success', Yii::t('partner', 'The Liquidations was executed successfully'));
        }
        
        return $this->actionIndex();
    }

    public function actionListLiquidation()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => (new PartnerLiquidationSearch())->searchLiquidations(Yii::$app->request->queryParams),
        ]);

        return $this->render('list-liquidation', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionLiquidationItems()
    {
        $searchModel = new PartnerLiquidationSearch();

        $dataProvider = new ActiveDataProvider([
            'query' => $searchModel->searchLiquidationItems(Yii::$app->request->queryParams),
        ]);
        $model = PartnerLiquidation::findOne(['partner_liquidation_id'=>$searchModel->partner_liquidation_id]);

        return $this->render('liquidation-items', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model
        ]);
    }
}