<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 11/12/18
 * Time: 13:34
 */

use app\modules\afip\models\TaxesBook;
use app\tests\fixtures\CompanyFixture;
use app\modules\sale\models\Bill;
use app\tests\fixtures\CurrencyFixture;
use app\tests\fixtures\BillTypeFixture;
use app\tests\fixtures\CompanyHasBillTypeFixture;

class TaxesBookTest extends \Codeception\Test\Unit
{

    /**
     * @var UnitTester
     */
    protected $tester;

    public function before()
    {
    }

    public function _fixtures()
    {
        return [
            'company' => [
                'class' => CompanyFixture::class
            ],
            'currency' => [
                'class' => CurrencyFixture::class
            ],
            'bill_type' => [
                'class' => BillTypeFixture::class
            ],
            'company_has_bill_type' => [
                'class' => CompanyHasBillTypeFixture::class
            ]
        ];
    }

    public function testInvalidWhenEmptyAndNew(){
        $model = new TaxesBook();

        expect('Invalid when empty an new', $model->validate())->false();
    }

    public function testValidWhenNew(){
        $model_buy = new TaxesBook([
            'type' => 'buy',
            'period' => (new \DateTime('now'))->format('yyyy-MM-dd')
        ]);

        expect('Valid when type buy and new', $model_buy->validate())->true();

        $model_sale = new TaxesBook([
            'type' => 'sale',
            'period' => (new \DateTime('now'))->format('yyyy-MM-dd')
        ]);

        expect('Valid when type sale and new', $model_sale->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew(){
        $model = new TaxesBook();

        expect('Not saved when empty an new', $model->save())->false();
    }

    public function testSaveWhenNew(){
        $model_buy = new TaxesBook([
            'type' => 'buy',
            'period' => new \DateTime('now'),
            'company_id' => 1
        ]);

        expect('Saved when type buy and new', $model_buy->save())->true();

        $model_sale = new TaxesBook([
            'type' => 'sale',
            'period' => new \DateTime('now'),
            'company_id' => 1
        ]);

        expect('Saved when type sale and new', $model_sale->save())->true();
    }

    public function testAddTaxesBookItem()
    {
        $model = new TaxesBook([
            'type' => 'buy',
            'period' => new \DateTime('now'),
            'company_id' => 1
        ]);
        $model->save();

        $bill = new Bill([
            'date' => (new \DateTime('now'))->format('Y-m-d'),
            'number' => '1',
            'company_id' => '1',
            'class' => 'app\\modules\\sale\\models\\bills\\Bill',
            'bill_type_id' => 1
        ]);
        $bill->save();

        $model->addTaxesBookItem($bill->bill_id, 1);

        expect('Taxes book has item', $model->taxesBookItems)->notEmpty();
    }

    public function testCloseTaxesBook()
    {
        $model = new TaxesBook([
            'type' => 'buy',
            'period' => new \DateTime('now'),
            'company_id' => 1
        ]);
        $model->save();

        $bill = new Bill([
            'date' => (new \DateTime('now'))->format('Y-m-d'),
            'number' => '1',
            'company_id' => '1',
            'class' => 'app\\modules\\sale\\models\\bills\\Bill',
            'bill_type_id' => 1
        ]);
        $bill->save();

        $model->addTaxesBookItem($bill->bill_id, 1);
        $model->close();

        expect('Taxes book closed', $model->status == 'closed')->true();

        $model2 = new TaxesBook([
            'type' => 'sale',
            'period' => new \DateTime('now'),
            'company_id' => 1
        ]);
        $model2->save();

        $bill2 = new Bill([
            'date' => (new \DateTime('now'))->format('Y-m-d'),
            'number' => '1',
            'company_id' => '1',
            'class' => 'app\\modules\\sale\\models\\bills\\Bill',
            'bill_type_id' => 1
        ]);
        $bill2->save();

        $model2->addTaxesBookItem($bill2->bill_id, 1);
        $model2->close();

        expect('Taxes book closed', $model2->status == TaxesBook::STATE_CLOSED)->true();
    }

    public function testGetStatusesForSelect()
    {
        $select = TaxesBook::getStatusesForSelect();

        expect('close exists', array_key_exists(TaxesBook::STATE_CLOSED, $select))->true();
        expect('draft exists', array_key_exists(TaxesBook::STATE_DRAFT, $select))->true();

    }
}