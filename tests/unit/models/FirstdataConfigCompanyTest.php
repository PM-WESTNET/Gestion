<?php namespace models;

use app\modules\firstdata\models\FirstdataCompanyConfig;

class FirstdataConfigCompanyTest extends \Codeception\Test\Unit
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

    public function testSaveFailOnNew() {

        $config = new FirstdataCompanyConfig([
        ]);

        expect('Not save', $config->save())->false();
    }

    public function testSaveSuccess() {

        $config = new FirstdataCompanyConfig([
            'company_id' => 1,
            'commision_number' => '1234565'
        ]);

        expect('Not save', $config->save())->true();
    }

    public function testSaveFailOnCompanyNull() {

        $config = new FirstdataCompanyConfig([
            'commision_number' => '1234565'
        ]);

        expect('Not save', $config->save())->false();
    }

    public function testSaveFailOnNumberNull() {

        $config = new FirstdataCompanyConfig([
            'company_id' => 1,
        ]);

        expect('Not save', $config->save())->false();
    }
}