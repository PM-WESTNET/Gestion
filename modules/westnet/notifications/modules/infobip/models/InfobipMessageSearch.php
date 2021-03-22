<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 01/07/19
 * Time: 10:32
 */

namespace app\modules\westnet\notifications\modules\infobip\models;


use app\modules\westnet\notifications\models\InfobipMessage;
use yii\data\ActiveDataProvider;

class InfobipMessageSearch extends InfobipMessage
{

    public function search($params) {
        $query = InfobipMessage::find();

        $this->load($params);

        $query->andFilterWhere(['bulkId' => $this->bulkId])
            ->andFilterWhere(['messageId' => $this->messageId])
            ->andFilterWhere(['to' => $this->to])
            ->andFilterWhere(['customer_id' => $this->customer_id])
            ->andFilterWhere(['status' => $this->status]);

        if ($this->sent_timestamp){
            $query->andFilterWhere(['>=','sent_timestamp',strtotime($this->sent_timestamp)]);
            $query->andFilterWhere(['<', 'sent_timestamp', (strtotime($this->sent_timestamp) + 86400)]);
        }

        $query->andFilterWhere(['like', 'message', $this->message]);

        $query->orderBy(['sent_timestamp' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider(['query' => $query]);

        return  $dataProvider;
    }
}