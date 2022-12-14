<?php

use yii\db\Migration;

/**
 * Class m190606_154527_contactos_mobile_app_param
 */
class m190606_154527_contactos_mobile_app_param extends Migration
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

        $this->insert('item',[
            'attr' => 'app_contact_info',
            'type' => 'textarea',
            'label' =>"Info de contacto para mostrar en la app",
            'description' => "",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 'Telefonos de Contacto: **********'
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
        echo "m190606_154527_contactos_mobile_app_param cannot be reverted.\n";

        return false;
    }
    */
}
