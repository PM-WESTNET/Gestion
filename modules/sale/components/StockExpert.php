<?php

namespace app\modules\sale\components;

use Yii;
use app\modules\sale\models\StockMovement;

/**
 * Description of StockExpert
 *
 * @author mmoyano
 */
class StockExpert extends \yii\base\Component{
    
    public $optimize = false;
    
    public function createMove()
    {
        $sm = Yii::createObject([
            'class' => StockMovement::className(),
        ]);
        
        $sm->on(StockMovement::EVENT_BEFORE_INSERT, function($event){
            Yii::$app->getModule('sale')->stock->setNewStock($event->sender);
        });
        
        return $sm;
    }
    
    public function createMoveIn()
    {
        $sm = $this->createMove();
        $sm->type = 'in';
        return $sm;
    }
    
    private function createMoveOut()
    {
        $sm = $this->createMove();
        $sm->type = 'out';
        return $sm;
    }
    
    //Pedido por ingresar
    private function createReservationIn()
    {
        $sm = $this->createMove();
        $sm->type = 'r_in';
        return $sm;
    }
    
    private function createReservationOut()
    {
        $sm = $this->createMove();
        $sm->type = 'r_out';
        return $sm;
    }
    
    /**
     * Efectua el registro de movimientos de stock vinculados a un comprobante
     * de acuerdo al tipo del comprobante, derivando el control a una funcion
     * especifica por tipo de comprobante.
     * Transaccional.
     * @param app\modules\sale\models\Bill $bill
     * @throws \Exception
     * @throws \yii\web\HttpException
     */
    public function register($bill)
    {
        
        $reflex = new \ReflectionObject($bill);
        
        $method = 'register'.$reflex->getShortName();
        
        //Para stock:
        Yii::$app->set('company', $bill->company);
        
        if(method_exists($this, $method)){
        
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            
            try{
                
                $count = $this->$method($bill);
                $transaction->commit();
                
                return $count;
                
            } catch (\Exception $e) {
                $transaction->rollBack();
                //var_dump($e);die;
                throw $e;
            }
        
        }
            
        throw new \yii\web\HttpException(500, Yii::t('Stock expert could not manage this class.'));
        
    }
          
    private function registerBill($bill)
    {
        $count = 0;
        
        foreach($bill->billDetails as $detail){
            //Si se trata de un producto stockable, reducimos stock
            if(!empty($detail->product_id) && $detail->product->stockable){
                $stockMovement = $this->createMoveOut();
                $stockMovement->qty = $detail->qty;
                $stockMovement->secondary_qty = $detail->secondary_qty;
                $stockMovement->product_id = $detail->product_id;                
                $stockMovement->bill_detail_id = $detail->bill_detail_id;
                $stockMovement->concept = $bill->typeName;
                $stockMovement->company_id = $bill->company_id;
                $stockMovement->active = 1;
                if($stockMovement->save()){
                    $count++;
                }
            }
        }
        
        return $count;
        
    }
    
    private function registerDeliveryNote($bill)
    {
        $count = 0;
        
        foreach($bill->billDetails as $detail){
            //Si se trata de un producto stockable, reducimos stock
            if(!empty($detail->product_id) && $detail->product->stockable){
                $stockMovement = $this->createMoveOut();
                $stockMovement->qty = $detail->qty;
                $stockMovement->secondary_qty = $detail->secondary_qty;
                $stockMovement->product_id = $detail->product_id;                
                $stockMovement->bill_detail_id = $detail->bill_detail_id;
                $stockMovement->concept = $bill->typeName;
                $stockMovement->company_id = $bill->company_id;
                $stockMovement->active = 1;
                if($stockMovement->save()){
                    $count++;
                }
            }
        }
        
        return $count;
        
    }
    
    private function registerOrder($order)
    {
        $count = 0;
        
        foreach($order->billDetails as $detail){
            //Si se trata de un producto stockable, reducimos stock
            if(!empty($detail->product_id) && $detail->product->stockable){
                $stockMovement = $this->createReservationOut();
                $stockMovement->qty = $detail->qty;
                $stockMovement->secondary_qty = $detail->secondary_qty;
                $stockMovement->product_id = $detail->product_id;
                $stockMovement->bill_detail_id = $detail->bill_detail_id;
                $stockMovement->concept = $order->typeName;
                $stockMovement->company_id = $order->company_id;
                $stockMovement->active = 1;
                
                $stockMovement->expiration = $order->iso_expiration;
                $stockMovement->expiration_timestamp = $order->expiration_timestamp;
                
                if($stockMovement->save()){
                    $count++;
                }
            }
        }
        
        return $count;
        
    }
    
