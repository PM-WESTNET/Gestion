<?php

use app\modules\automaticdebit\models\DebitDirectFailedPayment;
use Codeception\Test\Unit;

class DebitDirectFailedPaymentTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _fixtures()
    {
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new DebitDirectFailedPayment();

        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new DebitDirectFailedPayment([
            'amount' => 100,
            'customer_code' => '123456',
            'date' => (new \DateTime('now'))->format('Y-m-d'),
            'cbu' => '123456789',
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new DebitDirectFailedPayment();

        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new DebitDirectFailedPayment([
            'amount' => 100,
            'customer_code' => '123456',
            'date' => (new \DateTime('now'))->format('Y-m-d'),
            'cbu' => '123456789',
        ]);

        expect('Save when full and new', $model->save())->true();
    }
}