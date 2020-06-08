<?php

namespace app\modules\mobileapp\v1\models;

use Yii;

/**
 * This is the model class for table "mobile_push_has_user_app".
 *
 * @property integer $mobile_push_has_user_app_id
 * @property integer $mobile_push_id
 * @property integer $user_app_id
 * @property integer $customer_id
 * @property string $notification_title
 * @property string $notification_content
 * @property string $notification_read
 *
 * @property MobilePush $mobilePush
 * @property UserApp $userApp
 */
class MobilePushHasUserApp extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mobile_push_has_user_app';
    }
    
    /**
     * @inheritdoc
     */
    /*
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['timestamp'],
                ],
            ],
            'date' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date'],
                ],
                'value' => function(){return date('Y-m-d');},
            ],
            'time' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['time'],
                ],
                'value' => function(){return date('h:i');},
            ],
        ];
    }
    */

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile_push_id', 'user_app_id'], 'required'],
            [['mobile_push_id', 'user_app_id', 'customer_id'], 'integer'],
            [['notification_read', 'notification_content', 'notification_title'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mobile_push_id' => Yii::t('app', 'Mobile Push ID'),
            'user_app_id' => Yii::t('app', 'User App ID'),
            'customer_id' => Yii::t('app', 'Customer'),
            'notification_title' => Yii::t('app', 'Notification title'),
            'notification_content' => Yii::t('app', 'Notification content'),
            'notification_read' => Yii::t('app', 'Notification read'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMobilePush()
    {
        return $this->hasOne(MobilePush::className(), ['mobile_push_id' => 'mobile_push_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserApp()
    {
        return $this->hasOne(UserApp::className(), ['user_app_id' => 'user_app_id']);
    }
             
    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        return true;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: MobilePush, UserApp.
     */
    protected function unlinkWeakRelations(){
    }
    
    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if($this->getDeletable()){
                $this->unlinkWeakRelations();
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Devuelve un array con los botones seleccionados para la notificación
     */
    public function getButtoms()
    {
        return explode(',', $this->mobilePush->buttoms);
    }

}
