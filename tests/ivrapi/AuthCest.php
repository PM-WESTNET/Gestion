<?php 

class AuthCest
{
    public function _before(IvrapiTester $I)
    {
    }

    public function _fixtures(){
        return [
            'users' => [
                'class' => \app\tests\fixtures\UserFixture::class
            ]
        ];
    }
    // tests
    public function testTokenOK(IvrapiTester $I)
    {
        $I->sendPOST('/auth/token', [
            'grant_type' => 'password',
            'username' => 'Alf',
            'password' => 'superadmin',
            'scope' => '',
            'client_id' => 'ivr_user',
            'client_secret' => '4kjaw4a0d0ks09sdfi9ersj23i4l2309aid09qe',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains('access_token');

    }

    public function testTokenFailWithIncorrectPassword(IvrapiTester $I)
    {
        $I->sendPOST('/auth/token', [
            'grant_type' => 'password',
            'username' => 'Alf',
            'password' => 'superadmins',
            'scope' => '',
            'client_id' => 'ivr_user',
            'client_secret' => '4kjaw4a0d0ks09sdfi9ersj23i4l2309aid09qe',
        ]);
        $I->seeResponseCodeIs(400);
    }

    public function testTokenFailWithIncorrectUser(IvrapiTester $I)
    {
        $I->sendPOST('/auth/token', [
            'grant_type' => 'password',
            'username' => 'Alfs',
            'password' => 'superadmin',
            'scope' => '',
            'client_id' => 'ivr_user',
            'client_secret' => '4kjaw4a0d0ks09sdfi9ersj23i4l2309aid09qe',
        ]);
        $I->seeResponseCodeIs(400);
    }

    public function testTokenFailWithIncorrectClientId(IvrapiTester $I)
    {
        $I->sendPOST('/auth/token', [
            'grant_type' => 'password',
            'username' => 'Alf',
            'password' => 'superadmin',
            'scope' => '',
            'client_id' => 'ivr_users',
            'client_secret' => '4kjaw4a0d0ks09sdfi9ersj23i4l2309aid09qe',
        ]);
        $I->seeResponseCodeIs(400);
    }

    public function testTokenFailWithIncorrectClientSecret(IvrapiTester $I)
    {
        $I->sendPOST('/auth/token', [
            'grant_type' => 'password',
            'username' => 'Alf',
            'password' => 'superadmin',
            'scope' => '',
            'client_id' => 'ivr_user',
            'client_secret' => '4kjaw4a0d0ks09sdfi9ersj23i4l2309aid09qes',
        ]);
        $I->seeResponseCodeIs(400);
    }
}
