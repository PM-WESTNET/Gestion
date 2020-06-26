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

    public function testGetButtons()
    {
        $model = new MobilePushHasUserApp([
            'mobile_push_id' => 1,
            'user_app_id' => 1
        ]);
        $model->save();

        $mobile_push = MobilePush::findOne(1);
        $mobile_push->buttons = Notification::BUTTON_PAYMENT_EXTENSION .','. Notification::BUTTON_PAYMENT_NOTIFY .',';
        $mobile_push->save();

        expect('Buttons is an array', is_array($model->getButtons()))->true();
        expect('Buttons is an array', $model->getButtons())->equals([Notification::BUTTON_PAYMENT_EXTENSION, Notification::BUTTON_PAYMENT_NOTIFY, '']);
    }

    public function testMarkAsRead()
    {
        $model = new MobilePushHasUserApp([
            'mobile_push_id' => 1,
            'user_app_id' => 1
        ]);
        $model->save();

        expect('Notification marked as read', MobilePushHasUserApp::markAsRead($model->mobile_push_has_user_app_id))->true();
        $model->refresh();
        expect('Field notification_read equals 1', $model->notification_read)->equals(1);
    }

    public function testSetTimeSentAt()
    {
        $model = new MobilePushHasUserApp([
            'mobile_push_id' => 1,
            'user_app_id' => 1
        ]);
        $model->save();

        $model2 = new MobilePushHasUserApp([
            'mobile_push_id' => 1,
            'user_app_id' => 12
        ]);
        $model2->save();

        MobilePushHasUserApp::setTimeSentAt([$model->mobile_push_has_user_app_id, $model2->mobile_push_has_user_app_id]);

        $model->refresh();
        $model2->refresh();

        expect('Model1 has sent_at filled', $model->sent_at)->notEmpty();
        expect('Model1 has sent_at filled', $model2->sent_at)->notEmpty();

        //Probamos que el timestamp que se pasa es el que actualiza
        $model->updateAttributes(['sent_at' => null]);
        $model2->updateAttributes(['sent_at' => null]);

        $timestamp = (new \DateTime('now'))->getTimestamp();
        MobilePushHasUserApp::setTimeSentAt([$model->mobile_push_has_user_app_id, $model2->mobile_push_has_user_app_id], $timestamp);

        $model->refresh();
        $model2->refresh();

        expect('Scenario2 Model1 has sent_at filled', $model->sent_at)->notEmpty();
        expect('Scenario2 Model1 has sent_at filled', $model2->sent_at)->notEmpty();
        expect('Scenario2 Model1 has sent_at filled', $model->sent_at)->equals($timestamp);
        expect('Scenario2 Model1 has sent_at filled', $model2->sent_at)->equals($timestamp);
    }
}