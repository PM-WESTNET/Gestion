<?php

use app\tests\fixtures\CompanyFixture;
use app\tests\fixtures\NodeFixture;
use app\modules\westnet\models\EmptyAds;
use app\tests\fixtures\CompanyHasPaymentTrackFixture;
use app\tests\fixtures\AdsPercentagePerCompanyFixture;
use app\modules\cobrodigital\models\PaymentCard;
use app\tests\fixtures\PaymentCardFixture;
use app\modules\sale\models\Company;
use app\modules\westnet\models\Node;

class EmptyAdsTest extends \Codeception\Test\Unit
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

    public function _fixtures(){
        return [
            'company' => [
                'class' => CompanyFixture::class,
            ],
            'node' => [
                'class' => NodeFixture::class
            ],
            'company_has_payment_track' => [
                'class' => CompanyHasPaymentTrackFixture::class
            ],
            'ads_percentage_per_company' => [
                'class' => AdsPercentagePerCompanyFixture::class
            ],
            'payment_card' => [
                'class' => PaymentCardFixture::class
            ]
        ];
    }

    public function testInvalidWhenEmptyAndNew()
    {
        $model = new EmptyAds();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new EmptyAds([
            'code' => '236',
            'payment_code' => '456',
            'node_id' => 1
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new EmptyAds();
        expect('Invalid when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new EmptyAds([
            'code' => '236',
            'payment_code' => '456',
            'node_id' => 1
        ]);

        expect('Valid when full and new', $model->save())->true();
    }

    public function testCanCreateEmptyAds()
    {
        $company = Company::findOne(1);
        $can_create = EmptyAds::canCreateEmptyAds($company, 10);

        expect('Cant create Empty ads when company_id param is not a parent company', $can_create)->false();

        $company = Company::findOne(9);
        $can_create = EmptyAds::canCreateEmptyAds($company, 10);

        expect('Can create empty ads cause parent company doesnt use payment_cards', $can_create)->true();

        $company = Company::findOne(1);
        $can_create = EmptyAds::canCreateEmptyAds($company, 10);

        expect('Cant create empty ads cause theres only 3 avalable', $can_create)->false();

        PaymentCard::updateAll(['used' => 0]);
        $company = Company::findOne(8);
        $can_create = EmptyAds::canCreateEmptyAds($company, 10);

        expect('Can create empty ads cause theres 10 available payment cards', $can_create)->true();
    }

    public function testCreateEmptyAds() {
        $node  = Node::findOne(1);
        $previous_empty_ads = count(EmptyAds::find()->all());

        //Cuando la empresa no es padre
        $company = Company::findOne(1);
        $codes = EmptyAds::createEmptyAds($company, $node, 10);

        expect('Result is empty when company param is not a parent company', $codes)->isEmpty();

        //Cuando la empresa es padre
        $company = Company::findOne(8);
        $codes = EmptyAds::createEmptyAds($company, $node, 10);

        expect('Result is not empty cuase the company param is a parent company', $codes)->notEmpty();
        expect('Result has 10 items', count($codes))->equals(11);
        expect('Result has key payment_code', array_key_exists('payment_code',$codes[0]))->true();
        expect('Result has key code', array_key_exists('code',$codes[0]))->true();
        expect('Theres 10 empty ads', count(EmptyAds::find()->all()) >= $previous_empty_ads + 10 );
    }

    public function testAssociatePaymentCard()
    {
        PaymentCard::updateAll(['used' => 1]);

        $model = new EmptyAds([
            'code' => '236',
            'payment_code' => '456',
            'node_id' => 1
        ]);
        $model->save();

        expect('Cant associate payment card cause all are already used', $model->associatePaymentCard())->false();

        PaymentCard::updateAll(['used' => 0]);

        $payment_card_id = $model->associatePaymentCard();
        expect('Payment card associated', $payment_card_id > 0)->true();
        expect('Payment card exist', PaymentCard::findOne($payment_card_id))->isInstanceOf(PaymentCard::class);
        expect('Payment card id is associated to empty ads', $model->payment_card_id)->equals($payment_card_id->payment_card_id);
    }
}
