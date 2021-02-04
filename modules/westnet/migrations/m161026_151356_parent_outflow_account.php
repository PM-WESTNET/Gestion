<?php

use app\modules\config\models\Category;
use yii\db\Migration;

class m161026_151356_parent_outflow_account extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function up()
    {
        $category = Category::findOne(['name' => 'Contabilidad']);

        $this->insert('item', [
            'attr' => 'parent_outflow_account',
            'type' => 'textInput',
            'label' => 'Cuenta de Egreso por defecto.',
            'description' => '',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => ''
        ]);
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
