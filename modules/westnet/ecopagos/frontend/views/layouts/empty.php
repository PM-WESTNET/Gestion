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
        <?php $this->head() ?>
        <?= Html::csrfMetaTags() ?>

    </head>

    <body>

        <?php $this->beginBody() ?>

        <?= $content ?>

        <?php $this->endBody() ?>

        <?php
        //Footer
        echo $this->render('_footer');
        ?>

    </body>

</html>

<?php $this->endPage() ?>
