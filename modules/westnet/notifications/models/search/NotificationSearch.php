<?php

namespace app\modules\westnet\notifications\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\westnet\notifications\models\Notification;

/**
 * BatchClosureSearch represents the model behind the search form about `app\modules\westnet\ecopagos\models\Payout`.
 */
class NotificationSearch extends Notification {

    public $search_text;
    public $enabled_transports_only;

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
            [['notification_id', 'status', 'transport_id', 'name', 'subject', 'from_date', 'to_date'], 'safe'],
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

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'notification.notification_id' => $this->notification_id,
            'notification.status' => $this->status,
            'notification.transport_id' => $this->transport_id,
            'notification.from_date' => $this->from_date,
            'notification.to_date' => $this->to_date,
        ]);
        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'subject', $this->subject]);

        $dataProvider->query = $query;

        return $dataProvider;
    }

}
