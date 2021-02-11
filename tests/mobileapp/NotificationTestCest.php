<?php

use app\tests\fixtures\MobilePushFixture;
use app\tests\fixtures\MobilePushHasUserAppFixture;
use app\tests\fixtures\UserAppFixture;
use app\tests\fixtures\AuthTokenFixture;


class NotificationTestCest
{
    public function _before(MobileappTester $I)
    {
    }

    public function _fixtures() 
    {
        return [
            [
                'class' => MobilePushFixture::class,
            ],
            [
                'class' => MobilePushHasUserAppFixture::class,
            ],
            [
                'class' => UserAppFixture::class,
            ],
            [
                'class' => AuthTokenFixture::class
            ]

        ];
    }

    // tests
    public function tryMobilePushView(MobileappTester $I)
    {
        $I->haveHttpHeader('Auth-token', 'test_token');
        $I->haveHttpHeader('X-private-token', 'west123456net');
        $I->sendPost('/notification/view', ['mobile_push_id' => 1]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'mobile_push_has_user_app_id'=> 1,
            'user_app_id' => 1,
            'customer_id' =>45900,
            'title' => 'Title Notification',
            'content' => 'Notification Content',
            'notification_title' => 'Title Notification',
            'notificationResume' => 'Notification Resume',
            'resume' => 'Notification Resume',
            'notification_content' => 'Notification Content',
            'notification_read' => 0,
            'date' => '11/02/2021',
            'buttons' => []
        ]);
    }   

    public function tryMobilePushViewFailWithoutAuthToken(MobileappTester $I)
    {
        $I->haveHttpHeader('X-private-token', 'west123456net');
        $I->sendPost('/notification/view', ['mobile_push_id' => 1]);

        $I->seeResponseCodeIs(403);
        
    }   

    public function tryMobilePushViewFailWithoutPrivateToken(MobileappTester $I)
    {
        $I->haveHttpHeader('Auth-token', 'test_token');
        $I->sendPost('/notification/view', ['mobile_push_id' => 1]);

        $I->seeResponseCodeIs(403);
        
    } 

    public function tryMobilePushViewFailWithoutTokens(MobileappTester $I)
    {
        $I->sendPost('/notification/view', ['mobile_push_id' => 1]);

        $I->seeResponseCodeIs(403);
        
    } 

    // tests
    public function tryMobilePushViewFailWithoutMobilePushId(MobileappTester $I)
    {
        $I->haveHttpHeader('Auth-token', 'test_token');
        $I->haveHttpHeader('X-private-token', 'west123456net');
        $I->sendPost('/notification/view');

        $I->seeResponseCodeIs(400);
        
    }   

}
