<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 28/02/19
 * Time: 16:51
 */

use app\modules\accounting\models\AccountingPeriod;
use yii\base\UserException;
use app\tests\fixtures\PartnerDistributionModelFixture;
use app\tests\fixtures\CompanyFixture;
use app\modules\accounting\models\AccountMovement;

class AccountingPeriodTest extends \Codeception\Test\Unit {
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $accounting_period_id;

    public function _before()
    {

    }

    public function _after()
    {

    }

    public function _fixtures()
    {
        return [
            'partner_distribution_model' => [
                'class' => PartnerDistributionModelFixture::class
            ],
            'company' => [
                'class' => CompanyFixture::class
            ]
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new AccountingPeriod();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new AccountingPeriod([
            'name' => 'Period'
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new AccountingPeriod();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new AccountingPeriod([
            'name' => 'Period'
        ]);

        expect('Saved when full and new', $model->save())->true();
    }

    public function testGetActivePeriod()
    {
        $model = new AccountingPeriod([
            'name' => 'Period',
            'status' => AccountingPeriod::STATE_CLOSED
        ]);
        $model->save();

        $this->tester->expectException(UserException::class, function() use ($model){
            echo 'Fails when thers no open accounting period';
            AccountingPeriod::getActivePeriod();
        });

        $model->updateAttributes(['status' => AccountingPeriod::STATE_OPEN]);

        expect('Get period', AccountingPeriod::getActivePeriod())->isInstanceOf(AccountingPeriod::class);
    }

    public function testClose()
    {
        $model = new AccountingPeriod([
            'name' => 'Period',
            'status' => AccountingPeriod::STATE_OPEN
        ]);
        $model->save();

        $movement = new AccountMovement([
            'accounting_period_id' => $model->accounting_period_id,
            'partner_distribution_model_id' => 1,     //Fixture,
            'date' => '2018-12-18',
            'company_id' => 1
        ]);
        $movement->save();

        expect('close return true', $model->close())->true();
        expect('Status is changed', $model->status)->equals(AccountingPeriod::STATE_CLOSED);
    }

    public function testGetWorkFlowAttr()
    {
        $model = new AccountingPeriod([
            'name' => 'Period',
            'status' => AccountingPeriod::STATE_OPEN
        ]);
        $model->save();

        $attribute = $model->getWorkflowAttr();
        expect('Return is a string', property_exists(get_class($model) , $attribute))->true();
    }

    public function testGetWorkFlowStates()
    {
        $model = new AccountingPeriod([
            'name' => 'Period',
            'status' => AccountingPeriod::STATE_OPEN
        ]);
        $model->save();

        $return_some_status = false;
        foreach ($model->getWorkFlowStates() as $workFlowState => $value) {
            if($workFlowState == AccountingPeriod::STATE_OPEN || $workFlowState == AccountingPeriod::STATE_CLOSED) {

            }
        }

        expect('Get workflow states function return some state', $return_some_status)->true();
    }
}