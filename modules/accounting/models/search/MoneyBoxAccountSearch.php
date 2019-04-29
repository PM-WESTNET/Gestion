<?php

namespace app\modules\accounting\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\accounting\models\MoneyBoxAccount;

/**
 * CompanySearch represents the model behind the search form about `app\modules\sale\models\Company`.
 */
class MoneyBoxAccountSearch extends MoneyBoxAccount
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['money_box_account_id', 'money_box_id', 'company_id', 'account_id'], 'integer'],
            [['number'], 'string'],
            [['number', 'money_box_id', 'company_id', 'account_id'], 'safe'],
            [['enable'], 'boolean'],
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
        $query = MoneyBoxAccount::find();

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
            'money_box_account_id' => $this->money_box_account_id,
            'money_box_id' => $this->money_box_id,
            'company_id' => $this->company_id,
            'account_id' => $this->account_id,
            'enable' => $this->enable,
        ]);

        $query->andFilterWhere(['like', 'number', $this->number]);

        return $dataProvider;
    }

}
