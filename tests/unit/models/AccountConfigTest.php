<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 01/03/19
 * Time: 12:17
 */

use app\modules\accounting\models\AccountConfig;
use app\tests\fixtures\AccountFixture;
use app\modules\accounting\models\AccountConfigHasAccount;
use app\tests\fixtures\CurrencyFixture;

class AccountConfigTest extends \Codeception\Test\Unit
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
            'account' => [
                'class' => AccountFixture::class
            ],
            'currency' => [
                'class' => CurrencyFixture::class
            ],
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new AccountConfig();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new AccountConfig([
            'name' => 'Facturar',
            'class' => 'app\modules\sale\models\Bill',
            'classMovement' => 'app\modules\accounting\components\impl\BillMovement'
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new AccountConfig();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new AccountConfig([
            'name' => 'Facturar',
            'class' => 'app\modules\sale\models\Bill',
            'classMovement' => 'app\modules\accounting\components\impl\BillMovement'
        ]);

        expect('Saved when full and new', $model->save())->true();
    }

    public function testAddAccount()
    {
        $model = new AccountConfig([
            'name' => 'Facturar',
            'class' => 'app\modules\sale\models\Bill',
            'classMovement' => 'app\modules\accounting\components\impl\BillMovement'
        ]);
        $model->save();

        $acha = new AccountConfigHasAccount([
            'account_config_id' => $model->account_config_id,
            'account_id' => 1,
        ]);
        $acha->save();

        $result = $model->addAccount([
            'account_config_id' => $acha->account_config_id,
            'account_id' => 1,
            'attrib' => 'total'
        ]);

        expect('Result is AccountConfigHasAccount', $result)->isInstanceOf(AccountConfigHasAccount::class);

        $result = $model->addAccount([
            'account_config_id' => '',
            'account_id' => 1,
            'attrib' => 'total'
        ]);

        expect('Result is AccountConfigHasAccount with empty account_config_id', $result)->isInstanceOf(AccountConfigHasAccount::class);
    }

    public function testGetModelAttribs()
    {
        $model = new AccountConfig([
            'name' => 'Facturar',
            'class' => 'app\modules\sale\models\Bill',
            'classMovement' => 'app\modules\accounting\components\impl\BillMovement'
        ]);
        $model->save();

        expect('Attribute for payment is total', array_key_exists('total', $model->getModelAttribs()))->true();
        expect('Attribute for payment is total',  $model->getModelAttribs()['total'])->equals('Total');

        $model->updateAttributes(['class' => 'app\modules\checkout\models\Payment']);
        expect('Attribute for payment is total', array_key_exists('total', $model->getModelAttribs()))->true();
        expect('Attribute for payment is total',  $model->getModelAttribs()['total'])->equals('Total');

        $model->updateAttributes(['class' => 'app\modules\provider\models\ProviderBill']);
        expect('Attribute for ProviderBill is totalItems', array_key_exists('totalItems', $model->getModelAttribs()))->true();
        expect('Attribute for ProviderBill is total', array_key_exists('total', $model->getModelAttribs()))->true();
        expect('Attribute for ProviderBill is rest', array_key_exists('rest', $model->getModelAttribs()))->true();

        $model->updateAttributes(['class' => 'app\modules\provider\models\ProviderPayment']);
        expect('Attribute for payment is total', array_key_exists('total', $model->getModelAttribs()))->true();
        expect('Attribute for payment is total',  $model->getModelAttribs()['total'])->equals('Total');

        $model->updateAttributes(['class' => 'app\modules\westnet\ecopagos\models\BatchClosure']);
        expect('Attribute for payment is total',  $model->getModelAttribs()[0])->equals('total');

        $model->updateAttributes(['class' => 'app\modules\paycheck\models\Paycheck']);
        expect('Attribute for payment is total', array_key_exists('total', $model->getModelAttribs()))->true();
        expect('Attribute for payment is total',  $model->getModelAttribs()['total'])->equals('Total');
    }
}