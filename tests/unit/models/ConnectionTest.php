<?php namespace models;

use app\modules\westnet\models\Connection;
use app\tests\fixtures\ConnectionFixture;
use app\tests\fixtures\ContractFixture;
use app\tests\fixtures\ProductFixture;
use app\tests\fixtures\ProductPriceFixture;
use Codeception\Util\Debug;

class ConnectionTest extends \Codeception\Test\Unit
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

    public function _fixtures ()
    {
        return [
            'contract' => [
                'class' => ContractFixture::class
            ],
            'connection' => [
                'class' => ConnectionFixture::class
            ],
            'product' => [
                'class' =>  ProductFixture::class
            ],
            'product_price' => [
                'class' =>  ProductPriceFixture::class
            ]
        ];
    }


    public function testSuccessForceConnection()
    {
        $model = Connection::findOne(2);

        $data = [
            'due_date' => '25-03-2019',
            'reason' => 'TestMe',
            'product_id' => 3,
            'create_product' => 1,
            'vendor_id' => 1
        ];

        if (!$model->force($data['due_date'], $data['product_id'], $data['vendor_id'])){
            expect('Failed force', false)->true();
            return false;
        }


        $contract = $model->contract;

        $contract_detail = $contract->getContractDetails()->andWhere(['product_id' => $data['product_id']])->one();
        expect('failed status', $model->status_account === Connection::STATUS_ACCOUNT_FORCED)->true();
        expect('failed due date', $model->due_date === '2019-03-25')->true();
        expect('failed contract detail', !empty($contract_detail))->true();
        expect('failed product to invoices', $contract_detail->getProductToInvoices()->exists())->true();

    }

    public function testSuccessForceConnectionWithoutProductToInvoice()
    {
        $model = Connection::findOne(2);

        $data = [
            'due_date' => '25-03-2019',
            'reason' => 'TestMe',
            'product_id' => 3,
            'create_product' => 0,
            'vendor_id' => 1
        ];

        if (!$model->force($data['due_date'], $data['product_id'], $data['vendor_id'], $data['create_product'])){
            expect('Failed force', false)->true();
            return false;
        }


        $contract = $model->contract;

        $contract_detail = $contract->getContractDetails()->andWhere(['product_id' => $data['product_id']])->one();
        expect('failed status', $model->status_account === Connection::STATUS_ACCOUNT_FORCED)->true();
        expect('failed due date', $model->due_date === '2019-03-25')->true();
        expect('failed contract detail', empty($contract_detail))->true();

    }
}