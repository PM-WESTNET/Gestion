<?php

use kartik\select2\Select2;
use kartik\daterange\DateRangePicker;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

$this->title = 'Contrastador de Pagos';
$this->params['breadcrumbs'][] = $this->title;
?>

<div>
	<h1>Contrastador de Intenciones de Pagos</h1>
</div>
<hr>
<div class="container-fluid">
<?= Html::beginForm(['siro/checker-of-payments'], 'post', ['enctype' => 'multipart/form-data']) ?>

<div class="row">
	<div class="col-lg-4">
		Empresa
		<?= Select2::widget([
			'name' => 'company_id',
			'data' => $companies_arr,
			'options' => [
				'placeholder' => 'Seleccionar empresa...',
				'required' => true
			],
		]); ?>
	</div>
	<div class="col-lg-4">
		Desde
		<input type="date" class="form-control" id="date_from" name="date_from" placeholder="Desde" required>
	</div>
	<div class="col-lg-4">
		Hasta
		<input type="date" class="form-control" id="date_to" name="date_to" placeholder="Hasta" required>
	</div>
</div>
<br>
*No puede superar los 3 meses entre fecha de inicio y fin para recuperar una rendicion
<br>
*Se debe seleccionar la empresa para buscar pagos nuevos en linea
<br>
<br>
<div class="">
	<input type="submit" class="btn btn-success" name="enviar" value="Confirmar">

	<?= Html::a("<span class='glyphicon glyphicon-pencil'></span> " . "Edit companies", 
                ['siro-company-config/index'], ['class' => 'btn btn-default', 'target' => '_blank']);
    ?>
</div>
<?= Html::endForm() ?>
<?= Html::beginForm(['siro/checker-of-payments'], 'post', ['enctype' => 'multipart/form-data']) ?>
	<input type="submit" class="btn btn-danger float-right" name="cierre_masivo" value="Cierre Masivo" onclick="return confirm('Confirmar envio de formulario?\nEsta accion no puede revertirse.');">
<?= Html::endForm() ?>
</div>
<?php if (isset($dataProvider)) : ?>
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			'payment_intention_accountability_id',
			[
				'attribute' => 'customer_name',
				'label' => 'Nombre - Apellido - Codigo',
				'format' => 'text',
				'value' => function ($model) {
					return $model->customer->name . ", " . $model->customer->lastname . " - " . $model->customer->code;
				}
			],

			[
				'attribute' => 'customer_id',
				'format' => 'raw',
				'value' => function ($model) {
					return Html::a($model->customer_id, Url::toRoute(['/checkout/payment/current-account', 'customer' => $model->customer_id]));
				}
			],
			[
				'attribute' => 'company_name',
				'label' => 'Empresa',
				'format' => 'raw',
				'filter' => $companyNamesArr,
				'value' => function ($model) {
					return $model->customer->company->name;
				}
			],
			[
				'attribute' => 'siro_payment_intention_id',
				'format' => 'raw',
				'value' => function ($model) {
					return Html::a($model->siro_payment_intention_id, Url::toRoute(['/reports/reports-company/payment-intention-view', 'id' => $model->siro_payment_intention_id]));
				}
			],
			[
				'attribute' => 'total_amount',
				'format' => 'currency',
				'value' => function ($model) {
					$canBeError = ($model->total_amount <= 0) ? true : false; // if total amount is lower than 0
					return $model->total_amount;
				}
			],
			[
				'attribute' => 'is_duplicate',
				'label' => 'ES DUPLICADO?',
				'format' => 'raw',
				'value' => function ($model) {
					$si = '<span class="label label-success">SI <i class="glyphicon glyphicon-ok"></i></span>';
					$no = '<span class="label label-danger">NO <i class="glyphicon glyphicon-remove"></i></span>';

					return ($model->is_duplicate) ? $si : $no;
				}
			],
			[
				'attribute' => 'payment_method',
				'format' => 'raw',
				'filter' => $paymentMethodArr,
			],
			[
				'attribute' => 'status',
				'format' => 'raw',
				'filter' => $statusArr,
			],
			[
				'attribute' => 'collection_channel_description',
				'format' => 'raw',
				'filter' => $collectionChannelNamesArr,
				'value' => function ($model) {
					return $model->collection_channel_description;
				},
			],
			'rejection_code',
			'payment_date',
			'accreditation_date',
			[
				'attribute' => 'payment_id',
				'format' => 'raw',
				'value' => function ($model) {
					if (isset($model->payment_id))
						return Html::a($model->payment_id, Url::toRoute(['/checkout/payment/view', 'id' => $model->payment_id]));

					return null;
				}
			],
			[
				'class' => 'app\components\grid\ActionColumn',
				'template' => '{view} {confirm} {cancel}',
				'buttons' => [
					'view' => function ($url, $model, $key) {
						return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::toRoute(['/westnet/notifications/siro/view', 'id' => $model->payment_intention_accountability_id]), ['class' => 'btn btn-primary']);
					},
					'confirm' => function ($url, $model, $key) {
						if ($model->status == 'draft')
							return Html::a('<span class="glyphicon glyphicon-ok"></span>', Url::toRoute(['/westnet/notifications/siro/confirm', 'id' => $model->payment_intention_accountability_id]), ['class' => 'btn btn-success']);
					},
					'cancel' => function ($url, $model, $key) {
						if ($model->status == 'draft')
							return Html::a('<span class="glyphicon glyphicon-remove"></span>', Url::toRoute(['/westnet/notifications/siro/cancel', 'id' => $model->payment_intention_accountability_id]), ['class' => 'btn btn-danger']);
					}

				]
			]
		],

	]); ?>
<?php endif; ?>