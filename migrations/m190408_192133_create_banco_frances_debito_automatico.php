<?php

use yii\db\Migration;

/**
 * Class m190408_192133_create_banco_frances_debito_automatico
 */
class m190408_192133_create_banco_frances_debito_automatico extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('bank', [
            'name' => 'Banco Frances',
            'status' => \app\modules\automaticdebit\models\Bank::STATUS_ENABLED,
            'class' => 'app\modules\automaticdebit\components\BancoFrances',
            'created_at' => time(),
            'updated_at' => time()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $model = \app\modules\automaticdebit\models\Bank::findOne(['name' => 'Banco Frances']);

        if ($model && $model->getDeletable()) {
            return $model->delete();
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190408_192133_create_banco_frances_debito_automatico cannot be reverted.\n";

        return false;
    }
    */
}
