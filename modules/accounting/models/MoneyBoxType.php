<?php

namespace app\modules\accounting\models;

use Yii;

/**
 * This is the model class for table "money_box_type".
 *
 * @property integer $money_box_type_id
 * @property string $name
 * @property string $code
 *
 * @property MoneyBox[] $moneyBoxes
 */
class MoneyBoxType extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'money_box_type';
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
            [['name'], 'string', 'max' => 150],
            [['code'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'money_box_type_id' => Yii::t('accounting', 'ID'),
            'name' => Yii::t('accounting', 'Name'),
            'code' => Yii::t('accounting', 'Code'),
            'moneyBoxes' => Yii::t('accounting', 'Money Boxes'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMoneyBoxes()
    {
        return $this->hasMany(MoneyBox::className(), ['money_box_type_id' => 'money_box_type_id']);
    }
    
        
             
    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        if ($this->getMoneyBoxes()->exists()) {
            return false;
        }
        
        return true;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: MoneyBoxes.
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
