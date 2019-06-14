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
</div>