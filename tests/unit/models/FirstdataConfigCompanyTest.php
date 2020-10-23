<?php namespace models;

use app\modules\firstdata\models\FirstdataCompanyConfig;
use app\tests\fixtures\CompanyFixture;
use app\tests\fixtures\FirstdataConfigCompanyFixture;
use app\tests\fixtures\CustomerFixture;

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

    public function _fixtures() {
        return [
            [
                'class' => CompanyFixture::class
            ],
            
            [
                'class' => FirstdataConfigCompanyFixture::class
            ],
        ];
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