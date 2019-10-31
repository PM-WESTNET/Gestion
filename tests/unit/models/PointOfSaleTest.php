<?php
/**
 * Created by PhpStorm.
 * User: dexterlab10
 * Date: 03/07/19
 * Time: 16:17
 */

use app\modules\invoice\models\PointOfSale;

class PointOfSaleTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _fixtures()
    {
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new PointOfSale();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new PointOfSale([
            'number' => 1
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new PointOfSale();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new PointOfSale([
            'number' => 1
        ]);

        expect('Saved when full and new', $model->save())->true();
    }

}