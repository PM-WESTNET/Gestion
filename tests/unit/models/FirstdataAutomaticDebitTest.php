<?php namespace models;

use Codeception\Util\Debug;
use app\tests\fixtures\CustomerFixture;
use app\tests\fixtures\FirstdataConfigCompanyFixture;
use app\modules\firstdata\models\FirstdataAutomaticDebit;

class FirstdataAutomaticDebitTest extends \Codeception\Test\Unit
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

    public function _fixtures() {
        return [
            [
                'class' => FirstdataConfigCompanyFixture::class
            ],
            [
                'class' => CustomerFixture::class
            ],
        ];
    }

    public function testSaveFailOnNew() {
        $model = new FirstdataAutomaticDebit();

        expect('not saved', $model->save())->false();
    }

    public function testSaveFailOnEmptyCustomer() {
        $model = new FirstdataAutomaticDebit([
            'status' => 'enabled'
        ]);

        expect('not saved', $model->save())->false();
    }

    public function testSaveFailOnEmptyStatus() {
        $model = new FirstdataAutomaticDebit([
            'customer_id' => 45900,
        ]);

        expect('not saved', $model->save())->false();
    }

    public function testSaveSuccess() {
        $model = new FirstdataAutomaticDebit([
            'customer_id' => 45900,
            'status' => 'enabled'
        ]);

        $result = $model->save();
        Debug::debug($model->getErrors());
        expect('not saved', $result)->true();
    }
}