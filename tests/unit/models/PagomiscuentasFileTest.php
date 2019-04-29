<?php 
class PagomiscuentasFileTest extends \Codeception\Test\Unit
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
        $model = new \app\modules\pagomiscuentas\models\PagomiscuentasFile();

        expect('Save', $model->save())->false();
    }

    public function testSuccessSaveTypeBill()
    {
        $model= new \app\modules\pagomiscuentas\models\PagomiscuentasFile([
            'company_id' => 1,
            'from_date' => '01-01-2019',
            'date' => '31-01-2019',
            'type' => 'bill'
        ]);

        expect('Not Saved', $model->save())->true();
    }

    public function testSuccessSaveTypePayment()
    {
        $model= new \app\modules\pagomiscuentas\models\PagomiscuentasFile([
            'company_id' => 1,
            'date' => '31-01-2019',
            'type' => 'payment'
        ]);

        expect('Not Saved', $model->save())->true();
    }


}