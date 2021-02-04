<?php

namespace app\modules\sale\models\bills;

use app\modules\accounting\components\CountableInterface;
use Yii;
use \app\modules\checkout\models\Payment;
use \app\modules\sale\modules\invoice\components\Invoice;
use yii\helpers\ArrayHelper;
use app\modules\sale\models\BillQuery;

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
class Credit extends \app\modules\sale\models\Bill
{
    
    /**
     * Determina si el comprobante puede abrirse una vez q esta en estado "closed"
     * @var boolean
     */
    static $openable = false;
    
    /**
     * Indica si este documento debe ser considerado como un estado final
     * para un workflow dado. En caso de ser false y encontrarse activo, el 
     * workflow se considera como pendiente.
     * @var boolean
     */
    static $endpoint = true;
    
    /**
     * Determina si este comprobante es pagable
     * @var boolean 
     */
    static $payable = false;
    
    /**
     * Cierra la nota. Antes de cerrarla verifica que este completada.
     * En caso de no estar completada, se completa y luego se cierra.
     * Al finalizar el escenario es 'closed'
     * @return boolean
     */
    public function close()
    {
        
        $transaction = $this->db->beginTransaction();
        
        try{
            //Si el estado es 'draft' primero debemos completar la factura
            if($this->status == 'draft'){
                if(!$this->complete()){
                    return false;
                }
            }

            if($this->status == 'completed'){

                \app\modules\sale\components\BillExpert::manageStock($this);
                
                //Si un tipo no tiene asociado factura electronica, la actualizacion de estado se debe realizar de todas formas
                $this->updateAttributes(['status' => 'closed']);

                //Factura electronica
                $succeed = $this->invoice();
                
                if($succeed){
                    $transaction->commit();
                    return true;
                }else {
                    $transaction->rollback();
                    return false;
                }
            }
        }  catch (\Exception $e){
            $transaction->rollback();
        }

        //En caso de llegar a este punto, retornamos false
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