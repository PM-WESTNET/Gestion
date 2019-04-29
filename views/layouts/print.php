<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
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
    <style type="text/css">       
        #print-preview{
            margin-top: 60px;
            margin-bottom: 60px;
        }
        .page{
            border: 2px dotted #ecc;
            padding: 2mm;
            width: <?= Yii::$app->params['print_params']['paper_size']['width'] ?>;
            height: <?= Yii::$app->params['print_params']['paper_size']['height'] ?>;
        }
        img{
            max-width: 100%;
        }
        .row{
            margin-bottom: 0;
        }
        .page .row:last-child{
            margin-bottom: 0;
        }
        @media print {
            nav{
                display: none;
            }
            .page, #print-preview, .container{
                margin: 0;
                border: 0;
                padding: 0;
            }
            .page{
                margin: 3mm 5mm 0mm 5mm;
            }
            body{
                margin: 0;
                padding: 0;
            }
            #yii-debug-toolbar,#yii-debug-toolbar,.yii-debug-toolbar-bottom{
                display: none;
            }
            a:link:after, a:visited:after {    
                content: " ("attr(href) ") ";    
                font-size: 90%;   
            }
        }
        .barcode{
            width: 25% !important;
            height: 34mm !important;
            float: left;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        .barcode img{
            width: 85% !important;
            height: 26mm !important;
            margin: 2mm 0 0 0;
            padding: 0;
        }
        /* Ocultamos los datos impresion del navegador (funciona en chrome) */
        @page 
        {
            size: auto;   /* auto is the initial value */
            margin: 0mm;  /* this affects the margin in the printer settings */
            padding: 10mm;
        }
        body 
        {
            background-color:#FFFFFF; 
            margin: 0px;  /* this affects the margin on the content before sending to printer */
        }
        /* /Ocultamos los datos impresion del navegador (funciona en chrome) */
    </style>
</head>
<body>

<?php $this->beginBody() ?>
    <?php
        NavBar::begin([
            'brandLabel' => Yii::t('app','Print template'),
            'options' => [
                'class' => 'navbar-inverse navbar-fixed-top',
            ],
        ]);
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => [
                ['label' => Yii::t('app','Back'), 'linkOptions'=>['onclick'=>'window.history.back();'], 'url' => ''],
                ['label' => Yii::t('app','Print'), 'options'=>['onclick'=>'window.print();'], 'url' => '#'],
            ],
        ]);
        NavBar::end();
    ?>
    <div id="print-preview">
        <div class="page container">
            <?= $content ?>
        </div>
    </div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
