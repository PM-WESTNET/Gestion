<?php

namespace app\modules\westnet\notifications\models;

use Yii;

/**
 * This is the model class for table "notification_has_customer".
 *
 * @property integer $notification_id
 * @property integer $customer_id
 *
 */
class NotificationHasCustomer extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification_has_customer';
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
    public function rules()
    {
        return [
            [['notification_id', 'customer_id'], 'required'],
            [['notification_id', 'customer_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'notification_id' => Yii::t('app', 'Notification ID'),
            'customer_id' => Yii::t('app', 'Customer ID'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotification()
    {
        return $this->hasOne(Notification::className(), ['notification_id' => 'notification_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['customer_id' => 'customer_id']);
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
     * Weak relations: Destinatary.
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
     * Return all customer for notification_id
     */
    public static function FindAllCustomersForNotificationID($notification_id){
        return self::getDb()->createCommand('SELECT * FROM notification_has_customer WHERE notification_id=:notification_id')
        ->bindValue('notification_id',$notification_id)
        ->queryAll(); 
    }

    /**
     * Remove all customer for notification_id
     */
    public static function RemoveAllCustomersForNotificationID($notification_id){
        return self::getDb()->createCommand('DELETE FROM notification_has_customer WHERE notification_id=:notification_id')
        ->bindValue('notification_id',$notification_id)
        ->execute(); 
    }

    /**
     * Return all customer for notification_id
     */
    public static function GetCustomerToCampaign($notification_id){
        return self::getDb()->createCommand('SELECT 
                cu.customer_id, cu.name, cu.lastname, nhc.email, cu.code, cu.phone, cu.phone2, 
                cu.payment_code, nhc.node, nhc.saldo, nhc.company_code, nhc.debt_bills,
                cu.status, nhc.category 
                FROM notification_has_customer nhc 
                LEFT JOIN westnet2.customer cu ON cu.customer_id = nhc.customer_id 
                WHERE 
                    nhc.notification_id=:notification_id 
                AND nhc.status = "pending"')
        ->bindValue('notification_id',$notification_id)
        ->queryAll();
    }

    /**
     * Return customer fo customer_id
     */
    public static function MarkSendEmail($email, $status){
        $model = self::find()->where(['email' => $email])->one();
        $model->status = $status;
        $model->save(false); 
    }
}
