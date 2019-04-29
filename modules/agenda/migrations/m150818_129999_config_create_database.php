<?php

use yii\db\Schema;
use yii\db\Migration;

class m150818_129999_config_create_database extends Migration {

    public function up() {
        $db = app\components\helpers\DbHelper::getDbName(Yii::$app->dbagenda);
        $this->execute("create database $db");
    }

    public function down() {
        $db = app\components\helpers\DbHelper::getDbName(Yii::$app->dbagenda);

        $this->execute("drop database $db");
        return true;
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
