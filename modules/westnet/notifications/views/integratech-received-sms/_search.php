<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\westnet\notifications\NotificationsModule;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\notifications\models\search\IntegratechReceivedSms */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="integratech-received-sms-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="col-sm-12">
        <div class="col-sm-6">
            <div class="input-group" style="z-index:0;">
                <?php
                echo $this->render('@app/modules/sale/views/customer/_find-with-autocomplete', ['form' => $form, 'model' => $model, 'attribute' => 'customer_id']);
                ?>
            </div>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'message') ?>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'fromDate')->widget(yii\jui\DatePicker::className(), [
                    'language' => Yii::$app->language,
                    'model' => $model,
                    'attribute' => 'date',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options'=>[
                        'class'=>'form-control dates',
                        'id' => 'from-date'
                    ]
                ]);
                ?>
            </div>
            <div class="col-sm-6">

                <?= $form->field($model, 'toDate')->widget(yii\jui\DatePicker::className(), [
                    'language' => Yii::$app->language,
                    'model' => $model,
                    'attribute' => 'date',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options'=>[
                        'class' => 'form-control dates',
                        'id' => 'to-date'
                    ]
                ]);
                ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(NotificationsModule::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
