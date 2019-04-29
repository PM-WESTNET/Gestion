<?php

namespace app\modules\sale\modules\contract\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sale\modules\contract\models\PlanFeature;

/**
 * PlanFeatureSearch represents the model behind the search form about `app\modules\sale\models\PlanFeature`.
 */
class PlanFeatureSearch extends PlanFeature
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['plan_feature_id', 'parent_id'], 'integer'],
            [['name', 'type', 'parent_id'], 'safe'],
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
        $query = PlanFeature::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        
        //Si no se filtra, se muestran los resultados jerarquizados
        if(!$this->isFiltered()){
            $models = PlanFeature::getOrderedPlanFeatures();
            $dataProvider->setModels($models);
            return $dataProvider;
        }
        
        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'plan_feature_id' => $this->plan_feature_id,
            'parent_id' => $this->parent_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['parent_id' => $this->parent_id]);

        return $dataProvider;
    }
    
    public function isFiltered(){
        if(empty($this->name) && empty($this->type) && empty($this->parent_id))
            return false;
        else
            return true;
    }
}
