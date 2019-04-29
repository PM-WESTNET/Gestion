<?php

namespace app\modules\agenda\models;

use Yii;
use app\modules\config\models\Config;
use app\modules\agenda\AgendaModule;
use app\components\db\ActiveRecord;

/**
 * This is the model class for table "task".
 *
 * @property integer $task_id
 * @property integer $task_type_id
 * @property integer $status_id
 * @property integer $parent_id
 * @property string $name
 * @property string $description
 * @property string $date
 * @property string $time
 * @property string $datetime
 * @property integer $priority
 *
 * @property Task $parent
 * @property Task[] $children
 * @property Event[] $events
 * @property Notification[] $notifications
 * @property Status $status
 * @property TaskType $taskType
 * @property User[] $users
 * @property User $creator_id
 * @property Category $category
 */
class Task extends ActiveRecord {

    const PRIORITY_LOW = '1';
    const PRIORITY_MEDIUM = '2';
    const PRIORITY_HIGH = '3';
    const PRIORITY_HIGHEST = '4';

    public $userModelClass;
    public $userModelId;
    public $assignAllUsers = false;
    public $userGroups = [];
    private $_users;
    private $_events;
    private $_added_note = false;

    public function init() {

        parent::init();

        $agendaModule = Yii::$app->getModule('agenda');

        if (isset($agendaModule->params['user']['class']))
            $this->userModelClass = $agendaModule->params['user']['class'];
        else
            $this->userModelClass = 'User';
        if (isset($agendaModule->params['user']['idAttribute']))
            $this->userModelId = $agendaModule->params['user']['idAttribute'];
        else
            $this->userModelId = 'id';

        //Seteamos valores por defecto
        $this->priority = (!empty($agendaModule->params['task']['default_priority'])) ? $agendaModule->params['task']['default_priority'] : '2';
        $this->status_id = (!empty($agendaModule->params['task']['default_status'])) ? $agendaModule->params['task']['default_status'] : '1';
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'task';
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
    public function rules() {

        if (!empty(Config::getConfig('work_hours_quantity')->value))
            $workHoursQuantity = Config::getConfig('work_hours_quantity')->value;
        else
            $workHoursQuantity = '10:00';
        //$workDayStart = Config::getConfig('work_hours_start')->value;
        //$workDayEnd = Config::getConfig('work_hours_end')->value;

        return [
            [['task_type_id', 'status_id', 'name', 'priority', 'date', 'time', 'duration'], 'required'],
            [['task_type_id', 'status_id', 'category_id', 'creator_id', 'priority'], 'integer'],
            [['description'], 'string'],
            [['date'], 'date', 'format' => 'd-m-Y'],
            //[['time'], 'compare', 'compareValue' => $workDayStart, 'operator' => '>='],
            //[['time'], 'compare', 'compareValue' => $workDayEnd, 'operator' => '<='],
            [['duration'], 'compare', 'compareValue' => $workHoursQuantity, 'operator' => '<='],
            [['date', 'time', 'datetime', 'status', 'category', 'taskType', 'users', 'events', 'assignAllUsers', 'userGroups'], 'safe'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'task_id' => \app\modules\agenda\AgendaModule::t('app', 'ID'),
            'task_type_id' => \app\modules\agenda\AgendaModule::t('app', 'Task type'),
            'taskType' => \app\modules\agenda\AgendaModule::t('app', 'Task type'),
            'status_id' => \app\modules\agenda\AgendaModule::t('app', 'Status'),
            'category_id' => \app\modules\agenda\AgendaModule::t('app', 'Category'),
            'creator_id' => \app\modules\agenda\AgendaModule::t('app', 'Creator'),
            'name' => \app\modules\agenda\AgendaModule::t('app', 'Name'),
            'description' => \app\modules\agenda\AgendaModule::t('app', 'Description'),
            'date' => \app\modules\agenda\AgendaModule::t('app', 'Date'),
            'time' => \app\modules\agenda\AgendaModule::t('app', 'Time'),
            'datetime' => \app\modules\agenda\AgendaModule::t('app', 'Datetime'),
            'priority' => \app\modules\agenda\AgendaModule::t('app', 'Priority'),
            'duration' => \app\modules\agenda\AgendaModule::t('app', 'Duration'),
            'events' => \app\modules\agenda\AgendaModule::t('app', 'Events'),
            'notifications' => \app\modules\agenda\AgendaModule::t('app', 'Notifications'),
            'status' => \app\modules\agenda\AgendaModule::t('app', 'Status'),
            'assignAllUsers' => \app\modules\agenda\AgendaModule::t('app', 'Assign all users'),
            'userGroups' => \app\modules\agenda\AgendaModule::t('app', 'User groups'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents() {
        return $this->hasMany(Event::className(), ['task_id' => 'task_id'])
                        ->orderBy(['datetime' => SORT_DESC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildren() {
        return $this->hasMany(Task::className(), ['parent_id' => 'task_id'])
                        ->orderBy(['date' => SORT_ASC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotifications() {
        return $this->hasMany(Notification::className(), ['task_id' => 'task_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus() {
        return $this->hasOne(Status::className(), ['status_id' => 'status_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory() {
        return $this->hasOne(Category::className(), ['category_id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator() {
        $userModel = $this->userModelClass;
        $userPK = $this->userModelId;
        return $this->hasOne($userModel::className(), [$userPK => 'creator_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent() {
        return $this->hasOne(Task::className(), ['task_id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskType() {
        return $this->hasOne(TaskType::className(), ['task_type_id' => 'task_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers() {
        $userModel = $this->userModelClass;
        $userPK = $this->userModelId;
        return $this->hasMany($userModel::className(), [$userPK => 'user_id'])->viaTable('notification', ['task_id' => 'task_id']);
    }

    /**
     * Busca los usuarios a asignar si es para todos o por grupos
     * @return type
     */
    protected function fetchUsers($users = []) {

        if (empty($users) || !is_array($users)) {
            $users = [];
        } else {
            $users = array_filter($users);
        }

        //Si viene seleccionado seleccionar todos los usuarios, los buscamos
        if ($this->assignAllUsers) {
            $userClass = $this->userModelClass;
            $allUsers = $userClass::findAll([
                        'status' => $userClass::STATUS_ACTIVE
            ]);
            $users = \yii\helpers\ArrayHelper::map($allUsers, 'id', 'id');
        }

        //Si vienen grupos de usuarios, buscamos sus integrantes
        if (!empty($this->userGroups)) {
            foreach ($this->userGroups as $userGroupId) {
                $userGroup = UserGroup::findOne($userGroupId);
                $users = array_merge($users, \yii\helpers\ArrayHelper::map($userGroup->users, 'id', 'id'));
            }
            $users = array_unique($users);
        }

        return $users;
    }

    /**
     * @param array $users
     */
    public function setUsers($users) {

        //Obtenemos los usuarios a asignar
        $this->_users = $this->fetchUsers($users);

        $saveUsers = function($event) {

            $userModel = $this->userModelClass;
            $userPK = $this->userModelId;

            switch ($this->taskType->slug) {

                //Tarea global
                case TaskType::TYPE_GLOBAL :
                    $this->unlinkAll('users', true);
                    foreach ($this->_users as $id) {
                        $this->link('users', $userModel::findOne($id), [
                            'status' => Notification::STATUS_READ,
                            'datetime' => $this->datetime,
                            'reason' => EventType::EVENT_TASK_UPDATED,
                            'show' => true,
                        ]);
                    }
                    break;

                //Si la tarea es por usuarios, creamos una notificación por cada usuario
                case TaskType::TYPE_BY_USER :
                    $this->unlinkAll('users', true);
                    foreach ($this->_users as $id) {
                        if ($this->taskType->slug == TaskType::TYPE_BY_USER && $this->parent_id > 0){
                            $visible = true;
                        }else{
                            $visible = false;
                        }
                        $this->link('users', $userModel::findOne($id), [
                            'status' => Notification::STATUS_UNREAD,
                            'datetime' => $this->datetime,
                            'reason' => EventType::EVENT_TASK_UPDATED,
                            'show' => $visible,
                        ]);
                    }
                    break;
            }
        };

        $this->on(self::EVENT_AFTER_INSERT, $saveUsers);
        $this->on(self::EVENT_AFTER_UPDATE, $saveUsers);
    }

    /**
     * @param array $events
     */
    public function setEvents($events) {

        if (empty($events)) {
            $events = [];
        }

        $this->_events = $events;

        $saveEvents = function($e) {

            if (count($this->_events) > 0)
                $this->_added_note = true;

            foreach ($this->_events as $timestamp => $note) {

                $event = new Event();
                $event->event_type_id = EventType::find()->where([
                            'slug' => EventType::EVENT_NOTE_ADDED,
                        ])->one()->event_type_id;

                $event->datetime = $timestamp;
                $event->date = date('Y-m-d', $timestamp);
                $event->time = date('H:i:s', $timestamp);
                $event->body = $note;
                $event->user_id = Yii::$app->user->id;

                $this->link('events', $event);
            }
        };

        $this->on(self::EVENT_AFTER_INSERT, $saveEvents);
        $this->on(self::EVENT_AFTER_UPDATE, $saveEvents);
    }

    /**
     * Devuelve todas las prioridades disponibles para tareas
     * @return type
     */
    public static function getPriorities() {

        return [
            self::PRIORITY_LOW => AgendaModule::t('app', 'Low priority'),
            self::PRIORITY_MEDIUM => AgendaModule::t('app', 'Medium priority'),
            self::PRIORITY_HIGH => AgendaModule::t('app', 'High priority'),
            self::PRIORITY_HIGHEST => AgendaModule::t('app', 'Highest priority'),
        ];
    }

    /**
     * Devuelve la prioridad de esta tarea
     * @return type
     */
    public function getPriority() {

        $priority = $this->getPriorities();

        return $priority[$this->priority];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {

            if ($insert && empty($this->creator_id))
                $this->creator_id = \Yii::$app->user->id;

            $this->datetime = time();

            $this->formatDatesBeforeSave();


            if ($this->hasErrors())
                return false;

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
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes) {

        parent::afterSave($insert, $changedAttributes);

        //Obtenemos la razon del update
        $reason = $this->checkReason($insert, $changedAttributes);

        switch ($this->taskType->slug) {

            //Si es tarea global, creamos notificaciones para todos los usuarios del sistema (solo si es nueva)
            case TaskType::TYPE_GLOBAL :
                if ($insert) {
                    $users = $this->_users;
                    foreach ($users as $user_id){
                        Notification::createNotification($user_id, $this, $reason);
                    }    
                }
                break;

            //Si la tarea es por usuarios, creamos una notificación por cada usuario
            case TaskType::TYPE_BY_USER :

                $notifications = $this->notifications;
                $this->unlinkAll('users', true);
                foreach ($notifications as $notification){
                    Notification::createNotification($notification->user_id, $this, $reason, $notification->is_expired_reminder);
                }
                if (!$insert){
                    $this->deleteChildren();
                }    
                $this->createChildren();

                break;
        }
    }

    /**
     * Verifica la razon por la cual se debe crear una notificacion
     */
    private function checkReason($isNewRecord = true, $changedAttributes = []) {

        $reason = null;

        //Si es una nueva tarea
        if ($isNewRecord)
            $reason = EventType::EVENT_TASK_CREATED;

        //Si se agrego una nota
        if (!$isNewRecord && $this->_added_note)
            $reason = EventType::EVENT_NOTE_ADDED;

        //Si es un cambio de fecha o en la hora
        if (!$isNewRecord && ((isset($changedAttributes['date']) && $changedAttributes['date'] != $this->date) || (isset($changedAttributes['time']) && $changedAttributes['time'] != date('H:i:s', strtotime($this->time)))))
            $reason = EventType::EVENT_DATE_CHANGED;

        //Si es un cambio de prioridad
        if (!$isNewRecord && isset($changedAttributes['priority']) && $changedAttributes['priority'] != $this->priority)
            $reason = EventType::EVENT_PRIORITY_CHANGED;

        //Si es un cambio de estado
        if (!$isNewRecord && isset($changedAttributes['status_id']) && $changedAttributes['status_id'] != $this->status_id) {
            $reason = EventType::EVENT_STATUS_CHANGED;
        }

        return $reason;
    }

    /**
     * Crea tareas hijas para esta tarea. Las replica y les asigna un único usuario
     */
    private function createChildren() {

        if ($this->parent_id == null && count($this->_users) > 0) {

            foreach ($this->_users as $user_id) {

                $userAssigned = [$user_id];

                $childTask = new Task();
                $childTask->attributes = $this->attributes;
                $childTask->parent_id = $this->task_id;
                $childTask->creator_id = $this->creator_id;
                $childTask->setUsers($userAssigned);
                $childTask->setEvents($this->_events);
                $childTask->save(false);
            }
        }
    }

    /**
     * Sets this task's status to "completed"
     */
    public function completeTask() {

        $statusCompleted = Status::find()->where([
                    'slug' => Status::STATUS_COMPLETED
                ])->one();

        $this->status_id = $statusCompleted->status_id;
        $this->save(false);
    }

    /**
     * Indica si una tarea es un parent o no
     * @return boolean
     */
    public function isParent() {

        if (empty($this->parent_id)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Indica si esta tarea es child o no
     * @return boolean
     */
    public function isChild() {

        if ($this->taskType->slug == TaskType::TYPE_BY_USER && !$this->isParent()){
            return true;
        }else{
            return false;
        }
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
        try {
            $this->date = Yii::$app->formatter->asDate($this->date, 'yyyy-MM-dd');
        } catch (yii\base\InvalidParamException $e) {
            $this->addError('date', \app\modules\agenda\AgendaModule::t('app', 'Invalid date format'));
        }
    }

    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable() {
        return true;
    }

    /**
     * Elimina todas las tareas hijas (desencadea unlinkWeakRelations de cada una de las task hijas
     */
    private function deleteChildren() {

        if (!empty($this->children)) {

            foreach ($this->children as $childTask)
                $childTask->delete();
        }
    }

    /**
     * Busca todas las tareas de un usuario y las marca como vencidas (crea nuevas notificaciones)
     * @param integer $user_id
     * @return integer $task_count
     */
    public static function markAllAsExpired($user_id) {

        //Completed status
        $status_complete_id = Status::find()->
                        where(['slug' => Status::STATUS_COMPLETED])
                        ->one()
                ->status_id;

        //Global type
        $type_global_id = TaskType::find()->
                        where(['slug' => \app\modules\agenda\models\TaskType::TYPE_GLOBAL])
                        ->one()
                ->task_type_id;

        $tasks = self::find()
                ->joinWith('notifications', false)
                //Tareas asignadas a este usuario de cualquier tipo
                ->where([
                    'user_id' => $user_id,
                    'show' => true,
                ])
                //Tareas globales actualizadas
                ->orWhere([
                    'show' => false,
                    'task_type_id' => $type_global_id
                ])
                //Tareas creadas por este usuario que sean globales
                ->orWhere([
                    'creator_id' => $user_id,
                ])
                //Que esten vencidas y no esten completadas, y no sean asignaciones por aviso de vencimiento
                ->andWhere(['<', 'date', date('Y-m-d')])
                ->andWhere(['<>', 'status_id', $status_complete_id])
                ->andWhere(['>', 'parent_id', 0])
                ->andWhere(['is_expired_reminder' => false])
                ->all();

        if (!empty($tasks)) {
            foreach ($tasks as $task) {
                $task->markAsExpired($user_id);
            }
            return count($tasks);
        } else
            return 0;
    }

    /**
     * @brieft Marca esta tarea como vencida
     * @param integer $user_id
     */
    protected function markAsExpired($user_id) {

        $assignedUsers = [];

        //Modificamos notificaciones creadas y marcamos como vencidas y no leidas
        if (!empty($this->notifications)) {

            //Eliminamos las notificaciones de vencimiento actuales
            //Notification::deleteAll("task_id = $this->task_id AND is_expired_reminder = 1");

            foreach ($this->notifications as $notification) {
                $assignedUsers[] = $notification->user_id;
                $notification->datetime = time();
                $notification->status = Notification::STATUS_UNREAD;
                $notification->reason = EventType::EVENT_TASK_EXPIRED;
                $notification->save(false);
            }
        }

        //Si el creador NO es un usuario asignado, debemos crear una notificación extra solo para ese usuario
        if (!in_array($user_id, $assignedUsers)) {
            Notification::createNotification($user_id, $this, EventType::EVENT_TASK_EXPIRED, true);
        }
    }

    /**
     * Indica si el usuario tiene relacion con la tarea o no
     * @param int $user_id (ID del usuario)
     * @return boolean (True si existe notificacion, False si no existe)
     * @throws \yii\web\BadRequestHttpException
     */
    public function isAssignedUser($user_id = 0) {

        //Solo buscamos si tenemos task_id y user_id
        if ($user_id > 0) {

            //Buscamos notificacion para el usuario en la tarea
            $notification = Notification::find()->where([
                        'task_id' => $this->task_id,
                        'user_id' => $user_id
                    ])->one();

            //Si existe una relacion entre el usuario y la tarea, devolvemos true
            if (!empty($notification) && $notification->task_id == $this->task_id)
                return true;
            //Si no existe, devolvemos false
            else
                return false;
        } else {
            throw new \yii\web\BadRequestHttpException("[AgendaAPI] UserID not given.", 403);
        }
    }

    /**
     * Indica si una tarea, segun sus atributos, se puede posponer o no
     * @return boolean
     */
    public function isPostponable() {

        if (!$this->isChild())
            return false;

        if ($this->taskType->slug == TaskType::TYPE_GLOBAL)
            return false;

        return true;
    }

    /**
     * Obtiene tareas segun criterio de búsqueda
     * @param \yii\data\ActiveDataProvider $activeRecord
     * @param TaskSearch $searchModel
     * @return array()
     */
    public static function getFilteredTasks($activeRecord, $searchModel) {

        $taskQuery = \app\modules\agenda\models\Task::find()
                ->joinWith('notifications', false)
                ->Where($activeRecord->query->where);

        if (empty($searchModel->creator_id)) {
            $taskQuery->andWhere(['<>', 'creator_id', \Yii::$app->user->id]);
        }

        if (!empty($searchModel->user_id)){
            $taskQuery->andWhere([
                'user_id' => $searchModel->user_id,
            ]);
            
        }else{
            $taskQuery->andWhere(['<>', 'user_id', \Yii::$app->user->id]);           
            $taskQuery->andWhere(['is_expired_reminder' => false]);
        } 
        /*
          $taskTypeByUser = \app\modules\agenda\models\TaskType::find()->where([
          'slug' => \app\modules\agenda\models\TaskType::TYPE_BY_USER
          ])->one();
          $taskTypeGlobal = \app\modules\agenda\models\TaskType::find()->where([
          'slug' => \app\modules\agenda\models\TaskType::TYPE_BY_USER
          ])->one();
         */

        $tasks = $taskQuery->all();

        return $tasks;
    }

    /**
     * Devuelve todos los eventos de un usuario
     * @param User $user
     * @return Task
     */
    public static function getAllTasks($user) {
        return \app\modules\agenda\models\Task::find()
                        ->joinWith('notifications', false)
                        ->joinWith('taskType', false)
                        //Tareas asignadas a este usuario de cualquier tipo
                        ->where([
                            'user_id' => $user->id,
                            'show' => true
                        ])
                        //Tareas globales actualizadas
                        ->orWhere([
                            'show' => false,
                            'taskType.slug' => \app\modules\agenda\models\TaskType::TYPE_GLOBAL
                        ])
                        //Tareas creadas por este usuario que sean por usuario
                        ->orWhere([
                            'creator_id' => $user->id,
                            'parent_id' => null,
                            'taskType.slug' => \app\modules\agenda\models\TaskType::TYPE_BY_USER
                        ])
                        //Tareas creadas por este usuario que sean globales
                        ->orWhere([
                            'creator_id' => $user->id,
                            //'show' => false,
                            'taskType.slug' => \app\modules\agenda\models\TaskType::TYPE_GLOBAL
                        ])
                        ->andWhere([
                            'is_expired_reminder' => false
                        ])
                        ->all();
    }

    /**
     * Deletes weak relations for this model on delete
     * Weak relations: Events, Notifications, Status, TaskType.
     */
    protected function unlinkWeakRelations() {
        $this->unlinkAll('notifications', true);
        $this->unlinkAll('events', true);
        $this->unlinkAll('users', true);
        if (!empty($this->children))
            foreach ($this->children as $childTask)
                $childTask->delete();
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

    public function getVisualPriority() {
        $visual_priority = '';
        for ($i = 0; $i <= $this->priority; $i++) {
            $visual_priority .= '*';
        }
        return $visual_priority;
    }

}
