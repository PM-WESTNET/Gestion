<?php

use app\modules\automaticdebit\models\DebitDirectImport;
use Codeception\Test\Unit;
use app\tests\fixtures\CompanyFixture;
use app\tests\fixtures\BankFixtures;
use app\tests\fixtures\MoneyBoxAccountFixture;
use app\modules\checkout\models\Payment;
use app\tests\fixtures\CustomerFixture;
use app\tests\fixtures\PartnerDistributionModelFixture;
use app\modules\automaticdebit\models\DebitDirectImportHasPayment;
use app\modules\automaticdebit\models\DebitDirectFailedPayment;

class DebitDirectImportTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _fixtures()
    {
        return [
            'bank' => [
                'class' => BankFixtures::class
            ],
            'company' => [
                'class' => CompanyFixture::class
            ],
            'money_box_account' => [
                'class' => MoneyBoxAccountFixture::class
            ],
            'customer' => [
                'class' => CustomerFixture::class
            ],
            'partner_distribution_model_id' => [
                'class' => PartnerDistributionModelFixture::class
            ]
        ];
    }
    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new DebitDirectImport();

        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new DebitDirectImport([
            'company_id' => 1 ,
            'bank_id' => 1,
            'money_box_account_id' => 42,
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new DebitDirectImport();

        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new DebitDirectImport([
            'company_id' => 1 ,
            'bank_id' => 1,
            'money_box_account_id' => 42,
        ]);

        expect('Save when full and new', $model->save())->true();
    }

    public function testClosePayments()
    {
        $payment = new Payment([
            'customer_id' => 45900,
            'company_id' => 1,
            'amount' => 100,
            'date' => (new \DateTime('now'))->format('Y-m-d'),
            'partner_distribution_model_id' => 1,
        ]);
        $payment->save();

        $model = new DebitDirectImport([
            'company_id' => 1 ,
            'bank_id' => 1,
            'money_box_account_id' => 42,
        ]);
        $model->save();

        $model->createPaymentRelation($payment->payment_id);

        $close_payment_result = $model->closePayments();

        expect('Close payments successfully', $close_payment_result['status'])->true();
        expect('Close payments successfully without errors', $close_payment_result['errors'])->isEmpty();
    }

    public function testCreatePaymentRelation()
    {
        $payment = new Payment([
            'customer_id' => 45900,
            'company_id' => 1,
            'amount' => 100,
            'date' => (new \DateTime('now'))->format('Y-m-d'),
            'partner_distribution_model_id' => 1,
        ]);
        $payment->save();

        $model = new DebitDirectImport([
            'company_id' => 1 ,
            'bank_id' => 1,
            'money_box_account_id' => 42,
        ]);
        $model->save();

        expect('Create relation return true', $model->createPaymentRelation($payment->payment_id))->true();

        expect('Has one relation', count(DebitDirectImportHasPayment::find()->all()))->equals(1);
    }

    public function testCreateFailedPayment()
    {
        $model = new DebitDirectImport([
            'company_id' => 1 ,
            'bank_id' => 1,
            'money_box_account_id' => 42,
        ]);
        $model->save();

        $failed_payment = DebitDirectImport::createFailedPayment('123', 123, (new \DateTime('now'))->format('Y-m-d'), '123456789', $model->debit_direct_import_id, 'error');

        expect('Create failed payment return true', $failed_payment)->true();
        expect('Failed payment created', count(DebitDirectFailedPayment::find()->all()))->equals(1);
    }

    public function testArePaymentPendingToClose()
    {
        $model = new DebitDirectImport([
            'company_id' => 1 ,
            'bank_id' => 1,
            'money_box_account_id' => 42,
        ]);
        $model->save();

        $payment = new Payment([
            'customer_id' => 45900,
            'company_id' => 1,
            'amount' => 100,
            'date' => (new \DateTime('now'))->format('Y-m-d'),
            'partner_distribution_model_id' => 1,
            'status' => Payment::PAYMENT_DRAFT
        ]);
        $payment->save();

        expect('Import doesnt have any pending payment', $model->arePaymentPendingToClose())->false();

        $model->createPaymentRelation($payment->payment_id);

        expect('Import have one pending payment to close', $model->arePaymentPendingToClose())->true();
    }
}