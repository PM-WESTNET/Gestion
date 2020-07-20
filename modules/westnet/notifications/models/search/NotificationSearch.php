<?php

namespace app\modules\westnet\notifications\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\notifications\models\Notification;
use yii\db\Expression;

/**
 * BatchClosureSearch represents the model behind the search form about `app\modules\westnet\ecopagos\models\Payout`.
 */
class NotificationSearch extends Notification {

    public $search_text;
    public $enabled_transports_only;
    public $programmed = false;
    public $create_timestamp_from;
    public $create_timestamp_to;

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['notification_id', 'status', 'transport_id', 'name', 'subject', 'from_date', 'to_date', 'create_timestamp_from', 'create_timestamp_to'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Uses request for model filling and search porpouses
     * @param type $params
     * @return ActiveDataProvider
     */
    public function search($params) {

        $query = Notification::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (empty($params['sort'])) {
            $query->orderBy([
                'from_date' => SORT_DESC,
            ]);
        }
        if($this->enabled_transports_only){

        $query->leftJoin('transport', 'transport.transport_id = notification.transport_id')
            ->andWhere(['transport.status' => 'enabled']);
        }

        if ($this->programmed) {
            $query->andWhere(['IS NOT', 'scheduler', null]);
        }else {
            $query->andWhere(['OR',['IS', 'scheduler', null], ['scheduler' => '']]);
        }

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if($this->create_timestamp_from){
            $date = (new \DateTime($this->create_timestamp_from . ' 00:00:00'))->getTimestamp();
            $query->andFilterWhere(['>=','create_timestamp' , $date]);
        }

        if($this->create_timestamp_to){
            $date = (new \DateTime($this->create_timestamp_to .' 23:59:59'))->getTimestamp();
            $query->andFilterWhere(['<=','create_timestamp' , $date]);
        }

        $query->andFilterWhere([
            'notification.notification_id' => $this->notification_id,
            'notification.status' => $this->status,
            'notification.transport_id' => $this->transport_id,
            'notification.from_date' => $this->from_date,
            'notification.to_date' => $this->to_date,
        ]);
        $query->andFilterWhere(['like', 'notification.name', $this->name]);
        $query->andFilterWhere(['like', 'notification.subject', $this->subject]);

        $dataProvider->query = $query;

        return $dataProvider;
    }

}
