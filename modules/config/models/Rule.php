<?php

namespace app\modules\config\models;

use Yii;
use app\modules\config\ConfigModule;

/**
 * This is the model class for table "rule".
 *
 * @property integer $rule_id
 * @property string $message
 * @property double $max
 * @property double $min
 * @property string $pattern
 * @property string $format
 * @property string $targetAttribute
 * @property string $targetClass
 * @property integer $item_id
 * @property string $validator
 *
 * @property Item $item
 */
class Rule extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rule';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbconfig');
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
            [['item_id', 'validator'], 'required'],
            [['item_id'], 'integer'],
            [['max', 'min'], 'number'],
            [['message', 'pattern', 'targetClass'], 'string', 'max' => 255],
            [['format', 'targetAttribute', 'validator'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rule_id' => ConfigModule::t('config', 'Rule ID'),
            'message' => ConfigModule::t('config', 'Message'),
            'max' => ConfigModule::t('config', 'Max value'),
            'min' => ConfigModule::t('config', 'Min value'),
            'pattern' => ConfigModule::t('config', 'Pattern'),
            'format' => ConfigModule::t('config', 'Format'),
            'targetAttribute' => ConfigModule::t('config', 'Target Attribute'),
            'targetClass' => ConfigModule::t('config', 'Target Class'),
            'item_id' => ConfigModule::t('config', 'Item ID'),
            'item' => ConfigModule::t('config', 'Item'),
            'validator' => ConfigModule::t('config', 'Validator'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['item_id' => 'item_id']);
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
     * Weak relations: Item.
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
    
    public static function getValidatorsList() 
    {
        
        $builtInValidators = \yii\validators\Validator::$builtInValidators;
        
        $validators = array_diff_key($builtInValidators, array_flip([
            'default',
            'safe',
            'filter',
            'file',
            'image',
            'captcha',
            'compare',
            'each',
            'in'
        ]));
        
        $keys = array_keys($validators);
        return array_combine($keys, $keys);
        
    }
    
    public static function getAvaibleAttributes()
    {
        $avaibleAttrs = [
            'max',
            'min',
            'pattern',
            'format',
            'targetAttribute',
            'targetClass',
        ];
        
        return $avaibleAttrs;
    }
    
    public static function getValidatorAttributes($validator)
    {
        $builtInValidators = \yii\validators\Validator::$builtInValidators;
        
        $avaibleAttrs = static::getAvaibleAttributes();
        
        $attrs = [];
        
        if (isset($builtInValidators[$validator])) {
            
            $type = $builtInValidators[$validator];
            
            if(is_array($type)){
                $type = $type['class'];
            }
            
            $reflex = new \ReflectionClass($type);
            
            foreach($reflex->getProperties() as $property){
                if(in_array($property->name, $avaibleAttrs)){
                    $attrs[] = $property->name;
                }
            }            
            
        }
        
        return $attrs;
        
    }

    public function getLine()
    {
        
        $rule = ['value', $this->validator];
        
        $avaibleAttrs = static::getAvaibleAttributes();
        
        foreach ($avaibleAttrs as $attr){
            if(!empty($this->$attr)){
                $rule[$attr] = $this->$attr;
            }
        }
        
        return $rule;
        
    }
    
}
