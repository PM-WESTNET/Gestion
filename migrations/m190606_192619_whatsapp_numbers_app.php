<?php

use yii\db\Migration;

/**
 * Class m190606_192619_whatsapp_numbers_app
 */
class m190606_192619_whatsapp_numbers_app extends Migration
{

    public function init()
    {
        $this->db= 'dbconfig';
        parent::init(); // TODO: Change the autogenerated stub

    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $category = \app\modules\config\models\Category::findOne(['name' => 'Mobile App']);

        if (empty($category)) {
            $category = new \app\modules\config\models\Category(['name' => 'Mobile App', 'status' => 'enabled']);

            $category->save();
        }

        $this->insert('item', [
            'attr' => 'app_ws_tecnico',
            'type' => 'textInput',
            'label' =>"Whatsapp del área Técnica",
            'description' => "Número de Teléfono de Whatsapp del área Técnica .Debe incluir el +54 9",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => '**********'
        ]);

        $this->insert('item', [
            'attr' => 'app_ws_admin',
            'type' => 'textInput',
            'label' =>"Whatsapp del área Administrativa",
            'description' => "Número de Teléfono de Whatsapp del área Administrativa. Debe incluir el +54 9",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => '**********'
        ]);

        $this->insert('item', [
            'attr' => 'app_ws_ventas',
            'type' => 'textInput',
            'label' =>"Whatsapp del área de Ventas",
            'description' => "Número de Teléfono de Whatsapp del área de Ventas. Debe incluir el +54 9",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => '**********'
        ]);
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
        echo "m190606_192619_whatsapp_numbers_app cannot be reverted.\n";

        return false;
    }
    */
}