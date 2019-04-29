<form id="form-change-server">
    <input type="hidden" name="node_id" id="node_id" value="<?php echo $model->node_id ?>" />
    <div class="row">
        <div class="col-md-3">
            <?php echo Yii::t('westnet', 'Node') ?>
        </div>
        <div class="col-md-9">
            <?php echo $model->name ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <?php echo Yii::t('westnet', 'New Server') ?>
        </div>
        <div class="col-md-9">
            <?php
            echo \yii\helpers\Html::dropDownList('servr_id', null, \yii\helpers\ArrayHelper::map($servers, 'server_id', 'name'), [
                'prompt' => Yii::t('app','Select'),
                'id' => 'server_id',
                'class' => 'form-control'
            ] );
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <?php echo Yii::t('westnet', 'To Process') ?>
        </div>
        <div class="col-md-9" id="to_process"><?php echo $to_process ?></div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="progress">
                <div id="progress-bar" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
        </div>
    </div>
</form>