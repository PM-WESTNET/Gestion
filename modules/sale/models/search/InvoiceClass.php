<?php

namespace app\modules\sale\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sale\models\InvoiceClass as InvoiceClassModel;

/**
 * InvoiceClass represents the model behind the search form about `app\modules\sale\models\InvoiceClass`.
 */
class InvoiceClass extends InvoiceClassModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoice_class_id'], 'integer'],
            [['class', 'name'], 'safe'],
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
        $query = InvoiceClassModel::find();

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
            'invoice_class_id' => $this->invoice_class_id,
        ]);

        $query->andFilterWhere(['like', 'class', $this->class])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
