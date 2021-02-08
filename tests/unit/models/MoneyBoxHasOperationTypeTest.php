<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 28/02/19
 * Time: 11:52
 */

use app\tests\fixtures\OperationTypeFixture;
use app\modules\accounting\models\MoneyBoxHasOperationType;
use app\tests\fixtures\MoneyBoxFixture;
use app\tests\fixtures\AccountFixture;

class MoneyBoxHasOperationTypeTest extends \Codeception\Test\Unit
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
            'operation_type' => [
                'class' => OperationTypeFixture::class,
            ],
            'money_box' => [
                'class' => MoneyBoxFixture::class,
            ],
            'account' => [
                'class' => AccountFixture::class,
            ]
        ];

    }

    public function testInvalidWhenNewAndEmpty ()
    {
        $model = new MoneyBoxHasOperationType();

        expect('Invalid when new and empty', $model->validate())->false();
    }

    public function testValidWhenNewAndFull ()
    {
        $model = new MoneyBoxHasOperationType([
            'operation_type_id' => 1,
            'money_box_id' => 1,
            'account_id' => 2,
        ]);

        $model->save();

        \Codeception\Util\Debug::debug($model->getErrors());

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenNewAndEmpty ()
    {
        $model = new MoneyBoxHasOperationType();

        expect('Not save when new and empty', $model->save())->false();
    }

    public function testSaveWhenNewAndFull ()
    {
        $model = new MoneyBoxHasOperationType([
            'operation_type_id' => 1,
            'money_box_id' => 1,
            'account_id' => 2,
        ]);

        expect('Save when full and new', $model->save())->true();
    }

}
