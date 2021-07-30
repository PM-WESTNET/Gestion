<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 05/12/19
 * Time: 11:07
 */
use yii\helpers\Url;

$total = Yii::$app->cache->get('total_'.$model->notification_id);
$success = Yii::$app->cache->get('success_'.$model->notification_id);
$process = 0;
if ($total) {
    $process = (((int)$success) * 100) / (int)$total;
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
                <span style="float:left;">
                    <button type="button" class="glyphicon glyphicon-play green" id="start-process" style="color:white;background-color:green;border-radius: 8px;">
                    <button type="button" class="glyphicon glyphicon-pause yellow" id="stop-process" style="margin-left: 2px;color:white;background-color:orange;border-radius: 8px;">
                    <button type="button" class="glyphicon glyphicon-remove red" id="cancel-process" style="margin-left: 2px;color:white;background-color:red;border-radius: 8px;">
                </span>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-xs-12">
            <h5>Total de correos: <span id="total"><?php echo Yii::$app->cache->get('total_'.$model->notification_id)?></span></h5>
            <h5>Enviados: <span id="success"><?php echo Yii::$app->cache->get('success_'.$model->notification_id)?></span></h5>
            <h5 class="text-danger"><span id="message"></span></h5>
        </div>
    </div>
</div>


<script>

    var EmailStatus = new function() {

        this.interval;

        this.init = function () {
            EmailStatus.interval = setInterval(function(){ EmailStatus.getProcess()}, 1000);

            $(document).off('click', "#start-process").on('click', "#start-process", function(ev){
                $.ajax({
                    url: '<?= Url::to(['update-status-notification'])?>',
                    method: 'POST',
                    data: {
                        'id': <?=$model->notification_id?>,
                        'status': 'pending'
                    },
                    dataType: 'json',
                    success: function (data) { 
                        $("#start-process").attr('disabled', true);
                        $("#start-process").css('color', 'gray');

                        $("#stop-process").attr('disabled', false);
                        $("#stop-process").css('color', 'white');

                        $('#sta-lbl').html("Procesando...");
                        EmailStatus.init();
                    }
                })
            });

            $(document).off('click', "#stop-process").on('click', "#stop-process", function(ev){
                $.ajax({
                    url: '<?= Url::to(['update-status-notification'])?>',
                    method: 'POST',
                    data: {
                        'id': <?=$model->notification_id?>,
                        'status': 'paused'
                    },
                    dataType: 'json',
                    success: function (data) {
                        $("#stop-process").attr('disabled', true);
                        $("#stop-process").css('color', 'gray');

                        $("#start-process").attr('disabled', false);
                        $("#start-process").css('color', 'white');

                    }
                })       
            });

            $(document).off('click', "#cancel-process").on('click', "#cancel-process", function(ev){
                $.ajax({
                    url: '<?= Url::to(['update-status-notification'])?>',
                    method: 'POST',
                    data: {
                        'id': <?=$model->notification_id?>,
                        'status': 'cancelled'
                    },
                    dataType: 'json',
                    success: function (data) {
                        window.location.reload();
                    }
                })
            });

        };

        this.getProcess = function () {
            $.ajax({
                url: "<?php echo \yii\helpers\Url::to(['notification-proccess-status', 'id' => $model->notification_id])?>",
                method: 'GET',
                dataType: 'json'
            }).done(function(response, status){
                if (status === 'success') {
                    if (response.status === 'pending' || response.status === 'in_process') {

                        var process = ((parseInt(response.success)) * 100) / parseInt(response.total);
                        $("#bar").css('width', process + '%');
                        $('#total').html(response.total);
                        $('#success').html(response.success);
                        $('#error').html(response.error);

                        if(process === 100) {
                            clearInterval(EmailStatus.interval);
                            $("#bar").css('background-color', 'green');
                            $('#sta-lbl').html("<?php echo \app\modules\westnet\notifications\NotificationsModule::t('app', 'Finished')?>")
                        }
                        $("#start-process").attr('disabled', true);
                        $("#start-process").css('color', 'gray');
                    }else if (response.status === 'error') {
                        clearInterval(EmailStatus.interval);
                        $('#message').html(response.message);
                        $("#bar").css('background-color', 'red');
                        $("#bar").css('width', '100%');
                        $('#sta-lbl').html("<?php echo \app\modules\westnet\notifications\NotificationsModule::t('app', 'Error')?>")

                    }else if (response.status === 'sent') {
                        clearInterval(EmailStatus.interval);
                        $("#bar").css('background-color', 'green');
                        $('#sta-lbl').html("<?php echo \app\modules\westnet\notifications\NotificationsModule::t('app', 'Finished')?>")
                    }else if (response.status === 'paused') {
                        clearInterval(EmailStatus.interval);
                        $("#bar").css('background-color', 'yellow');
                        $("#stop-process").attr('disabled', true);
                        $("#stop-process").css('color', 'gray');
                        $('#sta-lbl').html("Pausado...");

                    }else if (response.status === 'canceled') {
                        clearInterval(EmailStatus.interval);
                        $("#bar").css('background-color', 'red');
                        $('#sta-lbl').html("Cancelado...");
                    }
                }
            })
        }
    }


</script>

