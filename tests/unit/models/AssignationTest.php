<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 10/01/19
 * Time: 10:08
 */

use app\modules\westnet\ecopagos\models\Assignation;
use app\tests\fixtures\EcopagoFixture;
use app\tests\fixtures\CollectorFixture;

class AssignationTest extends \Codeception\Test\Unit
{

    public function _fixtures()
    {
        return [
            'ecopago' => [
                'class' => EcopagoFixture::class
            ],
            'collector' => [
                'class' => CollectorFixture::class
            ]
        ];
    }

    public function testInvalidWhenEmptyAndNew()
    {
        $model = new Assignation();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new Assignation([
            'ecopago_id' => 1,
            'collector_id' => 1,
            'date' => '2018-01-01',
            'time' => '10:57:00',
            'datetime' => '1465912635'
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new Assignation();
        expect('Invalid when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new Assignation([
            'ecopago_id' => 1,
            'collector_id' => 1,
            'date' => '2018-01-01',
            'time' => '10:57:00',
            'datetime' => '1465912635'
        ]);

        expect('Valid when full and new', $model->save())->true();
    }
}