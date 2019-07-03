<?php

use app\tests\fixtures\BillTypeFixture;
use app\tests\fixtures\CompanyFixture;
use app\tests\fixtures\PointOfSaleFixture;
use app\modules\sale\components\BillExpert;
use app\tests\fixtures\CompanyHasBillTypeFixture;
use app\tests\fixtures\PartnerDistributionModelFixture;
use app\tests\fixtures\CurrencyFixture;
use app\tests\fixtures\UnitFixture;
use app\modules\sale\models\BillDetail;
use app\tests\fixtures\ProductFixture;
use app\tests\fixtures\ProductPriceFixture;
use app\tests\fixtures\ProductHasTaxRateFixture;

class BillDetailTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $bill_id;

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
            'unit' => [
                'class' => UnitFixture::class
            ],
            'product' => [
                'class' => ProductFixture::class
            ],
            'product_price' => [
                'class' => ProductPriceFixture::class
            ],
            'product_has_tax_rate' => [
                'class' => ProductHasTaxRateFixture::class
            ],
        ];
    }

    public function testInvalidAndEmpty()
    {
        $model = new BillDetail();
        expect('Invalid when empty', $model->validate())->false();
    }

    public function testValidAndFull()
    {
        $bill = BillExpert::createBill(1);
        $bill->company_id = 1;
        $bill->status = 'draft';
        $bill->partner_distribution_model_id = 1;
        $bill->save();

        $model = new BillDetail([
            'bill_id' => $bill->bill_id,
            'unit_id' => 1
        ]);

        expect('Valid when empty', $model->validate())->true();
    }

    public function testNotSaveWhenEmpty()
    {
        $model = new BillDetail();

        expect('Not save when empty', $model->save())->false();
    }

    public function testSaveWhenNew()
    {
        $bill = BillExpert::createBill(1);
        $bill->company_id = 1;
        $bill->status = 'draft';
        $bill->partner_distribution_model_id = 1;
        $bill->save();

        $model = new BillDetail([
            'bill_id' => $bill->bill_id,
            'unit_id' => 1
        ]);

        expect('Save when full', $model->save())->true();
    }

    public function testGetIva()
    {
        $bill = BillExpert::createBill(1);
        $bill->company_id = 1;
        $bill->status = 'draft';
        $bill->partner_distribution_model_id = 1;
        $bill->save();

        $model = new BillDetail([
            'bill_id' => $bill->bill_id,
            'unit_id' => 1,
            'concept' => 'xxx',
            'unit_final_price' => 100,
            'product_id' => 4
        ]);
        $model->save();

        expect('Get iva return 0.21', $model->getIva())->equals(0.21);
    }
}