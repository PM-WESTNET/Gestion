<?php

namespace app\modules\sale\models;

use app\modules\checkout\models\search\PaymentSearch;
use app\modules\westnet\notifications\components\transports\InfobipService;
use app\modules\westnet\notifications\components\transports\IntegratechService;
use Yii;

/**
 * This is the model class for table "customer_message".
 *
 * @property integer $customer_message_id
 * @property string $name
 * @property string $message
 * @property integer $status
 *
 * @property CustomerHasCustomerMessage[] $customerHasCustomerMessages
 */
class CustomerMessage extends \app\components\db\ActiveRecord
{

    const STATUS_ENABLED = 10;
    const STATUS_DISABLED = 31;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_message';
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
            [['name', 'message', 'status'], 'required'],
            [['message'], 'string'],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'customer_message_id' => Yii::t('app', 'Customer Message ID'),
            'name' => Yii::t('app', 'Name'),
            'message' => Yii::t('app', 'Message'),
            'status' => Yii::t('app', 'Status'),
            'customerHasCustomerMessages' => Yii::t('app', 'CustomerHasCustomerMessages'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerHasCustomerMessages()
    {
        return $this->hasMany(CustomerHasCustomerMessage::className(), ['customer_message_id' => 'customer_message_id']);
    }
    
        
             
    /**
     * @inheritdoc
     * Strong relations: CustomerHasCustomerMessages.
     */
    public function getDeletable()
    {
        if($this->getCustomerHasCustomerMessages()->exists()){
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

    public static function availablesFields()
    {
        return [
            'customer_name' => [
                'description' => 'Nombre del Cliente',
                'value' => function (Customer $customer) {
                    return $customer->fullName;
                }
            ],
            'payment_code' =>  [
                'description' => 'Código de Pago',
                'value' => function (Customer $customer) {
                    return $customer->payment_code;
                }
            ],
            'code' =>  [
                'description' => 'Número de Cliente',
                'value' => function (Customer $customer) {
                    return $customer->code;
                }
            ],
            'debt' => [
                'description' => 'Deuda del Cliente',
                'value' => function (Customer $customer) {
                    $paymentSearch = new PaymentSearch();
                    $paymentSearch->customer_id = $customer->customer_id;

                    return Yii::$app->formatter->asCurrency($paymentSearch->accountTotal());
                }
            ]
        ];
    }

    public function getValue($attr, $customer)
    {
        $fields = self::availablesFields();

        if (isset($fields[$attr])) {
            $value = call_user_func($fields[$attr]['value'], $customer);

            return $value;
        }

        return false;
    }

    public function send(Customer $customer, $phones = ['phone', 'phone2', 'phone3', 'phone4'])
    {
        $fields =  self::availablesFields();
        $template = $this->message;
        $message = $template;

        foreach ($fields as $key => $field) {
            if (strpos($template, '{'.$key.'}') !== false) {
                $message = str_replace('{'.$key.'}', $this->getValue($key, $customer), $message);
            }
        }

        $alerts = [];
        $errors = 0;
        foreach ($phones as $phone) {
            $number = $customer->getAttribute($phone);

            if ($number) {
                $response = InfobipService::sendSimpleSMS('Westnet', $number, $message);
                Yii::info('SMS response: '. print_r($response,1));
                if ($response['status'] === 'success') {
                    $chcm = new CustomerHasCustomerMessage([
                        'customer_id' => $customer->customer_id,
                        'customer_message_id' => $this->customer_message_id,
                        'timestamp' => time()
                    ]);

                    $chcm->save();
                    $alerts[] = [
                        'status' => 'success',
                        'phone' => $number,
                    ];
                }else {
                    $alerts[] = [
                        'status' => 'error',
                        'phone' => $number,
                    ];
                    $errors++;
                }
            }
        }

        return [
            'message' => $message,
            'status' => ($errors === 0 || YII_ENV_TEST ? 'success' : 'error'),
            'alerts' => $alerts
        ];


    }

    public function getStatusLabel()
    {
        $labels =[
            self::STATUS_ENABLED => Yii::t('app','Enabled'),
            self::STATUS_DISABLED => Yii::t('app','Disabled')
        ];

        return $labels[$this->status];
    }
}
