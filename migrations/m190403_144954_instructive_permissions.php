<?php

use yii\db\Migration;

/**
 * Class m190403_144954_instructive_permissions
 */
class m190403_144954_instructive_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $roles = \webvimark\modules\UserManagement\models\rbacDB\Role::find()->all();

        foreach ($roles as $role) {
            \webvimark\modules\UserManagement\models\rbacDB\Role::assignRoutesViaPermission(
                $role->name,
                'view-instructive',
                ['/instructive/instructive/index', '/instructive/instructive/view'],
                'Can View Instructive',
                'userCommonPermissions'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190403_144954_instructive_permissions cannot be reverted.\n";

        return false;
    }
    */
}
