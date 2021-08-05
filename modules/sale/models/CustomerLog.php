<?php

namespace app\modules\sale\models;

use app\components\db\ActiveRecord;
use DateTime;
use webvimark\modules\UserManagement\models\User;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "customer_log".
 *
 * @property integer $customer_log_id
 * @property string $action
 * @property string $before_value
 * @property string $new_value
 * @property DateTime $date
 * @property integer $customer_id
 * @property integer $user_id
 * @property string $observations
 * @property integer $object_id
 * @property string $class_name 
 *
 * @property Customer $customer
 */
class CustomerLog extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'customer_log';
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
            [['action', 'date', 'customer_id', 'user_id', 'observations'], 'required'],
            [['date'], 'safe'],
            [['customer_id', 'user_id'], 'integer'],
            [['before_value', 'new_value'], 'string', 'max' => 45],
            [['observations'], 'string', 'max' => 300],
            [['action'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'customer_log_id' => 'Customer Log ID',
            'action' => Yii::t('app', 'Action'),
            'before_value' => Yii::t('app', 'Before Value'),
            'new_value' => Yii::t('app', 'New Value'),
            'date' => Yii::t('app', 'Date'),
            'customer_id' => 'Customer ID',
            'user_id' => Yii::t('app', 'User'),
            'observations' => Yii::t('app', 'Observations'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCustomer() {
        return $this->hasOne(Customer::className(), ['customer_id' => 'customer_id']);
    }

    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable() {
        return true;
    }

    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: Customer.
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

    public function createInsertLog($customer_id, $class_name, $object_id, $user_napear = null) {

        $this->date = (new DateTime('now'))->format('Y-m-d H:i:s');
        $this->action = 'Alta de Datos de ' . Yii::t('app', $class_name);
        $this->customer_id = $customer_id;
        if(!YII_ENV_TEST){
            $this->user_id = Yii::$app->user->identity->id;
        } else {
            $this->user_id = 1;
        }

        $this->observations = (empty($user_napear)) ? 'Alta ' . $class_name . ': ' . $object_id : 'Alta ' . $class_name . ': ' . $object_id .' - ' . $user_napear;

        $this->object_id = $object_id;
        $this->class_name = $class_name;

        $this->save(false);
    }

    public function createUpdateLog($customer_id, $attributeChanged, $oldValue, $newValue, $class_name, $object_id, $observations = null) {

        $this->date = (new DateTime('now'))->format('Y-m-d H:i:s');
        $this->action = 'Actualizacion de Datos de ' . Yii::t('app', $class_name) . ': ' . $attributeChanged;
        $this->customer_id = $customer_id;
        if ( YII_ENV_TEST || Yii::$app instanceof \yii\console\Application || (Yii::$app->controller->module->id === 'mobileapp' || Yii::$app->controller->module->id === 'v1' )) {
            $this->user_id = User::findOne(['username'=>'superadmin'])->id;
        } else {
            $this->user_id = (Yii::$app->user && Yii::$app->user->identity)?Yii::$app->user->identity->id:0;
        }

        $this->before_value = $oldValue;
        $this->new_value = $newValue;
        $this->object_id = $object_id;
        $this->class_name = $class_name;
        $this->observations = $observations;

        $this->save(false);
    }

    public function createChangeStateLog($customer_id, $attributeChanged, $oldValue, $newValue, $class_name, $object_id) {
        $this->date = (new DateTime('now'))->format('Y-m-d H:i:s');
        $this->action = 'Actualizacion de Datos de ' . Yii::t('app', $class_name) . ': ' . $attributeChanged;
        $this->customer_id = $customer_id;
        $this->user_id = Yii::$app->user->identity->user_id;
        $this->before_value = $oldValue;
        $this->new_value = $newValue;
        $this->object_id = $object_id;
        $this->class_name = $class_name;

        $this->save(false);
    }

    public function afterFind() {
        $this->formatDatesAfterFind();
        parent::afterFind();
    }

    /**
     * @brief Format dates using formatter local configuration
     */
    private function formatDatesAfterFind() {


        $this->date = Yii::$app->formatter->asDate($this->date, 'd-M-Y H:m:s');
    }

    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave() {
        if (empty($this->to_date) || $this->to_date == Yii::t('app', 'Undetermined time')) {
            $this->to_date = '';
        } else {
            $this->to_date = Yii::$app->formatter->asDate($this->to_date, 'yyyy-MM-dd');
        }
        if (empty($this->from_date)) {
            $this->from_date = '';
        } else {
            $this->from_date = Yii::$app->formatter->asDate($this->from_date, 'yyyy-MM-dd');
        }
        $this->date = Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd');
    }

}
