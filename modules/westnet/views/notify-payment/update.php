<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\models\Node */

$this->title = Yii::t('app', 'Notify Payment') .' '. $model->notify_payment_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Notify payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
#var_dump($model,$payment_methods);die();
?>
<div class="form">
	<?php $form = ActiveForm::begin(); ?>

    	<?= $form->field($model, 'payment_method_id')->label('Método de Pago')
        ->dropDownList($payment_methods, [
            'prompt' => Yii::t('app','Select'),
            'id' => 'payment_method_id'
        ]) ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>