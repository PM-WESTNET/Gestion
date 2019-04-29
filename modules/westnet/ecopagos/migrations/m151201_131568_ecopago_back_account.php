<?php

use yii\db\Schema;
use yii\db\Migration;

class m151201_131568_ecopago_back_account extends Migration {
    public function up() {

        //Money box type inserts
        if ($this->db->getTableSchema('money_box_type') !== null) {
            $this->insert('money_box_type', [
                'name' => 'Cajas ecopago',
                'code' => 'cajas_ecopago',
            ]);
        }
    }

    public function down() {
        
    }

}
