<?php

namespace app\modules\westnet\notifications\models;

use app\modules\westnet\notifications\NotificationsModule;
use Yii;
use app\modules\sale\models\Customer;
use app\modules\ticket\models\Ticket;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\config\models\Config;

/**
 * This is the model class for table "integratech_received_sms".
 *
 * @property integer $integratech_received_sms_id
 * @property string $destaddr
 * @property string $charcode
 * @property string $sourceaddr
 * @property string $messageç
 * @property integer $customer_id
 */
class IntegratechReceivedSms extends \app\components\db\ActiveRecord
{

    public static function tableName()
    {
        return 'integratech_received_sms';
    }

    public function rules()
    {
        return [
            [['destaddr', 'charcode', 'sourceaddr', 'message'], 'string'],
            [['customer_id'], 'number']
        ];

    }

    public function behaviors()
    {
        return [
            'datetime' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['datetime'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'integratech_received_sms_id' => NotificationsModule::t('app', 'ID'),
            'destaddr' => NotificationsModule::t('app', 'Destaddr'),
            'charcode' => NotificationsModule::t('app', 'Charcode'),
            'sourceaddr' => NotificationsModule::t('app', 'Sourceaddr'),
            'message' => NotificationsModule::t('app', 'Message'),
            'ticket_id' => NotificationsModule::t('app', 'Ticket Mesa id'),
            'customer_id' => NotificationsModule::t('app','Customer'),
            'datetime' => NotificationsModule::t('app','Datetime'),
        ];
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbnotifications');
    }

    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        if($this->ticket_id){
            return false;
        }
        return true;
    }

    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: None.
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

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $this->analizeSMS();

        return true;
    }

    public function getCustomer(){
        return $this->hasOne(Customer::className(),['customer_id' => 'customer_id']);
    }

    public function analizeSMS(){
        $this->analizePhone();
        $this->analizeWords();

    }

    public function analizePhone(){
        $query = Customer::find();
        $customer = $query
            ->orWhere(['phone'=> $this->destaddr])
            ->orWhere(['phone2'=> $this->destaddr])
            ->orWhere(['phone3'=> $this->destaddr])
            ->one();
        if($customer) {
            $this->updateAttributes(['customer_id' => $customer->customer_id]);
        }
    }

    public function analizeWords(){
        $arrayWords = explode( ' ', $this->message);
        $filters = IntegratechSmsFilter::find()->where(['status' => 'enabled'])->all();
        foreach ($arrayWords as $word) {
            $match = false;
            foreach ($filters as $filter) {
                if($filter->is_created_automaticaly){
                    if ($filter->word == $word) {
                        $match = true;
                        if ($filter->action == 'Delete') {
                            $this->delete();
                        }
                        if ($filter->action == 'Create Ticket') {
                            $this->createTicket($filter);
                        }
                    }
                }
            }
        }
    }

    public function createTicket(IntegratechSmsFilter $filter)
    {

        $contract = Contract::findOne(['customer_id' => $this->customer_id]);

        $ticket = new Ticket();
        $ticket->contract_id = $contract->contract_id;
        $ticket->customer_id = $this->customer_id;
        $ticket->category_id = $filter->category_id;
        $ticket->title = 'Ticket Automatico via SMS';
        $ticket->content = $this->message;
        $ticket->status_id = Config::getValue('ticket_new_status_id');
        $ticket->user_id = (Yii::$app instanceof \yii\console\Application ? 1 : Yii::$app->user->id);
        if($ticket->save()){
            $this->updateAttributes(['ticket_id' => $ticket->ticket_id]);
        }

        return $ticket->ticket_id;
    }
}
