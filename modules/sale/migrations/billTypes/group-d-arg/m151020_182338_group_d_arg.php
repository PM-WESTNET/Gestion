<?php

use yii\db\Schema;
use yii\db\Migration;

class m151020_182338_group_d_arg extends Migration
{
    public function up()
    {
        
        $this->insert('invoice_class', [
            'name' => 'MTXCA',
            'class' => 'app\modules\invoice\components\einvoice\afip\fev1\Mtxca',
        ]);
        
        $this->insert('invoice_class', [
            'name' => 'FEv1',
            'class' => 'app\modules\invoice\components\einvoice\afip\fev1\Fev1',
        ]);
        
        $invoiceClassId = $this->db->getLastInsertID();
        
        $this->insert('bill_type', [
            'name' => 'Factura C',
            'code' => 11,
            'type' => '',
            'view' => 'final',
            'multiplier' => 1,
            'customer_required' => 1,
            'invoice_class_id' => $invoiceClassId,
            'class' => 'app\modules\sale\models\bills\Bill',
            'startable' => 1
        ]);
        
        $this->insert('bill_type', [
            'name' => 'Nota Crédito C',
            'code' => 13,
            'type' => '',
            'view' => 'final',
            'multiplier' => -1,
            'customer_required' => 1,
            'invoice_class_id' => $invoiceClassId,
            'class' => 'app\modules\sale\models\bills\Credit',
            'startable' => 1
        ]);
        
        $this->insert('bill_type', [
            'name' => 'Nota Débito C',
            'code' => 12,
            'type' => '',
            'view' => 'final',
            'multiplier' => 1,
            'customer_required' => 1,
            'invoice_class_id' => $invoiceClassId,
            'class' => 'app\modules\sale\models\bills\Debit',
            'startable' => 1
        ]);
        
    }

    public function down()
    {
        echo "m151020_182338_group_d_arg cannot be reverted.\n";

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
