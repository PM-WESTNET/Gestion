<?php

namespace app\modules\sale\models;

use Yii;

/**
 * This is the model class for table "tax_condition_has_document_type".
 *
 * @property integer $tax_condition_id
 * @property integer $document_type_document_type_id
 *
 * @property DocumentType $documentTypeDocumentType
 * @property TaxCondition $taxCondition
 */
class TaxConditionHasDocumentTypeBuy extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tax_condition_has_document_type_buy';
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
            [['tax_condition_id', 'document_type_document_type_id'], 'required'],
            [['tax_condition_id', 'document_type_document_type_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tax_condition_id' => 'Tax Condition ID',
            'document_type_document_type_id' => 'Document Type Document Type ID',
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentTypeDocumentType()
    {
        return $this->hasOne(DocumentType::className(), ['document_type_id' => 'document_type_document_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxCondition()
    {
        return $this->hasOne(TaxCondition::className(), ['tax_condition_id' => 'tax_condition_id']);
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
     * Weak relations: DocumentTypeDocumentType, TaxCondition.
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
