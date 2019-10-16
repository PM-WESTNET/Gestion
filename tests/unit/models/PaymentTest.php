<?php

use app\modules\checkout\models\Payment;
use app\tests\fixtures\CustomerFixture;
use app\tests\fixtures\CompanyFixture;
use app\tests\fixtures\PartnerDistributionModelFixture;

class PaymentTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _fixtures()
    {
        return [
            'customer' => [
                'class' => CustomerFixture::class
            ],
            'company' => [
                'class' => CompanyFixture::class
            ],
            'partnert_distribution_model' => [
                'class' => PartnerDistributionModelFixture::class
            ]
        ];
    }
    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new Payment();
        $this->assertFalse($model->validate());
    }

    public function testValidWhenFullAndNew()
    {
        $model = new Payment([
            'customer_id' => 1,
            'company_id' => 1,
            'amount' => 1,
            'date' => (new \DateTime('now'))->format('Y-m-d'),
        ]);

        $this->assertTrue($model->validate());
    }

    public function testFunctionGetLastNumber(){
        $company_id = 1;

        $payment_id = $this->tester->haveRecord(Payment::class, [
            'amount' => '1650',
            'date' => (new \DateTime('now'))->format('Y-m-d'),
            'time' => (new \DateTime('now'))->format('H:m:i'),
            'timestamp' => '1541417762',
            'concept' => 'PAGO FACIL',
            'customer_id' => 45900,     //Fixture
            'number' => '10',
            'balance' => '1650',
            'status' => 'closed',
            'company_id' => $company_id,
            'partner_distribution_model_id' => 1
        ]);

        $last_number = Payment::getLastNumber($company_id);

        expect('Last number is the last inserted', $last_number)->equals('10');
        expect('Last number + 1 not exists', Payment::find()->where(['company_id' => $company_id, 'number' => $last_number + 1])->exists())->false();
    }

    public function testClosePayment()
    {
        $payment = new Payment([
            'customer_id' => 45900,
            'company_id' => 1,
            'amount' => 100,
            'date' => (new \DateTime('now'))->format('Y-m-d'),
            'partner_distribution_model_id' => 1,
        ]);
        $payment->save();

        expect('Close payment', $payment->close())->true();
    }
}