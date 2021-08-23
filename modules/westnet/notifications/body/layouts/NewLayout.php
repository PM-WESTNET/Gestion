<?php

use app\modules\config\models\Config;
use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */

$notification = Yii::$app->view->params['notification'];
$title = $notification['subject'];
?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $title ?></title>
  <?php $this->head() ?>
</head>

<body style="margin: 0;padding: 0;background-color: #f8fcf6;">
  <center style="width: 100%;table-layout: fixed;background-color: #f8fcf6;padding-bottom: 40px;">
    <div style="max-width: fit-content; background-color: #FFFFFF;">
      <table align="center" style="font-family: 'Lato' ;border-spacing: 0;padding: 5px;margin: 0 auto;width: 100%;max-width: 900px;border-spacing: 0;color: #171717;">
        <tr>
          <td style="padding: 0;">
            <table width="100%" style="border-spacing: 0;border-spacing: 0;">
              <tr>
                <td height="11" style="padding: 0;background-color: #1C3AE2;border-radius: 25px 25px 0 0 !important;">

                </td>
              </tr>
            </table>
          </td>
        </tr>
        <!-- actual message content -->
        <tr>
          <td style="padding: 0;">
            <table width="100%" style="border-spacing: 0;">
              <tr>
                <!-- content height -->
                <td height="300" style="padding: 0;background-color: #EEEEEE;">
                  <table align="center" style="border-spacing: 0; padding: 25px 0;">
                    <tr>
                      <td style="padding: 0;background-color: #EEEEEE; text-align: center;">
                        <a style="color: #0645AD;text-decoration: none;font-size: 11px;" href="https://westnet.com.ar/">
                          <?= Html::img(
                            Url::base(true) . '/images/logo-westnet.png',
                            ['alt' => 'Logo', 'style' => 'border: 0;width: 145px;']
                          ) ?>
                        </a>


                      </td>
                    </tr>

                  </table>
                  <table class="email-content" align="center" style="max-width: 550px;border-spacing: 0; padding: 0 20px;">
                    <tr>
                      <td width="15px" height="auto" style="padding: 0;background-color: #1C3AE2; border-radius: 25px 0 0 0;">
                        &nbsp;
                      </td>
                      <td height="auto" style="max-width:100px;text-align: center;  background-color: #1C3AE2;">
                        <table align="center" style="border-spacing: 0;">
                          <tr>
                            <td style="padding: 0;text-align: center;color: #FFFFFF;">
                              <p style="font-weight: bold;font-size: 24px; letter-spacing: 1.6px;margin: 20px 0;">
                                Su factura ya se encuentra disponible

                              </p>
                            </td>
                          </tr>

                        </table>
                      </td>
                      <td width="15px" height="auto" style="background-color: #1C3AE2; border-radius: 0 25px 0 0;">
                        &nbsp;
                      </td>
                    </tr>
                    <tr>
                      <td width="15px" height="auto" style="background-color: #FFFFFF; border-radius: 0 0 0 25px;">
                        &nbsp;
                      </td>
                      <td height="auto" style="text-align: center; padding-bottom: 15px; padding-top: 15px; background-color: #ffffff;">
                        <table align="center" style="border-spacing: 0;">

                          <tr>
                            <td width="800px" style="text-align: center;">
                              <table style="border-spacing: 0;font-size:9px;letter-spacing: 1.2px;">
                                <tr>
                                  <td style="padding: 0;">
                                    <p style="font-size:12px;margin: 20px 0;">
                                      Estimado cliente @Nombre (cliente número
                                      @CodigoDeCliente), Westnet le informa que ya
                                      se encuentra emitida su factura
                                      correspondiente al mes de Junio, y vence el
                                      día 15/6/2021. Recuerde que puede
                                      descargarla desde nuestra aplicación móvil.
                                    </p>
                                  </td>
                                </tr>
                                <tr>
                                  <td style="padding: 0;">
                                    <p style="font-size:16px; letter-spacing: .5px;font-size: 14px;margin: 20px 0;">
                                      Total a pagar $@Saldo
                                      <br>
                                      Código de Pago Facil y Mercado Pago:
                                      @PaymentCode
                                    </p>
                                  </td>
                                </tr>
                                <tr>
                                  <td style="padding: 0;">
                                    <p style="font-size:12px;margin: 20px 0;">
                                      Consultá todos nuestros medios de pago
                                      <br>
                                      <br>
                                      <a style="color: #0645AD;text-decoration: none;font-size: 11px;" href="https://westnet.com.ar/medios-de-pago/">
                                        <?= Html::img(
                                          Url::base(true) . '/images/notifications/payment-methods-icon.png',
                                          ['alt' => 'Payment-Methods', 'style' => 'border: 0;width: 50px;']
                                        ) ?>
                                      </a>
                                      <br>
                                      <a style="color: #0645AD;text-decoration: none;font-size: 11px;" href="https://westnet.com.ar/medios-de-pago/" style="font-size: 12px;">https://westnet.com.ar/medios-de-pago/</a>
                                    </p>
                                  </td>
                                </tr>

                              </table>
                            </td>
                          </tr>
                        </table>
                      </td>
                      <td width="15px" height="auto" style="background-color: #FFFFFF; border-radius: 0 0 25px 0;">
                        &nbsp;
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>



        <!-- footer -->
        <tr>
          <td style="padding: 0;">
            <table width="100%" style="border-spacing: 0;">
              <tr>
                <td style="background-color: #EEEEEE; padding: 10px; text-align: center;">
                  <table class="email-footer" align="center" style="border-spacing: 0;">
                    <tr class="footer-item">
                      <td style="padding: 0;">
                        <p style="font-size: 9px;margin: 15px 0;margin: 20px 0 8px;
                                                padding: 0;">
                          Descargá nuestra App de celular para Android e iOS
                        </p>
                      </td>
                    </tr>
                    <tr class="footer-item">
                      <td style="padding: 0;">
                        <table style="border-spacing: 0;" align="center">
                          <tr>
                            <td style="padding: 0;">
                              <a style="color: #0645AD;text-decoration: none;font-size: 11px;" href="https://play.google.com/store/apps/details?id=ar.com.westnet.customer.app">
                                <?= Html::img(
                                  Url::base(true) . '/images/notifications/android-icon.png',
                                  ['alt' => 'Android-App', 'class' => 'footer-img', 'style' => 'border: 0;margin: 0 10px;width: 25px;']
                                ) ?>

                              </a>
                            </td>
                            <td style="padding: 0;">
                              <a style="color: #0645AD;text-decoration: none;font-size: 11px;" href="https://apps.apple.com/ar/app/westnet/id1491036341">
                                <?= Html::img(
                                  Url::base(true) . '/images/notifications/apple-icon.png',
                                  ['alt' => 'iOS-App', 'class' => 'footer-img', 'style' => 'border: 0;margin: 0 10px;width: 25px;']
                                ) ?>

                              </a>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr class="footer-item">
                      <td style="padding: 0;">
                        <p style="font-size: 9px;margin: 15px 0;margin: 20px 0 8px;
                                                padding: 0;"> Visitanos en
                          <br>
                          <a style="color: #0645AD;text-decoration: none;font-size: 11px;" href="https://www.westnet.com.ar">https://www.westnet.com.ar</a>
                        </p>

                      </td>
                    </tr>
                    <tr class="footer-item">
                      <td style="padding: 0;">
                        <p style="font-size: 9px;margin: 15px 0;margin: 20px 0 8px;
                                                padding: 0;">
                          Atención al cliente
                        </p>
                        <a style="color: #0645AD;text-decoration: none;font-size: 11px;" href="https://westnet.com.ar/atencion-al-cliente/">
                          <?= Html::img(
                            Url::base(true) . '/images/notifications/chat-icon.png',
                            ['alt' => 'chat-icon', 'class' => 'footer-img', 'style' => 'border: 0;margin: 0 10px;width: 25px;']
                          ) ?>


                        </a>
                      </td>
                    </tr>
                    <tr class="footer-item">
                      <td style="padding: 0;">
                        <p style="font-size: 9px;margin: 15px 0;margin: 20px 0 8px;
                                                padding: 0;">
                          Seguinos en nuestras redes sociales
                        </p>
                      </td>
                    </tr>
                    <tr class="footer-item">
                      <td style="padding: 0;">
                        <table style="border-spacing: 0;" align="center">
                          <tr>
                            <td style="padding: 0;">
                              <a style="color: #0645AD;text-decoration: none;font-size: 11px;" href="https://twitter.com/westnetoficial">
                                <?= Html::img(
                                  Url::base(true) . '/images/notifications/twitter-icon.png',
                                  ['alt' => 'twitter-page', 'class' => 'footer-img', 'style' => 'border: 0;margin: 0 10px;width: 20px;']
                                ) ?>

                              </a>
                            </td>
                            <td style="padding: 0;">
                              <a style="color: #0645AD;text-decoration: none;font-size: 11px;" href="https://www.facebook.com/internet.westnet/">
                                <?= Html::img(
                                  Url::base(true) . '/images/notifications/facebook-icon.png',
                                  ['alt' => 'facebook-page', 'class' => 'footer-img', 'style' => 'border: 0;margin: 0 10px;width: 20px;']
                                ) ?>
                              </a>
                            </td>
                            <td style="padding: 0;">
                              <a style="color: #0645AD;text-decoration: none;font-size: 11px;" href="https://www.instagram.com/westnet.internet/">
                                <?= Html::img(
                                  Url::base(true) . '/images/notifications/instagram-icon.png',
                                  ['alt' => 'instagram-page', 'class' => 'footer-img', 'style' => 'border: 0;margin: 0 10px;width: 20px;']
                                ) ?>
                              </a>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr class="footer-item">
                      <td style="padding: 0;">
                        <p style="margin: 45px 0 0;color: #EA345F; font-size:9px;font-weight:bold;">
                          Este email es enviado automáticamente
                          <br>
                          Por favor, no lo responder a este correo
                        </p>
                      </td>
                    </tr>
                  </table>

                </td>
              </tr>
            </table>
          </td>
        </tr>

      </table>
    </div>
  </center>
</body>

</html>
<?php $this->endPage() ?>