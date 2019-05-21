<?php

use app\tests\fixtures\CompanyFixture;
use app\tests\fixtures\NodeFixture;
use app\modules\westnet\models\EmptyAds;
use app\tests\fixtures\CompanyHasPaymentTrackFixture;
use app\tests\fixtures\AdsPercentagePerCompanyFixture;
use app\modules\cobrodigital\models\PaymentCard;
use app\tests\fixtures\PaymentCardFixture;

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
            'code' => '123',
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
            'code' => '123',
            'payment_code' => '456',
            'node_id' => 1
        ]);

        expect('Valid when full and new', $model->save())->true();
    }

    public function testCanCreateEmptyAds()
    {
        $can_create = EmptyAds::canCreateEmptyAds(1, 10);

        expect('Cant create Empty ads when company_id param is not a parent company', $can_create)->false();

        $can_create = EmptyAds::canCreateEmptyAds(9, 10);

        expect('Can create empty ads cause parent company doesnt use payment_cards', $can_create)->true();

        $can_create = EmptyAds::canCreateEmptyAds(8, 10);

        expect('Cant create empty ads cause theres only 3 avalable', $can_create)->false();

        $payment_card = new PaymentCard([
            'payment_card_file_id' => 1,
            'code_19_digits' =>
        ]);

        $can_create = EmptyAds::canCreateEmptyAds(8, 10);


    }
}

/*
     public static function canCreateEmptyAds(Company $parent_company, $qty)
    {
        //La empresa desde la que se debe partir debe ser una padre.
        if ($parent_company->parent_id != null) {
            return false;
        }

        //Verifico si alguna de las empresas hijas tiene las tarjetas de cobro habilitadas
        if ($parent_company->hasEnabledTrackWithPaymentCards(true)) {

            $qty_percentage = round(($parent_company->getTotalADSPercentage() * $qty) / 100);

            //Verifico que la cantidad de ADS disponible sea mayor o igual a la cantidad porcentual entre las empresas hijas
            $availablePaymentCardsQty = PaymentCard::getUnusedPaymentCardsQty();
            if ($availablePaymentCardsQty < $qty_percentage) {
                return false;
            }
        }

        return true;
    }

public static function createEmptyAds(Company $parent_company, $node, $qty)
{
    $codes = [];
    $associate_payment_card = false;

    foreach ($parent_company->companies as $company) {
        $generator = CodeGeneratorFactory::getInstance()->getGenerator('PagoFacilCodeGenerator');
        $percentage_qty = AdsPercentagePerCompany::getCompanyPercentageQty($company->company_id, $qty);
        if($company->hasEnabledTrackWithPaymentCards()) {
            $associate_payment_card = true;
        }

        for ($i = 0; $i < $percentage_qty; $i++) {
            $init_value = Customer::getNewCode();
            $code = str_pad($company->code, 4, "0", STR_PAD_LEFT) . ($company->code == '9999' ? '' : '000' ) .
                str_pad($init_value, 5, "0", STR_PAD_LEFT) ;

            $payment_code = $generator->generate($code);

            $emptyAds = new EmptyAds([
                'code' => $init_value,
                'payment_code' => $payment_code,
                'node_id' => $node->node_id,
                'company_id' => $company->company_id,
                'used' => false,
            ]);
            $emptyAds->save(false);
            $codes[] = ['payment_code'=> $payment_code, 'code' => $init_value, ''];

            if($associate_payment_card) {
                $emptyAds->associatePaymentCard();
            }
        }
    }

    return $codes;
}

public function associatePaymentCard()
{
    $payment_card = PaymentCard::find()->where(['used' => 0])->one();

    if(!$payment_card) {
        return false;
    }

    $this->updateAttributes(['payment_card_id' => $payment_card->payment_card_id]);
    $payment_card->updateAttributes(['used' => 1]);

    return $payment_card->payment_card_id;
}
 */
