<?php

namespace app\modules\sale\models;

use Yii;

/**
 * This is the model class for table "tax_condition".
 *
 * @property integer $tax_condition_id
 * @property string $name
 * @property integer $bill_type_id
 * @property integer $exempt
 *
 * @property Customer[] $customers
 * @property BillType[] $billTypes
 * @property BillType[] $billTypesBuy
 * @property DocumentType[] $documentType
 */
class TaxCondition extends \app\components\db\ActiveRecord
{
    private $_billTypes;
    private $_billTypesBuy;
    
    public $_documentTypes;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tax_condition';
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
            [['name'], 'required'],
            [['billTypes', 'documentType', '_documentTypes', 'billTypesBuy'], 'safe'],
            [['name'], 'string', 'max' => 45],
            [['exempt'], 'default', 'value'=>null],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tax_condition_id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'bill_type_id' => Yii::t('app', 'Bill Type'),
            'document_type_id' => Yii::t('app', 'Document type required'),
            'customers' => Yii::t('app', 'Customers'),
            'billTypes' => Yii::t('app', 'Bill Types'),
            'billTypesBuy' => Yii::t('app', 'Bill Types Buy'),
            'documentType' => Yii::t('app', 'Document type required'),
            'exempt'       => Yii::t('app', 'Exempt'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomers()
    {
        return $this->hasMany(Customer::className(), ['tax_condition_id' => 'tax_condition_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBillTypes()
    {
        return $this->hasMany(BillType::className(), ['bill_type_id' => 'bill_type_id'])->viaTable('tax_condition_has_bill_type', ['tax_condition_id' => 'tax_condition_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBillTypesBuy()
    {
        return $this->hasMany(BillType::className(), ['bill_type_id' => 'bill_type_id'])->viaTable('tax_condition_has_bill_type_buy', ['tax_condition_id' => 'tax_condition_id']);
    }

    /**
     * Devuelve una lista con los nombres de los tipos de factura
     * @return string
     */
    public function getBillTypesNames($separator = ', '){
        $names = null;
        foreach($this->billTypes as $i => $type){
            if(($i+1) == count($this->billTypes)){
                $names .= $type->name;
            }else{
                $names .= $type->name.$separator;
            }
        }
        
        return $names;
    }

    /**
     * Devuelve una lista con los nombres de los tipos de factura
     * @return string
     */
    public function getBillTypesBuyNames($separator = ', '){
        $names = null;
        foreach($this->billTypesBuy as $i => $type){
            if(($i+1) == count($this->billTypesBuy)){
                $names .= $type->name;
            }else{
                $names .= $type->name.$separator;
            }
        }

        return $names;
    }

    /**
     * @param array $categories
     */
    public function setBillTypes($types){
        if(empty($types)){
            $types = [];
        }
        
        $this->_billTypes = $types;

        $save = function($event){
            //Quitamos las relaciones actuales
            $this->unlinkAll('billTypes', true);
            //Guardamos las nuevas relaciones
            foreach ($this->_billTypes as $id){
                $this->link('billTypes', BillType::findOne($id));
            }
        };

        $this->on(self::EVENT_AFTER_INSERT, $save);
        $this->on(self::EVENT_AFTER_UPDATE, $save);
    }

    /**
     * @param array $categories
     */
    public function setBillTypesBuy($types){
        if(empty($types)){
            $types = [];
        }

        $this->_billTypesBuy = $types;

        $save = function($event){
            //Quitamos las relaciones actuales
            $this->unlinkAll('billTypesBuy', true);
            //Guardamos las nuevas relaciones
            foreach ($this->_billTypesBuy as $id){
                $this->link('billTypesBuy', BillType::findOne($id));
            }
        };

        $this->on(self::EVENT_AFTER_INSERT, $save);
        $this->on(self::EVENT_AFTER_UPDATE, $save);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentType()
    {
        return $this->hasMany(DocumentType::className(), ['document_type_id' => 'document_type_document_type_id'])->viaTable('tax_condition_has_document_type', ['tax_condition_id' => 'tax_condition_id']);
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
     * Weak relations: BillType, DocumentType.
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
    
    private function addDocumentTypes(){
        if (!empty($this->documentType)) {
            $this->unlinkAll('documentType', true);
        }

        if(!is_array($this->_documentTypes)){
            $this->_documentTypes = [];
        }
        
        foreach ($this->_documentTypes as $type){
            $tchdt= new TaxConditionHasDocumentType();
            $tchdt->document_type_document_type_id= $type;
            $tchdt->tax_condition_id= $this->tax_condition_id;
            $tchdt->save();
        }
        
    }
    
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        
        $this->addDocumentTypes();
    }
    
    public function getDocumentTypesLabels(){
        $labels= '';
        foreach ($this->documentType as $key => $type){
            $labels .=  $type->name . ($key === (count($this->documentType)-1)? ' ' : ', ' ) ;
        }
        
        return $labels;
    }
    
    public function afterFind() {
        parent::afterFind();
        
        $this->_documentTypes=[];
        foreach ($this->documentType as $type){
            $this->_documentTypes[]= $type->document_type_id;
        }
        
        
        
    }

}
