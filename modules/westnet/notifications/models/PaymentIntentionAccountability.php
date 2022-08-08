<?php

namespace app\modules\westnet\notifications\models;
use app\components\db\ActiveRecord;
use app\modules\sale\models\Customer;
use app\modules\sale\models\Company;


use Yii;

/**
 * This is the model class for table "payment_intentions_Accountability".
 *
 * @property int $payment_intention_accountability_id
 * @property int $customer_id
 * @property int $siro_payment_intention_id
 * @property double $total_amount
 * @property int $payment_method
 * @property int $status
 * @property string $collection_channel
 * @property string $rejection_code
 * @property date $payment_date
 * @property date $accreditation_date
 * @property date $created_at
 * @property date $updated_at
 * @property int $payment_id
 *
 */
class PaymentIntentionAccountability extends ActiveRecord
{
    public $customer_name;
    public $company_name;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_intentions_accountability';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['siro_payment_intention_id','customer_id'], 'number'],
            [['collection_channel_description','customer_name','company_name','payment_intention_accountability_id', 'customer_id', 'siro_payment_intention_id', 'total_amount', 'payment_method', 'status', 'collection_channel', 'rejection_code', 'payment_date', 'accreditation_date', 'created_at', 'updated_at', 'payment_id'], 'safe'],
        ];
    }

    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'payment_intention_accountability_id' => 'ID',
            'customer_id' => 'ID Cliente',
            'siro_payment_intention_id' => 'ID Intención de Pago',
            'total_amount' => 'Monto Total',
            'payment_method' => 'Método de Pago',
            'status' => 'Estado',
            'collection_channel' => 'Canal de Cobro',
            'rejection_code' => 'Código de Rechazo',
            'payment_date' => 'Fecha de Pago',
            'accreditation_date' => 'Fecha de Acreditación',
            'created_at' => 'Fecha de Creación',
            'updated_at' => 'Fecha de Actualización',
            'payment_id' => 'ID Pago',

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['customer_id' => 'customer_id']);
    }
    /**
     * Gives back an array of distinct values of CollectionChannelDescription from the models table
     */
    public function getArrColletionChannelDescriptions(){
        $arr = $this->find()->select('collection_channel_description')->distinct()->asArray()->indexBy('collection_channel_description')->column(); //this one indexes by its own name, simplyfing the process of filtering later  
        return $arr;
    }

    /**
     * Gives back an array of distinct values of Status from this models table
     */
    public function getArrStatus(){
        $arr = $this->find()->select('status')->distinct()->asArray()->indexBy('status')->column(); //this one indexes by its own name, simplyfing the process of filtering later  
        return $arr;
    }

    public function getArrPaymentMethod(){
        $arr = $this->find()->select('payment_method')->distinct()->asArray()->indexBy('payment_method')->column(); //this one indexes by its own name, simplyfing the process of filtering later  
        return $arr;
    }
}
