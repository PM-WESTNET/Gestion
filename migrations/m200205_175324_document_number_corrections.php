<?php

use yii\db\Migration;

/**
 * Class m200205_175324_document_number_corrections
 */
class m200205_175324_document_number_corrections extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $query = \app\modules\sale\models\Customer::find()
            ->andWhere(['like', 'document_number', '_']);

        $file = fopen(Yii::getAlias('@runtime/logs/correcion_dni.txt'), 'a');

        $countLog= "Clientes encontrados: " . $query->count();
        echo $countLog;
        echo "\n";

        fwrite($file, $countLog. PHP_EOL);

        $customers = $query->all();

        foreach ($customers as $customer) {
            if (substr($customer->document_number, -1) === '_') {
                $log = "Customer ID: ". $customer->customer_id . "\t". "Old document: ". $customer->document_number. "\t";
                $customer->updateAttributes(['document_number' => rtrim($customer->document_number, '_')]);
                $log .= "New document: " . $customer->document_number;
                fwrite($file, $log. PHP_EOL);
            }
        }

        fclose($file);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200205_175324_document_number_corrections cannot be reverted.\n";

        return false;
    }
    */
}
