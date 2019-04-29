<?php

namespace app\modules\sale\modules\contract\models;
use app\modules\sale\models\Product;
use app\modules\sale\models\ProductPrice;
use Yii;
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
 * @property double $balance
 * @property integer $create_timestamp
 * @property integer $update_timestamp
 * @property integer $unit_id
 * @property string $class
 * @property string $productcol
 * @property integer $show_in_ads
 * @property string $ads_name;
 *
 * @property BillDetail[] $billDetails
 * @property Unit $unit
 * @property ProductDiscount[] $productDiscounts
 * @property ProductHasCategory $productHasCategory
 * @property Category[] $categories
 * @property ProductPrice[] $productPrices
 * @property StockMovement[] $stockMovements
 */
class Plan extends Product
{


    const TYPE = 'plan';
    public $_planfeature = [];

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
            [['description', 'ads_name'], 'string'],
            [['unit_id', 'show_in_ads'], 'integer'],
            [['taxes'], 'number'],
            [['taxRates'], 'safe'],
            [['initial_stock'], 'number'],
            [['status'], 'in', 'range'=>['enabled','disabled']],
            [['name'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['code', 'system'], 'string', 'max' => 45],
            [['ads_name'], 'string', 'max' => 15],
            [['code'], 'unique'],
            [['categories', '_planfeature', 'company_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_id' => Yii::t('app', 'Plan'),
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
            'finalPrice' => Yii::t('app', 'Final Price'),
            'netPrice' => Yii::t('app', 'Net Price'),
            'initial_stock' => Yii::t('app', 'Initial stock'),
            'taxes' => Yii::t('app','Taxes'),
            'stock' => Yii::t('app', 'Stock'),
            'categories' => Yii::t('app', 'Categories'),
            'class' => Yii::t('app', 'Class'),
            'ads_name' => Yii::t('app', 'Ads Name'),
            'show_in_ads' => Yii::t('app', 'Show In Ads'),
            'futureFinalPrice' => Yii::t('app', 'Future final price'),
        ];
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
            'media' => [
                'class' => \app\modules\media\behaviors\MediaBehavior::className(),
            ]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
//    public function getBillDetails()
//    {
//        return $this->hasMany(BillDetail::className(), ['product_id' => 'product_id']);
//    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlanPrices()
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
    public function getPlan_id(){
        return $this->product_id;
    }
    public function setPlan_id($id){
        $this->product_id = $id;
    }

    /**
     * Es stockable??
     * @return boolean
     */
    public function getStockable(){
        return false;
    }
    
    public function getPlanFeatures()
    {
        return $this->hasMany(PlanFeature::className(), ['plan_feature_id' => 'plan_feature_id'])->viaTable('product_has_plan_feature', ['product_id' => 'product_id']);
    } 
    
    public function setPlanFeatures(){
        $features_id=$this->_planfeature;
        $this->unLinkAll('planFeatures', true);
        foreach ($features_id as $id_feature){
            if(is_array($id_feature)){
                foreach ($id_feature as $id){
                    if(!empty($id))
                        $this->link('planFeatures', PlanFeature::findOne($id));
                }
            }
            else{
                if(!empty($id_feature))
                 $this->link('planFeatures', PlanFeature::findOne($id_feature));
            }
        }
    }

    public function getNamePrice(){
        return $this->name.' - $'. $this->finalPrice;
    }
            


    public function findFeatures() {
        $planFeatures = $this->planFeatures;
        $planFeatures_helper= [];
        foreach ($planFeatures as $planFeature){
            $parent_id=$planFeature->parent_id;
            if($parent_id!== ''){
                if(PlanFeature::findOne($parent_id)->type === 'radiobutton'){
                    $planFeatures_helper[$parent_id]=$planFeature->plan_feature_id;
                }
                else{
                    $planFeatures_helper[$parent_id][]=$planFeature->plan_feature_id;
                }
            }
        $this->_planfeature=$planFeatures_helper;
    } 
    }
    
    public function afterFind() {
       $this->findFeatures();
       parent::afterFind();
     }

    public function afterSave($insert, $changedAttributes) {

        $this->setPlanFeatures();
        parent::afterSave($insert, $changedAttributes);
    }
    
    public function beforeDelete() {
        $this->unLinkAll('planFeatures', true);
        parent::beforeDelete();
        return TRUE;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActivePrice($contractDetail = null)
    {
        if($contractDetail === null){
            return $this->hasOne(ProductPrice::class, ['product_id' => 'product_id'])
                ->where('exp_timestamp <= :now OR exp_timestamp = -1',['now'=>time()])
                ->orderBy(['product_price_id' => SORT_DESC]);
        }
        
        //Meses sin aplicar incremento
        $months = Config::getValue('months-without-increase');

        //Si el detalle de contrato tiene menos de $months meses
        if((new \DateTime($contractDetail->date ))->format('Ymd') > (new \DateTime('now - '.$months . ' months'))->format('Ymd')){

            $query = $this->hasOne(ProductPrice::class, ['product_id' => 'product_id'])
                ->where('timestamp >= :detailTimestamp',
                    ['detailTimestamp'=>strtotime($contractDetail->date)]);

            if ( $query->exists() ) {
                return $this->hasOne(ProductPrice::class, ['product_id' => 'product_id'])
                    ->where('timestamp >= :detailTimestamp and ( exp_timestamp = -1 OR exp_timestamp < :now )',
                        ['detailTimestamp'=>strtotime($contractDetail->date), 'now'=>strtotime((new \DateTime('now'))->format('Y-m-d'))])
                    ->orderBy(['product_price_id' => SORT_DESC]);
            } else {
                //Traemos el precio vigente en la fecha del detalle
                return $this->hasOne(ProductPrice::class, ['product_id' => 'product_id'])
                    ->where('timestamp <= :detailTimestamp', ['detailTimestamp'=>strtotime($contractDetail->date)])
                    ->orderBy(['product_price_id' => SORT_DESC]);
                    ;

            }
        }

        //Si la ultima actualizacion ocurrio hace menos de $months meses, buscamos la del mes anterior.
        $query = $this->hasOne(ProductPrice::class, ['product_id' => 'product_id'])
                ->where('exp_timestamp <= :now AND (exp_timestamp = -1 OR exp_timestamp < :now )',['now'=>time()])
            ->orderBy(['product_price_id' => SORT_DESC]);

        return $query;
    }
    
}