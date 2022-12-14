<?php

use app\modules\config\models\Config;
use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */

$notification = Yii::$app->view->params['notification'];
$title = $notification['subject'];

$use_https = isset($notification['use_https']) ? $notification['use_https'] : false; // used for setting the Yii:Base 'https' parameter for every image (except the one of SiroLogo?, stil..)
$urlBase = Url::base(($use_https)?'https':false);

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

<body style="margin: 0;padding: 0;background-color: #FFFFFF;">
  <center style="width: 100%;table-layout: fixed;background-color: #FFFFFF;padding: 20px 0;">
    <div style="width: 550px;background-color: #FFFFFF;">
      <table align="center" style="font-family: 'Lato' ;border-spacing: 0;margin: 0;width: 100%;border-spacing: 0;color: #171717;">
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
                <td height="300" style="padding: 0;background-color: #EEEEEE;">
                  <!-- logo -->
                  <table align="center" style="border-spacing: 0; padding: 15px 0;">
                    <tr>
                      <td style="background-color: #EEEEEE; text-align: center;">
                        <a style="color: #0645AD;text-decoration: none;font-size: 11px;" href="https://westnet.com.ar/">
                          <?= Html::img(
                            $urlBase . '/images/westnet-small-highres.png',
                            ['alt' => 'Logo', 'style' => 'border: 0;width: 160px;']
                          ) ?>
                        </a>
                      </td>
                    </tr>
                  </table>
                  <!-- title plus message -->
                  <table class="email-content" align="center" style="width: auto;margin: 0;border-spacing: 0; padding: 0 20px;">
                    <tr>
                      <td width="15px" height="auto" style="padding: 0;background-color: #1C3AE2; border-radius: 25px 0 0 0;">
                        &nbsp;
                      </td>
                      <td height="auto" style="max-width:100px;text-align: center;  background-color: #1C3AE2;">
                        <table align="center" style="border-spacing: 0;">
                          <tr>
                            <td style="padding: 0;text-align: center;color: #FFFFFF;">
                              <p style="font-weight: bold;font-size: 24px; letter-spacing: 1.6px;margin: 20px 0;">
                                <?= $title ?>
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
                              <table align="center" style="border-spacing: 0;font-size:9px;letter-spacing: 1.2px;">
                                <tr>
                                  <td style="padding: 0;">
                                    <p style="font-size:12px;margin: 20px 0;">
                                      <?= $notification->content ?>
                                    </p>
                                  </td>
                                </tr>
                                <tr>
                                  <td style="padding: 0;">
                                    <p style="font-size:12px;margin: 20px 0;">
                                      Consult?? todos nuestros medios de pago
                                      <br>
                                      <br>
                                      <a style="color: #0645AD;text-decoration: none;font-size: 11px;" href="https://westnet.com.ar/medios-de-pago/">
                                        <?= Html::img(
                                          $urlBase . '/images/notifications/payment-methods-icon.png',
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
                          Descarg?? nuestra App de celular para Android e iOS
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
                                  $urlBase . '/images/notifications/android-icon.png',
                                  ['alt' => 'Android-App', 'class' => 'footer-img', 'style' => 'border: 0;margin: 0 10px;width: 25px;']
                                ) ?>

                              </a>
                            </td>
                            <td style="padding: 0;">
                              <a style="color: #0645AD;text-decoration: none;font-size: 11px;" href="https://apps.apple.com/ar/app/westnet/id1491036341">
                                <?= Html::img(
                                  $urlBase . '/images/notifications/apple-icon.png',
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
                          Atenci??n al cliente
                        </p>
                        <a style="color: #0645AD;text-decoration: none;font-size: 11px;" href="https://westnet.com.ar/atencion-al-cliente/">
                          <?= Html::img(
                            $urlBase . '/images/notifications/chat-icon.png',
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
                                  $urlBase . '/images/notifications/twitter-icon.png',
                                  ['alt' => 'twitter-page', 'class' => 'footer-img', 'style' => 'border: 0;margin: 0 10px;width: 20px;']
                                ) ?>

                              </a>
                            </td>
                            <td style="padding: 0;">
                              <a style="color: #0645AD;text-decoration: none;font-size: 11px;" href="https://www.facebook.com/internet.westnet/">
                                <?= Html::img(
                                  $urlBase . '/images/notifications/facebook-icon.png',
                                  ['alt' => 'facebook-page', 'class' => 'footer-img', 'style' => 'border: 0;margin: 0 10px;width: 20px;']
                                ) ?>
                              </a>
                            </td>
                            <td style="padding: 0;">
                              <a style="color: #0645AD;text-decoration: none;font-size: 11px;" href="https://www.instagram.com/westnet.internet/">
                                <?= Html::img(
                                  $urlBase . '/images/notifications/instagram-icon.png',
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
                          Este email es enviado autom??ticamente
                          <br>
                          Por favor, no responder a este correo
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