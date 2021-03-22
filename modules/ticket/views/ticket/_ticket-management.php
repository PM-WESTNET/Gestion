<?php
use yii\helpers\Html;
?>

<div class="row">
    <div class="col-xs-12">
        <h4><?= Yii::t('app', 'Ticket management')?></h4>
        <small style="padding-bottom: 15px"><?= (new \DateTime('now'))->setTimestamp($model->timestamp)->format('d-m-Y H:i:s') .' - '. $model->user->username ?></small>
        <div class="row">
            <?= Html::checkbox(Yii::t('app', 'WhatsApp'), $model->by_wp, ['label' => Yii::t('app', 'WhatsApp'), 'disabled' => true]) ?>
        </div>
        <div>
            <?= Html::checkbox(Yii::t('app', 'SMS'), $model->by_sms, ['label' => Yii::t('app', 'SMS'), 'disabled' => true]) ?>
        </div>
        <div>
            <?= Html::checkbox(Yii::t('app', 'Email'), $model->by_email, ['label' => Yii::t('app', 'Email'), 'disabled' => true]) ?>
        </div>
        <div>
             <?= Html::checkbox(Yii::t('app', 'Call'), $model->by_call, ['label' => Yii::t('app', 'Call'), 'disabled' => true]) ?>
        </div>
    </div>
</div>
<hr>
