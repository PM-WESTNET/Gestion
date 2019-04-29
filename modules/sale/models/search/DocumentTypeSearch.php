<?php

namespace app\modules\sale\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sale\models\DocumentType;

/**
 * DocumentTypeSearch represents the model behind the search form about `app\modules\sale\models\DocumentType`.
 */
class DocumentTypeSearch extends DocumentType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['document_type_id', 'code'], 'integer'],
            [['name', 'regex'], 'safe'],
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
        $query = DocumentType::find();

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
            'document_type_id' => $this->document_type_id,
            'code' => $this->code,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'regex', $this->regex]);

        return $dataProvider;
    }
}
