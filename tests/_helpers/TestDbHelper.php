<?php

class TestDbHelper
{

    private $_actor;

    public static function execute($sql, $db = 'db')
    {
        switch ($db) {
            case 'afip':
                $used_db = \Yii::$app->dbafip;
                break;
            case 'config':
                $used_db = \Yii::$app->config;
                break;
            case 'agenda':
                $used_db = \Yii::$app->dbagenda;
                break;
            case 'ecopago':
                $used_db = \Yii::$app->dbecopago;
                break;
            case 'dbnotifications':
                $used_db = \Yii::$app->dbnotifications;
                break;
            case 'log':
                $used_db = \Yii::$app->dblog;
                break;
            case 'ticket':
                $used_db = \Yii::$app->dbticket;
                break;
            case 'media':
                $used_db = \Yii::$app->dbmedia;
                break;
            default :
                $used_db = \Yii::$app->db;
                break;
        }

        $command = $used_db->createCommand();
        $command->setSql($sql);
        return $command->execute();
    }

    private function exec($sql)
    {
        $this->_actor->expect($sql);
        exec($sql);
    }

    public function initializeDb($actor)
    {
        $this->_actor = $actor;

        $db_arya = \app\components\helpers\DbHelper::getDbName(\Yii::$app->db);
        $db_afip = \app\components\helpers\DbHelper::getDbName(\Yii::$app->dbafip);
        $db_config = \app\components\helpers\DbHelper::getDbName(\Yii::$app->dbconfig);
        $db_agenda = \app\components\helpers\DbHelper::getDbName(\Yii::$app->dbagenda);
        $db_ticket = \app\components\helpers\DbHelper::getDbName(\Yii::$app->dbticket);
        $db_ecopago = \app\components\helpers\DbHelper::getDbName(\Yii::$app->dbecopago);
        $db_notifications = \app\components\helpers\DbHelper::getDbName(\Yii::$app->dbnotifications);
        $db_log = \app\components\helpers\DbHelper::getDbName(\Yii::$app->dblog);
        $db_media = \app\components\helpers\DbHelper::getDbName(\Yii::$app->dbmedia);

        $host = '-h ' . \app\components\helpers\DbHelper::getDbHost(\Yii::$app->db);
        $username = '-u ' . \Yii::$app->db->username;

        $port = '--port=' . \app\components\helpers\DbHelper::getDbPort(\Yii::$app->db);
        if ($port == '--port=') {
            $port = '';
        }

        if (empty(\Yii::$app->db->password)) {
            $password = '';
        } else {
            $password = "-p'" . \Yii::$app->db->password . "'";
        }

        // drop databases
        $this->exec("mysql $username $password $host $port -e \"DROP DATABASE $db_afip;\"");
        $this->exec("mysql $username $password $host $port -e \"DROP DATABASE $db_config\"");
        $this->exec("mysql $username $password $host $port -e \"DROP DATABASE $db_agenda\"");
        $this->exec("mysql $username $password $host $port -e \"DROP DATABASE $db_ticket\"");
        $this->exec("mysql $username $password $host $port -e \"DROP DATABASE $db_ecopago\"");
        $this->exec("mysql $username $password $host $port -e \"DROP DATABASE $db_notifications\"");
        $this->exec("mysql $username $password $host $port -e \"DROP DATABASE $db_log\"");
        $this->exec("mysql $username $password $host $port -e \"DROP DATABASE $db_media\"");
        $this->exec("mysql $username $password $host $port -e \"DROP DATABASE $db_arya;\"");

        // create databases
        $this->exec("mysql $username $password $host $port -e \"CREATE DATABASE $db_arya DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;\"");
        $this->exec("mysql $username $password $host $port -e \"CREATE DATABASE $db_afip DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;\"");
        $this->exec("mysql $username $password $host $port -e \"CREATE DATABASE $db_config DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;\"");
        $this->exec("mysql $username $password $host $port -e \"CREATE DATABASE $db_agenda DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;\"");
        $this->exec("mysql $username $password $host $port -e \"CREATE DATABASE $db_ticket DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;\"");
        $this->exec("mysql $username $password $host $port -e \"CREATE DATABASE $db_ecopago DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;\"");
        $this->exec("mysql $username $password $host $port -e \"CREATE DATABASE $db_notifications DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;\"");
        $this->exec("mysql $username $password $host $port -e \"CREATE DATABASE $db_log DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;\"");
        $this->exec("mysql $username $password $host $port -e \"CREATE DATABASE $db_media DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;\"");

        // upload databases
        $this->exec("mysql $username $password $host $port $db_arya < tests/_data/arya.sql");
        $this->exec("mysql $username $password $host $port $db_afip < tests/_data/arya_afip.sql");
        $this->exec("mysql $username $password $host $port $db_config < tests/_data/arya_config.sql");
        $this->exec("mysql $username $password $host $port $db_agenda < tests/_data/arya_agenda.sql");
        $this->exec("mysql $username $password $host $port $db_ticket < tests/_data/arya_ticket.sql");
        $this->exec("mysql $username $password $host $port $db_ecopago < tests/_data/arya_ecopago.sql");
        $this->exec("mysql $username $password $host $port $db_notifications < tests/_data/arya_notifications.sql");
        $this->exec("mysql $username $password $host $port $db_log < tests/_data/arya_log.sql");
        $this->exec("mysql $username $password $host $port $db_media < tests/_data/arya_media.sql");
    }

    public static function cleanBills()
    {
        $firstBillId = 13;

        static::execute("DELETE FROM `bill_has_payment` where `bill_id` in (SELECT `bill_id` FROM `bill` where `bill_id` > $firstBillId)");
        static::execute("DELETE FROM `stock_movement` where `bill_detail_id` in (SELECT `bill_detail_id` FROM `bill_detail` where `bill_id` > $firstBillId)");
        static::execute("DELETE FROM `bill_detail` where `bill_id` > $firstBillId");
        static::execute("DELETE FROM `bill` where `bill_id` > $firstBillId");
        static::execute("DELETE FROM `product_to_invoice` where `product_to_invoice_id` > 3");
    }

    public static function cleanDestinataries()
    {
        static::execute("DELETE FROM `destinatary_has_company`", 'dbnotifications');
        static::execute("DELETE FROM `destinatary_has_contract`", 'dbnotifications');
        static::execute("DELETE FROM `destinatary_has_contract_status`", 'dbnotifications');
        static::execute("DELETE FROM `destinatary_has_customer`", 'dbnotifications');
        static::execute("DELETE FROM `destinatary_has_customer_category`", 'dbnotifications');
        static::execute("DELETE FROM `destinatary_has_customer_status`", 'dbnotifications');
        static::execute("DELETE FROM `destinatary_has_node`", 'dbnotifications');
        static::execute("DELETE FROM `destinatary_has_plan`", 'dbnotifications');
    }

}
