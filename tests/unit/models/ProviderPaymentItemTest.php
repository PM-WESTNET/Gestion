<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 12/12/18
 * Time: 12:15
 */

use app\tests\fixtures\ProviderFixture;
use app\tests\fixtures\CompanyFixture;
use app\modules\provider\models\ProviderPaymentItem;
use app\tests\fixtures\PartnerDistributionModelFixture;
use app\modules\provider\models\ProviderPayment;

class ProviderPaymentItemTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $provider_id;
    protected $provider_payment_id;

    public function _before()
    {
        $this->provider_payment_id = $this->tester->haveRecord(ProviderPayment::class,
            [
                "date" => "2018-11-30",
                "amount" => "500",
                "description" => "",
                "timestamp" => null,
                "balance" => null,
                "provider_id" => 149,
                "company_id" => 1,
                "status" => "created",
                "partner_distribution_model_id" => 1
            ]);
    }

    public function _fixtures(){
        return [
            'provider' => [
                'class' => ProviderFixture::class
            ],
            'company' => [
                'class' => CompanyFixture::class
            ],
            'partner_distribution_model' => [
                'class' => PartnerDistributionModelFixture::class
            ]
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new ProviderPaymentItem();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new ProviderPaymentItem([
            'provider_payment_id' => $this->provider_payment_id
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new ProviderPaymentItem();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new ProviderPaymentItem([
            'provider_payment_id' => $this->provider_payment_id
        ]);

        expect('Saved when full and new', $model->save())->true();
    }
}