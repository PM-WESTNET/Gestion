<?php

use yii\db\Migration;

/**
 * Handles the creation of table `siro_company_config`.
 */
class m220914_135514_create_siro_company_config_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('siro_company_config', [
            'id' => $this->primaryKey(),
             // company fk
            'company_id' => $this->integer()->notNull()->unique(),
            // bool that determines if this company can do Siro stuff
            'is_enabled' => $this->boolean()->defaultValue(0),
            // numero de convenio
            'company_agreement_id' => $this->string()->notNull()->unique(),

            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        //add foreign key for table `company`
        $this->addForeignKey(
            'fk_company_id',
            'siro_company_config',
            'company_id',
            'company',
            'company_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('siro_company_config');
    }
}
