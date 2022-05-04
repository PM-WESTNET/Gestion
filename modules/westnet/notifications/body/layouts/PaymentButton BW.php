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
    <title>
        <?= $title ?>
    </title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&display=swap" rel="stylesheet">
    <?php $this->head() ?>


    <style>

        .parent {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-template-rows: repeat(6, 1fr);
        grid-column-gap: 0px;
        grid-row-gap: 0px;
        }

        .div1 { grid-area: 1 / 1 / 3 / 3; }
        .div2 { grid-area: 3 / 1 / 4 / 3; }
        .div3 { grid-area: 4 / 1 / 5 / 3; }
        .div4 { grid-area: 5 / 1 / 6 / 3; }
        .div5 { grid-area: 6 / 1 / 7 / 3; }


        .subparent {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: 1fr;
            grid-column-gap: 0px;
            grid-row-gap: 0px;
        }

        .subdiv1 { grid-area: 1 / 1 / 2 / 2; }
        .subdiv2 { grid-area: 1 / 2 / 2 / 3; }




    </style>




</head>


<body style="margin: 0;padding: 0;background-color: #E5E5E5;font-family: 'Open Sans', sans-serif;overflow: auto;">


    <div class="parent" style="width: 500px; left: -392px; top: -388px; border-radius: 24px; background-color:#EBCBFF; margin: 3rem 30% 7rem ;display: inline-block;">
        <!-- LOGO BIGWAY -->
        <div class="div1" style=" height: 330px; width: auto; left: -392px; top: -388px; border-radius: 23px; background: linear-gradient(150deg, #8b46ac 23%, #8b46ac 45%, #fff56c 100%); ">
            
            <!-- <img src="/web/images/logo-bigway.png" alt=" " style="margin: 3.2% 0 0 5%;width: 90%;border-radius: 23px;filter: invert(1);"> -->

            <?=



            Html::img(
                $urlBase . '/images/logo-bigway.png',
                ['alt' => 'BigWaylogo', 'class' => 'footer-img', 'style' => 'margin: 3.2% 0 0 5%;width: 90%;border-radius: 23px;filter: invert(1);']
            ) ?>

            


        </div>
        <!-- TITULO -->
        <div class="div2" style="position: static;padding: auto;">
            <div style="height: auto;width: auto;margin: 10% 2% 2% 2%;background-color: #9B41D1; border-radius: 0 0 24px 24px; color: white;display:block; ">
                <p style="font-size: 25px;text-align: center; "><?= $title ?></p>
            </div>
        </div>

        <!-- CONTENIDO -->
        <div class="div3" style="position: static;padding: 2% 5% 2% 5%;">
            <div style="border-radius: 8px;background-color:#9B41D1;color: white;display: block;margin-left:auto;margin-right: auto;">
                <p style="font-size: 30px;text-align: center;padding: 2% 0 2% 0;margin: 2px 5px 2px 5px; "><?= $notification['content'] ?></p>
            </div>
        </div>


        <div class="div4" style="position: relative; bottom: -50px;">
            <!-- ADVERTENCIA "no responder el mail " -->
            <div style="text-align: center;color: #700b84; ">
                <p>Este correo es enviado automaticamente </p>
                <p>No responder este correo !</p>
            </div>
        </div>

        <!-- CONTACTO -->
        <div class="div5 subparent" style="color: #700b84;display: flex; margin: 0 0 15px 0;">
            <div class="subdiv1" style="font-size: 17px;text-align: center;margin-left: 2%;margin-right:45%">
                <p>Atencion al cliente</p>
                <div>
                  <!-- ICONO CHAT -->
                    <a style="color: #0645AD;text-decoration: none;font-size: 11px;" href="https://bigway.com.ar/site/#contactanos" target="_blank">                    
                      <?= Html::img(
                            $urlBase . '/images/notifications/chat-icon.png',
                            ['alt' => 'chat-icon', 'class' => 'footer-img', 'style' => 'border: 0;margin: 0 10px;width: 35px;']
                      ) ?>
                    </a>
                </div>
            </div>
            <div class="subdiv2" style="font-size: 17px;text-align: center; margin-left: 410px; padding 2px;">
                <p>Nuestras redes sociales</p>
                <div>
                    <!-- ICONO TWITTER -->
                    <a style="color: #0645AD;text-decoration: none;font-size: 11px;" href="https://twitter.com/bigway_internet?lang=es" target="_blank">
                        
                      <?= Html::img(
                                  $urlBase .'/images/notifications/twitter-icon.png',
                                  ['alt' => 'twitter-page', 'class' => 'footer-img', 'style' => 'border: 0;margin: 0 10px;width: 35px;']
                      )?>
                    </a>
                    
                    <!-- ICONO INSTAGRAM -->
                    <a style="color: #0645AD;text-decoration: none;font-size: 11px;" href="https://instagram.com/bigway.internet?utm_medium=copy_link" target="_blank">
                      <?= Html::img(
                                  $urlBase . '/images/notifications/instagram-icon.png',
                                  ['alt' => 'instagram-page', 'class' => 'footer-img', 'style' => 'border: 0;margin: 0 10px;width: 35px;']
                      ) ?>
                    </a>
                    
                    <!-- ICONO FACEOOK -->
                    <a style="color: #0645AD;text-decoration: none;font-size: 11px;" href="https://www.facebook.com/bigway.internet/" target="_blank">
                    
                        

                      <?= 
                      
                      

                      Html::img(
                        $urlBase . '/images/notifications/facebook-icon.png',
                        ['alt' => 'facebook-page', 'class' => 'footer-img', 'style' => 'border: 0;margin: 0 10px;width: 35px;']
                      )



                       ?>                 
                  </a>
                </div>
            </div>
        </div>
    </div>


</body>

</html>
<?php $this->endPage() ?>