<!-- Customer info -->
<div class="panel panel-success no-margin-bottom">

    <div class="panel-heading">

        <h3 class="panel-title font-bold">
            <?= \app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Information about'); ?> <?= $model->name; ?> <?= $model->lastname; ?>
        </h3>

    </div>

    <div class="panel-body">
        <div class="row alert alert-danger" style="margin-bottom:0px">
            <div class="col-md-6">
                <br>
                <span class="font-bold"><?= Yii::t('app', 'Amount due'); ?></span>
            </div>
            <div class="col-md-6">
                <span class="font-bold font-size-xxxb "><?= Yii::$app->formatter->asCurrency($due) ?></span>
            </div>
        </div>

    </div>

</div>
<!-- end Customer info -->