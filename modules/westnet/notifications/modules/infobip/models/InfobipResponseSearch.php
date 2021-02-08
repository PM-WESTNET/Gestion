<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 27/05/19
 * Time: 14:46
 */

namespace app\modules\westnet\notifications\modules\infobip\models;


use app\modules\westnet\notifications\models\InfobipResponse;
use yii\data\ActiveDataProvider;

class InfobipResponseSearch extends InfobipResponse
{

    public function search($params) {
        $query = InfobipResponse::find();

        $this->load($params);

        $query->andFilterWhere(['like', 'content', $this->content]);
        $query->andFilterWhere(['keyword' =>  $this->keyword]);
        $query->andFilterWhere(['>=', 'received_timestamp', $this->received_timestamp]);
        $query->andFilterWhere(['<', 'received_timestamp', ($this->received_timestamp+ 86400)]);

        $query->orderBy(['infobip_response_id' =>  SORT_DESC]);

        $dataProvider = new ActiveDataProvider(['query' => $query]);

        return $dataProvider;
    }
}