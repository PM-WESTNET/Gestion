<?php

use app\modules\log\models\Log;
use app\modules\westnet\notifications\models\Notification;
use kartik\widgets\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use webvimark\modules\UserManagement\models\User;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\search\LogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="log-search">

    <?php
    $form = ActiveForm::begin([
        'action' => ['index-programmed'],
        'method' => 'get',
    ]);
    ?>
    <div class="row">


        <div class="col-sm-6">
            <?= $form->field($model, 'notification_id')->textInput(); ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'name')->textInput(); ?>
        </div>

        <div class="col-sm-6">
            <label class="control-label"><?= Yii::t('app', 'From Date Created') ?></label>
            <?= DatePicker::widget([
                'model' => $model,
                'attribute' => 'create_timestamp_from',
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-sm-6">
            <label class="control-label"><?= Yii::t('app', 'To Date Created') ?></label>
            <?= DatePicker::widget([
                'model' => $model,
                'attribute' => 'create_timestamp_to',
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'status')->widget(Select2::class, [
                'data' => Notification::staticFetchStatuses(),
                'pluginOptions' => [
                    'allowClear' => true,
                    'placeholder' => Yii::t('app', 'Select an option...')
                ]
            ])?>
        </div>
    </div>
    <br>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
