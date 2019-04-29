<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 01/03/19
 * Time: 16:40
 */

use app\modules\mobileapp\v1\models\AppFailedRegister;

class AppFailedRegisterTest extends \Codeception\Test\Unit
{
    protected function _before()
    {

    }

    protected function _after()
    {

    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new AppFailedRegister();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new AppFailedRegister([
            'name' => 'AppFailedRegister',
            'phone' => '123456789'
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new AppFailedRegister();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new AppFailedRegister([
            'name' => 'AppFailedRegister',
            'phone' => '123456789'
        ]);

        expect('Saved when full and new', $model->save())->true();
    }

    public function testGetFullName()
    {
        $model = new AppFailedRegister([
            'name' => 'AppFailedRegister',
            'phone' => '123456789'
        ]);
        $model->save();

        expect('Get fullname', $model->getFullName())->equals('AppFailedRegister');
    }
}