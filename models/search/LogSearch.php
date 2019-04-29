<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Log;

/**
 * LogSearch represents the model behind the search form about `app\models\Log`.
 */
class LogSearch extends Log
{
    
    public $username;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['log_id', 'user_id', 'datetime', 'model_id'], 'integer'],
            [['route', 'model', 'data', 'username'], 'safe'],
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
        $query = Log::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [ 'log_id' => SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'log_id' => $this->log_id,
            'user_id' => $this->user_id,
            'datetime' => $this->datetime,
            'model_id' => $this->model_id,
        ]);
        
        $this->filterUsername($query);

        $query->andFilterWhere(['like', 'route', $this->route])
            ->andFilterWhere(['like', 'model', $this->model])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
    
    public function filterUsername($query)
    {
        if($this->username){

            $users_ids = (new \yii\db\Query())
                ->select(['id'])
                ->from('user')
                ->where(['like', 'username', $this->username])
                ->all();
            
            $users_ids = array_map(function($user){ return $user['id']; }, $users_ids);
            
            $query->andWhere(['user_id' => $users_ids]);
            
        }
    }
}
