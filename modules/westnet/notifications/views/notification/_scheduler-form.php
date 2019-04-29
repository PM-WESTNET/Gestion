<?php 
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>

<p class="bg-info padding-left-half">
    <span class="glyphicon glyphicon-info-sign"></span> <?= $scheduler->description() ?>
</p>

<?php
$form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?php if($scheduler::$selectDates): ?>
    <div class="row">

        <div class="col-lg-6 no-padding-left">
            <?= $form->field($model, 'from_date')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR', 'dateFormat' => 'dd-MM-yyyy', 'options' => ['class' => 'form-control datepicker'],]) ?>
        </div>

        <div class="col-lg-6 no-padding-right">
            <?= $form->field($model, 'to_date')->widget(\yii\jui\DatePicker::classname(), ['language' => 'es-AR', 'dateFormat' => 'dd-MM-yyyy', 'options' => ['class' => 'form-control datepicker'],]) ?>
        </div>

    </div>
    <?php endif; ?>

    <?php 
    $transport = $model->transport;
    if($transport->hasFeature('manyTimesPerDay')):
    ?>
    <div class="row">

        <div class="col-lg-6 no-padding-left">
            <?=
            $form->field($model, 'from_time')->widget(\kartik\time\TimePicker::classname(), [
                'pluginOptions' => [
                    'showMeridian' => false,
                    'minuteStep' => 15,
                ]
            ]);
            ?>
        </div>

        <div class="col-lg-6 no-padding-right">
            <?=
            $form->field($model, 'to_time')->widget(\kartik\time\TimePicker::classname(), [
                'pluginOptions' => [
                    'showMeridian' => false,
                    'minuteStep' => 15,
                ]
            ]);
            ?>
        </div>

    </div>   

    <?= $form->field($model, 'times_per_day')->textInput(['maxlength' => 2]) ?>

    <div id="period-times">
        <?php if (!$model->isNewRecord) : ?>
            <?= $this->render('schedule', ['schedule' => $model->calcDailyPeriod()]); ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if($scheduler::$selectDays): ?>
    <div class="row">
        <?= $form->field($model, 'monday')->checkbox() ?>
        <?= $form->field($model, 'tuesday')->checkbox() ?>
        <?= $form->field($model, 'wednesday')->checkbox() ?>
        <?= $form->field($model, 'thursday')->checkbox() ?>
        <?= $form->field($model, 'friday')->checkbox() ?>
        <?= $form->field($model, 'saturday')->checkbox() ?>
        <?= $form->field($model, 'sunday')->checkbox() ?>
    </div>
    <?php else: ?>
        <?= Html::hiddenInput('Notification[monday]', 0) ?>
        <?= Html::hiddenInput('Notification[tuesday]', 0) ?>
        <?= Html::hiddenInput('Notification[wednesday]', 0) ?>
        <?= Html::hiddenInput('Notification[thursday]', 0) ?>
        <?= Html::hiddenInput('Notification[friday]', 0) ?>
        <?= Html::hiddenInput('Notification[saturday]', 0) ?>
        <?= Html::hiddenInput('Notification[sunday]', 0) ?>
    <?php endif; ?>

<?php ActiveForm::end(); ?>