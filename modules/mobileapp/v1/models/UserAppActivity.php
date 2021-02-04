<?php

namespace app\modules\mobileapp\v1\models;

use Yii;
use app\modules\mobileapp\v1\models\UserApp;

/**
 * This is the model class for table "user_app_activity".
 *
 * @property int $user_app_activity_id
 * @property int $user_app_id
 * @property int $installation_datetime
 * @property int $last_activity_datetime
 *
 * @property UserApp $userApp
 */
class UserAppActivity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_app_activity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_app_id'], 'required'],
            [['user_app_id', 'installation_datetime', 'last_activity_datetime'], 'integer'],
            [['user_app_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserApp::class, 'targetAttribute' => ['user_app_id' => 'user_app_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_app_activity_id' => Yii::t('app', 'User App Activity ID'),
            'user_app_id' => Yii::t('app', 'User App ID'),
            'installation_datetime' => Yii::t('app', 'Installation Datetime'),
            'last_activity_datetime' => Yii::t('app', 'Last Activity Datetime'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserApp()
    {
        return $this->hasOne(UserApp::class, ['user_app_id' => 'user_app_id']);
    }

    /**
     * @param $user_app_id
     * @return bool
     * @throws \Exception
     * Crea un registro de instalación.
     */
    public static function createInstallationRegister($user_app_id, $return_model = false) {
        $user_app_activity_exists = UserAppActivity::find()->where(['user_app_id' => $user_app_id])->exists();

        if(!$user_app_activity_exists) {
            $user_app_activity = new UserAppActivity([
                'user_app_id' => $user_app_id,
                'installation_datetime' => (new \DateTime('now'))->getTimestamp(),
                'last_activity_datetime' => (new \DateTime('now'))->getTimestamp(),
            ]);

            if($return_model && $user_app_activity->save()) {
                return $user_app_activity;
            }

            return $user_app_activity->save();
        }

        return true;
    }

    /**
     * @param $user_app_id
     * @return bool
     * @throws \Exception
     * Actualiza la fecha de última actividad. Si el registro del user app no existe, lo crea.
     */
    public static function updateLastActivity($user_app_id) {
        $user_app_activity = UserAppActivity::findOne(['user_app_id' => $user_app_id]);

        if(!$user_app_activity) {
            $user_app_activity = UserAppActivity::createInstallationRegister($user_app_id, true);
        }

        $user_app_activity->last_activity_datetime = (new \DateTime('now'))->getTimestamp();

        return $user_app_activity->save();
    }
}
