<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 28/02/19
 * Time: 11:52
 */

use app\modules\accounting\models\MoneyBoxAccount;
use app\tests\fixtures\MoneyBoxFixture;
use app\tests\fixtures\CurrencyFixture;
use app\tests\fixtures\CompanyFixture;
use app\tests\fixtures\AccountFixture;
use app\modules\accounting\models\AccountMovement;
use app\modules\accounting\models\AccountingPeriod;
use app\tests\fixtures\PartnerDistributionModelFixture;

class MoneyBoxAccountTest extends \Codeception\Test\Unit
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

    public function _fixtures(){
        return [
            'money_box' => [
                'class' => MoneyBoxFixture::class,
            ],
            'currency' => [
                'class' => CurrencyFixture::class,
            ],
            'company' => [
                'class' => CompanyFixture::class
            ],
            'account' => [
                'class' => AccountFixture::class,
            ],
            'partner_distribution_model' => [
                'class' => PartnerDistributionModelFixture::class,
            ]
        ];
    }

    public function testInvalidWhenNewAndEmpty ()
    {
        $model = new MoneyBoxAccount();

        expect('Invalid when new and empty', $model->validate())->false();
    }

    public function testValidWhenNewAndFull ()
    {
        $model = new MoneyBoxAccount([
            'number' => 'MoneyBoxAccount 1',
            'money_box_id' => 1,
            'currency_id' => 1,
            'company_id' => 1,
        ]);

        $model->validate();

        \Codeception\Util\Debug::debug($model->getErrors());

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenNewAndEmpty ()
    {
        $model = new MoneyBoxAccount();

        expect('Not save when new and empty', $model->save())->false();
    }

    public function testSaveWhenNewAndFull ()
    {
        $model = new MoneyBoxAccount([
            'number' => 'MoneyBoxAccount 1',
            'money_box_id' => 1,
            'currency_id' => 1,
            'company_id' => 1,
        ]);

        expect('Save when full and new', $model->save())->true();
    }

    public function testCloseDailyBox()
    {
        $model = new MoneyBoxAccount([
            'number' => 'MoneyBoxAccount 1',
            'money_box_id' => 1,
            'currency_id' => 1,
            'company_id' => 1,
            'account_id' => 1,
        ]);
        $model->save();

        expect('close daily when is not a small box', $model->closeDailyBox((new \DateTime('now'))))->false();

        $model->updateAttributes(['small_box' => 1]);

        expect('Close daily when is small box', $model->closeDailyBox((new \DateTime('now'))))->true();
        expect('daily_box_last_closing_date is today', $model->daily_box_last_closing_date)->equals((new \DateTime('now'))->format('Y-m-d'));
    }

    public function testIsDailyBoxClosed()
    {
        $model = new MoneyBoxAccount([
            'number' => 'MoneyBoxAccount 1',
            'money_box_id' => 1,
            'currency_id' => 1,
            'company_id' => 1,
            'account_id' => 1,
        ]);
        $model->save();

        $period = new AccountingPeriod([
            'name' => 'Mes',
            'date_from' => (new \DateTime('now'))->format('Y-m-d')
        ]);
        $period->save();

        $yesterday = (new \DateTime('now'))->modify('-1 month')->format('Y-m-d');
        $movement = new AccountMovement([
            'accounting_period_id' => $period->accounting_period_id,
            'partner_distribution_model_id' => 1,
            'date' => $yesterday,
            'company_id' => 1,
            'daily_money_box_account_id' => $model->money_box_account_id,
            'status' => AccountMovement::STATE_DRAFT
        ]);
        $movement->save();

        $model->isDailyBoxClosed((new \DateTime('now')));

        expect('Its not closed', $model->isDailyBoxClosed((new \DateTime('now'))))->false();
    }

    //TODO no logro que encuentre el movimiento
//    public function testDailyBoxPendingClose()
//    {
//        $model = new MoneyBoxAccount([
//            'number' => 'MoneyBoxAccount 1',
//            'money_box_id' => 1,
//            'currency_id' => 1,
//            'company_id' => 1,
//            'account_id' => 1,
//        ]);
//        $model->save();
//
//        $period = new AccountingPeriod([
//            'name' => 'Mes'
//        ]);
//        $period->save();
//
//        $yesterday = (new \DateTime('now'))->modify('-1 month')->format('Y-m-d');
//        $movement = new AccountMovement([
//            'accounting_period_id' => $period->accounting_period_id,
//            'partner_distribution_model_id' => 1,
//            'date' => $yesterday,
//            'company_id' => 1,
//            'daily_money_box_account_id' => $model->money_box_account_id,
//            'status' => AccountMovement::STATE_DRAFT
//        ]);
//        $movement->save();
//
//        $a = $model->dailyBoxPendingClose((new \DateTime('now')));
//
//        \Codeception\Util\Debug::debug($a);
//        expect('Pending box is true', $model->dailyBoxPendingClose((new \DateTime('now'))))->true();
//    }
}
