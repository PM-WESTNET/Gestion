<?php

namespace app\modules\ticket\models;

use app\modules\config\models\Config;
use app\modules\sale\modules\contract\models\Contract;
use app\modules\ticket\behaviors\GenerateActionBehavior;
use app\modules\ticket\components\MesaTicket;
use webvimark\modules\UserManagement\models\User;
use Yii;
use app\modules\ticket\components\TicketId;
use app\modules\sale\models\Customer;
use yii\httpclient\Client;
use app\modules\ticket\TicketModule;
use app\modules\ticket\models\query\TicketQuery;
use app\modules\agenda\models\Task;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ticket".
 *
 * @property integer $ticket_id
 * @property integer $status_id
 * @property integer $customer_id
 * @property integer $task_id
 * @property integer $color_id
 * @property integer $category_id
 * @property string $start_date
 * @property string $finish_date
 * @property integer $start_datetime
 * @property integer $update_datetime
 * @property string $title
 * @property string $content
 * @property integer $number
 * @property integer $user_id
 * @property integer $contract_id
 * @property integer $external_tag_id
 *
 * @property Assignation[] $assignations
 * @property Observation[] $observations
 * @property Status $status
 * @property Task $task
 * @property Color $color
 * @property User[] $users
 * @property Customer $customer
 * @property Category $category
 * @property Task[] $tasks
 * @property History[] $completeHistory
 * @property User $user
 * @property Contract $contract
 * @property boolean $discounted
 */
class Ticket extends \app\components\db\ActiveRecord {

    public $userModelClass;
    public $userModelId;
    public $assignAllUsers = false;
    public $userGroups = [];
    public $assigned_user;
    private $_users;
    private $_observations;
    //Stores old attributes so we can compare against them before save
    protected $oldAttributes;
    protected $ticketIdentificator;
    //Sets this ticket prepared for an external change, making beforeSave and afterSave less functional
    private $_isExternal = false;

    //Fecha que debe ser seteada si el estado del ticket genera una tarea.
    public $task_date;
    /**
     * @inheritdoc
     */
    public function init() {

        parent::init();

        $ticketModule = Yii::$app->getModule('ticket');

        if (isset($ticketModule->params['user']['class']))
            $this->userModelClass = $ticketModule->params['user']['class'];
        else
            $this->userModelClass = 'User';
        if (isset($ticketModule->params['user']['idAttribute']))
            $this->userModelId = $ticketModule->params['user']['idAttribute'];
        else
            $this->userModelId = 'id';

        $this->ticketIdentificator = new TicketId();

        //Seteamos valores por defecto
        $this->status_id = (!empty($ticketModule->params['ticket']['default_status'])) ? $ticketModule->params['ticket']['default_status'] : '1';
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'ticket';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('dbticket');
    }

    /**
     * @inheritdoc
     *      GeneratedActionBehavior Analiza si el ticket ha cambiado de estado, si ha cambiado, verifica que el cambio de estado
     * desencadene o no una acción, por ejemplo, la creación de un ticket o una tarea.
     */
    public function behaviors() {
        $behaviors = parent::getBehaviors();

        $behaviors[] = GenerateActionBehavior::class;
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['status_id', 'customer_id', 'title', 'content', 'category_id'], 'required'],
            [['status_id', 'customer_id', 'task_id', 'color_id', 'category_id', 'number', 'start_datetime', 'user_id', 'contract_id', 'external_tag_id'], 'integer'],
            [['start_date', 'finish_date'], 'date'],
            [['content'], 'string'],
            [['discounted'], 'boolean'],
            [['title'], 'string', 'max' => 255],
            [['user_id', 'start_date', 'start_datetime', 'update_datetime', 'finish_date', 'status', 'users', 'observations', 'category', 'task', 'task_id', 'category_id', 'user', 'contract', 'task_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'ticket_id' => TicketModule::t('app', 'Ticket'),
            'status_id' => TicketModule::t('app', 'Status'),
            'customer_id' => TicketModule::t('app', 'Customer'),
            'task_id' => TicketModule::t('app', 'Task'),
            'color_id' => TicketModule::t('app', 'Color'),
            'category_id' => TicketModule::t('app', 'Category'),
            'start_date' => TicketModule::t('app', 'Instalation date'),
            'finish_date' => TicketModule::t('app', 'Finish date'),
            'title' => TicketModule::t('app', 'Title'),
            'content' => TicketModule::t('app', 'Content'),
            'number' => TicketModule::t('app', 'Number'),
            'assignations' => TicketModule::t('app', 'Assignations'),
            'observations' => TicketModule::t('app', 'Observations'),
            'status' => TicketModule::t('app', 'Status'),
            'task' => TicketModule::t('app', 'Task'),
            'category' => TicketModule::t('app', 'Category'),
            'contract_id' => TicketModule::t('app', 'Contract'),
            'user_id' => TicketModule::t('app', 'User'),
            'external_tag_id' => TicketModule::t('app', 'Tag'),
            'task_date' => TicketModule::t('app', 'Task date'),
            'discounted' => TicketModule::t('app', 'Discounted'),
        ];
    }

