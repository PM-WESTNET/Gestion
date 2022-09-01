<?php 

use yii\helpers\Html;
use yii\helpers\Url;

use app\modules\config\models\Config;

use app\modules\sale\assets\AdminAsset;

AdminAsset::register($this);


/** @var Profile $profile */
if($profile) {
    $isConsumidorFinal = $profile->value;
}

?>

<?php if($is_cupon) { ?>
	<div class="container-main-coupon">
		<div class="coupon-header">
	        <div class="coupon-header-logo">
	            <?= Html::img(Yii::$app->params['path'].'/'.Yii::$app->params['web_logo'], ['alt' => 'Marca Empresa']) ?>
	        </div>
	        <p>
	        	<b>Nº: <?= str_pad($model->company->defaultPointOfSale->number, 4, "0", STR_PAD_LEFT).'-'. str_pad($model->number, 8, "0", STR_PAD_LEFT)?></b>
	    	</p>
	        <p><span>Fecha de emisión: </span><b><?=$formatter->asDate($model->date);?><b></p>
	        <p>
	            Documento no válido como factura
	        </p>
	    </div>
	    <div class="coupon-data-client">
            <div class="data-client" id="col-1">
                Señores / Razón Social: <span><b><?=(trim($model->customer->lastname)=='' ? '' : trim($model->customer->lastname) .',') .$model->customer->name?></b></span>
            </div>
            <div class="data-client" id="col-2">
                N° de Cliente: <b><span><?= $model->customer->code ?></span></b>
            </div>
	    </div>
	    <div class="coupon-table">
	    	<table>
		    	<?php
			        foreach($model->billDetails as $detail) {
			            ?>

			            <tr>
			                <td>
			                    <p><?= $detail->qty ?></p>
			                </td>
			                <td>
			                    <p><?= $detail->concept ?></p>
			                </td>
			                <td>
			                    <p>
			                        <span>$</span> <?= (($model->billType->code==1) ? round($detail->unit_net_price,2) : round($detail->unit_final_price,2) ) ?>
			                    </p>
			                </td>
			                <td>
			                    <p>
			                        <span>$</span> <?= (($model->billType->code==1) ? round($detail->getSubtotal(),2) : round($detail->getTotal(),2) ) ?>
			                    </p>
			                </td>
			                <?php
			                if ($model->billType->code==1) {
			                    ?>
			                    <td>
			                        <p>
			                            <?php
			                            if(isset($detail->product)) {
			                                foreach ($detail->product->getTaxRates()->all() as $tax) {
			                                    echo $tax->pct * 100;
			                                }
			                            } elseif($detail->unit_net_price > 0) {
			                                $pct = abs(1 - ($detail->unit_final_price / $detail->unit_net_price));
			                                echo $pct * 100;
			                            } else {
			                                echo 0;
			                            }
			                            ?><span>%</span>
			                        </p>
			                    </td>
			                    <td>
			                            <span>$</span> <?= round($detail->getTotal(), 2) ?>
			                        </p>
			                    </td>
			                    <?php
			                }
			                ?>
			            </tr>
			            <?php
			        }
			    ?>
			</table>
			<div class="content">
				<div class="mind-content">
					<div class="total-amount">
			            <div class="amount">
			            	<b><span>TOTAL: $ <?= round($model->calculateTotal(),2)?></span></b>
			            </div>
			        </div>

		            <div>
		                <?= $model->observation? 'Observación:'. $model->observation : ''?>
		            </div>

		            <div>
		                Puede retirar su factura en: <?= Config::getValue('general_address') ?>
		            </div>

			            <div>
			                Medios de Pago:
			            </div>

			        <div>
			            <div>
			                <ul>
			                    <li> <?= Config::getValue('pdf_bill_payment_methods')?> </li>
			                </ul>
			            </div>
			        </div>
			    </div>
	        
		        <div class="barcode">
		           <div class="barcode-img">
			            <?='<img src="data:image/png;base64,' . base64_encode($barcode->getBarcode($model->customer->payment_code, $barcode::TYPE_CODABAR, 3, 50)) . '">';?>
			            <div class="text_payment_code">
		        			<?= $model->customer->payment_code ?>      
		        		</div>
			        </div>
		            <div class="barcode-data">
		                <p>CÓDIGO DE PAGO:<br><?= $model->customer->payment_code ?></p>
		                <?php if($debt < 0) { ?>
		                    <p>IMPORTE AL <?php echo (new \DateTime('now'))->format('d/m/Y') . ": " . Yii::$app->formatter->asCurrency(abs($debt)) ?></p>
		                <?php } ?>
		            </div>
		        </div>
		    </div>
    	</div>




    </div>
<?php } else { ?>
	<div class="container-main">

		<div class="header">
			<div class="logo-header">
				<div class="logo">
					<?= Html::img(Yii::$app->params['path'].'/'.Yii::$app->params['web_logo'], ['alt' => 'Marca Empresa']) ?>
				</div>
				<div class="subtitles-logo">
					<p>
						<b><?= $company->taxCondition->name; ?></b> <br>
						<?= $company->address; ?>
					</p>
		        </div>	
			</div>

			<div class="code-header">
				<div class="subcode-header">
					<h1><?=substr($model->billType->name, -1)?></h1>
				</div>
		        <p>Código: <?=$model->billType->code?></p>
			</div>

			<div class="data-header">
				<h3>COMPROBANTE ELECTRÓNICO</h3>
	            <h3><?=substr($model->billType->name,0,strlen($model->billType->name)-1)?> <?=sprintf("%04d", $model->getPointOfSale()->number) . "-" . sprintf("%08d", $model->number )?></h3>
	            <p>Fecha de emisión: <b><?=$formatter->asDate($model->date);?></b> <br>
	            CUIT: <b><?= $companyData->tax_identification ?></b> <br>
	            Ingresos Brutos: <b>Nro <?= $companyData->iibb ?></b> <br>
	            Inicio de actividades: <b><?= $companyData->start ?></b></p>
			</div>
		</div>

		<div class="sub-header">
			<div class="col-personal-data">
				<div>
		        	Señores / Razón Social: <b><span><?=(trim($model->customer->lastname)=='' ? '' : trim($model->customer->lastname) .',') .$model->customer->name?></span></b>
		        </div>
		        <div>
	                Domicilio:
	                <?php if(!$isConsumidorFinal) { ?>
	                    <b><span><?=($model->customer->address ? $model->customer->address->shortAddress: '' )?></span></b>
	                <?php } ?>
		        </div>
		        <div>
		            <?php if(!$isConsumidorFinal) { ?>
		                <?=$model->customer->documentType ? $model->customer->documentType->name : Yii::t('app','Document')?>: <b><span><?=$model->customer->document_number?></span></b>
		            <?php } ?>
		        </div>
			</div>
			<div class="col-data-client">
				<div>
		            <?php if(!$isConsumidorFinal) { ?>
		                N° de Cliente: <b><span><?= $model->customer->code ?></span></b>
		            <?php } ?>
		        </div>
		        <div>IVA: <b><span><?= $model->customer->taxCondition->name ?></span></b></div>
		        <div>Condición de Venta:<b><span> Cuenta corriente </span></b></div>
		        <div class="barcode-img">
		            <?='<img src="data:image/png;base64,' . base64_encode($barcode->getBarcode($model->customer->payment_code, $barcode::TYPE_CODABAR, 3, 50)) . '">';?>
		            <div class="text_payment_code">
	        			<?= $model->customer->payment_code ?>      
	        		</div>
		        </div>
			</div>
		</div>

		<div class="content">
			<div class="table-content">
					<table>
						<tr>
							<th>CANTIDAD</th>
							<th>DESCRIPCIÓN</th>
						<th>PRECIO UNITARIO</th>
						<th>SUBTOTAL</th>
						<?php if ($model->billType->code==1): ?>
							<th>
								IVA<br>ALICUOTA
							</th>
							<th>
								SUBTOTAL<br> C/IVA
							</th>
						<?php endif ?>
					</tr>

					<?php foreach ($model->billDetails as $detail): ?>
						<tr>
							<td>
								<p><?= $detail->qty ?></p>
							</td>
							<td>
								<p><?= $detail->concept ?></p>
							</td>
							<td>
								<p>
			                    <span>$</span> <?= (($model->billType->code==1) ? round($detail->unit_net_price,2) : round($detail->unit_final_price,2) ) ?>
			                	</p>
							</td>
							<td>
								<p>
			                    	<span>$</span> <?= (($model->billType->code==1) ? round($detail->getSubtotal(),2) : round($detail->getTotal(),2) ) ?>
			                	</p>
							</td>
							<?php if ($model->billType->code==1): ?>
								<td>
									<p>
				                        <?php
				                        if(isset($detail->product)) {
				                            foreach ($detail->product->getTaxRates()->all() as $tax) {
				                                echo $tax->pct * 100;
				                            }
				                        } elseif($detail->unit_net_price > 0) {
				                            $pct = abs(1 - ($detail->unit_final_price / $detail->unit_net_price));
				                            echo $pct * 100;
				                        } else {
				                            echo 0;
				                        }
				                        ?><span>%</span>
			                   	 	</p>
								</td>
								<td>
									<p>
			                        	<span>$</span> <?= round($detail->getTotal(), 2) ?>
			                    	</p>
								</td>
							<?php endif ?>
						</tr>
						
					<?php endforeach ?>
				</table>
			</div>
			<div class="mind-content">
				<div class="total-amount">
					<?php $discount = $model->totalDiscountWithTaxes();
			        if($discount > 0) { ?>
			            <div><h5>Descuento aplicado:</h5></div>
			            <div><h5><span>$</span> <?= round($discount,2)?></h5></div>
			        <?php } ?>
			        <div class="amount"><span>TOTAL: $ <?= round($model->calculateTotal(),2)?></span></div>
				</div>
				<div class="data-account">
					<div><?= $model->observation? 'Observación: '. $model->observation : ''?></div>
			   		<!-- <div>Puede retirar su factura en: <?php 
					// echo $model->company->address  ?></div> -->
					    <?php if (!$model->hasDirectDebit()):?>
					        <div>Medios de Pago:</div>
					        <div><ul><li> <?= Config::getValue('pdf_bill_payment_methods')?> </li></ul></div>
				    	<?php else: ?>
					        <div><?php echo Config::getValue('direct_debit_bill_text')?></div>
				    	<?php endif;?>
		    		</div>
		    	</div>
	    		<div class="barcode">
		        	<div class="barcode-img">
			            <?='<img src="data:image/png;base64,' . base64_encode($barcode->getBarcode($model->customer->payment_code, $barcode::TYPE_CODABAR, 3, 50)) . '">';?>
			            <div class="text_payment_code">
		        			<?= $model->customer->payment_code ?>      
		        		</div>
		        	</div>

			        <div class="barcode-data">
			            <p>CÓDIGO DE PAGO:<br><?= $model->customer->payment_code ?></p>
			            <?php if($debt < 0) { ?>
			                <p>IMPORTE AL <?php echo (new \DateTime('now'))->format('d/m/Y') . ": " . Yii::$app->formatter->asCurrency(abs($debt)) ?></p>
			            <?php } ?>
			        </div>
	    		</div>
	    	</div>
	   	<div class="footer">
		    <div>
		        <?php
		        $bill_type_code = $model->billType->code ;
		        if ($bill_type_code == 1 || $bill_type_code == 3 ||$bill_type_code == 8 || $bill_type_code == 2) { ?>
		        <div>
		            <div>
		                <h5>I.V.A. 21%:</h5>
		            </div>
		            <div>
		                <h5><span>$</span> <?=round($model->calculateTaxes(),2)?></h5>
		            </div>
		        </div>
		        <div>
		            <div>
		                <h5>Importe neto:</h5>
		            </div>
		            <div>
		                <h5><span>$</span> <?=round($model->calculateAmount(),2)?></h5>
		            </div>
		        </div>
		    <?php } ?>
		</div>
		<?php if($model->billType->invoice_class_id) { ?>
	    <div class="footer-barcode">
	    	<div id="footer-col-1"><?= Html::img($qrCode->writeDataUri(), ['alt' => 'Código QR', 'style' => 'width:100px;']) ?></div>
	    	<div id="footer-col-2">
	    		
				<h5>
					<!-- <= Html::img(Yii::$app->params['path'].'/'.Yii::$app->params['logo-afip'], ['alt' => 'Logo Afip', 'style'=>'width:100px;']) ?> <br> -->
					<b>Comprobante Autorizado </b>
				</h5>
	    	</div>
	    	<div id="footer-col-3">
	    		<div id="cae"><b>C.A.E. Nº:</b> <?=$model->ein ?></div>
				<div id="date-vto-cae"><b>Fecha de Vencimiento de C.A.E.:</b> <?=$formatter->asDate($model->ein_expiration);?></div>
	    	</div>
	    </div>
	    <?php } ?>
	<?php } ?>
</div>