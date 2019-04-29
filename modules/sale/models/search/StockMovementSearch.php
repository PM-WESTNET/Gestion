<?php

namespace app\modules\sale\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sale\models\StockMovement;

/**
 * StockMovementSearch represents the model behind the search form about `app\modules\sale\models\StockMovement`.
 */
class StockMovementSearch extends StockMovement
{
    
    //Solo para graph search
    public $toDate;
    public $fromDate;
    public $chartType = 'Line';
    
    public function init()
    {
        
        parent::init();
        
        $this->active = true;
        
    }
    
    public function rules()
    {
        return [
            [['stock_movement_id', 'timestamp', 'bill_detail_id', 'company_id'], 'integer'],
            [['type', 'concept', 'date'], 'safe'],
            [['qty', 'stock', 'avaible_stock'], 'number'],
            [['toDate', 'fromDate'], 'date'],
            [['toDate', 'fromDate'], 'default', 'value'=>null],
            [['chartType'], 'in', 'range'=>['Line','Bar','Radar']]
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = StockMovement::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['stock_movement_id'=>SORT_DESC]
            ]
        ]);

        $this->load($params);
        
        if (!$this->validate()) {
            return $dataProvider;
        }
        
        $query->andFilterWhere([
            'stock_movement_id' => $this->stock_movement_id,
            'qty' => $this->qty,
            'type' => $this->type,
            'timestamp' => $this->timestamp,
            'date' => $this->date,
            'stock' => $this->stock,
            'avaible_stock' => $this->avaible_stock,
            'product_id' => $this->product_id,
            'bill_detail_id' => $this->bill_detail_id,
            'company_id' => $this->company_id,
            'active' => $this->active
        ]);

        $query->andFilterWhere(['like', 'concept', $this->concept]);
        
        // Para optimizar la busqueda, buscamos por timestamp
        if(!empty($this->fromDate)){
            $query->andWhere ('timestamp>='.(int)strtotime($this->fromDate));
        }
        
        // En el caso de la fecha de fin debemos compararla como menor que el timestamp del dia siguiente para cubrir todo el dia
        if(!empty($this->toDate)){
            $toDate = (int)strtotime($this->toDate.' +1day');
            $query->andWhere ('timestamp<'.$toDate);
        }

        return $dataProvider;
    }
    
    public function graphSearch($params)
    {
        $query = StockMovement::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        
        if (!$this->validate()) {
            return $dataProvider;
        }
        
        $query->select(['timestamp'=>'MAX(timestamp)','stock_movement_id', 'qty', 'type', 'date', 'product_id', 'company_id']);
        
        $query->groupBy(['date','product_id']);
        
        $query->andFilterWhere([
            'stock_movement_id' => $this->stock_movement_id,
            'qty' => $this->qty,
            'type' => $this->type,
            'date' => $this->date,
            'stock' => $this->stock,
            'avaible_stock' => $this->avaible_stock,
            'product_id' => $this->product_id,
            'company_id' => $this->company_id
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

        $query->andFilterWhere(['like', 'concept', $this->concept]);

        return $dataProvider;
    }
}
