<?php

namespace app\modules\sale\models;

use Yii;
use app\modules\config\models\Config;

/**
 * This is the model class for table "stock_movement".
 *
 * @property integer $stock_movement_id
 * @property string $type
 * @property string $concept
 * @property double $qty
 * @property integer $timestamp
 * @property string $date
 * @property string $time
 * @property double $balance
 * @property integer $product_id
 * @property integer $bill_detail_id
 * @property integer $company_id
 *
 * @property Product $product
 * @property BillDetail $billDetail
 */
class StockMovement extends \app\components\companies\ActiveRecord
{
    
    public function __construct($config = array()) {
        
        $from = get_called_class();
        
        if(!in_array($from, [
            'app\modules\sale\models\search\StockMovementSearch',
            'app\modules\sale\components\StockExpert'
        ]));
        
        parent::__construct($config);
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stock_movement';
    }
    
    /**
     * Datos iniciales
     */
    public function init()
    {
        $this->active = 1;
        parent::init();
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['timestamp'],
                ],
            ],
            'date' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date'],
                ],
                'value' => function(){return date('Y-m-d');},
            ],
            'time' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['time'],
                ],
                'value' => function(){return date('h:i');},
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['type', 'company_id', 'qty'], 'required'],
            [['timestamp', 'product_id', 'bill_detail_id'], 'integer'],
            [['date', 'time'], 'safe'],
            [['product_id'], 'required'],
            [['concept'], 'string', 'max' => 255],
        ];
        
        if(!empty($this->product)){
            
            $unit = $this->product->unit;
            
            if($unit->type == 'int')
                $rules[] = [['qty', 'stock', 'avaible_stock'], 'integer'];
            else
                $rules[] = [['qty', 'stock', 'avaible_stock'], 'number'];
            
            $secondaryUnit = $this->product->secondaryUnit;
            
            //Secondary stock
            if(Config::getValue('enable_secondary_stock') && $secondaryUnit){
                
                if($secondaryUnit->type == 'int')
                    $rules[] = [['secondary_qty', 'secondary_stock', 'secondary_avaible_stock'], 'integer'];
                else
                    $rules[] = [['secondary_qty', 'secondary_stock', 'secondary_avaible_stock'], 'number'];
            }
            
        }else{
            $rules[] = [['qty', 'stock', 'avaible_stock'], 'number'];
            if(Config::getValue('enable_secondary_stock') && $this->product && $this->product->secondaryUnit){
                $rules[] = [['secondary_qty', 'secondary_stock', 'secondary_avaible_stock'], 'number'];
            }
        }
        
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'stock_movement_id' => Yii::t('app', 'Stock Movement ID'),
            'type' => Yii::t('app', 'Type'),
            'concept' => Yii::t('app', 'Concept'),
            'qty' => Yii::t('app', 'Quantity'),
            'qtyAndUnit' => Yii::t('app', 'Quantity'),
            'secondary_qty' => Yii::t('app', 'Secondary Quantity'),
            'secondaryQtyAndUnit' => Yii::t('app', 'Secondary Quantity'),
            'timestamp' => Yii::t('app', 'Timestamp'),
            'date' => Yii::t('app', 'Date'),
            'time' => Yii::t('app', 'Time'),
            'stock' => Yii::t('app', 'Stock'),
            'avaible_stock' => Yii::t('app', 'Avaible Stock'),
            'secondary_stock' => Yii::t('app', 'Secondary Stock'),
            'secondary_avaible_stock' => Yii::t('app', 'Avaible Secondary Stock'),
            'product_id' => Yii::t('app', 'Product'),
            'bill_detail_id' => Yii::t('app', 'Bill Detail'),
            'company' => Yii::t('app', 'Company')
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['product_id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBillDetail()
    {
        return $this->hasOne(BillDetail::className(), ['bill_detail_id' => 'bill_detail_id']);
    }
    
    /**
     * Devuelve el color asociado al producto en formato RGB
     * @return string
     */
    public function getRgb()
    {
        
        return $this->product->rgb;
        
    }
    
    public function getDeletable()
    {

        return false;
        
    }
    
    /**
     * Actualizamos valores
     * @param type $insert
     * @param type $changedAttributes
     */
    public function afterSave($insert, $changedAttributes) 
    {
        parent::afterSave($insert, $changedAttributes);
        
        if($this->type == 'in'){
            $this->product->updateAttributes([
                'balance' => $this->product->balance + $this->qty,
                'secondary_balance' => $this->product->secondary_balance + $this->secondary_qty,
            ]);
        }elseif($this->type == 'out'){
            $this->product->updateAttributes([
                'balance' => $this->product->balance - $this->qty,
                'secondary_balance' => $this->product->secondary_balance - $this->secondary_qty,
            ]);
        }
        
    }
    
    //Devuelve el balance a la fecha del movimiento de stock actual
    public function getBalance($company = null, $secondary = false)
    {
        //Stock principal o secundario?
        $qty_attr = 'qty';
        if($secondary){
            $qty_attr = 'secondary_qty';
        }
        
        $in = StockMovement::find()->where([
            'active'=>1, 
            'product_id' => $this->product_id,
            'type'=>'in'
        ])->andWhere('stock_movement_id<='.$this->stock_movement_id);
        
        $out = StockMovement::find()->where([
            'active'=>1, 
            'product_id' => $this->product_id,
            'type'=>'out'
        ]);
        
        if($company){
            $in->andWhere(['company_id' => $company->company_id]);
            $out->andWhere(['company_id' => $company->company_id]);
        }

        return $in->sum($qty_attr) - $out->sum($qty_attr);
        
    }
    
    public function getSecondaryBalance($company = null)
    {
        return $this->getBalance($company, true);
    }
    
    /**
     * Devuelve la cantidad con la unidad
     * @return string
     */
    public function getQtyAndUnit()
    {
        if($this->product->unit->symbol_position == 'prefix'){
            return $this->product->unit->symbol . " $this->qty";
        }else{
            return "$this->qty". $this->product->unit->symbol;
        }
    }
    
    /**
     * Devuelve la cantidad con la unidad
     * @return string
     */
    public function getSecondaryQtyAndUnit()
    {
        if(!$this->secondary_qty){
            return null;
        }
        
        if($this->product->secondary_unit_id && $this->product->secondaryUnit->symbol_position == 'prefix'){
            return $this->product->secondaryUnit->symbol . " $this->secondary_qty";
        }elseif($this->product->secondary_unit_id){
            return $this->secondary_qty. $this->product->secondaryUnit->symbol;
        }
        
        return null;
    }
    
    /**
     * Devuelve la cantidad con la unidad
     * @return string
     */
    public function getStockAndUnit()
    {
        if($this->product->unit->symbol_position == 'prefix'){
            return $this->product->unit->symbol . " $this->stock";
        }else{
            return "$this->stock". $this->product->unit->symbol;
        }
    }
    
    /**
     * Devuelve la cantidad con la unidad
     * @return string
     */
    public function getSecondaryStockAndUnit()
    {
        if(!$this->secondary_qty){
            return null;
        }
        
        if($this->product->secondary_unit_id && $this->product->secondaryUnit->symbol_position == 'prefix'){
            return $this->product->secondaryUnit->symbol . " $this->secondary_stock";
        }elseif($this->product->secondary_unit_id){
            return $this->secondary_stock. $this->product->secondaryUnit->symbol;
        }
        
        return null;
    }
    
    public function fields()
    {
        
        return [
            'active',
            'avaible_stock',
            'company_id',
            'date' => function($model){
                return Yii::$app->formatter->asDate($model->date);
            },
            'product_id',
            'qty',
            'secondary_avaible_stock',
            'secondary_qty',
            'secondary_stock',
            'stock',
            'stock_movement_id',
            'time',
            'timestamp',
            'type',
            'combined_stock' => function($model){
                $stock = $this->stockAndUnit;
                $sstock = $this->secondaryStockAndUnit;
                if($sstock){
                    return "$stock | $sstock";
                }else{
                    return $stock;
                }
            }
        ];
        
    }
}
