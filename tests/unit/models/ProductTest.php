<?php
/**
 * Created by PhpStorm.
 * User: dexterlab10
 * Date: 03/07/19
 * Time: 16:24
 */

use app\modules\sale\models\Product;
use app\tests\fixtures\UnitFixture;

class ProductTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _fixtures()
    {
        return [
            'unit' => [
                'class' => UnitFixture::class
            ]
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new Product();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new Product([
            'unit_id' => 1,
            'name' => 'Producto1',
            'status' => Product::STATUS_ENABLED
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new Product();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new Product([
            'unit_id' => 1,
            'name' => 'Producto1',
            'status' => Product::STATUS_ENABLED
        ]);

        expect('Saved when full and new', $model->save())->true();
    }

    //TODO
    /*
      public function getFinalPrice($contractDetail = null)
    {

        $price = $this->getActivePrice($contractDetail)->one();
        if(!empty($price))
            return $price->finalPrice;

        return null;

    }


    public function getNetPrice($contractDetail = null)
    {

        $price = $this->getActivePrice($contractDetail)->one();
        if(!empty($price))
            return $price->net_price;

        return null;

    }

    public function getFutureFinalPrice($contractDetail = null)
    {
        $price = $this->getActivePrice($contractDetail)->one();

        if(!empty($price))
            return $price->future_final_price;

        return null;
    }

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


    public function getTaxes($contractDetail = null)
    {

        $price = $this->getActivePrice($contractDetail)->one();
        if(!empty($price))
            return $price->taxes;

        return null;

    }

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


    public function getAllTaxes($contractDetail = null)
    {
        $netPrice = $this->getNetPrice($contractDetail)->one();
        if($netPrice){
            return $this->calculateAllTaxes($netPrice);
        }else{
            return 0.0;
        }

    }


    public function getTaxAmount($slug, $contractDetail = null)
    {
        $netPrice = $this->getNetPrice($contractDetail)->one();
        return $this->calculateTaxAmount($slug, $netPrice);
    }


    public function calculateTaxAmount($slug, $net)
    {
        $rate = $this->getTaxRates()->joinWith('tax')->where(['tax.slug'=>$slug])->one();

        //0 o error?
        if(!$rate){
            return 0;
        }

        return $rate->calculate($net);
    }

    public function calculateTaxes($net)
    {

        $taxes = 0.0;

        $taxRates = $this->taxRates;
        foreach($taxRates as $rate){
            $taxes += $rate->calculate($net);
        }

        return $taxes;

    }


    public function calculateTaxRates()
    {

        $rate = 0.0;

        $taxRates = $this->taxRates;
        foreach($taxRates as $r){
            $rate += $r->pct;
        }

        return $rate;

    }


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

    public function calculateNetPrice($final)
    {

        $rates = 1+$this->calculateTaxRates();
        if($rates > 0){
            return $final / $rates;
        }else{
            return $final;
        }

    }

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


    public function setPrice($net_price, $expiration)
    {

        $price = new ProductPrice;
        $price->net_price = $net_price;


        $price->taxes = $this->calculateTaxes($net_price);

        $price->exp_date = $expiration;
        $price->exp_timestamp = strtotime($expiration);

        //Relacionamos
        $this->link('productPrices',$price);

        return $price;

    }

    public function setFinalPrice($final, $expiration)
    {

        $price = new ProductPrice;
        $price->net_price = $this->calculateNetPrice($final);


        $price->taxes = $this->calculateTaxes($price->net_price);

        $price->exp_date = $expiration;
        $price->exp_timestamp = strtotime($expiration);

        //Relacionamos
        $this->link('productPrices',$price);

    }

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


        if($insert){
            $this->initStock( $this->initial_stock, $this->initial_secondary_stock );
        }

        //Solo un plan por defecto para vendedores a la vez
        $defaultSellerPlanCategory = Category::find()->where(['system' => 'default-seller-plan'])->one();
        if($defaultSellerPlanCategory && $this->getCategories()->where(['category_id' => $defaultSellerPlanCategory->category_id])->exists()){
            ProductHasCategory::deleteAll("category_id=$defaultSellerPlanCategory->category_id AND product_id<>$this->product_id");
        }

    }

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

    public function compareCode($code)
    {

        if($this->code == $code || $this->parseVariableCode($code) == $this->code){
            return true;
        }else {
            return false;
        }

    }


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

    public function getInStock($company = null)
    {

        return $this->getStock($company) > 0 || !\app\modules\config\models\Config::getValue('strict_stock');

    }


    public function getDeletable(){

        if($this->getBillDetails()->exists()){
            return false;
        }

        if($this->getStockMovements()->exists()){
            return false;
        }

        return true;

    }


    public function getStockable(){
        return true;
    }


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

    public function hasCategory($system){
        $categories= $this->categories;

        foreach ($categories as $cat) {
            if ($cat->system === $system) {
                return true;
            }
        }

        return false;
    }

}

*/

}