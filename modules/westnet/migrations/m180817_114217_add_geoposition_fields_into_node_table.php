<?php

use yii\db\Migration;

class m180817_114217_add_geoposition_fields_into_node_table extends Migration
{
    
    public function safeUp()
    {
        $this->addColumn('node', 'geocode', $this->text(100));
    }

    public function safeDown()
    {
        $this->dropColumn('node', 'geocode');
    }

}
