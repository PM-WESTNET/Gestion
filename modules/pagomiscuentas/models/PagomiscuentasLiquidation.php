<?php

namespace app\modules\pagomiscuentas\models;

use Yii;

/**
 * This is the model class for table "pagomiscuentas_liquidation".
 *
 * @property integer $pagomiscuentas_liquidation_id
 * @property string $file
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $number
 * @property integer $account_movement_id
 *
 * @property AccountMovement $accountMovement
 */
class PagomiscuentasLiquidation extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pagomiscuentas_liquidation';
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
            [['created_at', 'updated_at'], 'required'],
            [['created_at', 'updated_at', 'number', 'account_movement_id'], 'integer'],
            [['accountMovement'], 'safe'],
            [['file'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pagomiscuentas_liquidation_id' => Yii::t('app', 'Pagomiscuentas Liquidation ID'),
            'file' => Yii::t('app', 'File'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'number' => Yii::t('app', 'Number'),
            'account_movement_id' => Yii::t('app', 'Account Movement ID'),
            'accountMovement' => Yii::t('app', 'AccountMovement'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccountMovement()
    {
        return $this->hasOne(AccountMovement::className(), ['account_movement_id' => 'account_movement_id']);
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
