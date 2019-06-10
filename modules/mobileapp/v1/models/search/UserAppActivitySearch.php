<?php

namespace app\modules\mobileapp\v1\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\mobileapp\v1\models\UserAppActivity;

/**
 * UserAppActivitySearch represents the model behind the search form of `app\modules\mobileapp\v1\models\UserAppActivity`.
 */
class UserAppActivitySearch extends UserAppActivity
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_app_activity_id', 'user_app_id', 'installation_datetime', 'last_activity_datetime'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = UserAppActivity::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'user_app_activity_id' => $this->user_app_activity_id,
            'user_app_id' => $this->user_app_id,
            'installation_datetime' => $this->installation_datetime,
            'last_activity_datetime' => $this->last_activity_datetime,
        ]);

        return $dataProvider;
    }
}
