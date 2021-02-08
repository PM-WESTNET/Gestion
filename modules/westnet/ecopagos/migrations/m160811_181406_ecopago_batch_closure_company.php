<?php

use app\modules\config\models\Category;
use yii\db\Migration;

class m160811_181406_ecopago_batch_closure_company extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function up()
    {
        $category= Category::findOne(['name' => 'Ecopago']);

        $this->insert('item', [
            'attr' => 'ecopago_batch_closure_company_id',
            'type' => 'textInput',
            'label' => 'Empresa para rendiciones.',
            'description' => 'Empresa a la que se le asocian los movimientos de la rendicion.',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 1,
            'default' => 1
        ]);
    }

    public function down()
    {
        echo "m160811_181406_ecopago_batch_closure_company cannot be reverted.\n";

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
