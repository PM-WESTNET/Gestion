<?php

namespace app\modules\sale\models;

use Yii;

/**
 * This is the model class for table "bill_type".
 *
 * @property integer $bill_type_id
 * @property string $name
 * @property integer $code
 * @property string $view
 * @property integer $multiplier
 * @property integer $invoice_class_id
 * @property string $class
 * @property boolean $startable
 *
 * @property Bill[] $bills
 * @property InvoiceClass $invoiceClass
 */
class BillType extends \app\components\db\ActiveRecord
{

    private $_billTypes;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bill_type';
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
            [['name','code','multiplier','class'], 'required'],
            [['code','invoice_class_id'], 'integer'],
            /*[['code'], 'unique'],*/
            [['customer_required', 'startable', 'applies_to_buy_book', 'applies_to_sale_book'], 'boolean'],
            [['multiplier'], 'integer', 'min'=>-1, 'max'=>1],
            [['name'], 'string', 'max' => 45],
            [['class'], 'string', 'max' => 100],
            [['view'], 'in', 'range'=>['default','final','delivery-note']],
            ['billTypes', 'safe'],
            ['customer_required', 'in', 'range'=>[1], 'when' => function ($model) {
                return $model->invoice_class_id !=0;
            }, 'whenClient' => "function (attribute, value) {
                return $('#billtype-invoice_class_id').val() !=0;
            }"],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'bill_type_id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'code' => Yii::t('app', 'Code'),
            'bills' => Yii::t('app', 'Bills'),
            'view' => Yii::t('app', 'View'),
            'multiplier' => Yii::t('app', 'Multiplier'),
            'customer_required' => Yii::t('app', 'Customer required?'),
            'invoice_class_id' => Yii::t('app', 'Invoice Class'),
            'startable' => Yii::t('app', 'Startable'),
            'class' => Yii::t('app', 'Class'),
            'billTypes' => Yii::t('app', 'Could generate:'),
            'applies_to_buy_book' => Yii::t('app', 'Aplica al libro de compras'),
            'applies_to_sale_book' => Yii::t('app', 'Aplica al libro de ventas'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBills()
    {
        return $this->hasMany(Bill::class, ['bill_type_id' => 'bill_type_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBillTypes()
    {
        return $this->hasMany(BillType::class, ['bill_type_id' => 'child_id'])->viaTable('bill_type_has_bill_type', ['parent_id' => 'bill_type_id']);
    }
    
    /**
     * Setter para bill types
     * @param array $types
     */
    public function setBillTypes($types)
    {
        if(empty($types)){
            $types = [];
        }
        
        $this->_billTypes = $types;

        $saveTypes = function($event){
            //Quitamos las relaciones actuales
            $this->unlinkAll('billTypes', true);
            //Guardamos las nuevas relaciones
            foreach ($this->_billTypes as $id){
                $this->link('billTypes', BillType::findOne($id));
            }
        };

        $this->on(self::EVENT_AFTER_INSERT, $saveTypes);
        $this->on(self::EVENT_AFTER_UPDATE, $saveTypes);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceClass()
    {
        
        return $this->hasOne(InvoiceClass::class, ['invoice_class_id' => 'invoice_class_id']);
        
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompanies()
    {
        return $this->hasMany(Company::class, ['company_id' => 'company_id'])->viaTable('company_has_bill_type', ['bill_type_id' => 'bill_type_id']);
    }
             
    /**
     * @inheritdoc
     * Strong relations: Bills.
     */
    public function getDeletable()
    {
        if($this->getBills()->exists()){
            return false;
        }
        return true;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: None.
     */
    protected function unlinkWeakRelations(){
        \Yii::$app->db->createCommand()->delete('bill_type_has_bill_type', [
            'or', ['parent_id' => $this->bill_type_id], ['child_id' => $this->bill_type_id]
        ])->execute();
        
        \Yii::$app->db->createCommand()->delete('company_has_bill_type', ['bill_type_id' => $this->bill_type_id])->execute();
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
