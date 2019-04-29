<?php

use yii\db\Migration;
use app\modules\sale\models\BillType;

class m180928_170305_add_applies_to_fields_into_bill_type_table extends Migration
{

    public function safeUp()
    {
        $this->addColumn('bill_type','applies_to_buy_book', $this->boolean());
        $this->addColumn('bill_type','applies_to_sale_book', $this->boolean());

        $bill_types_to_buy_book = BillType::find()->where(['code' => [1,11,3,2]])->all();
        $bill_types_to_sale_book = BillType::find()->where(['code' => [1,6,11,3,2,7,8]])->all();
        $bill_types_doesnt_apply = BillType::find()->where(['code' => [10,20,14,9,16]])->all();

        foreach ($bill_types_to_buy_book as $bill_type){
            $this->update('bill_type', [
                'applies_to_buy_book' => true,
            ],[
                'bill_type_id' => $bill_type->bill_type_id
            ]);
        }

        foreach ($bill_types_to_sale_book as $bill_type){
            $this->update('bill_type', [
                'applies_to_sale_book' => true
            ],[
                'bill_type_id' => $bill_type->bill_type_id
            ]);
        }

        foreach ($bill_types_doesnt_apply as $bill_type){
            $this->update('bill_type', [
                'applies_to_sale_book' => false,
                'applies_to_buy_book' => false,
            ],[
                'bill_type_id' => $bill_type->bill_type_id
            ]);
        }
    }

    public function safeDown()
    {
        $this->dropColumn('bill_type', 'applies_to_buy_book');
        $this->dropColumn('bill_type', 'applies_to_sale_book');
    }
}
