<?php

use app\modules\mobileapp\v1\models\MobilePush;
use app\modules\mobileapp\v1\models\MobilePushHasUserApp;
use app\modules\westnet\notifications\models\Notification;
use app\tests\fixtures\MobilePushFixture;
use app\tests\fixtures\UserAppFixture;

class MobilePushHasUserAppTest extends \Codeception\Test\Unit
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
            'user_app' => [
                'class' => UserAppFixture::class
            ],
            'mobile_push' => [
                'class' => MobilePushFixture::class
            ]
        ];
    }

    public function testInvalidWhenNewAndEmpty()
    {
        $model = new MobilePushHasUserApp();

        expect('Invalid when new and empty', $model->validate())->false();
    }

    public function testValidWhenNewAndFull()
    {
        $model = new MobilePushHasUserApp([
            'mobile_push_id' => 1,
            'user_app_id' => 1
        ]);

        expect('Valid when new and full', $model->validate())->true();
    }

    public function testNotSaveWhenNewAndEmpty()
    {
        $model = new MobilePushHasUserApp();

        expect('Not save when new and empty', $model->save())->false();
    }

    public function testSaveWhenNewAndFull()
    {
        $model = new MobilePushHasUserApp([
            'mobile_push_id' => 1,
            'user_app_id' => 1
        ]);

        expect('Save when new and full', $model->save())->true();
    }

    public function testGetButtoms()
    {
        $model = new MobilePushHasUserApp([
            'mobile_push_id' => 1,
            'user_app_id' => 1
        ]);
        $model->save();

        $mobile_push = MobilePush::findOne(1);
        $mobile_push->buttoms = Notification::BUTTOM_PAYMENT_EXTENSION .','. Notification::BUTTOM_PAYMENT_NOTIFY .',';
        $mobile_push->save();

        expect('Buttoms is an array', is_array($model->getButtoms()))->true();
        expect('Buttoms is an array', $model->getButtoms())->equals([Notification::BUTTOM_PAYMENT_EXTENSION, Notification::BUTTOM_PAYMENT_NOTIFY, '']);
    }
}