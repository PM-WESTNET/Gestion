<?php

namespace app\modules\checkout\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "payment_method".
 *
 * @property integer $payment_method_id
 * @property string $name
 * @property string $status
 * @property integer $register_number
 * @property boolean $send_ivr
 *
 * @property Payment[] $payments
 */
class PaymentMethod extends \app\components\db\ActiveRecord
{
    const STATUS_ENABLED = 'enabled';
    const STATUS_DISABLED = 'disabled';

    public static function tableName()
    {
        return 'payment_method';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['register_number', 'send_ivr'], 'boolean'],
            [['send_ivr'], 'default', 'value' => false],
            [['show_in_app'], 'default', 'value' => false],
            [['status'], 'in', 'range'=>['enabled','disabled']],
            [['type'], 'in', 'range'=>['exchanging','provisioning','account']],
            [['name'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'payment_method_id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
            'register_number' => Yii::t('app', 'Register Number?'),
            'type' => Yii::t('app', 'Tipo de pago'),
            'send_ivr' => Yii::t('app', 'Send to Ivr'),
            'show_in_app' => Yii::t('app', 'Show in app'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayments()
    {
        return $this->hasMany(PaymentItem::class, ['payment_method_id' => 'payment_method_id']);
    }

    /**
     * 
     * @return PaymentMethod[]
     */
    public static function getPaymentMethods($accounts = true){
        
        $query = self::find();
        $query->where(['status'=>'enabled']);
        
        if($accounts === false){
            $query->andWhere('type<>"account"');
        }
        
        return $query->all();
        
    }
    
    /**
     * Devuelve medios de pago de tipo cuenta corriente
     * @return PaymentMethod[]
     */
    public static function getAccountMethods(){
        
        $query = self::find();
        $query->where(['status'=>'enabled']);
        
        return $query->all();
        
    }
    
    /**
     * Devuelve true si hay algun medio de pago de tipo cuenta corriente, false
     * en caso contrario
     * @return boolean
     */
    public static function currentAccountAvaible(){
        
        $query = self::find();
        $query->where(['status'=>'enabled','type'=>'account']);
        
        return $query->exists();
        
    }
    
    public function getDeletable(){
        
        if($this->getPayments()->exists()){
            return false;
        }

        return true;
        
    }

    /**
     * Devuelve un listado de medios de pago que están disponibles para ser mostrados en la app
     */
    public static function getPaymentMethodsAvailableForApp()
    {
        return PaymentMethod::find()->where(['show_in_app' => true, 'status' => PaymentMethod::STATUS_ENABLED])->all();
    }

    /**
     * Devuelve los medios de pago para ser listados en un desplegable
     */
    public static function getPaymentMethodForSelect()
    {
        return ArrayHelper::map(PaymentMethod::find()->all(), 'payment_method_id', 'name');
    }

    /**
     * Obtiene el medio de pago Transferencia.
     */
    public static function getTransferencia()
    {
        $payment_method = PaymentMethod::findOne(['name' => 'Transferencia']);

        if(!$payment_method) {
            return false;
        }

        return $payment_method;
    }
}
