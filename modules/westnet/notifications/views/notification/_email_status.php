<?php
/**
 * Created by PhpStorm.
 * User: juan
 * Date: 05/12/19
 * Time: 11:07
 */

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
            EmailStatus.interval = setInterval(function(){ EmailStatus.getProcess()}, 3000);
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
                    }else if (response.status === 'error') {
                        clearInterval(EmailStatus.interval);
                        $('#message').html(response.message);
                        $("#bar").css('background-color', 'red');
                        $("#bar").css('width', '100%');
                        $('#sta-lbl').html("<?php echo \app\modules\westnet\notifications\NotificationsModule::t('app', 'Error')?>")

                    }
                }
            })
        }
    }


</script>

