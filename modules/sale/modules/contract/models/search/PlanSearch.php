<?php

namespace app\modules\sale\modules\contract\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sale\modules\contract\models\Plan;

/**
 * PlanSearch represents the model behind the search form about `app\modules\sale\models\Plan`.
 */
class PlanSearch extends Plan
{
    public $search_text;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'create_timestamp', 'update_timestamp', 'unit_id'], 'integer'],
            [['name', 'system', 'code', 'description', 'status', 'type', 'uid', 'search_text'], 'safe'],
            [['balance'], 'number'],
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
    public static function getdataProvider($model = null)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $model->getPlanFeatures(),
            'pagination' => [
            'pageSize' => 10,
            ],
        ]);
        return $dataProvider;
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
        $query = Plan::find();

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
            'product_id' => $this->product_id,
            'balance' => $this->balance,
            'create_timestamp' => $this->create_timestamp,
            'update_timestamp' => $this->update_timestamp,
            'unit_id' => $this->unit_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'system', $this->system])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'uid', $this->uid]);
            //->orFilterWhere(['like', 'name', $this->search_text]);
        
        return $dataProvider;
    }
    
    public function searchText($params){
        
        $query = Plan::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                //'defaultOrder' => ['product_id'=>SORT_DESC]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $searchHelper = new \app\components\helpers\SearchStringHelper();
        $searchHelper->string = $this->search_text;
        
        //Separamos las palabras de busqueda
        $words = $searchHelper->getSearchWords('%{word}%');
        
        $operator = 'like';
        
        //Alias necesarios para evitar ambiguedades
        $query->where([$operator,'product.code',$words,false])
            ->orWhere([$operator,'product.name',$words,false])
            ->orWhere([$operator,'product.product_id',$words,false])
            ->orWhere([$operator,'product.description',$words,false]);
        
        //Join con categorias
        $query->joinWith(['categories']);
        $query->orWhere([$operator,'category.name',$words,false]);
        
        //Recuperamos las palabras nuevamente sin %
        $words = $searchHelper->getSearchWords('{word}');
        foreach($words as $word){
            $code = $this->parseVariableCode($word);
            $query->orFilterWhere([$operator, 'code', $code, false]);
        }
        
        return $dataProvider;
        
    }
}
