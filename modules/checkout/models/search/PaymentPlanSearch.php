<?php

namespace app\modules\checkout\models\search;

use app\modules\checkout\models\PaymentPlan;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * PaymentSearch represents the model behind the search form about `app\modules\checkout\models\Payment`.
 */
class PaymentPlanSearch extends PaymentPlan
{

    public $id_customer;


    /**
     * @inheritdoc
     */
    
    public function rules() {
        return array_merge(parent::rules(), [
            [['id_customer'], 'safe'],
        ]);

    }

    public function attributeLabels() {
        return array_merge(parent::attributeLabels(), [
            'id_customer' => Yii::t('app', 'Customer'),
        ]);
    }
    /**
     * @inheritdoc
     */


    public function search($params){
        $query= PaymentPlan::find();

        $dataProvider= new ActiveDataProvider(
                [
                   'query' => $query
        ]);

        error_log('Estoy en search');
        $this->load($params);

        $query->leftJoin('customer cus', 'cus.customer_id = payment_plan.customer_id');
        if (!empty($this->id_customer)) {
            $query->andFilterWhere(['like', 'payment_plan.customer_id', $this->id_customer]);
        }

        if (!empty($this->from_date)) {
            $query->andFilterWhere(['>=','payment_plan.from_date', Yii::$app->formatter->asDate($this->from_date, 'yyyy-MM-dd')]);
        }


        return $dataProvider;

    }

}