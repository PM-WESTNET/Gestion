<?php

use yii\helpers\Html;
use app\modules\westnet\ecopagos\EcopagosModule;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\DailyClosure */

$this->title = EcopagosModule::t('app', 'Daily closure preview');
$this->params['breadcrumbs'][] = ['label' => EcopagosModule::t('app', 'Daily closures'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="daily-closure-preview container">

    <h1>
        <?= Html::encode($this->title) ?>
        <small class="text-danger display-block margin-top-quarter"><span class="glyphicon glyphicon-warning-sign"></span> <?= EcopagosModule::t('app', 'You are closing the cash register. Here you can view details from the current cash register'); ?></small>
    </h1>

    <!-- Daily closure preview -->
    <div class="panel panel-default z-depth-1 z-depth-important margin-top-full">
        <div class="panel-heading">
            <h3 class="panel-title"><?= app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Batch closure details'); ?></h3>
        </div>
        <div class="panel-body">

            <ul class="list-group no-margin-bottom">

                <li class="list-group-item">
                    <span class="badge"><?= Yii::$app->formatter->asCurrency($model->total) ?></span>
                    <strong class="text-primary"><?= $model->getAttributeLabel("total"); ?></strong>
                </li>

                <li class="list-group-item">
                    <span class="badge"><?= $model->payment_count; ?></span>
                    <strong class="text-primary"><?= $model->getAttributeLabel("payment_count"); ?></strong>
                </li>

                <?php if (!empty($model->firstPayout) && !empty($model->lastPayout)) : ?>
                
                    <li class="list-group-item">
                        <span class="badge"><?= Yii::$app->formatter->asDatetime($model->firstPayout->datetime); ?></span>
                        <strong class="text-primary"><?= $model->getAttributeLabel("first_payout"); ?></strong>
                    </li>
                    <li class="list-group-item">
                        <span class="badge"><?= Yii::$app->formatter->asDatetime($model->lastPayout->datetime); ?></span>
                        <strong class="text-primary"><?= $model->getAttributeLabel("last_payout"); ?></strong>
                    </li>
                    
                <?php else : ?>
                    
                    <li class="list-group-item text-warning font-bold text-center">
                        <?= EcopagosModule::t('app', 'Payouts not found for this cash register'); ?>
                    </li>
                    
                <?php endif; ?>
            </ul>

        </div>
    </div>
    <!-- end Daily closure preview -->

    <p>
        <?=
        Html::a("<span class='glyphicon glyphicon-check'></span> " . EcopagosModule::t('app', 'Execute daily closure'), ['close'], ['id' => 'execute-button', 'class' => 'btn btn-success'])
        ;
        ?>
    </p>

</div>

