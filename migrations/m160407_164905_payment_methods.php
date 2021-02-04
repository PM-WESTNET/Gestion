<?php

use yii\db\Schema;
use yii\db\Migration;

class m160407_164905_payment_methods extends Migration
{
    public function up()
    {
        $this->createPaymentMethods();
    }

    public function down()
    {

        $this->deletePaymentMethods();

        return false;
    }

    private function deletePaymentMethods()
    {
        $this->delete('payment_method', ['name'=>[
            'Contado',
            'Cheque',
            'Deposito - Transferencia',
            'Tarjeta de Credito',
            'Tarjeta de Debito'
        ]]);
    }

    private function createPaymentMethods()
    {

        $this->insert('payment_method', [
            'name' => 'Contado',
            'status' => 'enabled',
            'register_number' => 0,
            'type' => 'exchanging'
        ]);

        $this->insert('payment_method', [
            'name' => 'Cheque',
            'status' => 'enabled',
            'register_number' => 0,
            'type' => 'provisioning'
        ]);

        $this->insert('payment_method', [
            'name' => 'Deposito - Transferencia',
            'status' => 'enabled',
            'register_number' => 0,
            'type' => 'exchanging'
        ]);

        $this->insert('payment_method', [
            'name' => 'Tarjeta de Credito',
            'status' => 'enabled',
            'register_number' => 1,
            'type' => 'provisioning'
        ]);

        $this->insert('payment_method', [
            'name' => 'Tarjeta de Debito',
            'status' => 'enabled',
            'register_number' => 1,
            'type' => 'provisioning'
        ]);
    }

}
