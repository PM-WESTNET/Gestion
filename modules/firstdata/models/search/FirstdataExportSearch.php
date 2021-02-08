<?php

namespace app\modules\firstdata\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\firstdata\models\FirstdataExport;

/**
 * FirstdataExportSearch represents the model behind the search form of `app\modules\firstdata\models\FirstdataExport`.
 */
class FirstdataExportSearch extends FirstdataExport
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['firstdata_export_id', 'created_at', 'firstdata_config_id'], 'integer'],
            [['file_url'], 'safe'],
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
        $query = FirstdataExport::find();

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
            'firstdata_export_id' => $this->firstdata_export_id,
            'created_at' => $this->created_at,
            'firstdata_config_id' => $this->firstdata_config_id,
        ]);

        $query->andFilterWhere(['like', 'file_url', $this->file_url]);

        return $dataProvider;
    }
}
