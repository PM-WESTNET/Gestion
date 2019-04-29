<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 12/12/18
 * Time: 12:15
 */

use app\modules\provider\models\ProviderBill;
use app\tests\fixtures\BillTypeFixture;
use app\modules\provider\models\Provider;
use app\tests\fixtures\TaxConditionFixture;
use app\tests\fixtures\TaxRateFixture;
use app\tests\fixtures\ProviderFixture;
use \app\modules\provider\models\ProviderBillHasTaxRate;
use app\tests\fixtures\CompanyFixture;
use app\modules\provider\models\ProviderBillItem;
use app\modules\provider\models\ProviderPayment;
use app\tests\fixtures\PartnerDistributionModelFixture;
use app\modules\provider\models\ProviderBillHasProviderPayment;

class ProviderBillTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $model;
    protected $item;
    protected $item2;
    protected $provider_bill_has_tax_rate_id;
    protected $payment_id;
    protected $provider_id;
    protected $provider_bill_has_provider_payment_id;

    public function _before()
    {
        $this->model = $this->tester->haveRecord(ProviderBill::class,
            [
                'date' => (new \DateTime('now'))->format('Y-m-d'),
                'bill_type_id' => 1,        //Fixture
                'provider_id' => 150,
                'company_id' => 1,       //Fixture
                'status' => ProviderBill::STATUS_DRAFT,
                'number' => 0001-00000045,
            ]);

        $this->item = $this->tester->haveRecord(ProviderBillItem::class, [
            'provider_bill_id' => $this->model,
            'amount' => 250
        ]);

        $this->item2 = $this->tester->haveRecord(ProviderBillItem::class, [
            'provider_bill_id' => $this->model,
            'amount' => 300
        ]);

        $this->provider_bill_has_tax_rate_id = $this->tester->haveRecord(ProviderBillHasTaxRate::class, [
            'provider_bill_id' => $this->model,
            'tax_rate_id' => 1,      //Fixture
            'amount' => 123
        ]);

        $this->provider_id = $this->tester->haveRecord(Provider::class, [
            'name' => 'Provider',
            'bill_type' => Provider::getAllBillTypes()['A'],
            'tax_condition_id' => 3,        //Fixture
            'tax_identification' => '124567898'
        ]);

        $this->payment_id = $this->tester->haveRecord(ProviderPayment::class, [
            "date" => "2018-11-30",
            "amount" => "500",
            "description" => "",
            "timestamp" => null,
            "balance" => null,
            "provider_id" => $this->provider_id,
            "company_id" => 1,      //Fixture
            "status" => "created",
            "partner_distribution_model_id" => 1        //Fixture
        ]);

        $this->provider_bill_has_provider_payment_id = $this->tester->haveRecord(ProviderBillHasProviderPayment::class, [
            'provider_bill_id' => $this->model,
            'provider_payment_id' => $this->payment_id,
            'amount' => '500'
        ]);
    }

    public function _fixtures(){
        return [
            'bill_type_id' => [
                'class' => BillTypeFixture::class
            ],
            'tax_condition' => [
                'class' => TaxConditionFixture::class
            ],
            'tax_rate' => [
                'class' => TaxRateFixture::class
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
        $model = new ProviderBill();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new ProviderBill([
            'date' => (new \DateTime('now'))->format('Y-m-d'),
            'bill_type_id' => 1,        //Fixture
            'provider_id' => $this->provider_id
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new ProviderBill();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $provider_id = $this->tester->haveRecord(Provider::class, [
            'name' => 'Provider',
            'bill_type' => Provider::getAllBillTypes()['A'],
            'tax_condition_id' => 3,        //Fixture
            'tax_identification' => '124567898'
        ]);

        $model = new ProviderBill([
            'date' => (new \DateTime('now'))->format('Y-m-d'),
            'bill_type_id' => 1,        //Fixture
            'provider_id' => $provider_id,
            'company_id' => 1       //Fixture
        ]);

        expect('Saved when full and new', $model->save())->true();
    }

    public function testAddTax()
    {
        $model = new ProviderBill([
            'date' => (new \DateTime('now'))->format('Y-m-d'),
            'bill_type_id' => 1,        //Fixture
            'provider_id' => 149,
            'company_id' => 1       //Fixture
        ]);
        $model->save();

        $provider_bill_has_tax_rate = $model->addTax([
            'provider_bill_id' => $model->provider_bill_id,
            'tax_rate_id' => 1,      //Fixture
            'amount' => 123
        ]);

        $this->assertInstanceOf(ProviderBillHasTaxRate::class, $provider_bill_has_tax_rate);
        expect('Provider bill has tax rate exists', ProviderBillHasTaxRate::find()->where([
            'provider_bill_id' => $model->provider_bill_id,
            'tax_rate_id' => 1,
            'amount' => 123
        ])->one())->notEmpty();
    }

    public function testAddItem()
    {
        $model = new ProviderBill([
            'date' => (new \DateTime('now'))->format('Y-m-d'),
            'bill_type_id' => 1,        //Fixture
            'provider_id' => 149,
            'company_id' => 1       //Fixture
        ]);
        $model->save();

        $item = new ProviderBillItem([
            'provider_bill_id' => $model->provider_bill_id,
        ]);
        $model->addItem($item);

        $this->assertInstanceOf(ProviderBillItem::class, $item);
        expect('Provider bill has tax rate exists', ProviderBillItem::find()->where([
            'provider_bill_id' => $model->provider_bill_id,
        ])->one())->notEmpty();
    }

    public function testCalculateTotal()
    {
        $model = ProviderBill::findOne($this->model);
        $item = ProviderBillItem::findOne($this->item);
        $item2 = ProviderBillItem::findOne($this->item2);
        $model->addItem($item);
        $model->addItem($item2);

        expect('Calculate total', $model->calculateTotal())->equals('673');
    }

    public function testCalculateTaxes()
    {
        $model = ProviderBill::findOne($this->model);
        $tax = ProviderBillHasTaxRate::findOne($this->provider_bill_has_tax_rate_id);
        $model->addTax($tax);

        expect('Calculate tax rate', $model->calculateTaxes())->equals('123');
    }

    public function testCalculatePayment()
    {
        $model = ProviderBill::findOne($this->model);
        $payment = ProviderPayment::findOne($this->payment_id);

        expect('Calculate Payment', $model->calculatePayment())->equals('500');
    }

    public function testCalculateItems()
    {
        $model = ProviderBill::findOne($this->model);
        $item = ProviderBillItem::findOne($this->item);
        $item2 = ProviderBillItem::findOne($this->item2);
        $model->addItem($item);
        $model->addItem($item2);

        expect('Calculate Items', $model->calculateItems())->equals('550');
    }

    //TODO
    public function testGetConfig(){}

    //TODO
    public function testGetAmounts(){}

    public function testClose()
    {
        $model = ProviderBill::findOne($this->model);
        $item = ProviderBillItem::findOne($this->item);
        $item2 = ProviderBillItem::findOne($this->item2);
        $model->addItem($item);
        $model->addItem($item2);

        expect('Close', $model->close())->true();
        expect('Provider bill has closed status', $model->status)->equals(ProviderBill::STATUS_CLOSED);
    }

}