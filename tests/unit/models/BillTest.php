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

class BillTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _fixtures()
    {
        return [
            BillTypeFixture::class,
            CompanyFixture::class,
            PointOfSaleFixture::class,
            CompanyHasBillTypeFixture::class,
            PartnerDistributionModelFixture::class,
            CurrencyFixture::class,
            PaymentMethodFixture::class,
            UnitFixture::class,
            ProductFixture::class,
            DiscountFixture::class,
            TaxRateFixture::class,
        ];
    }


//    public function _before()
//    {
//        Yii::$app->db->createCommand('INSERT INTO `company_has_bill_type` (`company_id`, `bill_type_id`, `default`) VALUES (1, 1, 1)')->execute();
//    }
//
//    public function _after()
//    {
//        Yii::$app->db->createCommand('DELETE FROM`company_has_bill_type` WHERE `company_id` = 1 AND `bill_type_id` = 1 AND `default` = 1')->execute();
//    }

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
        $ein_expiration = (new \DateTime('now'))->format('Y-m-d');

        $model = BillExpert::createBill(1);
        $model->company_id = 1;
        $model->status = 'draft';
        $model->partner_distribution_model_id = 1;
        $model->save();

        expect('Cant update ein if status is draft', $model->updateEinAndEinExpiration($ein, $ein_expiration))->false();
        $model->status = 'closed';

        expect('Can update ein if status is closed and ein is null', $model->updateEinAndEinExpiration($ein, $ein_expiration))->true();
        expect('Ein is correct', $model->ein)->equals(123);
        expect('Ein expiration is correct', $model->ein_expiration)->equals((new \DateTime('now'))->format('Y-m-d'));
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

    public function testUpdateBillNumberTo()
    {
        $model = BillExpert::createBill(1);
        $model->company_id = 1;
        $model->status = 'draft';
        $model->partner_distribution_model_id = 1;
        $model->save();

        expect('Cant update bill number to if status is closed', $model->updateBillNumberTo('69'))->false();
        $model->status = 'closed';

        expect('bill number to is updated', $model->updateBillNumberTo('69'))->true();
        expect('Bill numbet to is correct', $model->bill_number_to)->equals('00000000000000000069');
    }
}