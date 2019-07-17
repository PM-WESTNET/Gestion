<?php

use yii\db\Migration;

/**
 * Class m190716_180803_ivr_user
 */
class m190716_180803_ivr_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        \webvimark\modules\UserManagement\models\rbacDB\Role::create('ivr', 'Role para usuario IVR');

        $user = new \webvimark\modules\UserManagement\models\User([
            'username' => 'ivruser',
            'status' => \webvimark\modules\UserManagement\models\User::STATUS_ACTIVE,
            'password' => 'aT63A7eYRv8wwAsv',
            'repeat_password' => 'aT63A7eYRv8wwAsv'
        ]);

        $user->scenario = 'newUser';

        if (!$user->save()){
            return false;
        }

        \webvimark\modules\UserManagement\models\User::assignRole($user->id, 'ivr');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        \webvimark\modules\UserManagement\models\rbacDB\Role::deleteIfExists(['name' => 'ivr']);
        $user = \webvimark\modules\UserManagement\models\User::findOne(['username' => 'ivruser']);

        if ($user) {
            $user->delete();
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190716_180803_ivr_user cannot be reverted.\n";

        return false;
    }
    */
}
