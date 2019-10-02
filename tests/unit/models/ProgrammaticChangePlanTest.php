<?php namespace models;

use app\modules\sale\modules\contract\models\ProgrammaticChangePlan;
use app\tests\fixtures\ContractDetailFixture;
use app\tests\fixtures\ContractFixture;
use app\tests\fixtures\ProductFixture;
use app\tests\fixtures\UserFixture;

class ProgrammaticChangePlanTest extends \Codeception\Test\Unit
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
            'contract' => [
                'class' => ContractFixture::class
            ],
            'contract_detail' => [
                'class' => ContractDetailFixture::class
            ],
            'product' => [
                'class' => ProductFixture::class
            ],
            'user' => [
                'class' => UserFixture::class
            ]
        ];
    }

    public function testSaveSuccess() {
        $model = new ProgrammaticChangePlan();

        $model->date = (new \DateTime('now'))->modify('+5 days');
        $model->contract_id = 1;
        $model->product_id = 4;
        $model->applied = false;
        $model->user_id = 1;

        expect('Not saved', $model->save())->true();
    }

    public function testSaveFailWithBeforeDate() {
        $model = new ProgrammaticChangePlan();

        $model->date = (new \DateTime('now'))->modify('-5 days');
        $model->contract_id = 1;
        $model->product_id = 4;
        $model->applied = false;
        $model->user_id = 1;

        expect('Not saved', $model->save())->false();
    }



}