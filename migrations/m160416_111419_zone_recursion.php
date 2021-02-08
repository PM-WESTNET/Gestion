<?php

use yii\db\Migration;

class m160416_111419_zone_recursion extends Migration
{
    public function up()
    {
        $this->addColumn('zone', 'lft', 'int');
        $this->addColumn('zone', 'rgt', 'int');
    }

    public function down()
    {
        $this->dropColumn('zone', 'lft');
        $this->dropColumn('zone', 'rgt');

        return true;
    }
}
