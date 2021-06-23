<?php namespace models;

use app\modules\sale\models\CustomerCompanyHistory;
use app\tests\fixtures\CompanyFixture;
use app\tests\fixtures\CustomerFixture;


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

    public function _fixtures()
    {
        return [
            'customer' => [
                'class' => CustomerFixture::class
            ],
        ];
    }

    public function testSaveSuccessfull()
    {   
        $model = new CustomerCompanyHistory([
            //'customer_company_history_id' => '',
            'customer_id' => 45900,
            'old_company_id' => 1,
            'new_company_id' => 2,
            //'created_at' => ,
        ]);

        expect(
            'Invalid when add costumer history', 
            $model->save()
        )->true();

    }
    
    public function testNotSaveOnEmpty()
    {
        $model = new CustomerCompanyHistory();
        expect(
            'Invalid when empty and new', 
            $model->save()
        )->false();
    }

    public function testNotSaveOnNewCompanyIdIsEmpty()
    {
        $model = new CustomerCompanyHistory([
            'customer_id' => 45900,
            'new_company_id' => 2,
        ]);
        expect(
            'Invalid when empty and new', 
            $model->save()
        )->false();
    }

    
    public function testNotSaveOnNewCompanyIdIsNull(){
        $model = new CustomerCompanyHistory([
            'customer_id' => 45900,
            'old_company_id' => null,
            'new_company_id' => 1,
        ]);
        expect(
            'Invalid when empty and new', 
            $model->save()
        )->false();
    }
    public function testNotSaveOnCompanyIdIsNull(){
        $model = new CustomerCompanyHistory([
            'customer_id' => 45900,
            'old_company_id' => 1,
            'new_company_id' => 0,
        ]);
        expect(
            'Invalid when empty and new', 
            $model->save()
        )->false();
    }

    public function testNotSaveOnCostumerIdInvalid(){
        $model = new CustomerCompanyHistory([
            'customer_id' => 'AQw2',
            'old_company_id' => 1,
            'new_company_id' => 0,
        ]);
        expect(
            'Invalid when empty and new', 
            $model->save()
        )->false();
    }
}