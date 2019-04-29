<?php

namespace app\modules\sale\models;

use Yii;

/**
 * This is the model class for table "sale_point".
 *
 * @property integer $point_of_sale_id
 * @property string $name
 * @property integer $number
 * @property string $status
 * @property string $description
 * @property integer $company_id
 * @property integer $default
 * @property integer $electronic_billing
 *
 * @property Company $company
 */
class PointOfSale extends \app\components\companies\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'point_of_sale';
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
            [['name', 'number', 'company_id'], 'required'],
            [['number', 'company_id'], 'integer'],
            [['number'], 'unique', 'filter' => ['company_id' => $this->company_id]],
            [['status'], 'string'],
            [['company'], 'safe'],
            [['name'], 'string', 'max' => 45],
            [['description'], 'string', 'max' => 255],
            [['default'], 'boolean'],
            [['electronic_billing'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'point_of_sale_id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'number' => Yii::t('app', 'Number'),
            'status' => Yii::t('app', 'Status'),
            'description' => Yii::t('app', 'Description'),
            'company' => Yii::t('app', 'Company'),
            'default' => Yii::t('app', 'Default'),
            'electronic_billing' => Yii::t('app', 'Issue electronic bill')
        ];
    }    
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['company_id' => 'company_id']);
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
     * Weak relations: Company.
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
     * Solo un default por empresa
     * @param type $insert
     */
    public function beforeSave($insert) 
    {
        if(parent::beforeSave($insert)){
            
            if($this->default && $this->status == 'enabled'){
                PointOfSale::updateAll(['default' => 0], ['company_id' => $this->company_id]);
            }
            
            return true;
            
        }else{
            return false;
        }
    }

    public function getFullname()
    {
        return "$this->name - $this->number";
    }

}
