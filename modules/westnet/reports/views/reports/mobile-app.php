<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\export\ExportMenu;
use yii\bootstrap\Collapse;
use app\modules\westnet\reports\ReportsModule;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\provider\models\search\ProviderBillSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = ReportsModule::t('app', 'Mobile app report')  ;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provider-bill-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?= Collapse::widget([
            'items' => [
                [
                    'label' => '<span class="glyphicon glyphicon-chevron-down"></span> '.Yii::t('app','Filters'),
                    'content' => $this->render('_mobile-app-filters', ['model' => $searchModel]),
                    'encode' => false,
                ],
            ],
            'options' => [
                'class' => 'hidden-print'
            ]
        ]);
    ?>


    <div class="col-sm-12 list-group">
        <div class="col-sm-4 list-group-item text-center">
            <h5><?= ReportsModule::t('app', 'Active customers')?></h5> <br>
            <?= $statistics[0]['customer_qty']?>
        </div>
        <div class="col-sm-4 list-group-item text-center">
            <h5><?= ReportsModule::t('app', 'Customers percentage with the app installed')?></h5> <br>
            <?= round($statistics[0]['installed_qty'] / $statistics[0]['customer_qty'] * 100, 2) .'% ('.$statistics[0]['installed_qty'].' '.Yii::t('app', 'Customers').')'?> <br>
        </div>
        <div class="col-sm-4 list-group-item text-center">
            <h5><?= ReportsModule::t('app', 'Customers percentage using the app')?></h5> <br>
            <?= round($statistics[0]['used_qty'] / $statistics[0]['customer_qty'] * 100, 2) .'% ('.$statistics[0]['used_qty'].' '.Yii::t('app', 'Customers').')'?> <br>
        </div>
    </div>

    <hr>

    <div class="col-sm-12 list-group">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Yii::t('app', 'Notify payments qty per payment method') ?></h3>
            </div>
            <div class="panel-body">
                <ul class="list-group">
                    <?php
                    $last_period = '';
                    foreach ($paymentsStatistics as $paymentStatistic) {

                        $date = DateTime::createFromFormat('Ymd', $paymentStatistic['period'].'01');
                        if($last_period != $paymentStatistic['period']) {
                            echo "<li class='list-group-item disabled'>". $date->format('Y-m')."</li>";
                            $last_period = $paymentStatistic['period'];
                        }
                        echo "<li class='list-group-item' style='padding-left:5em'>". $paymentStatistic['payment_method_name']." <span class='badge'>".$paymentStatistic['qty']."</span></li>";
                    }?>
                </ul>
            </div>
        </div>
    </div>

    <hr>

    <div class="col-sm-12 list-group">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Yii::t('app', 'Payment extension qty per period') ?></h3>
            </div>
            <div class="panel-body">
                <ul class="list-group">
                    <?php foreach ($paymentExtensionStatistics as $paymentExtensionStatistic) {
                        $date = DateTime::createFromFormat('Ymd', $paymentExtensionStatistic['period'].'01');
                        $qty = ($paymentExtensionStatistic['payment_extension_qty'] - $paymentExtensionStatistic['notify_payment_qty']) <= 0 ? 0 : ($paymentExtensionStatistic['payment_extension_qty'] - $paymentExtensionStatistic['notify_payment_qty']);
                        echo "<li class='list-group-item'>". $date->format('Y-m')." <span class='badge badge-lg'>".$qty."</span></li>";
                    }?>
                </ul>
            </div>
        </div>
    </div>
</div>