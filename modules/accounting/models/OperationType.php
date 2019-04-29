<?php

namespace app\modules\accounting\models;

use Yii;

/**
 * This is the model class for table "operation_type".
 *
 * @property integer $operation_type_id
 * @property string $name
 * @property string $code
 * @property integer $is_debit
 *
 * @property MoneyBoxHasOperationType[] $moneyBoxHasOperationTypes
 */
class OperationType extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'operation_type';
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
            [['name', 'code'], 'required'],
            [['is_debit'], 'integer'],
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
            'operation_type_id' => Yii::t('accounting', 'Operation Type ID'),
            'name' => Yii::t('accounting', 'Name'),
            'code' => Yii::t('accounting', 'Code'),
            'is_debit' => Yii::t('accounting', 'Debit'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMoneyBoxHasOperationTypes()
    {
        return $this->hasMany(MoneyBoxHasOperationType::className(), ['operation_type_id' => 'operation_type_id']);
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
     * Weak relations: MoneyBoxHasOperationTypes.
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

    public static function findRestOfMoneyBox($money_box_id, $operation_type_id=null)
    {
        $query = OperationType::find();
        $query->where(['not in', 'operation_type_id',
            (MoneyBoxHasOperationType::find()
                ->select('operation_type_id')
                ->where(['money_box_id' => $money_box_id]))]);

        if(!is_null($operation_type_id)) {
            $query->orWhere(['operation_type_id' => $operation_type_id]);
        }
        return $query;
    }
}
