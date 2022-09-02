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
$urlBase = Url::base(($use_https) ? 'https' : false);

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

<body style="margin: 0;padding: 0;background-color: #EEEEEE;">
  <center style="width: 100%;table-layout: fixed;background-color: #EEEEEE;padding: 20px 0;">
    <div style="width: 550px;background-color: #F9EEFE;border-radius: 23px;">
      <table align="center" style="font-family: 'Lato' ;border-spacing: 0;margin: 0;width: 100%;border-spacing: 0;color: #171717;">


        <!-- actual message content -->
        <tr>
          <td style="padding: 0;">
            <table width="100%" style="border-spacing: 0;">
              <tr>
                <td height="300" style="padding: 0 0 3.5% 0;border-radius: 23px; background: linear-gradient(150deg, #8b46ac 23%, #8b46ac 45%, #fff56c 100%);">
                  <!-- logo -->
                  <table align="center" style="border-spacing: 0; padding: 15px 0;">
                    <tr>
                      <td>
                        <!-- LOGO BIGWAY -->
                        <div class="logoDiv" style=" border-radius: 23px;">
                          <?= Html::img($urlBase . '/images/logo-bigway.png',
                            ['alt' => 'BigWaylogo', 'class' => 'footer-img', 'style' => "width: 60%;border-radius: 23px;margin-left: 109px;margin-right: 109px;"]) ?>
                        </div>
                      </td>
                    </tr>
                  </table>

                  <!-- title plus message -->
                  <table class="email-content" align="center" style="width: auto;margin: 0;border-spacing: 0; padding: 0 20px;">
                    <tr>
                      <td width="15px" height="auto" style="padding: 0;background-color: #ffffff; border-radius: 25px 0 0 0;">
                        &nbsp;
                      </td>
                      <td height="auto" style="max-width:100px;text-align: center;  background-color: #ffffff;">
                        <table align="center" style="border-spacing: 0;">
                          <tr>
                            <td style="padding: 0;text-align: center;color: #000000;">
                              <p style="font-weight: bold;font-size: 24px; letter-spacing: 1.6px;margin: 20px 0;">
                                <?= $title ?>
                              </p>
                            </td>
                          </tr>

                        </table>
                      </td>
                      <td width="15px" height="auto" style="background-color: #FFFFFF; border-radius: 0 25px 0 0;">
                        &nbsp;
                      </td>
                    </tr>
                    <tr>
                      <td width="15px" height="auto" style="background-color: #FFFFFF; border-radius: 0 0 0 25px;">
                        &nbsp;
                      </td>
                      <td height="auto" style="text-align: center; padding-bottom: 15px; padding-top: 0px; background-color: #ffffff;">
                        <table align="center" style="border-spacing: 0;">

                          <tr>
                            <td width="800px" style="text-align: center;">
                              <table align="center" style="border-spacing: 0;font-size:14px;letter-spacing: 1.2px;">
                                <tr>
                                  <td style="padding: 0;">
                                    <p style="font-size:12px;margin: 20px 0;">
                                      <?= $notification->content ?>
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
          <td style=" background-color: #F9EEFE;">
            <table width="100%" style="border-spacing: 0;text-align: center;">
              <tr>
                <td style="background-color: #F9EEFE;">
                  <table class="email-footer" style="border-spacing: 0;">
                    <tr class="footer-item" >
                      <td style="width: 207px;"></td>
                      <td>
                        <p style="font-size: 9px;"> Visitanos en
                          <br>
                          <a style="color: #0645AD;text-decoration: none;font-size: 11px;" href="https://www.bigway.com.ar">https://www.bigway.com.ar</a>
                        </p>
                      </td>
                      <td style="width: 207px;"></td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr class="footer-item">
          <td>
            <p style="color: #595757; font-size:9px;font-weight:bold;text-align: center;">
              Correo enviado automáticamente
            </p>
          </td>
        </tr>

      </table>
    </div>
  </center>
</body>

</html>
<?php $this->endPage() ?>