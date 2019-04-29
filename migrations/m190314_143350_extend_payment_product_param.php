<?php

use app\modules\config\models\Category;
use yii\db\Migration;

/**
 * Class m190314_143350_extend_payment_product_param
 */
class m190314_143350_extend_payment_product_param extends Migration
{
    public function init()
    {
        $this->db= 'dbconfig';
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $category = Category::findOne(['name' => 'Productos']);

        $this->insert('item', [
            'attr' => 'extend_payment_product_id',
            'type' => 'textInput',
            'label' => "ID Producto para extensión de pago",
            'description' => "Producto para asignarle al adicional que se crea al realizar el forzado de la conexión",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 0
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $item= \app\modules\config\models\Item::findOne(['attr' => 'extend_payment_product_id']);

        if ($item) {
            return $item->delete();
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190314_143350_extend_payment_product_param cannot be reverted.\n";

        return false;
    }
    */
}
