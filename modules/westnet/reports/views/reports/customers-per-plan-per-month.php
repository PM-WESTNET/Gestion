<?php

use yii\helpers\Html;
use kartik\date\DatePicker;
use kartik\daterange\DateRangePicker;
$monthOfAnalisis = (isset($monthOfAnalisis))?$monthOfAnalisis:'n/a';
$download = (isset($download))?$download:'n/a';
$this->title = 'Clientes Plan: '.$download.' kbps ('.($download/1024).' mbps) del Mes: '.$monthOfAnalisis;

?>

<div class="customer-registrations">

    <h1><?php echo $this->title ?></h1>
    <form action="customer-registrations" method="GET">
        <?php echo \yii\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $reportSearch,
            'columns' => [
                [
                    'attribute' => 'fullName',
                    'label' => 'Cliente',
                    'value' => function ($model) {
                        return Html::a(
                            $model['fullName'],
                            yii\helpers\Url::toRoute([
                                '/sale/customer/view', 
                                'id' => $model['customer_id'], 
                            ])
                        );
                        
                    },
                    'format' => 'html'
                ],
                [
                    'attribute' => 'contract_id',
                    'label' => 'Contrato',
                    'value' => function ($model) {
                        return Html::a(
                            $model['contract_id'],
                            yii\helpers\Url::toRoute([
                                '/sale/contract/contract/view', 
                                'id' => $model['contract_id'], 
                            ])
                        );
                        
                    },
                    'format' => 'html'
                ],
                [
                    'attribute' => 'pName',
                    'format' => 'text',
                    'label' => 'Nombre del plan completo',
                    // 'filter' => $plansArray, // mapped from ReportsController
                ],
                [
                    'attribute' => 'detailDate',
                    'label' => 'Detalle de contrato fecha',
                ],
                // 'detailStatus',
                [
                    'attribute' => 'contractStatus',
                    'label' => 'Estado de contrato actual',
                ],


            ]
        ]) ?>
    </form>


</div>