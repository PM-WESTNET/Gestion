<?php

use app\modules\checkout\models\CompanyHasPaymentTrack;
use app\tests\fixtures\CompanyFixture;
use app\tests\fixtures\PaymentMethodFixture;
use app\tests\fixtures\TrackFixture;

class CompanyHasPaymentCompanyHasPaymentTrackTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    

    public function _fixtures()
    {
        return [
            'company' => [
                'class' => CompanyFixture::class
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
        $model = new CompanyHasPaymentTrack();

        expect('CompanyHasPaymentTrack not valid when new and full', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new CompanyHasPaymentTrack([
            'company_id' => 1,
            'payment_method_id' => 1,
            'track_id' => 1,
        ]);

        expect('CompanyHasPaymentTrack valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new CompanyHasPaymentTrack();

        expect('CompanyHasPaymentTrack not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new CompanyHasPaymentTrack([
            'company_id' => 1,
            'payment_method_id' => 1,
            'track_id' => 1,
        ]);

        expect('CompanyHasPaymentTrack saved when full and new', $model->save())->true();
    }

}