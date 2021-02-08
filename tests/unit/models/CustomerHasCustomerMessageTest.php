<?php
/**
 * Created by PhpStorm.
 * User: dexterlab10
 * Date: 11/06/2019
 * Time: 16:55
 */

use app\modules\sale\models\CustomerHasCustomerMessage;
use app\tests\fixtures\CustomerFixture;
use app\tests\fixtures\CustomerMessageFixture;
use Codeception\Test\Unit;

class CustomerHasCustomerMessageTest extends Unit
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
            'customer_message' => [
                'class' => CustomerMessageFixture::class
            ]
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new CustomerHasCustomerMessage();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new CustomerHasCustomerMessage([
            'customer_id' => 45900,
            'customer_message_id' => 1,
            'timestamp' => (new \DateTime('now'))->getTimestamp()
        ]);
        $model->validate();

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new CustomerHasCustomerMessage();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new CustomerHasCustomerMessage([
            'customer_id' => 45900,
            'customer_message_id' => 1,
            'timestamp' => (new \DateTime('now'))->getTimestamp()
        ]);

        expect('Saved when full and new', $model->save())->true();
    }
}