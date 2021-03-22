<?php

use yii\db\Migration;

class m180628_174551_ecopago_add_date_into_justification_table extends Migration
{
    public function init()
    {
        $this->db = 'dbecopago';
        parent::init();
    }
    
    public function up()
    {
        $this->addColumn('justification', 'date', $this->timestamp()->defaultValue(null));
    }

    public function down()
    {
        $this->dropColumn('justification', 'date');
    }

}
