<?php

use app\modules\westnet\models\AdsPercentagePerCompany;
use Codeception\Test\Unit;
use app\tests\fixtures\CompanyFixture;

class AdsPercentagePerCompanyTest extends Unit {
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _before()
    {

    }

    public function _after()
    {

    }

    public function _fixtures()
    {
        return [
            'company' => [
                'class' => CompanyFixture::class
            ]
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new AdsPercentagePerCompany();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new AdsPercentagePerCompany([
            'parent_company_id' => 8,
            'company_id' => 1,
            'percentage' => 100
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new AdsPercentagePerCompany();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new AdsPercentagePerCompany([
            'parent_company_id' => 8,
            'company_id' => 1,
            'percentage' => 100
        ]);

        expect('Saved when full and new', $model->save())->true();
    }
}