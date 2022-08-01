<?php

namespace app\modules\sale\modules\contract\models;

use app\modules\sale\models\Discount;
use app\modules\sale\models\FundingPlan;
use app\modules\sale\models\Product;
use Yii;

/**
 * This is the model class for table "contract_detail_log".
 *
 * @property integer $contract_detail_log_id
 * @property integer $contract_detail_id
 * @property string $from_date
 * @property string $to_date
 * @property string $date
 * @property string $status
 * @property integer $product_id
 * @property integer $funding_plan_id
 * @property integer $discount_id
 *
 * @property ContractDetail $contractDetail
 * @property FundingPlan $fundingPlan
 * @property Product $product
 * @property Discount $discount
 */
class ContractDetailLog extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contract_detail_log';
    }
    
    /**
     * @inheritdoc
     */
    /*
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
    */

    public function behaviors()
    {
        return [
            'created_at' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['contract_detail_id'], 'required'],
            [['contract_detail_id', 'product_id', 'funding_plan_id', 'discount_id'], 'integer'],
            [['from_date', 'to_date', 'contractDetail', 'fundingPlan', 'product', 'discount'], 'safe'],
            [['from_date', 'to_date'], 'date'],
            [['status'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'contract_detail_log_id' => Yii::t('app', 'Contract Detail Log'),
            'contract_detail_id' => Yii::t('app', 'Contract Detail'),
            'from_date' => Yii::t('app', 'From Date'),
            'to_date' => Yii::t('app', 'To Date'),
            'status' => Yii::t('app', 'Status'),
            'product_id' => Yii::t('app', 'Product'),
            'funding_plan_id' => Yii::t('app', 'Funding Plan'),
            'contractDetail' => Yii::t('app', 'Contract Detail'),
            'fundingPlan' => Yii::t('app', 'Funding Plan'),
            'product' => Yii::t('app', 'Product'),
            'discount' => Yii::t('app', 'Discount'),
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContractDetail()
    {
        return $this->hasOne(ContractDetail::className(), ['contract_detail_id' => 'contract_detail_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFundingPlan()
    {
        return $this->hasOne(FundingPlan::className(), ['funding_plan_id' => 'funding_plan_id']);
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
    public function getDiscount()
    {
        return $this->hasOne(Discount::className(), ['discount_id' => 'discount_id']);
    }
        
        
    /**
     * @inheritdoc
     */
     
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {            
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
        $this->from_date = Yii::$app->formatter->asDate($this->from_date);
        $this->to_date = Yii::$app->formatter->asDate($this->to_date);
        $this->date = Yii::$app->formatter->asDate($this->date);
    }

    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave()
    {
        if(empty($this->from_date) || $this->from_date == Yii::t('app', 'Undetermined time')){
            $this->from_date = null;
        }else{
            $this->from_date = Yii::$app->formatter->asDate($this->from_date, 'yyyy-MM-dd');
        }
        if(empty($this->to_date) || $this->to_date == Yii::t('app', 'Undetermined time')){
            $this->to_date = null;
        }else{
            $this->to_date = Yii::$app->formatter->asDate($this->to_date, 'yyyy-MM-dd');
        }
        try{
            $this->date = Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd');
        } catch(\Exception $ex){
            $this->date = (new \DateTime('now'))->format('Y-m-d');
        }
    }
    
         
    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        return true;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: Contract.
     */
    protected function unlinkWeakRelations(){
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

}
