<?php
/**
 * Created by PhpStorm.
 * User: dexterlab10
 * Date: 18/06/19
 * Time: 16:19
 */

use app\modules\sale\models\FundingPlan;
use app\tests\fixtures\ProductFixture;
use Codeception\Test\Unit;

class FundingPlanTest extends Unit
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
        $model = new FundingPlan();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new FundingPlan([
            'product_id' => 1,
            'qty_payments' => 2,
            'amount_payment' => 100,
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new FundingPlan();
        expect('Invalid when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new FundingPlan([
            'product_id' => 1,
            'qty_payments' => 2,
            'amount_payment' => 100,
        ]);

        expect('Valid when full and new', $model->save())->true();
    }
}