<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('westnet', 'Proceso de Liquidación de Vendedores');
$this->params['breadcrumbs'][] = ['label' => Yii::t('westnet', 'Vendor Liquidation Process'), 'url' => ['vendor-liquidation-process']];
$this->params['breadcrumbs'][] = $this->title;
$urlcontroller= Url::to(['status-vendor-liquidation-process']);

$this->registerJs("
    $(document).ready(function() {
        setInterval(ProgressStatus, 1000);
    });

    function ProgressStatus() { 
        $.ajax({
            url:'$urlcontroller',
            data: { id: $model->vendor_liquidation_process_id },
            type: 'post',
            success: function(data){
                data = JSON.parse(data);
                console.log(data);
                let total = (data.pending?parseInt(data.pending):0) +  (data.cancelled?parseInt(data.cancelled):0) + (data.success?parseInt(data.success):0);
                let total_percentage = (((parseInt(data.cancelled) + parseInt(data.success))/total)*100);

                $('#restantes').html(data.pending);
                $('#cancelados').html(data.cancelled);
                $('#completados').html(data.success);
                $('#totales').html(total);
                
                $('.progress-bar').css({'width':total_percentage+'%'});
                if(total_percentage == 100){
                    $('.progress-bar').removeClass('progress-bar-info').addClass('progress-bar-success');

                    $('').html('Finalizado...');
                    $('#status').removeClass('alert-info').addClass('alert-success');
                }
            }
        })
    }
");

?>
<div class="view-vendor-liquidation-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="progress">
      <div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 1%">
        <span class="sr-only">45% Complete</span>
      </div>
    </div>

    <div class="alert alert-info" role="alert" id="status">En Proceso...</div>

     <span class="label label-info">Total: 
        <span id="totales">0</span>
    </span><br>
     <span class="label label-success">Completados: 
        <span id="completados">0</span>
    </span> <br>
    <span class="label label-warning">Restantes: 
        <span id="restantes">0</span>
    </span> <br>
    <span class="label label-danger">Cancelados: 
        <span id="cancelados">0</span>
    </span> <br>
   
    <div class="text-center">
        <button type="button" class="btn btn-warning" onclick="window.history.go(-1)"><i class="fa fa-reply"></i> Volver</button>
    </div>
</div>