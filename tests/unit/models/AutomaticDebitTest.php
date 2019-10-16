<?php namespace models;

use app\modules\automaticdebit\models\AutomaticDebit;
use app\tests\fixtures\BankFixtures;
use app\tests\fixtures\CustomerFixture;
use Codeception\Util\Debug;

class AutomaticDebitTest extends \Codeception\Test\Unit
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
            'customer' => [
                'class' => CustomerFixture::class
            ],
            'bank' => [
                'class' => BankFixtures::class
            ]
        ];
    }

    public function testInvalidWhenNew()
    {
        $model = new AutomaticDebit();

        expect('Failed', $model->save())->false();
    }

    public function testSuccessSave()
    {
        $model = new AutomaticDebit();

        $model->customer_id = 45900;
        $model->bank_id = 1;
        $model->cbu = '1111111111111111111111';
        $model->beneficiario_number = '1231231231231231231231';
        $model->status = AutomaticDebit::ENABLED_STATUS;

        expect('Failed', $model->save())->true();

    }

}