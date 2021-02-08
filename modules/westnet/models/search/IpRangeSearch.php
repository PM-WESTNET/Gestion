<?php

namespace app\modules\westnet\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\models\IpRange;

/**
 * IpRangeSearch represents the model behind the search form about `app\modules\westnet\models\IpRange`.
 */
class IpRangeSearch extends IpRange
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ip_range_id', 'node_id'], 'integer'],
            [['ip_start', 'ip_end', 'status'], 'safe'],
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
        $query = IpRange::find();

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
            'ip_range_id' => $this->ip_range_id,
            'node_id' => $this->node_id,
        ]);

        $query->andFilterWhere(['like', 'ip_start', $this->ip_start])
            ->andFilterWhere(['like', 'ip_end', $this->ip_end])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
