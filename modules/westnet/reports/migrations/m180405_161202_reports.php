<?php

use yii\db\Migration;

class m180405_161202_reports extends Migration
{
    public function up()
    {
        $this->execute('CREATE TABLE report_data (
                          report_data_id int(11) NOT NULL AUTO_INCREMENT,
                          report varchar(50) not null,
                          period int(6) NOT NULL,
                          value double NOT NULL,
                          PRIMARY KEY (report_data_id)
                        ) ENGINE=InnoDB ;');
    }

    public function down()
    {
        echo "m180405_161202_reports cannot be reverted.\n";

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
