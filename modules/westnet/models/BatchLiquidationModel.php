<?php

namespace app\modules\westnet\models;

use Yii;
use app\modules\westnet\models\Vendor;
use app\modules\sale\modules\contract\models\ContractDetail;

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
class BatchLiquidationModel extends \yii\base\Model
{

    public $period;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['period'], 'required'],
            [['period'], 'date'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'vendor_liquidation_id' => Yii::t('westnet', 'Vendor Liquidation ID'),
            'period' => Yii::t('app', 'Period'),
            'periodMonth' => Yii::t('app', 'Period')
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function findVendors()
    {
        $year = Yii::$app->formatter->asDate($this->period, 'yyyy');
        $month = Yii::$app->formatter->asDate($this->period, 'MM');
        
        $subQuery = VendorLiquidation::find()->where("YEAR(period)='$year' AND MONTH(period)='$month'");
        
        return Vendor::find()->where(['NOT IN', 'vendor_id', $subQuery->select('vendor_id')]);
        
    }
    
    /**
     * Devuelve una query para buscar contratos que deberian ser liquidados
     * TODO: debe traer contratos no liquidados
     * @return type
     */
    public function findContractsDetails()
    {
        $year = Yii::$app->formatter->asDate($this->period, 'yyyy');
        $month = Yii::$app->formatter->asDate($this->period, 'MM');
        
        //Detalles del mes correspondiente al periodo
        $query = ContractDetail::find()->andWhere("MONTH(date)<=$month AND YEAR(date)<=$year");
                
        $query->join('natural left join', 'vendor_liquidation_item')->where('vendor_liquidation_item.vendor_liquidation_id IS NULL');
        
        return $query;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVendorLiquidationItems()
    {
        return $this->hasMany(VendorLiquidationItem::class, ['vendor_liquidation_id' => 'vendor_liquidation_id']);
    }
    
    public function getPeriodMonth()
    {
        return Yii::$app->formatter->asDate($this->period, 'MM-yyyy');
    }
}
