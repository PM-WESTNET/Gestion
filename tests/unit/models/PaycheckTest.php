<?php

namespace models;

use app\modules\accounting\models\AccountMovementRelation;
use app\modules\paycheck\models\Paycheck;
use app\tests\fixtures\AccountMovementFixture;
use app\tests\fixtures\PaycheckFixture;

class PaycheckTest extends \Codeception\Test\Unit
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

    public function _fixtures() {
        return [
            [
                'class' => PaycheckFixture::class
            ],
            [
                'class' => AccountMovementFixture::class
            ],

        ];
    }

    // tests
    public function testOnCommitedCreateAccountMovement()
    {
        $paycheck = Paycheck::findOne(1);
        $accountMovementRelation = null;

        if (empty($paycheck)) {
            expect('Paycheck not found', false)->true();
            return;
        }

        if ($paycheck->changeState(Paycheck::STATE_COMMITED)) {
            $accountMovementRelation = AccountMovementRelation::find()
                ->andWhere([
                    'class' => 'app\modules\paycheck\models\Paycheck',
                    'model_id' => 1
                ])->one();

        }

        expect('Movement not created', $accountMovementRelation)->notNull();
    }
}