    /**
     * @inheritdoc
     * @return \app\modules\ticket\models\query\TicketQuery the active query used by this AR class.
     */
    public static function find() {
        return new TicketQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssignations() {
        return $this->hasMany(Assignation::class, ['ticket_id' => 'ticket_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompleteHistory() {
        return $this->hasMany(History::class, ['ticket_id' => 'ticket_id'])
                        ->orderBy(['datetime' => SORT_DESC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getObservations() {
        return $this->hasMany(Observation::class, ['ticket_id' => 'ticket_id'])
                        ->orderBy(['datetime' => SORT_DESC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus() {
        return $this->hasOne(Status::class, ['status_id' => 'status_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory() {
        return $this->hasOne(Category::class, ['category_id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer() {
        return $this->hasOne(Customer::class, ['customer_id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTask() {
        return $this->hasOne(Task::class, ['task_id' => 'task_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getColor() {
        return $this->hasOne(Color::class, ['color_id' => 'color_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContract() {
        return $this->hasOne(Contract::class, ['contract_id' => 'contract_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicketManagements() {
        return $this->hasMany(TicketManagement::class, ['ticket_id' => 'ticket_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers() {
        $userModel = $this->userModelClass;
        $userPK = $this->userModelId;
        return $this->hasMany($userModel::className(), [$userPK => 'user_id'])->viaTable('assignation', ['ticket_id' => 'ticket_id']);
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
            $users = ArrayHelper::map($allUsers, 'id', 'id');
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
     * @param array $observations
     */
    public function setObservations($observations) {

        if (empty($observations)) {
            $observations = [];
        }

        $this->_observations = $observations;

        $saveObservations = function($e) {

            foreach ($this->_observations as $timestamp => $note) {

                $observation = new Observation();

                $observation->datetime = $timestamp;
                $observation->date = date('Y-m-d', $timestamp);
                $observation->time = date('H:i:s', $timestamp);
                $observation->title = $note['title'];
                $observation->description = $note['content'];
                $observation->user_id = (!empty($note['user_id'])) ? $note['user_id'] : Yii::$app->user->id;

                $this->link('observations', $observation);
            }
        };

        $this->on(self::EVENT_AFTER_INSERT, $saveObservations);
        $this->on(self::EVENT_AFTER_UPDATE, $saveObservations);
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

            $this->unlinkAll('users', true);

            foreach ($this->_users as $id) {
                $this->link('users', $userModel::findOne($id), [
                    'date' => date("Y-m-d"),
                    'time' => date("H:i"),
                ]);
            }
        };

        $this->on(self::EVENT_AFTER_INSERT, $saveUsers);
        $this->on(self::EVENT_AFTER_UPDATE, $saveUsers);
    }

    /**
     * Sets a new task for this ticket
     */
    public function buildTask() {

        //We delete the current task   
        $this->deleteTask();

        $users = [];
        $userModelId = $this->userModelId;

        //Find users for the new task
        if (!empty($this->_users)) {
            $users = $this->_users;
        } else if (!empty($this->users)) {
            foreach ($this->users as $user) {
                $users[] = $user->$userModelId;
            }
        }

        if (!empty($users)) {

            //Here we create a task for this user, regarding this ticket
            $type_global_id = \app\modules\agenda\models\TaskType::find()->where([
                        'slug' => \app\modules\agenda\models\TaskType::TYPE_GLOBAL
                    ])->one()->task_type_id;
            $task = \app\modules\agenda\components\AgendaAPI::createTask([
                        'users' => $users,
                        'name' => $this->title,
                        'duration' => 1,
                        'description' => $this->content,
                        'task_type_id' => $type_global_id,
            ]);
            $this->task_id = $task->task_id;
        }
    }

    /**
     * Deletes task instance related to this ticket
     */
    public function deleteTask() {
        if (!empty($this->task))
            $this->task->delete();
    }

    /**
     * Closes the current ticket. It also sets this ticket's task to finished
     */
    public function closeTicket() {

        if ($this->statusIsActive()) {

            $statusClosed = Status::findOne([
                        'is_open' => Status::STATUS_CLOSED
            ]);

            $this->status_id = $statusClosed->status_id;
            $this->update_datetime = time();
            $this->finish_date = date("Y-m-d");

            if (!empty($this->task))
                $this->task->completeTask();

            if ($this->save(false)) {

                //Creates a history entry
                History::createHistoryEntry($this, History::TITLE_CLOSED);
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Reopens a previously closed ticket. This is like updating a ticket and setting its status to "Active"
     */
    public function reopenTicket() {

        if ($this->statusIsClosed()) {

            $statusActive = Status::find()->where([
                        'is_open' => Status::STATUS_ACTIVE
                    ])->one();

            $this->status_id = $statusActive->status_id;
            $this->update_datetime = time();
            $this->finish_date = null;

            if ($this->save(false)) {

                //Creates a history entry
                History::createHistoryEntry($this, History::TITLE_REOPENED);
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Checks whether this ticket's status is closed or not
     * @return boolean
     */
    public function statusIsClosed() {
        if (!$this->status->is_open)
            return true;
        else
            return false;
    }

    /**
     * Checks whether this ticket's status is active or not
     * @return boolean
     */
    public function statusIsActive() {
        if ($this->status->is_open)
            return true;
        else
            return false;
    }

    /**
     * Returns if a user_id is part of the assignation of this ticket
     * @param type $user_id
     * @return boolean
     */
    public function isAssignatedUser($user_id) {

        $assignatedUserIds = [];

        if (!empty($this->assignations)) {
            foreach ($this->assignations as $assignation) {
                $assignatedUserIds[] = $assignation['user_id'];
            }
        }

        if (in_array($user_id, $assignatedUserIds))
            return true;
        else
            return false;
    }

    /**
     * @inheritdoc
     */
    public function afterFind() {

        $this->oldAttributes = $this->attributes;

        $this->formatDatesAfterFind();
        parent::afterFind();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            //Values for new instances ($insert = true means $this is a new record)
            if ($insert) {
                //Asigno el usuario
                if (empty($this->user_id)) {
                    $this->user_id = (Yii::$app instanceof \yii\console\Application ? 1 : Yii::$app->user->id) ;
                }
                $this->start_datetime = time();
                //Assings a color to this ticket
                $this->assignNumber();
            } else {
                //Assings a color to this ticket
                $this->update_datetime = time();
            }

            //If it is not an external change
            if (!$this->_isExternal) {
                //Color assignation
                $this->assignColor();

                //Creates and sets a task for this ticket, only if it is not a closing ticket
                if (Status::findOne($this->status_id)->is_open)
                    $this->buildTask();
            }


            $this->formatDatesBeforeSave();
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        if (!$insert) {
            if (!empty($changedAttributes['status_id']) && $changedAttributes['status_id'] == $this->status_id) {
                //Creates a new history entry for this ticket, being updated
                History::createHistoryEntry($this, History::TITLE_UPDATED);
            }
            if ($this->category->notify === 1) {
                MesaTicket::updateTicket($this);
            }
        } else {
            //Creates a new history entry for this ticket, being created
            History::createHistoryEntry($this, History::TITLE_CREATED);
            if ($this->category->notify === 1) {
                MesaTicket::createTicket($this);
            }
        }
    }

    /**
     * Assigns a color to this ticket. This depends of customer_id and quantity of tickets for that customer_id
     */
    private function assignColor() {
        $color = $this->ticketIdentificator->getColorByObservations($this->observations);
        if (!empty($color)) {
            $this->color_id = $color->color_id;
        }
    }

    /**
     * Externally refresh the color of this ticket
     */
    public function externalColorAssignation() {

        $this->_isExternal = true;
        $this->assignColor();
        $this->save(true, [
            'color_id'
        ]);
    }

    /**
     * Assigns a color to this ticket. This depends of customer_id and quantity of tickets for that customer_id
     */
    private function assignNumber() {
        $this->number = $this->ticketIdentificator->assignNumber($this->customer_id);
    }

    /**
     * Returns an array with assigned usernames
     * @return type
     */
    public function fetchAssignations() {

        $assignations = [];
        if (!empty($this->assignations)) {
            foreach ($this->assignations as $assignation) {
                $user = $assignation->user;

                if ($user) {
                    $assignations[] = $assignation->user->username;
                }
            }
        }

        return $assignations;
    }

    /**
     * Returns the last history that closed this ticket
     * @return type
     */
    public function getLastHistoryClosed() {

        $labels = History::titleLabels();

        return History::find()->where([
                    'ticket_id' => $this->ticket_id,
                    'title' => $labels[History::TITLE_CLOSED]
                ])->orderBy([
                    'datetime' => SORT_DESC
                ])->one();
    }

    /**
     * Returns the last history that opened this ticket
     * @return type
     */
    public function getLastHistoryOpen() {

        $labels = History::titleLabels();

        return History::find()->where([
                    'ticket_id' => $this->ticket_id,
                    'title' => $labels[History::TITLE_CREATED]
                ])->orWhere([
                    'ticket_id' => $this->ticket_id,
                    'title' => $labels[History::TITLE_REOPENED]
                ])->orderBy([
                    'datetime' => SORT_DESC
                ])->one();
    }

    /**
     * Format dates using formatter local configuration
     */
    private function formatDatesAfterFind() {
        if (!empty($this->start_date))
            $this->start_date = Yii::$app->formatter->asDate($this->start_date);
        if (!empty($this->finish_date))
            $this->finish_date = Yii::$app->formatter->asDate($this->finish_date);
    }

    /**
     * Format dates as database requieres it
     */
    private function formatDatesBeforeSave() {

        if (!empty($this->start_date))
            $this->start_date = Yii::$app->formatter->asDate($this->start_date, 'yyyy-MM-dd');
        else
            $this->start_date = date("Y-m-d");

        if (!empty($this->finish_date))
            $this->finish_date = Yii::$app->formatter->asDate($this->finish_date, 'yyyy-MM-dd');
    }

    /**
     * @inheritdoc
     * Strong relations: Assignations, Observations.
     */
    public function getDeletable() {
        return true;
    }

    /**
     * Deletes weak relations for this model on delete
     * Weak relations: Events, Notifications, Status, TaskType.
     */
    protected function unlinkWeakRelations() {
        $this->deleteTask();
        $this->unlinkAll('assignations', true);
        $this->unlinkAll('observations', true);
        $this->unlinkAll('completeHistory', true);
        $this->unlinkAll('ticketManagements', true);
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
     * Retorna las etiquetas para ser listadas en los selects
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getTagsForSelect()
    {

        $client = new Client();
        $response = $client->createRequest()
                ->setMethod('GET')
                ->setUrl('https://mesa.bigway.com.ar/gestion/etiquetas')
                ->setHeaders(['content-type' => 'application/x-www-form-urlencoded'])
                ->send();
        return json_decode($response->content);
    }

    /**
     * Retorna el id de la etiqueta requerida
     *
     * @return integer
     */
    public static function getTagByName($tag_name)
    {

        $client = new Client();
        $tag_id = '';
        $response = $client->createRequest()
                ->setMethod('GET')
                ->setUrl('https://mesa.bigway.com.ar/gestion/etiquetas')
                ->setHeaders(['content-type' => 'application/x-www-form-urlencoded'])
                ->send();

        foreach (json_decode($response->content) as $tag) {
            if ($tag->etiqueta == $tag_name) {
                $tag_id = $tag->id;
            }
        }
        return $tag_id;
    }

    /**
     * @param $ticket_id
     * @param $user_id
     * @return bool
     * Asigna el ticket dado al usuario indicado.
     */
    public static function assignTicketToUser($ticket_id, $user_id) {
        $assignation = new Assignation([
            'date' => (new \DateTime('now'))->format('Y-m-d'),
            'time' => (new \DateTime('now'))->format('H:m:i'),
            'user_id' => $user_id,
            'ticket_id' => $ticket_id,
            'external_id' => null
        ]);
        History::createHistoryEntry(Ticket::findOne($ticket_id), History::TITLE_NEW_ASSIGNATION);

        return $assignation->save();
    }

    /**
     * @param $user_id
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     * Elimina las asignaciones de un ticket a un usuario
     */
    public function deleteAssignedUser($user_id) {
        $assignations = Assignation::find()->where(['ticket_id' => $this->ticket_id, 'user_id' => $user_id])->all();
        foreach ($assignations as $assignation) {
            $assignation->delete();
        }

        History::createHistoryEntry($this, History::TITLE_DELETE_ASSIGNATION);
    }

    /**
     * @param $ticket_id
     * @param $exclude_users_id
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     * Elimina todas las asignaciones de un ticket dando la posiblidad de excluir a ciertas asignaciones de usuarios
     */
    public static function deleteAllAssignations($ticket_id, $exclude_users_id = [])
    {
        $query = Assignation::find()->where(['ticket_id' => $ticket_id]);
        if($exclude_users_id) {
            $query->andWhere(['not', ['in', 'user_id', $exclude_users_id]]);
        }
        $assignations = $query->all();
        foreach ($assignations as $assignation) {
            $assignation->delete();
        }

        History::createHistoryEntry(Ticket::findOne($ticket_id), History::TITLE_DELETE_ASSIGNATION);
    }

    /**
     * @return bool
     * Regla de negocio- Indica si al ticket se le puede registrar una gestion.
     */
    public function canAddTicketManagement()
    {
       if($this->getObservations()->exists()) {
           return true;
       }

       return false;
    }

    /**
     * @param $ticket_id
     * @param $user_id
     * @return bool
     * Crea una gestion de ticket
     */
    public function addTicketManagement($user_id)
    {
        if($this->canAddTicketManagement()) {
            $ticket_management = new TicketManagement([
                'ticket_id' => $this->ticket_id,
                'user_id' => $user_id
            ]);

            return $ticket_management->save();
        }

        return false;
    }

    /**
     * @return int|string
     * Devuelve la cantidad de gestiones de un ticket
     */
    public function getTicketManagementQuantity()
    {
        return $this->getTicketManagements()->count();
    }

    /**
     * Crea un ticket de la categoría de Gestion de ADS.
     */
    public static function createGestionADSTicket($customer_id)
    {
        $ticket = new Ticket([
            'customer_id' => $customer_id,
            'task_id' => null,
            'category_id' => Config::getValue('ticket_category_gestion_ads'),
            'title' => 'Gestionar ADS',
            'content' => 'Instalación realizada. Se asignó la IP al cliente.'
        ]);

        return $ticket->save();
    }
}
