<?php

namespace app\modules\westnet\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\models\VendorLiquidation;

/**
 * VendorLiquidationSearch represents the model behind the search form about `app\modules\westnet\models\VendorLiquidation`.
 */
class VendorLiquidationSearch extends VendorLiquidation
{
    public function init()
    {
        $this->status = null;
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vendor_liquidation_id', 'vendor_id'], 'integer'],
            [['date', 'period', 'status'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = VendorLiquidation::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'period' => SORT_DESC,
                    'vendor_liquidation_id' => SORT_DESC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'vendor_liquidation_id' => $this->vendor_liquidation_id,
            'vendor_id' => $this->vendor_id,
            'date' => $this->date,
        ]);
        
        if($this->period){
            $year = Yii::$app->formatter->asDate($this->period, 'yyyy');
            $month = Yii::$app->formatter->asDate($this->period, 'MM');

            $query->andWhere("YEAR(period)='$year' AND MONTH(period)='$month'");
        }

        $query->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
