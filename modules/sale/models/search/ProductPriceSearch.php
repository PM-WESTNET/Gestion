<?php

namespace app\modules\sale\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sale\models\ProductPrice;

/**
 * ProductPriceSearch represents the model behind the search form about `app\modules\sale\models\ProductPrice`.
 */
class ProductPriceSearch extends ProductPrice 
{
    
    //Solo para graph search
    public $toDate;
    public $fromDate;
    public $chartType = 'Line';
    
    public function rules()
    {
        return [
            [['product_price_id', 'timestamp', 'exp_timestamp', 'update_timestamp', 'product_id'], 'integer'],
            [['net_price', 'taxes'], 'number'],
            [['date', 'time', 'exp_date', 'exp_time', 'status'], 'safe'],
            [['toDate', 'fromDate'], 'default', 'value'=>null],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = ProductPrice::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder'=>'product_price_id DESC'
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'product_price_id' => $this->product_price_id,
            'net_price' => $this->net_price,
            'taxes' => $this->taxes,
            'date' => $this->date,
            'time' => $this->time,
            'timestamp' => $this->timestamp,
            'exp_timestamp' => $this->exp_timestamp,
            'exp_date' => $this->exp_date,
            'exp_time' => $this->exp_time,
            'update_timestamp' => $this->update_timestamp,
            'product_id' => $this->product_id,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
    
    public function graphSearch($params)
    {
        $query = ProductPrice::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $this->load($params);
        
        if (!$this->validate()) {
            return $dataProvider;
        }
        
        $query->select(['timestamp'=>'MAX(timestamp)','product_price_id', 'net_price', 'taxes', 'date', 'product_id']);
        
        $query->groupBy(['date','product_id']);
        
        $query->andFilterWhere([
            'product_price_id' => $this->product_price_id,
            'net_price' => $this->net_price,
            'date' => $this->date,
            'taxes' => $this->taxes,
            'product_id' => $this->product_id,
        ]);
        
        // Para optimizar la busqueda, buscamos por timestamp
        if(!empty($this->fromDate)){
            $query->andWhere ('timestamp>='.(int)strtotime($this->fromDate));
        }
        
        // En el caso de la fecha de fin debemos compararla como menor que el timestamp del dia siguiente para cubrir todo el dia
        if(!empty($this->toDate)){
            $toDate = (int)strtotime($this->toDate.' +1day');
            $query->andWhere ('timestamp<'.$toDate);
        }
        
        $query->orderBy(['timestamp'=>SORT_ASC]);

        return $dataProvider;
    }
}
