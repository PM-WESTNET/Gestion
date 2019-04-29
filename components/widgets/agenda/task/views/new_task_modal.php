<?php
use app\components\widgets\agenda\AgendaBundle;
use app\components\widgets\agenda\task\TaskBundle;

AgendaBundle::register($this);
TaskBundle::register($this);
?>

<div class="modal fade" id="new-task-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Crear Tarea</h4>
            </div>
            
            <div class="modal-body">
                
                <iframe id="new-task-iframe" src="<?= \yii\helpers\Url::to(['/agenda/task/quick-create']); ?>"  style="overflow-x: hidden;">

                </iframe>
                
            </div>
            
        </div>
    </div>
</div>

<?php
$this->registerJs('AgendaTask.setNewTaskHtml("' . $this->renderFile("@app/components/widgets/agenda/task/views/_new_task_init.php") . '");', yii\web\View::POS_END);
$this->registerJs("AgendaTask.init();", yii\web\View::POS_END);
//$this->registerJs("Notification.setChangeStatusUrl('" . yii\helpers\Url::to(['/agenda/notification/change-status'], true) . "');", yii\web\View::POS_END);
?>