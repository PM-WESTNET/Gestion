<?php
use yii\helpers\Html;
use yii\widgets\ListView;
?>

<style>
    hr {
        border-bottom: 1px inset #9E9E9E;
    }
</style>

<div class="observations" style="height:700px; overflow-y: scroll">

    <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '. Yii::t('app','Create Observation'), '#', [
        'class' => 'btn btn-success pull-right',
        'id' => 'add_obs_btn',
        'data-ticket' => $model->ticket_id
    ])?>

    <?php if($model->canAddTicketManagement()) {
        echo Html::a('<span class="glyphicon glyphicon-pushpin"></span> '. Yii::t('app', 'Register ticket management'), '#', [
            'class' => 'btn btn-primary',
            'id' => 'add_management_btn',
            'data-ticket' => $model->ticket_id
        ]);
    } ?>
    <h3><?= Yii::t('app', 'Observations')?></h3>

    <div class="observation_list" >

        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => '_observation',
            'summary' => ''
        ])?>
    </div>

    <h3><?= Yii::t('app', 'Ticket Managements')?></h3>

    <div class="management_list" >
        <?= ListView::widget([
            'dataProvider' => $dataProviderTicketManagement,
            'itemView' => '_ticket-management',
            'summary' => ''
        ])?>
    </div>
</div>
