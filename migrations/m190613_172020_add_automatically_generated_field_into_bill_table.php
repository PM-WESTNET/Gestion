<?php

use app\modules\config\models\Category;
use app\modules\config\models\Config;
use app\modules\config\models\Item;
use yii\db\Migration;

class m190613_172020_add_automatically_generated_field_into_bill_table extends Migration
{

    public function safeUp()
    {
        $this->addColumn('bill','automatically_generated', $this->boolean());
    }


    public function safeDown()
    {
        $this->dropColumn('bill', 'automatically_generated');
    }
}