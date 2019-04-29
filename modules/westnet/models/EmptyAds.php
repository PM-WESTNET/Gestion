<?php

namespace app\modules\westnet\models;

use app\components\db\ActiveRecord;
use app\modules\sale\models\Company;
use Yii;
use yii\db\Query;

/**
 * This is the model class for table "empty_ads".
 *
 * @property integer $empty_ads_id
 * @property integer $code
 * @property string $payment_code
 * @property integer $node_id
 * @property integer $used
 * @property integer $company_id
 * @property Company $company
 */
class EmptyAds extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'empty_ads';
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
            [['code', 'payment_code', 'node_id'], 'required'],
            [['code', 'node_id', 'used', 'company_id'], 'integer'],
            [['payment_code'], 'string', 'max' => 20],
            [['code'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'empty_ads_id' => 'Empty Ads ID',
            'code' => Yii::t('app','Code'),
            'payment_code' => Yii::t('app','Payment Code'),
            'node_id' => Yii::t('westnet','Node'),
            'used' => 'Used',
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['company_id' => 'company_id']);
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
    
    public static function maxCode()
    {
        $maxCode= (int)(new Query())
                    ->from('empty_ads')
                    ->max('code');
        
        return $maxCode;
    }

}
