<?php

use yii\helpers\Html;

?>

<div class="row">
    <div class="col-xs-12">
        <div class="col-xs-8">
            <h4><?php echo $model->title?></h4>
            <small><?php echo $model->user->username . ' - '. Yii::$app->formatter->asDatetime($model->datetime)?></small>

            <p><?php echo $model->description?></p>
        </div>
        <div class="col-xs-4">
            <?php if($model->ticket_management_id) {
                $tm = $model->ticketManagement; ?>
                <small style="padding-bottom: 15px"><?= (new \DateTime('now'))->setTimestamp($tm->timestamp)->format('d-m-Y H:i:s') .' - '. $model->user->username ?></small>
                <div class="row">
                    <?= Html::checkbox(Yii::t('app', 'WhatsApp'), $tm->by_wp, ['label' => Yii::t('app', 'WhatsApp'), 'disabled' => true]) ?>
                </div>
                <div>
                    <?= Html::checkbox(Yii::t('app', 'SMS'), $tm->by_sms, ['label' => Yii::t('app', 'SMS'), 'disabled' => true]) ?>
                </div>
                <div>
                    <?= Html::checkbox(Yii::t('app', 'Email'), $tm->by_email, ['label' => Yii::t('app', 'Email'), 'disabled' => true]) ?>
                </div>
                <div>
                    <?= Html::checkbox(Yii::t('app', 'Call'), $tm->by_call, ['label' => Yii::t('app', 'Call'), 'disabled' => true]) ?>
                </div>
            <?php } else {
                echo Html::a('<span class="glyphicon glyphicon-pushpin"></span> '. Yii::t('app', 'Register ticket management'), '#', [
                        'class' => 'btn btn-primary',
                        'id' => 'add_management_btn',
                        'data-ticket' => $model->ticket_id,
                        'data-observation' => $model->observation_id
                    ]);
            } ?>
        </div>
    </div>
</div>
<hr>
