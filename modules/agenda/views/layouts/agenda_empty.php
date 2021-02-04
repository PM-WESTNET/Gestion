<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\components\widgets\agenda\notification\Notification;
use app\components\widgets\agenda\task\Task;
use app\assets\AppAsset;
use app\components\widgets\agenda\AgendaBundle;

AgendaBundle::register($this);

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

            <div class="row" style="display: none;">
                <div class="col-xs-12">
                        <?=
                        \yii\bootstrap\Alert::widget([
                            'options' => [
                                'class' => 'alert-'
                            ],
                            'body' => ''
                        ]);
                        ?>
                </div>
            </div>

            <?= $content ?>

        <?php $this->endBody() ?>

    </body>
</html>
<?php $this->endPage() ?>
