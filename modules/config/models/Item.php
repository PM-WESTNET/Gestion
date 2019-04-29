<?php

namespace app\modules\config\models;

use Yii;
use app\modules\config\ConfigModule;

/**
 * This is the model class for table "item".
 *
 * @property integer $item_id
 * @property string $attr
 * @property string $type
 * @property string $default
 * @property string $label
 * @property string $description
 * @property integer $multiple
 * @property integer $category_category_id
 *
 * @property Config[] $configs
 * @property Category $categoryCategory
 */
class Item extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'item';
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
            [['attr', 'category_id'], 'required'],
            [['multiple', 'category_id'], 'integer'],
            [['label'], 'string', 'max' => 140],
            [['attr', 'type'], 'string', 'max' => 45],
            [['default', 'description'], 'string', 'max' => 1000],
            [['attr'], 'unique'],
            [['superadmin'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'item_id' => ConfigModule::t('config', 'Item ID'),
            'attr' => ConfigModule::t('config', 'Attr'),
            'type' => ConfigModule::t('config', 'Type'),
            'default' => ConfigModule::t('config', 'Default'),
            'label' => ConfigModule::t('config', 'Label'),
            'description' => ConfigModule::t('config', 'Description'),
            'multiple' => ConfigModule::t('config', 'Multiple'),
            'category_id' => ConfigModule::t('config', 'Category'),
            'superadmin' => ConfigModule::t('config', 'Superadmin only'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConfigs()
    {
        return $this->hasMany(Config::className(), ['item_id' => 'item_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRules()
    {
        return $this->hasMany(Rule::className(), ['item_id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['category_id' => 'category_id']);
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
     * Weak relations: Configs, CategoryCategory.
     */
    protected function unlinkWeakRelations(){
        
        $this->unlinkAll('configs', true);
        
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
    
    public static function types()
    {
        return [
            'textInput' => 'Text',
            'textarea' => 'Textarea',
            'html' => 'Html',
            'checkbox' => 'Checkbox',
            'passwordInput' => 'Password'
        ];
    }
    
    public static function getItems($category)
    {
        
        $query = Item::find();
        
        $query->where(['category_id' => $category->category_id]);
        
        if(!Yii::$app->user->isSuperadmin){
            $query->andWhere(['superadmin' => 0]);
        }
        
        return $query->all();
        
    }

}
