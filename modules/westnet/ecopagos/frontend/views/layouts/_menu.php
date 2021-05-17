<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use \webvimark\modules\UserManagement\components\GhostNav;
use app\modules\westnet\ecopagos\EcopagosModule;

/*
  NavBar::begin([
  'brandLabel' => 'Arya',
  'brandUrl' => Yii::$app->homeUrl,
  'options' => [
  'class' => 'navbar-inverse navbar-fixed-top',
  ],
  'innerContainerOptions' => [
  'class' => 'container-fluid'
  ]
  ]);
 */

$items = [];
$alwaysVisibleItems = [];

//Home
$items[] = ['label' => '<span class="glyphicon glyphicon-home"></span> ' . EcopagosModule::t('app', 'Home'), 'url' => ['site/index']];
$items[] = ['label' => '<span class="glyphicon glyphicon-usd"></span> ' . EcopagosModule::t('app', 'Payouts'),
    'items' => [
        ['label' => EcopagosModule::t('app', 'Create Payout'), 'url' => ['payout/create']],
        '<li class="divider"></li>',
        ['label' => EcopagosModule::t('app', 'View created payouts'), 'url' => ['payout/index']],
    ]
];
$items[] = ['label' => '<span class="glyphicon glyphicon-check"></span> ' . EcopagosModule::t('app', 'Daily closures'),
    'items' => [
        ['label' => EcopagosModule::t('app', 'Execute daily closure'), 'url' => ['daily-closure/preview'], 'visible' => \app\modules\westnet\ecopagos\frontend\helpers\UserHelper::hasOpenCashRegister()],
        '<li class="divider"></li>',
        ['label' => EcopagosModule::t('app', 'View daily closures'), 'url' => ['daily-closure/index']],
    ]
];
$items[] = ['label' => '<span class="glyphicon glyphicon-briefcase"></span> ' . EcopagosModule::t('app', 'Batch closures'),
    'items' => [
        ['label' => EcopagosModule::t('app', 'Execute batch closure'), 'url' => ['batch-closure/create']],
        '<li class="divider"></li>',
        ['label' => EcopagosModule::t('app', 'View all batch closures'), 'url' => ['batch-closure/index']],
    ]
];
$items[] = ['label' => '<span class="glyphicon glyphicon-tag"></span> ' . EcopagosModule::t('app', 'Credential reprint'),
    'items' => [
        ['label' => '<span class="glyphicon glyphicon-print"></span> ' . EcopagosModule::t('app', 'Credentials'), 'url' => ['credential/reprint-ask']],
        '<li class="divider"></li>',
        ['label' => EcopagosModule::t('app', 'View all credential asks'), 'url' => ['credential/index']],
    ]
];

$items[] = ['label' => '<span class="glyphicon glyphicon-user"></span> (' . Yii::$app->user->identity->username . ')',
    'items' => [
        ['label' => '<span class="glyphicon glyphicon-lock"></span> ' . EcopagosModule::t('app', 'Change password'), 'url' => ['cashier/change-password']],
        '<li class="divider"></li>',
        ['label' => '<span class="glyphicon glyphicon-log-out"></span> ' . EcopagosModule::t('app', 'Exit'), 'url' => ['/user-management/auth/logout']],
    ]
];
?>
<nav class="navbar navbar-default <?= YII_ENV == 'test' ? '' : 'navbar-fixed-top' ?> z-depth-0">

    <div class="container-fluid">

        <div class="navbar-header">

            <?php
            echo GhostNav::widget([
                'options' => ['class' => 'navbar-nav navbar-right pull-right collapsed no-margin height-50 display-navbar-breakpoint'],
                'items' => $alwaysVisibleItems,
                'encodeLabels' => false,
                'activateParents' => true
            ]);
            ?>

            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <a class="navbar-brand" href="<?= \yii\helpers\Url::to(['site/index']); ?>">Ecopagos</a>

        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

            <?php
            echo GhostNav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $items,
                'encodeLabels' => false,
                'activateParents' => true
            ]);
            ?>

        </div>

    </div>

</nav>
