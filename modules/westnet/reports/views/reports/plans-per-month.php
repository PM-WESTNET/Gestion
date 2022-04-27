<?php
// $data = yii\helpers\Json::htmlEncode($dataProvider->getModels());
// var_dump($data);
// die();

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\date\DatePicker;
use kartik\daterange\DateRangePicker;
use yii\web\View;
use yii\widgets\Pjax;
use \yii\grid\GridView;

$this->title = 'Altas de Planes por Mes';

?>

<div class="customer-registrations">

    <h1><?php echo $this->title ?></h1>
    <form action="customer-registrations" method="GET">
        <?= Html::a(
                'exportar a excel',
                [
                    '/reports/reports/plans-per-month','excel-export'=>true
                ],
                [
                    'class' => 'btn btn-info',
                    'target' => '_blank',
                    // 'style' => 'display: none;' // todo: display button when the controller to export is working correctly
                ]
            )
        ?>
        <?php 
            Pjax::begin(); 
        ?>
        <?php echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $reportSearch,
            'options' => [ 'style' => 'table-layout:fixed;' ],
            'columns' => [
                [
                    'label' => 'bajada',
                    'value' => function($model){
                        if(!isset($model['download'])) return 'n/a';
                        $mb = ($model['download']/1024);
                        return $mb." mbps";
                    }
                ],                
                [
                    'label' => 'subida',
                    'value' => function($model){
                        if(!isset($model['upload'])) return 'n/a';
                        $mb = ($model['upload']/1024);
                        return $mb." mbps";
                    }
                ],
                [
                    'label' => 'tecnologia',
                    'attribute' => 'technology',
                ],
                [
                    'attribute' => 'cantAltasPorMes',
                    'format' => 'html',
                    'label' => 'Altas por Mes',
                    'value' => function ($model) {
                        return Html::a(
                            $model['cantAltasPorMes'],
                            yii\helpers\Url::toRoute([
                                '/reports/reports/customers-per-plan-per-month', 
                                'download' => $model['download'], 
                                'upload' => $model['upload'], 
                                'technology' => $model['technology'], 
                                'year_month' => $model['groupDate']
                            ])
                        );
                        
                    },
                ],
                [
                    'attribute' => 'groupDate',
                    'contentOptions' => [ 'style' => 'width: 25%;' ],
                    // 'headerOptions' => ['style' => 'width:20%'],
                    'label' => 'Año y Mes (Agrupado x plan)',
                    'filter'=>DateRangePicker::widget([
                        'model' => $reportSearch,
                        'attribute' => 'groupDate',
                        'options' => ['placeholder' => 'Select range...'],
                        'presetDropdown' => true,
                        // 'includeDaysFilter' => false,
                        // 'includeMonthsFilter' => false,
                    ]),
                ],
            ]
        ]) ?>
        <?php 
            Pjax::end(); 
        ?>
    </form>


</div>

