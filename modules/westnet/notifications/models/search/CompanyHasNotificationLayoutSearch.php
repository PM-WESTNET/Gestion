<?php

namespace app\modules\westnet\notifications\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\notifications\models\CompanyHasNotificationLayout;

/**
 * CompanyHasNotificationLayoutSearch represents the model behind the search form of `app\modules\westnet\notifications\models\CompanyHasNotificationLayout`.
 */
class CompanyHasNotificationLayoutSearch extends CompanyHasNotificationLayout
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'company_id', 'is_enabled'], 'integer'],
            [['layout_path'], 'safe'],
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
        $query = CompanyHasNotificationLayout::find();

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
            'id' => $this->id,
            'company_id' => $this->company_id,
            'is_enabled' => $this->is_enabled,
        ]);

        $query->andFilterWhere(['like', 'layout_path', $this->layout_path]);

        return $dataProvider;
    }
}
