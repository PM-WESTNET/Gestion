<?php

use yii\db\Migration;

class m181017_123022_remove_config_item_from_ads_category extends Migration
{

    public function init(){
        $this->db = 'dbconfig';
        parent::init();
    }
    public function safeUp()
    {
        $ads_email_technical_service_id = $this->db->createCommand("SELECT `item_id` FROM item WHERE attr = 'ads-email_technical_service' LIMIT 1")->queryScalar();
        $ads_comercial_office_id = $this->db->createCommand("SELECT `item_id` FROM item WHERE attr = 'ads-comercial-office' LIMIT 1")->queryScalar();
        $ads_contact_technical_service_id = $this->db->createCommand("SELECT `item_id` FROM item WHERE attr = 'ads-contact_technical_service' LIMIT 1")->queryScalar();

        $this->delete('config', ['item_id' => $ads_email_technical_service_id]);
        $this->delete('config', ['item_id' => $ads_comercial_office_id]);
        $this->delete('config', ['item_id' => $ads_contact_technical_service_id]);

        $this->delete('item', ['item_id' => $ads_email_technical_service_id]);
        $this->delete('item', ['item_id' => $ads_comercial_office_id]);
        $this->delete('item', ['item_id' => $ads_contact_technical_service_id]);
    }

    public function safeDown()
    {
        echo "m181017_123022_remove_config_item_from_ads_category cannot be reverted.\n";

        return false;
    }
}
