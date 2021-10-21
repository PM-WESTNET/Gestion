<?php

use yii\helpers\Html;
use yii\helpers\Url;

use app\modules\config\models\Config;

use app\modules\sale\assets\AdminAsset;

AdminAsset::register($this);

//include('bigway-pdf.html');
?>
<div class="body">
	<div class="sections-wrapper">
		<div class="section">

			<div class="header">
				<div class="company-logo float-left w-50">
					< logo img >
				</div>
				<div class="header-data-container float-right w-50">
					<div class="barcode">
						< codigo de barras >
					</div>
					<div>
						FICHA EMPRESA <?php echo $date_now ?>
					</div>
					<div>
						FECHA
					</div>
					<div>
						CLIENTE
					</div>
				</div>
			</div>
		</div>
		<div class="section">
			<div class="pill-container-vertical">
				CLIENTE
			</div>

		</div>
		<div class="section">
			<div class="pill-container-vertical">
				DATOS
			</div>
		</div>
		<div class="section">
			<div class="pill-container-vertical">
				ACEPTACION DEL SERVICIO
			</div>
			<div class="pill-container-vertical">
				CONTACTOS
			</div>
		</div>

		<div class="section footer">
			<div class="pill-container-vertical">
				FIRMA Y ACLARACION
			</div>
		</div>
	</div>

</div>