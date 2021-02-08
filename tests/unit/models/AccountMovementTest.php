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
            'name' => '2018',
            'date_from' => '2018-01-01',
            'date_to' => '2018-12-31',
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
            ],
            'account_movement_item' => [
                'class' => \app\tests\fixtures\AccountMovementItemFixture::class
            ],
            'money_box_account' => [
                'class' => \app\tests\fixtures\MoneyBoxAccountFixture::class
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

    /*
      public function getWorkflowAttr()
    {
        return "status";
    }

    public function getWorkflowStates()
    {
        return [
            self::STATE_DRAFT => [
                self::STATE_CLOSED,
                self::STATE_BROKEN
            ],
            self::STATE_BROKEN => [
                self::STATE_DRAFT,
            ],
        ];
    }
    public function getWorkflowCreateLog(){}

    public function close()
    {
        try {
            if ($this->can(AccountMovement::STATE_CLOSED)) {
                foreach($this->accountMovementItems as $item) {
                    $item->changeState(AccountMovement::STATE_CLOSED);
                }
                return $this->changeState(AccountMovement::STATE_CLOSED);
            } else {
                throw new \Exception('Cant Close');
            }

            return true;
        } catch(\Exception $ex) {
            return false;
        }
    }

    public function getDebt()
    {
        $total = 0;
        foreach($this->accountMovementItems as $item) {
            $total += $item->debit;
        }

        return $total;
    }
    public function getCredit()
    {
        $total = 0;
        foreach($this->accountMovementItems as $item) {
            $total += $item->credit;
        }

        return $total;
    }

    public function validateMovement()
    {
        if(empty($this->accountMovementItems)) {
            return false;
        }
        $debit = 0;
        $credit = 0;
        foreach ($this->accountMovementItems as $item) {
            $debit += $item->debit;
            $credit += $item->credit;
        }

        return (round($debit,2)==round($credit,2));
    }

    public static function getTotal($models, $attribute)
    {
        $total = 0;

        foreach ($models as $model) {
            $total += $model[$attribute];
        }

        return $total;

    }
     */

    public function testCanDeleteSuccessCommonAccount()
    {
        $movement = AccountMovement::findOne(4);

        expect('Cant delete', $movement->getDeletable())->true();
    }

    public function testCanDeleteSuccessDailyAccount()
    {
        $movement = AccountMovement::findOne(6);

        expect('Cant delete', $movement->getDeletable())->true();
    }

    public function testCanDeleteFailForAfterMovementsClosed()
    {
        $movement = AccountMovement::findOne(2);

        expect('Deleted', $movement->getDeletable())->false();
    }

    public function testCanDeleteFailForDailyBoxClosed()
    {
        $movement = AccountMovement::findOne(5);

        expect('Deleted', $movement->getDeletable())->false();
    }
}