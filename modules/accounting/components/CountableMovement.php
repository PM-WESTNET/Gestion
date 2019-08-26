<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 22/09/15
 * Time: 13:43
 */

namespace app\modules\accounting\components;

use app\modules\accounting\models\AccountingPeriod;
use app\modules\accounting\models\AccountMovement;
use app\modules\accounting\models\AccountMovementItem;
use Codeception\Util\Debug;
use Yii;


/**
 * Class CountableMovement
 * Clase con un solo metodo estatico para la creacion de movmientos contables.
 *
 * @package app\modules\accounting\components
 */
class CountableMovement
{
    private static $instance;

    private $errors = [];

    /**
     * Retorna una instancia de la clase.
     *
     * @return CountableMovement
     */
    public static function getInstance()
    {
        if(self::$instance == null) {
            self::$instance = new CountableMovement();
        }
        return self::$instance;
    }

    /**
     * Retorna los errores de los procesos.
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Guarda el movmiento contable con los parametros enviados.
     *
     * @param $description
     * @param $company_id
     * @param array $items
     * @param string $status
     * @return bool
     */
    public function createMovement($description, $company_id, $items=array(), $status="draft",
                                          $partner_distribution_model_id = null, $date = "now")
    {
        Debug::debug('Llego a createMovement');
        $this->errors = [];
        $movement = new AccountMovement();
        try{
            $movement->date = (new \DateTime($date))->format('d-m-Y');
            $movement->time = date("H:i:s");
            $movement->description = $description;
            $movement->status = $status;
            $movement->company_id = $company_id;
            Debug::debug('Antes del periodo');
            $movement->accounting_period_id = AccountingPeriod::getActivePeriod()->accounting_period_id;
            Debug::debug('Despues del periodo');
            $movement->partner_distribution_model_id = $partner_distribution_model_id;

            Debug::debug('Por validar');
            if ($movement->validate() && $movement->save()) {
                Debug::debug('Mov validado y guardado');
                foreach ($items as $item) {
                    $movement->link('accountMovementItems', $item);
                }
                if ($movement->validateMovement()) {
                    return $movement->account_movement_id;
                } else {
                    $movement->updateAttributes(['status'=>AccountMovement::STATE_BROKEN]);
                }
            } else {
                $this->errors[] = Yii::t('accounting', 'The movement is invalid.');
                foreach($movement->getErrors() as $key => $errors) {
                    foreach($errors as $error) {
                        $this->errors[] = $error;
                    }
                }
            }

        } catch(\Exception $ex) {
            if(!$movement->account_movement_id) {
                throw new \Exception(Yii::t('accounting', 'The movement could not be created.')  . $ex->getMessage());
            } else {
                $this->errors[] = Yii::t('accounting', 'The debit and credit is not equal.');
            }
        }
        return $movement->account_movement_id;
    }

    /**
     * Se revierte un movimiento contable.
     *
     * @param $account_movement_id
     * @param string $description
     * @return bool
     */
    public function revertMovement($account_movement_id, $description = '')
    {
        try {
            $this->errors = [];

            if($account_movement_id) {
                $items = [];

                $accountMovement = AccountMovement::findOne(['account_movement_id'=>$account_movement_id]);
                foreach( $accountMovement->accountMovementItems as $amiOld ) {
                    $ami = new AccountMovementItem();
                    $ami->debit         = $amiOld->credit;
                    $ami->credit        = $amiOld->debit;
                    $ami->account_id    = $amiOld->account_id;
                    $ami->status        = AccountMovementItem::STATE_DRAFT;
                    $items[] = $ami;
                }

                return $this->createMovement(($description ? $description: Yii::t('accounting', 'Revert') ) . " - " . $accountMovement->description,
                    $accountMovement->company_id,
                    $items,
                    null,
                    $accountMovement->partner_distribution_model_id
                );

            }
        } catch(\Exception $ex) {
            $this->errors[] = Yii::t('accounting', 'The movement could not be created.')  . $ex->getMessage();
        }
        return false;
    }
}