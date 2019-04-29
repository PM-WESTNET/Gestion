<?php

use yii\db\Migration;

class m170906_155731_low_reason extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function up()
    {
        $category = \app\modules\config\models\Category::findOne(['name' => 'Westnet']);

        $this->insert('item', [
            'attr' => 'mesa_category_low_reason',
            'type' => 'textInput',
            'label' => 'Categoria princiapal de Baja en Mesa',
            'description' => '',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => '15'
        ]);
    }

    public function down()
    {
        echo "m170906_155731_motivo_baja cannot be reverted.\n";

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
