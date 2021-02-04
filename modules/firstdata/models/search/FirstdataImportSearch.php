<?php

namespace app\modules\firstdata\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\firstdata\models\FirstdataImport;

/**
 * FirstdataImportSearch represents the model behind the search form of `app\modules\firstdata\models\FirstdataImport`.
 */
class FirstdataImportSearch extends FirstdataImport
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['firstdata_import_id', 'presentation_date', 'created_at'], 'integer'],
            [['status', 'response_file', 'observation_file'], 'safe'],
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
        $query = FirstdataImport::find();

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
            'firstdata_import_id' => $this->firstdata_import_id,
            'presentation_date' => $this->presentation_date,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'response_file', $this->response_file])
            ->andFilterWhere(['like', 'observation_file', $this->observation_file]);

        return $dataProvider;
    }
}
