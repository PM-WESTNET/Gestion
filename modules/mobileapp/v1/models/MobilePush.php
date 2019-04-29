<?php

namespace app\modules\mobileapp\v1\models;

use app\components\db\ActiveRecord;
use app\modules\config\models\Config;
use Yii;

/**
 * This is the model class for table "mobile_push".
 *
 * @property integer $mobile_push_id
 * @property string $title
 * @property string $content
 * @property string $status
 * @property integer $send_timestamp
 * @property integer $created_at
 * @property string $type
 *
 * @property MobilePushHasUserApp[] $mobilePushHasUserApps
 * @property UserApp[] $userApps
 */
class MobilePush extends \app\components\db\ActiveRecord
{

    public $extra_data;

    private $api_url = 'https://onesignal.com/api/v1/notifications';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mobile_push';
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

        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content',], 'required'],
            [['status', 'content', 'type'], 'string'],
            [['send_timestamp', 'created_at'], 'integer'],
            [['title'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mobile_push_id' => Yii::t('app', 'Mobile Push ID'),
            'title' => Yii::t('app', 'Title'),
            'content' => Yii::t('app', 'Content'),
            'status' => Yii::t('app', 'Status'),
            'send_timestamp' => Yii::t('app', 'Send Timestamp'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMobilePushHasUserApps()
    {
        return $this->hasMany(MobilePushHasUserApp::className(), ['mobile_push_id' => 'mobile_push_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserApps()
    {
        return $this->hasMany(UserApp::className(), ['user_app_id' => 'user_app_id'])->viaTable('mobile_push_has_user_app', ['mobile_push_id' => 'mobile_push_id']);
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
     * Weak relations: MobilePushHasUserApps, UserApps.
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
     * Envia notificacion al servidor de OneSignal
     *
     * https://documentation.onesignal.com/reference
     * @return bool
     */
    public function send(){

        $data= [
            'app_id' => Config::getValue('one_signal_app_id'),

            'contents' =>  ['en' => $this->title . ' - '. $this->content, 'es' => $this->title . ' - '. $this->content],
            'data' => $this->extra_data,
            //'time_to_live' => !empty($this->time_to_live) ? $this->time_to_live : Config::getValue('notification_time_to_live'),
        ];

        // Si hay usuarios especificos, solo mando la notificacion a esos usuarios, de lo contrario se les envia a todos los usuarios
        if ($this->userApps){
            foreach ($this->userApps as $user){
                $data['include_player_ids'][] = $user->player_id;
            }
        }else{
            /**
             * Si la notificacion es de facturacion, y no hay ningun usuario especifico para enviarsela, no enviamos nada
             */
            if ($this->type == 'invoice'){
                return true;
            }

            $data['included_segments'] = ['All'];

        }

        $ch= curl_init($this->api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-type: application/json',
            'Authorization: Basic '.Config::getValue('one_signal_rest_key')
        ]);

        $response = curl_exec($ch);

        if (curl_getinfo($ch, CURLINFO_RESPONSE_CODE) !== 200){
            return false;
        }

        $decoded_response= json_decode($response);

        if (isset($decoded_response->errors)){
            $errors= "";
            foreach ($decoded_response->errors as $e){
                $errors .= json_encode($e) .' \n';
            }
            //Yii::$app->session->addFlash('warning', Yii::t('app','Notification has been sended but has errors'). ' Errors: '. $errors);
        }else {
            //Yii::$app->session->addFlash('success', Yii::t('app', 'Notification has been sended succesfully'));
        }

        $this->status= 'sended';
        $this->updateAttributes(['status']);

        return true;

    }

    public function beforeSave($insert)
    {
        if (empty($this->send_timestamp)){
            $this->send_timestamp = time();
        }

        if (empty($status)){
            $this->status= 'pending';
        }


        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function addUserApp($customer_id){
        $userApps= UserApp::find()
            ->innerJoin('user_app_has_customer uahc', 'uahc.user_app_id= user_app.user_app_id')
            ->andWhere(['uahc.customer_id' => $customer_id])
            ->all();

        foreach ($userApps as $user){
           $mphua= new MobilePushHasUserApp(['user_app_id' => $user->user_app_id, 'mobile_push_id' => $this->mobile_push_id]);

           if(!$mphua->save()){
               Yii::info($mphua->getErrors());
            }


        }
    }

    public static function sendPendingAll(){
        $mobile_pushes= self::find()->andWhere(['status' => 'pending'])->andWhere(['<=', 'send_timestamp', time()])->all();

        echo 'Mobile Pushes: ' . count($mobile_pushes);
        echo "\n";

        foreach ($mobile_pushes as $mobile_push){
            if(!$mobile_push->send()){
                echo 'Ocurrio un error al mandar la notificación ' . $mobile_push->title;
                echo "\n";
                return false;
            }
        }

        return true;
    }

}
