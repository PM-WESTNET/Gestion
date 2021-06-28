<?php

use yii\db\Migration;

/**
 * Handles adding has_automatic_debit to table `destinatary`.
 */
class m210625_171403_add_has_automatic_debit_columns_to_destinatary_table extends Migration
{
    public function init() 
    {
        $this->db = 'dbnotifications';
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function Up()
    {
        $this->addColumn('{{%destinatary}}','has_automatic_debit', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function Down()
    {
        $this->dropColumn('{{%destinatary}}','has_automatic_debit');
    }
}
