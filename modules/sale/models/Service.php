<?php

namespace app\modules\sale\models;

use Yii;

/**
 * This is the model class for table "product".
 *
 * @property integer $product_id
 * @property string $name
 * @property string $system
 * @property string $code
 * @property string $description
 * @property string $status
 * @property double $balance
 * @property integer $create_timestamp
 * @property integer $update_timestamp
 * @property integer $unit_id
 * @property string $class
 * @property string $productcol
 *
 * @property BillDetail[] $billDetails
 * @property Unit $unit
 * @property ProductDiscount[] $productDiscounts
 * @property ProductHasCategory $productHasCategory
 * @property Category[] $categories
 * @property ProductPrice[] $productPrices
 * @property StockMovement[] $stockMovements
 */
class Service extends Product
{
    
    const TYPE = 'service';
    
    public function init() {
        parent::init();
        $this->initial_stock = null;
        
        $this->type = self::TYPE;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description', 'class'], 'string'],
            [['unit_id'], 'integer'],
            [['taxes'], 'number'],
            [['initial_stock'], 'number'],
            [['status'], 'in', 'range'=>['enabled','disabled']],
            [['unit_id','name'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['code'], 'string', 'max' => 45],
            [['code'], 'unique'],
            [['categories'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'system' => Yii::t('app', 'System'),
            'code' => Yii::t('app', 'Code'),
            'barcode' => Yii::t('app', 'Barcode'),
            'description' => Yii::t('app', 'Description'),
            'status' => Yii::t('app', 'Status'),
            'balance' => Yii::t('app', 'Stock'),
            'create_timestamp' => Yii::t('app', 'Create Timestamp'),
            'update_timestamp' => Yii::t('app', 'Update Timestamp'),
            'unit_id' => Yii::t('app', 'Unit'),
            'class' => Yii::t('app', 'Class'),
            'finalPrice' => Yii::t('app', 'Final Price'),
            'netPrice' => Yii::t('app', 'Net Price'),
            'initial_stock' => Yii::t('app', 'Initial stock'),
            'taxes' => Yii::t('app','Taxes'),
            'stock' => Yii::t('app', 'Stock'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBillDetails()
    {
        return $this->hasMany(BillDetail::className(), ['product_id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServicePrices()
    {
        return $this->hasMany(ProductPrice::className(), ['product_id' => 'product_id']);
    }
    
    /**
     * Inicializa el stock de un producto, creando un movimiento de stock de entrada
     * inicial por la cantidad indicado por $initialStock. Si el stock inicial es 0,
     * no genera movimiento de entrada.
     * @param int $initialStock
     */
    private function initStock($initialStock)
    {
        
        $this->balance = null;
        
    }
    
    public function fields() 
    {
        return [
            'uid',
            'code',
            'create_timestamp',
            'description',
            'name',
            'product_id',
            'status',
            'system',
            'taxes',
            'unit_id',
            'netPrice'=>function($model, $field){
                return $model->netPrice;
            },
            'finalPrice'=>function($model, $field){
                return $model->finalPrice;
            },
        ];
    }
    
    public function getInStock($company=null){
        
        return true;
        
    }
    
    /**
     * Definimos dos atributos virtuales para trabajar con los ids. Al extender
     * de Product, hereda el attr product_id. Para trabajar de manera mas
     * consistente, es conveniente que el nombre del attr para id sea
     * service_id.
     */
    public function getService_id(){
        return $this->product_id;
    }
    public function setService_id($id){
        $this->product_id = $id;
    }
    
    /**
     * Es stockable??
     * @return boolean
     */
    public function getStockable(){
        return false;
    }
    
}
