<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\mobileapp\v1\models\AppFailedRegisterSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="app-failed-register-search">

    <?php $form = ActiveForm::begin([
        'action' => ['view', 'mobile_push_id' => $model->mobile_push_id],
        'method' => 'post',
    ]); ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $this->render('/../../sale/views/customer/_find-with-autocomplete', ['form'=> $form, 'model' => $model, 'attribute' => 'customer_id']);?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'customer_code')->textInput()?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
