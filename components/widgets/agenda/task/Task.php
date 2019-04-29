<?php

namespace app\components\widgets\agenda\task;

use Yii;
use yii\base\Widget;

/**
 * Notifications Widget
 * @author smaldonado
 */
class Task extends Widget {

    /**
     * @var User logged user
     */
    public $user = null;

    /**
     * Renders notifications.
     */
    public function run() {
        
        TaskBundle::register($this->getView());
        
        //Obtenemos usuario
        if (empty($this->user)) {
            $this->user = Yii::$app->user;
        }
        
        $model = new \app\modules\agenda\models\Task;

        //Renderizamos el modal de crear tarea
        return $this->renderFile('@app/components/widgets/agenda/task/views/new_task_modal.php', [
            'user' => $this->user,
            'model' => $model
        ]);
        
    }
    
}
    