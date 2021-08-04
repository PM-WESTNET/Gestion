<?php

use yii\db\Migration;

/**
 * Handles the creation of table `menu`.
 */
class m210804_132327_create_menu_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('menu', [
            'menu_id' => $this->primaryKey(),
            'description' => $this->string(),
            'icon' => $this->string(),
            'route' => $this->string(),
            'menu_idmenu' => $this->integer(),
            'status' => $this->boolean(),
            'is_submenu' => $this->boolean(),
            'created_at' => $this->date(),
            'updated_at' => $this->date(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('menu');
    }
}
