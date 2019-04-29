<?php

namespace app\modules\sale\models;

use Yii;

/**
 * This is the model class for table "profile_class".
 *
 * @property integer $profile_class_id
 * @property string $name
 * @property string $data_type
 * @property integer $data_min
 * @property integer $data_max
 * @property string $pattern
 * @property string $status
 * @property string $order
 * @property string $multiple
 * @property string $default
 * @property string $hint
 * @property string $searchable
 *
 * @property Profile[] $profiles
 */
class ProfileClass extends \app\components\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'profile_class';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'],'required'],
            [['data_max','data_min','order'], 'integer'],
            [['name', 'data_type', 'pattern'], 'string', 'max' => 45],
            [['status'],'in','range'=>['enabled','disabled']],
            [['multiple', 'searchable'],'boolean'],
            [['hint'],'string','max'=>255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'profile_class_id' => Yii::t('app', 'Profile Class ID'),
            'name' => Yii::t('app', 'Name'),
            'data_type' => Yii::t('app', 'Data Type'),
            'data_max' => Yii::t('app', 'Max data value (or max length)'),
            'data_min' => Yii::t('app', 'Min data value (or min length)'),
            'pattern' => Yii::t('app', 'Pattern'),
            'status' => Yii::t('app', 'Status'),
            'order' => Yii::t('app', 'Order'),
            'multiple' => Yii::t('app', 'Multiple'),
            'hint' => Yii::t('app', 'Hint'),
            'searchable' => Yii::t('app', 'Searchable'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfiles()
    {
        return $this->hasMany(Profile::className(), ['profile_class_id' => 'profile_class_id']);
    }
    
    /**
     * Devuelve las reglas de validacion generadas para esta clase de profile
     * @return []
     */
    public function getValidationRules(){
        
        $rules = [
            'textInput'=>[[$this->attr], 'string', 'max'=>$this->data_max, 'min'=>$this->data_min],
            'textArea'=>[[$this->attr], 'string', 'max'=>$this->data_max, 'min'=>$this->data_min],
            'checkbox'=>[[$this->attr], 'boolean'],
        ];
        
        $rules = $rules[$this->data_type];
        
        if(!empty($this->pattern))
            $rules[] = [[$this->attr], 'match', 'pattern'=>$this->pattern];
        
        return [
            $rules
        ];
        
    }
    
    public function getAttr(){
        return 'profile_'.$this->profile_class_id;
    }
    
    public function getDeletable(){
    
        if($this->getProfiles()->exists()){
            return false;
        }
        return true;
        
    }
}
