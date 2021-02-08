<?php

namespace app\modules\log\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\log\models\Log;
use app\modules\log\LogModule;

/**
 * LogSearch represents the model behind the search form about `app\modules\log\models\Log`.
 */
class LogSearch extends Log
{

    public $username;
    public $fromDate;
    public $toDate;
    public $search_text;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['log_id', 'user_id', 'datetime', 'model_id'], 'integer'],
            [['route', 'model', 'username', 'old_value', 'new_value', 'post', 'get', 'attribute', 'search_text', 'fromDate', 'toDate', 'search_text'], 'safe'],
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
                'defaultOrder' => ['log_id' => SORT_DESC]
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
        
        $query->andFilterWhere(['route' => $this->route])
                ->andFilterWhere(['like', 'model', $this->model]);

        if($this->fromDate && $this->toDate){
            $query->andFilterWhere(['between', 'datetime', (new \Datetime($this->fromDate.' 00:00:00'))->getTimestamp(), (new \Datetime($this->toDate.' 23:23:59'))->getTimestamp()]);
        } else {
            if($this->fromDate){
                $query->andFilterWhere(['>=', 'datetime', (new \Datetime($this->fromDate.' 00:00:00'))->getTimestamp()]);
            }

            if($this->toDate){
                $query->andFilterWhere(['<=', 'datetime', (new \Datetime($this->toDate.' 23:23:59'))->getTimestamp()]);
            }
        }

        if($this->search_text){
            $query->andFilterWhere(['or', ['like', 'old_value', $this->search_text], ['like', 'new_value', $this->search_text]]);
        }

        return $dataProvider;
    }

    public function filterUsername($query)
    {
        if ($this->username) {

            $users_ids = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('user')
                    ->where(['like', 'username', $this->username])
                    ->all();

            $users_ids = array_map(function($user) {
                return $user['id'];
            }, $users_ids);

            $query->andWhere(['user_id' => $users_ids]);
        }
    }

}
