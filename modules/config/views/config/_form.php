<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\config\ConfigModule;

/* @var $this yii\web\View */
/* @var $model app\modules\config\models\Config */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="config-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'item_id')->textInput() ?>

    <?= $form->field($model, 'value')->textarea(['rows' => 6]) ?>

    
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? ConfigModule::t('config', 'Create') : ConfigModule::t('config', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
