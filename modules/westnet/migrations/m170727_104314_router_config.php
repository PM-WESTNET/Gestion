<?php

use yii\db\Migration;

class m170727_104314_router_config extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function up()
    {
        $category = \app\modules\config\models\Category::findOne(['name' => 'Westnet']);

        $this->insert('item', [
            'attr' => 'router_product_id',
            'type' => 'textInput',
            'label' => 'Id Del producto Router',
            'description' => '',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => '22'
        ]);
    }

    public function down()
    {
        echo "m170727_104314_router_config cannot be reverted.\n";

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
