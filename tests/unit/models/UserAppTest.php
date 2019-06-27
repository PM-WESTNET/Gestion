<?php

use app\modules\mobileapp\v1\models\UserApp;
use app\tests\fixtures\CustomerFixture;
use app\modules\mobileapp\v1\models\Customer;
use app\modules\mobileapp\v1\models\UserAppHasCustomer;

class UserAppTest extends \Codeception\Test\Unit
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
        ];
    }

    public function testValidWhenFullAndNew()
    {
        $model = new UserApp();

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new UserApp();

        expect('Saved when full and new', $model->save())->true();
    }

    public function testAddCustomer()
    {
        $model = new UserApp([
            'email'=> 'jperez@quoma.com.ar'
        ]);
        $model->save();

        $customer = Customer::findOne(45900);

        expect('Customer added succesfully', $model->addCustomer($customer))->true();
        $uahcu = UserAppHasCustomer::findOne(['customer_code' => $customer->code, 'user_app_id' => $model->user_app_id]);
        expect('User app has customer exists', $uahcu)->isInstanceOf(UserAppHasCustomer::class);

        UserAppHasCustomer::deleteAll();
        expect('Customer added succesfully', $model->addCustomer($customer, true))->true();
        $uahcu = UserAppHasCustomer::findOne(['customer_code' => $customer->code, 'user_app_id' => $model->user_app_id, 'customer_id' => $customer->customer_id]);
        expect('User app has customer exists', $uahcu)->isInstanceOf(UserAppHasCustomer::class);
    }

    //TODO resto de la clase
}