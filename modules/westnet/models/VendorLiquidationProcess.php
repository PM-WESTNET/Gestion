<?php

namespace app\modules\westnet\models;

use Yii;

/**
 * This is the model class for table "vendor_liquidation_process".
 *
 * @property integer $vendor_liquidation_process_id
 * @property string $status
 * @property string $date
 * @property long $timestamp
 *
 */
class VendorLiquidationProcess extends \app\components\db\ActiveRecord
{

    const VENDOR_LIQUIDATION_PROCESS_PENDING = 'pending';
    const VENDOR_LIQUIDATION_PROCESS_CANCELLED = 'cancelled';
    const VENDOR_LIQUIDATION_PROCESS_SUCCESS = 'success';

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
        return 'vendor_liquidation_process';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            '' => [
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
            [['timestamp'], 'integer'],
            [['date', 'period'], 'safe'],
            [['date', 'period'], 'date'],
            [['status'], 'in', 'range' => ['pending','cancelled', 'success']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'vendor_liquidation_process_id' => Yii::t('westnet', 'Vendor Liquidation Process ID'),
            'status' => Yii::t('app', 'Status'),
            'period' => Yii::t('app', 'Period'),
            'timestamp' => Yii::t('app', 'Timestamp'),
            'date' => Yii::t('app', 'Date'),
        ];
    }

     /**
     * @return \yii\db\ActiveQuery
     */
    public function findVendorsSQL()
    {
        $year = Yii::$app->formatter->asDate($this->period, 'yyyy');
        $month = Yii::$app->formatter->asDate($this->period, 'MM');
        

        $vendors= Yii::$app->db->createCommand(
            'SELECT * FROM vendor WHERE vendor_id NOT IN
                (SELECT vendor_id 
                    FROM vendor_liquidation
                        WHERE YEAR(period) = :year AND MONTH(period) = :month)
        ')
        ->bindValue('year',$year)
        ->bindValue('month',$month)
        ->queryAll();

        return $vendors;    
    }

    
    /**
     * Devuelve una query para buscar contratos que deberian ser liquidados
     * TODO: debe traer contratos no liquidados
     * @return type
     */
    public function findContractsDetailsSQL($vendor_id)
    {
        $year = Yii::$app->formatter->asDate($this->period, 'yyyy');
        $month = Yii::$app->formatter->asDate($this->period, 'MM');
        
        //Detalles del mes correspondiente al periodo
        return $contract_details = Yii::$app->db->createCommand(
            'SELECT * FROM contract_detail cd
             LEFT JOIN vendor_liquidation_item vli ON vli.contract_detail_id = cd.contract_detail_id
             WHERE MONTH(date) <= :month AND YEAR(date) <= :year AND vli.vendor_liquidation_id IS NULL AND cd.vendor_id = :vendor_id 
            ')
        ->bindValue('month', $month)
        ->bindValue('year',$year)
        ->bindValue('vendor_id', $vendor_id)
        ->queryAll();

    }    

}