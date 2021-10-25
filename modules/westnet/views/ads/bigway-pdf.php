<?php

use yii\helpers\Html;
use yii\helpers\Url;

use app\modules\config\models\Config;

use app\modules\sale\assets\AdminAsset;

AdminAsset::register($this);
//'<img src="data:image/png;base64,' . base64_encode($barcode->getBarcode($init_value, $barcode::TYPE_CODABAR, 3, 60)) . '">'; //change before production

//include('bigway-pdf.html');
?>
<?php foreach ($codes as $key => $code){ ?>
<?php
	if($key!=0){
		echo '<div style="page-break-after: always"></div>';
	}
?>

<div class="body">
	<div class="sections-wrapper">
		<div class="section bg-white">

			<div class="header">
				<div class="float-left w-50">
					<?= Html::img(Yii::$app->params['path'] . '/' . 'logo-bigway.png', [
						'alt' => 'Logo',
						'class' => 'company-logo'
					])
					?>
					<!-- 
						Aspect Ratio:
						width: 330px;
						height: 141px;
						box-shadow: 0px 0px 15px #8a8a8a;
					-->
				</div>

				<div class="header-data-container float-right w-50">
					<!--convert to variables and retrieve from db-->
					<div class="barcode center-div">
					</div>
					<div>
						<p class="text-center spaced-letters">
							FICHA EMPRESA <?php echo $date_now ?>
						</p>
					</div>
					<div class="center-div pill-container-horizontal">
						<div class="spaced-letters left float-left">
							FECHA
						</div>
					</div>
					<div class="center-div pill-container-horizontal">
						<!--convert to variables and retrieve from db-->
						<div class="" style="overflow: hidden;">
							<div class="spaced-letters left float-left" style="float:left;">
								CLIENTE
							</div>
							<div style="padding: 5px;margin-left: 60px;float:right;"><?= $init_value ?></div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
		<div class="section bg-white">
			<div class="pill-container-vertical">
				<div class="top spaced-letters">
					CLIENTE
				</div>
				<div class="bottom">
					<div class="float-left w-50">
						<div class="center-div pill-container-horizontal">
							<div class="spaced-letters left float-left">
								NOMBRE
							</div>
						</div>
						<div class="center-div pill-container-horizontal">
							<div class="spaced-letters left float-left">
								APELLIDO
							</div>
						</div>
						<div class="center-div pill-container-horizontal">
							<div class="spaced-letters left float-left">
								TELEFONO
							</div>
						</div>
						<div class="center-div pill-container-horizontal">
							<div class="spaced-letters left float-left">
								EMAIL
							</div>
						</div>
						<div class="center-div pill-container-horizontal">
							<div class="spaced-letters left float-left">
								CUIT
							</div>
						</div>
					</div>
					<div class="float-right w-50">
						<div class="center-div pill-container-vertical-child">
							<div class="top spaced-letters">
								DOMICILIO DE INSTALACION
							</div>
							<div class="bottom">
							</div>
						</div>
						<div class="center-div pill-container-horizontal">
							<div class="spaced-letters left float-left">
								ZONA
							</div>
						</div>
						<div class="center-div pill-container-horizontal">
							<div class="spaced-letters left float-left">
								C.P
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>

		<div class="section bg-white">
			<div class="pill-container-vertical">

				<div class="top spaced-letters">
					<div class="justify-right float-left">
						DATOS DE INSTALACION
					</div>
					<!--convert to variables and retrieve from db-->
					<div class="float-right instalation-percentages">
						CIR 75%
						<br>
						DISPONIBILIDAD ANUAL 96%
					</div>
				</div>
				<div class="bottom padding-top-bottom">
					<div class="items-container float-left">
						<div class="title spaced-letters text-center">
							ENTREGA EN COMODATO
						</div>
						<!-- php variable echoing -->
						<div class="items">
							<!-- php variable echoing -->
							<div class="checkbox">
								<input type="checkbox">
								<label>ONU</label>
							</div>
							<div class="checkbox">
								<input type="checkbox">
								<label>Roseta</label>
							</div>
							<div class="checkbox">
								<input type="checkbox">
								<label>Patchcord</label>
							</div>
						</div>
					</div>
					<div class="float-left items-container h-custom">
						<div class="title spaced-letters text-center">
							TIPO DE CONEXION
						</div>
						<div class="items">
							<!-- php variable echoing -->
							<div class="checkbox ">
								<input type="checkbox">
								<label>Fibra Óptica</label>
							</div>
							<div class="checkbox second-level">
								<input type="checkbox">
								<label>Hogar</label>
							</div>
							<div class="checkbox second-level">
								<input type="checkbox">
								<label>Empresa</label>
							</div>
						</div>
					</div>
					<div class="items-container float-left">
						<div class="title spaced-letters text-center">
							VELOCIDAD DEL PLAN
						</div>
						<div class="items">
							<!-- php variable echoing -->
							<div class="checkbox">
								<input type="checkbox">
								<label>25MB</label>
							</div>
							<div class="checkbox">
								<input type="checkbox">
								<label>50MB</label>
							</div>
							<div class="checkbox">
								<input type="checkbox">
								<label>100MB</label>
							</div>
							<div class="checkbox">
								<input type="checkbox">
								<label>300MB</label>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="section">
			<div class="pill-container-vertical bg-white float-left w-50">
				<div class="top spaced-letters">
					ACEPTACION DEL SERVICIO
				</div>
				<div class="bottom">

					<ol>
						<li>No se realizan periodos de prueba</li>
						<li>Los equipos instalados son en comodato.</li>
						<li>Firmando acepta la forma y estetica de la instalación, modificaciones posterioriores tendrán recargo adicional.</li>
					</ol>

				</div>
			</div>
			<div class="pill-container-vertical bg-white float-right w-50">
				<div class="top spaced-letters">
					CONTACTOS
				</div>
				<div class="bottom">
					<ul>
						<li>Servicio técnico: 2613439030</li>
						<li>Administración: 2615884223</li>
						<li>Correo electrónico: administracion@bigway.com.ar</li>
						<li>Horarios de atención:</li>
						<ul>
							<li>Lunes a Viernes de 8:30 a 17:00
							</li>
							<li>Sabados de 8:30 a 13:00hs</li>
						</ul>
					</ul>
				</div>
			</div>
		</div>

		<div class="section footer bg-white no-margin-bottom">
			<div class="pill-container-vertical">
				<div class="top spaced-letters">
					FIRMA Y ACLARACION
				</div>
				<div class="bottom">
					<div class="w-50 float-left">
						<div class="center-div spaced-letters signature">
							Cliente
						</div>
					</div>
					<div class="w-50 float-left">
						<div class="center-div spaced-letters signature">
							Instalador
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>

<?php } ?>
