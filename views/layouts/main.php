<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\components\widgets\agenda\notification\Notification;
use app\components\widgets\agenda\task\Task;
use app\assets\AppAsset;
use app\components\widgets\agenda\ticket\TicketBundle;

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
    
    <?php TicketBundle::register($this); ?>
</head>
<body>

<?php $this->beginBody() ?>
    <div class="wrap">
        <?php
        //Menu principal
        echo $this->render('_menu');
        ?>

        <div class="container">
            <br><br><br>
            <div class="row hidden-print">
                <div class="col-lg-10 col-sm-9 col-xs-12 no-padding">
                    <?= Breadcrumbs::widget([
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]) ?>
                </div>
                <div class="col-lg-2 col-sm-3 hidden-xs no-padding">
                    <div class="btn btn-default pull-right" onclick="window.print();">
                        <?= Yii::t('app', 'Print Screen'); ?>
                    </div>
                </div>
            </div>
            <div class="row hidden-print">
                <div class="col-xs-12">
                    <?php
                    $flashes = Yii::$app->getSession()->getAllFlashes();
                    foreach($flashes as $class=>$flash):
                        if($class == 'error') {
                            $class = 'danger';
                        }
                        if(is_array($flash)){
                            foreach($flash as $flashito) {
                                echo \yii\bootstrap\Alert::widget([
                                    'options' => [
                                        'class' => 'alert-' . $class
                                    ],
                                    'body' => $flashito
                                ]);
                            }
                        } else {
                            echo \yii\bootstrap\Alert::widget([
                                'options' => [
                                    'class' => 'alert-' . $class
                                ],
                                'body' => $flash
                            ]);
                        }
                        ?>
                    <?php
                    endforeach;
                    ?>
                </div>
            </div>
            <?= $content ?>
        </div>
    </div>
    
    <?php
    //Menu principal
    //echo $this->render('_footer');
    ?>
    
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
