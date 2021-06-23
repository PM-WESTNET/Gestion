<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%firstdata_import}}`.
 */
class m210520_145554_add_firsdata_config_id_columns_to_firstdata_import_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%firstdata_import}}','firstdata_config_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%firstdata_import}}','firstdata_config_id');
    }
}
