<?php

use app\modules\sale\models\TaxRate;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\Bill $model
 */
/** @var \app\modules\sale\models\Company $company */
$company = $this->model->company;
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
                <img style="height: 100px; display: inline-block;" src="<?= Yii::getAlias("@app/web/images/". $company->getLogoWebPath());  ?>" alt="Marca Empresa">
            </div>
            <div style="">
                <p style="margin: 5px 0; text-transform: capitalize;"><?=$company->name?></p>
                <p style="margin: 5px 0; text-transform: capitalize;"><?=$company->address?></p>
                <p style="font-weight: 600; margin-top: 10px; text-transform: uppercase;"><?=$company->taxCondition->name?></p>
            </div>
        </td>

        <!-- Tipo de Facutra -->
        <td style="width:50px;padding: 10px;height: 50px;width: 50px;">
            <h1 style="text-align: center;border: 2px solid lightgray; padding:3px; font-size: 38px;width: 100%;font-weight: 800;margin: 0;display: inline-block;padding-bottom: 10px;padding-top: 10px;">
                X
            </h1>
            <!-- <p style="margin: 50px 0;text-align: center;">
                Código: 1
            </p> -->
        </td>

        <!-- Legales header -->
        <td style="width:400px; padding-left: 80px;">
            <h1 style="font-size: 24px;font-weight: 800;margin: 1 0px 0;">RECIBO AUTORIZADO</h1>
            <h2 style="font-size: 14px;">Documento no válido como Factura</h2>
            <p style="font-size: 18px;margin: 20px 0;font-weight: 400;">N° </p>
            <p style="margin: 5px 0;font-weight: 800;"><span style="padding-right: 10px;font-weight: 400;">CUIT:</span><?=$company->tax_identification?></p>
            <p style="margin: 5px 0;font-weight: 800;"><span style="padding-right: 10px;font-weight: 400;">Fecha de emisión:</span><?=$formatter->asDate($model->date)?></p>
            <p style="margin: 5px 0;font-weight: 800;"><span style="padding-right: 10px;font-weight: 400;">Ingresos Brutos:</span>Nro <?=$company->iibb?></p>
            <p style="margin: 5px 0;font-weight: 800;"><span style="padding-right: 10px;font-weight: 400;">Inicio de actividades:</span><?=Yii::$app->formatter->asDate($company->start)?></p>
        </td>
    </tr>

    <!-- Datos clientes a Facturar -->
    <tr style="">
        <td>
            <p style="margin-top: 5px; padding-left: 20px;">Recibimos de:<?=$model->customer->name?></p>
            <p style="margin-top: 5px; padding-left: 20px;">La cantidad de:<?=$formatter->asCurrency($model->amount)?></p>
        </td>
    </tr>

    <!-- Tabla de items 1 -->
    <tr style="border-bottom: 1px solid #828282;background-color:#DCDCDC; border-top: 1px solid #828282; ">
        <td style="width: 40%; float: left; min-height: 30px;padding: 3px 0;text-align: center; display: block; background-color:#DCDCDC;">
            <h1 style="text-align: center; font-size: 16px; color: #828282;">CONCEPTO A COBRAR</h1>
        </td>
        <td style="width: 30%; float: left; min-height: 30px;padding: 3px 0;text-align: center; display: block; background-color:#DCDCDC;">
            <h1 style="text-align: center; font-size: 16px; color: #828282;">FECHA</h1>
        </td>
        <td style="width: 29%; float: left; min-height: 30px;padding: 3px 0;text-align: center; display: block; background-color:#DCDCDC;">
            <h1 style="text-align: center; font-size: 16px; color: #828282;">IMPORTE</h1>
        </td>
    </tr>
    <tr>
        <td style="width: 40%; float: left; min-height: 20px;text-align: left;">
            <h1 style=" center; font-size: 16px; color: #828282;margin: 5px 0; padding-left:5px">
                Pago a Cuenta
            </h1>
        </td>
        <td style="width: 30%; float: left; min-height: 20px;text-align: center;">
            <h1 style=" font-size: 16px; color: #828282; margin: 5px 0;">
                <?=$formatter->asDate($model->date)?>
            </h1>
        </td>
        <td style="width: 29%; float: left; min-height: 20px;text-align: center;">
            <h1 style=" font-size: 16px; color: #828282;margin: 5px 0;">
                <?=$formatter->asCurrency($model->amount)?>
            </h1>
        </td>
    </tr>
    <tr style="border-top: 1px solid #828282; margin-top: 20px; margin-bottom: 20px; display:block;">
        <td style="width: 70%;  float: left; min-height: 20px;">
            <h1 style="text-align: right; margin: 10px 0; font-size: 18px;">Total a Cobrar</h1>
        </td>
        <td style="width: 29%;  float: left; min-height: 20px; ">
            <h1 style=" font-size: 16px; color: #828282;margin: 10px 0;text-align: center;">
                <?=$formatter->asCurrency($model->amount)?>
            </h1>
        </td>
    </tr>

    <!-- <div style="margin-top: 50px; display: block;"></div> -->

    <!-- Tabla de items 2 -->
    <tr style="border-bottom: 2px solid #828282;background-color:#DCDCDC; border-top: 2px solid #828282; ">
        <td style="width: 40%; float: left; min-height: 30px;padding: 3px 0;text-align: center; display: block; background-color:#DCDCDC;">
            <h1 style="text-align: center; font-size: 16px; color: #828282;">VALORES RECIBIDOS</h1>
        </td>
        <td style="width: 30%; float: left; min-height: 30px;padding: 3px 0;text-align: center; display: block; background-color:#DCDCDC;">
            <h1 style="text-align: center; font-size: 16px; color: #828282;">&#160;</h1>
        </td>
        <td style="width: 29%; float: left; min-height: 30px;padding: 3px 0;text-align: center; display: block; background-color:#DCDCDC;">
            <h1 style="text-align: center; font-size: 16px; color: #828282;">IMPORTE</h1>
        </td>
    </tr>

    <tr>
        <td style="width: 40%; float: left; min-height: 20px;text-align: left;">
            <h1 style=" center; font-size: 16px; color: #828282;margin: 5px 0;padding-left:5px">
                <?=$model->paymentMethod->name . ": "  . $model->concept?>
            </h1>
        </td>
        <td style="width: 30%; float: left; min-height: 20px;text-align: center;">
            <h1 style=" font-size: 16px; color: #828282; margin: 5px 0;">&#160;</h1>
        </td>
        <td style="width: 29%; float: left; min-height: 20px;text-align: center;">
            <h1 style=" font-size: 16px; color: #828282;margin: 5px 0;">
                <?=$formatter->asCurrency($model->amount)?>
            </h1>
        </td>
    </tr>

    <tr style="border-top: 1px solid #828282; margin-top: 20px; margin-bottom: 10px; display:block;">
        <td style="width: 70%;  float: left; min-height: 20px;">
            <h1 style="text-align: right; margin: 5px 0; font-size: 18px;">Total Valores Recibidos</h1>
        </td>
        <td style="width: 29%;  float: left; min-height: 20px; ">
            <h1 style="font-size: 16px; color: #828282;margin: 5px 0;text-align: center;">
                <?=$formatter->asCurrency($model->amount)?>
            </h1>
        </td>
    </tr>

    <tr style="">
        <td style="width: 70%;  float: left; min-height: 20px;">
            <h1 style="text-align: right; margin: 5px 0; font-size: 18px;">Total Efectivo</h1>
        </td>
        <td style="width: 29%;  float: left; min-height: 20px; ">
            <h1 style=" font-size: 16px; color: #828282;margin: 5px 0;text-align: center;">
                <?=$formatter->asCurrency( ($model->paymentMethod->type=="exchanging" ? $model->amount : 0 ))?>
            </h1>
        </td>
    </tr>

    <tr style="">
        <td style="width: 70%;  float: left; min-height: 20px;">
            <h1 style="text-align: right; margin: 5px 0; font-size: 18px;">Son</h1>
        </td>
        <td style="width: 29%;  float: left; min-height: 20px; ">
            <h1 style=" font-size: 16px; color: #828282;margin: 5px 0;text-align: center;">
                <?=$formatter->asCurrency($model->amount)?>
            </h1>
        </td>
    </tr>

    <!-- Aclaración y Firma -->
    <tr>
        <td style="width: 50%; float:left; font-size: 16px; margin-top: 80px; margin-bottom: 30px;">
            <p style="padding-right: 20px; padding-left: 20px;">Valores que serán acreditados una vez hecho el efectivo.</p>
        </td>
        <td style="width: 49%; float:left; font-size: 16px; margin-top: 80px; margin-bottom: 30px;">
            <p style="text-align: center;">________________________________________</p>
            <p style="text-align: center;">por <?=$company->name?></p>
        </td>
    </tr>

    </tbody>
</table>