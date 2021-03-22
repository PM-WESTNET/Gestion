<?php

/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 01/04/19
 * Time: 13:45
 */

use app\modules\sale\models\CustomerHasHourRange;
use app\tests\fixtures\CustomerFixture;
use app\tests\fixtures\HourRangeFixture;

class CustomerHasHourRangeTest extends \Codeception\Test\Unit
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
            'hour_range' => [
                'class' => HourRangeFixture::class
            ],
        ];
    }

    public function testInvalidWhenNewAndEmpty()
    {
        $model = new CustomerHasHourRange();

        expect('Invalid when new and empty', $model->validate())->false();
    }

    public function testValidWhenNewAndFull()
    {
        $model = new CustomerHasHourRange([
            'customer_id' => 45900,
            'hour_range_id' => 1,
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenNewAndEmpty()
    {
        $model = new CustomerHasHourRange();

        expect('Not save when new and empty', $model->save())->false();
    }

    public function testSaveWhenNewAndFull()
    {
        $model = new CustomerHasHourRange([
            'customer_id' => 45900,
            'hour_range_id' => 1,
        ]);

        expect('Save when full and new', $model->save())->true();
    }
}