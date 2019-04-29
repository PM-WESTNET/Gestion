<?php

use yii\db\Schema;
use yii\db\Migration;

class m160229_145922_account_config extends Migration
{
    public function up()
    {
        $this->insert('account_config', [
            'name'          => 'Facturar',
            'class'         => 'app\modules\sale\models\Bill',
            'classMovement' => 'app\modules\accounting\components\impl\BillMovement'
        ]);

        $this->insert('account_config', [
            'name'          => 'Cobro',
            'class'         => 'app\modules\checkout\models\Payment',
            'classMovement' => 'app\modules\accounting\components\impl\PaymentMovement'
        ]);

        $this->insert('account_config', [
            'name'          => 'Factura Proveedores',
            'class'         => 'app\modules\provider\models\ProviderBill',
            'classMovement' => 'app\modules\accounting\components\impl\ProviderBillMovement'
        ]);

        $this->insert('account_config', [
            'name'          => 'Pago a Proveedores',
            'class'         => 'app\modules\provider\models\ProviderPayment',
            'classMovement' => 'app\modules\accounting\components\impl\ProviderPaymentMovement'
        ]);


    }

    public function down()
    {
        echo "m160229_145922_account_config cannot be reverted.\n";

        return false;
    }
}
