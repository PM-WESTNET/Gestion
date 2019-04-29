<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 30/06/15
 * Time: 15:22
 */

namespace app\modules\invoice\components\einvoice\afip;

use app\modules\invoice\models\GenericType;
use app\modules\invoice\models\MoneyQuotation;
use app\modules\invoice\models\PointOfSale;

/**
 * Class Migrate
 * Helper para migrar las estructuras retornadas por los Web Services de afip a tablas.
 *
 * @package app\modules\invoice\components\einvoice\afip
 */
class Migrate
{

    private $serviceName;

    public function __construct($serviceName)
    {
        $this->serviceName = $serviceName;
    }

    public function genericType($type, $data)
    {
        try {
            if( is_array($data) ) {
                foreach($data as $value) {
                    $this->saveGenericType($type, $value);
                }
            } else {
                $this->saveGenericType($type, $data);
            }
            return true;
        } catch(\Exception $ex) {
            throw new \Exception("It could not be saved.",$ex->getCode(), $ex );
        }
    }

    public function moneyQuotation($data)
    {
        try{
            $obj = new MoneyQuotation();
            $obj->code  = $data['id'];
            $obj->price = $data['cotizacion'];
            $obj->date  = $data['fecha'];
            $obj->save();
            return true;
        } catch (\Exception $ex) {
            throw new \Exception("It could not be saved.",  $ex->getCode(), $ex );
        }
    }

    public function pointOfSale($data)
    {
        try{
            $obj = new PointOfSale();
            $obj->number    = $data['numero'];
            $obj->type      = $data['tipo'];
            $obj->blocked   = $data['bloqueado'];
            $obj->dateto    = $data['fechaBaja'];
            $obj->save();
            return true;
        } catch (\Exception $ex) {
            throw new \Exception("It could not be saved.", $ex->getMessage(), $ex->getCode(),$ex );
        }
    }

    private function saveGenericType($type, $data)
    {
        try {
            $obj = new GenericType();
            $obj->service       = $this->serviceName;
            $obj->type          = $type;
            $obj->code          = $data['id'];
            $obj->description   = $data['desc'];
            if (array_key_exists("desde",  $data)) {
                $obj->datefrom = $data['desde'];
            }
            if (array_key_exists("hasta",  $data)) {
                $obj->dateto = $data['hasta'];
            }
            $obj->save();
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }

    }
}