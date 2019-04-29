<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 28/02/19
 * Time: 11:52
 */

use app\modules\accounting\models\OperationType;
use app\modules\accounting\models\MoneyBox;
use app\tests\fixtures\MoneyBoxTypeFixture;
use app\modules\accounting\models\MoneyBoxHasOperationType;
use app\tests\fixtures\AccountFixture;

class OperationTypeTest extends \Codeception\Test\Unit
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
            'money_box_type' => [
                'class' => MoneyBoxTypeFixture::class,
            ],
            'account' => [
                'class' => AccountFixture::class,
            ]

        ];
    }

    public function testInvalidWhenNewAndEmpty ()
    {
        $model = new OperationType();

        expect('Invalid when new and empty', $model->validate())->false();
    }

    public function testValidWhenNewAndFull ()
    {
        $model = new OperationType([
            'name' => 'AFIP',
            'code' => 'AFIP'
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenNewAndEmpty ()
    {
        $model = new OperationType();

        expect('Not save when new and empty', $model->save())->false();
    }

    public function testSaveWhenNewAndFull ()
    {
        $model = new OperationType([
            'name' => 'AFIP',
            'code' => 'AFIP'
        ]);

        expect('Save when full and new', $model->save())->true();
    }

    public function testFindRestOfMoneyBox()
    {
        $model = new OperationType([
            'name' => 'AFIP',
            'code' => 'AFIP'
        ]);
        $model->save();

        $money_box = new MoneyBox([
            'name' => 'MoneyBox',
            'money_box_type_id' => 1,
        ]);
        $money_box->save();

        expect('Operation tpe not in', count(OperationType::findRestOfMoneyBox($money_box->money_box_id)->all()))->equals(1);

        $mhot = new MoneyBoxHasOperationType([
            'operation_type_id' => $model->operation_type_id,
            'money_box_id' => $money_box->money_box_id,
            'account_id' => 1
        ]);
        $mhot->save();

        expect('Operation tpe not in', count(OperationType::findRestOfMoneyBox($money_box->money_box_id)->all()))->equals(0);
    }
}
