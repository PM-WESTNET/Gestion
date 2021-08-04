<?php

use app\components\widgets\agenda\notification\Notification;
use app\components\widgets\agenda\task\Task;
use yii\helpers\Html;
use yii\bootstrap\NavBar;
use \webvimark\modules\UserManagement\components\GhostNav;
use app\modules\westnet\ecopagos\frontend\helpers\UserHelper;
use app\components\widgets\Nav;
use app\modules\sale\components\BillExpert;
use app\modules\westnet\reports\ReportsModule;
use webvimark\modules\UserManagement\models\User;
use app\modules\westnet\notifications\NotificationsModule;
use app\modules\westnet\models\Vendor;
use app\modules\sale\models\BillType;

//Fix ancho de submenu NavX > DropdownX
$this->registerCss('.dropdown-submenu .dropdown-menu { right: auto; }');


/* $item = ['label' => "y1", 'items' => [
    [
        'label' => "x1", 
        'url' => ['/route'],
        'visible' => true,
    ],
    [
        'label' => "x2", 
        'url' => ['/route'],
        'visible' => true,
    ],
    [
        'label' => "x3", 
        'items' => [
            [
                'label' => "w1", 
                'url' => ['/route'],
                'visible' => true,
            ],
            [
                'label' => "w2", 
                'url' => ['/route'],
                'visible' => true,
            ],
        ],
    ],
    [
        'label' => "x4", 
        'url' => ['/route'],
        'visible' => true,
    ],
]]; */
$mockItemsFromDB = array(
    array('Pagos', false, '/routePagos'),
    array('Ventas', true,
        array('Zona', false, '/routeVentas'),
        array('Mapa', false, '/routeVentas'),
        array('Planes', false, '/routeVentas'),
        array('Precios', false, '/routeVentas'),
    ),
    array('Analiticas', false, '/routeAnaliticas'),
    array('Reportes', false, '/routeReportes'),
    array('Usuarios', false, '/routeUsuarios'),
);
/* var_dump($mockItemsFromDB[1][2][2]);
die(); */
$menu = [];

//Prueba MENU RECURSIVO
function createMenu($arrLenght, $items, $mockItemsFromDB, $depthLvl)
{
    $currentItemName = $mockItemsFromDB[$arrLenght][0];
    $currentItemIsParent = $mockItemsFromDB[$arrLenght][1];
    var_dump("Item: $currentItemName Es padre? ");
    var_dump($currentItemIsParent);
    var_dump($arrLenght);
    
    if($currentItemIsParent){
        $parentItem = [
            'label' => "$currentItemName",
            'items' => createMenu(count($mockItemsFromDB)-1, $items, $mockItemsFromDB, $depthLvl+1),
            'visible' => true,
        ];
        array_push($items, $parentItem);
    }else{
        $childItem = [
            'label' => "y_$arrLenght", 
            'url' => ["/route_$arrLenght"], 
            'visible' => true,
        ];
        array_push($items, $childItem);
    }

    array_push($items, $parentItem);

    if ($arrLenght > 0) {
        return createMenu(($arrLenght - 1), $items, $mockItemsFromDB, $depthLvl);
    }
    var_dump($items);
    die();
    return $items;
}

/* var_dump(count($mockItemsFromDB));
die(); */

$menu[] = createMenu(count($mockItemsFromDB)-1, $menu, $mockItemsFromDB, 0);


?>
<nav id="main-menu" class="navbar navbar-inverse <?= YII_ENV == 'test' ? '' : 'navbar-fixed-top' ?>">

    <div class="container-fluid">

        <div class="navbar-header" id="narrow-navbar">
            <button type="button" class="navbar-toggle collapsed pull-left" data-toggle="collapse" data-target="#wide-navbar" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>


            <a class="navbar-brand" href="<?= Yii::$app->homeUrl; ?>"><?php echo Yii::$app->params['web_title'] ?></a>

            <?php
            /* echo Nav::widget([
                'options' => ['class' => ' navbar-nav navbar-right pull-right no-margin display-navbar-breakpoint navbar-links-responsive '],
                'items' => $alwaysVisibleItems,
                'encodeLabels' => false,
                'activateParents' => true
            ]); */
            ?>


        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="wide-navbar">

            <?php
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-left'], //WIP
                'items' => $menu,
                'encodeLabels' => false,
                'activateParents' => true
            ]);
            ?>
        </div>
    </div>
</nav>
