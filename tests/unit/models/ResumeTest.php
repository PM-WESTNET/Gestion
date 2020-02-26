<?php 
class ResumeTest extends \Codeception\Test\Unit
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
           \app\tests\fixtures\ResumeFixture::class,
           //\app\tests\fixtures\ResumeItemFixture::class
        ];
    }


    public function testInvalidWhenNew()
    {

        $model = new \app\modules\accounting\models\Resume();

        expect('Saved', $model->save())->false();
    }

    public function testSuccessSave()
    {
        $model= new \app\modules\accounting\models\Resume();
        $model->name = "TestMeYou";
        $model->money_box_account_id = 42;
        $model->status = 'draft';
        $model->company_id = 2;
        $model->date = '22-03-2019';
        $model->date_from = '01-02-2019';
        $model->date_to = '28-02-2019';

        $save = $model->save();

        \Codeception\Util\Debug::debug(print_r($model->getErrors(),1));

        expect('Fail', $save)->true();
    }

    public function testFormatDateWithSlashDMY()
    {
        $date = '20/08/2019';

        $resumeImport= new \app\modules\accounting\components\ResumeImporter('file', []);

        expect('Failed', $resumeImport->formatDate($date))->equals('20-08-2019');

    }

    public function testFormatDateWithSlashYMD()
    {
        $date = '2019/08/20';

        $resumeImport= new \app\modules\accounting\components\ResumeImporter('file', []);

        expect('Failed', $resumeImport->formatDate($date))->equals('20-08-2019');

    }

    public function testFormatDateWithDashDMY()
    {
        $date = '20-08-2019';

        $resumeImport= new \app\modules\accounting\components\ResumeImporter('file', []);

        expect('Failed', $resumeImport->formatDate($date))->equals('20-08-2019');

    }

    public function testFormatDateWithDashYMD()
    {
        $date = '2019-08-20';

        $resumeImport= new \app\modules\accounting\components\ResumeImporter('file', []);

        expect('Failed', $resumeImport->formatDate($date))->equals('20-08-2019');

    }

    public function testFormatDateWithoutDashAndSlashDMY()
    {
        $date = '20082019';

        $resumeImport= new \app\modules\accounting\components\ResumeImporter('file', []);

        expect('Failed', $resumeImport->formatDate($date))->equals('20-08-2019');

    }

    public function testFormatDateWithoutDashAndSlashYMD()
    {
        $date = '20190820';

        $resumeImport= new \app\modules\accounting\components\ResumeImporter('file', []);

        expect('Failed', $resumeImport->formatDate($date))->equals('20-08-2019');

    }

}