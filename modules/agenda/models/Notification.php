<?php

namespace app\modules\agenda\models;

use Yii;

/**
 * This is the model class for table "notification".
 *
 * @property integer $notification_id
 * @property integer $user_id
 * @property integer $task_id
 * @property string $datetime
 * @property string $status
 * @property string $reason
 * @property integer $show
 * @property integer $is_expired_reminder
 *
 * @property Task $task
 */
class Notification extends \app\components\db\ActiveRecord {

    const STATUS_UNREAD = 'unread';
    const STATUS_READ = 'read';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'notification';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('dbagenda');
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
            [['user_id', 'task_id'], 'required'],
            [['user_id', 'task_id'], 'integer'],
            [['status', 'reason'], 'string'],
            [['task', 'reason'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'notification_id' => \app\modules\agenda\AgendaModule::t('app', 'ID'),
            'user_id' => \app\modules\agenda\AgendaModule::t('app', 'User  ID'),
            'task_id' => \app\modules\agenda\AgendaModule::t('app', 'Task ID'),
            'datetime' => \app\modules\agenda\AgendaModule::t('app', 'Date'),
            'status' => \app\modules\agenda\AgendaModule::t('app', 'Status'),
            'task' => \app\modules\agenda\AgendaModule::t('app', 'Task'),
            'reason' => \app\modules\agenda\AgendaModule::t('app', 'Reason'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTask() {
        return $this->hasOne(Task::className(), ['task_id' => 'task_id']);
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
     * Weak relations: Task.
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

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {

            //Solo seteamos datetime si es una nueva notificacion
            if ($insert) {
                $this->datetime = time();
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @brief Crea notificaciones a todos los usuarios activos del sistema para una tarea dada
     * @param \app\modules\agenda\models\Task $task
     */
    public static function createGlobalNotifications(\app\modules\agenda\models\Task $task, $reason) {

        //Obtenemos todos los usuarios activos
        $users = \webvimark\modules\UserManagement\models\User::findAll([
                    'status' => \webvimark\modules\UserManagement\models\User::STATUS_ACTIVE
        ]);

        //Creamos notificaciones para cada usuario, para esta tarea
        if (!empty($users)) {
            foreach ($users as $user) {
                self::createNotification($user, $task, $reason);
            }
        }
    }

    /**
     * @brief Crea una notificacion para un usuario y una tarea especifica y la guarda
     * @param integer $user_id
     * @param Task $task
     * @return \self
     */
    public static function createNotification($user_id, $task, $reason, $is_expired_reminder = false) {

        $notification = new self;
        $notification->user_id = $user_id;
        $notification->task_id = $task->task_id;
        $notification->datetime = time();
        $notification->status = self::STATUS_UNREAD;
        $notification->reason = $reason;
        if ($task->taskType->slug == TaskType::TYPE_GLOBAL || $task->parent_id > 0)
            $notification->show = true;
        else
            $notification->show = false;

        if ($is_expired_reminder) {
            $notification->is_expired_reminder = $is_expired_reminder;
            $notification->show = true;
        }

        if ($notification->validate()) {
            $notification->save();
        }

        return $notification;
    }

}
