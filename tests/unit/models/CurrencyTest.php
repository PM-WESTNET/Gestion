<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 12/12/18
 * Time: 12:15
 */

use app\modules\sale\models\Currency;

class CurrencyTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new Currency();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new Currency([
            'name' => 'Peso',
            'status' => 'enabled',
            'code' => 'ARS'
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new Currency();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new Currency([
            'name' => 'Peso',
            'status' => 'enabled',
            'code' => 'ARS'
        ]);

        expect('Saved when full and new', $model->save())->true();
    }
}