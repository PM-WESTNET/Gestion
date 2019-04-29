<?php

namespace app\modules\westnet\notifications\models;

use Yii;

/**
 * This is the model class for table "destinatary_has_contract_status".
 *
 * @property string $contract_status
 * @property integer $destinatary_destinatary_id
 *
 * @property Destinatary $destinataryDestinatary
 */
class DestinataryHasContractStatus extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'destinatary_has_contract_status';
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
            [['contract_status', 'destinatary_destinatary_id'], 'required'],
            [['destinatary_destinatary_id'], 'integer'],
            [['contract_status'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'contract_status' => 'Contract Status',
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
