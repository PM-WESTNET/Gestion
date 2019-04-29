<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use app\modules\accounting\models\Account;
use kartik\widgets\Select2;
use app\modules\westnet\ecopagos\EcopagosModule;
use yii\helpers\ArrayHelper;
use app\modules\westnet\ecopagos\models\Status;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\Ecopago */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ecopago-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'limit')->textInput() ?>

    <?= $form->field($model, 'number')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'status_id')->dropdownList(Status::getStatusesForSelect(), ['encode' => false, 'separator' => '<br/>', 'prompt' => EcopagosModule::t('app', 'Select an option...')]) ?>

    <!-- Commission -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= EcopagosModule::t('app', 'Commission'); ?></h3>
        </div>
        <div class="panel-body">
            <?= $form->field($model, 'commission_type')->dropdownList(app\modules\westnet\ecopagos\models\Commission::fetchCommissionTypes(), ['encode' => false, 'separator' => '<br/>', 'prompt' => app\modules\westnet\ecopagos\EcopagosModule::t('app', 'Select an option...')]) ?>

            <?= $form->field($model, 'commission_value')->textInput() ?>
        </div>
    </div>
    <!-- end Commission -->

    <!-- Account -->
    <?php if (Yii::$app->getModule("accounting")) : ?>
        <div class="form-group <?= (!empty($model->getErrors('account_id'))) ? 'has-error' : '' ; ?>">
            <?= Html::label($model->getAttributeLabel('account_id'), ['account_id']) ?>
            <?=
            Select2::widget([
                'model' => $model,
                'attribute' => 'account_id',
                'data' => yii\helpers\ArrayHelper::map(Account::getForSelect(), 'account_id', 'name'),
                'options' => ['placeholder' => EcopagosModule::t("app", "Select an account..."), 'encode' => false],
                'pluginOptions' => [
                    'allowClear' => true
                ]
            ]);
            ?>
            <?php if(!empty($model->getErrors('account_id'))) : ?>
                <div class="help-block"><?= $model->getErrors('account_id')[0]; ?></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <!-- end Account -->

    <?php
        echo $form->field($model, 'provider_id')->label(isset($label) ? $label : null)->widget(Select2::classname(), [
            'options' => ['placeholder' => Yii::t('app', 'Search')],
            'initValueText' => ($model->provider ? $model->provider->name : '' ),
            'pluginOptions' => [

                'allowClear' => true,
                'minimumInputLength' => 3,
                'ajax' => [
                    'url' => Url::to(['/provider/provider/find-by-name']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {name:params.term, id:$(this).val()}; }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(provider) { return provider.text; }'),
                'templateSelection' => new JsExpression('function (provider) { return provider.text; }'),
                'cache' => true
            ],
        ]);

        ?>
        <?php if(!empty($model->getErrors('account_id'))) : ?>
            <div class="help-block"><?= $model->getErrors('account_id')[0]; ?></div>
        <?php endif; ?>
    </div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
