<?php 
use app\modules\config\models\Config;
use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */

$notification = Yii::$app->view->params['notification'];
$title = $notification->subject;
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
    <div class="content" style="background-color: white; margin: 0 auto; color: #292929; width: 600px;">
      <table style="width: 100%; font-family: Arial, Helvetica, sans-serif;">
        <thead>
          <tr>
            <th colspan="9">
            <?= Html::img(Url::base().'/images/logo-westnet.png', ['alt' => 'Westnet', 'style' => 'margin: 0 auto; width: 140px;']) ?>
            </th>
          </tr>
        </thead>

        <tbody>
            
          <!-- Mensaje info -->
          <tr>
            <td style="background-color: #0069ff; padding: 10px; color: white;">
               <h4 style="margin: 10px; text-align: center; line-height: 1.5em;"><?= Config::getValue('mail-top-title') ?></h4>
               <h2 style="margin: 10px; text-align: center; line-height: 1.5em;"><?= $title ?></h2>
            </td>            
          </tr>
          
          <tr>
            <td>
                <?= $notification->content ?>
            </td>
          </tr>
          
        </tbody>

          <?= $this->renderFile('@app/modules/westnet/notifications/body/partials/_footer.php', ['company'=> $notification->emailTransport->getObject()]) ?>
        
      </table>
    </div>
  </body>
</html>
<?php $this->endPage() ?>
