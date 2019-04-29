<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 14/01/19
 * Time: 15:08
 */

use app\modules\westnet\ecopagos\models\BatchClosure;
use app\tests\fixtures\EcopagoFixture;
use app\tests\fixtures\CollectorFixture;

class BatchClosureTest extends \Codeception\Test\Unit
{

    public function _fixtures()
    {
        return [
            'ecopago' => [
                'class' => EcopagoFixture::class
            ],
            'collector' => [
                'class' => CollectorFixture::class
            ]
        ];
    }

    public function testInvalidWhenEmptyAndNew()
    {
        $model = new BatchClosure();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new BatchClosure([
            'ecopago_id' => 1,
            'collector_id' => 1,
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new BatchClosure();
        expect('Invalid when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new BatchClosure([
            'ecopago_id' => 1,
            'collector_id' => 1,
        ]);

        expect('Valid when full and new', $model->save())->true();
    }
/*
    public function testPreview()
    {
        $model = new BatchClosure([
            'ecopago_id' => 1,
            'collector_id' => 1,
            'datetime' => '1547489943'
        ]);
        $model->save();
        $model->preview();

        expect('Preview without previous batchClosure', $model->last_batch_closure_id)->isEmpty();

        $model2 = new BatchClosure([
            'ecopago_id' => 1,
            'collector_id' => 1,
            'datetime' => '1547317135'
        ]);
        $model2->save();

        $model->preview();

        expect('Preview with previous batchClosure', $model->last_batch_closure_id)->notEmpty();
        expect('Preview with previous batchClosure', $model->last_batch_closure_id)->equals($model2->batch_closure_id);

    //TODO test poayout y fixture
    }*/
}