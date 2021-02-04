<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 12/12/18
 * Time: 12:15
 */

use app\modules\sale\models\CustomerClassHasCustomer;
use app\tests\fixtures\CustomerClassFixture;
use app\tests\fixtures\CustomerFixture;

class CustomerClassHasCustomerTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _fixtures()
    {
       return [

            'customer_class' => [
                'class' => CustomerClassFixture::class
            ],
           'customer' => [
               'class' => CustomerFixture::class
           ],
        ];
    }

    public function testInvalidWhenEmptyAndNew()
    {
        $model = new CustomerClassHasCustomer();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new CustomerClassHasCustomer([
            'customer_class_id' => 1,        //Fixture
            'customer_id' => 45900,     //Fixture
            'date_updated' => '1468903783'
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new CustomerClassHasCustomer();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new CustomerClassHasCustomer([
            'customer_class_id' => 1,        //Fixture
            'customer_id' => 45900,     //Fixture
            'date_updated' => '1468903783'
        ]);

        expect('Saved when full and new', $model->save())->true();
    }
}