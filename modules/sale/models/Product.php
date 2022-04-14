<?php

namespace app\modules\sale\models;

use app\components\companies\ActiveRecord;
use app\modules\accounting\models\Account;
use Yii;
use \Hackzilla\BarcodeBundle\Utility\Barcode;
use app\modules\sale\modules\contract\models\Plan;
use app\modules\westnet\models\ProductCommission;
use yii\behaviors\SluggableBehavior;
use app\modules\media\behaviors\MediaBehavior;
use app\modules\config\models\Config;

/**
 * This is the model class for table "product".
 *
 * @property integer $product_id
 * @property string $name
 * @property string $system
 * @property string $code
 * @property string $description
 * @property string $status
 * @property integer $create_timestamp
 * @property integer $update_timestamp
 * @property integer $unit_id
 * @property string $class
 * @property string $productcol
 * @property integer $account_id
 * @property string $balance
 *
 * @property BillDetail[] $billDetails
 * @property Unit $unit
 * @property ProductDiscount[] $productDiscounts
 * @property ProductHasCategory $productHasCategory
 * @property Category[] $categories
 * @property ProductPrice[] $productPrices
 * @property StockMovement[] $stockMovements
 * @property Account[] $account
 * @property FundingPlan[] $fundingPlan
 * @property Discount[] $discounts
 */
class Product extends ActiveRecord
{
    const STATUS_ENABLED = 'enabled';
    const STATUS_DISABLED = 'disabled';

    protected static $companyRequired = false;
    
    public $initial_stock;
    public $initial_secondary_stock;
    
    public $rgb;
    
    private $_categories;
    private $_rates;
    
    public $stockQuickMovement;
    public $secondaryStockQuickMovement;
    
    const TYPE = 'product';
    
