<?php

namespace app\modules\config\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\config\models\Item;

/**
 * ItemSearch represents the model behind the search form about `app\modules\config\models\Item`.
 */
class ItemSearch extends Item
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_id', 'multiple', 'category_id'], 'integer'],
            [['attr', 'type', 'default', 'label', 'description'], 'safe'],
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
        $query = Item::find();

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
            'item_id' => $this->item_id,
            'multiple' => $this->multiple,
            'category_id' => $this->category_id,
        ]);

        $query->andFilterWhere(['like', 'attr', $this->attr])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'default', $this->default])
            ->andFilterWhere(['like', 'label', $this->label])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
