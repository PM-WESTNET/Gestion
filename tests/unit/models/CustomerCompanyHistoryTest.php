<?php namespace models;

use app\modules\sale\models\CustomerCompanyHistory;

class CustomerCompanyHistoryTest extends \Codeception\Test\Unit
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
    public function testNotSaveOnEmpty()
    {
        $model = new CustomerCompanyHistory();
        expect(
            'Invalid when empty and new', 
            $model->save()
        )->false();
    }
}