<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\components\widgets\agenda\notification\Notification;
use app\components\widgets\agenda\task\Task;
use app\assets\AppAsset;
use app\components\helpers\UserA;

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
    <link rel="icon" type="image/x-icon" href= <?= isset(Yii::$app->params['favicon-filename'])? Yii::$app->params['favicon-filename'] : 'favicon.ico' ?> />
    <?php $this->head() ?>
    <?= Html::csrfMetaTags() ?>
</head>
<body id=<?= UserA::getBodyId() ?>>

<?php $this->beginBody() ?>
    <div class="wrap">
        <?php
        //Menu principal
        echo $this->render('_menu');
        ?>
        <br><br><br>
        <div class="row">            
            <div class="col-lg-10 col-sm-9 col-xs-12">
                <?= Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
            </div>
            <div class="col-lg-2 col-sm-3 hidden-xs">
                <div class="btn btn-default pull-right" onclick="window.print();">
                    <span class="glyphicon glyphicon-print"></span>
                    <?= Yii::t('app', 'Print'); ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <?php 
                $flashes = Yii::$app->getSession()->getAllFlashes();
                foreach($flashes as $class=>$flash):
                ?>
                    <?= 
                        \yii\bootstrap\Alert::widget([
                            'options'=>[
                                'class'=>'alert-'.$class
                            ],
                            'body'=>$flash
                        ]);
                    ?>
                <?php
                endforeach;
                ?>
            </div>
        </div>
        <?= $content ?>
    
    </div>
    
    <?php
    //Menu principal
    //echo $this->render('_footer');
    ?>
    
    
    <?php
        echo Notification::widget();
    ?>
    <?php
        echo Task::widget();
    ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
