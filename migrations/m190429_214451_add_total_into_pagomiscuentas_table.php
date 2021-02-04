<?php

use yii\db\Migration;
use app\modules\pagomiscuentas\models\PagomiscuentasFile;
use yii\db\Query;
use yii\db\Expression;

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

                $total = (new Query())->select(new Expression('SUM(b.total) as total'))
                    ->from('bill b')
                    ->leftJoin('pagomiscuentas_file_has_bill phb', 'phb.bill_id = b.bill_id')
                    ->where(['not',['phb.bill_id' => null]])
                    ->andWhere(['phb.pagomiscuentas_file_id' => $pago_mis_cuentas_file->pagomiscuentas_file_id])
                    ->all();

                $total = $total[0]['total'];
            }

            if($pago_mis_cuentas_file->type == PagomiscuentasFile::TYPE_PAYMENT) {

                $total = (new Query())->select(new Expression('SUM(p.amount) as total'))
                    ->from('payment p')
                    ->leftJoin('pagomiscuentas_file_has_payment php', 'php.payment_id = p.payment_id')
                    ->where(['not',['php.payment_id' => null]])
                    ->andWhere(['php.pagomiscuentas_file_id' => $pago_mis_cuentas_file->pagomiscuentas_file_id])
                    ->all();

                $total = $total[0]['total'];
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
