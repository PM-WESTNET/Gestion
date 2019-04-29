<?php

use yii\db\Migration;
use yii\db\Query;

class m160609_180257_ecopago_payout_limit extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function up()
    {
        $this->createConfig();
    }

    public function down()
    {
        $this->deleteConfig();

        return false;
    }

    private function createConfig()
    {
        $categoryId = $this->db
            ->createCommand("SELECT category_id FROM category WHERE name='Ecopago'")
            ->queryScalar();

        $this->insert('item', [
            'attr' => 'ecopago_payout_limit',
            'type' => 'textInput',
            'label' => 'Limite de pago.',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
            'default' => 0
        ]);

    }

    private function deleteConfig()
    {
        $this->delete('item', ['attr' => 'ecopago_payout_limit']);
    }
}
