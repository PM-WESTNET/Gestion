<?php

namespace app\modules\westnet\models;

use Yii;

/**
 * This is the model class for table "vendor_liquidation".
 *
 * @property integer $vendor_liquidation_id
 * @property integer $vendor_id
 * @property string $date
 * @property string $period
 * @property string $status
 *
 * @property Vendor $vendor
 * @property VendorLiquidationItem[] $vendorLiquidationItems
 */
class VendorLiquidation extends \app\components\db\ActiveRecord
{

    const VENDOR_LIQUIDATION_DRAFT = 'draft';
    const VENDOR_LIQUIDATION_PAYED = 'payed';
    const VENDOR_LIQUIDATION_CANCELLED = 'cancelled';
    const VENDOR_LIQUIDATION_BILLED = 'billed';

    public function init()
    {
        parent::init();
        
        $this->status = 'draft';
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vendor_liquidation';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'date' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date'],
                ],
                'value' => function(){return date('Y-m-d');},
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vendor_id', 'period'], 'required'],
            [['vendor_id'], 'integer'],
            [['date', 'period'], 'safe'],
            [['date', 'period'], 'date'],
            [['status'], 'in', 'range' => ['draft','payed','cancelled', 'billed']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'vendor_liquidation_id' => Yii::t('westnet', 'Vendor Liquidation ID'),
            'vendor_id' => Yii::t('westnet', 'Vendor'),
            'date' => Yii::t('app', 'Date'),
            'period' => Yii::t('app', 'Period'),
            'status' => Yii::t('app', 'Status'),
            'periodMonth' => Yii::t('app', 'Period'),
            'total' => Yii::t('app', 'Total'),
            'addTotal' => Yii::t('app', 'Additional Total'),
            'discountTotal' => Yii::t('app', 'Discount Total'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVendor()
    {
        return $this->hasOne(Vendor::className(), ['vendor_id' => 'vendor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVendorLiquidationItems()
    {
        return $this->hasMany(VendorLiquidationItem::className(), ['vendor_liquidation_id' => 'vendor_liquidation_id'])
            ->where('amount<>0.0');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAllVendorLiquidationItems()
    {
        return $this->hasMany(VendorLiquidationItem::className(), ['vendor_liquidation_id' => 'vendor_liquidation_id']);
    }
    
    /**
     * @inheritdoc
     */
     
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {   
            
            if($insert){
                $year = Yii::$app->formatter->asDate($this->period, 'yyyy');
                $month = Yii::$app->formatter->asDate($this->period, 'MM');

                if(self::find()->where('MONTH(period)="'.$month.'" AND YEAR(period)="'.$year.'"')->andWhere(['vendor_id' => $this->vendor_id])->exists()){
                    $this->addError('period', 'Ya existe una liquidación para este periodo y este usuario.');
                    return false;
                }
            }
            
            $this->formatDatesBeforeSave();          
            return true;
        } else {
            return false;
        }     
    }
    
    /**
     * @inheritdoc
     */
    public function afterFind()
    {        
        $this->formatDatesAfterFind();
        parent::afterFind();
    }
     
    /**
     * @brief Format dates using formatter local configuration
     */
    private function formatDatesAfterFind()
    {
        $this->date = Yii::$app->formatter->asDate($this->date);
        $this->period = $this->period ? Yii::$app->formatter->asDate($this->period) : NULL;
    }
     
    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave()
    {
        $this->date = Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd');
        $this->period = $this->period ? Yii::$app->formatter->asDate($this->period, 'yyyy-MM-dd') : NULL;
    }
    
             
    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        if($this->status == 'payed'){
            return false;
        }
        return true;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: Vendor, VendorLiquidationItems.
     */
    protected function unlinkWeakRelations(){
        $this->unlinkAll('allVendorLiquidationItems',true);
    }
    
    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if($this->getDeletable()){
                $this->unlinkWeakRelations();
                return true;
            }
        } else {
            return false;
        }
    }

    public function getPeriodMonth()
    {
        return Yii::$app->formatter->asDate($this->period, 'MM-yyyy');
    }

    /**
     * Crea una nueva liquidacion para el vendedor y periodos indicados
     */
    public static function create($vendor, $period)
    {
        $liq = new self;
        
        $liq->vendor_id = $vendor->vendor_id;
        $liq->period = $period;
        
        $liq->save();
        
        return $liq;
    }
    
    //Total
    public function getTotal()
    {
        return $this->getVendorLiquidationItems()->sum('amount');    
    }
    
    //Total de descuentos
    public function getDiscountTotal()
    {
        $amount = $this->getVendorLiquidationItems()->where('amount<0')->sum('amount');    
        
        return $amount != null ? $amount : 0.0;
    }
    
    //Total de descuentos
    public function getDiscountCount()
    {
        $count = $this->getVendorLiquidationItems()->where('amount<0')->count();    
        
        return $count != null ? $count : 0;
    }
    
    //Total de planes
    public function getPlansTotal()
    {
        //Productos (no planes)
        $amount = $this->getVendorLiquidationItems()
            ->joinWith(['contractDetail' => function($query){
                $query->joinWith([
                    'product' => function($query){
                        $query->where('type="plan"');
                    }
                ]);
            }])
            ->where('amount>0')
            ->sum('amount');
            
        return $amount != null ? $amount : 0.0;
    }
    
    //Total de adicionales
    public function getAddTotal()
    {
        //Productos (no planes)
        $amount = $this->getVendorLiquidationItems()
            ->joinWith(['contractDetail' => function($query){
                $query->joinWith([
                    'product' => function($query){
                        $query->where('type<>"plan"');
                    }
                ]);
            }])
            ->where('amount>0')
            ->sum('amount');
            
        return $amount != null ? $amount : 0.0;
    }
    
    //Total de adicionales
    public function getManualTotal()
    {
        //Manuales
        $amount = $this->getVendorLiquidationItems()->where('amount>0 AND contract_detail_id IS NULL')->sum('amount');
            
        return $amount != null ? $amount : 0.0;
    }
    
    //Total de adicionales
    public function getAddCount()
    {
        //Productos (no planes)
        $count = $this->getVendorLiquidationItems()
            ->joinWith(['contractDetail' => function($query){
                $query->joinWith([
                    'product' => function($query){
                        $query->where('type<>"plan"');
                    }
                ]);
            }])
            ->where('amount>0')
            ->count();
            
        return $count != null ? $count : 0.0;
    }
    
    //Total de adicionales
    public function getManualCount()
    {
        //Manuales
        $count = $this->getVendorLiquidationItems()->where('amount>0 AND contract_detail_id IS NULL')->count();    
            
        return $count != null ? $count : 0.0;
    }
    
    //Total de adicionales
    public function getPlansCount()
    {
        //Productos (no planes)
        $count = $this->getVendorLiquidationItems()
            ->joinWith(['contractDetail' => function($query){
                $query->joinWith([
                    'product' => function($query){
                        $query->where('type="plan"');
                    }
                ]);
            }])
            ->where('amount>0')
            ->count();
            
        return $count != null ? $count : 0.0;
    }

    /**
     * Inserta en batch los items de liquidación
     */
    public static function batchInsertLiquidationItems($liquidation_items)
    {
        Yii::$app->db->createCommand()->batchInsert('vendor_liquidation_item', [
            'contract_detail_id', 'description', 'vendor_liquidation_id', 'amount'
        ], $liquidation_items)->execute();
    }
}