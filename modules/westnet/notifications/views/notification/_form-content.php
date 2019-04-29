<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\tinymce\TinyMce;
use app\modules\westnet\notifications\components\scheduler\Scheduler;
use app\modules\westnet\notifications\NotificationsModule;
use app\modules\westnet\notifications\models\Notification;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\Notification */
/* @var $form yii\widgets\ActiveForm */
?>

<!--  Notification form -->
<div class="notification-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    
    <?php
    echo $form->errorSummary([$model]);
    ?>

    <?= $form->field($model, 'status')->dropDownList(Notification::staticFetchStatuses()) ?>

    <?php
    $file = Yii::getAlias("transport-form/_{$model->transport->slug}");
    if(file_exists(__DIR__.DIRECTORY_SEPARATOR.$file.'.php')){
        echo $this->render($file, ['form' => $form, 'model' => $model]);
    }else{
        $default = Yii::getAlias("transport-form/_default");
        echo $this->render($default, ['form' => $form, 'model' => $model]);
    }
    ?>
    
    <?php if($model->transport->hasFeature('programmable')): ?>
    <div class="panel panel-default">

        <div class="panel-heading">
            <h5 class="panel-name font-bold"><?= NotificationsModule::t('app', 'Schedule');?></h5>
        </div>

        <div class="panel-body">
            <div class="row">
                <?= $form->field($model, 'scheduler')->dropDownList(Scheduler::getSchedulersForSelect(), ['id' => 'scheduler-class', 'prompt' => \app\modules\westnet\notifications\NotificationsModule::t('app', 'No scheduler')]) ?>
            </div>
            
            <div id="scheduler-form-here"></div>
        </div>
    </div>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<!--  end Notification form -->

<style>
    .ui-datepicker{
        z-index: 2 !important;
    }
</style>

<script>
    
    /**
     * Sets event for period calculation     
     */
    var Scheduler = new function(){
        
        var notification_id;
        
        this.init = function(notif_id){
            notification_id = notif_id;
            
            loadSchedulerForm();
            $('#scheduler-class').on('change', function(){
                loadSchedulerForm();
            });
        }
        
        function loadSchedulerForm(){
            var sclass = $('#scheduler-class').val();
            
            if(!sclass){
                $('#scheduler-form-here').html('');
                return;
            }
            
            var data = {
                id: notification_id,
                class: sclass
            };
            
            $.ajax({
                url: '<?= yii\helpers\Url::to(['load-scheduler-form']) ?>',
                data: data,
                type: 'get',
            }).done(function(json){
                if(json.status == 'success'){
                    $('#scheduler-form-here').html(json.form);
                    bindPeriodCalc();
                    
                    $('.datepicker').datepicker({
                        dateFormat: 'dd-mm-yy'
                    });
                }
            });
        }
        
        function bindPeriodCalc(){

            var $timesPerDay = $("[name='Notification[times_per_day]']");
            var $timeFrom = $("[name='Notification[from_time]']");
            var $timeTo = $("[name='Notification[to_time]']");

            var $target = $("#period-times");

            var calcSchedule = function () {

                //If any of the requried values is null, just return and keep going with something else
                if (!$timesPerDay.val() > 0 || !$timeFrom.val() || !$timeTo.val())
                    return;

                $.ajax({
                    url: '<?= yii\helpers\Url::to(['notification/get-period-times']); ?>',
                    dataTransport: 'json',
                    transport: 'get',
                    data: {
                        period: $timesPerDay.val(),
                        timeFrom: $timeFrom.val(),
                        timeTo: $timeTo.val()
                    },
                    beforeSend: function () {
                        $target.html('<?= \app\modules\westnet\notifications\NotificationsModule::t('app', 'Calculating times...'); ?>');
                    }
                }).done(function (response) {
                    if (response.status == 'success') {
                        $target.html(response.html);
                    } else {
                        console.log("Ocurrió un error al buscar.");
                    }
                }).error(function () {
                    console.log("Error en request");
                });
            }

            $timesPerDay.on("change", calcSchedule);
            $timeFrom.on("change", calcSchedule);
            $timeTo.on("change", calcSchedule);

        }
    }
</script>

<?= $this->registerJs('Scheduler.init('.$model->notification_id.')'); ?>
<?php
    \yii\jui\JuiAsset::register($this);
?>