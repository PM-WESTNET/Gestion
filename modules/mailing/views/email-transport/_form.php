<?php

use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\EmailTransport */
/* @var $form yii\widgets\ActiveForm */
\kartik\select2\Select2Asset::register($this);
?>

<div class="email-transport-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'from_email')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'transport')->label(\app\modules\mailing\MailingModule::t('Transport'))
        ->dropDownList( \app\modules\mailing\services\ConfigMailing::getTransports() , [
            'prompt' => Yii::t('app','Select'),
            'id' => 'transport'
        ]) ?>

    <?= $form->field($model, 'host')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'port')->textInput() ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'encryption')->textInput(['maxlength' => 10]) ?>

    <?= $form->field($model, 'layout')->label(\app\modules\mailing\MailingModule::t('Layout'))
        ->dropDownList( \app\modules\mailing\services\ConfigMailing::getLayouts() , [
            'prompt' => Yii::t('app','Select'),
            'id' => 'layout'
    ]) ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'relation_class')->label(\app\modules\mailing\MailingModule::t('Relation class'))
                ->dropDownList( \app\modules\mailing\services\ConfigMailing::getRelationClases() , [
                    'prompt' => Yii::t('app','Select'),
                    'id' => 'relation_class'
                ]) ?>
        </div>
        <div class="col-md-6">
            <label class="control-label" for="emailtransport-relation_id"><?php echo \app\modules\mailing\MailingModule::t('Relation ID') ?></label>
            <?php
            echo \kartik\select2\Select2::widget([
                'name' => 'EmailTransport[relation_id]',
                'options' => ['placeholder' => ''],
                'initValueText' => $model->getText(),
                'value' => $model->relation_id,
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 1,
                    'ajax' => [
                        'url' => \yii\helpers\Url::toRoute('/mailing/email-transport/autocomplete'),
                        'dataType' => 'json',
                        'delay' => 250,
                        'data' => new JsExpression('function(params) { return {term:params.term, transport: $("#relation_class").val()}; }'),
                    ]
                ],
            ]);
            ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>