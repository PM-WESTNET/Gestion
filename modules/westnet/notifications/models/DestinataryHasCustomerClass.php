<?php

namespace app\modules\westnet\notifications\models;

use Yii;

/**
 * This is the model class for table "destinatary_has_customer_class".
 *
 * @property integer $customer_class_id
 * @property integer $destinatary_destinatary_id
 *
 * @property Destinatary $destinataryDestinatary
 */
class DestinataryHasCustomerClass extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'destinatary_has_customer_class';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbnotifications');
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
            [['customer_class_id', 'destinatary_destinatary_id'], 'required'],
            [['customer_class_id', 'destinatary_destinatary_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'customer_class_id' => 'Customer Class ID',
            'destinatary_destinatary_id' => 'Destinatary Destinatary ID',
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDestinataryDestinatary()
    {
        return $this->hasOne(Destinatary::className(), ['destinatary_id' => 'destinatary_destinatary_id']);
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
     * Weak relations: DestinataryDestinatary.
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
