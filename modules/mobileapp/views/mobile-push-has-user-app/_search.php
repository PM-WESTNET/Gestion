<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\mobileapp\v1\models\AppFailedRegisterSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="app-failed-register-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">
        <div class="col-sm-6">

        </div>
        <div class="col-sm-6">

        </div>
    </div>

    <?= $this->render('@app/modules/sale/views/customer/_find-with-autocomplete', ['form' => $form, 'model' => $model, 'attribute' => 'customer_id']) ?>



    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
