<?php
/**
 * Created by PhpStorm.
 * User: dexterlab10
 * Date: 18/06/19
 * Time: 14:51
 */
use app\modules\sale\models\DiscountEvent;
use app\tests\fixtures\ProductFixture;

class DiscountEventTest extends \Codeception\Test\Unit
{

    public function testValidWhenFullAndNew()
    {
        $model = new DiscountEvent([
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new DiscountEvent([

        ]);

        expect('Valid when full and new', $model->save())->true();
    }
}