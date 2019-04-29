<?php

    if(!empty($this->collector)){
        $collector = $this->collector;
    }

?>
<!-- Batch closure details -->
<div class="panel panel-success z-depth-1 z-depth-important">
    <div class="panel-heading">
        <h3 class="panel-title"><?= app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Batch closure details'); ?></h3>
    </div>
    <div class="panel-body">

        <ul class="list-group no-margin-bottom">
            <li class="list-group-item">
                <span class="badge"><?= $model->payment_count; ?></span>
                <strong class="text-success"><?= $model->getAttributeLabel("payment_count"); ?></strong>
            </li>
            <li class="list-group-item">
                <span class="badge"><?= Yii::$app->formatter->asCurrency($model->total) ; ?></span>
                <strong class="text-success"><?= $model->getAttributeLabel("total"); ?></strong>
            </li>
            <li class="list-group-item">
                <span class="badge"><?= Yii::$app->formatter->asCurrency($model->commission) ?></span>
                <strong class="text-success"><?= $model->getAttributeLabel("commission"); ?></strong>
            </li>
            <li class="list-group-item">
                <span class="badge"><?= Yii::$app->formatter->asCurrency($model->netTotal) ?></span>
                <strong class="text-success"><?= $model->getAttributeLabel("net_total"); ?></strong>
            </li>
            <li class="list-group-item">
                <span class="badge"><?= Yii::$app->formatter->asDatetime($model->firstPayout->datetime) ?></span>
                <strong class="text-success"><?= $model->getAttributeLabel("first_payout"); ?></strong>
            </li>
            <li class="list-group-item">
                <span class="badge"><?= Yii::$app->formatter->asDatetime($model->lastPayout->datetime); ?></span>
                <strong class="text-success"><?= $model->getAttributeLabel("last_payout"); ?></strong>
            </li>
        </ul>

    </div>
</div>
<!-- end Batch closure details -->