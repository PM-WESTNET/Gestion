<?php

use yii\db\Schema;
use yii\db\Migration;

class m160101_000000_create_tables_logs extends Migration
{
    public function init()
    {
        $this->db = 'dblog';
        parent::init();
    }

    public function up()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS log (
          log_id int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
          route varchar(100) DEFAULT NULL,
          user_id int(11) DEFAULT NULL,
          datetime int(11) DEFAULT NULL,
          model varchar(45) DEFAULT NULL,
          model_id int(11) DEFAULT NULL,
          data text
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

    }

    public function down()
    {
        echo "m140101_000000_create_tables_logs cannot be reverted.\n";

        return false;
    }
}