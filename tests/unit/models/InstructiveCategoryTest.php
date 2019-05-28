<?php namespace models;

use app\modules\instructive\models\InstructiveCategory;

class InstructiveCategoryTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testInvalidWhenNew()
    {
        $model = new InstructiveCategory();

        expect('Failed', $model->save())->false();
    }

    public function testFailSaveWhenNotStatus()
    {
        $model = new InstructiveCategory();
        $model->name = 'Test Me';

        expect('Failed', $model->save())->false();
    }

    public function testSuccessSave()
    {
        $model = new InstructiveCategory();
        $model->name = 'Test Me';
        $model->status = InstructiveCategory::STATUS_ENABLED;

        expect('Failed', $model->save())->true();
    }
}