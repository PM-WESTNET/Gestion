<?php

namespace app\modules\sale\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sale\models\CustomerCategory;

/**
 * CustomerCategorySearch represents the model behind the search form about `app\modules\sale\models\CustomerCategory`.
 */
class CustomerCategorySearch extends CustomerCategory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_category_id', 'parent_id'], 'integer'],
            [['name', 'status'], 'safe'],
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
        $query = CustomerCategory::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
   
        if(!$this->isFiltered()){
            $models = CustomerCategory::getOrderedCustomerCategories();
            $dataProvider->setModels($models);
            return $dataProvider;
        }
            
 
        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'customer_category_id' => $this->customer_category_id,
            'parent_id' => $this->parent_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
    
    public function isFiltered(){
        if(empty($this->name) && empty($this->customer_category_id) && empty($this->status) && empty($this->parent_id))
            return false;
        else
            return true;
    }
}
