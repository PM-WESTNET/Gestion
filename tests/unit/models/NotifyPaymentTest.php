<?php

use app\modules\accounting\models\AccountConfigHasAccount;
use app\tests\fixtures\AccountConfigFixture;
use app\tests\fixtures\AccountFixture;
use app\modules\westnet\models\NotifyPayment;
use app\tests\fixtures\PaymentMethodFixture;

class NotifyPaymentTest extends \Codeception\Test\Unit
{
    protected function _before()
    {

    }

    protected function _after()
    {

    }

    public function _fixtures()
    {
        return [
            'payment_method' => [
                'class' => PaymentMethodFixture::class,
            ],
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new NotifyPayment();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new NotifyPayment([
            'amount' => 100,
            'payment_method_id' => 1,
            'image_receipt' =>  'sahld',
            'date' => (new \DateTime('now'))->format('Y-m-d'),
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new NotifyPayment();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new NotifyPayment([
            'amount' => 100,
            'payment_method_id' => 1,
            'image_receipt' =>  'sahld',
            'date' => (new \DateTime('now'))->format('Y-m-d'),
        ]);

        expect('Saved when full and new', $model->save())->true();
    }
}