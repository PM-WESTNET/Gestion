<?php

use app\modules\zone\models\Zone;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Zone */
/* @var $form ActiveForm */
?>

<div class="zone-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'type')->label(Yii::t('app', 'Type'))->dropDownList([ 'country' => Yii::t('app', 'Country'), 'state' => Yii::t('app', 'State'), 'department' => Yii::t('app', 'Department'), 'locality' => Yii::t('app', 'Locality'), 'zone' => Yii::t('app', 'Zone'),], ['prompt' => Yii::t('app', 'Select')]) ?>

    <?=
    $form->field($model, 'parent_id')->widget(kartik\widgets\Select2::className(), [
        'data' => ArrayHelper::map(Zone::getForSelect(), 'zone_id', 'name'),
        'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false],
        'pluginOptions' => [
            'allowClear' => true
        ]
    ])
    ?>

    <?= $form->field($model, 'status')->dropDownList([ 'enabled' => Yii::t('app', 'Enabled'), 'disabled' => Yii::t('app', 'Disabled'),]) ?>

    <?= $form->field($model, 'postal_code')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
