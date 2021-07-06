<?php

namespace app\modules\westnet\notifications\models;

use Yii;

/**
 * This is the model class for table "siro_payment_intention".
 *
 * @property integer $siro_payment_intention_id
 * @property string $bill_id
 * @property string $hash
 * @property string $reference
 * @property string $url
 * @property string $createdAt
 * @property string $updatedAt
 * @property string $status
 * @property string id_resultado
 */
class SiroPaymentIntention extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'siro_payment_intention';
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
            [['createdAt', 'updatedAt', 'status'], 'safe'],
            [['bill_id'], 'string', 'max' => 45],
            [['hash','id_resultado'], 'string', 'max' => 100],
            [['reference'], 'string', 'max' => 50],
            [['url'], 'string', 'max' => 70],
            [['siro_payment_intention_id'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'siro_payment_intention_id' => 'Siro Payment Intention ID',
            'bill_id' => 'Bill ID',
            'hash' => 'Hash',
            'reference' => 'Reference',
            'url' => 'Url',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
            'status' => 'Status',
            'id_resultado' => 'Resultado ID'
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

}
