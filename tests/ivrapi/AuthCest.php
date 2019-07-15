<?php 

class AuthCest
{
    public function _before(IvrapiTester $I)
    {
    }

    // tests
    public function testTokenOK(IvrapiTester $I)
    {
        $I->amOnRoute('/auth/token');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains('access_token');

    }
}
