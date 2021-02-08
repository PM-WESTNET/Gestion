<?php

use yii\db\Schema;
use yii\db\Migration;

class m160511_190825_partner_config extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function up()
    {
        $this->insert('category', [
            'name' => 'Socio',
            'status' => 'enabled'
        ]);

        $categoryId = $this->db->getLastInsertID();


        $this->insert('item', [
            'attr' => 'partner_payment_account',
            'type' => 'textInput',
            'label' => 'Cuenta de Cobro',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
        ]);

        $this->insert('item', [
            'attr' => 'partner_provider_payment_account',
            'type' => 'textInput',
            'label' => 'Cuenta de Pago',
            'description' => '',
            'multiple' => 0,
            'category_id' => $categoryId,
            'superadmin' => 0,
        ]);
    }

    public function down()
    {
        $this->delete('item', ['attr' => 'partner_payment_account']);
        $this->delete('item', ['attr' => 'partner_provider_payment_account']);
        $this->delete('category', ['name' => 'Socio']);

        return false;
    }
}