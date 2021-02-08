<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 28/02/19
 * Time: 11:52
 */

use app\modules\accounting\models\MoneyBox;
use app\tests\fixtures\MoneyBoxTypeFixture;

class MoneyBoxTest extends \Codeception\Test\Unit
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

        ];
    }

    public function testInvalidWhenNewAndEmpty ()
    {
        $model = new MoneyBox();

        expect('Invalid when new and empty', $model->validate())->false();
    }

    public function testValidWhenNewAndFull ()
    {
        $model = new MoneyBox([
            'name' => 'MoneyBox',
            'money_box_type_id' => 1,
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenNewAndEmpty ()
    {
        $model = new MoneyBox();

        expect('Not save when new and empty', $model->save())->false();
    }

    public function testSaveWhenNewAndFull ()
    {
        $model = new MoneyBox([
            'name' => 'MoneyBox',
            'money_box_type_id' => 1,
        ]);

        expect('Save when full and new', $model->save())->true();
    }

    public function testFindByMoneyBoxType()
    {
        $model = new MoneyBox([
            'name' => 'MoneyBox 1',
            'money_box_type_id' => 2,
        ]);
        $model->save();

        expect('Find by type is empty', count($model->findByMoneyBoxType(1)->all()))->equals(0);

        $model = new MoneyBox([
            'name' => 'MoneyBox 2',
            'money_box_type_id' => 1,
        ]);
        $model->save();

        expect('Find by type is correct', count($model->findByMoneyBoxType(1)->all()))->equals(1);
        expect('Find by type is correct', $model->findByMoneyBoxType(1)->all()[0]->name)->equals('MoneyBox 2');
    }
}
