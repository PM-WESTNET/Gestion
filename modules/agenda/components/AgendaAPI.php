<?php

namespace app\modules\agenda\components;

use app\modules\agenda\models\Task;
use app\modules\config\models\Config;

/**
 * AgendaAPI
 *  AgendaAPI se encarga de la creacion de tareas a partir de una llamada sencilla de creacion, desde cualquier lugar de la aplicación
 * @author smaldonado
 * @example \app\modules\agenda\components\AgendaAPI::createTask($taskData = array());
 */
class AgendaAPI {

    /**
     * Crea una tarea a partir de datos de usuario, fecha y duración. Si no se indica 'time', se asigna automáticamente un time para la tarea segun el calendario para 'date' de cada usuario
     * @param array $taskData
     *      [
     *       'users' => ['username|id'],
     *       'task_type_id' => \Yii::$app->user->id,
     *       'creator_id' => \Yii::$app->user->id,
     *       'category_id' => 1,
     *       'status_id' => 3,
     *       'date' => date("Y-m-d"),
     *       'time' => date("H:i"),
     *       'datetime' => time(),
     *       'name' => 'Tarea nueva',
     *       'priority' => 2,
     *      ]
     * @throws yii\web\BadRequestHttpException
     */
    public static function createTask($taskData = []) {

        $type_by_user_id = \app\modules\agenda\models\TaskType::find()->where([
                    'slug' => \app\modules\agenda\models\TaskType::TYPE_BY_USER
                ])->one()->task_type_id;

        //Atributos por defecto de tarea
        $taskAttributes = array_merge([

            'users' => [(\Yii::$app instanceof \yii\console\Application ? 1 : \Yii::$app->user->id)],
            'task_type_id' => $type_by_user_id,
            'creator_id' => (\Yii::$app instanceof \yii\console\Application ? 1 : \Yii::$app->user->id),
            'category_id' => 1,
            'status_id' => 2,
            'date' => date("Y-m-d"),
            'time' => date("H:i"),
            'duration' => '02:00',
            'datetime' => time(),
            'name' => 'Tarea nueva',
            'description' => '',
            'priority' => 2,
                ], $taskData);

        $taskAttributes['users'] = self::findUsers($taskAttributes['users']);

        //Solo analizamos la hora de inicio y duracion si es una tarea por usuario
        if ($taskAttributes['task_type_id'] == $type_by_user_id && empty($taskAttributes['time']))
            $taskAttributes = self::assignTasks($taskAttributes);

        //Creación de task
        $task = new Task();
        $task->load($taskAttributes, '');

        if ($task->save(false))
            return $task;
    }

    /**
     * Pospone una tarea y la ubica en la fecha elegida, en el primer hueco encontrado
     * @param Task $task (Tarea a posponer)
     * @param phpDate $date (Fecha a la cual se quiere posponer la tarea. Si está ocupada para el usuario, se asignará la siguiente disponible)
     * @param Integer[] $users (OPTIONAL | Si no se envian usuarios, se pospondrá la tarea para todos los usuarios asignados)
     * @throws yii\web\BadRequestHttpException
     */
    public static function postponeTask($task, $date, $users = array()) {

        //Verificamos que la tarea sea válida
        if (!empty($task) && $task->task_id > 0 && $task->isPostponable()) {

            //Obtenemos los nombres del campo ID de user
            $userModelId = self::getUserIdName();

            if (empty($users))
                $users = $task->users; //Si no vienen usuarios, posponemos para todos los usuarios asignados a la tarea
            else
                $users = self::findUsers($users); //Si vienen usuarios, buscamos que sean usuarios validos

            foreach ($users as $user_id) {

                //Verificamos si el usuario está asignado a la tarea
                if ($task->isAssignedUser($user_id)) {

                    //Cambiamos la fecha y hora de esta tarea para este usuario
                    $task->load(self::addTaskToSchedule($task->attributes, $user_id, $date), '');
                    $task->save(false);
                }
            }

            //Si la tarea no es válida, tiramos exception
        } else {
            throw new \yii\web\BadRequestHttpException("[AgendaAPI->postponeTask] No valid tasks given.", 403);
        }
    }

    /**
     * Asigna una fecha y hora de inicio a partir de la fecha y hora elegida por el usuario
     * @param array $taskAttributes (Atributos de la tarea)
     * @return array
     */
    protected static function assignTasks($taskAttributes) {

        $date = $taskAttributes['date'];
        $users = $taskAttributes['users'];

        if (!empty($users)) {
            foreach ($users as $user_id) {
                $taskAttributes = self::addTaskToSchedule($taskAttributes, $user_id, $date);
            }
        }

        return $taskAttributes;
    }

