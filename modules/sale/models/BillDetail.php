<?php

namespace app\modules\sale\models;

use Yii;
use app\modules\config\models\Config;

/**
 * This is the model class for table "bill_detail".
 *
 * @property integer $bill_detail_id
 * @property double $amount
 * @property string $concept
 * @property double $qty
 * @property double $line_total
 * @property integer $bill_id
 * @property integer $product_id
 * @property integer $discount_id
 * @property double $unit_net_discount
 * @property integer $unit_id
 *
 * @property Bill $bill
 * @property Product $product
 * @property StockMovement[] $stockMovements
 * @property Discount $discount
 * @property Unit $unit
 */
class BillDetail extends \app\components\db\ActiveRecord
{
    private $old_unit_net_price;
    private $old_unit_final_price;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bill_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['unit_final_price','unit_net_price', 'qty', 'line_total', 'unit_net_discount'], 'number'],
            [['unit_id'], 'exist', 'targetClass' => Unit::class],
            [['bill_id', 'unit_id'], 'required'],
            [['bill_id', 'product_id', 'unit_id', 'discount_id'], 'integer'],
            [['concept'], 'string', 'max' => 255],
            [['unit'], 'safe'],
        ];

        if($this->product && $this->product->unit->type == 'int'){

            $rules[] = [['qty'], 'integer'];

        } else {

            $rules[] = [['qty'], 'number'];

        }

        //Si esta activado el stock secundario
        if(Config::getValue('enable_secondary_stock')){

            if($this->product && $this->product->secondaryUnit && $this->product->secondaryUnit->type == 'int'){

                $rules[] = [['secondary_qty'], 'integer'];

            } else {

                $rules[] = [['secondary_qty'], 'number'];

            }
        }

        return $rules;
    }

    public function behaviors()
    {
        return [
            'modifier' => [
                'class'=> 'app\components\db\ModifierBehavior'
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'bill_detail_id' => Yii::t('app', 'Bill Detail ID'),
            'unit_final_price' => Yii::t('app', 'Unit Amount'),
            'unit_net_price' => Yii::t('app', 'Unit Net Price'),
            'unit' => Yii::t('app', 'Unit'),
            'concept' => Yii::t('app', 'Concept'),
            'qty' => Yii::t('app', 'Qty'),
            'secondary_qty' => Yii::t('app', 'Secondary Qty'),
            'line_total' => Yii::t('app', 'Line Total'),
            'line_subtotal' => Yii::t('app', 'Line Subtotal'),
            'bill_id' => Yii::t('app', 'Bill ID'),
            'product_id' => Yii::t('app', 'Product ID'),
            'discount' => Yii::t('app', 'Discount'),
            'unit_net_discount' => Yii::t('app', 'Unit Net Discount'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBill()
    {
        return $this->hasOne(Bill::class, ['bill_id' => 'bill_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['product_id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStockMovements()
    {
        return $this->hasMany(StockMovement::class, ['bill_detail_id' => 'bill_detail_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnit()
    {
        return $this->hasOne(Unit::class, ['unit_id' => 'unit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiscount()
    {
        return $this->hasOne(Discount::class, ['discount_id' => 'discount_id']);
    }

    //Calcula el subtotal del detalle
    public function getSubtotal()
    {
        $subtotal = 0.0;

        if($this->type == 'discount') {
            $subtotal = $this->qty * $this->unit_net_discount;
        } else {
            $subtotal = $this->qty * ($this->unit_net_price - $this->unit_net_discount);
        }

        return round($subtotal, 2);
    }

    //Calcula el total del detalle
    public function getTotal()
    {
        return round($this->qty * $this->unit_final_price, 2) ;
    }

    public function getTotalDiscount($withTaxes = false)
    {
        if($withTaxes) {
            return round($this->qty * ($this->unit_net_discount * 1.21), 2);
        }

        return round($this->qty * $this->unit_net_discount, 2) ;
    }

    public function beforeSave($insert) {
        if(parent::beforeSave($insert)){

            $real_unit_price = $this->unit_net_price - $this->unit_net_discount;
            //Para que funcione el actualizador de precios in situ sobre el detalle de factura
            if($this->product){
                $this->unit_final_price = $real_unit_price + $this->product->calculateTaxes($real_unit_price);
            } else {
                try {
                    $pct = ( abs((($this->old_unit_final_price ? $this->old_unit_final_price : $this->unit_final_price) * 100 ) /
                            ($this->old_unit_net_price ? $this->old_unit_net_price : $real_unit_price))/100 );
                } catch(\Exception $ex) {
                    $pct = 1;
                }
                $this->unit_final_price = $real_unit_price * $pct;

            }
            //Guardamos el total y subtotal para mejor rendimiento al calcular montos de factura
            $this->line_total = $this->getTotal();
            $this->line_subtotal = $this->getSubtotal();

            return true;
        }else{
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        //Al actualizar exitosamente un detalle, se calcula nuevamente el importe de la factura
        $this->bill->updateAmounts();
    }

    public function getDeletable()
    {

        return $this->bill->getDeletable();

    }

    public function beforeDelete()
    {
        if(parent::beforeDelete()){
            if($this->getDeletable()){
                $this->unlinkAll('stockMovements', true);

                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();

        //Al actualizar exitosamente un detalle, se calcula nuevamente el importe de la factura
        $this->bill->updateAmounts();
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        $this->old_unit_net_price = round($this->unit_net_price, 2);
        $this->old_unit_final_price = round($this->unit_final_price, 2);
        parent::afterFind();
    }

    public function fields(){
        return [
            'bill_detail_id',
            'bill_id',
            'concept',
            'line_subtotal' => function($model){
                return Yii::$app->formatter->asDecimal($model->line_subtotal, 2);
            },
            'line_total' =>  function($model){
                return Yii::$app->formatter->asDecimal($model->line_total, 2);
            },
            'product_id',
            'qty',
            'secondary_qty',
            'type',
            'unit_final_price' => function($model){
                return Yii::$app->formatter->asDecimal($model->unit_final_price, 2);
            },
            'unit_net_price'=> function($model){
                return Yii::$app->formatter->asDecimal((float)$model->unit_net_price, 2);
            },
            'unit_net_discount',
            'unit_id'
        ];
    }
    
    /**
     * Devuelve el valor del iva aplicado al producto
     * @return type
     */
    public function getIva()
    {
        $tax_iva = Tax::findOne(['slug'=> 'iva']);
        $tax_rates = TaxRate::findAll(['tax_id' => $tax_iva->tax_id]);
        $tax_rates_ids = [];
        
        foreach ($tax_rates as $tr){
            $tax_rates_ids[] = $tr->tax_rate_id;
        }
        
        $phtr= ProductHasTaxRate::findOne(['product_id' => $this->product_id, 'tax_rate_id' => $tax_rates_ids]);
        
        return (double)$phtr->taxRate->pct;
    }
}