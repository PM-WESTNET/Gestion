<?php

namespace app\modules\sale\components;

use app\components\helpers\ClassFinderHelper;
use Yii;
use app\modules\sale\models\Bill;
use app\modules\sale\models\bills\Order;
use app\modules\sale\models\bills\Budget;
use app\modules\sale\models\BillType;
use app\modules\sale\models\bills\DeliveryNote;
use webvimark\modules\UserManagement\models\User;
/**
 * Description of BillExpert
 *
 * @author mmoyano
 */
class BillExpert {
    
    /**
     * Crea un objeto Bill de la clase indicada
     * @param type $type
     * @return type
     * @throws \yii\web\ForbiddenHttpException
     * @throws \yii\web\HttpException
     */
    public static function createBill($type)
    {
        $type = BillType::findOne($type);

        //El usuario puede crear este tipo de documento?
        if(!YII_ENV_TEST) {
            if(!static::checkAccess('create', $type->class)){
                throw new \yii\web\ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
            }
        }

        if($type != null && class_exists($type->class)){
            $bill = Yii::createObject($type->class);
            $bill->bill_type_id = $type->bill_type_id;
            $bill->class = $type->class;
            return $bill;
        }
        
        throw new \yii\web\HttpException(500, Yii::t('app', 'Unknow bill class.'));
        
    }
    
    /**
     * El usuario tiene acceso para generar este tipo de factura?
     * @param string $type
     * @return boolean
     */
    public static function checkAccess($action, $type)
    {
        if(Yii::$app instanceof Yii\console\Application) {
            return true;
        }

        //Obtenemos el nombre corto de la clase
        $reflex = new \ReflectionClass($type);
        $class = $reflex->getShortName();
        
        //Con inflector obtenemos el nombre como id
        $id = \yii\helpers\Inflector::camel2id($class);

        return User::hasPermission("user-can-$action-$id", true);
        
    }

    /**
     * Devuelve una lista de todas las clases bajo \app\modules\sale\models\bills\
     * @param boolean $keys
     * @return array
     */
    public static function getBillClasses($keys = false)
    {
        $classes = ClassFinderHelper::findClasses(['@app/modules/sale/models/bills/']);
        return array_combine($classes, $classes);
    }
    
    /**
     * Devuelve una lista de todas los tipos de comprobante (BillType)
     * que no son clases endpoin (static $endpoint = false)
     * https://docs.google.com/document/d/1rf44UE0cUj0rSmS5jy_U40R0VNuouh3RcQR7A3TWrtI/edit#heading=h.8653qyhayrkz
     * @param boolean $keys
     * @return array
     */
    public static function getNonEndpointBillTypes()
    {
        
        $classes = static::getBillClasses();
        
        foreach($classes as $i => $class){
            if($class::$endpoint){
                unset($classes[$i]);
            }
        }
        
        $query = BillType::find();
        $query->where(['class' => $classes]);
        
        return $query->all();
        
    }
    
    /**
     * Genera un nuevo comprobante a partir de $source, y desactiva $source.
     * En caso de que el nuevo comprobante no abarque todos los detalles de
     * $source, se genera un comprobante extra. El comprobante extra
     * podra ser accedido mediante el footprint del grupo.
     * Transaccional.
     * @param Bill $source
     * @param int $type
     * @param array $details
     * @return false|Bill
     */
    public static function generate($source, $type, $details = array())
    {
        //Para evitar inconsistencias, utilizamos transacciones
        $transaction = $source->db->beginTransaction();
        
        try
        {
        
            //Desactivamos el source si es requerido (hay comprobantes que no deben desactivarse)
            if($source::$deactivable){
                $source->deactivate();
            }
            
            //Generamos el nuevo comprobante
            $target = self::createTarget($source, $type, $details);
            
            //Si quedan remanentes de source, generamos un comprobante extra
            if(count($details) > 0 && count($details) < $source->getBillDetails()->count()){
                
                $extraDetails = $source->getBillDetails()->where(['NOT IN', 'bill_detail_id', $details])->asArray()->all();
                $extraDetailsArray = [];
                foreach($extraDetails as $detail){
                    $extraDetailsArray[] = $detail['bill_detail_id'];
                }
                
                $extra = self::createTarget($source, $source->bill_type_id, $extraDetailsArray);
                
                //Depende del estado del nuevo comprobante, es a donde debemos apuntar el link que mostramos al usuario
                if($extra->status == 'draft'){
                    $extraAction = 'update';
                }else{
                    $extraAction = 'view';
                }
                
                Yii::$app->session->setFlash('info', \yii\helpers\Html::a(
                    Yii::t('app', 'A new {type} has been generated with the remaining details.', ['type' => $source->typeName]),
                    ["bill/$extraAction", 'id' => $extra->bill_id],
                    ['target' => '_blank']
                ));
            }
            
            $transaction->commit();
            
            return $target;
        
        }catch(yii\db\Exception $e){
            $transaction->rollback();
            
            return false;
        }
        
    }
    
    /**
     * Genera un nuevo comprobante a partir de $source, solo para los detalles
     * especificados en $details.
     * @param Bill $source
     * @param int $type
     * @param array $details
     */
    private static function createTarget($source, $type, $details)
    {
        
        $target = self::createBill($type);
        
        $target->footprint = $source->footprint;
        $target->status = 'draft';
            
        $target->currency_id = $source->currency_id;
        $target->observation = $source->observation;

        $target->expiration = Yii::$app->formatter->asDate($source->expiration);
        $target->expiration_timestamp = $source->expiration_timestamp;

        $target->company_id = $source->company_id;
        $target->customer_id = $source->customer_id;
        
        $target->save();
        
        foreach($source->billDetails as $detail){
            //Si no se ha especificado ningun detalle, copiamos todos los detalles
            if(empty($details) || in_array($detail->bill_detail_id, $details)){

                $target->addDetail([
                    'product_id'=>$detail->product_id,
                    'concept'=>$detail->concept,
                    'unit_net_price'=>$detail->unit_net_price,
                    'unit_final_price'=>$detail->unit_final_price,
                    'qty'=>$detail->qty,
                    'secondary_qty'=>$detail->secondary_qty,
                    'type'=>$detail->type,
                    'unit_id'=>$detail->unit_id
                ]);

            }
        }
        
        //
        if($source->status == 'closed'){
            $reflex = new \ReflectionObject($source);
            $class = $reflex->getShortName();

            if($class == 'DeliveryNote'){
                $target->close();
            }
                
        }

        return $target;
        
    }
    
    /**
     * Los movs de stock se pueden efectuar en una factura o en un remito,
     * dependiendo del flujo del grupo de comprobantes actual (agrupados por
     * footprint). Si durante el flujo se utilizó un remito, el remito habra
     * registrado el mov de stock. Caso contrario, la factura registrará el
     * mov de stock. Para más info:
     * https://docs.google.com/document/d/1rf44UE0cUj0rSmS5jy_U40R0VNuouh3RcQR7A3TWrtI/edit#heading=h.dj982sdrh8xx
     * @param type $bill
     */
    public static function manageStock($bill)
    {
        
        $reflex = new \ReflectionObject($bill);
        $class = $reflex->getShortName();
        
        if($class == 'Bill'){
            
            if(!DeliveryNote::find()->where(['footprint' => $bill->footprint, 'status' => 'closed'])->exists()){
                Yii::$app->getModule('sale')->stock->register($bill);
            }
            
        }elseif($class == 'DeliveryNote' || $class == 'Credit' || $class == 'Debit'){
            Yii::$app->getModule('sale')->stock->register($bill);
        }
        
    }
}
