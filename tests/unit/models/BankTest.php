<?php namespace models;

use app\modules\automaticdebit\models\Bank;
use app\tests\fixtures\BankFixtures;
use app\tests\fixtures\CompanyFixture;

class BankTest extends \Codeception\Test\Unit
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



    public function testInvalidWhenNew()
    {
        $model = new Bank();

        expect('Failed', $model->save())->false();
    }

    public function testSuccessSave() {
        $model= new Bank();

        $model->name = 'Test Bank';
        $model->status = Bank::STATUS_ENABLED;
        $model->class = 'Test\Class';

        expect('Failed', $model->save())->true();
    }

    public function testFailSaveWhenEmptyName() {
        $model= new Bank();

        $model->status = Bank::STATUS_ENABLED;
        $model->class = 'Test\Class';

        expect('Failed', $model->save())->false();
    }


    public function testFailSaveWhenEmptyStatus() {
        $model= new Bank();

        $model->name = 'Test Bank';
        $model->class = 'Test\Class';

        expect('Failed', $model->save())->false();
    }

    public function testFailSaveWhenEmptyClass() {
        $model= new Bank();

        $model->name = 'Test Bank';
        $model->status = Bank::STATUS_ENABLED;

        expect('Failed', $model->save())->false();
    }

}