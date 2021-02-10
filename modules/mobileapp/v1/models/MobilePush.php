<?php

namespace app\modules\mobileapp\v1\models;

use app\components\helpers\FileLog;
use app\modules\agenda\models\Notification;
use app\modules\config\models\Config;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "mobile_push".
 *
 * @property integer $mobile_push_id
 * @property string $title
 * @property string $content
 * @property string $resume
 * @property string $status
 * @property integer $send_timestamp
 * @property integer $created_at
 * @property integer $notification_id
 * @property string $type
 * @property string $buttons
 *
 * @property MobilePushHasUserApp[] $mobilePushHasUserApps
 * @property UserApp[] $userApps
 */
class MobilePush extends ActiveRecord
{

    const STATUS_PENDING = 'pending';
    const STATUS_SENDED = 'sended';

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
            [['status', 'content', 'type', 'title', 'buttons', 'resume'], 'string'],
            [['send_timestamp', 'created_at', 'notification_id'], 'integer'],
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
            'resume' => Yii::t('app', 'Resume'),
            'content' => Yii::t('app', 'Content'),
            'status' => Yii::t('app', 'Status'),
            'send_timestamp' => Yii::t('app', 'Send Timestamp'),
            'created_at' => Yii::t('app', 'Created At'),
            'notification_id' => Yii::t('app', 'Notification'),
            'buttons' => Yii::t('app', 'Buttons'),
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
     * @return \yii\db\ActiveQuery
     */
    public function getNotification()
    {
        return $this->hasOne(\app\modules\westnet\notifications\models\Notification::class, ['notification_id' => 'notification_id']);
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

    public function beforeSave($insert)
    {
        if (empty($this->send_timestamp)){
            $this->send_timestamp = time();
        }

        if (empty($status)){
            $this->status = MobilePush::STATUS_PENDING;
        }

        return parent::beforeSave($insert);
    }

    /**
     * Envia notificacion al servidor de OneSignal
     *
     * https://documentation.onesignal.com/reference
     * @return bool
     */
    public function send($notification_id)
    {
        $data_formated = $this->getDataOneSignalFormat();
        $result = $this->sendThroughOneSignal($data_formated, $notification_id);

        if($result['total_sended'] > 0 || YII_ENV_TEST) {
            $this->updateAttributes(['status' => MobilePush::STATUS_SENDED]);
            return true;
        }

        return false;
    }

    /**
     * Devuelve un array con los datos de todos los MobilePushHasUserApp en el formato que espera las request OneSignal
     */
    public function getDataOneSignalFormat()
    {
        $app_id = Config::getValue('one_signal_app_id');
        $data = [];
        $title = $this->cleanTitle;
        $content = $this->cleanResume;

        if($this->getUserApps()->exists()) {
            foreach ($this->mobilePushHasUserApps as $mphua) {

                $title = $mphua->title ? $mphua->title : $this->title;
                $content = $mphua->resume ? $mphua->notificationResume : $this->cleanResume;
                array_push($data, [
                        'app_id' => $app_id,
                        'headings' => [
                            'en' => $title,
                            'es' => $title
                        ],
                        'contents' => [
                            'en' => $content,
                            'es' => $content
                        ],
                        'data' => ['mobile_push_id' => $this->mobile_push_id],
                        'include_player_ids' => [$mphua->userApp->player_id],
                        'mobile_push_has_user_app_id' => $mphua
                ]);
            }
        // Si la notificacion es de facturacion, y no hay ningun usuario especifico para enviarla, no lo incluimos
        } elseif ($this->type != 'invoice') {
            //Si no es de facturación, y no tiene usuarios en específico, ĺe indicamos a oneSignal que incluya todos los que están agregados a la plataforma
            array_push($data, [
                'app_id' => $app_id,
                'headings' => [
                    'en' => $title,
                    'es' => $title
                ],
                'contents' => [
                    'en' => $content,
                    'es' => $content
                ],
                'data' => ['mobile_push_id' => $this->mobile_push_id],
                'included_segments' => ['All']
            ]);
        }

        return $data;
    }

    /**
     * Hace el envio de los datos a OneSignal
     * @return Array
     */
    public function sendThroughOneSignal($one_signal_data, $notification_id)
    {
        $one_signal_rest_key = Config::getValue('one_signal_rest_key');
        $errors = '';
        $total_with_errors = 0;
        $total_sended = 0;
        $total_not_sended = 0;

        $mobile_push_has_user_app_ids_sent = [];
        Yii::$app->cache->set('notification_'.$notification_id.'_total', count($one_signal_data));
        Yii::$app->cache->set('notification_'.$notification_id.'_sended', 0);
        Yii::$app->cache->set('notification_'.$notification_id.'_with_errors', 0);
        Yii::$app->cache->set('notification_'.$notification_id.'_not_sended', 0);

        if(!YII_ENV_TEST) {
            foreach ($one_signal_data as $data) {
                $mphua = false;
                if(array_key_exists('mobile_push_has_user_app_id', $data)) {
                    $mphua = $data['mobile_push_has_user_app_id'];
                    unset($data['mobile_push_has_user_app_id']);
                }

                $ch = curl_init($this->api_url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-type: application/json',
                    'Authorization: Basic '.$one_signal_rest_key
                ]);

                $response = curl_exec($ch);
                FileLog::addLog('notifications', $response);
                if (curl_getinfo($ch, CURLINFO_RESPONSE_CODE) == 200){
                    $decoded_response = json_decode($response);

                    //Si no devuelve errores, el envío es exitoso
                    if (isset($decoded_response->errors)){
                        $total_with_errors ++;
                        foreach ($decoded_response->errors as $e){
                            Yii::info($e, 'mobile_push');
                            $errors .= json_encode($e) .' \n';
                        }
                    }

                    //One signal devuelve los usuarios alcanzados con la notificacion
                    if(isset($decoded_response->recipients)){
                        $total_sended = $total_sended + $decoded_response->recipients;

                    }

                    //Agregamos el id del MobilePushHasUserApp para que se marquen como enviados
                    if($mphua) {
                        array_push($mobile_push_has_user_app_ids_sent, $mphua);
                    }

                } else {
                    $decoded_response = json_decode($response);
                    if (isset($decoded_response->errors)){
                        $total_with_errors ++;
                        foreach ($decoded_response->errors as $e){
                            Yii::info($e, 'mobile_push');
                            $errors .= json_encode($e) .' \n';
                        }
                    }
                    $total_not_sended ++;
                }
                Yii::$app->cache->set('notification_'.$notification_id.'_total', count($one_signal_data));
                Yii::$app->cache->set('notification_'.$notification_id.'_sended', $total_sended);
                Yii::$app->cache->set('notification_'.$notification_id.'_with_errors', $total_with_errors);
                Yii::$app->cache->set('notification_'.$notification_id.'_not_sended', $total_not_sended);
            }

            MobilePushHasUserApp::setTimeSentAt($mobile_push_has_user_app_ids_sent);
        }

        return [
            'status' => true,
            'errors' => $errors,
            'total_to_send' => count($one_signal_data),
            'total_sended' => $total_sended,
            'total_sended_with_errors' => $total_with_errors,
            'total_not_sended' => $total_not_sended
        ];
    }

