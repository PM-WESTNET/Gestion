<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\westnet\models\IpRange;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\IpRank */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ip-rank-form">

    <?php $form = ActiveForm::begin(); ?>



    <?= $form->field($model, 'net_address')->textInput(['maxlength' => 45]) ?>

    
    <?= $form->field($model, 'status')->dropdownList([
        IpRange::ENABLED_STATUS => Yii::t('app', 'Enabled'),
        IpRange::DISABLED_STATUS => Yii::t('app', 'Disabled'),
        IpRange::AVAILABLE_STATUS => Yii::t('app', 'Available'),
    ]) ?>

    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
