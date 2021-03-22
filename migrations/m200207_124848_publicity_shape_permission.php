<?php

use webvimark\modules\UserManagement\models\rbacDB\Role;
use yii\db\Migration;

/**
 * Class m200207_124848_publicity_shape_permission
 */
class m200207_124848_publicity_shape_permission extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Role::create(
            'Manejo de canales de publicidad',
            'Manejo de canales de publicidad',
            'Clientes');

        Role::assignRoutesViaPermission(
            'Manejo de canales de publicidad',
            'Canales de publicidad', [
                'sale/publicity-shape/*'
            ],
            'Manejo de canales de publicidad',
            'Clientes'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }
}