    /**
     * Intenta ubicar una tarea en el calendario de un usuario, dependiendo su asignacion de tareas actual
     * @param array() $taskAttributes (Atributos de la tarea, se van modificando segun necesidades)
     * @param int $user_id (id del usuario)
     * @param date $date (fecha desde la cual se empieza a verificar el calendario del usuario)
     * @return array() $taskAttributes (devuelve los atributos de la tarea, actualizados, con la nueva fecha y hora)
     */
    protected static function addTaskToSchedule($taskAttributes = array(), $user_id = 0, $date) {

        if ($user_id > 0 && !empty($taskAttributes)) {

            //Obtenemos todas las tareas del usuario para la fecha asignada
            $userTasks = self::getUserTasks($user_id, $date);

            //Obtenemos el schedule de ese usuario para ese dia
            $schedule = self::getDaySchedule($userTasks, $date);

            if (!empty($schedule)) {

                //Asingamos la nueva fecha
                $taskAttributes['date'] = $date;

                $found = false;

                //Trato de ubicar la tarea en el schedule del usuario. Si la duracion es mas chica que el hueco libre, asignamos el inicio del hueco como horario de la tarea
                foreach ($schedule as $startTime => $maxDuration) {
                    if ($taskAttributes['duration'] <= $maxDuration) {
                        $found = true;
                        $taskAttributes['time'] = $startTime;
                        break;
                    }
                }
                //Si no pude acomodarlo, sumo uno a la fecha y busco de nuevo de manera recursiva hasta encontrar un espacio libre en el calendario del usuario
                if (!$found) {
                    $date = self::getNextBusinessDay($date);
                    $taskAttributes = self::addTaskToSchedule($taskAttributes, $user_id, $date);
                }
            }
            return $taskAttributes;
        } else {
            throw new \yii\web\BadRequestHttpException("[AgendaAPI->addTaskToSchedule] No valid attributes given.", 403);
        }
    }

    /**
     * Devuelve el siguiende día laboral posible
     * @param stringDate $currentDate (Fecha de la cual se quiere obtener el dia habil siguiente)
     * @return stringDate (Dia siguiente)
     */
    protected static function getNextBusinessDay($currentDate) {

        $nextDate = new \DateTime($currentDate);
        $nextDate->modify('+1 Weekday');

        return $nextDate->format('Y-m-d');
    }

    /**
     * Devuelve un schedule con el fin de cada tarea y las horas libres siguientes a ese fin
     * @param array $tasks (Array de tareas de un dia especifico)
     * @param string $taskDate (Fecha de las tareas)
     * @return array $schedule (Array con los "huecos" disponibles en el dia)
     */
    protected static function getDaySchedule($tasks = [], $taskDate) {

        if (empty($taskDate))
            $taskDate = date("Y-m-d");

        $schedule = [];

        //Obtenemos de params el inicio, fin y duración de jornada laboral (Formato H:i)
        /*
          $agendaModule = \Yii::$app->getModule('agenda');
          $workDayStart = $agendaModule->params['task']['work_hours_start'];
          $workDayEnd = $agendaModule->params['task']['work_hours_end'];
         */
        $workDayStart = Config::getConfig('work_hours_start')->value;
        $workDayEnd = Config::getConfig('work_hours_end')->value;

        if (!empty($tasks)) { //Si no es un dia libre, analizamos las tareas
            foreach ($tasks as $keyTask => $task) {

                //Obtenemos datetimes del inicio y fin del horario laboral
                $workDayStartDatetime = new \DateTime($task->date . ' ' . $workDayStart);
                $workDayEndDatetime = new \DateTime($task->date . ' ' . $workDayEnd);

                $keys = array_keys($tasks);

                //Calcula la hora de finalizacion de esta tarea
                $thisStartDatetime = new \DateTime($task->date . ' ' . $task->time);
                $thisStartDatetimeInitial = new \DateTime($task->date . ' ' . $task->time);
                $thisStartDatetime->add(new \DateInterval("P0000-00-00T$task->duration"));
                $thisEndTime = $thisStartDatetime->format("H:i");

                //Si es la primer tarea del dia, usamos el inicio del dia laboral para ver que espacio queda libre
                if ($keyTask == 0) {
                    //Si la primer tarea NO inicia cuando empieza el dia laboral
                    if ($thisStartDatetime->format("H:i") > $workDayStart) {
                        $schedule[$workDayStart] = $thisStartDatetimeInitial->diff($workDayStartDatetime)->format("%H:%I");
                    }
                }

                //Obtenemos hora de inicio de la siguiente tarea, si existe
                if (!empty($tasks[$keyTask + 1])) {
                    $nextTask = $tasks[$keyTask + 1];

                    $nextStartDatetime = new \DateTime($nextTask->date . ' ' . $nextTask->time);
                    $freeInterval = $nextStartDatetime->diff($thisStartDatetime)->format("%H:%I");

                    $schedule[$thisEndTime] = $freeInterval;
                }

                //Si es la ultima, usamos el fin del dia laboral para ver que espacio queda libre
                if ($keyTask == end($keys)) {
                    $thisEndDatetime = new \DateTime($task->date . ' ' . $thisEndTime);
                    $schedule[$thisEndTime] = $workDayEndDatetime->diff($thisEndDatetime)->format("%H:%I");
                }
            }
        } else { //Si es un dia libre, asignamos al inicio del dia laboral
            //$workHoursQuantity = $agendaModule->params['task']['work_hours_quantity'];
            $workHoursQuantity = Config::getConfig('work_hours_quantity')->value;
            $schedule[$workDayStart] = $workHoursQuantity;
        }

        return $schedule;
    }

