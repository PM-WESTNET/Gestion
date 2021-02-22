<?php

/**
 * Notifications Widget
 */

namespace app\components\widgets\agenda\notification;

use app\modules\agenda\models\Task;
use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use app\modules\config\models\Config;

class Notification extends Widget {

    /**
     * @var array list of notifications items.
     */
    public $notifications = [];

    /**
     * @var array list of new notifications items.
     */
    public $newNotifications = [];

    /**
     * @var array list of notifications items.
     */
    public $user = null;

    /**
     * Renders notifications
     */
    public function run() {                
        
        NotificationBundle::register($this->getView());

        //Fetch logged in user
        if (empty($this->user)) {
            $this->user = Yii::$app->user;
        }

        if ((bool) Config::getConfig('check_expiration_on_login')->value)
            $this->checkOverdueTasks();

        //Fetch notifications
        if (empty($this->notifications)) {
            /*$this->notifications = \app\modules\agenda\models\Notification::find()->where([
                        'user_id' => $this->user->id,
                        'show' => true,
                        'status' => \app\modules\agenda\models\Notification::STATUS_UNREAD,
                    ])->orderBy([
                        'datetime' => SORT_ASC
                    ])->limit(20)->all(); */
            $this->notifications = Task::find()
                ->select([
                    'task.*', 
                    'n.status AS notification_status', 
                    'n.reason AS notification_reason', 
                    'n.notification_id AS notification_id',
                    'n.datetime AS notifcation_datetime',
                    's.name AS status_name', 
                    's.slug AS status_slug'
                ])
                ->innerJoin('notification n', 'n.task_id = task.task_id')
                ->innerJoin('status s', 's.status_id = task.status_id')
                ->where([
                    'n.user_id' => $this->user->id,
                    'n.show' => true,
                    'n.status' => \app\modules\agenda\models\Notification::STATUS_UNREAD,
                ])->orderBy([
                    'datetime' => SORT_ASC
                ])->limit(20)->all();
        }
        
        //Count
        if (empty($this->newNotifications)) {
            $this->newNotifications = \app\modules\agenda\models\Notification::find()->where([
                        'user_id' => $this->user->id,
                        'status' => \app\modules\agenda\models\Notification::STATUS_UNREAD,
                        'show' => true
                    ])->count();
        }

        //Render the list
        return $this->renderFile('@app/components/widgets/agenda/notification/views/list.php', [
                    'notifications' => $this->notifications,
                    'newNotifications' => $this->newNotifications,
        ]);
    }

    /**     
     * Creates a cookie to check if there is any overdue tasks. 
     * If there are some overdue tasks, it will create notifications on every assignated user and respective crators
     */
    protected function checkOverdueTasks() {

        //Login cookie
        if (!Yii::$app->getRequest()->getCookies()->has('firstLogin')) {

            Yii::$app->getResponse()->getCookies()->add(new \yii\web\Cookie([
                'name' => 'firstLogin',
                'value' => time(),
                'expire' => time() + Config::getConfig('check_expiration_timeout')->value
            ]));

            //Creates notifications on each overdue tasks (if any)
            \app\modules\agenda\models\Task::markAllAsExpired($this->user->id);
        }
    }

}
