<?php namespace models;

use app\modules\sale\modules\contract\models\ProgrammedPlanChange;
use app\tests\fixtures\ContractDetailFixture;
use app\tests\fixtures\ContractFixture;
use app\tests\fixtures\ProductFixture;
use app\tests\fixtures\UserFixture;

class ProgrammedPlanChangeTest extends \Codeception\Test\Unit
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

    public function testInvalidWhenNewAndEmppty()
    {
        $model = new ProgrammedPlanChange();

        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenNewAndFull()
    {
        $model = new ProgrammedPlanChange([
            'date' =>  (new \DateTime('now'))->modify('+5 days'),
            'contract_id' => 1,
            'product_id' => 4,
            'user_id' => 1,
        ]);

        expect('Valid when new and full', $model->validate())->true();
    }

    public function testSaveSuccess() {
        $model = new ProgrammedPlanChange();

        $model->date = (new \DateTime('now'))->modify('+5 days');
        $model->contract_id = 1;
        $model->product_id = 4;
        $model->applied = false;
        $model->user_id = 1;

        expect('Not saved', $model->save())->true();
    }

    public function testSaveFailWithBeforeDate() {
        $model = new ProgrammedPlanChange();

        $model->date = (new \DateTime('now'))->modify('-5 days');
        $model->contract_id = 1;
        $model->product_id = 4;
        $model->applied = false;
        $model->user_id = 1;

        expect('Not saved', $model->save())->false();
    }



}