    private function registerCredit($bill)
    {
        $count = 0;
        
        foreach($bill->billDetails as $detail){
            //Si se trata de un producto stockable, reducimos stock
            if(!empty($detail->product_id) && $detail->product->stockable){
                $stockMovement = $this->createMoveIn();
                $stockMovement->qty = $detail->qty;
                $stockMovement->secondary_qty = $detail->secondary_qty;
                $stockMovement->product_id = $detail->product_id;                
                $stockMovement->bill_detail_id = $detail->bill_detail_id;
                $stockMovement->concept = $bill->typeName;
                $stockMovement->company_id = $bill->company_id;
                $stockMovement->active = 1;
                if($stockMovement->save()){
                    $count++;
                }
            }
        }
        
        return $count;
        
    }
    
    private function registerDebit($bill)
    {
        $count = 0;
        
        foreach($bill->billDetails as $detail){
            //Si se trata de un producto stockable, reducimos stock
            if(!empty($detail->product_id) && $detail->product->stockable){
                $stockMovement = $this->createMoveOut();
                $stockMovement->qty = $detail->qty;
                $stockMovement->secondary_qty = $detail->secondary_qty;
                $stockMovement->product_id = $detail->product_id;                
                $stockMovement->bill_detail_id = $detail->bill_detail_id;
                $stockMovement->concept = $bill->typeName;
                $stockMovement->company_id = $bill->company_id;
                $stockMovement->active = 1;
                if($stockMovement->save()){
                    $count++;
                }
            }
        }
        
        return $count;
        
    }
    
    private function registerBudget($budget)
    {
        //Nothing here
    }
    
    /**
     * Devuelve el stock del producto para la empresa dada, en base al registro
     * disponible en el ultimo movimiento de stock. Esto es asi para evitar
     * calculos en exceso. Si no se especifica empresa, devuelve el balance
     * registrado en el producto, que representa el balance total).
     * @param app\modules\sale\models\Product $product
     * @param app\modules\sale\models\Company $company
     * @param boolean $secondary
     * @return double
     */
    public function getStock($product, $company = null, $secondary = false)
    {
        
        //Attr
        $qty_attr = 'stock';
        if($secondary){
            $qty_attr = 'secondary_stock';
        }
        
        if($company == null){
            if($secondary){
                return $product->secondary_balance;
            }else{
                return $product->balance;
            }
        }
        
        //Last move
        $lm = StockMovement::find()->orderBy(['stock_movement_id' => SORT_DESC])->where([
            'active'=>1, 
            'product_id' => $product->product_id,
            'type'=>['in','out']
        ])->andWhere(['company_id'=>$company->company_id])->one();
        
        return $lm ? $lm->$qty_attr : 0;
        
    }
    
    /**
     * Devuelve el stock disponible del producto para la empresa dada. Toma el
     * stock real en base al registro disponible en el ultimo movimiento de 
     * stock, a traves de getStock(). A esto, agrega valores reservados, a traves
     * de getReservedStock().
     * @param app\modules\sale\models\Product $product
     * @param app\modules\sale\models\Company $company
     * @param boolean $secondary
     * @return double
     */
    public function getAvaibleStock($product, $company = null, $secondary = false)
    {
        
        return $this->getStock($product, $company, $secondary) 
                - $this->getReservedStock($product, $company, $secondary);
        
    }
    
    /**
     * Devuelve el stock actualmente reservado. El calculo es exhaustivo.
     * Se debe calcular (al menos diariamente (TODO)), porque varía de acuerdo
     * a los vencimientos de las reservas efectuadas.
     * @param app\modules\sale\models\Product $product
     * @param app\modules\sale\models\Company $company
     * @param boolean $secondary
     * @return double
     */
    public function getReservedStock($product, $company = null, $secondary = false)
    {
        
        //Attr
        $qty_attr = 'qty';
        if($secondary){
            $qty_attr = 'secondary_qty';
        }
        
        //In
        $in = StockMovement::find()->where([
            'active'=>1, 
            'product_id' => $product->product_id,
            'type'=>'r_in'
        ])->andFilterWhere(['company_id'=>$company ? $company->company_id : null])
            ->andWhere('expiration_timestamp>'.time().' OR expiration_timestamp IS NULL')->sum($qty_attr);
        
        //Out
        $out = StockMovement::find()->where([
            'active'=>1, 
            'product_id' => $product->product_id,
            'type'=>'r_out'
        ])->andFilterWhere(['company_id'=>$company ? $company->company_id : null])
            ->andWhere('expiration_timestamp>'.time().' OR expiration_timestamp IS NULL')->sum($qty_attr);
        
        return $out - $in;
        
    }
    
