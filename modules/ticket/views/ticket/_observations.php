<?php



?>

<style>
    hr {
        border-bottom: 1px inset #9E9E9E;
    }
</style>

<div class="observations" style="height:700px; overflow-y: scroll">

    <?php echo \yii\helpers\Html::a('<span class="glyphicon glyphicon-plus"></span> '
        . Yii::t('app','Create Observation'), '#', [
                'class' => 'btn btn-success pull-right',
                'id' => 'add_obs_btn',
                'data-ticket' => $model->ticket_id
        ])?>
    <div class="observation_list" >

        <?php echo \yii\widgets\ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => '_observation',
            'summary' => ''
        ])?>
    </div>

</div>
