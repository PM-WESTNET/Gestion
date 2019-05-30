<?php

use yii\db\Migration;

class m190529_145757_add_fields_into_company_has_payment_track_table extends Migration
{

    public function safeUp()
    {
        $this->dropColumn('company_has_payment_track', 'status');
        $this->addColumn('company_has_payment_track', 'payment_status', "ENUM('enabled', 'disabled')");
        $this->addColumn('company_has_payment_track', 'track_status', "ENUM('enabled', 'disabled')");
        $this->addColumn('company_has_payment_track', 'payment_track_status', "ENUM('enabled', 'disabled')");
        $this->addColumn('company_has_payment_track', 'customer_status', "ENUM('enabled', 'disabled')");
        $this->addColumn('company_has_payment_track', 'default_track', $this->boolean());
    }

    public function safeDown()
    {
        $this->addColumn('company_has_payment_track', 'status', "ENUM('enabled', 'disabled')");
        $this->dropColumn('company_has_payment_track', 'payment_status');
        $this->dropColumn('company_has_payment_track', 'track_status');
        $this->dropColumn('company_has_payment_track', 'payment_track_status');
        $this->dropColumn('company_has_payment_track', 'customer_status');
        $this->dropColumn('company_has_payment_track', 'default_track');
    }
}
