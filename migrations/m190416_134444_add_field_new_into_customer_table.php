<?php

use yii\db\Migration;
use app\modules\config\models\Category;
use app\modules\config\models\Item;
use app\modules\config\models\Config;

class m190416_134444_add_field_new_into_customer_table extends Migration
{

    public function safeUp()
    {
        $this->addColumn('customer', 'date_new', $this->date()->defaultValue(null));
    }

    public function safeDown()
    {
        $this->dropColumn('customer', 'date_new');
    }
}
