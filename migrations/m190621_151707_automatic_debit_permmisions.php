<?php

use yii\db\Migration;

/**
 * Class m190621_151707_automatic_debit_permmisions
 */
class m190621_151707_automatic_debit_permmisions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        \webvimark\modules\UserManagement\models\rbacDB\Role::create('Direct Debit Manager', 'Debito Directo');

        \webvimark\modules\UserManagement\models\rbacDB\Role::assignRoutesViaPermission('Direct Debit Manager', 'Can manage direct debit', [
            '/automaticdebit/bank/index',
            '/automaticdebit/bank/create',
            '/automaticdebit/bank/view',
            '/automaticdebit/bank/update',
            '/automaticdebit/bank/delete',
            '/automaticdebit/bank/imports',
            '/automaticdebit/bank/create-import',
            '/automaticdebit/bank/view-import',
            '/automaticdebit/bank/exports',
            '/automaticdebit/bank/create-export',
            '/automaticdebit/bank/view-export',
            '/automaticdebit/bank/download-export',
            '/automaticdebit/bank-company-config/view',
            '/automaticdebit/bank-company-config/create',
            '/automaticdebit/bank-company-config/update',
            '/automaticdebit/bank-company-config/delete',
            '/automaticdebit/automatic-debit/index',
            '/automaticdebit/automatic-debit/view',
            '/automaticdebit/automatic-debit/create',
            '/automaticdebit/automatic-debit/update',
            '/automaticdebit/automatic-debit/delete',
        ], 'Débito Directo', 'Administrative');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190621_151707_automatic_debit_permmisions cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190621_151707_automatic_debit_permmisions cannot be reverted.\n";

        return false;
    }
    */
}
