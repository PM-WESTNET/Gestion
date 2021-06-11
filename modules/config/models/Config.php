<?php

namespace app\modules\config\models;

use Yii;
use app\modules\config\ConfigModule;

/**
 * This is the model class for table "config".
 *
 * @property integer $config_id
 * @property integer $item_id
 * @property string $value
 *
 * @property Item $item
 */
class Config extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'config';
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
        if(empty($this->item)){
            return [];
        }
        
        $rules = [];
        foreach($this->item->rules as $rule){
            $rules[] = $rule->getLine();
        }
        
        return $rules;
        
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'config_id' => ConfigModule::t('config', 'Config ID'),
            'item_id' => ConfigModule::t('config', 'Item ID'),
            'value' => ConfigModule::t('config', 'Value'),
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
    
    public static function getConfig($attr)
    {
        
        $item = Item::find()->where(['attr' => $attr])->one();
        if($item === null){
            throw new \yii\web\HttpException(404, 'Configuration item not found: ' . $attr);
        }
        
        $isMultiConfig = $item->multiple; // generally, they all seem to be 'single' configs, but just in case.
        if($isMultiConfig){
            $config = self::find()->where(['item_id' => $item->item_id])->all();
        }else{
            $config = self::find()->where(['item_id' => $item->item_id])->one();
        }
        
        if(empty($config)){
            $config = new self;
            $config->item_id = $item->item_id;
            $config->value = $item->default;
            $config->save();
            
            if($item->multiple){
                $config = [$config];
            }
        }
        
        return $config;
        
    }
    
    public static function getValue($attr)
    {
        
        $config = self::getConfig($attr);
        
        if(is_array($config)){
            return \yii\helpers\ArrayHelper::map($config, 'config_id', 'value');
        }
        
        return $config->value;
        
    }
    
    public static function setValue($attr, $value, $validate = true)
    {
        
        $item = Item::find()->where(['attr' => $attr])->one();
        if($item === null){
            throw new \yii\web\HttpException(404, ConfigModule::t('config','Configuration item not found: {attr}', ['attr' => $attr]));
        }
        
        if($item->superadmin && !Yii::$app->user->isSuperadmin){
            throw new \yii\web\HttpException(403, ConfigModule::t('config','Forbidden.'));
        }
        
        $config = self::find()->where(['item_id' => $item->item_id])->one();
        
        if(empty($config)){
            $config = new Config;
            $config->item_id = $item->item_id;
        }
        
        $config->value = $value;
        
        $config->save($validate);
        
        return $config;
        
    }
    
    public function getAttr(){
        return $this->item->attr;
    }
    
    public function getLabel(){
        return $this->item->label;
    }
    
    public function getType(){
        return $this->item->type;
    }
    
    public function getDescription(){
        return $this->item->description;
    }

}
