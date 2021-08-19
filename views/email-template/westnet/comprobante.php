<?php
use yii\helpers\Html;
$formatter = new \yii\i18n\Formatter();
?>
<table width="600">
    <!-- Header -->
    <!-- Header -->
    <tr >
        <td style="height:80px; width: 50%; padding-bottom: 20px; padding-top: 20px; border-bottom: 1px solid gray;">
	<?= Html::img(Yii::$app->view->params['image'], ['alt' => 'Westnet', 'style' => 'margin: 0 auto; width: 140px;']) ?>
            <p style="text-align: right; display: inline; width: 80%; float: left; color: gray; font-size: 16px; font-family: Arial, sans-serif;">
                <?= $formatter->asDate(new \DateTime("now"))?>
            </p>
        </td>
    </tr>
    <!-- Contenido -->
    <tr>
        <td style="width: 100%; padding: 20px; color: gray; font-size: 16px; font-family: Arial, sans-serif; line-height: 24px;">
            <p style="">
                Estimado cliente,
                le adjuntamos en formato pdf el siguiente comprobante: <?php echo Yii::$app->view->params['comprobante']?>

                Atentamente,
            </p>
        </td>
    </tr>
    <!-- Footer -->
    <?php if(isset($footer)) { ?>
        <tr>
            <td style="background-color: #C1C1C1; color: white; font-size: 12px; line-height: 16px; padding:10px; font-family: Arial, sans-serif;">
                <p><?//$footer?></p>
            </td>
        </tr>
    <?php }?>
</table>
