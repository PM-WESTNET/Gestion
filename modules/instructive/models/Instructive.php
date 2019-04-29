<?php

namespace app\modules\instructive\models;

use Yii;

/**
 * This is the model class for table "instructive".
 *
 * @property integer $instructive_id
 * @property string $name
 * @property string $summary
 * @property string $content
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $instructive_category_id
 *
 * @property InstructiveCategory $instructiveCategory
 */
class Instructive extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'instructive';
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
            [['instructive_id', 'created_at', 'updated_at', 'instructive_category_id'], 'integer'],
            [['name', 'instructive_category_id'], 'required'],
            [['content'], 'string'],
            [['name'], 'string', 'max' => 45],
            [['summary'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'instructive_id' => Yii::t('app', 'Instructive'),
            'name' => Yii::t('app', 'Name'),
            'summary' => Yii::t('app', 'Summary'),
            'content' => Yii::t('app', 'Content'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'instructive_category_id' => Yii::t('app', 'Instructive Category'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstructiveCategory()
    {
        return $this->hasOne(InstructiveCategory::className(), ['instructive_category_id' => 'instructive_category_id']);
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
     * Weak relations: InstructiveCategory.
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
