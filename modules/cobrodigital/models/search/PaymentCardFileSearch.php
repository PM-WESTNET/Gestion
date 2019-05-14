<?php

namespace app\modules\cobrodigital\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\cobrodigital\models\PaymentCardFile;

/**
 * PaymentCardFileSearch represents the model behind the search form of `app\modules\cobrodigital\models\PaymentCardFile`.
 */
class PaymentCardFileSearch extends PaymentCardFile
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payment_card_file_id'], 'integer'],
            [['upload_date', 'file_name', 'path'], 'safe'],
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
        $query = PaymentCardFile::find();

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
            'payment_card_file_id' => $this->payment_card_file_id,
        ]);

        $query->andFilterWhere(['like', 'upload_date', $this->upload_date])
            ->andFilterWhere(['like', 'file_name', $this->file_name])
            ->andFilterWhere(['like', 'path', $this->path]);

        return $dataProvider;
    }
}
