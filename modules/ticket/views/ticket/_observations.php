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

    <div class="col-sm-12">
        <h3><?= Yii::t('app', 'Observations')?></h3>
        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => '_observation',
            'summary' => ''
        ])?>
    </div>
</div>
