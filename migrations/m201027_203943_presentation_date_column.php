<?php

use yii\db\Migration;

/**
 * Class m201027_203943_presentation_date_column
 */
class m201027_203943_presentation_date_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('firstdata_export', 'presentation_date', 'INT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('firstdata_export', 'presentation_date');

    }

    
}
