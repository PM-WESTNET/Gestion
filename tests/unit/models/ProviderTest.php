<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 12/12/18
 * Time: 12:15
 */

use app\modules\provider\models\Provider;
use app\tests\fixtures\TaxConditionFixture;

class ProviderTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _fixtures(){
        return [
            'tax_condition' => [
                'class' => TaxConditionFixture::class
            ],
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new Provider();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new Provider([
            'name' => 'Provider',
            'bill_type' => Provider::getAllBillTypes()['A'],
            'tax_condition_id' => 3,        //Fixture
             'tax_identification' => '124567898'
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new Provider();
        expect('Invalid when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new Provider([
            'name' => 'Provider',
            'bill_type' => Provider::getAllBillTypes()['A'],
            'tax_condition_id' => 3,        //Fixture
            'tax_identification' => '124567898'
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }
}