<?php

use yii\db\Migration;

class m160824_181739_add_destinatary_filtered_by_contract_age extends Migration
{
        public function init() {
        $this->db = 'dbnotifications';
        parent::init();
    }
    
    public function up()
    {
        $this->addColumn('{{destinatary}}', 'contract_min_age', $this->integer());
        $this->addColumn('{{destinatary}}', 'contract_max_age', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('{{destinatary}}', 'contract_min_age');
        $this->dropColumn('{{destinatary}}', 'contract_max_age');
    }

}
