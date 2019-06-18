<?php
/**
 * Created by PhpStorm.
 * User: dexterlab10
 * Date: 14/06/19
 * Time: 16:20
 */
use app\modules\sale\models\Discount;
use app\tests\fixtures\ProductFixture;

class DiscountTest extends \Codeception\Test\Unit
{

    public function _fixtures()
    {
        return [
            'product' => [
                'class' => ProductFixture::class
            ],
        ];
    }

    public function testInvalidWhenEmptyAndNew()
    {
        $model = new Discount();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new Discount([
            'name' => 'Descuento',
            'periods' => 1,
            'apply_to' => Discount::APPLY_TO_CUSTOMER,
            'status' => Discount::STATUS_ENABLED,
            'type' => Discount::TYPE_FIXED,
            'value_from' => Discount::VALUE_FROM_TOTAL,
            'value' => '50'
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new Discount();
        expect('Invalid when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new Discount([
            'name' => 'Descuento',
            'periods' => 1,
            'apply_to' => Discount::APPLY_TO_CUSTOMER,
            'status' => Discount::STATUS_ENABLED,
            'type' => Discount::TYPE_FIXED,
            'value_from' => Discount::VALUE_FROM_TOTAL,
            'value' => '50'
        ]);

        expect('Valid when full and new', $model->save())->true();
    }

    public function testFindActiveByProduct()
    {
        expect('No discount found', count(Discount::findActiveByProduct(1, Discount::APPLY_TO_CUSTOMER)))->equals(0);

        $model = new Discount([
            'name' => 'Descuento',
            'periods' => 1,
            'apply_to' => Discount::APPLY_TO_CUSTOMER,
            'status' => Discount::STATUS_ENABLED,
            'type' => Discount::TYPE_FIXED,
            'value_from' => Discount::VALUE_FROM_TOTAL,
            'value' => '50',
            'from_date' => (new DateTime('now'))->format('d-m-Y'),
            'to_date' => (new DateTime('now'))->format('d-m-Y'),
            'product_id' => 1
        ]);
        $model->save();

        expect('One discount found', count(Discount::findActiveByProduct(1, Discount::APPLY_TO_CUSTOMER)))->equals(1);
    }
}