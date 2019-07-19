<?php 

class CustomerCest
{
    public function _before(IvrapiTester $I)
    {
    }

    public function _fixtures(){
        return [
            'tokens' => [
                'class' => \app\tests\fixtures\Oauth2AccessToken::class
            ],
            'customers' =>  [
                'class' => \app\tests\fixtures\CustomerFixture::class
            ],
            'payments' => [
                'class' => \app\tests\fixtures\PaymentFixture::class
            ]
        ];
    }

    // tests
    public function searchCustomerForDocumentNumber(IvrapiTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer QENUrXDpxJcSXTyC0gWeEyBMe9nPWFVxthEp8kpc');
        $I->haveHttpHeader('client_id', 'ivr_user');
        $I->haveHttpHeader('client_secret', '4kjaw4a0d0ks09sdfi9ersj23i4l2309aid09qe');
        $I->sendPOST('/customer/search', ['field' => 'document_number', 'value' => '17356926']);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            [
                'customer_id' => 45900,
                'fullName' => 'GOMEZ, MARIO SANTOS',
                'documentType' => ["document_type_id" => 2, "name" => "DNI","code" => 96,"regex" => ""],
                'document_number' => '17356926',
                'code' => 59809,
            ]
        ]);
    }

    public function searchCustomerForCode(IvrapiTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer QENUrXDpxJcSXTyC0gWeEyBMe9nPWFVxthEp8kpc');
        $I->haveHttpHeader('client_id', 'ivr_user');
        $I->haveHttpHeader('client_secret', '4kjaw4a0d0ks09sdfi9ersj23i4l2309aid09qe');
        $I->sendPOST('/customer/search', ['field' => 'code', 'value' => '59809']);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            [
                'customer_id' => 45900,
                'fullName' => 'GOMEZ, MARIO SANTOS',
                'documentType' => ["document_type_id" => 2, "name" => "DNI","code" => 96,"regex" => ""],
                'document_number' => '17356926',
                'code' => 59809,
            ]
        ]);
    }

    public function searchCustomerForInvalidDocumentNumber(IvrapiTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer QENUrXDpxJcSXTyC0gWeEyBMe9nPWFVxthEp8kpc');
        $I->haveHttpHeader('client_id', 'ivr_user');
        $I->haveHttpHeader('client_secret', '4kjaw4a0d0ks09sdfi9ersj23i4l2309aid09qe');
        $I->sendPOST('/customer/search', ['field' => 'document_number', 'value' => '18356926']);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error' => Yii::t('ivrapi','Customer not found')
        ]);
    }

    public function searchCustomerForInvalidCode(IvrapiTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer QENUrXDpxJcSXTyC0gWeEyBMe9nPWFVxthEp8kpc');
        $I->haveHttpHeader('client_id', 'ivr_user');
        $I->haveHttpHeader('client_secret', '4kjaw4a0d0ks09sdfi9ersj23i4l2309aid09qe');
        $I->sendPOST('/customer/search', ['field' => 'code', 'value' => '57809']);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error' => Yii::t('ivrapi','Customer not found')
        ]);
    }

    public function searchCustomerFailEmptyField(IvrapiTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer QENUrXDpxJcSXTyC0gWeEyBMe9nPWFVxthEp8kpc');
        $I->haveHttpHeader('client_id', 'ivr_user');
        $I->haveHttpHeader('client_secret', '4kjaw4a0d0ks09sdfi9ersj23i4l2309aid09qe');
        $I->sendPOST('/customer/search', ['value' => '57809']);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error' => Yii::t('ivrapi','"field" param is required')
        ]);
    }

    public function searchCustomerFailEmptyValue(IvrapiTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer QENUrXDpxJcSXTyC0gWeEyBMe9nPWFVxthEp8kpc');
        $I->haveHttpHeader('client_id', 'ivr_user');
        $I->haveHttpHeader('client_secret', '4kjaw4a0d0ks09sdfi9ersj23i4l2309aid09qe');
        $I->sendPOST('/customer/search', ['field' => 'code']);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error' => Yii::t('ivrapi','"value" param is required')
        ]);
    }

    public function searchCustomerFailEmptyFieldAndValue(IvrapiTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer QENUrXDpxJcSXTyC0gWeEyBMe9nPWFVxthEp8kpc');
        $I->haveHttpHeader('client_id', 'ivr_user');
        $I->haveHttpHeader('client_secret', '4kjaw4a0d0ks09sdfi9ersj23i4l2309aid09qe');
        $I->sendPOST('/customer/search', []);
        $I->seeResponseCodeIs(400);
        $I->seeResponseContainsJson([
            'error' => Yii::t('ivrapi','"field" and "value" params are required')
        ]);
    }

    public function balanceAccount(IvrapiTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer QENUrXDpxJcSXTyC0gWeEyBMe9nPWFVxthEp8kpc');
        $I->haveHttpHeader('client_id', 'ivr_user');
        $I->haveHttpHeader('client_secret', '4kjaw4a0d0ks09sdfi9ersj23i4l2309aid09qe');
        $I->sendPOST('/customer/balance-account', ['code' => 59809]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'balance' => 1500,
            'last_payment' => [
                'amount' => 200,
                'date' => '30-01-2019'
            ]
        ]);
    }

}
