<?php

namespace app\modules\westnet\notifications\models;

use Yii;

/**
 * This is the model class for table "destinatary_has_contract".
 *
 * @property integer $destinatary_id
 * @property integer $contract_id
 *
 * @property Destinatary $destinatary
 */
class DestinataryHasContract extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'destinatary_has_contract';
    }
    
    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
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
            [['destinatary_id', 'contract_id'], 'required'],
            [['destinatary_id', 'contract_id'], 'integer'],
            [['destinatary'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'destinatary_id' => Yii::t('app', 'Destinatary ID'),
            'contract_id' => Yii::t('app', 'Contract ID'),
            'destinatary' => Yii::t('app', 'Destinatary'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDestinatary()
    {
        return $this->hasOne(Destinatary::className(), ['destinatary_id' => 'destinatary_id']);
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
     * Weak relations: Destinatary.
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
