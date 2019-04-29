<?php

namespace app\modules\sale\models;

use Yii;

/**
 * This is the model class for table "invoice_class".
 *
 * @property integer $invoice_class_id
 * @property string $class
 * @property string $name
 *
 * @property BillType[] $billTypes
 */
class InvoiceClass extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoice_class';
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
            [['name','class'], 'required'],
            [['class'], 'string', 'max' => 255],
            [['class'], 'validateClass'],
            [['name'], 'string', 'max' => 45]
        ];
    }

    public function validateClass($attribute, $params)
    {
        if(!class_exists($this->$attribute)/* || !is_subclass_of($object, 'app\modules\invoice\component\APIInterface')*/ ){
            $this->addError($attribute, Yii::t('yii', 'Invalid class.'));
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'invoice_class_id' => Yii::t('app', 'ID'),
            'class' => Yii::t('app', 'Class'),
            'name' => Yii::t('app', 'Name'),
            'billTypes' => Yii::t('app', 'BillTypes'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBillTypes()
    {
        return $this->hasMany(BillType::className(), ['invoice_class_id' => 'invoice_class_id']);
    }
        
             
    /**
     * @inheritdoc
     * Strong relations: BillTypes.
     */
    public function getDeletable()
    {
        if($this->getBillTypes()->exists()){
            return false;
        }
        return true;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: None.
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
