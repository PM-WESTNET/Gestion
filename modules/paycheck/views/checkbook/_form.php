<?php

use app\modules\accounting\models\MoneyBox;
use app\modules\accounting\models\MoneyBoxAccount;
use kartik\widgets\DepDrop;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\paycheck\models\Checkbook */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="checkbook-form">

    <?php $form = ActiveForm::begin(); ?>
    <?php
    echo $this->render('@app/modules/accounting/views/money-box-account/_selector', [
        'model' => $model,
        'form' => $form,
        'style' => 'vertical',
        'money_box_id_name' => 'money_box_id',
        'money_box_id_access' => 'money_box_id',
    ]); ?>


    <?= $form->field($model, 'start_number')->textInput() ?>

    <?= $form->field($model, 'end_number')->textInput() ?>

    <?= $form->field($model, 'last_used')->textInput() ?>

    <?= $form->field($model, 'enabled')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    var CheckbookForm = new function(){
        this.init = function () {
            $('#money_box_id').trigger("change");
        }
    };
</script>
<?php  $this->registerJs("CheckbookForm.init();"); ?>