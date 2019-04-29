<?php

namespace app\modules\agenda\controllers;

use app\components\web\Controller;
use app\modules\agenda\helpers\Task;
use app\modules\agenda\models\search\TaskSearch;

class DefaultController extends Controller {

    public $layout = '@app/views/layouts/agenda';

    /**
     * Renderiza la vista principal de agenda
     * @return type
     */
    public function actionIndex($taskSearch = []) {

        if (\Yii::$app->user->isGuest) {
            return $this->redirect(['/user-management/auth/login'], true);
        }

        /*
          \app\modules\agenda\components\AgendaAPI::createTask([
          'users' => [
          'sebamza',
          'superadmin',
          ],
          'status_id' => 1,
          'date' => '2015-10-20',
          'duration' => '10:00'
          ]);
         */

        /*
          \app\modules\agenda\components\AgendaAPI::postponeTask(\app\modules\agenda\models\Task::findOne(344), '2015-10-15', array());
         */

        //Obtenemos usuario logueado
        $user = \Yii::$app->user;
        $searchModel = new TaskSearch();

        if (!empty(\Yii::$app->request->get()['TaskSearch'])) {
            $searchModel->load(\Yii::$app->request->get(), 'TaskSearch');
            if (empty($searchModel->create_option)) {
                $searchModel->create_option = 'all';
            }

            if (empty($searchModel->user_option)) {
                $searchModel->user_option = 'all';
            }
            $searchModel->user_id = $user->id;



            $tasks = \app\modules\agenda\models\Task::getFilteredTasks($searchModel->searchAgenda(), $searchModel);
            $events = $this->buildEvents($tasks);
            //$events = $this->getFilterEvents($searchModel->searchAgenda(), $searchModel);
        } else {
            $searchModel->create_option = 'all';
            $searchModel->user_option = 'all';
            $searchModel->user_id = $user->id;

            $tasks = \app\modules\agenda\models\Task::getFilteredTasks($searchModel->searchAgenda(), $searchModel);
            $events = $this->buildEvents($tasks);
            //$events = $this->getFilterEvents($searchModel->searchAgenda(), $searchModel);
        }


       
        return $this->render('index', [
                    'events' => $events,
                    'model' => $searchModel
        ]);
    }

    /**
     * [NOT USED] Reload the agenda view 
     * @return type
     * @throws NotFoundHttpException
     */
    public function actionUpdateAgenda() {

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $json = [];

        if ($post = \Yii::$app->request->post()) {

            //Obtenemos usuario logueado
            $user = \Yii::$app->user;

            $events = $this->getAllEvents($user);

            $json['status'] = 'success';

            $json['html'] = $this->renderPartial('update-agenda', [
                'events' => $events
            ]);

            return $json;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Devuelve todos los eventos de un usuario
     * @param type $user
     * @return Task
     */
    private function getAllEvents($user) {

        $events = [];

        $tasks = \app\modules\agenda\models\Task::getAllTasks($user);

        if (!empty($tasks))
            $events = $this->buildEvents($tasks, $user->id);

        return $events;
    }

    

    /**
     * Convierte las tareas en eventos de YiiFullCalendar
     * @param type $tasks
     * @return Task
     */
    protected function buildEvents($tasks = [], $user_id = 0) {

        $events = [];

        if (!empty($tasks)) {
            foreach ($tasks as $task) {

                $Event = new Task();
                $Event->id = $task->task_id;
                $Event->title = $task->name;

                $Event->start = date('Y-m-d H:i:s', strtotime($task->date . ' ' . $task->time));

                $startDatetime = new \DateTime($task->date . ' ' . $task->time);
                $startDatetime->add(new \DateInterval("P0000-00-00T$task->duration"));

                $Event->end = $startDatetime->format('Y-m-d H:i:s');

                //Si dura igual o mas de lo que dura el dia, es de "todo el dia"
                if ($task->duration >= \app\modules\config\models\Config::getConfig('work_hours_quantity')->value)
                    $Event->allDay = true;

                $classNames = $this->createCssClasses($task, $user_id);

                $Event->className = $classNames;
                $Event->url = \yii\helpers\Url::to(['/agenda/task/update', 'id' => $task->task_id], true);
                $events[] = $Event;
            }
        }

        return $events;
    }

    /**
     * Analiza datos de la tarea y del usuario y devuelve clases CSS segun el analisis
     * @param \app\modules\agenda\models\Task $task
     * @param \webvimark\modules\UserManagement\models\User $user
     * @return string cssClasses
     */
    protected function createCssClasses(\app\modules\agenda\models\Task $task, $user_id = 0) {

        //Estado, prioridad y ID
        $task_id = $task->task_id;
        $status = $task->status->slug;
        $priority = $task->priority;

        //Enfasis del creador de la tarea
        if ($task->creator_id == $user_id)
            $ownerClass = 'task-is-owner';
        else
            $ownerClass = 'task-is-assigned';

        //Tarea diferente si es parent_id = 0
        if ($task->taskType->slug == \app\modules\agenda\models\TaskType::TYPE_BY_USER && empty($task->parent_id))
            $parentClass = 'task-is-parent';
        else
            $parentClass = 'task-is-child';

        if ($ownerClass == 'task-is-assigned' && $parentClass == 'task-is-parent')
            $visibility = 'display-none';
        else
            $visibility = '';

        return "task id-$task_id scheduled task-$status priority-$priority $ownerClass $parentClass $visibility";
    }

}
