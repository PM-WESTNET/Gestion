<?php

namespace app\modules\sale\models\bills;

use app\modules\accounting\components\CountableInterface;
use Yii;
use \app\modules\checkout\models\Payment;
use \app\modules\sale\modules\invoice\components\Invoice;
use yii\helpers\ArrayHelper;
use app\modules\sale\models\BillQuery;
use app\modules\sale\models\Currency;

/**
 * This is the model class for table "bill".
 *
 * @property integer $bill_id
 * @property string $date
 * @property string $time
 * @property integer $timestamp
 * @property integer $number
 * @property string $currency
 * @property double $amount
 * @property integer $customer_id
 * @property string $observation
 *
 * @property Customer $customer
 * @property BillDetail[] $billDetails
 * @property BillType $billType
 * @property string $ein
 * @property Date $ein_expiration
 */
class DeliveryNote extends \app\modules\sale\models\Bill
{
    /**
     * Determina si se debe registrar fecha de vencimiento para este comprobante
     * @var boolean 
     */
    static $expirable = false;
    
    /**
     * Determina si este comprobante es pagable
     * @var boolean 
     */
    static $payable = false;
    
    /**
     * Determina si el comprobante puede abrirse una vez q esta en estado "closed"
     * @var boolean
     */
    static $openable = false;
    
    /**
     * Determina si el comprobante puede desactivarse al generar un comprobante
     * a partir de el.
     * @var boolean
     */
    static $deactivable = true;
    
    /**
     * Indica si este documento debe ser considerado como un estado final
     * para un workflow dado. En caso de ser false y encontrarse activo, el 
     * workflow se considera como pendiente.
     * @var boolean
     */
    static $endpoint = false;
    
    public static function find()
    {
        return new BillQuery(get_called_class(), ['class' => 'delivery-note']);
    }

    public function behaviors()
    {
        return [
            'datestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date'],
                ],
                'value' => function(){return date('Y-m-d');},
            ],
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['time'],
                ],
                'value' => function(){return date('H:i');},
            ],
            'unix_timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['timestamp'],
                ],
            ],
        ];
    }
    
    /**
     * Inicializa tipo por defecto de factura, tipo por defecto de moneda, y valida estos datos.
     * @throws \yii\web\HttpException
     */
    public function init()
    {
        
        parent::init();
        
        $defaultCurrency = Yii::$app->params['bill_default_currency'];
        
        //Verificacion
        if(!Currency::find()->exists()
            || !Currency::find()->where(['currency_id'=>$defaultCurrency])->exists()){
            
                throw new \yii\web\HttpException(500, Yii::t('app','No currencies avaible or bad configuration.'));
        
        }
        
        $this->currency_id = $defaultCurrency;
        $this->payed = false;
        
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceClass(){
        
        return null;
        
    }

    /**
     * No aplica.
     * @return boolean
     */
    public function complete()
    {
        
        return false;

    }
        
    /**
     * Cierra la factura. Antes de cerrarla verifica que este completada.
     * En caso de no estar completada, se completa y luego se cierra.
     * @return boolean
     */
    public function close()
    {
        
        if($this->status != 'closed'){
            
            $transaction = $this->db->beginTransaction();

            try{
                $succeed = $this->updateAttributes(['status' => 'closed']);
                
                //Agrega el numero de comprobante
                $this->fillNumber();
                
                \app\modules\sale\components\BillExpert::manageStock($this);

                if($succeed){
                    $transaction->commit();
                }else{
                    $transaction->rollback();
                }
                
            }  catch (Exception $e) {
                $transaction->rollback();
                return false;
            }
                
        }

        //En caso de llegar a este punto, retornamos false
        return false;

    }

    /**
     * Genera la factura electronica en base a la clase del tipo de factura.
     *
     * @return bool
     */
    public function invoice(){
        
        throw new \yii\web\HttpException(500, 'Budget can not be invoiced.');
        
    }

    /**
     * Verifica el monto pagado de la factura, y actualiza el estado de la misma
     * si fue pagada completamente.
     * @param type $payment
     */
    public function checkPayment(){
    }
    
    /**
     * Devuelve el importe restante de la factura. Si aun no hay ningun pago, 
     * devolvera el importe total de la factura.
     * @return real
     */
    public function getDebt(){
    }
    
    public function getDeletable(){
        
        return true;
    }
    
    /**
     * Abre una factura: cambia su estado de completed a draft
     * @return boolean
     */
    public function open(){
        
        return false;
        
    }

    /**
     * Retorna un array con todos los impuestos aplicados a los items
     *
     * @return array
     */
    public function getTaxesApplied()
    {
        return [];
    }

    /**
     * Retorna la configuracion usadas para la registracion de movimientos
     * En principio se devuelven todos los impuestos, el total de bill y el resto.
     *
     * @return array
     */
    public function getConfig()
    {
        return [];
    }

    /**
     * Retorna los valores para cada item de la configuracion.
     *
     * @return array
     */
    public function getAmounts()
    {
        return [];
    }
    
    public function getIsEditable()
    {
        
        if($this->status == 'draft' && $this->expiration_timestamp > time()){
            return true;
        }
        
        return false;
        
    }
    
    /**
     * Se informa al objeto cdo el tipo cambia.
     */
    public function onTypeChange()
    {
        $this->updateAttributes([
            'expiration' => null,
            'expiration_timestamp' => null
        ]);
    }
    
}