    //Para utilizar al mostrar stock:
    public $stockCompany = null;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product';
    }
    
    /**
     * Instancia un nuevo objeto de acuerdo al tipo. El objeto puede ser:
     *  Service.
     *  Plan.
     *  Product.
     * @param array $row
     * @return \app\modules\sale\models\Plan|\app\modules\sale\models\Service|\self
     */
    public static function instantiate($row)
    {
        switch ($row['type']) {
            case Service::TYPE:
                return new Service();
            case Plan::TYPE:
                return new Plan();
            default:
               return new self;
        }
    }
    
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['create_timestamp','update_timestamp'],
                    yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => 'update_timestamp',
                ],
            ],
            'slug'=>[
                'class' => SluggableBehavior::class,
                'slugAttribute' => 'system',
                'attribute' => 'name',
                'ensureUnique'=>true
            ],
            'media' => [
                'class' => MediaBehavior::class,
            ]
        ];
    }
    
    /**
     * Inicializa stock en 0
     */
    public function init() {
        parent::init();
        $this->initial_stock = 0;
        $this->initial_secondary_stock = 0;
        
        $this->status = 'enabled';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['description', 'type'], 'string'],
            [['unit_id', 'secondary_unit_id'], 'integer'],
            [['taxRates'], 'safe'],
            [['initial_stock', 'initial_secondary_stock'], 'number'],
            [['stockQuickMovement'], 'integer', 'min'=>0],
            [['status'], 'in', 'range'=>['enabled','disabled']],
            [['unit_id','name', 'status'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['code'], 'string', 'max' => 45],
            [['code'], 'unique'],
            [['categories'], 'safe'],
            [['product_commission_id'], 'integer']
        ];

        if (Yii::$app->getModule('accounting')) {
            $rules[] = [['account'], 'safe'];
            $rules[] = [['account_id'], 'integer'];
        }
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = [
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
            'type' => Yii::t('app', 'Type'),
            'finalPrice' => Yii::t('app', 'Final Price'),
            'netPrice' => Yii::t('app', 'Net Price'),
            'initial_stock' => Yii::t('app', 'Initial stock'),
            'initial_secondary_stock' => Yii::t('app', 'Initial secondary stock'),
            'taxes' => Yii::t('app','Taxes'),
            'stock' => Yii::t('app', 'Stock'),
            'secondary_balance' => Yii::t('app', 'Secondary Balance'),
            'secondary_unit_id' => Yii::t('app', 'Secondary Unit'),
            'secondaryUnit' => Yii::t('app', 'Secondary Unit'),
            'categories' => Yii::t('app', 'Categories'),
            'product_commission_id' => Yii::t('app', 'Commission')
        ];
        if (Yii::$app->getModule('accounting')) {
            $labels['account_id'] = Yii::t('accounting', 'Account');
        }

        return $labels;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBillDetails()
    {
        return $this->hasMany(BillDetail::class, ['product_id' => 'product_id']);
    }

    /*** Relations ***/
    
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
    public function getSecondaryUnit()
    {
        
        if(!Config::getValue('enable_secondary_stock')){
            return null;
        }
        return $this->hasOne(Unit::class, ['unit_id' => 'secondary_unit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductDiscounts()
    {
        return $this->hasMany(ProductDiscount::class, ['product_id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAllProductDiscounts()
    {
        return $this->hasMany(ProductDiscount::class, ['product_id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductHasCategory()
    {
        return $this->hasOne(ProductHasCategory::class, ['product_id' => 'product_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFundingPlan()
    {
        return $this->hasMany(FundingPlan::class, ['product_id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::class, ['category_id' => 'category_id'])->viaTable('product_has_category', ['product_id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::class, ['account_id' => 'account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommission()
    {
        return $this->hasOne(ProductCommission::class, ['product_commission_id' => 'product_commission_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiscounts()
    {
        return $this->hasMany(Discount::class, ['product_id' => 'product_id']);
    }

    /**
     * Agrega categorias al producto. Al hacer esto, se genera un evento para
     * que la relacion sea guardada luego de guardar el objeto.
     * TODO: link parents
     * @param type $categories
     */
    public function setCategories($categories){
        if(empty($categories)){
            $categories = [];
        }
        
        $this->_categories = $categories;

        $saveCategories = function($event){
            //Quitamos las relaciones actuales
            $this->unlinkAll('categories', true);
            //Guardamos las nuevas relaciones
            foreach ($this->_categories as $id){
                $this->link('categories', Category::findOne($id));
            }
        };

        $this->on(self::EVENT_AFTER_INSERT, $saveCategories);
        $this->on(self::EVENT_AFTER_UPDATE, $saveCategories);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStockMovements()
    {
        return $this->hasMany(StockMovement::class, ['product_id' => 'product_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductPrices()
    {
        return $this->hasMany(ProductPrice::class, ['product_id' => 'product_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActivePrice($contractDetail = null)
    {
        return $this->hasOne(ProductPrice::class, ['product_id' => 'product_id'])
            ->where('exp_timestamp <= :now AND ( exp_timestamp = -1 OR exp_timestamp < :now )',['now'=>time()])
            ->orderBy(['product_price_id' => SORT_DESC]);
    }

    /*** End Relations ***/
    
    /**
     * Devuelve el precio final actual del producto
     * @return float
     */
    public function getFinalPrice($contractDetail = null)
    {
        
        $price = $this->getActivePrice($contractDetail)->one();
        if(!empty($price))
            return $price->finalPrice;
        
        return null;
        
    }
    
    /**
     * Devuelve el precio neto actual del producto
     * @return float
     */
    public function getNetPrice($contractDetail = null)
    {
        
        $price = $this->getActivePrice($contractDetail)->one();
        if(!empty($price))
            return $price->net_price;
        
        return null;
        
    }

    /**
     * Devuelve el precio final que tendrá a futuro
     * @return float
     */
    public function getFutureFinalPrice($contractDetail = null)
    {
        $price = $this->getActivePrice($contractDetail)->one();

        if(!empty($price))
            return $price->future_final_price;

        return null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPriceFromDate($date)
    {
        
        $query = $this->hasOne(ProductPrice::class, ['product_id' => 'product_id'])
            ->where('timestamp <= :date_timestamp',['date_timestamp'=>strtotime($date)])
            ->orderBy(['timestamp' => SORT_DESC]);
        
        if($query->exists()){
            return $query;
        }

        //Si no encuentra precio para esa fecha
        return $this->getActivePrice();
    }
    
    /**
     * Devuelve el valor de los impuestos
     * @return float
     */
    public function getTaxes($contractDetail = null)
    {
        
        $price = $this->getActivePrice($contractDetail)->one();
        if(!empty($price))
            return $price->taxes;
        
        return null;
        
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxRates()
    {
        return $this->hasMany(TaxRate::class, ['tax_rate_id' => 'tax_rate_id'])->viaTable('product_has_tax_rate', ['product_id' => 'product_id']);
    }
    
    public function setTaxRates($rates)
    {
        
        $this->_rates = empty($rates) ? [] : $rates;
        
        $set = function(){
            $this->unlinkAll('taxRates', true);
            foreach($this->_rates as $rate){
                $taxRate = TaxRate::findOne($rate);
                if($taxRate){
                    $this->link('taxRates', $taxRate);
                }
            }
        };
        
        $this->on(self::EVENT_AFTER_INSERT, $set);
        $this->on(self::EVENT_AFTER_UPDATE, $set);
        
    }
    
    /**
     * Devuelve el valor de cada impuesto de acuerdo al producto para el precio neto actual
     * @param float $net
     * @return array
     */
    public function getAllTaxes($contractDetail = null)
    {
        $netPrice = $this->getNetPrice($contractDetail)->one();
        if($netPrice){
            return $this->calculateAllTaxes($netPrice);
        }else{
            return 0.0;
        }
        
    }
    
    /**
     * Devuelve el importe del impuesto con $slug del producto de acuerdo al precio neto actual
     * @param string $slug
     * @return float
     */
    public function getTaxAmount($slug, $contractDetail = null)
    {
        $netPrice = $this->getNetPrice($contractDetail)->one();
        return $this->calculateTaxAmount($slug, $netPrice);
    }
    
    /**
     * Devuelve el importe del impuesto con $slug del producto de acuerdo al precio neto actual
     * @param string $slug
     * @return float
     */
    public function calculateTaxAmount($slug, $net)
    {
        $rate = $this->getTaxRates()->joinWith('tax')->where(['tax.slug'=>$slug])->one();
        
        //0 o error?
        if(!$rate){
            return 0;
        }
        
        return $rate->calculate($net);
    }
    
    /**
     * Calcula el valor de los impuestos de acuerdo al producto para el valor $net
     * @param float $net
     * @return float
     */
    public function calculateTaxes($net)
    {
        
        $taxes = 0.0;
        
        $taxRates = $this->taxRates;
        foreach($taxRates as $rate){
            $taxes += $rate->calculate($net);
        }
        
        return $taxes;
        
    }
    
    /**
     * Calcula el porcentaje total de los impuestos
     * @param float $net
     * @return float
     */
    public function calculateTaxRates()
    {
        
        $rate = 0.0;
        
        $taxRates = $this->taxRates;
        foreach($taxRates as $r){
            $rate += $r->pct;
        }
        
        return $rate;
        
    }
    
    /**
     * Devuelve el valor de cada impuesto de acuerdo al producto para el valor $net
     * @param float $net
     * @return array
     */
    public function calculateAllTaxes($net)
    {
        
        $taxes = [];
        
        foreach($this->taxRates as $rate){
            $tax = $rate->tax;
            $taxes[] = [
                'tax' => $tax->slug,
                'code' => $rate->code,
                'value' => $rate->calculate($net)
            ];
        }
        
        return $taxes;
        
    }
    
    /**
     * Calcula el precio neto en funcion de un precio final dado
     * @param float $final
     * @return float
     */
    public function calculateNetPrice($final)
    {
        
        $rates = 1+$this->calculateTaxRates();
        if($rates > 0){
            return $final / $rates;
        }else{
            return $final;
        }
        
    }


    /**
     * Antes de guardar verificamos si el codigo del producto debe ser generado. 
     * En caso de que el valor de code sea AUTO, el codigo se genera utilizando
     * la funcion uniqid().
     * @param type $insert
     * @return boolean
     */
    public function beforeSave($insert) 
    {
        if (parent::beforeSave($insert)) {
            
            if($this->code == 'AUTO'){
                $this->code = uniqid();
            }
            
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Genera un codigo de barras en formato jph
     * @return type
     */
    public function getBarcode()
    {
        $barcode = new Barcode();
        $barcode->setGenbarcodeLocation(Yii::$app->params['genbarcode_location']);
        $barcode->setMode(Barcode::MODE_PNG);

        $headers = array(
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'inline; filename="'.$this->code.'.png"'
        );

       return $barcode->outputImage($this->code);
    }
    
    /**
     * 
     * @param float $net_price
     * @param float $taxes
     * @param date $expiration yyyy/mm/dd
     */
    public function setPrice($net_price, $expiration)
    {
        
        $price = new ProductPrice;
        $price->net_price = $net_price;
        
        /**
         * El precio almacena el resultado de aplicar todos los impuestos asociados.
         * Si es necesario el detalle, se debe utilizar el metodo getAllTaxes() 
         * de ProductPrice, o alternativamente el attr all
         */
        $price->taxes = $this->calculateTaxes($net_price);
        
        $price->exp_date = $expiration;
        $price->exp_timestamp = strtotime($expiration);
        
        //Relacionamos
        $this->link('productPrices',$price);

        return $price;
        
    }
    
    /**
     * 
     * @param float $net_price
     * @param float $taxes
     * @param date $expiration yyyy/mm/dd
     */
    public function setFinalPrice($final, $expiration)
    {
        
        $price = new ProductPrice;
        $price->net_price = $this->calculateNetPrice($final);
        
        /**
         * El precio almacena el resultado de aplicar todos los impuestos asociados.
         * Si es necesario el detalle, se debe utilizar el metodo getAllTaxes() 
         * de ProductPrice, o alternativamente el attr all
         */
        $price->taxes = $this->calculateTaxes($price->net_price);
        
        $price->exp_date = $expiration;
        $price->exp_timestamp = strtotime($expiration);
        
        //Relacionamos
        $this->link('productPrices',$price);
        
    }
    
    //TODO: strategy
    public static function batchUpdate($percentage, $type, $filter = 'all', $exp_date, $items = null, $category = null)
    {
        
        switch ($filter){
            
            case 'all':
                $products = Product::find()->where(['status'=>"enabled", 'type'=> $type])->all();
            break;
            
            case 'selected':
                if(empty($items)) return;
                $products = Product::find()->where(['in','product_id',explode(',',$items)])->all();
            break;

            case 'category':
                $products = Product::find()->joinWith(['categories'])->where(['in','category.category_id',$category])->all();
            break;
            
        }
        
        $count = count($products);
        
        foreach($products as $product)
            $product->updatePrice($percentage, $exp_date);
        
        return $count;
        
    }
    
    /**
     * Actualiza un precio de acuerdo al porcentaje recibido.
     * @param float $percentage
     * @param string $expiration
     * @return boolean
     */
    public function updatePrice($percentage, $expiration)
    {
        
        $price = $this->netPrice;
        
        if($price == null) return false;
        
        $percentage = 1 + ($percentage/100);
        
        $this->setPrice($price * $percentage, $expiration);
        
        return true;
        
    }
    
    public function beforeDelete() 
    {
        if(parent::beforeDelete()){
            
            $this->unlinkAll('productPrices', true);
            
            $this->unlinkAll('stockMovements', true);
            
            $this->unlinkAll('categories', true);
            
            $this->unlinkAll('taxRates', true);
            
            return true;
        }else{
            return false;
        }
    }
    
    public function afterSave($insert, $changedAttributes) 
    {
        
        parent::afterSave($insert, $changedAttributes);
        
        /**
         * Si es un nuevo producto y se definio stock inicial, inicializamos el stock
         */
        if($insert){
            $this->initStock( $this->initial_stock, $this->initial_secondary_stock );
        }
        
        //Solo un plan por defecto para vendedores a la vez
        $defaultSellerPlanCategory = Category::find()->where(['system' => 'default-seller-plan'])->one();
        if($defaultSellerPlanCategory && $this->getCategories()->where(['category_id' => $defaultSellerPlanCategory->category_id])->exists()){
            ProductHasCategory::deleteAll("category_id=$defaultSellerPlanCategory->category_id AND product_id<>$this->product_id");
        }
        
    }
    
    /**
     * Inicializa el stock de un producto, creando un movimiento de stock de entrada
     * inicial por la cantidad indicado por $initialStock. Si el stock inicial es 0,
     * no genera movimiento de entrada.
     * @param int $initialStock
     */
    private function initStock($initialStock, $initialSecondaryStock = 0)
    {
        
        if($initialStock == null || $initialStock == 0){
            
            $this->balance = 0;
            
        }else{
        
            $this->balance = $initialStock;
            if($this->updateAttributes(['balance'])){

                $mov = new StockMovement();
                $mov->scenario = 'initial';

                $mov->concept = Yii::t('app', 'Initial stock');
                $mov->qty = $initialStock;
                $mov->secondary_qty = $initialSecondaryStock;
                $mov->type = 'in';

                $this->link('stockMovements',$mov);

            }
        }
        
    }
    
    /**
     * Genera un color para utilizar en los graficos
     */
    public function afterFind() 
    {
        parent::afterFind();
        
        $hash = md5('color' . $this->product_id);
        $this->rgb = 
            hexdec(substr($hash, 0, 2)).','.    // r
            hexdec(substr($hash, 2, 2)).','.    // g
            hexdec(substr($hash, 4, 2))         // b
        ;
        
    }
    
    /**
     * TODO: Barcode interpreter
     * @param string $word
     * @return null
     */
    protected function parseVariableCode($word)
    {
        
        $word_prefix = mb_substr($word, 0, 2);
        
        $word = mb_substr($word, 2);

        $var_prefixes = Yii::$app->params['barcode_variable']['prefix'];
        $code_length = Yii::$app->params['barcode_variable']['code_length'];
        $variable_length = Yii::$app->params['barcode_variable']['variable_length'];
        
        if(in_array($word_prefix, $var_prefixes)){
            
            if(mb_strlen($word) >= $code_length + $variable_length){
                
                return mb_substr($word, 0, $code_length);
                
            }
            
        }
        
        return null;
        
    }
    
    /**
     * TODO: Barcode interpreter
     * @param string $code
     * @return boolean
     */
    public function compareCode($code)
    {
        
        if($this->code == $code || $this->parseVariableCode($code) == $this->code){
            return true;
        }else {
            return false;
        }
        
    }
    
    /**
     * Establece los atributos que deben ser utilizados al momento de convertir
     * el objeto a array
     * @return type
     */
    public function fields() 
    {
        Yii::$app->formatter->decimalSeparator = '.';
        Yii::$app->formatter->thousandSeparator = '';
        
        return [
            'uid',
            'stock'=>function($model, $field){
                return $model->getStock($this->stockCompany);
            },
            'secondary_stock'=>function($model, $field){
                return $model->getSecondaryStock($this->stockCompany);
            },
            'combined_stock'=>function($model){
                $secondaryStock = $model->getSecondaryStock($this->stockCompany, true);
                if($secondaryStock){
                    return $model->getStock($this->stockCompany, true).' | '.$secondaryStock;
                }else{
                    return $model->getStock($this->stockCompany, true);
                }
            },
                    
            'avaible_stock'=>function($model, $field){
                return $model->getAvaibleStock($this->stockCompany);
            },
            'avaible_secondary_stock'=>function($model, $field){
                return $model->getSecondaryAvaibleStock($this->stockCompany);
            },
            'avaible_combined_stock'=>function($model){
                $secondaryStock = $model->getSecondaryAvaibleStock($this->stockCompany, true);
                if($secondaryStock){
                    return $model->getAvaibleStock($this->stockCompany, true).' | '.$secondaryStock;
                }else{
                    return $model->getAvaibleStock($this->stockCompany, true);
                }
            },
                    
            'inStock'=>function($model, $field){
                return $model->getInStock($this->stockCompany);
            },
            'code',
            'create_timestamp',
            'description',
            'name',
            'product_id',
            'status',
            'system',
            'taxRates',
            'unit_id',
            'unit',
            'netPrice'=>function($model, $field){
                return $model->netPrice ? Yii::$app->formatter->asDecimal($model->netPrice, 2) : '';
            },
            'finalPrice'=>function($model, $field){
                return $model->finalPrice ? Yii::$app->formatter->asDecimal($model->finalPrice, 2) : '';
            },
            'priceDate'=>function($model){
                return $model->activePrice ? Yii::$app->formatter->asDate($model->activePrice->date) : null;
            },
            'categories',
            'media',
            'poster' => function($model){
                return $model->getMedia()->one();
            },
        ];
    }
    
    /**
     * Atributo virtual inStock, que permite determinar si el producto actual 
     * se encuentra en stock.
     * @return type
     */
    public function getInStock($company = null)
    {
        
        return $this->getStock($company) > 0 || !\app\modules\config\models\Config::getValue('strict_stock');
        
    }
    
    /**
     * Permite determina si el producto puede ser eliminado. Ver beforeDelete()
     * @return boolean
     */
    public function getDeletable(){
        
        if($this->getBillDetails()->exists()){
            return false;
        }
        
        if($this->getStockMovements()->exists()){
            return false;
        }
        
        return true;
        
    }
    
    /**
     * Es stockable??
     * @return boolean
     */
    public function getStockable(){
        return true;
    }
    
    /**
     * Stock por empresa (existencias; no tiene en cuenta reservas)
     * @param type $company
     * @return float
     * @throws \yii\web\HttpException
     */
    public function getStock($company = null, $symbol = false)
    {
        
        $value = Yii::$app->getModule('sale')->stock->getStock($this, $company);
        
        if($symbol == false){
            return $value;
        }
        
        if($this->unit->symbol_position == 'prefix'){
            return $this->unit->symbol . " $value";
        }else{
            return "$value". $this->unit->symbol;
        }
        
    }
    
    /**
     * Stock por empresa (existencias; no tiene en cuenta reservas)
     * @param type $company
     * @return float
     * @throws \yii\web\HttpException
     */
    public function getSecondaryStock($company = null, $symbol = false)
    {
        if(!$this->secondaryUnit){
            return null;
        }
        
        $value = Yii::$app->getModule('sale')->stock->getStock($this, $company, true);
        
        if($symbol == false){
            return $value;
        }
        
        if($this->secondaryUnit->symbol_position == 'prefix'){
            return $this->secondaryUnit->symbol . " $value";
        }else{
            return $value . $this->secondaryUnit->symbol;
        }
        
    }
    
    /**
     * Stock disponible (existencias - reservado), por empresa
     * @param type $company
     * @return float
     * @throws \yii\web\HttpException
     */
    public function getAvaibleStock($company = null, $symbol = false)
    {
        
        $value = Yii::$app->getModule('sale')->stock->getAvaibleStock($this, $company);
        
        if($symbol == false){
            return $value;
        }
        
        if($this->unit->symbol_position == 'prefix'){
            return $this->unit->symbol . " $value";
        }else{
            return "$value". $this->unit->symbol;
        }
        
    }
    
    /**
     * Stock disponible (existencias - reservado), por empresa
     * @param type $company
     * @return float
     * @throws \yii\web\HttpException
     */
    public function getSecondaryAvaibleStock($company = null, $symbol = false)
    {
        
        if(!$this->secondaryUnit){
            return null;
        }
        
        $value = Yii::$app->getModule('sale')->stock->getAvaibleStock($this, $company, true);
        
        if($symbol == false){
            return $value;
        }
        
        if($this->secondaryUnit->symbol_position == 'prefix'){
            return $this->secondaryUnit->symbol . " $value";
        }else{
            return $value . $this->secondaryUnit->symbol;
        }
        
    }
    
    /**
     * Stock reservado, por empresa
     * @param type $company
     * @return float
     * @throws \yii\web\HttpException
     */
    public function getReservedStock($company = null, $symbol = false)
    {

        if($company === null){
            $company = Yii::$app->get('company');
        }

        if(!$company){
            throw new \yii\web\HttpException(500, 'Company not defined.');
        }
        
        $value = Yii::$app->getModule('sale')->stock->getReservedStock($this, $company);
        
        if($symbol == false){
            return $value;
        }
        
        if($this->unit->symbol_position == 'prefix'){
            return $this->unit->symbol . " $value";
        }else{
            return "$value". $this->unit->symbol;
        }
        
    }
    
    /**
     * Stock reservado, por empresa
     * @param type $company
     * @return float
     * @throws \yii\web\HttpException
     */
    public function getSecondaryReservedStock($company = null, $symbol = false)
    {
        
        if($company === null){
            $company = Yii::$app->get('company');
        }
        
        if(!$company){
            throw new \yii\web\HttpException(500, 'Company not defined.');
        }
        
        $value = Yii::$app->getModule('sale')->stock->getReservedStock($this, $company, true);
        
        if($symbol == false){
            return $value;
        }
        
        if($this->secondaryUnit){
            if($this->secondaryUnit->symbol_position == 'prefix'){
                return $this->secondaryUnit->symbol . " $value";
            }else{
                return $value . $this->secondaryUnit->symbol;
            }
        }
        
    }
    
    /**
     * Devuelve true si el producto posee la categoria con sistema $system
     * @param type $system
     */
    public function hasCategory($system){
        $categories= $this->categories;
        
        foreach ($categories as $cat) {
            if ($cat->system === $system) {
                return true;
            }
        }
        
        return false;
    }

    public static function findAllPlans(){
        return self::find()->where(['type'=>'plan'])->all();
    }

}
