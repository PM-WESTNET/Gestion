<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 12/12/18
 * Time: 12:15
 */

use app\modules\accounting\models\AccountMovement;
use app\modules\accounting\models\AccountingPeriod;
use app\tests\fixtures\PartnerDistributionModelFixture;
use app\tests\fixtures\CompanyFixture;
use app\modules\accounting\models\AccountMovementItem;
use app\tests\fixtures\AccountFixture;

class AccountMovementTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $accounting_period_id;

    public function _before()
    {
        $this->accounting_period_id = $this->tester->haveRecord(AccountingPeriod::class, [
            'name' => '2018'
        ]);
    }

    public function _fixtures()
    {
        return [
            'partner_distribution_model' => [
                'class' => PartnerDistributionModelFixture::class
            ],
            'company' => [
                'class' => CompanyFixture::class
            ],
            'account' => [
                'class' => AccountFixture::class
            ]
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new AccountMovement();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new AccountMovement([
            'accounting_period_id' => $this->accounting_period_id,
            'partner_distribution_model_id' => 1,     //Fixture,
            'date' => '2018-12-18'
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new AccountMovement();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new AccountMovement([
            'accounting_period_id' => $this->accounting_period_id,
            'partner_distribution_model_id' => 1,     //Fixture,
            'date' => '2018-12-18',
            'company_id' => 1
        ]);

        expect('Saved when full and new', $model->save())->true();
    }

    public function testGetDebt()
    {
        $model = new AccountMovement([
            'accounting_period_id' => $this->accounting_period_id,
            'partner_distribution_model_id' => 1,     //Fixture,
            'date' => '2018-12-18',
            'company_id' => 1
        ]);
        $model->save();

        $item_1 = new AccountMovementItem([
            'account_movement_id' => $model->account_movement_id,
            'account_id' => 1,       //Fixture
            'debit' => 300,
            'credit' => 0
        ]);
        $item_1->save();

        $item_2 = new AccountMovementItem([
            'account_movement_id' => $model->account_movement_id,
            'account_id' => 2,       //Fixture
            'debit' => 0,
            'credit' => 300
        ]);
        $item_2->save();

        expect('Get debt', $model->getDebt())->equals(300);
    }

    public function testGetCredit()
    {
        $model = new AccountMovement([
            'accounting_period_id' => $this->accounting_period_id,
            'partner_distribution_model_id' => 1,     //Fixture,
            'date' => '2018-12-18',
            'company_id' => 1
        ]);
        $model->save();

        $item_1 = new AccountMovementItem([
            'account_movement_id' => $model->account_movement_id,
            'account_id' => 1,       //Fixture
            'debit' => 0,
            'credit' => 300
        ]);
        $item_1->save();

        $item_2 = new AccountMovementItem([
            'account_movement_id' => $model->account_movement_id,
            'account_id' => 2,       //Fixture
            'debit' => 300,
            'credit' => 0
        ]);
        $item_2->save();

        expect('Get credit', $model->getCredit())->equals(300);
    }

    public function testValidateMovement()
    {
        $model = new AccountMovement([
            'accounting_period_id' => $this->accounting_period_id,
            'partner_distribution_model_id' => 1,     //Fixture,
            'date' => '2018-12-18',
            'company_id' => 1
        ]);
        $model->save();

        $item_1 = new AccountMovementItem([
            'account_movement_id' => $model->account_movement_id,
            'account_id' => 1,       //Fixture
            'debit' => 0,
            'credit' => 300
        ]);
        $item_1->save();

        $item_2 = new AccountMovementItem([
            'account_movement_id' => $model->account_movement_id,
            'account_id' => 2,       //Fixture
            'debit' => 300,
            'credit' => 0
        ]);
        $item_2->save();

        expect('Validate movement', $model->validateMovement())->true();
    }

    public function testGetTotal()
    {
        $model = new AccountMovement([
            'accounting_period_id' => $this->accounting_period_id,
            'partner_distribution_model_id' => 1,     //Fixture,
            'date' => '2018-12-18',
            'company_id' => 1
        ]);
        $model->save();

        $item_1 = new AccountMovementItem([
            'account_movement_id' => $model->account_movement_id,
            'account_id' => 1,       //Fixture
            'debit' => 0,
            'credit' => 300
        ]);
        $item_1->save();

        $item_2 = new AccountMovementItem([
            'account_movement_id' => $model->account_movement_id,
            'account_id' => 2,       //Fixture
            'debit' => 300,
            'credit' => 0
        ]);
        $item_2->save();

        expect('Validate movement', AccountMovement::getTotal([$item_1], 'debit'))->equals(0);
        expect('Validate movement', AccountMovement::getTotal([$item_1], 'credit'))->equals(300);
        expect('Validate movement', AccountMovement::getTotal([$item_2], 'debit'))->equals(300);
        expect('Validate movement', AccountMovement::getTotal([$item_2], 'credit'))->equals(0);
    }
}