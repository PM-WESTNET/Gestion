<?php

namespace app\modules\westnet\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\models\EmptyAds;

/**
 * EmptyAdsSearch represents the model behind the search form about `app\modules\westnet\models\EmptyAds`.
 */
class EmptyAdsSearch extends EmptyAds
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['empty_ads_id', 'code', 'node_id', 'used', 'company_id'], 'integer'],
            [['payment_code'], 'safe'],
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
        $query = EmptyAds::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'empty_ads_id' => $this->empty_ads_id,
            'code' => $this->code,
            'node_id' => $this->node_id,
            'used' => $this->used,
            'company_id' => $this->company_id,
        ]);

        $query->andFilterWhere(['like', 'payment_code', $this->payment_code]);

        return $dataProvider;
    }
    
    public function searchForAutocomplete($code)
    {
        $query = EmptyAds::find();
       

        $query->andFilterWhere(['like', 'code', $code ]);
        
        $query->andFilterWhere(['used' => false]);

        $adses=$query->all();
        
        $response=['results' => []];
        
        foreach ($adses as $ads){
            $response['results'][] = ['id'=> $ads->code, 'text' => $ads->code];
        }
        return $response;
    }
}
