<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 12/03/19
 * Time: 17:08
 */

use app\tests\fixtures\EcopagoFixture;
use app\modules\westnet\ecopagos\models\Commission;

class CommissionTest extends \Codeception\Test\Unit
{

    public function _fixtures()
    {
        return [
            'ecopago' => [
                'class' => EcopagoFixture::class
            ],
        ];
    }

    public function testInvalidWhenEmptyAndNew()
    {
        $model = new Commission();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
       $model = new Commission([
           'ecopago_id' => 1,
           'create_datetime' => 1461604492
       ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new Commission();
        expect('Invalid when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new Commission([
            'ecopago_id' => 1,
            'create_datetime' => 1461604492
        ]);

        expect('Valid when full and new', $model->save())->true();
    }

    public function testFetchCommissionTypes()
    {
        $types = Commission::fetchCommissionTypes();

        expect('Type percentage exists', array_key_exists(Commission::COMMISSION_TYPE_PERCENTAGE, $types))->true();
        expect('Type fixed  exists', array_key_exists(Commission::COMMISSION_TYPE_FIXED, $types))->true();
    }

    public function testFetchSymbol()
    {
        $model = new Commission([
            'ecopago_id' => 1,
            'create_datetime' => 1461604492,
            'type' => Commission::COMMISSION_TYPE_FIXED
        ]);

        expect('Get symbol fixed', $model->fetchSymbol())->equals('$');

        $model->type = Commission::COMMISSION_TYPE_PERCENTAGE;
        expect('Get symbol percentage', $model->fetchSymbol())->equals('%');
    }
}