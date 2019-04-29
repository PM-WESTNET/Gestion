<?php

use app\modules\sale\modules\contract\models\Contract;
use app\tests\fixtures\CustomerFixture;
use app\modules\sale\modules\contract\models\ContractDetail;
use app\tests\fixtures\ProductFixture;
use app\modules\sale\models\Product;
use app\tests\fixtures\ZoneFixture;
use app\tests\fixtures\ServerFixture;
use app\tests\fixtures\NodeFixture;
use app\modules\westnet\models\Connection;

class ContractTest extends \Codeception\Test\Unit
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
            'zone' => [
                'class' => ZoneFixture::class
            ],
            'server' => [
                'class' => ServerFixture::class
            ],
            'node' => [
                'class' => NodeFixture::class
            ]
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new Contract();
        $this->assertFalse($model->validate());
    }

    public function testValidWhenFullAndNew()
    {
        $model = new Contract();
        $model->date = (new \DateTime('now'))->format('d-m-Y');
        $this->assertTrue($model->validate());
    }

    public function testNotSaveWhenEmpty()
    {
        $model = new Contract();
        $this->assertFalse($model->save());
    }

    public function testSavedWhenFullAndNew()
    {
        $model = new Contract();
        $model->date = (new \DateTime('now'))->format('d-m-Y');
        $model->customer_id = 45900;
        $this->assertTrue($model->save());
    }

    public function testGetPlan()
    {
        $contract_id = $this->tester->haveRecord(Contract::class, [
            'date' => (new \DateTime('now'))->format('d-m-Y'),
            'customer_id' => 45900,     //----Manejado por Fixture
        ]);

        $contract_detail_id = $this->tester->haveRecord(ContractDetail::class, [
            'contract_id' => $contract_id,
            'product_id' => 1,      //----Manejado por Fixture
            'from_date' => '2016-03-06',
            'to_date' => NULL,
            'status' => 'active',
            'funding_plan_id' => NULL,
            'date' => '2016-03-06',
            'discount_id' => NULL,
            'count' => '1',
            'vendor_id' => NULL,
            'applied' => '1'
        ]);

        $model = Contract::findOne($contract_id);
        $plan = $model->plan;

        $this->assertInstanceOf(Product::class, $plan);
        $this->assertTrue($plan->type == 'plan');

    }

    public function testPlanChanged()
    {
        $contract_id = $this->tester->haveRecord(Contract::class, [
            'date' => (new \DateTime('now'))->format('d-m-Y'),
            'customer_id' => 45900,     //----Manejado por Fixture
        ]);

        $contract_detail_id = $this->tester->haveRecord(ContractDetail::class, [
            'contract_id' => $contract_id,
            'product_id' => 1,      //----Manejado por Fixture
            'from_date' => (new \DateTime('now'))->format('d-m-Y'),
            'to_date' => NULL,
            'status' => 'active',
            'funding_plan_id' => NULL,
            'date' => '2016-03-06',
            'discount_id' => NULL,
            'count' => '1',
            'vendor_id' => NULL,
            'applied' => '1'
        ]);

        $model = Contract::findOne($contract_id);
        $plan_changed = $model->isPlanChanged();

        $this->assertFalse($plan_changed);
    }

    public function testSetTentativeNode()
    {
        $contract_id = $this->tester->haveRecord(Contract::class, [
            'date' => (new \DateTime('now'))->format('d-m-Y'),
            'customer_id' => 45900,     //----Manejado por Fixture
        ]);

        $model = Contract::findOne($contract_id);
        $result = $model->setTentativeNode(1);      //Manejado por NodeFixture

        $this->assertTrue($result);
        $this->assertEquals(1, $model->tentative_node);
    }

    public function testGetStatuses()
    {

        $statuses = Contract::getStatuses();
        $draft = false;
        $active = false;
        $low = false;

        foreach ($statuses as $key => $value){

            if($key == Contract::STATUS_DRAFT){
                $draft = true;
            }
            if($key == Contract::STATUS_ACTIVE){
                $active = true;
            }
            if($key == Contract::STATUS_LOW){
                $low = true;
            }
        }
        $this->assertTrue($draft);
        $this->assertTrue($active);
        $this->assertTrue($low);
    }

    public function testGetStatusesForSelect(){
        $statuses_for_select = Contract::getStatusesForSelect();

        $draft = false;
        $active = false;
        $low = false;
        if(array_key_exists('draft', $statuses_for_select)){
            $draft = true;
        }
        if(array_key_exists('active', $statuses_for_select)){
            $active = true;
        }
        if(array_key_exists('low', $statuses_for_select)){
            $low = true;
        }

        $this->assertTrue($draft);
        $this->assertTrue($active);
        $this->assertTrue($low);
    }

    public function testRevertNegativeSurvey(){
        $contract_id = $this->tester->haveRecord(Contract::class, [
            'date' => (new \DateTime('now'))->format('d-m-Y'),
            'customer_id' => 45900,     //----Manejado por Fixture
            'status' => 'negative-survey'
        ]);

        $model = Contract::findOne($contract_id);
        $model->revertNegativeSurvey();

        $this->assertEquals('draft', $model->status);

        $connection_id = $this->tester->haveRecord(Connection::class,[
            'contract_id' => $contract_id,
            'node_id' => 1,     //Fixture
            'server_id' => 1,       //Fixture
            'ip4_1' => '169125810',
            'ip4_2' => '0',
            'ip4_public' => '0',
            'status' => 'enabled',
            'due_date' => NULL,
            'company_id' => 1,      //Fixture
            'payment_code' => NULL,
            'status_account' => 'enabled',
            'clean' => '0',
            'old_server_id' => NULL
        ]);

        $model->status = 'negative-survey';
        $model->revertNegativeSurvey();

        $this->assertEquals('active', $model->status);
    }

    public function testGetLastContractDetailByType() {
        $today = (new \DateTime('now'));
        $model = new Contract([
            'date' => $today->format('d-m-Y'),
            'customer_id' => 45900
        ]);
        $model->save();

        $contract_detail_id = $this->tester->haveRecord(ContractDetail::class, [
            'contract_id' => $model->contract_id,
            'product_id' => 1,
            'from_date' => $today->modify('-7 months')->format('Y-m-d'),
            'to_date' => NULL,
            'status' => 'active',
            'funding_plan_id' => NULL,
            'date' => $today->modify('+1 months')->format('Y-m-d'),
            'discount_id' => NULL,
            'count' => '1',
            'vendor_id' => NULL,
            'applied' => '1'
        ]);

        $last_detail = $model->getLastContractDetailByType('plan');
        expect('Have detail valid in oday range', $last_detail)->isInstanceOf(ContractDetail::class);
    }
    //TODO
