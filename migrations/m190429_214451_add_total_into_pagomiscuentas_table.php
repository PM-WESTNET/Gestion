<?php

use app\modules\pagomiscuentas\models\search\PagomiscuentasFileSearch;
use yii\db\Migration;
use app\modules\pagomiscuentas\models\PagomiscuentasFile;

/**
 * Class m190429_214451_add_total_into_pagomiscuentas_table
 */
class m190429_214451_add_total_into_pagomiscuentas_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('pagomiscuentas_file', 'total', $this->float());

        $pago_mis_cuentas_files = PagomiscuentasFile::find()->where(['type' => 'bill'])->all();
        echo count($pago_mis_cuentas_files);

        foreach ($pago_mis_cuentas_files as $pago_mis_cuentas_file) {
            $total = 0;

            if($pago_mis_cuentas_file->type == PagomiscuentasFile::TYPE_BILL) {

                foreach ($pago_mis_cuentas_file->bills as $bill) {
                    $total = $total + $bill['total'];
                }
            }

            if($pago_mis_cuentas_file->type == PagomiscuentasFile::TYPE_PAYMENT) {

                foreach ($pago_mis_cuentas_file->payments as $payment) {
                    $total = $total + $payment->amount;
                }
            }
            $pago_mis_cuentas_file->updateAttributes(['total' => $total]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('pagomiscuentas_file', 'total');
    }
}
