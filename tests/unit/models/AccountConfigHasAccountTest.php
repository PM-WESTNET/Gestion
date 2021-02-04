<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 01/03/19
 * Time: 12:17
 */

use app\modules\accounting\models\AccountConfigHasAccount;
use app\tests\fixtures\AccountConfigFixture;
use app\tests\fixtures\AccountFixture;


class AccountConfigHasAccountTest extends \Codeception\Test\Unit
{
    protected function _before()
    {

    }

    protected function _after()
    {

    }

    public function _fixtures()
    {
        return [
            'account_config' => [
                'class' => AccountConfigFixture::class,
            ],
            'account' => [
                'class' => AccountFixture::class,
            ],
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new AccountConfigHasAccount();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new AccountConfigHasAccount([
            'account_config_id' => 1,
            'account_id' => 1,
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new AccountConfigHasAccount();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new AccountConfigHasAccount([
            'account_config_id' => 1,
            'account_id' => 1,
        ]);

        expect('Saved when full and new', $model->save())->true();
    }
}