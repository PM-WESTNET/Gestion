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

    public function testInvalidWhenNewAndEmpty()
    {
        $model = new PaymentMethod();

        expect('Invalid when new and empty', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new PaymentMethod([
            'name' => 'Payment method 1'
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenNewAndEmpty()
    {
        $model = new PaymentMethod();

        expect('Not saved when new and empty', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new PaymentMethod([
            'name' => 'Medio de pago'
        ]);

        expect('Saved when new and full', $model->save())->true();
    }

    public function testGetAllowedTrackConfigPaymentMethods()
    {
        $payments_qty = count(PaymentMethod::find()->where(['allow_track_config' => 1])->all());

        expect('Allowed track payments methods', count(PaymentMethod::getAllowedTrackConfigPaymentMethods()))->equals($payments_qty);
    }

    public function testGetAllowedAndEnabledPaymentMethods() {
        $company_has_payment_tracks = CompanyHasPaymentTrack::findOne(408);
        $company_has_payment_tracks->updateAttributes(['payment_status' => 'enabled']);
        $payment_method = PaymentMethod::find()->where(['payment_method_id' => $company_has_payment_tracks->payment_method_id])->one();
        $payment_method->updateAttributes(['allow_track_config' => 1]);

        $payment_methods_qty = count(PaymentMethod::getAllowedAndEnabledPaymentMethods(1));

        expect('Payment method qty is 5', $payment_methods_qty)->equals(5);
    }

    // tests
    public function testSaveSuccessWithoutSendIvr()
    {
        $paymentMethod = new PaymentMethod([
           'name' => 'Payment Method 1',
            'status' => 'enabled',
            'register_number' => true,
            'type' => 'exchanging'
        ]);

        expect('Not save', $paymentMethod->save())->true();
    }

    public function testSaveSuccessWithSendIvr()
    {
        $paymentMethod = new PaymentMethod([
            'name' => 'Payment Method 1',
            'status' => 'enabled',
            'register_number' => true,
            'type' => 'exchanging',
            'send_ivr' => true
        ]);

        expect('Not save', $paymentMethod->save())->true();
    }

    public function testGetPaymentMethodsAvailableForApp()
    {
        $model = new PaymentMethod([
            'name' => 'paymentMethod',
            'status' => PaymentMethod::STATUS_ENABLED,
        ]);
        $model->save();

        expect('getPaymentMethodsAvailableForApp return 0', count(PaymentMethod::getPaymentMethodsAvailableForApp()))->equals(0);

        $model->updateAttributes(['show_in_app' => true]);

        expect('getPaymentMethodsAvailableForApp return 1', count(PaymentMethod::getPaymentMethodsAvailableForApp()))->equals(1);

    }

    public function testGetPaymentMethodForSelect()
    {
        $model = new PaymentMethod([
            'name' => 'paymentMethod',
            'status' => PaymentMethod::STATUS_ENABLED,
        ]);
        $model->save();

        $array = PaymentMethod::getPaymentMethodForSelect();
        expect('Array is not empty', array_key_exists($model->payment_method_id, $array))->true();
    }
}
