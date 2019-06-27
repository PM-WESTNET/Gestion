<?php

use app\modules\sale\models\Bill;
use app\tests\fixtures\BillTypeFixture;
use app\tests\fixtures\CompanyFixture;
use app\tests\fixtures\PointOfSaleFixture;
use app\modules\sale\components\BillExpert;
use app\tests\fixtures\CompanyHasBillTypeFixture;
use app\tests\fixtures\PartnerDistributionModelFixture;
use app\tests\fixtures\CurrencyFixture;
use app\modules\checkout\models\Payment;
use app\modules\checkout\models\PaymentItem;
use app\tests\fixtures\PaymentMethodFixture;
use app\modules\checkout\models\BillHasPayment;
use app\modules\sale\models\search\BillSearch;
use app\modules\sale\models\InvoiceClass;
use app\tests\fixtures\UnitFixture;
use app\modules\sale\models\BillDetail;
use app\tests\fixtures\ProductFixture;
use app\tests\fixtures\DiscountFixture;
use app\tests\fixtures\TaxRateFixture;
use app\modules\sale\models\ProductHasTaxRate;
use app\tests\fixtures\TaxFixture;
use app\modules\sale\models\Customer;
use app\modules\sale\modules\contract\components\ContractToInvoice;
use app\tests\fixtures\TaxConditionFixture;
use app\tests\fixtures\DocumentTypeFixture;
use app\tests\fixtures\CustomerClassFixture;
use app\modules\sale\modules\contract\models\Contract;
use app\tests\fixtures\AddressFixture;
use app\modules\sale\models\Company;
use app\modules\sale\models\Discount;
use app\modules\sale\models\CustomerHasDiscount;
use app\modules\sale\modules\contract\models\ContractDetail;
use app\tests\fixtures\VendorFixture;
use app\tests\fixtures\ProductPriceFixture;
use app\tests\fixtures\ProductHasTaxRateFixture;

class BillTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _fixtures()
    {
        return [
            'bill_type' => [
                'class' => BillTypeFixture::class,
            ],
            'company' => [
                'class' => CompanyFixture::class
            ],
            'point_of_sale' => [
                'class' => PointOfSaleFixture::class
            ],
            'company_has_bill_type' => [
                'class' => CompanyHasBillTypeFixture::class
            ],
            'partner_distribution_model' => [
                'class' => PartnerDistributionModelFixture::class
            ],
            'currency' => [
                'class' => CurrencyFixture::class
            ],
            'payment_method' => [
                'class' => PaymentMethodFixture::class
            ],
            'unit' => [
                'class' => UnitFixture::class
            ],
            'product' => [
                'class' => ProductFixture::class
            ],
            'product_price' => [
                'class' => ProductPriceFixture::class
            ],
            'discount' => [
                'class' => DiscountFixture::class,
            ],
            'tax_rate' => [
                'class' => TaxRateFixture::class
            ],
            'tax_condition' => [
                'class' => TaxConditionFixture::class
            ],
            'document_type' => [
                'class' => DocumentTypeFixture::class
            ],
            'customer_class' => [
                'class' => CustomerClassFixture::class
            ],
            'address' => [
                'class' => AddressFixture::class
            ],
            'vendor' => [
                'class' => VendorFixture::class
            ],
            'product_has_tax_rate' => [
                'class' => ProductHasTaxRateFixture::class
            ],
        ];
    }


    public function _before()
    {
        Yii::$app->db->createCommand('INSERT INTO `company_has_bill_type` (`company_id`, `bill_type_id`, `default`) VALUES (1, 1, 1)')->execute();
    }

    public function _after()
    {
        Yii::$app->db->createCommand('DELETE FROM`company_has_bill_type` WHERE `company_id` = 1 AND `bill_type_id` = 1 AND `default` = 1')->execute();
    }

    public function testInvalidAndEmpty()
    {
        $model = new Bill();
        expect('Invalid when empty', $model->validate())->false();
    }

    public function testValidAndFull()
    {
        $model = new Bill([
            'bill_type_id' => 1
        ]);

        expect('Valid when empty', $model->validate())->true();
    }

    public function testNotSaveWhenEmpty()
    {
        $model = new Bill();

        expect('Not save when empty', $model->save())->false();
    }

    public function testSaveWhenNew() {
        $model = BillExpert::createBill(1);
        $model->company_id = 1;
        $model->status = 'draft';
        $model->partner_distribution_model_id = 1;

        expect('Save when full', $model->save())->true();
    }

    public function testGetInvoiceClass()
    {
        $model = BillExpert::createBill(1);
        $model->company_id = 1;
        $model->status = 'draft';
        $model->partner_distribution_model_id = 1;
        $model->save();

        expect('Invoice class', $model->getInvoiceClass())->isInstanceOf(InvoiceClass::class);
    }

    public function testAddDetail()
    {
        $model = new Bill();

        expect('Add detail fails with new record', $model->addDetail([]))->false();

        $model = BillExpert::createBill(1);
        $model->company_id = 1;
        $model->status = 'closed';
        $model->partner_distribution_model_id = 1;
        $model->save();

        expect('Add detail fails when status isnt draft', $model->addDetail([]))->false();

        $model->status = 'draft';

        $this->tester->expectException(yii\web\HttpException::class, function() use ($model){
            echo 'Add detail fails when detail is empty';
            $model->addDetail([]);
        });

        $detail = [
            'concept' => 'xxx',
            'unit_final_price' => 100,
            'unit_id' => 1
        ];

        expect('Add detail with minimal detail', $model->addDetail($detail))->isInstanceOf(BillDetail::class);

        BillDetail::deleteAll();

        $detail = [
            'concept' => 'xxx',
            'unit_final_price' => 100,
            'unit_id' => 1,
            'product_id' => 1
        ];

        expect('Add detail with product_id', $model->addDetail($detail))->isInstanceOf(BillDetail::class);
    }

    public function testGetNumberFromPointOfSale()
    {
        $model = BillExpert::createBill(1);
        $model->company_id = 1;
        $model->status = 'closed';
        $model->partner_distribution_model_id = 1;
        $model->save();

        expect('Point of sale', $model->getNumberFromPointOfSale());
    }

    public function testBillSearchWithFromDateAndPaymentMethodsFilters()
    {
        $search = new BillSearch();

        $params = [
            'BillSearch' => [
                'fromDate' => (new \DateTime('now'))->format('Y-m-d'),
                'payment_methods' => [
                    2
                ]
            ]
        ];

        expect('No bills with payment_method', $search->search($params)->getTotalCount())->equals(0);

        $model = BillExpert::createBill(1);
        $model->company_id = 1;
        $model->status = 'draft';
        $model->partner_distribution_model_id = 1;
        $model->save();

        expect('No bills with payment_method', $search->search($params)->getTotalCount())->equals(0);

        $payment = new Payment([
            'amount' => 500,
            'status' => Payment::PAYMENT_CLOSED,
            'date' => (new \DateTime('now'))->format('Y-m-d'),
            'company_id' => 1,
            'partner_distribution_model_id' => 1
        ]);
        $payment->save();

        $payment_item = new PaymentItem([
            'payment_id' => $payment->payment_id,
            'payment_method_id' => 1,
            'amount' => 300
        ]);
        $payment_item->save();

        $bill_has_payment = new BillHasPayment([
            'bill_id' => $model->bill_id,
            'payment_id' => $payment->payment_id,
            'amount' => 300
        ]);
        $bill_has_payment->save();

        $params = [
            'BillSearch' => [
                'fromDate' => (new \DateTime('now'))->format('Y-m-d'),
                'payment_methods' => [
                    1
                ]
            ]
        ];

        expect('No bills with payment_method', $search->search($params)->getTotalCount())->equals(1);
    }

    public function testUpdateEinAndEinExpiration() {
        $ein = 123;
        $ein_expiration = (new \DateTime('now'))->format('y-m-d');

        $model = BillExpert::createBill(1);
        $model->company_id = 1;
        $model->status = 'draft';
        $model->partner_distribution_model_id = 1;
        $model->save();

        expect('Cant update ein if status is draft', $model->updateEinAndEinExpiration($ein, $ein_expiration))->false();
        $model->status = 'closed';

        expect('Can update ein if status is closed and ein is null', $model->updateEinAndEinExpiration($ein, $ein_expiration))->true();
        expect('Ein is correct', $model->ein)->equals(123);
        expect('Ein expiration is correct', $model->ein_expiration)->equals((new \DateTime('now'))->format('y-m-d'));
        expect('Cant update ein if status is closed and ein is not null', $model->updateEinAndEinExpiration($ein, $ein_expiration))->false();
    }

    public function testGetTaxesAppliedWithDiscount()
    {
        $model = BillExpert::createBill(1);
        $model->company_id = 1;
        $model->status = 'draft';
        $model->partner_distribution_model_id = 1;
        $model->save();

        //Detalle con producto
        $detail = [
            'concept' => 'xxx',
            'unit_final_price' => 1227,
            'unit_id' => 1,
            'product_id' => 1,
        ];
        $detail = $model->addDetail($detail);
        $detail->updateAttributes([
            'line_total' =>  1227,
            'line_subtotal' => 1014.05
        ]);

        $product_has_tax_rate = new ProductHasTaxRate([
            'product_id' => 1,
            'tax_rate_id' => 1
        ]);
        $product_has_tax_rate->save();

        $taxesApplied = $model->getTaxesApplied();
        expect('Amount is correct', $taxesApplied[1]['amount'])->equals(212.95);
        expect('Base is correct', $taxesApplied[1]['base'])->equals(1014.05);

        //Descuento
        $detail_discount = [
            'concept' => 'xxx',
            'line_total' => 191,
            'unit_id' => 1,
            'discount_id' => 1,
            'unit_net_discount' => 191
        ];
        $detail_discount = $model->adddetail($detail_discount);
        $detail_discount->updateAttributes([
            'line_total' =>  0,
            'line_subtotal' => 191
        ]);

        $taxesApplied = $model->getTaxesApplied();
        expect('Amount is correct', $taxesApplied[1]['amount'])->equals(179.8);
        expect('Base is correct', $taxesApplied[1]['base'])->equals(856.2);
        expect('Amount is correct', $taxesApplied[5]['amount'])->equals(-191);
        expect('Base is correct', $taxesApplied[5]['base'])->equals(191);

        //Detalle manual
        $manual_detail = [
            'concept' => 'xxx',
            'unit_final_price' => 120,
            'unit_id' => 1
        ];
        $manual_detail = $model->addDetail($manual_detail);
        $manual_detail->updateAttributes([
            'line_total' =>  119,
            'line_subtotal' => 98.35
        ]);

        $taxesApplied = $model->getTaxesApplied();
        expect('Amount is correct', $taxesApplied[1]['amount'])->equals(196.38);
        expect('Base is correct', $taxesApplied[1]['base'])->equals(935.12);
        expect('Amount is correct', $taxesApplied[5]['amount'])->equals(-191);
        expect('Base is correct', $taxesApplied[5]['base'])->equals(191);
    }

    public function testVerifyAmounts()
    {
        $model = BillExpert::createBill(1);
        $model->company_id = 1;
        $model->status = 'draft';
        $model->partner_distribution_model_id = 1;
        $model->save();

        expect('Verify detect empty amounts', $model->verifyAmounts())->false();

        //Detalle manual
        $detail = [
            'concept' => 'xxx',
            'unit_net_price' => 100,
            'unit_final_price' => 121,
            'unit_id' => 1,
        ];
        $detail = $model->addDetail($detail);
        $detail->updateAttributes([
            'line_total' =>  121,
            'line_subtotal' => 100
        ]);

        $model->close();

        $model->updateAttributes(['total' => 200]);

        expect('Verify detect bad total', $model->verifyAmounts())->false();
        expect('Verify detect bad total', $model->verifyAmounts(true))->false();
        expect('Verify update total correctly', $model->total)->equals(121);

        $model->updateAttributes(['amount' => 0]);
        expect('Verify detect bad amount', $model->verifyAmounts())->false();
        expect('Verify detect bad amount', $model->verifyAmounts(true))->false();
        expect('Verify update amount correctly', $model->amount)->equals(100);
    }

    public function testBillWithDiscountPorRecomendado()
    {
        $model = new Customer([
            'name' => 'Nombre',
            'lastname' => 'Apellido',
            'tax_condition_id' => 3,
            'publicity_shape' => 'web',
            'document_number' => '29918157',
            'document_type_id' => 2,
            'customerClass' => 1,
            'company_id' => 2,
            'status' => Customer::STATUS_ENABLED,
            '_notifications_way' => [Customer::getNotificationWays()],
        ]);
        $model->save();

        $contract = new Contract([
            'customer_id' => $model->customer_id,
            'date' => (new \DateTime('now'))->modify('-1 month')->format('d-m-Y'),
            'from_date' => (new \DateTime('now'))->modify('-1 month')->format('d-m-Y'),
            'to_date' => (new \DateTime('now'))->modify('+1 year')->format('d-m-Y'),
            'status' => Contract::STATUS_ACTIVE,
            'address_id' => 1,
            'description' => 'Descripción del contrato',
        ]);
        $contract->save();

        $contract_detail = new ContractDetail([
            'contract_id' => $contract->contract_id,
            'product_id' => 4,
            'from_date' => (new \DateTime('now'))->modify('-1 month')->format('d-m-Y'),
            'to_date' =>(new \DateTime('now'))->modify('+1 year')->format('d-m-Y'),
            'status' => ContractDetail::STATUS_ACTIVE,
            'date' => (new \DateTime('now'))->modify('-1 month')->format('d-m-Y'),
            'count' => 1,
            'applied' => 1,
            'vendor_id' => 1
        ]);
        $contract_detail->save();

        $discount = new Discount([
            'name' => '50 % BONIFICACION POR RECOMENDADO',
            'status' => Discount::STATUS_ENABLED,
            'type' => Discount::TYPE_PERCENTAGE,
            'value' => 50,
            'from_date' => (new \DateTime('now'))->modify('-1 month')->format('d-m-Y'),
            'to_date' => (new \DateTime('now'))->modify('+1 year')->format('d-m-Y'),
            'periods' => 1,
            'product_id' => null,
            'apply_to' => Discount::APPLY_TO_PRODUCT,
            'value_from' => Discount::VALUE_FROM_PLAN,
            'referenced' => 1
        ]);
        $discount->save();

        $customer_has_discount = new CustomerHasDiscount([
            'customer_id' => $model->customer_id,
            'discount_id' => $discount->discount_id,
            'from_date' => (new \DateTime('now'))->modify('-1 days')->format('d-m-Y'),
            'status' => CustomerHasDiscount::STATUS_ENABLED,
        ]);
        $customer_has_discount->save();

        expect('No bills before', count(Bill::find()->all()))->equals(0);

        $company = Company::findOne(2);
        $cti = new ContractToInvoice();
        $cti->invoice($company, 2, $model->customer_id, (new \DateTime('now')), true, 'observación del comprobante');

        $generated_bill = Bill::find()->one();

        expect('One bills after', count(Bill::find()->all()))->equals(1);
        expect('Amount is not empty', $generated_bill->amount)->notEmpty();
        expect('Taxes is not empty', $generated_bill->taxes)->notEmpty();
        expect('Total is not empty', $generated_bill->total)->notEmpty();
        expect('Amount + Taxes = Total', $generated_bill->amount + $generated_bill->taxes)->equals($generated_bill->total);
        expect('Details quantity is one', count($generated_bill->billDetails))->equals(1);
        expect('Total is 643.5', $generated_bill->total)->equals(649.5);
        expect('Amount is 536.78', $generated_bill->amount)->equals(536.78);
        expect('Taxes is 112.72', $generated_bill->taxes)->equals(112.72);
    }

    //TODO aplicar descuentos con todos los escenarios posibles.
}