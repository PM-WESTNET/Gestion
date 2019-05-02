<?php

use app\modules\sale\models\TaxRate;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var app\modules\checkout\models\Payment $model
 */
/** @var \app\modules\sale\models\Company $company */
$company = $model->company;
$formatter = Yii::$app->formatter;
?>

<!-- Estructura Factura Electronica -->
<table id="container_fact" style="width:850px;border:1px solid black;">
    <tbody>
    <!-- Header Factura -->
    <tr style="display: inline-block; border-bottom: 2px solid #D7D7D7; margin-top:20px; padding-bottom: 20px;">

        <!-- Info empresa -->
        <td style="width:400px; text-align: center;">
            <div style="margin: 0 0 20px 0; width: 200px; height: 200px; text-align: center; display: inline-block;">
                <img style="height: 100px; display: inline-block;" src="<?= Url::base(true) . "/" . ($company->getLogoWebPath() )  ?>" alt="Marca Empresa">
            </div>
            <div style="">
               <!-- <p style="margin: 5px 0; text-transform: capitalize;"><?php //echo$company->name?></p> -->
                <p style="margin: 5px 0; text-transform: capitalize;"><?=$company->address . ($company->phone? ' | Tel: '.$company->phone : '')?></p>
            </div>
        </td>

        <!-- Tipo de Facutra -->
        <td style="width:50px;padding: 10px;height: 50px;width: 50px;">
            <h1 style="text-align: center;border: 2px solid lightgray; padding:3px; font-size: 38px;width: 100%;font-weight: 800;margin: 0;display: inline-block;padding-bottom: 10px;padding-top: 10px;">
                X
            </h1>
            <p style="margin: 50px 0;text-align: center;">
                Documento no válido como factura
            </p>
        </td>

        <!-- Legales header -->
        <td style="width:400px; padding-left: 80px;">
            <h1 style="font-size: 24px;font-weight: 800;margin: 1 0px 0;text-align: center;">RECIBO</h1>
            <p style="font-size: 18px;margin: 20px 0;font-weight: 400; text-align: center;"><?= str_pad($model->company->defaultPointOfSale->number, 4, "0", STR_PAD_LEFT).'-'. str_pad($model->number, 8, "0", STR_PAD_LEFT)?></p>
            <p style="margin: 5px 0;font-weight: 800; text-align: center;"><span style="padding-right: 10px;font-weight: 400;">Fecha de emisión:</span><?=$formatter->asDate($model->date)?></p>
