<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 14/01/19
 * Time: 15:08
 */

use app\modules\paycheck\models\Checkbook;
use app\tests\fixtures\MoneyBoxAccountFixture;

class CheckBookTest extends \Codeception\Test\Unit
{

    public function _fixtures()
    {
        return [
            'money_box_account' => [
                'class' => MoneyBoxAccountFixture::class,
            ],
        ];
    }

    public function testInvalidWhenEmptyAndNew()
    {
        $model = new CheckBook();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
       $model = new Checkbook([
           'start_number' => 1,
           'end_number' => 2,
           'money_box_account_id' => 1
       ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new CheckBook();
        expect('Invalid when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new CheckBook([
            'start_number' => 1,
            'end_number' => 2,
            'money_box_account_id' => 1
        ]);

        expect('Valid when full and new', $model->save())->true();
    }

    public function testFindActive()
    {
        $model = new CheckBook([
            'start_number' => 1,
            'end_number' => 2,
            'money_box_account_id' => 1,
        ]);
        $model->save();

        $active = Checkbook::findActive(1);
        expect('Active is empty', $active->all())->isEmpty();

        $model->updateAttributes(['enabled' => true]);
        expect('Active isnt empty', $active->all())->notEmpty();
    }

    public function testGetLastNumberUsed()
    {
        $model = new CheckBook([
            'start_number' => 1,
            'end_number' => 10,
            'money_box_account_id' => 1,
        ]);
        $model->save();

        expect('last used', $model->getLastNumberUsed())->equals(1);
    }

    public function testGetName()
    {
        $model = new CheckBook([
            'start_number' => 1,
            'end_number' => 10,
            'money_box_account_id' => 1,
        ]);
        $model->save();
        $string_name = Yii::t('app', 'From') . ': ' . $model->start_number . ' - ' . Yii::t('app', 'To') . ': ' . $model->end_number;
        expect('Get name', $model->getName())->equals($string_name);
    }
}