<?php

use app\modules\accounting\models\MoneyBox;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\paycheck\models\Paycheck */
/* @var $form yii\widgets\ActiveForm */

$states = [];
foreach( $model->getPossibleStates() as $key=>$value){
    $states[$value] = Yii::t('paycheck', $value);
}
?>

<div class="paycheck-form">

    <?php $form = ActiveForm::begin(['id'=>'stateForm']); ?>
    <?= Html::hiddenInput('Paycheck[paycheck_id]', $model->paycheck_id)?>

    <div class="form-group field-provider-account hidden-print">
        <?=Html::label(Yii::t('app', "Status"), ['status'])?>
        <?= Select2::widget([
            'model' => $model,
            'attribute' => 'status',
            'data' => $states,
            'options' => ['placeholder' => Yii::t("app", "Select"), 'encode' => false, 'data-update-bill' => '' ],
        ]);
        ?>
    </div>

    <?php echo $form->field($model, 'dateStamp')
        ->widget(\yii\jui\DatePicker::classname(), ['value'=>'','language' => 'es-AR','dateFormat' => 'dd-MM-yyyy','options' => ['class' => 'form-control',],])
        ->label(Yii::t('app', 'Movement date'))
    ?>
    <?= $this->render('@app/modules/accounting/views/money-box-account/_selector', ['model' => $model, 'form' => $form, 'style' => 'horizontal']); ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => 255, 'value'=>'']) ?>
    <?php ActiveForm::end(); ?>

</div>
<?php  $this->registerJs("$('#paycheck-status').trigger('change');"); ?>