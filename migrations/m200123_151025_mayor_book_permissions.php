<?php

use yii\db\Migration;

/**
 * Class m200123_151025_mayor_book_permissions
 */
class m200123_151025_mayor_book_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        \webvimark\modules\UserManagement\models\rbacDB\Role::assignRoutesViaPermission(
            'Contable',
            'mayor_book', [
                'accounting/account-movement/mayor-book'
            ],
            'Libros Mayores',
            'Contabilidad'
        );
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
        echo "m200123_151025_mayor_book_permissions cannot be reverted.\n";

        return false;
    }
    */
}
