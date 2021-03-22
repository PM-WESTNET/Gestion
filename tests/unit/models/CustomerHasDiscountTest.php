<?php

/**
 * Created by PhpStorm.
 * User: dexterlab10
 * Date: 03/07/19
 * Time: 15:46
 */

use app\tests\fixtures\CustomerFixture;
use app\modules\sale\models\CustomerHasDiscount;
use app\tests\fixtures\DiscountFixture;

class CustomerHasDiscountTest extends \Codeception\Test\Unit
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
            'discount' => [
                'class' => DiscountFixture::class
            ],
        ];
    }

    public function testInvalidWhenNewAndEmpty()
    {
        $model = new CustomerHasDiscount();

        expect('Invalid when new and empty', $model->validate())->false();
    }

    public function testValidWhenNewAndFull()
    {
        $model = new CustomerHasDiscount([
            'customer_id' => 45900,
            'discount_id' => 1,
            'status' => CustomerHasDiscount::STATUS_ENABLED
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenNewAndEmpty()
    {
        $model = new CustomerHasDiscount();

        expect('Not save when new and empty', $model->save())->false();
    }

    public function testSaveWhenNewAndFull()
    {
        $model = new CustomerHasDiscount([
            'customer_id' => 45900,
            'discount_id' => 1,
            'status' => CustomerHasDiscount::STATUS_ENABLED,
            'from_date' => (new \DateTime('now'))->format('d-m-Y')
        ]);

        expect('Save when full and new', $model->save())->true();
    }

    public function testCanAddDiscount()
    {
        $model = new CustomerHasDiscount([
            'customer_id' => 45900,
            'discount_id' => 1,
            'status' => CustomerHasDiscount::STATUS_ENABLED,
            'from_date' => (new \DateTime('now'))->format('d-m-Y')
        ]);
        $model->save();

        expect('Cant add discount', $model->canAddDiscount())->false();

        $model->updateAttributes(['status' => CustomerHasDiscount::STATUS_DISABLED]);

        expect('Can add discount', $model->canAddDiscount())->true();
    }
}