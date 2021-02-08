<?php namespace models;

use app\modules\checkout\models\search\PaymentSearch;
use app\modules\sale\models\Customer;
use app\modules\sale\models\CustomerMessage;
use app\tests\fixtures\BillFixture;
use app\tests\fixtures\CustomerFixture;
use app\tests\fixtures\CustomerMessageFixture;
use app\tests\fixtures\PaymentFixture;

class CustomerMessageTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function _fixtures()
    {
        return [
            'customer_message' => [
                'class' => CustomerMessageFixture::class
            ],
            'customer' => [
                'class' => CustomerFixture::class
            ],
            'bills' => [
                'class' => BillFixture::class
            ],
            'payments' => [
                'class' => PaymentFixture::class
            ]
        ];
    }

    public function testFailWhenNew()
    {
        $model = new CustomerMessage();

        expect('Fail', $model->save())->false();
    }

    public function testSuccessSave()
    {
        $model = new CustomerMessage();

        $model->name = 'Test Me';
        $model->status = CustomerMessage::STATUS_ENABLED;
        $model->message = 'Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem.';

        expect('Fail', $model->save())->true();
    }


    public function testGetCustomerName()
    {
        $customer = Customer::findOne(45900);
        $customerMessage = CustomerMessage::findOne(1);

        expect('fail', $customerMessage->getValue('customer_name', $customer))->equals($customer->fullName);

    }

    public function testGetCustomerPaymentCode()
    {
        $customer = Customer::findOne(45900);
        $customerMessage = CustomerMessage::findOne(1);

        expect('fail', $customerMessage->getValue('payment_code', $customer))->equals($customer->payment_code);

    }

    public function testGetCustomerCode()
    {
        $customer = Customer::findOne(45900);
        $customerMessage = CustomerMessage::findOne(1);

        expect('fail', $customerMessage->getValue('code', $customer))->equals($customer->code);

    }

    public function testGetCustomerDebt()
    {
        $customer = Customer::findOne(45900);
        $customerMessage = CustomerMessage::findOne(1);

        $paymentSearch = new PaymentSearch();
        $paymentSearch->customer_id = $customer->customer_id;
        $debth = $paymentSearch->accountTotalCredit();


        expect('fail', $customerMessage->getValue('debt', $customer))->equals(\Yii::$app->formatter->asCurrency($debth));

    }

    public function testSuccessSend()
    {
        $customer = Customer::findOne(45900);
        $customerMessage = CustomerMessage::findOne(1);

        $message = 'Test my name GOMEZ, MARIO SANTOS and my payment code 999000111222 and my code 59809  and my debt $0,00';

        $response = $customerMessage->send($customer);

        expect('Failed status', $response['status'])->equals('success');
        expect('Failed message', $response['message'])->equals($message);
    }

}