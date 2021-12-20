<?php

namespace app\modules\westnet\notifications\models;
use app\components\db\ActiveRecord;

use Yii;

/**
 * This is the model class for table "payment_intentions_Accountability".
 *
 * @property int $payment_extension_history_id
 * @property string $from
 * @property int $customer_id
 * @property string $date
 * @property int $created_at
 *
 * @property Customer $customer
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
            [['payment_intention_accountability_id', 'customer_id', 'siro_payment_intention_id', 'total_amount', 'payment_method', 'status', 'collection_channel', 'rejection_code', 'payment_date', 'accreditation_date'], 'safe'],
        ];
    }

    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'payment_intention_accountability_id' => 'ID Intención de Pago por Rendición de Cuenta',
            'customer_id' => 'ID Cliente',
            'siro_payment_intention_id' => 'ID Intención de Pago',
            'total_amount' => 'Monto Total'
            'payment_method' => 'Método de Pago',
            'status' => 'Estado',
            'collection_channel' => 'Canal de Cobro',
            'rejection_code' => 'Código de Rechazo',
            'payment_date' => 'Fecha de Pago',
            'accreditation_date' => 'Fecha de Acreditación'

        ];
    }

}
