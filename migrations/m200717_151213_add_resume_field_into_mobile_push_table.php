<?php

use yii\db\Migration;

/**
 * Class m200717_151213_add_resume_field_into_mobile_push_table
 */
class m200717_151213_add_resume_field_into_mobile_push_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('mobile_push', 'resume', $this->text());
        $this->addColumn('mobile_push_has_user_app', 'resume', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('mobile_push', 'resume', $this->text());
        $this->addColumn('mobile_push_has_user_app', 'resume', $this->text());
    }
}
