<?php
/**
 * Created by PhpStorm.
 * User: dexterlab10
 * Date: 07/02/2020
 * Time: 11:55
 */

use app\modules\sale\models\Customer;
use app\modules\sale\models\PublicityShape;
use app\tests\fixtures\CustomerFixture;

class PublicityShapeTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _fixtures()
    {
        return [
            'customer' => [
                'class' => CustomerFixture::class
            ],
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new PublicityShape();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new PublicityShape([
            'name' => 'Canal de publicidad',
            'status' => PublicityShape::STATUS_ENABLED,
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new PublicityShape();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new PublicityShape([
            'name' => 'Canal de publicidad',
            'status' => PublicityShape::STATUS_ENABLED,
        ]);

        expect('Saved when full and new', $model->save())->true();
    }

    public function testGetDeletable()
    {
        $model = new PublicityShape([
            'name' => 'Canal',
            'status' => PublicityShape::STATUS_ENABLED,
        ]);

        expect('Publicity shape saved', $model->save())->true();
        expect('Publicity shape can be deleted', $model->getDeletable())->true();

        $customer = Customer::findOne(45900);
        $customer->updateAttributes(['publicity_shape' => 'canal']);

        expect('Publicity shape cant be deleted', $model->getDeletable())->false();
    }

    public function testGetStatusForSelect()
    {
        $statuses = PublicityShape::getStatusForSelect();

        expect('Array has status key', array_key_exists(PublicityShape::STATUS_ENABLED, $statuses))->true();
    }

    public function testGetPublicityShapeForSelect()
    {
        $model = new PublicityShape([
            'name' => 'Canal',
            'status' => PublicityShape::STATUS_ENABLED,
        ]);
        $model->save();

        $publicity_shapes = PublicityShape::getPublicityShapeForSelect();

        \Codeception\Util\Debug::debug($publicity_shapes);

        expect('Publicity shape for select gets canal', array_key_exists($model->publicity_shape_id, $publicity_shapes))->true();

        $model->updateAttributes(['status' => PublicityShape::STATUS_DISABLED]);

        $publicity_shapes = PublicityShape::getPublicityShapeForSelect();

        expect('Publicity shape for select doesnt gets disabled publicity shapes', array_key_exists($model->publicity_shape_id, $publicity_shapes))->false();
    }
}