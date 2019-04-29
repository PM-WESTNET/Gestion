<?php

namespace app\modules\sale\models;

use Yii;

/**
 * This is the model class for table "tax".
 *
 * @property integer $tax_id
 * @property string $name
 * @property string $slug
 *
 * @property TaxRate[] $taxRates
 */
class Tax extends \app\components\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tax';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'slug'=>[
                'class' => \yii\behaviors\SluggableBehavior::className(),
                'slugAttribute' => 'slug',
                'attribute' => 'name',
                'ensureUnique'=>true
            ],
        ];
    }
    

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'slug'], 'string', 'max' => 45],
            [['required'], 'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tax_id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'slug' => Yii::t('app', 'Slug'),
            'required' => Yii::t('app', 'Required'),
            'taxRates' => Yii::t('app', 'Tax Rates'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxRates()
    {
        return $this->hasMany(TaxRate::className(), ['tax_id' => 'tax_id']);
    }
    
             
    /**
     * @inheritdoc
     * Strong relations: TaxRates.
     */
    public function getDeletable()
    {
        if($this->getTaxRates()->exists()){
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
    
    public function fields() 
    {
        return [
            'name',
            'slug',
            'required'
        ];
    }

}
