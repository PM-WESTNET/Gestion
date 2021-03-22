<?php

use app\modules\config\models\Category;
use yii\db\Migration;

class m160818_164222_ticket_status_nuevo extends Migration
{
    public function init() {
        $this->db = 'dbconfig';
        parent::init();
    }

    public function up()
    {
        $category = Category::findOne(['name' => 'Ticket']);

        $this->insert('item', [
            'attr' => 'ticket_new_status_id',
            'type' => 'textInput',
            'label' => 'Id del estado Nuevo de ticket.',
            'description' => '',
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => '20'
        ]);
    }

    public function down()
    {
        echo "m160818_164222_ticket_status_nuevo cannot be reverted.\n";

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
