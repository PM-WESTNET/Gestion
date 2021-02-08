<?php

use yii\db\Migration;
use app\modules\config\models\Category;
use app\modules\westnet\models\NotifyPayment;

class m190910_150809_add_fields_into_notify_payment_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('notify_payment', 'verified', $this->boolean());
        $this->addColumn('notify_payment', 'verified_by_user_id', $this->integer());

        $this->addForeignKey('fk_notify_payment_verified_by_user_id', 'notify_payment', 'verified_by_user_id', 'user', 'id');

        \webvimark\modules\UserManagement\models\rbacDB\Role::create('User-alert-new-no-verified-tranferences', ' En la página principal el usuario recibe alertas de informes de pago por transferencias no verificados');

        NotifyPayment::updateAll(['verified' => 0]);
    }

    public function safeDown()
    {
        $this->dropColumn('notify_payment', 'verified');
    }
}
