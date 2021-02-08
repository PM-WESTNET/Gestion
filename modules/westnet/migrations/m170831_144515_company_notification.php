<?php

use yii\db\Migration;
use app\components\helpers\DbHelper;

class m170831_144515_company_notification extends Migration
{
    public function init() {
        $this->db = 'dbnotifications';
        parent::init();
    }

    public function up()
    {
        $this->execute('ALTER TABLE notification ADD company_id INT NULL;');
        $this->execute('ALTER TABLE notification ADD email_transport_id INT NULL;');
        $this->execute('ALTER TABLE notification ADD CONSTRAINT notification_company_company_id_fk FOREIGN KEY (company_id) REFERENCES '.DbHelper::getDbName(Yii::$app->db).'.company (company_id);');
        $this->execute('ALTER TABLE notification ADD CONSTRAINT notification_email_transport_company_id_fk FOREIGN KEY (email_transport_id) REFERENCES '.DbHelper::getDbName(Yii::$app->db).'.email_transport (email_transport_id);');
    }

    public function down()
    {
        echo "m170831_144515_company_notification cannot be reverted.\n";

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
