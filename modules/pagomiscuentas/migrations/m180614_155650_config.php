<?php

use app\modules\config\models\Category;
use yii\db\Migration;

/**
 * Class m180614_155650_config
 */
class m180614_155650_config extends Migration
{

    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }


    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $category = new Category();
        $category->name = 'Pagomiscuentas';
        $category->status = 'enabled';
        $category->save();

        $this->insert('item', [
            'attr' => 'pagomiscuentas-payment-method',
            'type' => 'textInput',
            'label' => 'Metodo de pago por defecto para Pagomiscuenta',
            'description' => 'Metodo de pago por defecto para Pagomiscuentas',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 1,
            'default' => 9
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180614_155650_config cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180614_155650_config cannot be reverted.\n";

        return false;
    }
    */
}
