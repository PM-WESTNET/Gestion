<?php

namespace app\modules\agenda\controllers;

use app\components\web\Controller;
use app\modules\agenda\models\search\TaskSearch;
use Yii;
use app\modules\config\models\Config;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\modules\agenda\models\Task;
use yii2fullcalendar\models\Event;

class DefaultController extends Controller {

    public $layout = '/fluid';

    /**
     * Renderiza la vista principal de agenda
     * @return type
     */
    public function actionIndex($taskSearch = []) {

        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/user-management/auth/login'], true);
        }

        $searchModel = new TaskSearch();
        $searchModel->load(Yii::$app->request->get());

        if(!$searchModel->from_date) {
            $searchModel->from_date = (new \DateTime('now'))->modify('-1 months')->format('Y-m-01');
        }

        if(!$searchModel->to_date) {
            $searchModel->to_date = (new \DateTime('now'))->modify('last day of this month')->format('Y-m-d');
        }

        if(!$searchModel->create_option) {
            $searchModel->create_option = 'all';
        }

        if(!$searchModel->user_option) {
            $searchModel->user_option = 'all';
        }

        $tasks = $searchModel->searchAgenda();
        $events = $this->buildEvents($tasks);

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

        \Yii::$app->response->format = Response::FORMAT_JSON;

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

                $Event = new Event();
                $Event->id = $task->task_id;
                $Event->title = $task->visualPriority .' '.$task->name;
                $Event->start = date('Y-m-d H:i:s', strtotime($task->date . ' ' . $task->time));

                $startDatetime = new \DateTime($task->date . ' ' . $task->time);
                $startDatetime->add(new \DateInterval("P0000-00-00T$task->duration"));

                $Event->end = $startDatetime->format('Y-m-d H:i:s');

                //Si dura igual o mas de lo que dura el dia, es de "todo el dia"
                if ($task->duration >= Config::getConfig('work_hours_quantity')->value) {
                    $Event->allDay = true;
                }
                $this->setColors($task, $Event);
                $Event->url = Url::to(['/agenda/task/view', 'id' => $task->task_id, 'agenda' => true], true);
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
    protected function setColors(Task $task, Event &$event) {

        $status = $task->status->color;
        $event->color = 'white';

        if($status == 'normal') {
            $event->backgroundColor = '#777';
        }

        if($status == 'warning') {
            $event->backgroundColor = '#f0ad4e';
        }

        if($status == 'info') {
            $event->backgroundColor = '#5bc0de';
        }

        if($status == 'danger') {
            $event->backgroundColor = '#d9534f';
        }

        if($status == 'success') {
            $event->backgroundColor = '#5cb85c;';
        }
    }

}