<!--            <p style="margin: 5px 0;font-weight: 800; text-align: center;"><span style="padding-right: 10px;font-weight: 400; ">CUIT:</span><=$company->tax_identification?></p>-->
<!--            <p style="margin: 5px 0;font-weight: 800; text-align: center;"><span style="padding-right: 10px;font-weight: 400;">Ingresos Brutos: </span><=$company->iibb?></p>-->
<!--            <p style="margin: 5px 0;font-weight: 800; font-size: 8px; text-align: center;"><span style="padding-right: 10px;font-weight: 400;">Inicio de actividades:</span><=Yii::$app->formatter->asDate($company->start)?></p>-->
        </td>
    </tr>

    <!-- Datos clientes a Facturar -->
    <tr>
        <td>
            <p style="margin-top: 5px; padding-left: 20px; font-weight: 800;">Cliente:  <span style="font-weight: 400;"><?=$model->customer->fullName?></span></p>
            <p style="margin-top: 5px; padding-left: 20px; font-weight: 800;">Dirección: <span style="font-weight: 400;"><?=$model->customer->address->shortAddress?></span></p>
            <p style="margin-top: 5px; padding-left: 20px; font-weight: 800;">IVA: <span style="font-weight: 400;"><?=$model->customer->taxCondition->name?></span></p>
        </td>
    </tr>
    </tr>

    <!-- Tabla de items 1 -->
    <tr style="border-top: 2px solid #D7D7D7;">
        <td>
            <p style="margin-top: 5px; padding-left: 20px; font-weight: 800;">En concepto de:  <span style="font-weight: 400;"> Servicios de Internet.</span></p>
            <p style="margin-top: 5px; padding-left: 20px; font-weight: 800;">Fecha:  <span style="font-weight: 400;"> <?= $model->date?>.</span></p>
            <p style="margin-top: 5px; padding-left: 20px; font-weight: 800;">Importe:  <span style="font-weight: 400;"> <?=$formatter->asCurrency($model->amount)?></span></p>
        </td>
    </tr>
    <tr>
        <td><p style="margin-top: 5px; padding-left: 20px; font-weight: 800;"> Detalle:</p></td>
    </tr>



    <!-- Tabla de items 2 -->
    <tr style=" border-top: 2px solid #828282; ">
        <td style="width: 30%; float: left; min-height: 20px;">
            <h1 style="padding-left: 20px ;font-size:  12px; color: #828282;">MEDIOS DE PAGO</h1>
        </td>
        <td style="width: 40%; float: left; min-height: 20px;">
            <h1 style="padding-left: 20px ;font-size: 12px; color: #828282;"">&#160;</h1>
        </td>
        <td style="width: 30%; float: left; min-height: 20px;">
            <h1 style="padding-left: 20px ;font-size: 12px; color: #828282;">IMPORTE</h1>
        </td>
    </tr>

    <?php
        foreach($model->paymentItems as $item) {
    ?>
            <tr>
                <td style="width: 30%; float: left;">
                    <h1 style="padding-left: 20px ; font-size: 12px;">
                        <?php echo $item->paymentMethod->name .
                            ($item->moneyBoxAccount ? " - " . $item->moneyBoxAccount->moneyBox->name : '' ) .
                            ($item->moneyBoxAccount ? " - " . $item->moneyBoxAccount->number : '' ) .
                            ($item->number ? " - " . $item->number : '' ) . " - " . $item->description;
                        ?>
                    </h1>
                </td>
                <td style="width: 30%; float: left;">
                    <h1 style="font-size: 12px; color: #828282;"">&#160;</h1>
                </td>
                <td style="width: 29%; float: left; min-height: 20px;text-align: center;">
                    <h1 style="padding-left: 20px ;font-size: 12px;">
                        <?= $formatter->asCurrency($item->amount) ?>
                    </h1>
                </td>
            </tr>
    <?php
        }
    ?>

    <tr style=" border-top: 1px solid #828282;">
        <td style="width: 30%; float: left;  display: block;">
            <h1 style="font-size: 12px;  padding-left: 20px ; color: #828282;">FECHA</h1>
        </td>
        <td style="width: 40%; float: left; display: block;">
            <h1 style=" font-size: 12px;  padding-left: 20px ;color: #828282;">CONCEPTO A COBRAR</h1>
        </td>
        <td style="width: 30%; float: left; display: block;">
            <h1 style=" font-size: 12px;  padding-left: 20px ;color: #828282;">IMPORTE</h1>
        </td>
    </tr>

    <?php
    $concepts = $model->billHasPayments;
    // Si no tengo comprobante asociado pongo un solo concepto con el total pagado
    if (empty($concepts)) {
        $billPayment = new \app\modules\checkout\models\BillHasPayment();
        $billPayment->amount = $model->amount;
        $concepts[] = $billPayment;
    }

    // Itero los comprobantes/conceptos para ponerlos.
    foreach ($concepts as $concept ) {

        ?>
        <tr>
            <td style="width: 30%; float: left; min-height: 20px;">
                <h1 style=" padding-left: 20px ; font-size: 12px;margin: 5px 0;">
                    <?php
                    if ($concept->bill) {
                        echo $formatter->asDate($concept->bill->date);
                    } else {
                        echo $formatter->asDate($model->date);
                    }
                    ?>
                </h1>
            </td>
            <td style="width: 40%; float: left; min-height: 20px;">
                <h1 style=" padding-left: 20px ;center; font-size: 12px; margin: 5px 0; padding-left:5px">
                    <?php
                    if ($concept->bill) {
                        echo ($concept->bill ? ($concept->bill->billType ? $concept->bill->billType->name ." - " : "" ) . $concept->bill->number : "" );
                    } else {
                        echo Yii::t('app', 'Payment to Account');
                    }
                    ?>
                </h1>
            </td>
            <td style="width: 30%; float: left; min-height: 20px;">
                <h1 style=" padding-left: 20px ;font-size: 12px;margin: 5px 0;">
                    <?php
                    if ($concept->bill) {
                        echo $formatter->asCurrency($concept->amount);
                    } else {
                        echo $formatter->asCurrency($model->amount);
                    }
                    ?>
                </h1>
            </td>
        </tr>
    <?php } ?>

    <!-- Aclaración y Firma -->
    <tr style="">
        <td style="width: 40%; float:left; font-size: 14px; margin-top: 80px; margin-bottom: 30px;">
            <p style="text-align: center;">________________________________________</p>
	    <?php if($company->company_id !== 4): ?>	
            	<p style="text-align: center;">por <?=$company->name?></p>
	    <?php endif;?>
		
        </td>
        <td style="width: 30%;  float: left; min-height: 20px;">
            <h1 style="text-align: right; padding-right: 10px; margin-top: 80px; font-weight: 800; font-size: 14px;">Total recibo</h1>
        </td>
        <td style="width: 30%;  float: left; min-height: 20px; ">
            <h1 style=" font-size: 14px; font-weight: 800; margin-top: 80px;">
                <?=$formatter->asCurrency($model->amount)?>
            </h1>
        </td>
    </tr>


    <tr>
        <td style="width: 50%; float:left; font-size: 12px; margin-top: 10px; margin-bottom: 30px;">
            <p style="padding-right: 20px; padding-left: 20px;">Valores que serán acreditados una vez hecho el efectivo.</p>
	    <p style="padding-right: 20px; padding-left: 20px;">Recuerde que puede retirar su factura en nuestras oficinas.</p>	
        </td>
    </tr>
    </tbody>
</table>
