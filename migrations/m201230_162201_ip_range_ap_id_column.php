<?php

use yii\db\Migration;

/**
 * Class m201230_162201_ip_range_ap_id_column
 */
class m201230_162201_ip_range_ap_id_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('ip_range', 'ap_id', 'INT NULL');
        $this->addForeignKey('fk_ip_range_ap', 'ip_range', 'ap_id', 'access_point', 'access_point_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('ip_range', 'ap_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201230_162201_ip_range_ap_id_column cannot be reverted.\n";

        return false;
    }
    */
}
