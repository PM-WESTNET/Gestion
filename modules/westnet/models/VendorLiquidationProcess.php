<?php

namespace app\modules\westnet\models;

use Yii;
use app\modules\sale\modules\contract\models\ContractDetail;
use yii\db\Expression;

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
    const VENDOR_LIQUIDATION_PROCESS_DRAFT = 'draft';

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

                'createdAtAttribute' => 'date',
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()')
                
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
            [['date','start_time','finish_time'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['period'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['status'], 'in', 'range' => ['pending','cancelled', 'success', 'draft']],
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

        //Detalles del mes correspondiente al periodo
        return $contract_details = Yii::$app->db->createCommand(
            'SELECT cd.contract_detail_id, cd.contract_id, cd.product_id, cd.date FROM contract_detail cd
             NATURAL LEFT JOIN vendor_liquidation_item vli
             WHERE (vli.vendor_liquidation_id IS NULL) AND (cd.vendor_id = :vendor_id)
            ')
        ->bindValue('vendor_id', $vendor_id)
        ->queryAll();

    }    

    /**
     * Saves the time at which the process was activated via cron job liquidation process
     * @return boolean
     */
    public function setProcessStartTime(){
        $this->start_time = date('Y-m-d H:i:s');
        return true;
    }

    /**
     * Saves the time at which the process ended
     * @return boolean
     */
    public function setProcessFinishTime(){
        $this->finish_time = date('Y-m-d H:i:s');
        return true;
    }

    /**
     * Calculates time spent in the process ONCE IT FINISHES
     */
    public function setTimeSpent(){
        $this->time_spent = strtotime($this->finish_time)-strtotime( $this->start_time );
        return true;
    }
}