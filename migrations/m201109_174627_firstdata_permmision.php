<?php

use yii\db\Migration;
use webvimark\modules\UserManagement\models\rbacDB\Role;

/**
 * Class m201109_174627_firstdata_permmision
 */
class m201109_174627_firstdata_permmision extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Role::create('first-data-admin', 'Firstdata');

        Role::assignRoutesViaPermission('first-data-admin', 'admin-first-data', [
            'firstdata/firstdata-automatic-debit/index',
            'firstdata/firstdata-automatic-debit/view',
            'firstdata/firstdata-automatic-debit/create',
            'firstdata/firstdata-automatic-debit/update',
            'firstdata/firstdata-automatic-debit/delete',
            'firstdata/firstdata-company-config/index',
            'firstdata/firstdata-company-config/view',
            'firstdata/firstdata-company-config/create',
            'firstdata/firstdata-company-config/update',
            'firstdata/firstdata-company-config/delete',
            'firstdata/firstdata-export/index',
            'firstdata/firstdata-export/view',
            'firstdata/firstdata-export/create',
            'firstdata/firstdata-export/create-file',
            'firstdata/firstdata-export/download',
            'firstdata/firstdata-import/index',
            'firstdata/firstdata-import/view',
            'firstdata/firstdata-import/create',
            'firstdata/firstdata-import/update',
            'firstdata/firstdata-import/delete',
            'firstdata/firstdata-import/close-payments',
        ], 'Can admin Firstdata');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201109_174627_firstdata_permmision cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201109_174627_firstdata_permmision cannot be reverted.\n";

        return false;
    }
    */
}
