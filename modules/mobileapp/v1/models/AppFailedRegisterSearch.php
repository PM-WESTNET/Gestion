<?php

namespace app\modules\mobileapp\v1\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\mobileapp\v1\models\AppFailedRegister;

/**
 * AppFailedRegisterSearch represents the model behind the search form about `app\modules\mobileapp\v1\models\AppFailedRegister`.
 */
class AppFailedRegisterSearch extends AppFailedRegister
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_failed_register_id'], 'integer'],
            [['name', 'document_type', 'document_number', 'email', 'phone', 'status', 'type', 'text', 'customer_code'], 'safe'],
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
        $query = AppFailedRegister::find();

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
            'app_failed_register_id' => $this->app_failed_register_id,
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'document_type', $this->document_type])
            ->andFilterWhere(['like', 'document_number', $this->document_number])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'text', $this->text])
            ->andFilterWhere(['like', 'customer_code', $this->customer_code])
            ->andFilterWhere(['like', 'status', 'pending']);

        $query->orderBy(['app_failed_register_id' => SORT_DESC]);

        return $dataProvider;
    }
}
