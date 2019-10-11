<?php

namespace app\modules\sale\models;

use Yii;

/**
 * This is the model class for table "customer_has_discount".
 *
 * @property integer $cutomer_has_discount_id
 * @property integer $customer_id
 * @property integer $discount_id
 * @property string $from_date
 * @property string $to_date
 * @property string $status
 * @property string $description
 *
 * @property Customer $customer
 * @property Discount $discount
 */
class CustomerHasDiscount extends \app\components\db\ActiveRecord
{

    const STATUS_ENABLED = 'enabled';
    const STATUS_DISABLED = 'disabled';

    public static function tableName()
    {
        return 'customer_has_discount';
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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'discount_id', 'status'], 'required'],
            [['customer_id', 'discount_id'], 'integer'],
            [['from_date', 'to_date', 'customer', 'discount'], 'safe'],
            [['from_date', 'to_date'], 'date'],
            [['status', 'description'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cutomer_has_discount_id' => Yii::t('app', 'Cutomer Has Discount ID'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'discount_id' => Yii::t('app', 'Discount ID'),
            'from_date' => Yii::t('app', 'From Date'),
            'to_date' => Yii::t('app', 'To Date'),
            'status' => Yii::t('app', 'Status'),
            'customer' => Yii::t('app', 'Customer'),
            'discount' => Yii::t('app', 'Discount'),
            'description' => \Yii::t('app', 'Description for Invoice'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['customer_id' => 'customer_id']);
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
            if($insert) {
                if(!$this->discount->persistent) {
                    $this->to_date = (new \DateTime(Yii::$app->formatter->asDate($this->from_date)));
                    $this->to_date->add(new \DateInterval("P".$this->discount->periods."M"));
                    $this->to_date->modify('-1 day');
                    $this->to_date = $this->to_date->format('Y-m-d');
                }
            }
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
        }
     
    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave()
    {
            $this->from_date = Yii::$app->formatter->asDate($this->from_date, 'yyyy-MM-dd');
            $this->to_date = $this->to_date ? Yii::$app->formatter->asDate($this->to_date, 'yyyy-MM-dd') : null;
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
     * Weak relations: Customer, Discount.
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

    /**
     * Verifica si el Customer ya tiene un descuento activo en el rango de fechas
     * del que se quiere crear.
     *
     * @return bool
     */
    public function canAddDiscount()
    {
        return ($this
            ->find()
            ->where([
                'customer_id'   => $this->customer_id,
                'discount_id'   => $this->discount_id,
                'status'        => Discount::STATUS_ENABLED
            ])->andFilterWhere([ 'and',
                    ['<=', "from_date", Yii::$app->formatter->asDate($this->from_date, 'yyyy-MM-dd')],
                    ['>=', "to_date", Yii::$app->formatter->asDate($this->from_date, 'yyyy-MM-dd')]
            ])->count() == 0);
    }

    public function __toString()
    {
        return $this->discount->name . ($this->description ? " - " . $this->description : '' );
    }
}
