<?php

namespace app\modules\westnet\notifications\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\notifications\models\IntegratechSmsFilter;

/**
 * IntegratechSmsFilterSearch represents the model behind the search form about `app\modules\westnet\notifications\models\IntegratechSmsFilter`.
 */
class IntegratechSmsFilterSearch extends IntegratechSmsFilter
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['integratech_sms_filter_id'], 'integer'],
            [['word', 'action'], 'safe'],
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
        $query = IntegratechSmsFilter::find();

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
            'integratech_sms_filter_id' => $this->integratech_sms_filter_id,
        ]);

        $query->andFilterWhere(['like', 'word', $this->word])
            ->andFilterWhere(['like', 'action', $this->action]);

        return $dataProvider;
    }
}
