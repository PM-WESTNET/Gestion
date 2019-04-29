<?php

namespace app\modules\sale\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\sale\models\Company;

/**
 * CompanySearch represents the model behind the search form about `app\modules\sale\models\Company`.
 */
class CompanySearch extends Company
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['company_id', 'parent_id', 'create_timestamp'], 'integer'],
            [['status'], 'in', 'range' => ['enabled', 'disabled']],
            [['name', 'tax_identification', 'address', 'phone', 'email', 'certificate'], 'safe'],
            [['default'], 'boolean'],
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
        $query = Company::find();

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
            'company_id' => $this->company_id,
            'parent_id' => $this->parent_id,
            'status' => $this->status,
            'default' => $this->default,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'tax_identification', $this->tax_identification])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }

    public function findParentCompanies()
    {
        return Company::find()->where('parent_id is null')->all();
    }
}
