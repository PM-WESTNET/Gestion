<?php

use app\tests\fixtures\CustomerFixture;
use app\tests\fixtures\PaymentMethodFixture;
use app\tests\fixtures\TrackFixture;
use app\modules\sale\models\CustomerHasPaymentTrack;

class CustomerHasPaymentTrackTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    

    public function _fixtures()
    {
        return [
            'customer' => [
                'class' => CustomerFixture::class
            ],
            'payment_method' => [
                'class' => PaymentMethodFixture::class
            ],
            'track' => [
                'class' => TrackFixture::class
            ]
        ];
    }

    protected function _after()
    {
    }

    public function testInvalidWhenEmptyAndNew()
    {
        $model = new CustomerHasPaymentTrack();

        expect('CustomerHasPaymentTrack not valid when new and full', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new CustomerHasPaymentTrack([
            'customer_id' => 45900,
            'payment_method_id' => 1,
            'track_id' => 1,
        ]);

        $model->validate();

        \Codeception\Util\Debug::debug($model->getErrors());

        expect('CustomerHasPaymentTrack valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new CustomerHasPaymentTrack();

        expect('CustomerHasPaymentTrack not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new CustomerHasPaymentTrack([
            'customer_id' => 45900,
            'payment_method_id' => 1,
            'track_id' => 1,
        ]);

        expect('CustomerHasPaymentTrack saved when full and new', $model->save())->true();
    }

}