    /**
     * Devuelve las tareas NO completadas de un usuario para una fecha determinada, ordenadas por horario de inicio
     * @param int $user_id (ID del usuario del cual se quieren obtener las tareas)
     * @param phpDate $date (Fecha que se usa para buscar tareas)
     * @return array() $userTasks (Array con las tareas encontradas)
     */
    protected static function getUserTasks($user_id, $date) {

        //Status completado
        $status_complete_id = \app\modules\agenda\models\Status::find()->where([
                    'slug' => \app\modules\agenda\models\Status::STATUS_COMPLETED
                ])->one()->status_id;

        //Devuelve todas las tareas NO completadas del usuario, ordenadas por hora de inicio
        return $userTasks = Task::find()
                ->joinWith('notifications', false)
                ->where([
                    'date' => $date,
                    'show' => true,
                    'user_id' => $user_id,
                ])
                ->andWhere(['<>', 'status_id', $status_complete_id])
                ->orderBy([
                    'time' => SORT_ASC
                ])
                ->all();
    }

    /**
     * Busca un usuario con los datos ingresados (Tira exception si no se encuentran usuarios o si $usersFeed está vacio)
     * @param array() $usersFeed (Array con IDs o con usernames que se usa para buscar Users)
     * @return array() $users (Array con los usuarios válidos encontrados)
     * @throws yii\web\BadRequestHttpException 
     */
    protected static function findUsers($usersFeed = []) {

        $users = [];

        $userModelClass = self::getUserModelName();
        $userModelId = self::getUserIdName();

        if (!empty($usersFeed)) {

            foreach ($usersFeed as $userData) {
                
                $user = [];

                //busca con ID
                if (is_numeric($userData)){
                    $user = $userModelClass::findOne($userData);
                }else if (is_string($userData))
                    $user = $userModelClass::find()->where([
                                'username' => $userData
                            ])->one();
                
                //Si existe el usuario, lo agregamos al listado
                if (!empty($user) && $user->$userModelId > 0)
                    $users[] = $user->id;
            }
        }else {
            throw new \yii\web\BadRequestHttpException("[AgendaAPI->findUsers] Missing users for task assignation.", 403);
        }

        if (empty($users))
            throw new \yii\web\BadRequestHttpException("[AgendaAPI->findUsers] No valid users found in given user list.", 403);

        return $users;
    }

    /**
     * Devuelve el nombre del model usado para Usuarios
     * @return string (Nombre del modelo de User)
     */
    protected static function getUserModelName() {

        //Buscamos modulo para tabla users
        $agendaModule = \Yii::$app->getModule('agenda');

        if (isset($agendaModule->params['user']['class']))
            return $agendaModule->params['user']['class'];
        else
            return 'User';
    }

    /**
     * Devuelve el nombre del campo id del model usado de Usuarios
     * @return string (Nombre del campo ID del model de User)
     */
    protected static function getUserIdName() {

        //Buscamos pk para tabla users
        $agendaModule = \Yii::$app->getModule('agenda');

        if (isset($agendaModule->params['user']['idAttribute']))
            return $agendaModule->params['user']['idAttribute'];
        else
            return 'id';
    }

}
