<?php namespace models;

use app\modules\automaticdebit\models\BankCompanyConfig;
use app\tests\fixtures\BankFixtures;
use app\tests\fixtures\CompanyFixture;

class BankCompanyConfigTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function _fixtures()
    {
        return [
            'company' => [
                'class' => CompanyFixture::class
            ],
            'bank' => [
                'class' => BankFixtures::class
            ]
        ];
    }

    public function testInvalidWhenNew()
    {
        $model = new BankCompanyConfig();

        expect('failed', $model->save())->false();
    }

    public function testSuccessSave()
    {
        $model = new BankCompanyConfig();

        $model->company_id = 2;
        $model->bank_id = 1;

        expect('Failed', $model->save())->true();
    }
}