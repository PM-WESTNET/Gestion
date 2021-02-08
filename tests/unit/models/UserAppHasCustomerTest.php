<?php

use app\modules\mobileapp\v1\models\UserAppHasCustomer;
use app\tests\fixtures\CustomerFixture;
use app\modules\mobileapp\v1\models\UserAppHasCustomerHasCustomer;
use app\tests\fixtures\UserAppFixture;

class UserAppHasCustomerTest extends \Codeception\Test\Unit
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
            'customer' => [
                'class' => CustomerFixture::class
            ],
            'user_app' => [
                'class' => UserAppFixture::class
            ]
        ];
    }

    public function testInvalidWhenEmptyAndNew()
    {
        $model = new UserAppHasCustomer();

        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new UserAppHasCustomer([
            'user_app_id' => 1,
            'customer_id' => 45900
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new UserAppHasCustomer();

        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new UserAppHasCustomer([
            'user_app_id' => 1,
            'customer_id' => 45900
        ]);

        expect('Saved when full and new', $model->save())->true();
    }
}