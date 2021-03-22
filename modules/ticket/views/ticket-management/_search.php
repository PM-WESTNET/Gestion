<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\jui\DatePicker;
use yii\helpers\ArrayHelper;
use webvimark\modules\UserManagement\models\User;

/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\search\TicketManagementSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ticket-management-search">

    <?php $form = ActiveForm::begin([
        'action' => ['customer-index', 'customer_id' => $customer_id],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'date_from')->widget(DatePicker::class, [
                    'model' => $model,
                    'attribute' => 'date_from',
                    'options' => [
                        'class' => 'form-control'
                    ]
            ]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'date_to')->widget(DatePicker::class, [
                'model' => $model,
                'attribute' => 'date_to',
                'options' => [
                    'class' => 'form-control'
                ]
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'user_id')->widget(Select2::class, [
                    'data' => ArrayHelper::map(User::find()->all(), 'id', 'username'),
                    'pluginOptions' => [
                        'allowClear' => true,
                        'placeholder' => Yii::t('app', 'Select ...')
                    ]
            ]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'by_wp')->checkbox([]);?>
            <?= $form->field($model, 'by_call')->checkbox();?>
            <?= $form->field($model, 'by_email')->checkbox();?>
            <?= $form->field($model, 'by_sms')->checkbox();?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
