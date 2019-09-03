<?php

namespace app\modules\mobileapp\v1\models\search;

use app\modules\config\models\Config;
use app\modules\mobileapp\v1\models\Customer;
use app\modules\westnet\models\Connection;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\mobileapp\v1\models\UserAppActivity;
use yii\db\Expression;
use yii\db\Query;

/**
 * UserAppActivitySearch represents the model behind the search form of `app\modules\mobileapp\v1\models\UserAppActivity`.
 */
class UserAppActivitySearch extends UserAppActivity
{

    public $last_activity_from;
    public $last_activity_to;
    public $company_id;

    public function rules()
    {
        return [
            [['user_app_activity_id', 'user_app_id', 'installation_datetime', 'company_id'], 'integer'],
            [['last_activity_from', 'last_activity_to'], 'safe'],
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

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'last_activity_from' => Yii::t('app', 'Last activity from'),
            'last_activity_to' => Yii::t('app', 'Last activity to'),
        ]);
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
        $query = UserAppActivity::find();

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
            'user_app_activity_id' => $this->user_app_activity_id,
            'user_app_id' => $this->user_app_id,
        ]);

        return $dataProvider;
    }

    public function searchStatistics($params)
    {
        $this->load($params);

        $uninstalled_period = Config::getValue('month-qty-to-declare-app-uninstalled');
        $date_min_last_activity = (new \DateTime('now'))->modify("-$uninstalled_period month")->getTimestamp();

        $queryCustomer = new Query();
        $queryCustomer
            ->select([new Expression('count(*) AS customer_qty'), new Expression('0 as installed_qty'), new Expression('0 as used_qty')])
            ->from('customer c')
            ->leftJoin('contract con', 'con.customer_id = c.customer_id')
            ->leftJoin('connection conn', 'conn.contract_id = con.contract_id')
            ->where(['conn.status' => Connection::STATUS_ACCOUNT_ENABLED])
            ->all();

        $queryInstalled = new Query();
        $queryInstalled
            ->select([new Expression('0 AS customer_qty'), new Expression('count(*) AS installed_qty'), new Expression('0 AS used_qty')])
            ->from('user_app_activity uaa1')
            ->leftJoin('user_app_has_customer uahc1', 'uahc1.user_app_id = uaa1.user_app_id')
            ->leftJoin('customer cus1', 'uahc1.customer_id = cus1.customer_id')
            ->where(['not',['uahc1.customer_id' => null]])
            ->andFilterWhere(['>=','uaa1.last_activity_datetime', $date_min_last_activity])
            ->all();

        $queryUsed = new Query();
        $queryUsed
            ->select([new Expression('0 AS customer_qty'), new Expression('0 AS installed_qty'), new Expression('count(*) AS used_qty')])
            ->from('user_app_activity uaa2')
            ->leftJoin('user_app_has_customer uahc2', 'uahc2.user_app_id = uaa2.user_app_id')
            ->leftJoin('customer cus2', 'uahc2.customer_id = cus2.customer_id')
            ->where(['not',['uahc2.customer_id' => null]])
            ->all();

        if($this->company_id) {
            $queryCustomer->andFilterWhere(['company_id' => $this->company_id]);
            $queryInstalled->andFilterWhere(['cus1.company_id' => $this->company_id]);
            $queryUsed->andFilterWhere(['cus2.company_id' => $this->company_id]);
        }

        if($this->last_activity_from) {
            $queryUsed->andFilterWhere(['>=','uaa2.last_activity_datetime', (new \DateTime($this->last_activity_from))->getTimestamp()]);
        }

        if($this->last_activity_to) {
            $queryUsed->andFilterWhere(['<=','uaa2.last_activity_datetime', (new \DateTime($this->last_activity_to))->getTimestamp()]);
        }

        $queryCustomer->union($queryInstalled, true);
        $queryCustomer->union($queryUsed, true);

        $query = new Query();
        $query
            ->select([new Expression('sum(customer_qty) AS customer_qty'), new Expression('sum(installed_qty) AS installed_qty'), new Expression('sum(used_qty) AS used_qty')])
            ->from(['t' => $queryCustomer]);

        return $query->all();
    }
}
