<?php

use yii\db\Schema;
use yii\db\Migration;

class m160202_210510_sequre extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function up()
    {
        $this->insert('category', [
            'name' => 'Sequre',
            'status' => 'enabled'
        ]);

        $categoryId = $this->db->getLastInsertID();


        $this->insert('item', [
            'attr' => 'default_ceil_dfl_percent',
            'type' => 'textInput',
            'label' => 'Porcentaje de tráfico P2P',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => 30
        ]);
    }

    public function down()
    {
        $this->delete('item', ['attr' => 'default_ceil_dfl_percent']);
        $this->delete('category', ['name' => 'Sequre']);

        return false;
    }
}
