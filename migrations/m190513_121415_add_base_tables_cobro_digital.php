<?php

use yii\db\Migration;
use app\modules\sale\models\Company;
use app\modules\checkout\models\PaymentMethod;
use app\modules\checkout\models\CompanyHasPaymentTrack;
use app\modules\checkout\models\Track;

class m190513_121415_add_base_tables_cobro_digital extends Migration
{

    public function safeUp()
    {
        $this->createTable('track', [
            'track_id' => $this->primaryKey(),
            'name' => $this->string(),
            'slug' => $this->string(),
            'description' => $this->text()
        ]);

        $this->insert('track', [
           'name' => 'Directo',
           'slug' => 'directo',
           'description' => 'Los códigos de pago se administran directamente desde gestión'
        ]);

        $this->insert('track', [
            'name' => 'Cobro digital',
            'slug' => 'cobro-digital',
            'description' => 'Los códigos de pago se administran directamente desde la entidad de cobro digital'
        ]);

        $this->createTable('company_has_payment_track', [
            'company_has_payment_track_id' => $this->primaryKey(),
            'company_id' => $this->integer(),
            'payment_method_id' => $this->integer(),
            'track_id' => $this->integer(),
            'status' => "ENUM('enabled', 'disabled')"
        ]);

        $this->addForeignKey('fk_company_has_payment_track_company_id', 'company_has_payment_track', 'company_id', 'company', 'company_id');
        $this->addForeignKey('fk_company_has_payment_track_payment_method_id', 'company_has_payment_track', 'payment_method_id', 'payment_method', 'payment_method_id');
        $this->addForeignKey('fk_company_has_payment_track_track_id', 'company_has_payment_track', 'track_id', 'track', 'track_id');

        $this->addColumn('customer', 'payment_code_cobro_digital_19', $this->string());
        $this->addColumn('customer', 'payment_code_cobro_digital_29', $this->string());
        $this->addColumn('customer', 'payment_code_cobro_digital_pdf', $this->text());

        $track = Track::find()->where(['slug' => 'directo'])->one();

        foreach (Company::find()->all() as $company) {
            foreach (PaymentMethod::find()->all() as $payment_method) {
                $this->insert('company_has_payment_track', [
                    'company_id' => $company->company_id,
                    'payment_method_id' => $payment_method->payment_method_id,
                    'track_id' => $track->track_id,
                    'status' => CompanyHasPaymentTrack::STATUS_DISABLED
                ]);
            }
        }

    }

    public function safeDown()
    {
        $this->dropColumn('customer', 'payment_code_cobro_digital_pdf');
        $this->dropColumn('customer', 'payment_code_cobro_digital_29');
        $this->dropColumn('customer', 'payment_code_cobro_digital_19');

        $this->dropTable('company_has_payment_track');
        $this->dropTable('track');
    }
}
