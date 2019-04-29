<?php

namespace app\modules\westnet\models;

use Yii;

/**
 * This is the model class for table "debt_evolution".
 *
 * @property integer $debt_evolution_id
 * @property string $period
 * @property integer $invoice_1
 * @property integer $invoice_2
 * @property integer $invoice_3
 * @property integer $invoice_4
 * @property integer $invoice_5
 * @property integer $invoice_6
 * @property integer $invoice_7
 * @property integer $invoice_8
 * @property integer $invoice_9
 * @property integer $invoice_10
 * @property integer $invoice_x
 */
class DebtEvolution extends \app\components\db\ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'debt_evolution';
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
            [['period'], 'safe'],
            [['period'], 'date'],
            [['invoice_1', 'invoice_2', 'invoice_3', 'invoice_4', 'invoice_5', 'invoice_6', 'invoice_7', 'invoice_8', 'invoice_9', 'invoice_10', 'invoice_x'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'debt_evolution_id' => Yii::t('app', 'Debt Evolution ID'),
            'period' => Yii::t('app', 'Period'),
            'invoice_1' => Yii::t('app', 'Invoice 1'),
            'invoice_2' => Yii::t('app', 'Invoice 2'),
            'invoice_3' => Yii::t('app', 'Invoice 3'),
            'invoice_4' => Yii::t('app', 'Invoice 4'),
            'invoice_5' => Yii::t('app', 'Invoice 5'),
            'invoice_6' => Yii::t('app', 'Invoice 6'),
            'invoice_7' => Yii::t('app', 'Invoice 7'),
            'invoice_8' => Yii::t('app', 'Invoice 8'),
            'invoice_9' => Yii::t('app', 'Invoice 9'),
            'invoice_10' => Yii::t('app', 'Invoice 10'),
            'invoice_x' => Yii::t('app', 'Invoice X'),
        ];
    }    

    
    /**
     * @inheritdoc
     */
     
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {            
            $this->formatDatesBeforeSave();            
            return true;
        } else {
            return false;
        }     
    }
    
    /**
     * @inheritdoc
     */
    public function afterFind()
    {        
        $this->formatDatesAfterFind();
        parent::afterFind();
    }
     
    /**
     * @brief Format dates using formatter local configuration
     */
    private function formatDatesAfterFind()
    {
            $this->period = Yii::$app->formatter->asDate($this->period);
        }
     
    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave()
    {
            $this->period = Yii::$app->formatter->asDate($this->period, 'yyyy-MM-dd');
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
