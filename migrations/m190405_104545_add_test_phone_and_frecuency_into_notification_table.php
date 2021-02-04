<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 05/04/19
 * Time: 10:46
 */

use yii\db\Migration;

class m190405_104545_add_test_phone_and_frecuency_into_notification_table extends Migration
{
    public function init()
    {
        $this->db = 'dbnotifications';
        parent::init();
    }

    public function safeUp()
    {
        $this->addColumn('notification', 'test_phone', $this->string());
        $this->addColumn('notification', 'test_phone_frecuency', $this->integer());
        $this->update('notification', ['test_phone_frecuency' => 1000]);
    }

    public function safeDown()
    {
        $this->dropColumn('notification', 'test_phone_frecuency');
        $this->dropColumn('notification', 'test_phone');
    }
}