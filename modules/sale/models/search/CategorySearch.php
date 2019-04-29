<?php

namespace app\modules\sale\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sale\models\Category;

/**
 * CategorySearch represents the model behind the search form about `app\modules\sale\models\Category`.
 */
class CategorySearch extends Category
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        //MUY IMPORTANTE: al modificar los atributos que pueden ser buscados, se debe modificar la funcion isFiltered()
        return [
            [['category_id', 'parent_id'], 'integer'],
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
        $query = Category::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        
        //Si no se filtra, se muestran los resultados jerarquizados
        if(!$this->isFiltered()){
            $models = Category::getOrderedCategories();
            $dataProvider->setModels($models);
            return $dataProvider;
        }
            
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'category_id' => $this->category_id,
            'parent_id' => $this->parent_id,
            'status' => $this->status
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
    
    public function isFiltered(){
        if(empty($this->name) && empty($this->category_id) && empty($this->status) && empty($this->parent_id))
            return false;
        else
            return true;
    }
}
