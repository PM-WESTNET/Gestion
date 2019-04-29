<?php

namespace app\modules\westnet\ecopagos\models;

use Yii;
use app\modules\westnet\ecopagos\EcopagosModule;

/**
 * This is the model class for table "arya_westnet_ecopago.justification".
 *
 * @property integer $justification_id
 * @property integer $payout_id
 * @property string $cause
 */
class Justification extends \app\components\db\ActiveRecord
{

    const TYPE_REPRINT = 'reprint';
    const TYPE_CANCELLATION = 'cancellation';

    public static function tableName()
    {
        return 'justification';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('dbecopago');
    }

    public function rules()
    {
        return [
            [['payout_id'], 'integer'],
            [['type'], 'string'],
            [['date'], 'safe'],
            [['cause'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'justification_id' => 'Justification ID',
            'payout_id' => 'Payout ID',
            'cause' => EcopagosModule::t('app', 'Cause'),
            'type' => EcopagosModule::t('app', 'Type'),
            'date' => EcopagosModule::t('app', 'Date')
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
    
    public static function newJustification($payout_id, $cause, $re_print){
        $model = new Justification();
        $model->payout_id = $payout_id;
        $model->cause = $cause;
        $date = new \Datetime('now');
        $model->date = $date->format('Y-m-d H:i:s');
        if($re_print == 'true'){
            $model->type = Justification::TYPE_REPRINT;
        } else {
            $model->type = Justification::TYPE_CANCELLATION;
        }
        $model->save();
        
        return $model;
    }

}
