<?php

namespace app\modules\sale\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sale\models\Product;

/**
 * ProductSearch represents the model behind the search form about `app\modules\sale\models\Product`.
 */
class ProductSearch extends Product
{
    
    public $search_text;
    
    //Para utilizar al mostrar stock:
    public $showStockByCompany;
    //Para stock por empresa:
    public $stock_company_id;
    
    /**
     * Pisamos la relacion categories y utilizamos esta propiedad para buscar por categorias.
     * @var []
     */
    public $categories;

    public function rules()
    {
        return [
            [['product_id', 'create_timestamp', 'update_timestamp', 'unit_id'], 'integer'],
            [['name', 'system', 'code', 'description', 'status'], 'safe'],
            [['search_text','categories'], 'safe'],
            [['type'],'in','range'=>['product','service','plan']],
            [['stock_company_id'], 'integer'],
            [['showStockByCompany'], 'boolean']
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $type='product';
        $query = Product::find();
        $query->andFilterWhere(['like', 'product.type', $type]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['product_id'=>SORT_DESC]
            ]
        ]);
        
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'product.product_id' => $this->product_id,
            'product.create_timestamp' => $this->create_timestamp,
            'product.update_timestamp' => $this->update_timestamp,
            'product.unit_id' => $this->unit_id,
        ]);
        
        //Join con categorias
        if(!empty($this->categories)){
            $query->joinWith(['categories']);
            $query->andFilterWhere(['in','category.category_id',$this->categories]);
        }
        
        $code = $this->parseVariableCode($this->code);

        $query->andFilterWhere(['like', 'product.code', $this->code])
            ->orFilterWhere(['like', 'product.code', $code])
            ->andFilterWhere(['like', 'product.name', $this->name])
            ->andFilterWhere(['like', 'product.description', $this->description])
            ->andFilterWhere(['like', 'product.status', $this->status])
            ->andFilterWhere(['like', 'product.class', $this->type]);
        
        
        return $dataProvider;
    }
    
    public function searchText($params){
        
        $query = Product::find();
        $type='product';
        $query->andFilterWhere(['like', 'product.type', $type]);
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
    
    public function searchFlex($params = null){
        
        $query = Product::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['product_id'=>SORT_DESC]
            ]
        ]);

        if ($params != null && !($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $searchHelper = new \app\components\helpers\SearchStringHelper();
        $searchHelper->string = $this->search_text;
        
        //Separamos las palabras de busqueda
        $words = $searchHelper->getSearchWords('%{word}%');
        
        $operator = 'like';
        
        //Alias necesarios para evitar ambiguedades
        $query->andWhere([$operator,'product.code',$words,false])
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
        
        $query->andFilterWhere(['product.status' => 'enabled']);
        
        return $dataProvider;
        
    }
    
}
