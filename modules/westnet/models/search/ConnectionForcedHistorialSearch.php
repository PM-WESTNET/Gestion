<?php

namespace app\modules\westnet\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\models\ConnectionForcedHistorial;
use yii\db\Query;
use yii\db\Expression;

/**
 * ConnectionForcedHistorialSearch represents the model behind the search form about `app\modules\westnet\models\ConnectionForcedHistorial`.
 */
class ConnectionForcedHistorialSearch extends ConnectionForcedHistorial
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['connection_forced_historial_id', 'connection_id'], 'integer'],
            [['date', 'reason'], 'safe'],
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
        $query = ConnectionForcedHistorial::find();

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
            'connection_forced_historial_id' => $this->connection_forced_historial_id,
            'date' => $this->date,
            'connection_id' => $this->connection_id,
        ]);

        $query->andFilterWhere(['like', 'reason', $this->reason]);
        $query->orderBy(['date'=> 'ASC']);

        return $dataProvider;
    }
    
    /**
     * Busca la cantidad de veces que se forzó la conexión en un mes
     * @param $connection_id
     * 
     * 
     */
    public function countForcedTimesForConnection($connection_id){
        
        $actualDate= date('Y-m-d');       
        
        $initMonth= date('Y-m').'-01';
        
        $query= ConnectionForcedHistorial::find()
                ->where(['connection_id' => $connection_id])
                ->andWhere(['>=', 'date', $initMonth])
                ->andWhere(['<=', 'date', $actualDate]);
                
        
        return $query->count();
        
        
    }
}
