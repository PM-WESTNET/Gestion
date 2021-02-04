<?php

use yii\db\Migration;


class m161018_153947_correccion_adicionales_instalacion extends Migration
{
    public function up()
    {
        $instalacion_empresa_id= $this->db->createCommand("SELECT product_id FROM product WHERE system= 'instalacion-de-equipos-empresa'")->queryAll();
        $instalacion_residencial_id= $this->db->createCommand("SELECT product_id FROM product WHERE system= 'instalacion-de-equipos'")->queryAll();
        
        if (count($instalacion_empresa_id) == 0 or count($instalacion_residencial_id) == 0) {
            return true;
        }

        $adicionales= $this->db->createCommand("SELECT * FROM contract_detail WHERE status = 'draft' AND from_date is null  AND (product_id= '"
                .$instalacion_empresa_id[0]['product_id']."' OR product_id= '".$instalacion_residencial_id[0]['product_id']."') AND date >= '2016-10-13'")->queryAll();
        
        foreach ($adicionales as $add) {
            $this->execute("UPDATE contract_detail SET from_date='". $add['date']."' WHERE contract_detail_id='".$add['contract_detail_id']."'");
        }
    }

    public function down()
    {
        echo "m161018_153947_correccion_adicionales_instalacion cannot be reverted.\n";

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
