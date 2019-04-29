<?php

namespace app\modules\config\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\config\models\Rule;

/**
 * RuleSearch represents the model behind the search form about `app\modules\config\models\Rule`.
 */
class RuleSearch extends Rule
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rule_id', 'item_id'], 'integer'],
            [['message', 'pattern', 'format', 'targetAttribute', 'targetClass', 'validator'], 'safe'],
            [['max', 'min'], 'number'],
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
        $query = Rule::find();

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
            'rule_id' => $this->rule_id,
            'max' => $this->max,
            'min' => $this->min,
            'item_id' => $this->item_id,
        ]);

        $query->andFilterWhere(['like', 'message', $this->message])
            ->andFilterWhere(['like', 'pattern', $this->pattern])
            ->andFilterWhere(['like', 'format', $this->format])
            ->andFilterWhere(['like', 'targetAttribute', $this->targetAttribute])
            ->andFilterWhere(['like', 'targetClass', $this->targetClass])
            ->andFilterWhere(['like', 'validator', $this->validator]);

        return $dataProvider;
    }
}
