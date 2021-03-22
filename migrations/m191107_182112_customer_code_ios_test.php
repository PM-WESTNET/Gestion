<?php

use yii\db\Migration;

/**
 * Class m191107_182112_customer_code_ios_test
 */
class m191107_182112_customer_code_ios_test extends Migration
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
       $category = \app\modules\config\models\Category::findOne(['name' => 'Mobile App']);

       if (empty($category)) {
           $category = new \app\modules\config\models\Category(['name' => 'Mobile App', 'status' => 'enabled']);
           $category->save();
       }

       $this->insert('item', [
           'attr' => 'customer_code_ios_test',
           'type' => 'textInput',
           'label' =>"Número de cliente para las pruebas de IOS",
           'description' => "Cliente para las pruebas de la app que realiza Apple",
           'multiple' => 0,
           'category_id' => $category->category_id,
           'superadmin' => 0,
           'default' => '27237'
       ]);

       $this->insert('item', [
           'attr' => 'validation_code_ios_test',
           'type' => 'textInput',
           'label' =>"Código de Validación para las pruebas de IOS",
           'description' => "Código de Validacion para las pruebas de la app que realiza Apple",
           'multiple' => 0,
           'category_id' => $category->category_id,
           'superadmin' => 0,
           'default' => '1001'
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
        echo "m191107_182112_customer_code_ios_test cannot be reverted.\n";

        return false;
    }
    */
}
