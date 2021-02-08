<?php

use yii\db\Migration;

class m180808_093856_add_external_tag_id_into_ticket extends Migration
{
    public function init()
    {
        $this->db = 'dbticket';
        parent::init();
    }
    
    public function up()
    {
        $this->addColumn('ticket', 'external_tag_id', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('ticket', 'external_tag_id');
    }

}
