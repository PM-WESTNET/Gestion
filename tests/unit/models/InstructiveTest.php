<?php namespace models;

use app\modules\instructive\models\Instructive;
use app\modules\instructive\models\InstructiveCategory;
use app\tests\fixtures\InstructiveCategoryFixture;

class InstructiveTest extends \Codeception\Test\Unit
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

    public function _fixtures()
    {
        return [
            InstructiveCategoryFixture::class
        ];
    }

    public function testInvalidWhenNew()
    {
        $model = new Instructive();

        expect('Failed', $model->save())->false();
    }

    public function testFailSaveWhenNotStatus()
    {
        $model = new Instructive();
        $model->name = 'Test Me';

        expect('Failed', $model->save())->false();
    }

    public function testSuccessSave()
    {
        $model = new Instructive();
        $model->name = 'Test Me';
        $model->instructive_category_id = 1;

        expect('Failed', $model->save())->true();
    }
}