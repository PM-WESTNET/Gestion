<?php

use app\modules\config\models\Category;
use yii\db\Migration;

/**
 * Class m190527_204732_infobip_from_number_param
 */
class m190527_204732_infobip_from_number_param extends Migration
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
        $category = Category::findOne(['name' => 'Infobip']);

        if (empty($category)) {
            $category = new Category(['name' => 'Infobip', 'status' => 'enabled']);
            $category->save();
        }

        $this->insert('item', [
            'attr' => 'infobip_origin_number',
            'type' => 'textInput',
            'label' =>"Numero de origin de Infobip",
            'description' => "Puede ser un número o un string ",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 'Westnet'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190527_204732_infobip_from_number_param cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190527_204732_infobip_from_number_param cannot be reverted.\n";

        return false;
    }
    */
}
