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
  <style type="text/css">
    body {
      margin: 0;
      padding: 0;
    }
    table {
      border-spacing: 0;
    }
    td{
      padding:0;
    }
    img{
      border:0;
    }


    @media screen and (max-width:600px){

    }

    @media screen and (max-width:400px){

    }

  </style>
  <?php $this->head() ?>
  
</head>

<body>
  <div class="content" style="background-color:#f9f9f9;margin:0 auto;color:#292929;width:100%;">
    <table align="center" cellpadding="0" cellspacing="0" width="100%" style="background:#f9f9f9;">
      <tr style="background:#1C3AE2;">
        <td style="background:#1C3AE2;width:2rem;">&nbsp;</td>
        <td style="height:4rem;">&nbsp;</td>
        <td style="background:#1C3AE2;width:2rem;">&nbsp;</td>
      </tr>
      <tr style="background:#1C3AE2;">
        <td style="background:#1C3AE2;width:available;">&nbsp;</td>
        <td style="height:100%;">
          <table align="center" cellpadding="0" cellspacing="0" width="500px" style="background:#f9f9f9;">
            <tr>
              <td style="height:2rem;width:auto;">&nbsp;</td>
            </tr>
            <tr >
              <td style="width:2rem;">&nbsp;</td>
              <td style="width:auto;">
                <table align="center" cellpadding="0" cellspacing="0" width="100%" style="background:#FFFFFF;">
                  <tr>
                    <td>
                      <table align="center" cellpadding="0" cellspacing="0" width="100%" style="background:red;">
                        <tr><td style="height:5px;">&nbsp;</td></tr>
                        <tr><td style="text-align:center;font-size:25px;font-weight:bold;line-height:1.2;vertical-align:bottom">
                      Su factura ya se encuentra disponible
                    </td></tr>
                    <tr><td>&nbsp;</td></tr>
                      </table>
                    </td>
                    
                  </tr>
                  
                </table>
              </td>
              <td style="width: 2rem;">&nbsp;</td>
            </tr>
          </table>
        </td>
        <td style="background:#1C3AE2;width:available;">&nbsp;</td>
        
      </tr>
      <tr style="background:#1C3AE2;">
        <td style="background:#1C3AE2;width:available;">&nbsp;</td>
        <td style="height:100%;">
        &nbsp;
        </td>
        <td style="background:#1C3AE2;width:available;">&nbsp;</td>
        
      </tr>
    </table>

  </div>
</body>

</html>
<?php $this->endPage() ?>