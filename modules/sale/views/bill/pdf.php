<?php

use app\modules\checkout\models\Payment;
use app\modules\sale\models\Profile;
use app\modules\sale\models\TaxRate;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\modules\config\models\Config;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\Bill $model
 */
$this->title = $model->bill_id;
$formatter = Yii::$app->formatter;
$cupon_bill_types = explode(',', \app\modules\config\models\Config::getValue('cupon_bill_types'));
$is_cupon = (array_search($model->bill_type_id, $cupon_bill_types) !==false);
$payment = new Payment();
$payment->customer_id = $model->customer_id;
$debt = $payment->accountTotal();
$isConsumidorFinal = false;

/** @var Profile $profile */
$profile = $model->customer->getCustomerProfiles()->where(['name'=>'Consumidor Final'])->one();
if($profile) {
    $isConsumidorFinal = $profile->value;
}
$company = (isset($company) ? $company : $model->customer->parentCompany );
$companyData = $model->company;
?>
<?php if($is_cupon) { ?>

    <!-- Estructura Factura Electronica -->
    <table id="container_fact" style="width:860px;border:1px solid black;font-family: Arial, Helvetica, sans-serif;">
        <tbody>
        <!-- Header Factura -->
        <tr style="display: inline-block; border-bottom: 1px solid gray; padding-bottom:0; width: 100%;">
            <!-- Info empresa -->
            <td style="width:100%; text-align: center; display: inline-block;">
                <div style="margin: 10px 0 20px 0; width: 200px; height: 100px; text-align: center; display: inline-block;">
                    <img style="height: 100px; display: inline-block;" src="<?= Url::base(true) . "/" . $company->getLogoWebPath()   ?>" alt="Marca Empresa">
                </div>
                <p style="font-size: 0.35cm;line-height: 0.4cm;font-weight: 800;">
                    Nº: <?= str_pad($model->company->defaultPointOfSale->number, 4, "0", STR_PAD_LEFT).'-'. str_pad($model->number, 8, "0", STR_PAD_LEFT)?>
                </p>
                <p style="font-size: 0.35cm;line-height: 0.4cm;font-weight: 800;"><span style="padding-right: 10px;font-weight: 400;">Fecha de emisión:</span><?=$formatter->asDate($model->date);?></p>
                <p style="font-size: 0.35cm; text-align: center;">
                    Documento no válido como factura
                </p>
            </td>
        </tr>
        <!-- Datos clientes a Facturar -->
        <tr style=" border-bottom: 1px solid gray; padding-bottom: 0.2cm;padding-top: 0.2cm;display: inline-block; width: 100%;">

            <!-- Columna de Datos 1 -->
            <td style="padding-left: 25px; width:50%;">
                <div>
                    Señores / Razón Social: <span style="margin: 3px 0; font-weight: 800;"><?=(trim($model->customer->lastname)=='' ? '' : trim($model->customer->lastname) .',') .$model->customer->name?></span>
                </div>
            </td>
            <!-- Columna de Datos 2 -->
            <td style="padding-left: 50px; width:30%;">
                <div>
                    N° de Cliente: <span style="margin: 3px 0; font-weight: 800;"><?= $model->customer->code ?></span>
                </div>
            </td>
        </tr>

        <!-- Detalle de Items -->
        <?php
        foreach($model->billDetails as $detail) {
            ?>

            <tr style=" text-align: right;min-height: 20px;padding: 0px 0;">
                <td style="width: 10%; float: left; min-height: 50px;padding: 5px 0;text-align: center;">
                    <p style="font-size: 0.38cm; margin: 0; text-transform: capitalize">
                        <?= $detail->qty ?>
                    </p>
                </td>
                <td style="width: 39%; float: left; min-height: 50px;padding: 5px 0;text-align: left;">
                    <p style="font-size: 0.38cm; margin: 0; text-transform: capitalize">
                        <?= $detail->concept ?>
                    </p>
                </td>
                <td style="width: <?=($model->billType->code==1 ?  "10%": "20%") ?>; float: left; min-height: 50px;padding: 5px 0;text-align: center;">
                    <p style="font-size: 0.38cm; margin: 0; text-transform: capitalize">
                        <span>$</span> <?= (($model->billType->code==1) ? round($detail->unit_net_price,2) : round($detail->unit_final_price,2) ) ?>
                    </p>
                </td>
                <td style="width: <?=($model->billType->code==1 ?  "20%": "30%") ?>; float: left; min-height: 50px;padding: 5px 0;text-align: center;">
                    <p style="font-size: 0.38cm; margin: 0; text-transform: capitalize">
                        <span>$</span> <?= (($model->billType->code==1) ? round($detail->getSubtotal(),2) : round($detail->getTotal(),2) ) ?>
                    </p>
                </td>
                <?php
                if ($model->billType->code==1) {
                    ?>
                    <td style="width: 10%; float: left; min-height: 30px;padding: 5px 0;text-align: center;">
                        <p style="font-size: 0.38cm; margin: 0; text-transform: capitalize">
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
                    <td style="width: 10%; float: left; min-height: 50px;padding: 5px 0;text-align: center;">
                        <p style="font-size: 0.38cm; margin: 0; text-transform: capitalize">
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
        <tr style=" margin-left: 425px; width:435px; display: block;">
            <!-- Item -->
            <td style="width: 300px;float:left;">
                <h5 style="text-align: right;margin-top: 6px; margin-bottom: 6px; font-size: 18px;">TOTAL:</h5>
            </td>
            <!-- Precio -->
            <td style="width: 130px; float:left;">
                <h5 style="text-align: right;font-size: 18px;margin-top: 6px; margin-bottom: 6px; padding-right: 25%;"><span>$</span> <?=round($model->calculateTotal(),2)?></h5>
            </td>
        </tr>
        <tr style="padding-bottom: 5px;padding-top: 5px;padding-left: 30px; font-size: 0.3cm">
            <td>
                <?= $model->observation? 'Observación:'. $model->observation : ''?>
            </td>
        </tr>
        <tr style="padding-bottom: 5px;padding-top: 5px;padding-left: 30px; font-size: 0.3cm">
            <td>
                Puede retirar su factura en: <?= Config::getValue('general_address') ?>
            </td>
        </tr>
        <tr style="padding-bottom: 5px;padding-top: 5px;padding-left: 30px; font-size: 0.3cm">
            <td>
                Medios de Pago:
            </td>
        </tr>
        <tr style=" display: block; padding-bottom: 5px;padding-top: 5px;padding-left: 30px; font-size: 0.3cm">
            <td>
                <ul>
                    <li> <?= Config::getValue('pdf_bill_payment_methods')?> </li>;
                </ul>
            </td>
        </tr>
        <tr style=" display: block; border-top: 1px solid gray;padding-bottom: 5px;padding-top: 5px;padding-left: 30px; font-size: 16px; width: 100%">
            <td style="width: 60%">
                <img width="100%" src="<?=Url::toRoute(['/sale/customer/barcode', 'code'=>$model->customer->payment_code], true) ?>"/>
            </td>
            <td style="width: 40%">
                <p style="margin-bottom:5px; margin-top:5px;padding-left: 30px; font-weight: 700;">CÓDIGO DE PAGO:<br><?= $model->customer->payment_code ?></p>
                <?php if($debt < 0) { ?>
                    <p style="margin-bottom:5px; margin-top:5px;padding-left: 30px; font-weight: 700;">DEUDA AL <?php echo (new \DateTime('now'))->format('d/m/Y') . ": " . Yii::$app->formatter->asCurrency(abs($debt)) ?></p>
                <?php } ?>
            </td>
        </tr>

        </tbody>
    </table>
<?php } else { ?>
<!-- Estructura Factura Electronica -->
<table id="container_fact" style="width:860px;border:1px solid black;font-family: Arial, Helvetica, sans-serif;">
    <tbody>
    <!-- Header Factura -->
    <tr style="display: inline-block; border-bottom: 2px solid black; padding-bottom:0;">
        <!-- Info empresa -->
        <td style="width:400px; text-align: center;">
            <div style="margin: 0 0 20px 0; width: 200px; height: 100px; text-align: center; display: inline-block;">
                <img style="height: 100px; display: inline-block;" src="<?= Url::base(true) . "/" . ($company->getLogoWebPath() )  ?>" alt="Marca Empresa">
            </div>
            <div style="">
                <p style="margin: 0; text-transform: capitalize;"><?= $company->fantasy_name; ?></p>
                <p style="font-size: 12px; margin: 0; text-transform: capitalize;"><?= $company->address; ?></p>
                <p style="font-weight: 600; margin-top: 0.1cm; text-transform: uppercase;"><?= $company->taxCondition->name; ?></p>
            </div>
        </td>

        <!-- Tipo de Facutra -->
        <td style="width:50px;padding:0.2cm 0 0 0;height: 50px;width: 50px;">
            <h1 style="text-align: center;border: 2px solid lightgray;font-size: 30px;width: 100%;font-weight: 800;margin: 0;display: inline-block;padding-bottom: 0.8cm;padding-top: 0.8cm;">
                <?=substr($model->billType->name, -1)?>
            </h1>
            <p style="margin: 0.8cm 0;text-align: center;">
                Código: <?=$model->billType->code?>
            </p>
        </td>

        <!-- Legales header -->
        <td style="width:400px; padding-left: 80px;">
            <h2 style="font-size:0.45cm;margin-top:0.15cm;margin-bottom:0.1cm;">COMPROBANTE ELECTRÓNICO</h2>
            <h1 style="font-size: 0.55cm;margin-top:0.2cm;font-weight: 800;"><span style="padding-right: 30px;"><?=substr($model->billType->name,0,strlen($model->billType->name)-1)?></span><?=sprintf("%04d", $model->getPointOfSale()->number) . "-" . sprintf("%08d", $model->number )?></h1>
            <p style="font-size: 0.35cm;line-height: 0.4cm;margin: 0px 0;font-weight: 800;"><span style="padding-right: 10px;font-weight: 400;">Fecha de emisión:</span><?=$formatter->asDate($model->date);?></p>
            <p style="font-size: 0.35cm;line-height: 0.4cm;margin: 0px 0;font-weight: 800;"><span style="padding-right: 10px;font-weight: 400;">CUIT:</span><?= $companyData->tax_identification ?></p>
            <p style="font-size: 0.35cm;line-height: 0.4cm;margin: 0px 0;font-weight: 800;"><span style="padding-right: 10px;font-weight: 400;">Ingresos Brutos:</span>Nro <?= $companyData->iibb ?></p>
            <p style="font-size: 0.35cm;line-height: 0.4cm;margin: 0px 0;font-weight: 800;"><span style="padding-right: 10px;font-weight: 400;">Inicio de actividades:</span><?= $companyData->start ?></p>

        </td>
    </tr>

    <!-- Datos clientes a Facturar -->
    <tr style="border-bottom: 2px solid black;padding-bottom: 0.2cm;padding-top: 0.2cm;display: inline-block; width: 100%;">

        <!-- Columna de Datos 1 -->
        <td style="padding-left: 25px; width:50%;">
            <div>
                Señores / Razón Social: <span style="margin: 3px 0; font-weight: 800;"><?=(trim($model->customer->lastname)=='' ? '' : trim($model->customer->lastname) .',') .$model->customer->name?></span>
            </div>
            <div>
                Domicilio:
                <?php if(!$isConsumidorFinal) { ?>
                    <span style="margin: 3px 0; font-weight: 800;"><?=($model->customer->address ? $model->customer->address->shortAddress: '' )?></span>
                <?php } ?>
            </div>
            <div>
                <?php if(!$isConsumidorFinal) { ?>
                    <?=$model->customer->documentType ? $model->customer->documentType->name : Yii::t('app','Document')?>: <span style="margin: 3px 0; font-weight: 800;"><?=$model->customer->document_number?></span>
                <?php } ?>
            </div>
        </td>
        <!-- Columna de Datos 2 -->
        <td style="padding-left: 50px; width:30%;">
            <div>
            <?php if(!$isConsumidorFinal) { ?>
                N° de Cliente: <span style="margin: 3px 0; font-weight: 800;"><?= $model->customer->code ?></span>
            <?php } ?>
            </div>
            <div>
                IVA: <span style="margin: 3px 0; font-weight: 800;"><?= $model->customer->taxCondition->name ?></span>
            </div>
            <div>
                Condición de Venta:
                <span style="margin: 3px 0; font-weight: 800;"> Cuenta corriente </span>
            </div>
        </td>
        <td style="padding-left: 20px; padding-right: 20px; width: 20%;">
            <img style="width: 100%;" src="<?=Url::toRoute(['/sale/customer/barcode', 'code'=>$model->customer->payment_code], true) ?>">
        </td>
    </tr>

    <!-- Items facturados -->
    <tr style="border-bottom: 2px solid black;margin-bottom: 60px;margin-top: 20px;width:100%;background-color: rgb(221, 221, 221);padding-bottom:0.2cm;">
        <td>
            <table style="width: 100%;">
                <tr>
                    <td style="width: 10%; background-color: rgb(221, 221, 221);text-align: center;">
                        <h5 style="font-size: 12px; margin: 0;">
                            CANTIDAD
                        </h5>
                    </td>
                    <td style="width: 39%; border-left: 1px solid gray; background-color: rgb(221, 221, 221);text-align: center;">
                        <h5 style="font-size: 12px; margin: 0;">
                            DESCRIPCIÓN
                        </h5>
                    </td>
                    <td style="width:<?=($model->billType->code==1 ?  "10%": "20%") ?> ; border-left: 1px solid gray;background-color: rgb(221, 221, 221);text-align: center;">
                        <h5 style="font-size: 12px; margin: 0;">
                            PRECIO UNITARIO
                        </h5>
                    </td>
                    <td style="width: <?=($model->billType->code==1 ?  "20%": "30%") ?>; border-left: 1px solid gray;background-color: rgb(221, 221, 221);text-align: center;">
                        <h5 style="font-size: 12px; margin: 0;">
                            SUBTOTAL
                        </h5>
                    </td>
                    <?php
                    if ($model->billType->code==1) {
                        ?>
                        <td style="width: 10%; border-left: 1px solid gray; background-color: rgb(221, 221, 221);text-align: center;">
                            <h5 style="font-size: 12px; margin: 0;">
                                IVA<br>ALICUOTA
                            </h5>
                        </td>
                        <td style="width: 10%; border-left: 1px solid gray;background-color: rgb(221, 221, 221);text-align: center;">
                            <h5 style="font-size: 12px; margin: 0;">
                                SUBTOTAL<br> C/IVA
                            </h5>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Detalle de Items -->
    <?php
    foreach($model->billDetails as $detail) {
        ?>

        <tr style=" text-align: right;min-height: 20px;padding: 0px 0;">
            <td style="width: 10%; float: left; min-height: 50px;padding: 5px 0;text-align: center;">
                <p style="font-size: 0.38cm; margin: 0; text-transform: capitalize">
                    <?= $detail->qty ?>
                </p>
            </td>
            <td style="width: 39%; float: left; min-height: 50px;padding: 5px 0;text-align: left;">
                <p style="font-size: 0.38cm; margin: 0; text-transform: capitalize">
                    <?= $detail->concept ?>
                </p>
            </td>
            <td style="width: <?=($model->billType->code==1 ?  "10%": "20%") ?>; float: left; min-height: 50px;padding: 5px 0;text-align: center;">
                <p style="font-size: 0.38cm; margin: 0; text-transform: capitalize">
                    <span>$</span> <?= (($model->billType->code==1) ? round($detail->unit_net_price,2) : round($detail->unit_final_price,2) ) ?>
                </p>
            </td>
            <td style="width: <?=($model->billType->code==1 ?  "20%": "30%") ?>; float: left; min-height: 50px;padding: 5px 0;text-align: center;">
                <p style="font-size: 0.38cm; margin: 0; text-transform: capitalize">
                    <span>$</span> <?= (($model->billType->code==1) ? round($detail->getSubtotal(),2) : round($detail->getTotal(),2) ) ?>
                </p>
            </td>
            <?php
            if ($model->billType->code==1) {
                ?>
                <td style="width: 10%; float: left; min-height: 50px;padding: 5px 0;text-align: center;">
                    <p style="font-size: 0.38cm; margin: 0; text-transform: capitalize">
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
                <td style="width: 10%; float: left; min-height: 50px;padding: 5px 0;text-align: center;">
                    <p style="font-size: 0.38cm; margin: 0; text-transform: capitalize">
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
    <tr style="margin-left:425px; width:435px; display: block;">
        <!-- Item -->
        <td style="width: 300px;float:left;">
            <h5 style="text-align: right;margin-top: 6px; margin-bottom: 6px; font-size: 18px;">TOTAL:</h5>
        </td>
        <!-- Precio -->
        <td style="width: 130px; float:left;">
            <h5 style="text-align: right;font-size: 18px;margin-top: 6px; margin-bottom: 6px; padding-right: 25%;"><span>$</span> <?=round($model->calculateTotal(),2)?></h5>
        </td>
    </tr>
    <tr style="padding-bottom: 5px;padding-top: 5px;padding-left: 30px; font-size: 0.3cm">
        <td>
            <?= $model->observation? 'Observación: '. $model->observation : ''?>
        </td>
    </tr>
    <tr style="padding-bottom: 5px;padding-top: 5px;padding-left: 30px; font-size: 0.3cm">
        <td>
            Puede retirar su factura en: <?php echo $model->company->address  ?>
        </td>
    </tr>
    <tr style=" display: block; border-top: 2px solid black;padding-bottom: 5px;padding-top: 5px;padding-left: 30px; font-size: 16px;">
        <td>
            Medios de Pago:
        </td>
        <?php foreach ($model->customer->getPaymentMethodNameAndCodes() as $payment_method_name) { ?>
            <tr>
                <td>
                    <div>
                        <?= $payment_method_name['payment_method_name'] .': '?>
                    </div>
                    <div style="padding-left: 100px">
                        <?php if($payment_method_name['use_barcode']) { ?>
                            <img width="30%" src="<?=Url::toRoute(['/sale/customer/barcode', 'code' => $payment_method_name['code']], true) ?>"/>
                        <?php } else {
                            echo $payment_method_name['code'];
                        }?>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </tr>

    <!-- Totales Facturados -->
    <tr style=" display: block; border-bottom: 2px solid black;padding-bottom: 5px;padding-top: 5px;">

        <?php
        $bill_type_code = $model->billType->code ;
        if ($bill_type_code == 1 || $bill_type_code == 3 ||$bill_type_code == 8 || $bill_type_code == 2) { ?>
        <!-- Row de Items -->
        <tr style="margin-left:425px; width:425px; display: block;">
            <!-- Item -->
            <td style="width: 300px;float:left;">
                <h5 style="text-align: right;margin-top: 6px; margin-bottom: 6px;">I.V.A. 21%:</h5>
            </td>
            <!-- Precio -->
            <td style="width: 120px; float:left;">
                <h5 style="text-align: right;font-size: 16px;margin-top: 6px; margin-bottom: 6px;"><span>$</span> <?=round($model->calculateTaxes(),2)?></h5>
            </td>
        </tr>
        <tr style="margin-left:425px; width:425px; display: block;">
            <!-- Item -->
            <td style="width: 300px;float:left;">
                <h5 style="text-align: right;margin-top: 6px; margin-bottom: 6px;">Importe neto:</h5>
            </td>
            <!-- Precio -->
            <td style="width: 120px; float:left;">
                <h5 style="text-align: right;font-size: 16px;margin-top: 6px; margin-bottom: 6px;"><span>$</span> <?=round($model->calculateAmount(),2)?></h5>
            </td>
        </tr>

    <?php } ?>
    <?php if($model->billType->invoice_class_id) { ?>
    <tr>
        <td>
            <table class="table table-bordered">
                <tr>
                    <td style="width: 50%;">
                        <img width="100%" src="<?=Url::toRoute(['/sale/bill/barcode', 'id'=>$model->bill_id], true) ?>"/>
                    </td>
                    <td style="width: 50%;">
                        <div class="titulo">Código de Autorización Electrónica</div>
                        <div class="titulo">C.A.E. Nº: <?=$model->ein ?></div>
                        <div class="titulo">Fecha de Vencimiento de C.A.E.: <?=$formatter->asDate($model->ein_expiration);?></div>
                    </td>

                </tr>
            </table>
        </td>
    </tr>
    <?php } ?>
    </tbody>
</table>
<?php } ?>
