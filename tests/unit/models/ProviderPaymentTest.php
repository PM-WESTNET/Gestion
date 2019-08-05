<?php

use \app\modules\provider\models\ProviderPayment;
use \app\tests\fixtures\ProviderFixture;
use \app\tests\fixtures\CompanyFixture;
use \app\tests\fixtures\PartnerDistributionModelFixture;
use \app\tests\fixtures\ProviderPaymentFixture;
use app\tests\fixtures\ProviderBillFixture;
use app\tests\fixtures\ProviderPaymentItemFixture;
use \app\modules\provider\models\ProviderBill;
use app\modules\provider\models\ProviderPaymentItem;
use app\modules\provider\models\ProviderBillHasProviderPayment;
use \app\tests\fixtures\MoneyBoxAccountFixture;
use app\modules\checkout\models\PaymentMethod;
use app\tests\fixtures\BillTypeFixture;
use app\tests\fixtures\PaymentMethodFixture;

class ProviderPaymentTest extends  \Codeception\Test\Unit
{

    /**
     * @var UnitTester
     */
    protected $tester;

    protected $model;
    protected $provider_bill;
    protected $relation;
    protected $provider_payment_item_1;
    protected $provider_payment_item_2;
    protected $provider_bill_has_provider_payment_1;
    protected $provider_bill_has_provider_payment_2;

    protected function _before()
    {
        $money_box_account = $this->tester->grabFixture('money_box_account', 0);
        $this->model = $this->tester->haveRecord(ProviderPayment::class,
            [
                "date" => "2018-11-30",
                "amount" => "500",
                "description" => "",
                "timestamp" => null,
                "balance" => null,
                "provider_id" => 149,
                "company_id" => 1,
                "status" => "created",
                "partner_distribution_model_id" => 1
            ]);

        $this->provider_bill = $this->tester->haveRecord(ProviderBill::class,
            [
                "date" => "2018-11-06",
                "type" => null,
                "number" => "0004-00024824",
                "net" => "0",
                "taxes" => "0",
                "total" => "0",
                "provider_id" => 149,
                "description" => "",
                "timestamp" => "1541597745",
                "balance" => "0",
                "bill_type_id" => 1,
                "status" => "closed",
                "company_id" => 2,
                "partner_distribution_model_id" => 2,
                "created_at" => "1541597745",
                "updated_at" => "1541598434",
                "creator_user_id" => 227,
                "updater_user_id" => 227
            ]);

        $this->provider_payment_item_1 = $this->tester->haveRecord(ProviderPaymentItem::class,
            [
                "provider_payment_id" => $this->model,
                "description" => "",
                "number" => "",
                "amount" => "300",
                "payment_method_id" => 4,
                "paycheck_id" => null,
                "money_box_account_id" => $money_box_account->money_box_account_id
            ]);

       $this->provider_payment_item_2 = $this->tester->haveRecord(ProviderPaymentItem::class,
            [
                "provider_payment_id" => $this->model,
                "description" => "",
                "number" => "",
                "amount" => "200",
                "payment_method_id" => 4,
                "paycheck_id" => null,
                "money_box_account_id" => $money_box_account->money_box_account_id
            ]);

        $this->relation = $this->tester->haveRecord(ProviderBillHasProviderPayment::class,
            [
                'provider_bill_id' => $this->provider_bill,
                'provider_payment_id' => $this->model,
                'amount' => '500'
            ]);
    }

    protected function _after()
    {
    }

