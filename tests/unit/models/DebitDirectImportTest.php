<?php

use app\modules\automaticdebit\models\DebitDirectImport;
use Codeception\Test\Unit;
use app\tests\fixtures\CompanyFixture;
use app\tests\fixtures\BankFixtures;
use app\tests\fixtures\MoneyBoxAccountFixture;
use app\modules\checkout\models\Payment;
use app\tests\fixtures\CustomerFixture;
use app\tests\fixtures\PartnerDistributionModelFixture;


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
    /*
     public function closePayments()
    {
        $payments = $this->getPayments()->andWhere(['status' => 'draft'])->all();
        $errors = [];

        foreach ($payments as $payment) {
            if (!$payment->close()){
                array_push($errors, Yii::t('app', "Can't close payment"). ': '.$payment->payment_id);
            }
        }

        if(empty($errors)) {
            $this->updateAttributes(['status' => DebitDirectImport::SUCCESS_STATUS]);
        }

        return [
            'status' => empty($errors) ? true : false,
            'errors' => $errors
        ];
    }
     */
}