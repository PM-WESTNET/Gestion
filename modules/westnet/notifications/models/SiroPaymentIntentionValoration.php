<?php

namespace app\modules\westnet\notifications\models;
use Yii;

/**
 * This is the model class for table "siro_payment_intention".
 *
 * @property integer $siro_payment_intention_valoration_id
 * @property string $name
 * @property string $email
 * @property string $description
 * @property integer $siro_payment_intention_id
 * @property string $created_at
 */
class SiroPaymentIntentionValoration extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'siro_payment_intention_valoration';
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
            [['created_at', 'description', 'status'], 'safe'],
            [['siro_payment_intention_valoration_id','siro_payment_intention_id'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'siro_payment_intention_valoration_id' => 'Siro Payment Intention Valoration ID',
            'description' => 'Descripción',
            'siro_payment_intention_id' => 'Siro Payment Intention ID',
            'created_at' => 'Created At',
        ];
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
     * Weak relations: None.
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

    /**
    * Return siro_payment_intention_valoration
    */
    public static function findModel($siro_payment_intention_id){
        return self::find()->where(['siro_payment_intention_id' => $siro_payment_intention_id])->one();
    }

}