    public function _fixtures()
    {
        return [
            'provider' => [
                'class' => ProviderFixture::class
            ],
            'company' => [
                'class' => CompanyFixture::class
            ],
            'partner_distribution_model' => [
                'class' => PartnerDistributionModelFixture::class
            ],
            'provider_payment' => [
                'class' => ProviderPaymentFixture::class,
            ],
            'provider_bill' => [
                'class' => ProviderBillFixture::class
            ],
            'provider_payment_item' => [
                'class' => ProviderPaymentItemFixture::class
            ],
            'money_box_account' => [
               'class' => MoneyBoxAccountFixture::class
            ],
            'bill_type' => [
                'class' => BillTypeFixture::class
            ],
            'payment_method' => [
                'class' => PaymentMethodFixture::class
            ]
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new ProviderPayment();
        $this->assertFalse($model->validate());
    }

    public function testValidWhenFullAndNew()
    {
        $model = new ProviderPayment();
        $model->provider_id = 149;

        $this->assertTrue($model->validate());
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new ProviderPayment();
        $this->assertFalse($model->save());
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new ProviderPayment();
        $model->provider_id = 149;
        $model->company_id = 1;
        $model->partner_distribution_model_id = 1;
        $this->assertTrue($model->save());
    }

    //Test function addBill()
    /**
     * Add Bill with param $bill['provider_bill_id'] null
     */
    public function testAddBillWithProviderBillIdNull()
    {
        $model = $this->tester->grabFixture('provider_payment', 1);
        $bill = ['provider_bill_id' => ''];
        $result = $model->addBill($bill);

        $this->assertNotEmpty($result);
        $this->assertTrue($result->isNewRecord);
        $this->assertInstanceOf(\app\modules\provider\models\ProviderBillHasProviderPayment::class, $result);
    }

    public function testAddBillWithProviderBillIdAndPayNotExists()
    {
        $model = $this->tester->grabFixture('provider_payment', 1);
        $provider_bill = $this->tester->grabFixture('provider_bill', 1);
        $provider_payment_id = $this->tester->grabFixture('provider_payment', 1);
        $provider_bill = ['provider_bill_id' => $provider_bill->provider_bill_id, 'provider_payment_id' => $provider_payment_id ];
        $result = $model->addBill($provider_bill);

        $this->assertNotEmpty($result);
        $this->assertInstanceOf(\app\modules\provider\models\ProviderBillHasProviderPayment::class, $result);
    }

    public function testAddBillWithProviderBillIdAndPayExists()
    {
        $model = $this->tester->grabFixture('provider_payment', 1);
        $provider_bill = ['provider_bill_id' => 0];
        $result = $model->addBill($provider_bill);

        $this->assertNotEmpty($result);
        $this->assertTrue($result->isNewRecord);
        $this->assertInstanceOf(\app\modules\provider\models\ProviderBillHasProviderPayment::class, $result);
    }

    //test function AddItem
    public function testAddItemFunctionWithEmptyProviderPaymentId()
    {
        $model = $this->tester->grabFixture('provider_payment', 1);
        $payment_item = ['provider_payment_item_id' => ''];
        $new_item = $model->addItem($payment_item);

        $this->assertNotEmpty($new_item);
        $this->assertInstanceOf(\app\modules\provider\models\ProviderPaymentItem::class, $new_item);
    }

    public function testAddItemFunctionWithExistentProviderPaymentId()
    {
        $model = $this->tester->grabFixture('provider_payment', 1);
        $provider_payment_item = $this->tester->grabFixture('provider_payment_item', 1);
        $payment_item = ['provider_payment_item_id' => $provider_payment_item->provider_payment_item_id];
        $new_item = $model->addItem($payment_item);

        $this->assertNotEmpty($new_item);
        $this->assertInstanceOf(\app\modules\provider\models\ProviderPaymentItem::class, $new_item);
    }

    //Test function calculateTotal();
    public function testCalculateTotal()
    {
        $provider_payment = ProviderPayment::findOne($this->model);
        $provider_payment->calculateTotal();

        $this->assertTrue($provider_payment->amount == 500);
    }

    public function testCalculateTotalPayed(){

        $provider_payment = ProviderPayment::findOne($this->model);
        $provider_payment->calculateTotalPayed();

        \Codeception\Util\Debug::debug($provider_payment->amount);

        $this->assertTrue($provider_payment->amount == 500);
    }

    public function testGetConfig()
    {
        $payment_method_1 = $this->tester->haveRecord(PaymentMethod::class,
            [
                'name' => 'PM Contado',
                'status' => 'enabled',
                'register_number' => '1',
                'type' => 'exchanging'
            ]);

        $payment_method_2 = $this->tester->haveRecord(PaymentMethod::class,
            [
                'name' => 'PM Cheque',
                'status' => 'enabled',
                'register_number' => '2',
                'type' => 'exchanging'
            ]);
        $payment_method_ = $this->tester->haveRecord(PaymentMethod::class,
            [
                'name' => 'PM Transferencia',
                'status' => 'disabled',
                'register_number' => '0',
                'type' => 'exchanging'
            ]);

        $provider_payment = ProviderPayment::findOne($this->model);
        $payment_methods = $provider_payment->getConfig();

        $contado = false;
        $cheque = false;
        $transferencia  = false;

        foreach ($payment_methods as $pm){
            if($pm == 'PM Contado'){
                $contado = true;
            }
            if($pm == 'PM Cheque'){
                $cheque = true;
            }
            if($pm == 'PM Transferencia'){
                $transferencia = true;
            }
        }
        $this->assertTrue($contado);
        $this->assertTrue($cheque);
        $this->assertTrue($transferencia);

    }

    public function testGetAmounts()
    {
        $provider_payment = ProviderPayment::findOne($this->model);
        $amounts = $provider_payment->amounts;

        $this->assertEquals(500, $amounts['total'][0] , 500);
    }

    public function testCanClose()
    {
        $provider_payment = ProviderPayment::findOne($this->model);
        $this->assertTrue($provider_payment->canClose());

        $provider_payment->status = 'conciled';
        $this->assertFalse($provider_payment->canClose());

        $provider_payment->status = 'closed';
        $this->assertFalse($provider_payment->canClose());
    }

    public function testVerifyItems()
    {
        $provider_payment = ProviderPayment::findOne($this->model);

        $this->assertTrue($provider_payment->verifyItems());
    }

    public function testAssociateProviderBills()
    {
        $count_pbhpp = count(ProviderBillHasProviderPayment::find()->all());
        $model = ProviderPayment::findOne(59726);
        $result = $model->associateProviderBills([51819]);

        expect('Associate provider bill result is true', $result)->true();
        expect('ProviderBillHasProviderPayment has been created', count(ProviderBillHasProviderPayment::find()->all()))->equals($count_pbhpp +1);
    }

    public function testDisassociateProviderBills()
    {
        $count_pbhpp = count(ProviderBillHasProviderPayment::find()->all());
        $model = ProviderPayment::findOne(59726);
        $result = $model->associateProviderBills([51819]);

        expect('Associate provider bill result is true', $result)->true();
        expect('ProviderBillHasProviderPayment has been created', count(ProviderBillHasProviderPayment::find()->all()))->equals($count_pbhpp +1);

        $result = $model->disassociateProviderBills([51819]);

        expect('Disassociate provider bill result is true', $result)->true();
        expect('ProviderBillHasProviderPayment has been deleted', count(ProviderBillHasProviderPayment::find()->all()))->equals($count_pbhpp);
    }
}