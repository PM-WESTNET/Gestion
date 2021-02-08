<?php
/**
 * Created by PhpStorm.
 * User: Dexterlab10
 * Date: 17/10/19
 * Time: 11:43
 */

use app\tests\fixtures\CustomerFixture;
use app\modules\westnet\models\PaymentExtensionHistory;

class PaymentExtensionHistoryTest extends \Codeception\Test\Unit
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
            'customer' => [
                'class' => CustomerFixture::class,
            ],
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new PaymentExtensionHistory();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new PaymentExtensionHistory([
            'from' => PaymentExtensionHistory::FROM_APP,
            'customer_id' => 45900
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new PaymentExtensionHistory();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new PaymentExtensionHistory([
            'from' => PaymentExtensionHistory::FROM_APP,
            'customer_id' => 45900
        ]);

        expect('Saved when full and new', $model->save())->true();
    }

    public function testCreatePaymentExtensionHistory()
    {
        expect('Successfully created', PaymentExtensionHistory::createPaymentExtensionHistory(45900, PaymentExtensionHistory::FROM_APP))->true();
        expect('Successfully created', PaymentExtensionHistory::createPaymentExtensionHistory(45900, PaymentExtensionHistory::FROM_IVR))->true();
    }

    public function testGetFromTypesForSelect()
    {
        $select_from_types = PaymentExtensionHistory::getFromTypesForSelect();

        expect('App exists', array_key_exists(PaymentExtensionHistory::FROM_APP, $select_from_types))->true();
        expect('Ivr exists', array_key_exists(PaymentExtensionHistory::FROM_IVR, $select_from_types))->true();
    }
}