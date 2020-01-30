<?php

namespace app\modules\westnet\notifications\models;

use Yii;

/**
 * This is the model class for table "transport".
 *
 * @property integer $transport_id
 * @property string $name
 * @property string $slug
 * @property string $description
 *
 * @property Notification[] $notifications
 */
class Transport extends \app\components\db\ActiveRecord {

    CONST STATUS_ENABLED = 'enabled';
    CONST STATUS_DISABLED = 'disabled';

    public static function tableName() {
        return 'transport';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('dbnotifications');
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
    public function rules() {
        return [
            [['name', 'slug', 'class'], 'required'],
            [['description'], 'string'],
            [['name', 'class'], 'string', 'max' => 255],
            [['slug'], 'string', 'max' => 45],
            [['status'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'transport_id' => Yii::t('app', 'Transport ID'),
            'name' => Yii::t('app', 'Name'),
            'slug' => Yii::t('app', 'Slug'),
            'description' => Yii::t('app', 'Description'),
            'notifications' => Yii::t('app', 'Notifications'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotifications() {
        return $this->hasMany(Notification::className(), ['transport_id' => 'transport_id']);
    }

    /**
     * @inheritdoc
     * Strong relations: Notifications.
     */
    public function getDeletable() {
        if ($this->getNotifications()->exists()) {
            return false;
        }
        return true;
    }

    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: None.
     */
    protected function unlinkWeakRelations() {
        
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete() {
        if (parent::beforeDelete()) {
            if ($this->getDeletable()) {
                $this->unlinkWeakRelations();
                return true;
            }
        } else {
            return false;
        }
    }
    
    private function newTransportClass()
    {
        $class = $this->class;
        return new $class;
        
    }
    
    public function send($notification, $force_send = false)
    {
        $transport = $this->newTransportClass();
        
        $response = $transport->send($notification, $force_send);

        if(Yii::$app->request->isConsoleRequest) {
            echo "Respuesta final: ". print_r($response, 1);
            echo "\n";
        }
        Yii::trace($response);

        if($response['status'] == 'success'){
            $notification->markSending();
        }else{
            $notification->markAsError($response['error']);
        }
        
        return $response;
    }
    
    public function export($notification)
    {
        $transport = $this->newTransportClass();
        
        $transport->export($notification);
        
    }
    
    /**
     * Este transport tiene $what feature?
     * @param string $what
     * @return boolean
     */
    public function hasFeature($what)
    {
        $transport = $this->newTransportClass();
        $features = $transport->features();
        return in_array($what, $features);
    }

    /**
    * Funcion disponible para ser sobreescrita en cada transporte en caso de que necesiten cancelar o abortar el envio de los mensajes
    */
    public static function abortMessages($notification_id){}

    public static function getAllEnabled(){
        $enabled_transports = Transport::find()->where(['status' => Transport::STATUS_ENABLED])->all();
        return $enabled_transports;
    }
}
