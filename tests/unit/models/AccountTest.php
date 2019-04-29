<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 27/02/19
 * Time: 16:29
 */

use app\modules\accounting\models\Account;
use yii\helpers\ArrayHelper;
use app\modules\accounting\models\MoneyBoxAccount;
use app\tests\fixtures\MoneyBoxFixture;
use app\tests\fixtures\CurrencyFixture;
use app\tests\fixtures\CompanyFixture;

class AccountTest extends \Codeception\Test\Unit {
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
            'money_box' => [
                'class' => MoneyBoxFixture::class
            ],
            'currency' => [
                'class' => CurrencyFixture::class
            ],
            'company' => [
                'class' => CompanyFixture::class
            ]
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new Account();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new Account([
            'name' => 'Cuenta 01'
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new Account();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new Account([
            'name' => 'Cuenta 01'
        ]);

        expect('Saved when full and new', $model->save())->true();
    }

    public function testGetForSelect()
    {
        $model = new Account([
            'name' => 'Cuenta 01',
            'is_usable' => 1,
            'parent_account_id' => null,
        ]);
        $model->save();

        $model1 = new Account([
            'name' => 'Cuenta 02',
            'is_usable' => 1,
            'parent_account_id' => $model->account_id
        ]);
        $model1->save();

        $model->updateTree();
        $select = Account::getForSelect();

        $array_map = ArrayHelper::map($select, 'account_id', 'name');

        expect('Cuenta 01 is in array', array_key_exists($model->account_id, $array_map))->true();
        expect('Cuenta 02 is in array', array_key_exists($model1->account_id, $array_map))->true();
        expect('Cuenta 01', $array_map[$model->account_id])->equals('Cuenta 01');
        expect('Cuenta 02', $array_map[$model1->account_id])->equals('&nbsp;&nbsp;Cuenta 02');
    }

//    public function testGetOnlyAvailableForSelect()
//    {
//        $model = new Account([
//            'name' => 'Cuenta 01',
//            'is_usable' => 1,
//            'parent_account_id' => null,
//        ]);
//        $model->save();
//
//        $model1 = new Account([
//            'name' => 'Cuenta 02',
//            'is_usable' => 1,
//            'parent_account_id' => $model->account_id
//        ]);
//        $model1->save();
//
//        $model->updateTree();
//
//        \Codeception\Util\Debug::debug($model->lft);
//
//        $money_box_account = new MoneyBoxAccount([
//            'number' => '01',
//            'money_box_id' => 1,
//            'currency_id' => 1,
//            'company_id' => 1,
//            'account_id' => $model1->account_id
//        ]);
//        $money_box_account->save();
//
//        $select = Account::getOnlyAvailableForSelect();
//
//        $array_map = ArrayHelper::map($select, 'account_id', 'name');
//
//        expect('Cuenta 01 is in array', array_key_exists($model->account_id, $array_map))->true();
//        expect('Cuenta 02 is not in array', array_key_exists($model1->account_id, $array_map))->false();
//        expect('Cuenta 01', $array_map[$model->account_id])->equals('Cuenta 01');
//    }

    //TODO Falta testUpdateTree() y testUpdateCode()
}