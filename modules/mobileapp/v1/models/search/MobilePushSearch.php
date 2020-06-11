<?php

namespace app\modules\mobileapp\v1\models\search;

use app\modules\config\models\Config;
use app\modules\mobileapp\v1\models\Customer;
use app\modules\mobileapp\v1\models\MobilePush;
use app\modules\sale\modules\contract\models\Contract;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\mobileapp\v1\models\UserAppActivity;
use yii\db\Expression;
use yii\db\Query;

/**
 * UserAppActivitySearch represents the model behind the search form of `app\modules\mobileapp\v1\models\UserAppActivity`.
 */
class MobilePushSearch extends MobilePush
{
    public $customer_id;
    public $customer_code;

    public function rules()
    {
        return [
            [['customer_id', 'customer_code'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'customer_id' => Yii::t('app', 'Customer'),
            'customer_code' => Yii::t('app', 'Customer code'),
        ]);
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchMobilePushHasUserApp($params)
    {
        $this->load($params);

        $query = (new Query())
            ->select(['c.code as customer_code', 'c.customer_id',
                (new Expression("CONCAT(c.lastname, ', ', c.name) as customer_name" )),
                (new Expression("IF (mphua.sent_at IS NOT NULL, 'sent','no sent')as status")),
                'mphua.notification_read',
                'mphua.mobile_push_has_user_app_id'
                ])
            ->from('mobile_push_has_user_app mphua')
            ->leftJoin('mobile_push mp', 'mp.mobile_push_id = mphua.mobile_push_id')
            ->leftJoin('customer c', 'c.customer_id = mphua.customer_id')
            ;

        if($this->mobile_push_id){
            $query->andFilterWhere(['mphua.mobile_push_id' => $this->mobile_push_id]);
        }

        if($this->customer_id){
            $query->andFilterWhere(['c.customer_id' => $this->customer_id]);
        }

        if($this->customer_code){
            $query->andFilterWhere(['c.code' => $this->customer_code]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        return $dataProvider;
    }
}
