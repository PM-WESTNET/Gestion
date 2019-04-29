<?php

use yii\db\Migration;

/**
 * Class m180219_144308_add_fk_ction_forced_historial
 */
class m180219_144308_add_fk_connection_forced_historial_user extends Migration
{

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addForeignKey('fk_connection_forced_historial_user', 'connection_forced_historial', 'user_id', 'user', 'id');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_connection_forced_historial_user', 'connection_forced_historial');
    }

}
