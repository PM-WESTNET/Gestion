<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 10/01/19
 * Time: 10:08
 */

use app\modules\westnet\ecopagos\models\Status;

class StatusTest extends \Codeception\Test\Unit
{
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new Status();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new Status([
            'name' => 'enabled'
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new Status();
        expect('Invalid when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new Status([
            'name' => 'enabled'
        ]);

        expect('Valid when full and new', $model->save())->true();
    }
}