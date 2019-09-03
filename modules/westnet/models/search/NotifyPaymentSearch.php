<?php

namespace app\modules\westnet\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\models\NotifyPayment;
use yii\db\Query;

/**
 * NotifyPaymentSearch represents the model behind the search form of `app\modules\westnet\models\NotifyPayment`.
 */
class NotifyPaymentSearch extends NotifyPayment
{
    public $from_date;
    public $to_date;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['notify_payment_id', 'payment_method_id', 'created_at'], 'integer'],
            [['date', 'image_receipt', 'from_date', 'to_date', 'customer_id'], 'safe'],
            [['amount'], 'number'],
        ];
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'to_date' => Yii::t('app', 'To Date'),
            'from_date' => Yii::t('app', 'From Date'),
        ]);
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
        $query = NotifyPayment::find();

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
            'notify_payment_id' => $this->notify_payment_id,
            'date' => $this->date,
            'amount' => $this->amount,
            'payment_method_id' => $this->payment_method_id,
            'created_at' => $this->created_at,
            'customer_id' => $this->customer_id,
        ]);

        if($this->from_date) {
            $query->andFilterWhere(['>=', 'date', $this->from_date]);
        }

        if($this->to_date) {
            $query->andFilterWhere(['<=', 'date', $this->to_date]);
        }

        $query->andFilterWhere(['like', 'image_receipt', $this->image_receipt]);

        return $dataProvider;
    }

    public function report()
    {
        $query = (new Query())->select('COUNT(*) as qty, pm.name as payment_method_name')
                ->from('notify_payment np')
                ->leftJoin('payment_method pm', 'pm.payment_method_id = np.payment_method_id')
                ->where(['from' => NotifyPayment::FROM_APP])
                ->groupBy(['pm.name'])
                ->all();

        return $query;
    }
}
