<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 12/12/18
 * Time: 12:15
 */

use app\modules\sale\models\CustomerClass;

class CustomerClassTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new CustomerClass();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new CustomerClass([
            'name' => 'Basico',
            'code_ext' => 1,
            'is_invoiced' => 1,
            'tolerance_days' => 1,
            'percentage_bill' => 100,
            'days_duration' => 5,
            'colour' => '#3434f1',
            'percentage_tolerance_debt' => 10,
            'status' => 'enabled'
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new CustomerClass();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new CustomerClass([
            'name' => 'Basico',
            'code_ext' => 1,
            'is_invoiced' => 1,
            'tolerance_days' => 1,
            'percentage_bill' => 100,
            'days_duration' => 5,
            'colour' => '#3434f1',
            'percentage_tolerance_debt' => 10,
            'status' => 'enabled'
        ]);

        expect('Saved when full and new', $model->save())->true();
    }
}