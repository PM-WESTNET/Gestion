<?php

use yii\db\Migration;

class m180830_113513_add_point_of_sale_field_into_bill extends Migration
{
    public function up()
    {
        $this->addColumn('bill', 'point_of_sale_id', $this->integer());

        $command = (new \yii\db\Query())
            ->select(['company_id', 'point_of_sale_id'])
            ->from('point_of_sale')
            ->where(['point_of_sale.default' => '1'])
            ->createCommand();
        $points_of_sales = $command->queryAll();

        foreach ($points_of_sales as $point_of_sale) {
            $this->db->createCommand("UPDATE bill SET point_of_sale_id = ".$point_of_sale['point_of_sale_id']." WHERE company_id = ".$point_of_sale['company_id'])->execute();
        }
    }

    public function down()
    {
        $this->dropColumn('bill', 'point_of_sale');
    }

}
