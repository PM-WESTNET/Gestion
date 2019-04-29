<?php

use yii\db\Migration;

class m181029_162009_update_transport_status extends Migration
{
    public function init(){
        $this->db = 'dbnotifications';
        parent::init();
    }

    public function safeUp()
    {
        $this->update('transport', [
            'status' => 'enabled'
        ]);
    }
    public function safeDown()
    {
        $this->update('transport',[
            'status' => 'disabled'
        ]);
    }
}
