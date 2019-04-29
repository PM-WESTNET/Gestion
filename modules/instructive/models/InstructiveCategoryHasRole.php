<?php

namespace app\modules\instructive\models;

use Yii;

/**
 * This is the model class for table "instructive_category_has_role".
 *
 * @property integer $instructive_category_has_role_id
 * @property string $role_code
 * @property integer $instructive_category_id
 *
 * @property InstructiveCategory $instructiveCategoryHasRole
 */
class InstructiveCategoryHasRole extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'instructive_category_has_role';
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
            [['instructive_category_id'], 'integer'],
            [['instructiveCategoryHasRole'], 'safe'],
            [['role_code'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'instructive_category_has_role_id' => Yii::t('app', 'Instructive Category Has Role ID'),
            'role_code' => Yii::t('app', 'Role Code'),
            'instructive_category_id' => Yii::t('app', 'Instructive Category ID'),
            'instructiveCategoryHasRole' => Yii::t('app', 'InstructiveCategoryHasRole'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstructiveCategoryHasRole()
    {
        return $this->hasOne(InstructiveCategory::className(), ['instructive_category_id' => 'instructive_category_has_role_id']);
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
     * Weak relations: InstructiveCategoryHasRole.
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
