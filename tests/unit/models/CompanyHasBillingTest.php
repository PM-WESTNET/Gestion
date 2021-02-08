<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 12/12/18
 * Time: 12:15
 */

use app\tests\fixtures\BillTypeFixture;
use app\tests\fixtures\CompanyFixture;
use app\modules\sale\models\CompanyHasBilling;

class CompanyHasBillingTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _before(){}

    public function _fixtures(){
        return [
            'company' => [
                'class' => CompanyFixture::class
            ],+
            'bill_type' => [
                'class' => BillTypeFixture::class
            ]
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new CompanyHasBilling();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new CompanyHasBilling([
            'parent_company_id' => 1,
            'company_id' => 8,
            'bill_type_id' => 1
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new CompanyHasBilling();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new CompanyHasBilling([
            'parent_company_id' => 1,
            'company_id' => 8,
            'bill_type_id' => 1
        ]);

        expect('Saved when full and new', $model->save())->true();
    }
}