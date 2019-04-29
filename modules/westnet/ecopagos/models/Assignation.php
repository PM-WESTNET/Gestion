<?php

namespace app\modules\westnet\ecopagos\models;

use Yii;

/**
 * This is the model class for table "assignation".
 *
 * @property integer $ecopago_id
 * @property integer $collector_id
 * @property string $date
 * @property string $time
 * @property integer $datetime
 *
 * @property Collector $collector
 * @property Ecopago $ecopago
 */
class Assignation extends \app\components\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'assignation';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('dbecopago');
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
    public function rules() {
        return [
            [['ecopago_id', 'collector_id', 'date', 'time', 'datetime'], 'required'],
            [['ecopago_id', 'collector_id', 'datetime'], 'integer'],
            [['date', 'time', 'collector', 'ecopago'], 'safe'],
//            [['date'], 'date']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'ecopago_id' => Yii::t('app', 'Ecopago ID'),
            'collector_id' => Yii::t('app', 'Collector ID'),
            'date' => Yii::t('app', 'Date'),
            'time' => Yii::t('app', 'Time'),
            'datetime' => Yii::t('app', 'Datetime'),
            'collector' => Yii::t('app', 'Collector'),
            'ecopago' => Yii::t('app', 'Ecopago'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCollector() {
        return $this->hasOne(Collector::className(), ['collector_id' => 'collector_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEcopago() {
        return $this->hasOne(Ecopago::className(), ['ecopago_id' => 'ecopago_id']);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
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
    public function afterFind() {
        $this->formatDatesAfterFind();
        parent::afterFind();
    }

    /**
     * Format dates using formatter local configuration
     */
    private function formatDatesAfterFind() {
        $this->date = Yii::$app->formatter->asDate($this->date);
    }

    /**
     * Format dates as database requieres it
     */
    private function formatDatesBeforeSave() {
        $this->date = Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd');
    }

    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable() {
        return true;
    }

    /**
     * Deletes weak relations for this model on delete
     * Weak relations: Collector, Ecopago.
     */
    protected function unlinkWeakRelations() {
        
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete() {
        if (parent::beforeDelete()) {
            if ($this->getDeletable()) {
                $this->unlinkWeakRelations();
                return true;
            }
        } else {
            return false;
        }
    }

}
