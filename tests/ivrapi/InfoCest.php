<?php 

class InfoCest
{
    public function _before(IvrapiTester $I)
    {
    }

    public function _fixtures()
    {
        return [
            'tokens' => [
                'class' => \app\tests\fixtures\Oauth2AccessToken::class
            ],
            'methods' => [
                'class' => \app\tests\fixtures\PaymentMethodFixture::class
            ]
        ];
    }

    // tests
    public function testPaymentMethods(IvrapiTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer QENUrXDpxJcSXTyC0gWeEyBMe9nPWFVxthEp8kpc');
        $I->haveHttpHeader('client_id', 'ivr_user');
        $I->haveHttpHeader('client_secret', '4kjaw4a0d0ks09sdfi9ersj23i4l2309aid09qe');
        $I->sendGET('/info/payment-methods');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            ['payment_method_id' => 1,'name' => 'Contado','status' => 'enabled'],
            ['payment_method_id' => 2,'name' => 'Cheque','status' => 'enabled'],
            ['payment_method_id' => 4,'name' => 'Transferencia','status' => 'enabled'],
            ['payment_method_id' => 6,'name' => 'Ecopago','status' => 'enabled'],
            ['payment_method_id' => 8,'name' => 'Pago Facil','status' => 'enabled'],
            ['payment_method_id' => 9,'name' => 'Lapos Web','status' => 'enabled'],
            ['payment_method_id' => 10,'name' => 'Pagomiscuentas','status' => 'enabled'],
        ]);
    }
}
