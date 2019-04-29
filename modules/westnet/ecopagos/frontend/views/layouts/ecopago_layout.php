<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\components\widgets\agenda\notification\Notification;
use app\components\widgets\agenda\task\Task;
use app\modules\westnet\ecopagos\frontend\assets\EcopagoAsset;

/**
 * @var \yii\web\View $this
 * @var string $content
 */
EcopagoAsset::register($this);
?>

<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

    <head>

        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= Html::encode($this->title) ?></title>
        <link rel="stylesheet" type="text/css" href="" />
        <?php $this->head() ?>
        <?= Html::csrfMetaTags() ?>    

    </head>

    <body>
        <div class="wrap">

            <?php
            //Main navbar
            echo $this->render('_menu');
            ?>

            <?php $this->beginBody() ?>

            <div class="container-fluid margin-top-navbar padding-top-half">

                <!-- Breadcrumbs -->
                <div class="container no-padding">
                    <div class="text-center z-depth-1">
                        <?=
                        Breadcrumbs::widget([
                            'homeLink' => [
                                'label' => 'Home',
                                'url' => \yii\helpers\Url::to(['site/index']),
                            ],
                            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                        ])
                        ?>
                    </div>
                </div>
                <!-- end Breadcrumbs -->
                <br><br>
                <!-- Alerts -->
                <div class="container position-relative" style="z-index: 20;">
                    <div class="row">
                        <div class="col-xs-12">
                            <h3>
                            <?php
                            $flashes = Yii::$app->getSession()->getAllFlashes();
                            foreach ($flashes as $class => $flash):
                                if ($class == 'error')
                                    $class = 'danger';
                                ?>
                                <?=
                                \yii\bootstrap\Alert::widget([
                                    'options' => [
                                        'class' => 'alert-' . $class
                                    ],
                                    'body' => $flash
                                ]);
                                ?>
                            <?php endforeach; ?>
                            </h3>    
                        </div>
                    </div>
                </div>
                <!-- end Alerts -->

                <?= $content ?>

            </div>

        </div>

        <?php $this->endBody() ?>

        <?php
        //Footer
        //echo $this->render('_footer');
        ?>

    </body>

</html>

<?php $this->endPage() ?>
