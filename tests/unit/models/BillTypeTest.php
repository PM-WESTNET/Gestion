<?php

use app\modules\sale\models\BillType;

class BillTypeTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public $bill_id;

    public function _fixtures()
    {
    }

    public function testInvalidAndEmpty()
    {
        $model = new BillType();
        expect('Invalid when empty', $model->validate())->false();
    }

    public function testValidAndFull()
    {
        $model = new BillType([
            'name' => 'Billtype',
            'code' => 1,
            'multiplier' => 1,
            'class' => 'app\modules\sale\models\bills\Bill'
        ]);

        expect('Valid when empty', $model->validate())->true();
    }

    public function testNotSaveWhenEmpty()
    {
        $model = new BillType();

        expect('Not save when empty', $model->save())->false();
    }

    public function testSaveWhenNew() {
        $model = new BillType([
            'name' => 'Billtype',
            'code' => 1,
            'multiplier' => 1,
            'class' => 'app\modules\sale\models\bills\Bill'
        ]);

        expect('Save when full', $model->save())->true();
    }
}