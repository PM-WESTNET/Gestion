<?php

use app\modules\sale\models\Company;
use app\tests\fixtures\TaxConditionFixture;
use app\tests\fixtures\PartnerDistributionModelFixture;
use app\tests\fixtures\BillTypeFixture;

class CompanyTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $tax_condition_id;
    protected $partner_distribution_model_id;
    protected $parent_company_id_1;
    protected $parent_company_id_2;

    public function _fixtures()
    {
        return [
            'tax_condition' => [
                'class' => TaxConditionFixture::class
            ],
            'partner_distribution_model' => [
                'class' => PartnerDistributionModelFixture::class
            ]
        ];
    }

    protected function _after()
    {
    }

    public function testInvalidWhenEmptyAndNew()
    {
        $model = new Company();

        $this->assertFalse($model->validate());
    }

    public function testValidWhenFullAndNew()
    {
        $model = new Company();
        $model->name = 'Empresa';
        $model->status = 'enabled';
        $model->tax_identification = '27-258695-3';
        $model->tax_condition_id = 1;
        $model->code = '1';
        $model->partner_distribution_model_id = 1;

        $this->assertTrue($model->validate());
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new Company();

        $this->assertFalse($model->save());
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new Company();
        $model->name = 'Empresa';
        $model->status = 'enabled';
        $model->tax_identification = '27-258695-3';
        $model->tax_condition_id = 1;
        $model->code = '1';
        $model->partner_distribution_model_id = 1;

        $this->assertTrue($model->save());
    }

    public function testGetParentCompanies(){
        $parent_companies = Company::getParentCompanies();

        $find_company_1 = false;
        $find_company_2 = false;
        foreach ($parent_companies as $parent_company) {
            if($parent_company->name == 'Westnet'){
                $find_company_1 = true;
            }
            if($parent_company->name == 'Bigway'){
                $find_company_2 = true;
            }
        }

        $this->assertTrue($find_company_1 && $find_company_2);
    }
}