<?php

use yii\db\Migration;

/**
 * Class m190508_201728_verify_emails_permmissions
 */
class m190508_201728_verify_emails_permmissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        \webvimark\modules\UserManagement\models\rbacDB\Permission::assignRoutes('can-verify-emails',[
            '/sale/customer/verify-emails'
        ],'Can verify customers emails', 'Clientes');
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
        echo "m190508_201728_verify_emails_permmissions cannot be reverted.\n";

        return false;
    }
    */
}