    /**
     * Completa el registro de stock total del movimiento $stockMovement para
     * optimizar el acceso a datos.
     * @param type $stockMovement
     * @throws \yii\web\HttpException
     */
    public function setNewStock($stockMovement){
        
        if(!$stockMovement->isNewRecord){
            throw new \yii\web\HttpException(500, 'Stock movement must be new.');
        }
        
        if($stockMovement->type == 'in'){
            $stockMovement->stock = $this->getStock($stockMovement->product, $stockMovement->company) + $stockMovement->qty;
            $stockMovement->secondary_stock = $this->getStock($stockMovement->product, $stockMovement->company, true) + $stockMovement->secondary_qty;
        
            $stockMovement->avaible_stock = $this->getAvaibleStock($stockMovement->product, $stockMovement->company);
            $stockMovement->secondary_avaible_stock = $this->getAvaibleStock($stockMovement->product, $stockMovement->company, true);
        }
        
        if($stockMovement->type == 'out'){
            $stockMovement->stock = $this->getStock($stockMovement->product, $stockMovement->company) - $stockMovement->qty;
            $stockMovement->secondary_stock = $this->getStock($stockMovement->product, $stockMovement->company, true) - $stockMovement->secondary_qty;
        
            $stockMovement->avaible_stock = $this->getAvaibleStock($stockMovement->product, $stockMovement->company);
            $stockMovement->secondary_avaible_stock = $this->getAvaibleStock($stockMovement->product, $stockMovement->company, true);
        }
        
        if($stockMovement->type == 'r_in'){
            $stockMovement->stock = $this->getStock($stockMovement->product, $stockMovement->company);
            $stockMovement->secondary_stock = $this->getStock($stockMovement->product, $stockMovement->company, true);
        
            $stockMovement->avaible_stock = $this->getAvaibleStock($stockMovement->product, $stockMovement->company) + $stockMovement->qty;
            $stockMovement->secondary_avaible_stock = $this->getAvaibleStock($stockMovement->product, $stockMovement->company, true) + $stockMovement->secondary_qty;
        }
        
        if($stockMovement->type == 'r_out'){
            $stockMovement->stock = $this->getStock($stockMovement->product, $stockMovement->company);
            $stockMovement->secondary_stock = $this->getStock($stockMovement->product, $stockMovement->company, true);
        
            $stockMovement->avaible_stock = $this->getAvaibleStock($stockMovement->product, $stockMovement->company) - $stockMovement->qty;
            $stockMovement->secondary_avaible_stock = $this->getAvaibleStock($stockMovement->product, $stockMovement->company, true) - $stockMovement->secondary_qty;
        }
                
    }
    
    /**
     * Calcula el stock recorriendo todos los movimientos de stock disponibles.
     * @param type $product
     * @param type $company
     * @param type $secondary
     * @return type
     */
    public function calculateStock($product, $company = null, $secondary = false)
    {
        
        //Attr
        $qty_attr = 'qty';
        if($secondary){
            $qty_attr = 'secondary_qty';
        }
        
        //In
        $in = StockMovement::find()->where([
            'active'=>1, 
            'product_id' => $product->product_id,
            'type'=>'in'
        ])->andFilterWhere(['company_id'=>$company ? $company->company_id : null])->sum($qty_attr);
        
        //Out
        $out = StockMovement::find()->where([
            'active'=>1, 
            'product_id' => $product->product_id,
            'type'=>'out'
        ])->andFilterWhere(['company_id'=>$company ? $company->company_id : null])->sum($qty_attr);
        
        return $in - $out;
        
    }
    
    /**
     * Calcula el stock recorriendo todos los movimientos de stock disponibles.
     * @param app\modules\sale\models\Product $product
     * @param app\modules\sale\models\Company $company
     * @param boolean $secondary
     * @return double
     */
    public function calculateAvaibleStock($product, $company = null, $secondary = false)
    {
        
        return $this->calculateStock($product, $company, $secondary) 
                - $this->getReservedStock($product, $company, $secondary);
        
    }
    
    /**
     * Este metodo existe para complementar los metodos calculate (actualmente
     * es un alias, pero a futuro, el metodo getReservedStock podria ser
     * optimizado, debiendo ser utilizado este metodo donde se requiere un
     * calculo exhaustivo del stock reservado).
     * @param app\modules\sale\models\Product $product
     * @param app\modules\sale\models\Company $company
     * @param boolean $secondary
     * @return double
     */
    public function calculateReservedStock($product, $company = null, $secondary = false)
    {
        
        return $this->getReservedStock($product, $company, $secondary);
        
    }
    
}