/*
    public function testCancelContractDetailsOutOfDateRange()
    {
        $today = (new \DateTime('now'));
        $model = new Contract([
            'date' => $today->format('d-m-Y'),
            'customer_id' => 45900
        ]);
        $model->save();

        \Codeception\Util\Debug::debug($today->modify('-7 months')->format('Y-m-d'));
        $contract_detail_id = $this->tester->haveRecord(ContractDetail::class, [
            'contract_id' => $model->contract_id,
            'product_id' => 1,
            'from_date' => $today->modify('-7 months')->format('Y-m-d'),
            'to_date' => $today->modify('+1 months')->format('Y-m-d'),
            'status' => ContractDetail::STATUS_ACTIVE,
            'funding_plan_id' => NULL,
            'date' => $today->modify('+1 months')->format('Y-m-d'),
            'discount_id' => NULL,
            'count' => '1',
            'vendor_id' => 1,
            'applied' => '1'
        ]);

        \Codeception\Util\Debug::debug($today->modify('+1 months')->format('Y-m-d'));

        $contract_detail_id_2 = $this->tester->haveRecord(ContractDetail::class, [
            'contract_id' => $model->contract_id,
            'product_id' => 1,
            'from_date' => $today->modify('-3 days')->format('Y-m-d'),
            'to_date' => $today->modify('+1 months')->format('Y-m-d'),
            'status' => ContractDetail::STATUS_ACTIVE,
            'funding_plan_id' => NULL,
            'date' => $today->format('Y-m-d'),
            'discount_id' => NULL,
            'count' => '1',
            'vendor_id' => 1,
            'applied' => '1'
        ]);

        $contract_detail_1 = ContractDetail::findOne($contract_detail_id);
        $contract_detail_2 = ContractDetail::findOne($contract_detail_id_2);


        $model->cancelContractDetailsOutOfDateRange();

        expect('Contract detail 1 is in low state', $contract_detail_1->status)->equals(ContractDetail::STATUS_LOW);
        expect('Contract detail 2 is in active state', $contract_detail_2->status)->equals(ContractDetail::STATUS_ACTIVE);


    }

     */

}