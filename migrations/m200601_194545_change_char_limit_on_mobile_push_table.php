<?php

use yii\db\Migration;

/**
 * Class m200601_194545_change_char_limit_on_mobile_push_table
 */
class m200601_194545_change_char_limit_on_mobile_push_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('mobile_push', 'title', $this->text());

        //TODO actualizar charset en tabla de mobile_push, mobile_push_has_userapp
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('mobile_push', 'title', $this->string(45));
    }
}
