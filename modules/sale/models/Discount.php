<?php

namespace app\modules\sale\models;

use Yii;

/**
 * This is the model class for table "discount".
 *
 * @property integer $discount_id
 * @property string $name
 * @property string $status
 * @property string $type
 * @property double $value
 * @property string $from_date
 * @property string $to_date
 * @property integer $periods
 * @property integer $product_id
 * @property string $apply_to
 * @property string $value_from
 * @property integer $referenced
 *
 * @property BillDetail[] $billDetails
 * @property CustomerHasDiscount[] $customerHasDiscounts
 * @property Product $product
 * @property ProductToInvoice[] $productToInvoices
 */
class Discount extends \app\components\db\ActiveRecord
{
    public $customer_id;
    public $lastname;
    public $code;

    public $customerAmount;

    const STATUS_ENABLED = 'enabled';
    const STATUS_DISABLED = 'disabled';

    const TYPE_FIXED = 'fixed';
    const TYPE_PERCENTAGE = 'percentage';

    const VALUE_FROM_TOTAL = 'total';
    const VALUE_FROM_PRODUCT = 'product';
    const VALUE_FROM_PLAN = 'plan';

    const APPLY_TO_CUSTOMER = 'customer';
    const APPLY_TO_PRODUCT = 'product';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'discount';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'periods', 'apply_to', 'status', 'type', 'value_from', 'value'], 'required'],
            [['status', 'type', 'apply_to', 'value_from'], 'string'],
            [['value'], 'number'],
            [['from_date', 'to_date', 'product'], 'safe'],
            [['from_date', 'to_date'], 'date'],
            [['periods', 'product_id', 'referenced'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['status'], 'in', 'range' => [Discount::STATUS_ENABLED, Discount::STATUS_DISABLED]],
            [['type'], 'in', 'range' => [Discount::TYPE_FIXED, Discount::TYPE_PERCENTAGE]],
            [['apply_to'], 'in', 'range' => [Discount::APPLY_TO_CUSTOMER, Discount::APPLY_TO_PRODUCT]],
            [['value_from'], 'in', 'range' => [Discount::VALUE_FROM_TOTAL, Discount::VALUE_FROM_PRODUCT, Discount::VALUE_FROM_PLAN]],
            [['referenced', 'persistent'], function($attribute, $params, $validator) {
                if($this->persistent == 1 && $this->referenced != 1) {
                    $this->addError($attribute, Yii::t('app', 'If persistent is selected, must be a referenced discount'));
                }
            }]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'discount_id' => Yii::t('app', 'Discount'),
            'name' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
            'type' => Yii::t('app', 'Type'),
            'value' => Yii::t('app', 'Value'),
            'from_date' => Yii::t('app', 'Effective start date'),
            'to_date' => Yii::t('app', 'Effective end date'),
            'periods' => Yii::t('app', 'Periods'),
            'product_id' => Yii::t('app', 'Product or Plan'),
            'apply_to' => Yii::t('app', 'Apply to'),
            'value_from' => Yii::t('app', 'Value from'),
            'billDetails' => Yii::t('app', 'BillDetails'),
            'customerHasDiscounts' => Yii::t('app', 'CustomerHasDiscounts'),
            'product' => Yii::t('app', 'Product'),
            'productToInvoices' => Yii::t('app', 'ProductToInvoices'),
            'referenced' => Yii::t('app', 'Referenced'),
            'persistent' => Yii::t('app', 'Persistent'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBillDetails()
    {
        return $this->hasMany(BillDetail::className(), ['discount_id' => 'discount_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerHasDiscounts()
    {
        return $this->hasMany(CustomerHasDiscount::className(), ['discount_id' => 'discount_id']);
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
    public function getProductToInvoices()
    {
        return $this->hasMany(ProductToInvoice::className(), ['discount_id' => 'discount_id']);
    }

    /**
     * Retorna todos los descuentos activos por producto.
     *
     * @param $product_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findActiveByProduct($product_id, $apply_to=Discount::APPLY_TO_PRODUCT)
    {
        return Discount::find()
            ->where([
                'product_id'   => $product_id,
                'status'       => Discount::STATUS_ENABLED,
                'apply_to'     => $apply_to
            ])->andFilterWhere([ 'and',
                ['<=', "from_date", (new \DateTime('now'))->format('Y-m-d')],
                ['>=', "to_date", (new \DateTime('now'))->format('Y-m-d')]
            ])->all();
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
        }
     
    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave()
    {
            $this->from_date = Yii::$app->formatter->asDate($this->from_date, 'yyyy-MM-dd');
            $this->to_date = Yii::$app->formatter->asDate($this->to_date, 'yyyy-MM-dd');
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
     * Weak relations: BillDetails, CustomerHasDiscounts, Product, ProductToInvoices.
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