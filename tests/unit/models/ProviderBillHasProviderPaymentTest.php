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
use app\tests\fixtures\CompanyFixture;
use app\modules\provider\models\ProviderPayment;
use app\tests\fixtures\PartnerDistributionModelFixture;
use app\modules\provider\models\ProviderBillHasProviderPayment;

class ProviderBillHasProviderPaymentTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $provider_bill_id;
    protected $payment_id;

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

        $this->payment_id = $this->tester->haveRecord(ProviderPayment::class, [
            "date" => "2018-11-30",
            "amount" => "500",
            "description" => "",
            "timestamp" => null,
            "balance" => null,
            "provider_id" => 150,
            "company_id" => 1,      //Fixture
            "status" => "created",
            "partner_distribution_model_id" => 1        //Fixture
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
            'partner_distribution_model' => [
                'class' => PartnerDistributionModelFixture::class
            ]
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new ProviderBillHasProviderPayment();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new ProviderBillHasProviderPayment([
            'provider_bill_id' => $this->provider_bill_id,
            'provider_payment_id' => $this->payment_id
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new ProviderBillHasProviderPayment();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new ProviderBillHasProviderPayment([
            'provider_bill_id' => $this->provider_bill_id,
            'provider_payment_id' => $this->payment_id
        ]);

        expect('Saved when full and new', $model->save())->true();
    }
}