<?php

use app\modules\cobrodigital\models\PaymentCard;
use Codeception\Test\Unit;
use app\tests\fixtures\PaymentCardFileFixture;

class PaymentCardTest extends Unit {
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _fixtures()
    {
        return [
            'payment_card_file' => [
                'class' => PaymentCardFileFixture::class
            ],
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new PaymentCard();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new PaymentCard([
            'payment_card_file_id' => 1,
            'code_19_digits' => '5930034400019999999',
            'code_29_digits' => '73859300344000109052900000008',
            'url' => '73859300344000109052900000008'
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new PaymentCard();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new PaymentCard([
            'payment_card_file_id' => 1,
            'code_19_digits' => '5930034400019999999',
            'code_29_digits' => '73859300344000109052900000008',
            'url' => '73859300344000109052900000008'
        ]);

        expect('Saved when full and new', $model->save())->true();
    }
}