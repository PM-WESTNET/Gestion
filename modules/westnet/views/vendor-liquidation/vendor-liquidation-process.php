<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use kartik\widgets\Select2;
use app\components\helpers\UserA;
use yii\helpers\Url;

$this->title = Yii::t('westnet', 'Proceso de Liquidación de Vendedores');
$this->params['breadcrumbs'][] = ['label' => Yii::t('westnet', 'Vendor Liquidations Process'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vendor-liquidation-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="pull-right">
        <?= UserA::a(
            "<span class='glyphicon glyphicon-plus'></span> " . 'Nuevo Proceso (Calculo de Comisiones)',
            ['batch-vendor-liquidation-process'],
            ['class' => 'btn btn-success']
        ); ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => false,
        'columns' => [
            'vendor_liquidation_process_id',
            'status',
            'period',
            /* 'timestamp', */
            'date',
            'start_time',
            'finish_time',
            'time_spent',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{change-status} {remove-process} {view}',
                'buttons' => [
                    'change-status' => function ($url, $model) {
                        if ($model->status == 'draft')
                            return Html::a(
                                '<i class="glyphicon glyphicon-ok" style="color:green;"></i>',
                                [
                                    'vendor-liquidation/change-status-liquidation',
                                    'id' => $model->vendor_liquidation_process_id
                                ],
                                ['data-pjax' => '0']
                            );
                    },
                    'remove-process' => function ($url, $model) {
                        if ($model->status == 'draft')
                            return Html::a(
                                '<i class="glyphicon glyphicon-remove" style="color:red;"></i>',
                                [
                                    'vendor-liquidation/remove-vendor-liquidation-process',
                                    'id' => $model->vendor_liquidation_process_id
                                ],
                                ['data-pjax' => '0']
                            );
                    },
                    'view' => function ($url, $model) {
                        if ($model->status == 'pending')
                            return Html::a(
                                '<i class="glyphicon glyphicon-eye-open" ></i>',
                                [
                                    'vendor-liquidation/view-vendor-liquidation-process',
                                    'id' => $model->vendor_liquidation_process_id
                                ],
                                ['data-pjax' => '0']
                            );
                    }
                ]
            ]
        ]
    ]); ?>

</div>