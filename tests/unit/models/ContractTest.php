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
use app\modules\config\models\Config;
use app\modules\sale\models\ProductToInvoice;
use app\tests\fixtures\ContractDetailFixture;

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
            ],
            'contract_detail' => [
                'class' => ContractDetailFixture::class
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

            if($value['status'] == 'draft'){
                $draft = true;
            }
            if($value['status'] == 'active'){
                $active = true;
            }
            if($value['status'] == 'low'){
                $low = true;
            }
        }

        expect('Status draft', $draft)->true();
        expect('Status active', $active)->true();
        expect('Status low', $low)->true();
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

    public function testGetActivePaymentExtensionQtyPerPeriod()
    {
        $model = new Contract([
            'date' => (new \DateTime('now'))->format('d-m-Y'),
            'customer_id' => 45900
        ]);
        $model->save();

        Config::setValue('id-product_id-extension-de-pago', 3);

        $this->tester->haveRecord(ContractDetail::class, [
            'contract_id' => $model->contract_id,
            'product_id' => 1,
            'from_date' => (new \DateTime('now'))->format('Y-m-d'),
            'to_date' => NULL,
            'status' => 'active',
            'funding_plan_id' => NULL,
            'date' => (new \DateTime('now'))->modify('+1 year')->format('Y-m-d'),
            'discount_id' => NULL,
            'count' => '1',
            'vendor_id' => NULL,
            'applied' => '1'
        ]);

        expect('Contract dont have any payment extension', $model->getActivePaymentExtensionQtyPerPeriod())->equals(0);

        $contract_detail_id = $this->tester->haveRecord(ContractDetail::class, [
            'contract_id' => $model->contract_id,
            'product_id' => 3,
            'from_date' => (new \DateTime('now'))->format('Y-m-d'),
            'to_date' => NULL,
            'status' => 'active',
            'funding_plan_id' => NULL,
            'date' => (new \DateTime('now'))->modify('+1 year')->format('Y-m-d'),
            'discount_id' => NULL,
            'count' => '1',
            'vendor_id' => NULL,
            'applied' => '1'
        ]);

        $this->tester->haveRecord(ProductToInvoice::class, [
            'contract_detail_id' => $contract_detail_id,
            'funding_plan' => null,
            'date' => (new \DateTime('now'))->format('Y-m-01'),
            'period' => (new \DateTime('now'))->format('Y-m-01'),
            'amount' => 100,
            'status' => 'active',
        ]);

        expect('Contract dont have any payment extension', $model->getActivePaymentExtensionQtyPerPeriod())->equals(1);


    }

}