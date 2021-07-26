<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\westnet\ecopagos\models\BatchClosure */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Batch Closure',
]) . ' ' . $model->batch_closure_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Batch Closures'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->batch_closure_id, 'url' => ['view', 'id' => $model->batch_closure_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
		<div class="batch-closure-update">

		    <h1><?= Html::encode($this->title) ?></h1>

			<?php $form = ActiveForm::begin(); ?>
				<?= $form->field($model, 'observation')->textInput() ?>
				<?= $form->field($model, 'status')->dropdownList(['collected' => 'Recolectado', 'irrecoverable' => 'Irrecuperable', 'duplicate' => 'Duplicado'],['encode'=>false, 'separator'=>'<br/>','prompt'=>'Select an option...']) ?>
				<div class="form-group">
					<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
				</div>

    		<?php ActiveForm::end(); ?>

		</div>
	</div>
</div>
