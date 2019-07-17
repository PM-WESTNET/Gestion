<?php

use app\modules\mobileapp\v1\models\UserAppActivity;
use app\tests\fixtures\UserAppFixture;

class UserAppActivityTest extends \Codeception\Test\Unit
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
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new UserAppActivity();

        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new UserAppActivity([
            'user_app_id' => 1,
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new UserAppActivity();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new UserAppActivity([
            'user_app_id' => 1,
        ]);

        expect('Saved when full and new', $model->save())->true();
    }

    public function testCreateInstallationRegister()
    {
        $model = new UserAppActivity([
            'user_app_id' => 1,
        ]);
        $model->save();

        expect('Create installation register successfully', UserAppActivity::createInstallationRegister($model->user_app_id))->true();

        UserAppActivity::deleteAll();

        expect('Create installation register successfully and return model', UserAppActivity::createInstallationRegister($model->user_app_id, true))->isInstanceOf(UserAppActivity::class);
    }

    public function testUpdateLastActivity()
    {
        $model = new UserAppActivity([
            'user_app_id' => 1,
        ]);
        $model->save();
        UserAppActivity::createInstallationRegister($model->user_app_id);

        expect('User app activity last_update is null', UserAppActivity::find()->one()->last_activity_datetime)->isEmpty();
        expect('User app activity last_update successfully', UserAppActivity::updateLastActivity($model->user_app_id))->true();
        expect('User app activity last_update is not null', UserAppActivity::find()->one()->last_activity_datetime)->notEmpty();
    }
}