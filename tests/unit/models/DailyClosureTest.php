<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 10/01/19
 * Time: 10:08
 */

use app\modules\westnet\ecopagos\models\DailyClosure;
use app\tests\fixtures\EcopagoFixture;
use app\tests\fixtures\CashierFixture;

class DailyClosureTest extends \Codeception\Test\Unit
{

    public function _fixtures()
    {
        return [
            'ecopago' => [
                'class' => EcopagoFixture::class
            ],
            'cashier' => [
                'class' => CashierFixture::class
            ]
        ];
    }

    public function testInvalidWhenEmptyAndNew()
    {
        $model = new DailyClosure();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new DailyClosure([
            'cashier_id' => 1,      //Fixture
            'ecopago_id' => 1,      //Fixture
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new DailyClosure();
        expect('Invalid when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new DailyClosure([
            'cashier_id' => 1,      //Fixture
            'ecopago_id' => 1,      //Fixture
        ]);

        expect('Valid when full and new', $model->save())->true();
    }
}