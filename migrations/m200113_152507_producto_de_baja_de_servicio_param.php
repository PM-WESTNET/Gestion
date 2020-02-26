<?php

use yii\db\Migration;

/**
 * Class m200113_152507_producto_de_baja_de_servicio_param
 */
class m200113_152507_producto_de_baja_de_servicio_param extends Migration
{
    public function init()
    {
        $this->db = 'dbconfig';
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $category = \app\modules\config\models\Category::findOne(['name' => 'Productos']);

        if(empty($category)) {
            $category = new \app\modules\config\models\Category([
                'name' => 'Productos',
                'status' => 'enabled'
            ]);

            $category->save();
        }

        $this->insert('item', [
            'attr' => 'baja_product_id',
            'type' => 'textInput',
            'label' =>"ID de producto de baja de servicio",
            'description' => "Producto para crear la NC de baja de servicio",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 28
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $item = \app\modules\config\models\Item::findOne(['attr' => 'baja_product_id']);

        if ($item) {
            $item->delete();
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200113_152507_producto_de_baja_de_servicio_param cannot be reverted.\n";

        return false;
    }
    */
}
