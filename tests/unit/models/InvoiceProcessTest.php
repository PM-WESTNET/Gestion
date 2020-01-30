<?php
/**
 * Created by PhpStorm.
 * User: dexterlab10
 * Date: 24/10/19
 * Time: 11:04
 */

use app\modules\sale\models\InvoiceProcess;
use app\tests\fixtures\CompanyFixture;
use app\tests\fixtures\BillTypeFixture;

class InvoiceProcessTest extends \Codeception\Test\Unit
{
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
                'class' => CompanyFixture::class,
            ],
            'bill_type' => [
                'class' => BillTypeFixture::class,
            ],
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new InvoiceProcess();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new InvoiceProcess([
            'company_id' => 1,
            'bill_type_id' => 1,
            'status' => InvoiceProcess::STATUS_PENDING,
            'period' => (new \DateTime('now'))->format('Y-m-d'),
            'type' => InvoiceProcess::TYPE_CREATE_BILLS
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new InvoiceProcess();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new InvoiceProcess([
            'company_id' => 1,
            'bill_type_id' => 1,
            'status' => InvoiceProcess::STATUS_PENDING,
            'period' => (new \DateTime('now'))->format('Y-m-d'),
            'type' => InvoiceProcess::TYPE_CREATE_BILLS
        ]);

        expect('Saved when full and new', $model->save())->true();
    }

    public function testCreateInvoiceProcess()
    {
        $invoice_process_qty = count(InvoiceProcess::find()->all());

        InvoiceProcess::createInvoiceProcess(1,1,'2019-10-01', 'observation',InvoiceProcess::TYPE_CREATE_BILLS);

        expect('Invoice process created', count(InvoiceProcess::find()->all()))->equals($invoice_process_qty + 1);
    }

}