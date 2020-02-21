<?php

use yii\db\Migration;

/**
 * Class m200214_193113_error_backups_emails
 */
class m200214_193113_error_backups_emails extends Migration
{
    public function init()
    {
        $this->db = 'dbconfig';
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $category = \app\modules\config\models\Category::findOne(['name' => 'Backups']);

        if (empty($category)) {
            $category = new \app\modules\config\models\Category([
                'name' => 'Backups',
                'status' => 'enabled'
            ]);

            $category->save();
        }

        $this->insert('item', [
            'attr' => 'backup_emails_notify',
            'type' => 'textInput',
            'label' => "Emails para notificar",
            'description' => "separados por comas",
            'multiple' => 0,
            'category_id' => $category->category_id,
            'superadmin' => 0,
            'default' => 'jpdona@quoma.com.ar,jperez@quoma.com.ar'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200214_193113_error_backups_emails cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200214_193113_error_backups_emails cannot be reverted.\n";

        return false;
    }
    */
}
