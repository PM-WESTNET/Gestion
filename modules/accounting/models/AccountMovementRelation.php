<?php

namespace app\modules\accounting\models;

use Yii;

/**
 * This is the model class for table "account_movement_relation".
 *
 * @property integer $account_movement_relation_id
 * @property string $class
 * @property integer $model_id
 * @property integer $account_movement_id
 *
 * @property AccountMovement $accountMovement
 */
class AccountMovementRelation extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account_movement_relation';
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
            [['class', 'model_id', 'account_movement_id'], 'required'],
            [['model_id', 'account_movement_id'], 'integer'],
            [['accountMovement'], 'safe'],
            [['class'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'account_movement_relation_id' => Yii::t('accounting', 'Account Movement Relation ID'),
            'class' => Yii::t('accounting', 'Class'),
            'model_id' => Yii::t('accounting', 'Model ID'),
            'account_movement_id' => Yii::t('accounting', 'Account Movement ID'),
            'accountMovement' => Yii::t('accounting', 'AccountMovement'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccountMovement()
    {
        return $this->hasOne(AccountMovement::className(), ['account_movement_id' => 'account_movement_id']);
    }

    public function getModel()
    {
        $obj = new $this->class();
        return $this->hasOne($this->class, [$obj->tableSchema->primaryKey[0] => 'model_id']);
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
     * Weak relations: AccountMovement.
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
