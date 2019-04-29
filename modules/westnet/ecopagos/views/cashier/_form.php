<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\westnet\ecopagos\EcopagosModule;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\Cashier */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cashier-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 100]) ?>

    <?= $form->field($model, 'lastname')->textInput(['maxlength' => 100]) ?>

    <?= $form->field($model, 'number')->textInput(['maxlength' => 20]) ?>

    <?=
    $form->field($model, 'document_type')->dropDownList($model->fetchDocumentTypes(), [
        'encode' => false,
        'separator' => '<br/>',
        'prompt' => EcopagosModule::t('app', 'Select an option...')
    ])
    ?>

    <?= $form->field($model, 'document_number')->textInput(['maxlength' => 20]) ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'password_repeat')->passwordInput(['maxlength' => 255]) ?>

    <?=
    $form->field($model, 'ecopago_id')->dropdownList(yii\helpers\ArrayHelper::map(\app\modules\westnet\ecopagos\models\Ecopago::find()->all(), 'ecopago_id', 'name'), [
        'encode' => false,
        'separator' => '<br/>',
        'prompt' => EcopagosModule::t('app', 'Select an option...')
    ])
    ?>

    <?=
    $form->field($model, 'status')->dropdownList(\app\modules\westnet\ecopagos\models\Cashier::staticFetchStatuses(), [
        'encode' => false,
        'separator' => '<br/>',
        'prompt' => EcopagosModule::t('app', 'Select an option...')
    ])
    ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<style>
    .new-task-init{
        display: none;
    }
</style>