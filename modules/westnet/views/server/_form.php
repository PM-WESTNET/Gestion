<?php

use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\Server */
/* @var $form yii\widgets\ActiveForm */
$clases = \app\modules\westnet\isp\IspClassFinder::getInstance()->findIsp();
$clases = array_combine($clases, $clases)
?>

<div class="server-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'status')->dropDownList([ 'enabled' => Yii::t('app', 'Enabled'), 'disabled' => Yii::t('app', 'Disabled'), ]) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'user')->textInput(['maxlength' => 255, 'autocomplete'=>'off']) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'token')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'class')->widget(Select2::className(),[
        'data' => $clases,
        'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
        'pluginOptions' => [
            'allowClear' => true
        ]
    ]);
    ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
