<?php

use app\modules\checkout\models\PaymentMethod;

class PaymentMethodTest extends \Codeception\Test\Unit
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

    public function testInvalidWhenNewAndEmpty()
    {
        $model = new PaymentMethod();

        expect('Invalid when new and empty', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new PaymentMethod([
            'name' => 'Medio de pago'
        ]);

        expect('Valid when new and full', $model->validate())->true();
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
        expect('Array is empty', PaymentMethod::getPaymentMethodForSelect())->isEmpty();

        $model = new PaymentMethod([
            'name' => 'paymentMethod',
            'status' => PaymentMethod::STATUS_ENABLED,
        ]);
        $model->save();

        $array = PaymentMethod::getPaymentMethodForSelect();
        expect('Array is not empty', array_key_exists($model->payment_method_id, $array))->true();
    }
}