    /**
     * Agrega la relacion de MobilePush con UserApp
     */
    public function addUserApp($customer_id, $customer_data)
    {
        $userApps= UserApp::find()
            ->innerJoin('user_app_has_customer uahc', 'uahc.user_app_id= user_app.user_app_id')
            ->andWhere(['uahc.customer_id' => $customer_id])
            ->all();

        foreach ($userApps as $user){
            // Verificamos que el user app no haya sido agregado antes, para evitar duplicdad de notificaciones
            if(!MobilePushHasUserApp::find()
                ->andWhere([
                    'user_app_id' => $user->user_app_id,
                     'mobile_push_id' => $this->mobile_push_id])
                ->exists()) {

                $mphua = new MobilePushHasUserApp([
                    'user_app_id' => $user->user_app_id,
                    'mobile_push_id' => $this->mobile_push_id,
                    'customer_id' => $customer_id,
                    'notification_title' => MobilePush::replaceText($this->title, $customer_data),
                    'notification_content' => MobilePush::replaceText($this->content, $customer_data),
                    'resume' => MobilePush::replaceText($this->resume, $customer_data)
                ]);
                    
                if(!$mphua->save()){
                    Yii::info($mphua->getErrors());
                }
            }
        }
    }

    /**
     * Envía las notificaciones que están en estado pendiente
     */
    public static function sendPendingAll(){
        $mobile_pushes= self::find()->andWhere(['status' => self::STATUS_PENDING])->andWhere(['<=', 'send_timestamp', time()])->all();

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

    /**
     * Reemplaza los alias por los valores recibidos
     */
    public static function replaceText($text, $customer_data)
    {
        $replaced_text = $text;

        $replaced_text = array_key_exists('name', $customer_data) ? str_replace('@Nombre', $customer_data['name'], $replaced_text) : $replaced_text;
        $replaced_text = array_key_exists('phone', $customer_data) ? str_replace('@TelefonoFijo', $customer_data['phone'], $replaced_text) : $replaced_text;
        $replaced_text = array_key_exists('phone2', $customer_data) ? str_replace('@Celular1', $customer_data['phone2'], $replaced_text): $replaced_text;
        $replaced_text = array_key_exists('phone3', $customer_data) ? str_replace('@Celular2', $customer_data['phone3'], $replaced_text): $replaced_text;
        $replaced_text = array_key_exists('phone4', $customer_data) ? str_replace('@Celular3', $customer_data['phone4'], $replaced_text): $replaced_text;
        $replaced_text = array_key_exists('code', $customer_data) ? str_replace('@CodigoDeCliente', $customer_data['code'], $replaced_text): $replaced_text;
        $replaced_text = array_key_exists('payment_code', $customer_data) ? str_replace('@PaymentCode', $customer_data['payment_code'], $replaced_text): $replaced_text;
        $replaced_text = array_key_exists('node', $customer_data) ? str_replace('@Nodo', $customer_data['node'], $replaced_text): $replaced_text;
        //Si el saldo es mayor negativo (es decir que tiene dinero a favor), se le muestra 0
        $replaced_text = array_key_exists('saldo', $customer_data) ? str_replace('@Saldo', ($customer_data['saldo'] <= 0 ? '$0' : '$' .$customer_data['saldo']), $replaced_text): $replaced_text;
        $replaced_text = array_key_exists('company_code', $customer_data) ? str_replace('@CompanyCode', $customer_data['company_code'], $replaced_text): $replaced_text;
        $replaced_text = array_key_exists('debt_bills', $customer_data) ? str_replace('@FacturasAdeudadas', $customer_data['debt_bills'], $replaced_text): $replaced_text;
        $replaced_text = array_key_exists('product_extension_value', $customer_data) ? str_replace('@ValorDeExtensionDePago', ('$ '.round($customer_data['product_extension_value'])), $replaced_text): $replaced_text;
        $replaced_text = array_key_exists('email', $customer_data) ? str_replace('@EmailPrincipal', $customer_data['email'], $replaced_text): $replaced_text;
        $replaced_text = array_key_exists('email2', $customer_data) ? str_replace('@EmailSecundario', $customer_data['email2'], $replaced_text): $replaced_text;

        return $replaced_text;
    }

    /**
     * Elimina caracteres del titulo que la app no interpreta
     */
    public function getCleanTitle()
    {
        return strip_tags(str_replace('&nbsp;',' ', $this->title));
    }

    /**
     * Elimina caracteres del contenido que la app no interpreta
     */
    public function getCleanContent()
    {
        return strip_tags(str_replace('&nbsp;',' ', $this->content));
    }

    /**
     * Elimina caracteres del resumen que la app no interpreta
     */
    public function getCleanResume()
    {
        return strip_tags(str_replace('&nbsp;',' ', $this->resume));
    }
}
