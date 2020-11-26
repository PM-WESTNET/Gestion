<?php

namespace app\modules\firstdata\models\search;

use app\modules\firstdata\models\FirstdataImportPayment;
use yii\data\ActiveDataProvider;

class FirstdataImportPaymentSearch extends FirstdataImportPayment 
{

    public function rules(){

        return [
            [['customer_code', 'customer_id', 'status'], 'safe']
        ];
    }


    public function search($params) {

        $this->load($params);

        $query = FirstdataImportPayment::find()
            ->andWhere(['firstdata_import_id' => $this->firstdata_import_id])
            ->andFilterWhere([
                'customer_code' => $this->customer_code,
                'customer_id' => $this->customer_id,
                'status' => $this->status
        ]);

        $dataProvider = new ActiveDataProvider(['query' => $query]);

        return $dataProvider;
    }

}