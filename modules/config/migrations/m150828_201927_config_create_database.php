<?php

use yii\db\Schema;
use yii\db\Migration;

class m150828_201927_config_create_database extends Migration
{
    public function up()
    {
        $db = app\components\helpers\DbHelper::getDbName(Yii::$app->dbconfig);
        $this->execute("create database $db");
    }

    public function down()
    {
        $db = app\components\helpers\DbHelper::getDbName(Yii::$app->dbconfig);
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
