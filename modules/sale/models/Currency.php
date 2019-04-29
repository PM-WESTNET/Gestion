<?php

namespace app\modules\sale\models;

use Yii;

/**
 * This is the model class for table "currency".
 *
 * @property integer $currency_id
 * @property string $name
 * @property string $iso
 * @property double $rate
 * @property string $status
 * @property string $code
 *
 * @property Bill[] $bills
 */
class Currency extends \app\components\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'currency';
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
            [['name','status','code'], 'required'],
            [['code'], 'unique'],
            [['rate'], 'number'],
            [['status'], 'in', 'range'=>['enabled','disabled']],
            [['name'], 'string', 'max' => 45],
            [['iso', 'code'], 'string', 'max' => 5]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'currency_id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'iso' => Yii::t('app', 'Iso'),
            'rate' => Yii::t('app', 'Currency rate'),
            'status' => Yii::t('app', 'Status'),
            'code' => Yii::t('app', 'Code'),
            'bills' => Yii::t('app', 'Bills'),
        ];
    }    

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBills()
    {
        return $this->hasMany(Bill::className(), ['currency_id' => 'currency_id']);
    }
    
    /**
     * @inheritdoc
     * Strong relations: Bills.
     */
    public function getDeletable()
    {
        if($this->getBills()->exists()){
            return false;
        }
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
