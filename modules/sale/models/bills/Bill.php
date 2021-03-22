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
class Bill extends \app\modules\sale\models\Bill
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