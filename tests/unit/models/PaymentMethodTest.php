<?php

use app\modules\checkout\models\PaymentMethod;
use app\tests\fixtures\CompanyHasPaymentTrackFixture;
use app\tests\fixtures\PaymentMethodFixture;
use app\modules\checkout\models\CompanyHasPaymentTrack;
use Codeception\Test\Unit;

class PaymentMethodTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function _fixtures(){
        return [
            'company_has_payment_track' => [
                'class' => CompanyHasPaymentTrackFixture::class,
            ],
            'payment_method' => [
                'class' => PaymentMethodFixture::class
            ],
        ];
    }

    public function testInvalidWhenEmptyAndNew()
    {
        $model = new PaymentMethod();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new PaymentMethod([
            'name' => 'Payment method 1'
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new PaymentMethod();
        expect('Invalid when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new PaymentMethod([
            'name' => 'Payment method 1'
        ]);

        expect('Valid when full and new', $model->save())->true();
    }

    public function testGetAllowedTrackConfigPaymentMethods()
    {
        $payments_qty = count(PaymentMethod::find()->where(['allow_track_config' => 1])->all());

        expect('Allowed track payments methods', count(PaymentMethod::getAllowedTrackConfigPaymentMethods()))->equals($payments_qty);
    }

    public function testGetAllowedAndEnabledPaymentMethods() {
        $company_has_payment_tracks = CompanyHasPaymentTrack::findOne(57);
        $company_has_payment_tracks->updateAttributes(['status' => 'enabled']);
        $payment_method = PaymentMethod::find()->where(['payment_method_id' => $company_has_payment_tracks->payment_method_id])->one();
        $payment_method->updateAttributes(['allow_track_config' => 1]);

        $payment_methods_qty = count(PaymentMethod::getAllowedAndEnabledPaymentMethods(1));

        expect('Payment method qty is 1', $payment_methods_qty)->equals(1);
    }
}
