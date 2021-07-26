<?php

namespace app\modules\westnet\notifications\models;

use app\components\companies\ActiveRecord;
use app\modules\mailing\MailingModule;
use app\modules\mailing\models\EmailTransport;
use app\modules\sale\models\Customer;
use app\modules\westnet\notifications\components\scheduler\Scheduler;
use app\modules\westnet\notifications\components\scheduler\types\EachDayOfWeekScheduler;
use Yii;
use app\modules\westnet\notifications\models\Image;
use app\modules\westnet\notifications\NotificationsModule;

/**
 * This is the model class for table "notification".
 *
 * @property integer $notification_id
 * @property integer $transport_id
 * @property integer $create_timestamp
 * @property integer $update_timestamp
 * @property string $subject
 * @property string $name
 * @property string $content
 * @property string $resume
 * @property string $from_date
 * @property string $from_time
 * @property string $to_date
 * @property string $to_time
 * @property integer $times_per_day
 * @property string $status
 * @property integer $email_transport_id
 * @property string $test_phone
 * @property string $buttons
 * @property integer $test_phone_frecuency
 * @property string $error_msg
 *
 * @property Destinatary[] $destinataries
 * @property Transport $transport
 * @property Customer[] $customers
 * @property EmailTransport $emailTransport
 *
 */
class Notification extends ActiveRecord {

    public $_isExternal = false;

    //Buttons
    public $button_payment_extension;
    public $button_payment_notify;
    public $button_edit_data;
    public $button_send_bill;

    //Statuses
    const STATUS_CREATED = 'created';
    const STATUS_ENABLED = 'enabled';
    const STATUS_DISABLED = 'disabled';
    const STATUS_SENT = 'sent';
    const STATUS_ERROR = 'error';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROCESS = 'in_process';

    //Buttons
    const BUTTON_PAYMENT_EXTENSION = 'payment_extension';
    const BUTTON_PAYMENT_NOTIFY = 'payment_notify';
    const BUTTON_EDIT_DATA = 'edit_data';
    const BUTTON_SEND_BILL = 'send_bill';

