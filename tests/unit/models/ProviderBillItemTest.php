<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 12/12/18
 * Time: 12:15
 */

use app\modules\provider\models\ProviderBill;
use app\tests\fixtures\BillTypeFixture;
use app\tests\fixtures\ProviderFixture;
use app\tests\fixtures\CompanyFixture;
use app\modules\provider\models\ProviderBillItem;

class ProviderBillItemTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $provider_id;

    public function _before()
    {
        $this->provider_bill_id = $this->tester->haveRecord(ProviderBill::class,
            [
                'date' => (new \DateTime('now'))->format('Y-m-d'),
                'bill_type_id' => 1,        //Fixture
                'provider_id' => 150,
                'company_id' => 1,       //Fixture
                'status' => ProviderBill::STATUS_DRAFT,
                'number' => 0001-00000045,
            ]);
    }

    public function _fixtures(){
        return [
            'bill_type_id' => [
                'class' => BillTypeFixture::class
            ],
            'provider' => [
                'class' => ProviderFixture::class
            ],
            'company' => [
                'class' => CompanyFixture::class
            ],
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new ProviderBillItem();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new ProviderBillItem([
            'provider_bill_id'  => $this->provider_bill_id,
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new ProviderBillItem();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new ProviderBillItem([
            'provider_bill_id'  => $this->provider_bill_id,
        ]);

        expect('Saved when full and new', $model->save())->true();
    }
}