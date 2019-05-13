<?php

use app\modules\checkout\models\Track;

class TrackTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    

    public function _fixtures()
    {
    }

    protected function _after()
    {
    }

    public function testInvalidWhenEmptyAndNew()
    {
        $model = new Track();

        expect('Track not valid when new and full', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new Track([
            'name' => 'Semi directo',
            'description' => 'prueba'
        ]);

        expect('Track valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new Track();

        expect('Track not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new Track([
            'name' => 'Semi directo',
            'description' => 'prueba'
        ]);

        expect('Track saved when full and new', $model->save())->true();
    }

}