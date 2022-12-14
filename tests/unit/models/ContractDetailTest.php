<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 12/03/19
 * Time: 17:21
 */

use app\modules\sale\modules\contract\models\ContractDetail;
use app\tests\fixtures\CustomerFixture;
use app\tests\fixtures\ProductFixture;
use app\modules\sale\modules\contract\models\Contract;
use app\tests\fixtures\VendorFixture;
use app\modules\sale\models\ProductToInvoice;

class ContractDetailTest extends \Codeception\Test\Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function before()
    {

    }

    public function _fixtures()
    {
        return [
            'customer' => [
                'class' => CustomerFixture::class
            ],
            'product' => [
                'class' => ProductFixture::class
            ],
            'vendor' => [
                'class' => VendorFixture::class
            ],
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new ContractDetail();
        $this->assertFalse($model->validate());
    }

    public function testValidWhenFullAndNew()
    {
        $contract = new Contract([
            'date' => (new \DateTime('now'))->format('d-m-Y'),
            'customer_id' => 45900,
        ]);
        $contract->save();

        $model = new ContractDetail([
            'contract_id' => $contract->contract_id,
            'product_id' => 1,
            'count' => 1,
            'vendor_id' => 1
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmpty()
    {
        $model = new ContractDetail();
        $this->assertFalse($model->save());
    }

    public function testSavedWhenFullAndNew()
    {
        $contract = new Contract([
            'date' => (new \DateTime('now'))->format('d-m-Y'),
            'customer_id' => 45900,
        ]);
        $contract->save();

        $model = new ContractDetail([
            'contract_id' => $contract->contract_id,
            'product_id' => 1,
            'count' => 1,
            'vendor_id' => 1
        ]);

        expect('Save when full and new', $model->save())->true();
    }

    //TODO ver logica del metodo isEqual
//    public function testIsEqual()
//    {
//        $contract = new Contract([
//            'date' => (new \DateTime('now'))->format('d-m-Y'),
//            'customer_id' => 45900,
//        ]);
//        $contract->save();
//
//        $model = new ContractDetail([
//            'contract_id' => $contract->contract_id,
//            'product_id' => 1,
//            'count' => 1,
//            'vendor_id' => 1
//        ]);
//        $model->save();
//
//        $model2 = new ContractDetail([
//            'contract_id' => $contract->contract_id,
//            'product_id' => 1,
//            'count' => 1,
//            'vendor_id' => 1
//        ]);
//        $model2->save();
//
//        expect('Models are equal', $model->isEqual($model2))->true();
//
//        $model2->updateAttributes(['product_id' => 2]);
//
//        expect('Models arent equal', $model->isEqual($model2))->false();
//    }

//    public function testCreateLog()
//    {
//        $contract = new Contract([
//            'date' => (new \DateTime('now'))->format('d-m-Y'),
//            'customer_id' => 45900,
//        ]);
//        $contract->save();
//
//        $model = new ContractDetail([
//            'contract_id' => $contract->contract_id,
//            'product_id' => 1,
//            'count' => 1,
//            'vendor_id' => 1
//            'status' => ContractDetail::S
//        ]);
//    }

    /*
     public function createLog()
    {
        if ($this->status=='draft') {
            return false;
        }
        $log = new ContractDetailLog();
        $log->contract_detail_id = $this->contract_detail_id;
        $log->funding_plan_id = $this->funding_plan_id;
        $log->product_id = $this->product_id;
        $log->date = $this->date;
        $log->from_date = $this->from_date;
        $log->to_date = $this->to_date;
        $log->status = $this->status;
        $log->discount_id = $this->discount_id;
        $log->save(false);
        return true;
    }
     */

    //TODO resto de la clase
}