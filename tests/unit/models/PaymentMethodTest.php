<?php 
class PaymentMethodTest extends \Codeception\Test\Unit
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

    // tests
    public function testSaveSuccessWithoutSendIvr()
    {
        $paymentMethod = new \app\modules\checkout\models\PaymentMethod([
           'name' => 'Payment Method 1',
            'status' => 'enabled',
            'register_number' => true,
            'type' => 'exchanging'
        ]);

        expect('Not save', $paymentMethod->save())->true();
    }

    public function testSaveSuccessWithSendIvr()
    {
        $paymentMethod = new \app\modules\checkout\models\PaymentMethod([
            'name' => 'Payment Method 1',
            'status' => 'enabled',
            'register_number' => true,
            'type' => 'exchanging',
            'send_ivr' => true
        ]);

        expect('Not save', $paymentMethod->save())->true();
    }
}