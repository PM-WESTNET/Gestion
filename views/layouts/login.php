<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

/**
 * @var \yii\web\View $this
 * @var string $content
 */
AppAsset::register($this);
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
    <div class="wrap">
        <div class="text-center">
            <img src="images/logo-westnet.png" width="180" style="margin-top: 60px;"/>
        </div>
        <?= $content ?>
    </div>
    
    <?php
    //Menu principal
    //echo $this->render('_footer');
    ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
