<?php

use yii\db\Migration;

class m170512_170013_parametro_ciudad_815 extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function up()
    {
        $category = \app\modules\config\models\Category::findOne(['name' => 'Westnet']);

        $this->insert('item', [
            'attr' => '815_ciudad_id',
            'type' => 'textInput',
            'label' => 'Id de la ciudad por defecto en 815',
            'description' => '',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => '34'
        ]);
    }

    public function down()
    {
        echo "m170512_170013_parametro_ciudad_815 cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
