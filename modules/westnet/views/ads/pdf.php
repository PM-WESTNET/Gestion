<?php
$i =0;
use app\modules\config\models\Config;
use yii\helpers\Url;
try {
?>
<style>
    body{
        font-size: 14px;
    }
    .divisor{
        height:8px;
    }
</style>
<?php foreach ($codes as $key => $code){ ?>
<!-- Inicio Header -->
<table id="container" style="width:860px">
    <tbody>

        <tr>
            <td style="width:286px; text-align: center;">
                <?php
                if(isset($company)) { ?>
                <img style="height: 100px; display: inline-block;" src="<?=Url::base(true) . "/" . $company->getLogoWebPath()?>"/>
                <?php } ?>
            </td>
            <td style="width:286px; text-align: center;">
                <img  width="143px" height="50px" src="<?= Url::to(['/westnet/ads/barcode', 'code'=>$code['code'], 'mode'=>(isset($model) ? 0 : 1)], true)?>">
            </td>
            <td style="width:286px; text-align: center;">
                <table>
                    <tbody>
                        <tr>
                            <td style="border: 1px solid black; width: 80px">
                                CLIENTE
                            </td>
                            <td style="border: 1px solid black">
                                <?php
                                    if(isset($model) ){
                                        echo $model->customer->code; 
                                    }else{
                                        echo $code['code'];
                                    }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black; width: 80px">
                                FECHA
                            </td>
                            <td style="border: 1px solid black">
                                <?php 
                                    if(isset($model)){
                                        echo (new DateTime())->format('d/m/Y'); 
                                      
                                    }
                                ?>
                            </td>
                           
                        </tr>
                         <?php if(!isset($model)){?>
                            <tr>
                                <td style="border: 1px solid black; width: 80px">
                                    FECHA DE IMPRESION
                                </td>
                                <td style="border: 1px solid black; width: 80px">
                                    <?= date('d/m/Y')?>
                                </td>
                            </tr>    
                        <?php }?>
                        <tr>
                            <td colspan="2">
                                FICHA PARA ADMINISTRACIÓN
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
<!-- Fin Header -->

<!-- Inicio Datos del cliente 1 -->
<table id="container" style="width:860px;border:1px solid black;">
    <tbody>
        <tr>
            <td colspan="4" style="color:white; background-color: black">DATOS DEL CLIENTE</td>
        </tr>
        <tr>
            <td style="width:210px; border: 1px solid black; padding: 2px;">
                APELLIDO Y NOMBRE / RAZON SOCIAL
            </td>
            <td style="width:650px; border: 1px solid black; padding: 2px;" colspan="3">
                <?php 
                    if(isset($model)){
                        echo $model->customer->lastname . ", " . $model->customer->name ;
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td style="width:210px; border: 1px solid black; padding: 2px;">
                DIRECC. INSTALACION
            </td>
            <td style="width:650px; border: 1px solid black; padding: 2px; font-size: 8px " colspan="3">
                <?php 
                    if(isset($model)){
                        echo $model->address->getFullAddress();
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td style="width:210px; border: 1px solid black; padding: 2px;">
                LOCALIDAD
            </td>
            <td style="width:350px; border: 1px solid black; padding: 2px;">
                <?php 
                    if(isset($model)){
                        echo ($model->address->zone ? $model->address->zone->name : '' );
                    }
                ?>
            </td>
            <td style="width:210px; border: 1px solid black; padding: 2px;">
                COD.POSTAL
            </td>
            <td style="width:90px; border: 1px solid black; padding: 2px;">
            </td>
        </tr>
        <tr>
            <td style="width:210px; border: 1px solid black; padding: 2px;">
                TELEFONO
            </td>
            <td style="width:216px; border: 1px solid black; padding: 2px;">
                <?php 
                    if(isset($model)){
                        echo $model->customer->phone;
                    }    
                ?>
            </td>
            <td style="width:216px; border: 1px solid black; padding: 2px;">
                CELULAR
            </td>
            <td style="width:216px; border: 1px solid black; padding: 2px;">
                <?php 
                    if(isset($model)){
                        echo $model->customer->phone2 . ( $model->customer->phone3 ? " - " . $model->customer->phone3 :  "" );
                    }    
                ?>
            </td>
        </tr>
        <tr>
            <td style="width:210px; border: 1px solid black; padding: 2px;">
                EMAILS
            </td>
            <td style="width:216px; border: 1px solid black; padding: 2px;" colspan="3">
                <?php
                    if(isset($model)){
                        echo $model->customer->email . ( $model->customer->email2 ? " - " . $model->customer->email2 :  "" );
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td style="width:210px; border: 1px solid black; padding: 2px;">
                CONTACTO
            </td>
            <td style="width:216px; border: 1px solid black; padding: 2px;" colspan="3">

            </td>
        </tr>
    </tbody>
</table>
<!-- Fin Datos del cliente 1 -->
<div class="divisor"></div>
<!-- Inicio Equipamiento 1 -->
<table id="container" style="width:860px">
    <tr>
        <td colspan="11" style="color:white; background-color: black">EQUIPAMIENTO EN COMODATO INSTALADO: tildar lo que corresponda</td>
    </tr>
    <tr>
        <td style="width:60px; border: 1px solid black; padding: 2px;">
            ANTENA
        </td>
        <td style="width:20px; padding: 2px;">&nbsp;</td>
        <td style="width:30px; border: 1px solid black; padding: 2px;">
        <td style="width:20px; padding: 2px;">&nbsp;</td>


        <td style="width:60px; border: 1px solid black; padding: 2px;">
            RADIO
        </td>
        <td style="width:20px; padding: 2px;">&nbsp;</td>
        <td style="width:30px; border: 1px solid black; padding: 2px;">
        <td style="width:20px; padding: 2px;">&nbsp;</td>

        <td style="width:230px; border: 1px solid black; padding: 2px;">
            Nº DE SERIE RADIO
        </td>
        <td style="width:10px; padding: 2px;">&nbsp;</td>
        <td style="width:550px; border: 1px solid black; padding: 2px;">
    </tr>
</table>
<!-- Fin Equipamiento 2 -->
<div class="divisor"></div>
<!-- Inicio Equipamiento 2 -->
<table id="container" style="width:860px;">
    <tr>
        <td colspan="5" style="color:white; background-color: black">EQUIPAMIENTO INSTALADO Y/O SERVICIOS A FACTURAR</td>
    </tr>
    <tr>
        <td style="color:white; background-color: black; text-align: center;">ITEM</td>
        <td style="color:white; background-color: black; text-align: center;">CANTIDAD</td>
        <td style="color:white; background-color: black; text-align: center;">CUOTAS</td>
        <td style="color:white; background-color: black; text-align: center;">PRECIO</td>
    </tr>
    <?php
    if(isset($model)){
        foreach ($model->contractDetails as $detail){
                if($detail->product->type !== 'plan'){
                    error_log(print_r($detail->fundingPlan,1));
                    echo '<tr>';
                    echo '<td style="border: 1px solid black;">'. $detail->product->name . '</td>';
                    echo '<td style="border: 1px solid black; text-align: center;">'. $detail->count. '</td>';
                    if(!empty($detail->fundingPlan)){
                        echo '<td style="border: 1px solid black; text-align: center;">'. $detail->fundingPlan->qty_payments. '</td>';
                        echo '<td style="border: 1px solid black; text-align: center;">'. Yii::$app->formatter->asCurrency($detail->fundingPlan->getFinalAmount())  . '</td>';
                    }else{
                        echo '<td style="border: 1px solid black; text-align: center;"> 1 </td>';
                        echo '<td style="border: 1px solid black; text-align: center;">'. Yii::$app->formatter->asCurrency($detail->product->finalPrice);
                    }
                    echo '</tr>';
                }
            }
    }else {
        echo '<tr><td style="border: 1px solid black; width: 400px; heigth: 50px;"> </td><td style="border: 1px solid black; heigth: 50px;">&nbsp; </td><td style="border: 1px solid black; heigth: 50px;">&nbsp;</td><td style="border: 1px solid black; heigth: 50px;">&nbsp;</td></tr>';
        echo '<tr><td style="border: 1px solid black; width: 400px; heigth: 50px;"> </td><td style="border: 1px solid black; heigth: 50px;">&nbsp; </td><td style="border: 1px solid black; heigth: 50px;">&nbsp;</td><td style="border: 1px solid black; heigth: 50px;">&nbsp;</td></tr>';
    }
            
            ?>
        
</table>
<!-- Fin Equipamiento 2 -->
<div class="divisor"></div>

<!-- Inicio Datos de Conexion -->
<table id="container" style="width:860px;">
    <tr>
            <td colspan="6" style="color:white; background-color: black">

                <table style="width: 100%">
                    <tr>
                        <td>DATOS DE CONEXION </td>
                        <td  style="color:white; background-color: black; text-align: right">
                            CIR:<?= Config::getValue('default_cir') ?>% |
                            DISPONIBILIDAD DEL SERVICIO ANUAL <?= Config::getValue('annual_availability') ?>%

                        </td>
                    </tr>
                </table>
            </td>
    </tr>
    <tr>
        <td style="width:18%; height:20px;border: 1px solid black; padding: 2px; display: inline-block">
            TIPO DE INSTALACION
        </td>
        <td style="width:38%; height:20px;border: 1px solid black; padding: 2px; display: inline-block">
            [__] Tensado [__] Amurado [__] Otros_________
        </td>

        <td style="width:15%; height:20px;border: 1px solid black; padding: 2px; display: inline-block">
            MTS DE CABLE
        </td>

        <td style="width:5%; height:20px;border: 1px solid black; padding: 2px; display: inline-block"></td>

        <td style="width:15%; height:20px;border: 1px solid black; padding: 2px; display: inline-block">
            MTS DE CABLE
        </td>
        <td style="width:5%; height:20px;border: 1px solid black; padding: 2px; display: inline-block"></td>
    </tr>
</table>

<table style="width:860px;">
    <tr>
        <td style="width:99%; height:13px; padding: 2px; display: inline-block">
            Tipo de Conexion:
        </td>
        
        <td style="width:25%; height:59px;border: 1px solid black; padding: 10px; display: inline-block">
            [__] Empresa <br>
            [__] Hogar <br>
        </td>
        
        <td style="width:25%; height:59px;border: 1px solid black; padding: 10px; display: inline-block; padd">
            [__] Fibra <br>
            [__] Wireless <br>
        </td>
        
        <td style="width:40%; height:59px;border: 1px solid black; padding: 10px; display: inline-block">
            <?php
                $i = 1;
                foreach($plans as $plan) {

                    echo "[__] " . $plan->ads_name . ($i==3 ? "<br/>" : "&nbsp;&nbsp;&nbsp;" );
                    $i++;
                    if($i==4) {
                        $i = 1;
                    }
                }
            ?>
        </td>
    </tr>
</table>

<!-- Fin Datos de Conexion -->

<!-- Fin Datos de Conexion -->
<div>
    <h4><?= Config::getValue('ads-title')?></h4>
    <?= str_replace('@Empresa', $company->name,Config::getValue('ads-message')) ?>
</div>
<br>
<!-- Inicio Datos instalador -->
<table id="container" style="width:860px;">
    <tr>
        <td>
            <table  style="width:425px;">
                <tr>
                    <td colspan="3" style="color:white; background-color: black">DATOS INSTALADOR</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 2px;height: 80px;">&nbsp;</td>
                    <td style="border: 1px solid black; padding: 2px;height: 80px;">&nbsp;</td>
                    <td style="border: 1px solid black; padding: 2px;height: 80px;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="width:280px; padding: 2px;text-align: center">FIRMA</td>
                    <td style="width:400px; padding: 2px;text-align: center">ACLARACION</td>
                    <td style="width:180px; padding: 2px;text-align: center">DNI</td>
                </tr>

            </table>
        
<!-- Fin Datos instalador -->
        </td>
<!--<div class="divisor"></div>--!>

<!-- Inicio Datos instalador -->
        <td>
            <table  style="width:425px;">
                    <tr>
                        <td colspan="3" style="color:white; background-color: black">CONFORMIDAD DEL CLIENTE</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; padding: 2px;height: 80px;">&nbsp;</td>
                        <td style="border: 1px solid black; padding: 2px;height: 80px;">&nbsp;</td>
                        <td style="border: 1px solid black; padding: 2px;height: 80px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="width:280px; padding: 2px;text-align: center">FIRMA</td>
                        <td style="width:400px; padding: 2px;text-align: center">ACLARACION</td>
                        <td style="width:180px; padding: 2px;text-align: center">DNI</td>
                    </tr>

                    

                </table>
        </td>
        
    </tr>
</table>

<table style="width:860px;">
    <tr>
        <td style="width:49%; height:13px;border: 1px solid black; padding: 3px; display: inline-block">
            Observación instalador:
        </td>
        <td style="width:49%; height:13px;border: 1px solid black; padding: 3px; display: inline-block">
            Observación cliente:
        </td>
        
        <td style="width:47%; height:80px;border: 1px solid black; padding: 11px; display: inline-block"></td>
        
        <td style="width:47%; height:80px;border: 1px solid black; padding: 11px; display: inline-block;"></td>
    </tr>
    <tr>
        <td style=" width:100%; text-align: right; font-size: 8px">
            Se da conformidad y funcionamiento del servicio
        </td>
    </tr>
</table>
<!-- Fin Datos instalador -->

<!-- Inicio Equipamiento 2 -->
<table id="container" style="width:860px;">
    <tr>
        <td colspan="6" style="color:white; background-color: black">CONTACTOS TECNICOS Y ADMINISTRATIVOS</td>
    </tr>
    <tr>
        <td style="width:210px; border: 1px solid black; padding: 2px;">
            SERVICIO TECNICO
        </td>
        <td style="width:20px; padding: 2px;">&nbsp;</td>
        <td colspan="4" style="width:630px; border: 1px solid black; padding: 2px;">
            <?= $company->technical_service_phone ? $company->technical_service_phone : Config::getValue('ads-contact_technical_service') ?>
        </td>
    </tr>
    <tr>
        <td style="width:210px; border: 1px solid black; padding: 2px;">&nbsp;</td>
        <td style="width:20px; padding: 2px;">&nbsp;</td>
        <td colspan="4" style="width:630px; border: 1px solid black; padding: 2px;">
            <?= Config::getValue('ads-time_technical_service')?>
        </td>
    </tr>
    <?php
    $ads_comercial_office = Config::getValue('ads-comercial-office');
    if ($ads_comercial_office) { ?>
        <tr>
            <td style="width:210px; border: 1px solid black; padding: 1px; font-size: 0.3cm">
                OFICINA COMERCIAL
            </td>
            <td style="width:20px; padding: 2px; font-size: 0.3cm">&nbsp;</td>
            <td colspan="4" style="width:630px; border: 1px solid black; padding: 2px; font-size: 0.3cm">
                <?= $ads_comercial_office ?>
            </td>
        </tr>
    <?php }?>
    <tr>
        <td style="width:210px; border: 1px solid black; padding: 2px;">
            EMAIL
        </td>
        <td style="width:20px; padding: 2px;">&nbsp;</td>
        <td colspan="4" style="width:630px; border: 1px solid black; padding: 2px;">
            <?= $company->email? $company->email : '' ?>
        </td>
    </tr>
    <tr>
        <td style="width:210px; border: 1px solid black; padding: 2px;">
            CONTROL DE CALIDAD
        </td>
        <td style="width:20px; padding: 2px;">&nbsp;</td>
        <td style="width:30px; border: 1px solid black; padding: 2px;"></td>
        <td style="width:20px; padding: 2px;">&nbsp;</td>

        <td style="width:80px; border: 1px solid black; padding: 2px;text-align: center;">
            FECHA
        </td>
        <td style="width:500px; border: 1px solid black; padding: 2px;"></td>
    </tr>
</table>
<!-- Fin Equipamiento 2 -->


<div style="page-break-after: always"> </div>

<!-- PARA EL CLIENTE -->
<!-- Inicio Header -->
<table id="container" style="width:860px">
    <tbody>
    <tr>
        <td style="width:286px; text-align: center;">
            <?php if(isset($company)) { ?>
                <img style="height: 100px; display: inline-block;" src="<?=Url::base(true) . "/" . $company->getLogoWebPath()?>"/>
            <?php } ?>
        </td>
        <?php if(array_key_exists('barcode_url', $code)) { ?>
            <td style="width:286px; text-align: center;">
                <img  width="143px" height="50px" src="<?= $code['barcode_url']?>">
            </td>
        <?php } else { ?>
            <td style="width:286px; text-align: center;">
                <img  width="143px" height="50px" src="<?= Url::to(['/westnet/ads/barcode', 'code'=> $code['payment_code']], true)?>">
            </td>
        <?php } ?>
        <td style="width:286px; text-align: center;">
            <table>
                <tbody>
                <tr>
                    <td style="border: 1px solid black; width: 80px">
                        CLIENTE
                    </td>
                    <td style="border: 1px solid black">
                        <?php 
                            if(isset($model)){
                                echo $model->customer->code;
                            }else{
                                echo $code['code'];
                            }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; width: 80px">
                        FECHA
                    </td>
                    <td style="border: 1px solid black">
                        <?php
                            if(isset($model)){
                             echo (new DateTime())->format('d/m/Y'); 
                                      
                             } 
                        ?>
                    </td>
                </tr>
                 <?php if(!isset($model)):?>
                    <tr>
                        <td style="border: 1px solid black; width: 80px">
                            FECHA DE IMPRESION
                        </td>
                        <td style="border: 1px solid black; width: 80px">
                            <?= date('d/m/Y')?>
                        </td>
                    </tr>    
                 <?php endif;?>
                <tr>
                    <td colspan="2">
                        FICHA PARA EL CLIENTE
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
<!-- Fin Header -->

<!-- Inicio Datos del cliente 1 -->
<table id="container" style="width:860px;border:1px solid black;">
    <tbody>
    <tr>
        <td colspan="4" style="color:white; background-color: black">DATOS DEL CLIENTE</td>
    </tr>
    <tr>
        <td style="width:210px; border: 1px solid black; padding: 2px;">
            APELLIDO Y NOMBRE / RAZON SOCIAL
        </td>
        <td style="width:650px; border: 1px solid black; padding: 2px;" colspan="3">
            <?php
                if(isset($model)){
                    echo $model->customer->lastname . ", " . $model->customer->name;
                }
            ?>
        </td>
    </tr>
    <tr>
        <td style="width:210px; border: 1px solid black; padding: 2px;">
            DIRECC. INSTALACION
        </td>
        <td style="width:650px; border: 1px solid black; padding: 2px; font-size: 8px" colspan="3">
            <?php
                if(isset($model)){
                    echo $model->address->getFullAddress();
                }
            ?>
        </td>
    </tr>
    <tr>
        <td style="width:210px; border: 1px solid black; padding: 2px;">
            LOCALIDAD
        </td>
        <td style="width:350px; border: 1px solid black; padding: 2px;">
            <?php
                if(isset($model)){
                    echo ($model->address->zone ? $model->address->zone->name : '' );
                }
            ?>
        </td>
        <td style="width:210px; border: 1px solid black; padding: 2px;">
            COD.POSTAL
        </td>
        <td style="width:90px; border: 1px solid black; padding: 2px;">
        </td>
    </tr>
    <tr>
        <td style="width:210px; border: 1px solid black; padding: 2px;">
            TELEFONO
        </td>
        <td style="width:216px; border: 1px solid black; padding: 2px;">
            <?php
                if(isset($model)){
                    echo $model->customer->phone;
                }
            ?>
        </td>
        <td style="width:216px; border: 1px solid black; padding: 2px;">
            CELULAR
        </td>
        <td style="width:216px; border: 1px solid black; padding: 2px;">
            <?php
                if(isset($model)){
                    echo $model->customer->phone2 . ( $model->customer->phone3 ? " - " . $model->customer->phone3 :  "" );
                }
            ?>
        </td>
    </tr>
    <tr>
        <td style="width:210px; border: 1px solid black; padding: 2px;">
            EMAILS
        </td>
        <td style="width:216px; border: 1px solid black; padding: 2px;" colspan="3">
            <?php
                if(isset($model)){
                    echo $model->customer->email . ( $model->customer->email2 ? " - " . $model->customer->email2 :  "" );
                }
            ?>
        </td>
    </tr>
    <tr>
        <td style="width:210px; border: 1px solid black; padding: 2px;">
            CONTACTO
        </td>
        <td style="width:216px; border: 1px solid black; padding: 2px;" colspan="3">

        </td>
    </tr>
    </tbody>
</table>
<!-- Fin Datos del cliente 1 -->
<div class="divisor"></div>
<!-- Inicio Equipamiento 1 -->
<table id="container" style="width:860px">
    <tr>
        <td colspan="11" style="color:white; background-color: black">EQUIPAMIENTO EN COMODATO INSTALADO: tildar lo que corresponda</td>
    </tr>
    <tr>
        <td style="width:60px; border: 1px solid black; padding: 2px;">
            ANTENA
        </td>
        <td style="width:20px; padding: 2px;">&nbsp;</td>
        <td style="width:30px; border: 1px solid black; padding: 2px;">
        <td style="width:20px; padding: 2px;">&nbsp;</td>


        <td style="width:60px; border: 1px solid black; padding: 2px;">
            RADIO
        </td>
        <td style="width:20px; padding: 2px;">&nbsp;</td>
        <td style="width:30px; border: 1px solid black; padding: 2px;">
        <td style="width:20px; padding: 2px;">&nbsp;</td>

        <td style="width:230px; border: 1px solid black; padding: 2px;">
            Nº DE SERIE RADIO
        </td>
        <td style="width:10px; padding: 2px;">&nbsp;</td>
        <td style="width:550px; border: 1px solid black; padding: 2px;">
    </tr>
</table>
<!-- Fin Equipamiento 2 -->

<!-- Inicio Equipamiento 2 -->
<table id="container" style="width:860px;">
    <tr>
        <td colspan="5" style="color:white; background-color: black">EQUIPAMIENTO INSTALADO Y/O SERVICIOS A FACTURAR</td>
    </tr>
   <tr>
        <td style="color:white; background-color: black; text-align: center;">ITEM</td>
        <td style="color:white; background-color: black; text-align: center;">CANTIDAD</td>
        <td style="color:white; background-color: black; text-align: center;">CUOTAS</td>
        <td style="color:white; background-color: black; text-align: center;">PRECIO</td>
    </tr>
    <?php
    if(isset($model)){
        foreach ($model->contractDetails as $detail){
                if($detail->product->type !== 'plan'){
                    error_log(print_r($detail->fundingPlan,1));
                    echo '<tr>';
                    echo '<td style="border: 1px solid black;">'. $detail->product->name . '</td>';
                    echo '<td style="border: 1px solid black; text-align: center;">'. $detail->count. '</td>';
                    if(!empty($detail->fundingPlan)){
                        echo '<td style="border: 1px solid black; text-align: center;">'. $detail->fundingPlan->qty_payments. '</td>';
                        echo '<td style="border: 1px solid black; text-align: center;">'. Yii::$app->formatter->asCurrency($detail->fundingPlan->getFinalAmount())  . '</td>';
                    }else{
                        echo '<td style="border: 1px solid black; text-align: center;"> 1 </td>';
                        echo '<td style="border: 1px solid black; text-align: center;">'. Yii::$app->formatter->asCurrency($detail->product->finalPrice);
                    }
                    echo '</tr>';
                }
            }
    }else {
        echo '<tr><td style="border: 1px solid black; width: 400px; heigth: 50px;"> </td><td style="border: 1px solid black; heigth: 50px;">&nbsp; </td><td style="border: 1px solid black; heigth: 50px;">&nbsp;</td><td style="border: 1px solid black; heigth: 50px;">&nbsp;</td></tr>';
        echo '<tr><td style="border: 1px solid black; width: 400px; heigth: 50px;"> </td><td style="border: 1px solid black; heigth: 50px;">&nbsp; </td><td style="border: 1px solid black; heigth: 50px;">&nbsp;</td><td style="border: 1px solid black; heigth: 50px;">&nbsp;</td></tr>';
    }
            
            ?>
</table>
<!-- Fin Equipamiento 2 -->
<div class="divisor"></div>

<!-- Inicio Datos de Conexion -->
<table id="container" style="width:860px;">
    <tr>
            <td colspan="6" style="color:white; background-color: black">

                <table style="width: 100%">
                    <tr>
                        <td>DATOS DE CONEXION </td>
                        <td  style="color:white; background-color: black; text-align: right">
                            CIR:<?= Config::getValue('default_cir') ?>% |
                            DISPONIBILIDAD DEL SERVICIO ANUAL <?= Config::getValue('annual_availability') ?>%

                        </td>
                    </tr>
                </table>
            </td>
    </tr>
</table>

<table style="width:860px;">
    <tr>
        <td style="width:99%; height:12px; padding: 2px; display: inline-block">
            Tipo de Conexion:
        </td>

        <td style="width:25%; height:40px;border: 1px solid black; padding: 10px; display: inline-block">
            [__] Empresa <br>
            [__] Hogar <br>
        </td>

        <td style="width:25%; height:40px;border: 1px solid black; padding: 10px; display: inline-block; padd">
            [__] Fibra <br>
            [__] Wireless <br>
        </td>

        <td style="width:40%; height:40px;border: 1px solid black; padding: 10px; display: inline-block">
            <?php
            $i = 1;
            foreach($plans as $plan) {

                echo "[__] " . $plan->ads_name . ($i==3 ? "<br/>" : "&nbsp;&nbsp;&nbsp;" );
                $i++;
                if($i==4) {
                    $i = 1;
                }
            }
            ?>
        </td>
    </tr>
</table>

<!-- Fin Datos de Conexion -->

<!-- Fin Datos de Conexion -->
<div>
    <h3 style="padding-bottom: 0"><?= Config::getValue('ads-title')?></h3>
    <?= str_replace('@Empresa', $company->name,Config::getValue('ads-message')) ?>
</div>

<!-- Inicio Datos instalador -->
<table id="container" style="width:860px;">
    <tr>
        <td>
            <table  style="width:425px;">
                <tr>
                    <td colspan="3" style="color:white; background-color: black">DATOS INSTALADOR</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 2px;height: 50px;">&nbsp;</td>
                    <td style="border: 1px solid black; padding: 2px;height: 50px;">&nbsp;</td>
                    <td style="border: 1px solid black; padding: 2px;height: 50px;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="width:280px; padding: 2px;text-align: center">FIRMA</td>
                    <td style="width:400px; padding: 2px;text-align: center">ACLARACION</td>
                    <td style="width:180px; padding: 2px;text-align: center">DNI</td>
                </tr>

            </table>
        
<!-- Fin Datos instalador -->
        </td>

<!-- Inicio Datos instalador -->
        <td>
            <table  style="width:425px;">
                    <tr>
                        <td colspan="3" style="color:white; background-color: black">CONFORMIDAD DEL CLIENTE</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black; padding: 2px;height: 50px;">&nbsp;</td>
                        <td style="border: 1px solid black; padding: 2px;height: 50px;">&nbsp;</td>
                        <td style="border: 1px solid black; padding: 2px;height: 50px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="width:280px; padding: 2px;text-align: center">FIRMA</td>
                        <td style="width:400px; padding: 2px;text-align: center">ACLARACION</td>
                        <td style="width:180px; padding: 2px;text-align: center">DNI</td>
                    </tr>
                </table>
        </td>
    </tr>
</table>

<table style="width:860px;">
    <tr>
        <td style="width:49%; height:13px;border: 1px solid black; padding: 3px; display: inline-block">
            Observación instalador:
        </td>
        <td style="width:49%; height:13px;border: 1px solid black; padding: 3px; display: inline-block">
            Observación cliente:
        </td>
        
        <td style="width:47%; height:56px;border: 1px solid black; padding: 0 11px; display: inline-block"></td>
        
        <td style="width:47%; height:56px;border: 1px solid black; padding: 0 11px; display: inline-block;"></td>
    </tr>

    <tr style="line-height: 8px; margin: 0; padding: 0;">
        <td style=" width:100%; text-align: right; font-size: 8px; line-height: 8px; margin: 0; padding: 0;">
            Se da conformidad y funcionamiento del servicio
        </td>
    </tr>
</table>

<!-- Fin Datos instalador -->

<!-- Inicio Equipamiento 2-->
<table id="container" style="width:860px;">
    <tr>
        <td colspan="6" style="color:white; background-color: black">CONTACTOS TECNICOS Y ADMINISTRATIVOS</td>
    </tr>
    <tr>
        <td style="width:210px; border: 1px solid black; padding: 2px; font-size: 0.3cm">
            SERVICIO TECNICO
        </td>
        <td style="width:20px; padding: 2px; font-size: 0.3cm">&nbsp;</td>
        <td colspan="4" style="width:630px; border: 1px solid black; padding: 2px; font-size: 0.3cm">
            <?= $company->technical_service_phone ? $company->technical_service_phone : Config::getValue('ads-contact_technical_service')?>
        </td>
    </tr>
    <tr>
        <td style="width:210px; border: 1px solid black; padding: 2px; font-size: 0.3cm">&nbsp;</td>
        <td style="width:20px; padding: 2px; font-size: 0.3cm">&nbsp;</td>
        <td colspan="4" style="width:630px; border: 1px solid black; padding: 2px; font-size: 0.3cm">
            <?= Config::getValue('ads-time_technical_service')?>
        </td>
    </tr>
    <?php
    $ads_comercial_office = Config::getValue('ads-comercial-office');
    if ($ads_comercial_office) { ?>
        <tr>
            <td style="width:210px; border: 1px solid black; padding: 1px; font-size: 0.3cm">
                OFICINA COMERCIAL
            </td>
            <td style="width:20px; padding: 2px; font-size: 0.3cm">&nbsp;</td>
            <td colspan="4" style="width:630px; border: 1px solid black; padding: 2px; font-size: 0.3cm">
                <?= $ads_comercial_office ?>
            </td>
        </tr>
    <?php }?>

    <?php if($company->email) { ?>
        <tr>
            <td style="width:210px; border: 1px solid black; padding: 2px; font-size: 0.3cm">
                EMAIL
            </td>
            <td style="width:20px; padding: 2px; font-size: 0.3cm">&nbsp;</td>
            <td colspan="4" style="width:630px; border: 1px solid black; padding: 2px;">
                <?= $company->email ? $company->email : '' ?>
            </td>
        </tr>
    <?php }?>
    <tr>
        <td style="width:210px; border: 1px solid black; padding: 2px; font-size: 0.3cm">
            CONTROL DE CALIDAD
        </td>
        <td style="width:20px; padding: 2px; font-size: 0.3cm">&nbsp;</td>
        <td style="width:30px; border: 1px solid black; padding: 2px; font-size: 0.3cm"></td>
        <td style="width:20px; padding: 2px; font-size: 0.3cm">&nbsp;</td>

        <td style="width:80px; border: 1px solid black; padding: 2px;text-align: center; font-size: 0.3cm">
            FECHA
        </td>
        <td style="width:500px; border: 1px solid black; padding: 2px; font-size: 0.3cm"></td>
    </tr>
</table>

<table id="container" style="width:860px;">
    <tr>
        <td colspan="6" style="color:white; background-color: black">MEDIOS DE PAGO</td>
    </tr>
        <tr>
            <td colspan="6" style="width:100%; border: 1px solid black; font-size: 0.3cm">
                <?= Config::getValue('ads_payment_methods') ?>
            </td>
        </tr>
</table>

 <?php if(count($codes)> 1 && $key !== (count($codes)-1)){?>
    <div style="page-break-after: always"> </div>
<?php } ?>

 <?php } ?>
<?php }catch(\Exception $ex){\Yii::debug($ex);} ?>