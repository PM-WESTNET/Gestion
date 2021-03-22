<?php

namespace app\modules\sale\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "hour_range".
 *
 * @property integer $hour_range_id
 * @property string $from
 * @property string $to
 *
 * @property CustomerHasHourRange[] $customerHasHourRanges
 */
class HourRange extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hour_range';
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
            [['from', 'to'], 'required'],
            ['from', 'date', 'format' => 'H:m'],
            ['to', 'date', 'format' => 'H:m'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hour_range_id' => Yii::t('app', 'Hour Range'),
            'from' => Yii::t('app', 'From'),
            'to' => Yii::t('app', 'To'),
            'customerHasHourRanges' => Yii::t('app', 'CustomerHasHourRanges'),
        ];
    }    


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerHasHourRanges()
    {
        return $this->hasMany(CustomerHasHourRange::className(), ['hour_range_id' => 'hour_range_id']);
    }
             
    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable()
    {
        if($this->getCustomerHasHourRanges()->exists()) {
            return false;
        }

        return true;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: CustomerHasHourRanges.
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
     * @return string
     * Devuelve una concatenacion del horario desde y hasta, para una mejor identificación
     */
    public function getFullName()
    {
        return $this->from .'-'. $this->to;
    }

    /**
     * @return array
     * Devuelve un array con los nombres de los rangos horarios para ser mostrados en un checklist
     */
    public static function getHourRangeForCheckList()
    {
        return ArrayHelper::map(HourRange::find()->all(), 'hour_range_id', 'fullName');
    }

}
