<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 05/12/19
 * Time: 11:07
 */

$total = Yii::$app->cache->get('notification_'.$model->notification_id.'_total');
$success = Yii::$app->cache->get('notification_'.$model->notification_id.'_sended');
$success_with_errors = Yii::$app->cache->get('notification_'.$model->notification_id.'_sended_with_errors');
$not_sended = Yii::$app->cache->get('notification_'.$model->notification_id.'_not_sended');
$process = 0;
if ($total) {
    $process = (((int)$success + (int)$success_with_errors + (int)$not_sended) * 100) / (int)$total;
}

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo \app\modules\westnet\notifications\NotificationsModule::t('app','Process')?></h3>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="panel-body">
                <h5><span id="sta-lbl"><?php echo \app\modules\westnet\notifications\NotificationsModule::t('app', 'Processing...')?></span></h5>
                <div class="progress">
                    <div class="progress-bar" id="bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $process?>;%;">
                        <span class="sr-only">60% Complete</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-xs-12">
            <h5>Total de notificaciones: <span id="total"><?php echo $total?></span></h5>
            <h5>Total enviadas: <span id="sended"><?php echo $success?></span></h5>
            <h5>Total enviados con errores: <span id="sended_errors"><?php echo $success_with_errors?></span></h5>
            <h5>Total no enviado: <span id="not_sended"><?php echo $not_sended?></span></h5>
            <h5>Enviados: <span id="success"><?php echo Yii::$app->cache->get('success_'.$model->notification_id)?></span></h5>
            <h5 class="text-danger"><span id="message"></span></h5>
        </div>
    </div>
</div>


<script>

    var NotificationStatus = new function() {

        this.interval;

        this.init = function () {
            NotificationStatus.interval = setInterval(function(){ NotificationStatus.getProcess()}, 3000);
        };

        this.getProcess = function () {
            $.ajax({
                url: "<?php echo \yii\helpers\Url::to(['notification-proccess-status', 'id' => $model->notification_id])?>",
                method: 'GET',
                dataType: 'json'
            }).done(function(response, status){
                if (status === 'success') {
                    if (response.status === 'pending' || response.status === 'in_process') {

                        var process = ((parseInt(response.success) + parseInt(response.success_errors)) * 100) / parseInt(response.total);
                        $("#bar").css('width', process + '%');
                        $('#total').html(response.total);
                        $('#sended').html(response.success);
                        $('#sended_errors').html(response.success_errors);
                        $('#not_sended').html(response.notSended);
                        $('#error').html(response.error);

                        if(process === 100) {
                            clearInterval(NotificationStatus.interval);
                            $("#bar").css('background-color', 'green');
                            $('#sta-lbl').html("<?php echo \app\modules\westnet\notifications\NotificationsModule::t('app', 'Finished')?>")
                        }
                    }else if (response.status === 'error') {
                        clearInterval(NotificationStatus.interval);
                        $('#message').html(response.message);
                        $("#bar").css('background-color', 'red');
                        $("#bar").css('width', '100%');
                        $('#sta-lbl').html("<?php echo \app\modules\westnet\notifications\NotificationsModule::t('app', 'Error')?>")

                    } else if (response.status === 'sent') {
                        var process = ((parseInt(response.success) + parseInt(response.success_errors)) * 100) / parseInt(response.total);
                        $("#bar").css('width', process + '%');
                        $('#total').html(response.total);
                        $('#sended').html(response.success);
                        $('#sended_errors').html(response.success_errors);
                        $('#not_sended').html(response.notSended);
                        $('#error').html(response.error);

                        clearInterval(NotificationStatus.interval);
                        $("#bar").css('background-color', 'green');
                        $('#sta-lbl').html("<?php echo \app\modules\westnet\notifications\NotificationsModule::t('app', 'Finished')?>")
                    }
                }
            })
        }
    }


</script>

