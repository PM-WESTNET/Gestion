<?php

namespace app\modules\sale\models;

use Yii;

/**
 * This is the model class for table "document_type".
 *
 * @property integer $document_type_id
 * @property string $name
 * @property integer $code
 * @property string $regex
 *
 * @property Customer[] $customers
 */
class DocumentType extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'document_type';
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
            [['name', 'code'], 'required'],
            [['code'], 'integer'],
            [['name'], 'string', 'max' => 45],
            [['regex'], 'string', 'max' => 255],
            [['regex'], 'validateRegex']
        ];
    }
    
    /**
     * Valida que la exp. regular sea correcta.
     */
    public function validateRegex($attribute, $params)
    {
        
        if(@preg_match($this->$attribute, null) === false){
            $this->addError('regex', Yii::t('app', 'Regular expression is not valid.'));
        }
        
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'document_type_id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'code' => Yii::t('app', 'Code'),
            'regex' => Yii::t('app', 'Regex'),
            'customers' => Yii::t('app', 'Customers'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomers()
    {
        return $this->hasMany(Customer::className(), ['document_type_id' => 'document_type_id']);
    }
    
        
             
    /**
     * @inheritdoc
     * Strong relations: Customers.
     */
    public function getDeletable()
    {
        if($this->getCustomers()->exists()){
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

    public static function getTypeVentaGlobalDiaria()
    {
        return DocumentType::findOne(['code' => '99']);
    }

}
