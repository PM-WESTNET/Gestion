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

<body>
  <div class="content" style="background-color:#EEEEEE;margin:0 auto;color:#292929;width:100%;">
  <table align="center" cellpadding="0" cellspacing="0" width="100%" height="10%" style="background:red;">
      <tr style="background:#1C3AE2;border-radius:15px;width:640px;">
        <td style="background:#3D3D3D;font-size:0;border-radius:25px 0 0 6px;height:100%;width:40px;">&nbsp;</td>
        <td >&nbsp;</td>
        <td style="border-radius:0 15px 0 0; height:100%; width:4px;">&nbsp;</td>
      </tr>  
    </table>
    <table align="center" cellpadding="0" cellspacing="0" width="100%" height="100" style="background:red;">

      <tr style="background:#1C3AE2;">
        <td style="border-radius:15px 0 0 0; height:100%;">&nbsp;</td>
        <td style="background:#FFFFFF;width:70%;">&nbsp;</td>
        <td style="border-radius:0 15px 0 0; height:100%;">&nbsp;</td>
      </tr>      
    </table>
  </div>
</body>

</html>
<?php $this->endPage() ?>

