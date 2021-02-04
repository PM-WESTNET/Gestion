<?php

use yii\db\Migration;

/**
 * Class m190226_145857_venta_global_diaria_document_type
 */
class m190226_145857_venta_global_diaria_document_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('document_type', [
           'name' => 'Venta Global Diaria',
            'code' => 99
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $document_type= \app\modules\sale\models\DocumentType::findOne(['name' => 'Venta Global Diaria']);

        if ($document_type && $document_type->getDeletable()) {
            $document_type->delete();
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190226_145857_venta_global_diaria_document_type cannot be reverted.\n";

        return false;
    }
    */
}
