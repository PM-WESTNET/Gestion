<?php

namespace app\modules\ticket\models;

use Yii;

/**
 * This is the model class for table "ticket.schema_has_status".
 *
 * @property int $status_has_action_id
 * @property int $action_id
 * @property int $status_id
 * @property string $text_1
 * @property string $text_2
 * @property int $task_type_id
 */

class StatusHasAction extends \app\components\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'status_has_action';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('dbticket');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status_id'], 'required'],
            [['task_type_id', 'ticket_category_id', 'task_category_id', 'ticket_status_id', 'task_status_id', 'task_priority', 'action_id'], 'integer'],
            [['text_2', 'text_1'], 'string'],
            [['task_time'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'action_id' => Yii::t('app', 'Action'),
            'status_id' => Yii::t('app', 'Status'),
            'text_1' => Yii::t('app', 'Title'),
            'text_2' => Yii::t('app', 'Content'),
            'task_type_id' => Yii::t('app', 'Task: Type'),
            'ticket_category_id' => Yii::t('app', 'Ticket: Category'),
            'task_category_id' => Yii::t('app', 'Task: Category'),
            'ticket_status_id' => Yii::t('app', 'Ticket: Status'),
            'task_status_id' => Yii::t('app', 'Task: Status'),
            'task_priority' => Yii::t('app','Task: priority'),
            'task_time' => Yii::t('app', 'Task: Time'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAction()
    {
        return $this->hasOne(Action::class, ['action_id' => 'action_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskCategory()
    {
        return $this->hasOne(\app\modules\agenda\models\Category::class, ['category_id' => 'task_category_id']);
    }
}
