<?php

/**
 * Description of CSVProductImporter
 *
 * @author martin
 */
class CSVProductImporter {
    
    /**
     * Importa productos y sus precios
     * @param type $file
     * @return int
     */
    public function import($file)
    {
        //Establecemos el tiempo limite de acuerdo al parametro import_time_limit
        set_time_limit(Yii::$app->params['import_time_limit']);
        
        //Contamos cuantos productos se importan
        $count = 0;
        if (($f = fopen($file->tempName, "r")) !== FALSE) {
            
            //Cada linea es un producto
            while (($line = fgetcsv($f)) !== FALSE) {

                //Construimos un DTO del producto con la linea
                $productLine = $this->getProductLine($line);
                
                if($productLine !== false){

                    //Guardamos el producto
                    $imported = $this->saveProduct($productLine);
                    
                    if($imported){
                        $count++;
                    }
                    
                }
                
            }
            fclose($f);
        }
        return $count;
    }
    
    /**
     * Devuelve un DTO ProductLine de acuerdo a los datos recibidos.
     * Los datos del DTO son verificados por la funcion ProductLine->verify().
     * En caso de error, la funcion devuelve false
     * @param array $line
     * @return \ProductLine|boolean
     */
    public function getProductLine(Array $line){
        
        $product = new ProductLine;
        
        $product->uid=$line[0];
        $product->barcode=$line[1];
        $product->name=$line[2];
        $product->date=$line[3];
        $product->purchase_price=$line[4];
        $product->purchase_final_price=$line[5];
        $product->final_price=$line[6];
        
        if($product->verify()){
            return $product;
        }

        return false;
        
    }
    
    /**
     * Registra o actualiza un producto y su precio.
     * @param ProductLine $productLine
     * @return boolean
     * @throws \yii\web\HttpException
     */
    private function saveProduct(ProductLine $productLine)
    {
        
        $price = new app\modules\sale\models\ProductPrice;
        
        //Vemos si el producto existe, de acuerdo al uid
        $product = \app\modules\sale\models\Product::findOne(['uid'=>$productLine->uid]);
        
        //Si el producto no existe, creamos uno nuevo
        if(empty($product)){
            $product = new \app\modules\sale\models\Product;
            $product->code = $productLine->getCode();
            $product->uid = $productLine->uid;
        }
        
        $product->name = $productLine->name;
        $product->taxRates = [$productLine->taxes->tax_rate_id];
        $product->unit_id = Yii::$app->params['default_unit_id'];
        
        if($product->save()){
        
            $product->setPrice($productLine->net_price, null);
            return true;
            
        }else{
            if(YII_DEBUG){
                throw new \yii\web\HttpException(500, var_export($product->getErrors(),true));
            }
        }
        
        return false;
        
    }
    
}

/**
 * DTO de producto
 */
class ProductLine{
    
    public $uid;
    public $barcode;
    public $name;
    public $date;
    public $purchase_price;
    public $purchase_final_price;
    public $net_price;
    public $final_price;
    
    public $taxes;
    
    /**
     * Verifica que todos los datos sean correctos, formatea los precios y calcula
     * los impuestos y el precio bruto de venta
     * @return boolean
     */
    public function verify()
    {

        if(empty($this->uid) || empty($this->barcode) || empty($this->name) || 
            empty($this->purchase_price) || empty($this->purchase_final_price) || empty($this->final_price)){
            return false;
        }
        
        //Parseamos los precios
        $this->purchase_price = (float) str_replace('$','',$this->purchase_price);
        $this->purchase_final_price = (float) str_replace('$','',$this->purchase_final_price);
        $this->final_price = (float) str_replace('$','',$this->final_price);

        //Si los precios estan definidos, calculamos impuestos y precio de venta sin impuestos
        if($this->purchase_price * $this->purchase_final_price * $this->final_price > 0){
            $this->taxes = $this->getTaxes();
            
            if($this->taxes == false){
                return false;
            }
            
            $this->net_price = round($this->final_price / ($this->taxes->pct + 1),2);
            return true;
        }
        
        return false;
    }
    
    /**
     * Calcula los impuestos del producto
     * @return float|boolean
     */
    public function getTaxes(){

        $taxesAmount = $this->purchase_final_price - $this->purchase_price;
        $taxes = round($taxesAmount / $this->purchase_price, 3);
        
        $taxRate = \app\modules\sale\models\TaxRate::find()->joinWith('tax')->where([
            'tax.slug' => 'iva',
        ])->one();

        if(!$taxRate){
            return false;
        }
        
        return $taxRate;
        
    }
    
    /**
     * Devuelve el codigo de barras en caso de que sea valido, o 'AUTO' para
     * generar un nuevo codigo de barras.
     * @return string
     */
    public function getCode(){
        
        if(strlen($this->barcode) > 5){
            return $this->barcode;
        } else {
            return 'AUTO';
        }
        
    }
    
}