    public function init() {
        parent::init();
        self::$companyRequired = true;
        //Init from and to time for interval periods
        $this->from_time = "08:00:00";
        $this->to_time = "18:00:00";
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'notification';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('dbnotifications');
    }

    
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['create_timestamp','update_timestamp'],
                    yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => 'update_timestamp',
                ],
            ],
            'media' => [
                'class' => \app\modules\media\behaviors\MediaBehavior::className(),
            ]
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['button_payment_extension', 'button_payment_notify', 'button_edit_data', 'button_send_bill'], 'boolean'],
            [['transport_id', 'name'], 'required', 'on' => 'create'],
            [['transport_id', 'email_transport_id', 'company_id', 'test_phone_frecuency'], 'integer'],
            [['content', 'layout', 'test_phone', 'buttons', 'resume', 'error_msg'], 'string'],
           // [['content', 'subject'],'required'],
            [['status'], 'in', 'range' => [self::STATUS_CREATED, self::STATUS_ENABLED, self::STATUS_DISABLED,
                self::STATUS_ERROR, self::STATUS_SENT, self::STATUS_CANCELLED, self::STATUS_PENDING], 'on' => 'update-status'],
            [['status', 'error_msg'], 'safe'],
            [['test_phone_frecuency'], 'default' , 'value' => 1000],
            [['from_date', 'from_time', 'to_date', 'to_time', 'times_per_day', 'transport'], 'safe', 'on' => 'update'],
            [['from_date', 'to_date'], 'date', 'on' => 'update'],
            [['from_date', 'to_date'], 'default', 'value' => null, 'on' => 'update'],
            [['name', 'subject', 'scheduler'], 'string', 'max' => 255],
            ['from_time', 'compare', 'compareAttribute' => 'to_time', 'operator' => '<'],
            ['to_time', 'compare', 'compareAttribute' => 'from_time', 'operator' => '>'],
            [['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'], 'boolean', 'on' => 'update']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'notification_id' => NotificationsModule::t('app', 'Notification'),
            'transport_id' => NotificationsModule::t('app', 'Transport'),
            'subject' => NotificationsModule::t('app', 'Subject'),
            'resume' => NotificationsModule::t('app', 'Resume'),
            'name' => NotificationsModule::t('app', 'Name'),
            'content' => NotificationsModule::t('app', 'Content'),
            'from_date' => NotificationsModule::t('app', 'From Date'),
            'from_time' => NotificationsModule::t('app', 'From Time'),
            'to_date' => NotificationsModule::t('app', 'To Date'),
            'to_time' => NotificationsModule::t('app', 'To Time'),
            'to_datetime' => NotificationsModule::t('app', 'To Datetime'),
            'times_per_day' => NotificationsModule::t('app', 'Repeats per day'),
            'status' => NotificationsModule::t('app', 'Status'),
            'destinataries' => NotificationsModule::t('app', 'Destinataries'),
            'transport' => NotificationsModule::t('app', 'Transport'),
            'last_sent' => NotificationsModule::t('app', 'Last Sent'),
            'scheduler' => NotificationsModule::t('app', 'Scheduler'),
            'monday' => NotificationsModule::t('app', 'Monday'),
            'tuesday' => NotificationsModule::t('app', 'Tuesday'),
            'wednesday' => NotificationsModule::t('app', 'Wednesday'),
            'thursday' => NotificationsModule::t('app', 'Thursday'),
            'friday' => NotificationsModule::t('app', 'Friday'),
            'saturday' => NotificationsModule::t('app', 'Saturday'),
            'sunday' => NotificationsModule::t('app', 'Sunday'),
            'create_timestamp' => Yii::t('app', 'Created'),
            'update_timestamp' => Yii::t('app', 'Updated'),
            'email_transport_id' => MailingModule::t('Email transport'),
            'test_phone' => Yii::t('app', 'Test phone'),
            'test_phone_frecuency' => Yii::t('app', 'Test phone frecuency'),
            'buttons' => Yii::t('app', 'Buttons'),
            'button_payment_extension' => NotificationsModule::t('app', 'Button payment extension'),
            'button_payment_notify' => NotificationsModule::t('app', 'Button payment notify'),
            'button_edit_data' => NotificationsModule::t('app', 'Button edit data'),
            'button_send_bill' => NotificationsModule::t('app', 'Button send bill'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomers() {
        return $this->hasMany(Customer::className(), ['customer_id' => 'destinatary_id'])
                        ->viaTable('destinatary', ['notification_id' => 'notification_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDestinataries() {
        return $this->hasMany(Destinatary::className(), ['notification_id' => 'notification_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransport() {
        return $this->hasOne(Transport::className(), ['transport_id' => 'transport_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmailTransport() {
        return $this->hasOne(EmailTransport::className(), ['email_transport_id' => 'email_transport_id']);
    }

    /**
     * Returns all available statuses
     * @return array
     */
    public static function staticFetchStatuses() {
        return [
            static::STATUS_CREATED => NotificationsModule::t('app', 'Created'),
            static::STATUS_ENABLED => NotificationsModule::t('app', 'Enabled'),
            static::STATUS_DISABLED => NotificationsModule::t('app', 'Disabled'),
            static::STATUS_SENT => NotificationsModule::t('app', 'Sent'),
            static::STATUS_ERROR => NotificationsModule::t('app', 'Error'),
            static::STATUS_PENDING => NotificationsModule::t('app', 'Pending'),
            static::STATUS_IN_PROCESS => NotificationsModule::t('app', 'In Process'),
        ];
    }

    /**
     * Returns an array with all the times per day when the notification will be send
     * @return array
     */
    public function calcDailyPeriod() {

        //Creates datetimes objects
        $datetimeFrom = new \DateTime($this->from_time);
        $datetimeTo = new \DateTime($this->to_time);

        //Interval from to and from
        $interval = $datetimeTo->diff($datetimeFrom);

        //Get hours and minutes from interval
        $totalTimeHours = (int) $interval->h;
        $totalTimeMinutes = (int) $interval->i;

        //Calc seconds from interval
        $totalSeconds = $totalTimeHours * 3600 + $totalTimeMinutes * 60;

        if($this->times_per_day == 1){
            $times = [date("H:i:s", $datetimeFrom->getTimestamp())];
        }else{
            //Divides total seconds on times_per_day to get how many seconds between we need to send this notification
            $intervalSeconds = ceil($totalSeconds / ($this->times_per_day - 1));

            $times = [];

            for ($i = 0; $i < $this->times_per_day; $i++) {
                $times[$i] = date("H:i:s", $datetimeFrom->getTimestamp());
                $datetimeFrom->add(new \DateInterval("PT" . $intervalSeconds . "S"));
            }
        }

        return $times;
    }
    
    public function beforeValidate() 
    {
        
        if($this->from_date){
            $this->from_date = Yii::$app->formatter->asDate($this->from_date, 'dd-MM-yyyy');
        }
        if($this->to_date){
            $this->to_date = Yii::$app->formatter->asDate($this->to_date, 'dd-MM-yyyy');
        }
        
        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            
            //If this is a new record
            if ($insert) {
                $this->status = static::STATUS_CREATED;
            } else {
                
            }

            $this->formatButtonsBeforeSave();

            $this->formatDatesBeforeSave();
            
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes) {

        parent::afterSave($insert, $changedAttributes);

    }

    /**
     * @inheritdoc
     */
    public function afterFind() {
        
        if($this->from_date == '0000-00-00'){
            $this->from_date = null;
            $this->to_date = null;
        }

        if(!empty($this->buttons)){
            $this->formatButtonsAfterFind();
        }
        
        parent::afterFind();
    }

    /**
     * Format dates as database requieres it
     */
    private function formatDatesBeforeSave() {
        if(!$this->from_date) {
            $this->from_date = (new \DateTime('now'))->format('d-m-Y');
        }
        if(!$this->to_date) {
            $this->to_date = (new \DateTime('now'))->format('d-m-Y');
        }
        $this->from_date = Yii::$app->formatter->asDate($this->from_date, 'yyyy-MM-dd');
        $this->to_date = Yii::$app->formatter->asDate($this->to_date, 'yyyy-MM-dd');
    }

    /**
     * Format button string
     */
    private function formatButtonsBeforeSave() {
        $button_string = '';
        if($this->button_payment_extension){
            $button_string = $button_string . self::BUTTON_PAYMENT_EXTENSION . ',';
        }

        if($this->button_payment_notify){
            $button_string = $button_string . self::BUTTON_PAYMENT_NOTIFY . ',';
        }

        if($this->button_edit_data){
            $buttom_string = $button_string . self::BUTTON_EDIT_DATA . ',';
        }

        if($this->button_send_bill){
            $button_string = $button_string . self::BUTTON_SEND_BILL . ',';
        }

        $this->buttons = $button_string;
    }

    /**
     * Format button string
     */
    private function formatButtonsAfterFind() {
        $buttons = explode(',', $this->buttons);

        if(in_array(self::BUTTON_PAYMENT_EXTENSION, $buttons)){
            $this->button_payment_extension = 1;
        }

        if(in_array(self::BUTTON_PAYMENT_NOTIFY, $buttons)){
            $this->button_payment_notify = 1;
        }

        if(in_array(self::BUTTON_EDIT_DATA, $buttons)){
            $this->button_edit_data = 1;
        }

        if(in_array(self::BUTTON_SEND_BILL, $buttons)){
            $this->button_send_bill= 1;
        }
    }

    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable() {
        $has_integratech_messages = IntegratechMessage::find()->where(['notification_id' => $this->notification_id])->exists();
        if($has_integratech_messages){
            return false;
        }
        return true;
    }

    /**
     * Deletes weak relations for this model on delete
     * Weak relations: Destinataries, Images, Transport.
     */
    protected function unlinkWeakRelations() {
        foreach($this->destinataries as $d){
            $d->delete();
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete() {
        if (parent::beforeDelete()) {
            if ($this->deletable) {
                $this->unlinkWeakRelations();
                return true;
            }
        } else {
            return false;
        }
    }
    
    public function markSending()
    {
        $this->updateAttributes(['last_sent' => date('Y-m-d')]);
    }
    
    public function markAsError($message)
    {
        $this->status = 'error';
        $this->status_message = $message;
        $this->save(false, ['status', 'status_message']);
    }

    public function isInRangeTimeLapse(){
        $now_date = date('Y-m-d');
        $now_time = date('H:i:s');

        if($this->from_date <= $now_date && $now_date <= $this->to_date && $this->from_time <= $now_time && $now_time <= $this->to_time){
            return true;
        }
        return false;
    }

    /**
     * Devuelve la fecha en la que una notificacion programada va a ser enviada
     */
    public function getNextProgrammedSend()
    {
        if(!empty($this->scheduler)){
            $scheduler = Scheduler::getSchedulerObject($this->scheduler);
            return $scheduler->getNextSend($this);
        }

        return '';
    }


}
