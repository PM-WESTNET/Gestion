<?php

use yii\db\Schema;
use yii\db\Migration;

class m151020_182338_group_c_arg extends Migration
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
            'name' => 'Factura A',
            'code' => 1,
            'type' => '',
            'view' => 'default',
            'multiplier' => 1,
            'customer_required' => 1,
            'invoice_class_id' => $invoiceClassId,
            'class' => 'app\modules\sale\models\bills\Bill',
            'startable' => 1
        ]);
        
        $this->insert('bill_type', [
            'name' => 'Factura B',
            'code' => 6,
            'type' => '',
            'view' => 'final',
            'multiplier' => 1,
            'customer_required' => 1,
            'invoice_class_id' => $invoiceClassId,
            'class' => 'app\modules\sale\models\bills\Bill',
            'startable' => 1
        ]);
                
        $this->insert('bill_type', [
            'name' => 'Nota Crédito A',
            'code' => 3,
            'type' => '',
            'view' => 'default',
            'multiplier' => -1,
            'customer_required' => 1,
            'invoice_class_id' => $invoiceClassId,
            'class' => 'app\modules\sale\models\bills\Credit',
            'startable' => 1
        ]);
        
        $this->insert('bill_type', [
            'name' => 'Nota Crédito B',
            'code' => 8,
            'type' => '',
            'view' => 'final',
            'multiplier' => -1,
            'customer_required' => 1,
            'invoice_class_id' => $invoiceClassId,
            'class' => 'app\modules\sale\models\bills\Credit',
            'startable' => 1
        ]);
                
        $this->insert('bill_type', [
            'name' => 'Nota Débito A',
            'code' => 2,
            'type' => '',
            'view' => 'default',
            'multiplier' => 1,
            'customer_required' => 1,
            'invoice_class_id' => $invoiceClassId,
            'class' => 'app\modules\sale\models\bills\Debit',
            'startable' => 1
        ]);
        
        $this->insert('bill_type', [
            'name' => 'Nota Débito B',
            'code' => 7,
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
        echo "m151020_182338_group_c_arg cannot be reverted.\n";

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
