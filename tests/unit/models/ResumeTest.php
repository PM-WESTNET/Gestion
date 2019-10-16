<?php
/**
 * Created by PhpStorm.
 * User: dexterlab10
 * Date: 14/06/19
 * Time: 16:00
 */
use app\modules\accounting\models\Resume;
use app\tests\fixtures\MoneyBoxAccountFixture;
use app\tests\fixtures\CompanyFixture;

class ResumeTest extends \Codeception\Test\Unit {
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _before()
    {

    }

    public function _after()
    {

    }

    public function _fixtures()
    {
        return [
            'money_box_account' => [
                'class' => MoneyBoxAccountFixture::class
            ],
            'company' => [
                'class' => CompanyFixture::class
            ]
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new Resume();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new Resume([
            'money_box_account_id' => 1,
            'name' => 'Resume1',
            'company_id' => 1
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new Resume();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new Resume([
            'money_box_account_id' => 1,
            'name' => 'Resume1',
            'company_id' => 1,
            'date' => (new \DateTime('now'))->format('d-m-Y'),
            'date_from' => (new \DateTime('now'))->format('d-m-Y'),
            'date_to' => (new \DateTime('now'))->format('d-m-Y'),
        ]);

        expect('Saved when full and new', $model->save())->true();
    }

    //TODO resto de la clase
}