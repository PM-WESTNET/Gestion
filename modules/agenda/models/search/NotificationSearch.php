<?php

namespace app\modules\agenda\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\agenda\models\Notification;

/**
 * NotificationSearch represents the model behind the search form about `app\modules\agenda\models\Notification`.
 */
class NotificationSearch extends Notification {


    public function init() {
        parent::init();
    }

    public function rules() {
        return [
            [['task_id', 'user_id'], 'integer'],
            [['reason', 'datetime', 'status'], 'safe'],
            [['is_expired_reminder', 'show'], 'boolean'],
        ];
    }
    
    public function search($params) {
        $query = Notification::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'status' => SORT_ASC,
                    'datetime' => SORT_ASC
                ]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'user_id' => $this->user_id,
            'status' => $this->status,
            'is_expired_reminder' => $this->is_expired_reminder,
            'show' => 1
        ]);

        $query->andFilterWhere(['like', 'reason', $this->reason]);

        return $dataProvider;
    }

}
