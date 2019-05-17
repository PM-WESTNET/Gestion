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
            'description' => $this->text(),
            'use_payment_card' => $this->boolean(),
        ]);

        $this->insert('track', [
           'name' => 'Directo',
           'slug' => 'directo',
           'description' => 'Los códigos de pago se administran directamente desde gestión',
           'use_payment_card' => 0,
        ]);

        $this->insert('track', [
            'name' => 'Cobro digital',
            'slug' => 'cobro-digital',
            'description' => 'Los códigos de pago se administran directamente desde la entidad de cobro digital',
            'use_payment_card' => 1,
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

        $this->createTable('payment_card_file', [
            'payment_card_file_id' => $this->primaryKey(),
            'upload_date' => $this->string(),
            'file_name' => $this->string(),
            'path' =>  $this->text(),
            'status' => "ENUM('draft', 'imported')"
        ]);

        $this->createTable('payment_card', [
            'payment_card_id' => $this->primaryKey(),
            'payment_card_file_id' => $this->integer(),
            'code_19_digits' => $this->string(),
            'code_29_digits' => $this->string(),
            'url' => $this->text(),
            'used' => $this->boolean(),
        ]);

        $this->addForeignKey('fk_payment_card_payment_card_file_id', 'payment_card', 'payment_card_file_id', 'payment_card_file', 'payment_card_file_id');

    }

    public function safeDown()
    {
        $this->dropTable('payment_card');

        $this->dropColumn('customer', 'payment_code_cobro_digital_pdf');
        $this->dropColumn('customer', 'payment_code_cobro_digital_29');
        $this->dropColumn('customer', 'payment_code_cobro_digital_19');

        $this->dropTable('company_has_payment_track');
        $this->dropTable('track');
    }
}
