<?php
/**
 * Created by PhpStorm.
 * User: dexterlab10
 * Date: 03/07/19
 * Time: 16:21
 */

use app\modules\sale\models\ProductDiscount;
use app\tests\fixtures\ProductFixture;

class ProductDiscountTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _fixtures()
    {
        return [
            'product' => [
                'class' => ProductFixture::class
            ]
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new ProductDiscount();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new ProductDiscount([
            'product_id' => 4
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new ProductDiscount();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new ProductDiscount([
            'product_id' => 4
        ]);

        expect('Saved when full and new', $model->save())->true();
    }

}