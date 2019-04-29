<?php

use yii\db\Migration;

class m160728_234809_vendor_comission_init extends Migration
{
    public function up()
    {
        $this->insert('vendor_commission', [
            'name' => 'Comisión 100%',
            'percentage' => '100',
            'value' => null
        ]);
        
        $commission_id = $this->getDb()->lastInsertID;
        
        $this->update('vendor', [
            'vendor_commission_id' => $commission_id
        ]);
        
        //Necesitamos datos para poder crear la constraint
        $this->execute("ALTER TABLE vendor ADD CONSTRAINT `fk_vendor_vendor_commission1`
            FOREIGN KEY (`vendor_commission_id`)
            REFERENCES `vendor_commission` (`vendor_commission_id`);");
    }

    public function down()
    {
        echo "m160728_234809_vendor_comission_init cannot be reverted.\n";

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
