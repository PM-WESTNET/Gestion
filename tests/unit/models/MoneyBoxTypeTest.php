<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 28/02/19
 * Time: 11:57
 */

use app\modules\accounting\models\MoneyBoxType;

class MoneyBoxTypeTest extends \Codeception\Test\Unit
{
    protected function _before()
    {

    }

    protected function _after()
    {

    }

    public function testInvalidWhenNewAndEmpty()
    {
        $model = new MoneyBoxType();

        expect('Invalid when new and empty', $model->validate())->false();
    }

    public function testValidWhenNewAndFull()
    {
        $model = new MoneyBoxType([
            'name' => 'MoneyBoxType'
        ]);

        expect('Valid when new and full', $model->validate())->true();
    }

    public function testNotSaveWhenNewAndEmpty()
    {
        $model = new MoneyBoxType();

        expect('Not save when new and empty', $model->save())->false();
    }

    public function testSaveWhenNewAndFull()
    {
        $model = new MoneyBoxType([
            'name' => 'MoneyBoxType'
        ]);

        expect('Save when new and full', $model->save())->true();
    }
}