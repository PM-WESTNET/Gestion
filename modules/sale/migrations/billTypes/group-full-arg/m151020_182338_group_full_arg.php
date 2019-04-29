<?php

use yii\db\Schema;
use yii\db\Migration;

class m151020_182338_group_full_arg extends Migration
{
    public function up()
    {
        
        $bills = [];
        
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
        
        $bills[] = $this->db->getLastInsertID();
        
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
        
        $bills[] = $this->db->getLastInsertID();
        
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
        
        $bills[] = $this->db->getLastInsertID();
        
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
        
        $this->insert('bill_type', [
            'name' => 'Pedido',
            'code' => '',
            'type' => '',
            'view' => 'default',
            'multiplier' => 0,
            'customer_required' => 1,
            'invoice_class_id' => '',
            'class' => 'app\modules\sale\models\bills\Order',
            'startable' => 1
        ]);
        
        $order = $this->db->getLastInsertID();
        
        $this->insert('bill_type', [
            'name' => 'Presupuesto',
            'code' => '',
            'type' => '',
            'view' => 'default',
            'multiplier' => 0,
            'customer_required' => 1,
            'invoice_class_id' => '',
            'class' => 'app\modules\sale\models\bills\Budget',
            'startable' => 1
        ]);
        
        $budget = $this->db->getLastInsertID();
        
        $this->insert('bill_type', [
            'name' => 'Remito',
            'code' => '',
            'type' => '',
            'view' => 'default',
            'multiplier' => 0,
            'customer_required' => 1,
            'invoice_class_id' => '',
            'class' => 'app\modules\sale\models\bills\DeliveryNote',
            'startable' => 1
        ]);
        
        $deliveryNote = $this->db->getLastInsertID();
        
        //Budget, Order y DeliveryNote pueden generar facturas:
        foreach($bills as $bill){
            $this->insert('bill_type_has_bill_type', [
                'parent_id' => $order,
                'child_id' => $bill
            ]);
            
            $this->insert('bill_type_has_bill_type', [
                'parent_id' => $budget,
                'child_id' => $bill
            ]);
            
            $this->insert('bill_type_has_bill_type', [
                'parent_id' => $deliveryNote,
                'child_id' => $bill
            ]);
        }
        
        //Budget puede generar Order
        $this->insert('bill_type_has_bill_type', [
            'parent_id' => $budget,
            'child_id' => $order
        ]);
        
        //Budget puede generar DeliveryNote
        $this->insert('bill_type_has_bill_type', [
            'parent_id' => $budget,
            'child_id' => $deliveryNote
        ]);
        
        //Order puede generar DeliveryNote
        $this->insert('bill_type_has_bill_type', [
            'parent_id' => $order,
            'child_id' => $deliveryNote
        ]);
    }

    public function down()
    {
        echo "m151020_182338_group_full_arg cannot be reverted.\n";

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
