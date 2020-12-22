<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\IpRank */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ip-rank-form">

    <?php $form = ActiveForm::begin(); ?>



    <?= $form->field($model, 'net_address')->textInput(['maxlength' => 45]) ?>

    
    <?= $form->field($model, 'node_id')->dropdownList(yii\helpers\ArrayHelper::map(\app\modules\westnet\models\Node::find()->all(), 'node_id', 'name'),['encode'=>false, 'separator'=>'<br/>','prompt'=>'Select an option...']) ?>

    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
