<?php

use yii\db\Migration;

class m180627_150844_ecopago_add_justification_table extends Migration
{

    public function init()
    {
        $this->db = 'dbecopago';
        parent::init();
    }

    public function up()
    {
        $this->createTable('justification', [
            'justification_id' => $this->primaryKey(),
            'payout_id' => $this->integer(),
            'cause' => $this->string(),
            'type' => "ENUM('reprint', 'cancellation')"
        ]);
        
        $this->addForeignKey('fk_payout_id', 'justification', 'payout_id', 'payout', 'payout_id');
    }

    public function down()
    {
        $this->dropForeignKey('fk_payout_id', 'justification');
        $this->dropTable('justification');
    }

}
