<?php 
use kartik\select2\Select2;
use kartik\daterange\DateRangePicker;
use yii\helpers\Html;
?>

<?= Html::beginForm(['siro/checker-of-payments'], 'post', ['enctype' => 'multipart/form-data']) ?>
	<div>
		<h1>Contrastador de Intenciones de Pagos</h1>
	</div>
	<hr>
	<div class="row">
		<div class="col-lg-4">
			<?=Select2::widget([
			    'name' => 'company_id',
			    'data' => ['2' => 'Redes del Oeste', '7' => 'Servicargas'],
			    'options' => [
			        'placeholder' => 'Seleccionar empresa...',
			    ],
			]); ?>
		</div>
		<div class="col-lg-4">
		    <input type="date" class="form-control" id="date_from" name="date_from">
		</div>
		<div class="col-lg-4">
			<input type="date" class="form-control" id="date_to" name="date_to">
		</div>
	</div>
	<br>
	<div class="btn btn-success">
	    <input type="submit" class="btn btn-success" name="enviar" value="Confirmar">
	</div>

	<?php if(isset($dataProvider)): ?>
		<?=  GridView::widget([
		        'dataProvider' => $dataProvider,
		        'columns' => [
		            ['class' => 'yii\grid\SerialColumn'],
		            'PagoExitoso',
		            'MensajeResultado', 
		            'FechaOperacion',
		            'FechaRegistro',
		            'IdOperacion',
		            'Estado',
		            'idReferenciaOperacion',
		            'siro_payment_intention_id',
		            'customer_id',
		            'hash',
		            'reference',
		            'url',
		            'createdAt',
		            'updatedAt',
		            'status',
		            'estado',
		            'payment_id'
		            ],
		    ]); ?>
	<?php endif; ?>

<?= Html::endForm() ?>