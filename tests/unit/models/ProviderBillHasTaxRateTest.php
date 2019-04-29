<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 12/12/18
 * Time: 12:15
 */

use app\modules\provider\models\ProviderBill;
use app\tests\fixtures\BillTypeFixture;
use app\tests\fixtures\ProviderFixture;
use \app\modules\provider\models\ProviderBillHasTaxRate;
use app\tests\fixtures\CompanyFixture;
use app\tests\fixtures\TaxRateFixture;

class ProviderBillHasTaxRateTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $provider_id;

    public function _before()
    {
        $this->provider_bill_id = $this->tester->haveRecord(ProviderBill::class,
            [
                'date' => (new \DateTime('now'))->format('Y-m-d'),
                'bill_type_id' => 1,        //Fixture
                'provider_id' => 150,
                'company_id' => 1,       //Fixture
                'status' => ProviderBill::STATUS_DRAFT,
                'number' => 0001-00000045,
            ]);
    }

    public function _fixtures(){
        return [
            'bill_type_id' => [
                'class' => BillTypeFixture::class
            ],
            'provider' => [
                'class' => ProviderFixture::class
            ],
            'company' => [
                'class' => CompanyFixture::class
            ],
            'tax_rate' => [
                'class' => TaxRateFixture::class
            ]
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new ProviderBillHasTaxRate();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new ProviderBillHasTaxRate([
            'provider_bill_id'  => $this->provider_bill_id,
            'tax_rate_id' => 1,
            'amount' => '300'
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new ProviderBillHasTaxRate();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new ProviderBillHasTaxRate([
            'provider_bill_id'  => $this->provider_bill_id,
            'tax_rate_id' => 1,
            'amount' => '300'
        ]);

        expect('Saved when full and new', $model->save())->true();
    }
}