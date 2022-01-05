<?php

namespace app\modules\westnet\notifications\models;
use app\components\db\ActiveRecord;
use app\modules\sale\models\Customer;


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
            [['payment_intention_accountability_id', 'customer_id', 'siro_payment_intention_id', 'total_amount', 'payment_method', 'status', 'collection_channel', 'rejection_code', 'payment_date', 'accreditation_date', 'created_at', 'updated_at', 'payment_id'], 'safe'],
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

    public function getColletionChannelDescriptions(){
        $channelArray = $this->find()->select('collection_channel_description')->distinct()->all();
        return $channelArray;
    }
}
