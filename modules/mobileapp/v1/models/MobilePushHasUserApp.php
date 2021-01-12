<?php

namespace app\modules\mobileapp\v1\models;

use Codeception\Util\Debug;
use Yii;

/**
 * This is the model class for table "mobile_push_has_user_app".
 *
 * @property integer $mobile_push_has_user_app_id
 * @property integer $mobile_push_id
 * @property integer $user_app_id
 * @property integer $customer_id
 * @property integer $created_at
 * @property integer $sent_at
 * @property string $notification_title
 * @property string $notification_content
 * @property string $resume
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

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
//            'date' => [
//                'class' => 'yii\behaviors\TimestampBehavior',
//                'attributes' => [
//                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date'],
//                ],
//                'value' => function(){return date('Y-m-d');},
//            ],
//            'time' => [
//                'class' => 'yii\behaviors\TimestampBehavior',
//                'attributes' => [
//                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['time'],
//                ],
//                'value' => function(){return date('h:i');},
//            ],
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile_push_id', 'user_app_id'], 'required'],
            [['mobile_push_id', 'user_app_id', 'customer_id', 'created_at', 'sent_at'], 'integer'],
            [['notification_content', 'notification_title', 'resume'], 'string'],
            [['notification_read'], 'boolean']
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
            'resume' => Yii::t('app', 'Notification resume'),
            'notification_read' => Yii::t('app', 'Notification read'),
            'created_at' => Yii::t('app', 'Created at'),
            'sent_at' => Yii::t('app', 'Sent at'),
        ];
    }

    public function fields()
    {
        return [
            'mobile_push_has_user_app_id',
            'user_app_id',
            'customer_id',
            'title',
            'content',
            'notification_title',
            'notificationResume',
            'resume' => function($model){
                return $model->resume ? $model->resume : '';
            },
            'notification_content',
            'notification_read',
            'date',
            'buttons' => function($model) {
                return $model->getButtons();
            }
        ];
    }

    /**
     * Devuelve la fecha de envío con formato d/m/Y
     */
    public function getDate()
    {
        if($this->created_at) {
            return (new \DateTime())->setTimestamp($this->created_at)->format('d/m/Y');
        }

        return '';
    }

    /**
     * Elimina caracteres del titulo que la app no interpreta
     */
    public function getTitle()
    {
        return strip_tags(str_replace('&nbsp;',' ', $this->notification_title));
    }

    /**
     * Elimina caracteres del contenido que la app no interpreta
     */
    public function getContent()
    {
        return strip_tags(str_replace('&nbsp;',' ', $this->notification_content));
    }

    /**
     * Elimina caracteres del contenido que la app no interpreta
     */
    public function getNotificationResume()
    {
        return strip_tags(str_replace('&nbsp;',' ', ($this->resume ? $this->resume : '')));
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMobilePush()
    {
        return $this->hasOne(MobilePush::class, ['mobile_push_id' => 'mobile_push_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserApp()
    {
        return $this->hasOne(UserApp::class, ['user_app_id' => 'user_app_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['customer_id' => 'customer_id']);
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
    public function getButtons()
    {
        $buttons = [];

        foreach (explode(',', $this->mobilePush->buttons) as $button) {
            if(!empty($button)){
                array_push($buttons, ['name' => $button, 'label' => Yii::t('app', $button)]);
            }
        }

        return $buttons;
    }

    /**
     * Marca una notificación como leída
     */
    public static function markAsRead($mobile_push_has_user_app_id)
    {
        $mobile_push_has_user_app = MobilePushHasUserApp::findOne($mobile_push_has_user_app_id);

        if($mobile_push_has_user_app){
            $mobile_push_has_user_app->notification_read = true;
            return $mobile_push_has_user_app->save();
        }

        return false;
    }

    /**
     * Le determina un timestamp en el campo sent_at para los ids indicados
     */
    public static function setTimeSentAt($mobile_push_has_user_app_ids_sent, $timestamp = null)
    {
        $timestamp_value = (new \DateTime('now'))->getTimestamp();
        if($timestamp) {
            $timestamp_value = $timestamp;
        }

        Yii::$app->db->createCommand()->update(self::tableName(), ['sent_at' => $timestamp_value], ['in', 'mobile_push_has_user_app_id', $mobile_push_has_user_app_ids_sent])->execute();
    }

    /**
     * Devuelve un array con los datos que necesita la app para renderizar la vista de la notificacion
     * Este array se le pasa a One signal en el atributo "data", esto lo recibe la app cuando el usuario
     * pulsa sobre la notificacion en el centro de notificaciones del SO y redirige la app a la vista de la notificacion 
     */
    public function getExtraData() 
    {
        return [
            'mobile_push_has_user_app_id' => $this->mobile_push_has_user_app_id,
            'notification_title' => $this->notification_title,
            'resume' => $this->mobilePush->resume,
            'notification_content' => $this->notification_content,
            'buttons' => $this->getButtons(),
            'notification_read' => 0
        ];
    }
}
