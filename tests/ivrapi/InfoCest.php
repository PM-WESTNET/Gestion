<?php 

class InfoCest
{
    public function _before(IvrapiTester $I)
    {
    }


    // tests
    public function testPaymentMethods(IvrapiTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer QENUrXDpxJcSXTyC0gWeEyBMe9nPWFVxthEp8kpc');
        $I->haveHttpHeader('client_id', 'ivr_user');
        $I->haveHttpHeader('client_secret', '4kjaw4a0d0ks09sdfi9ersj23i4l2309aid09qe');
        $I->sendGET('/info/payment-methods');
        $I->seeResponseCodeIs(200);
    }
}
