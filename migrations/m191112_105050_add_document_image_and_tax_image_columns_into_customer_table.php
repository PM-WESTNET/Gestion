<?php

use yii\db\Migration;

class m191112_105050_add_document_image_and_tax_image_columns_into_customer_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('customer', 'document_image', $this->string());
        $this->addColumn('customer', 'tax_image', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('customer', 'document_image');
        $this->dropColumn('customer', 'tax_image');
    }
}
