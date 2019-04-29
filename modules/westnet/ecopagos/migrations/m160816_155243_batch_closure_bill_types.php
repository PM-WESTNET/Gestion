<?php

use app\modules\config\models\Category;
use yii\db\Migration;

class m160816_155243_batch_closure_bill_types extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function up()
    {
        $category = Category::findOne(['name' => 'Ecopago']);

        $this->insert('item', [
            'attr' => 'ecopago_batch_closure_bill_type_id',
            'type' => 'textInput',
            'label' => 'Tipo de comprobante de Factura Ecopago.',
            'description' => 'Tipo de comprobante con el que facturan los ecopagos.',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 1,
            'default' => 1
        ]);
        $this->insert('item', [
            'attr' => 'ecopago_batch_closure_debit_type_id',
            'type' => 'textInput',
            'label' => 'Tipo de comprobante de Nota de debito Ecopago.',
            'description' => 'Tipo de comprobante con el que se hacen las notas de debito de ecopagos.',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 1,
            'default' => 1
        ]);

        $this->insert('item', [
            'attr' => 'ecopago_batch_closure_credit_type_id',
            'type' => 'textInput',
            'label' => 'Tipo de comprobante de Nota de Credito Ecopago.',
            'description' => 'Tipo de comprobante con el que se hacen las notas de credito de ecopagos.',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 1,
            'default' => 1
        ]);


    }
    public function down()
    {
        echo "m160816_155243_batch_closure_bill_types cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
