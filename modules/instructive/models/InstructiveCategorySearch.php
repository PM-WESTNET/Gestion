<?php

namespace app\modules\instructive\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\instructive\models\InstructiveCategory;

/**
 * InstructiveCategorySearch represents the model behind the search form about `app\modules\instructive\models\InstructiveCategory`.
 */
class InstructiveCategorySearch extends InstructiveCategory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['instructive_category_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'safe'],
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
        $query = InstructiveCategory::find();

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
            'instructive_category_id' => $this->instructive_category_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
