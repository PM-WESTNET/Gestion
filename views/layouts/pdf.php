<?php
use app\assets\InvoiceAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;

/**
 * @var \yii\web\View $this
 * @var string $content
 */
InvoiceAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html >
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>

        <?= $content ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>