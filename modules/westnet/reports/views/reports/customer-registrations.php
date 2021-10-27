<?php

use yii\helpers\Html;
use kartik\date\DatePicker;

$this->title = Yii::t('app', 'Customer Registrations');

$this->registerJs(
    '$("document").ready(function(){
            $("#number-clients").click(function(){
                let date = document.getElementById("reportsearch-date");
                let date2 = document.getElementById("reportsearch-date2");
                $.ajax({ url: `number-of-clients?date=${date.value}&date2=${date2.value}`,
                    type: "get",
                    success: function(data) {
                       alert(`El total de clientes instalados desde ${date.value} hasta ${date2.value} es de: ${data}`);
                    }
                });
            });
        });'
);
?>

<div class="customer-registrations">

    <h1><?php echo $this->title ?></h1>
    <form action="customer-registrations" method="GET">
        <?= Html::a(
            'exportar a excel',
            [
                '/reports/reports/customer-registrations-excel'
            ],
            ['class' => 'btn btn-info', 'target' => '_blank']
        )
        ?>
        <?php echo \yii\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $reportSearch,
            'columns' => [
                [
                    'attribute' => 'code',
                    'format' => 'text',
                    'label' => 'Código',
                ],
                [
                    'attribute' => 'fullname',
                    'format' => 'text',
                    'label' => 'Nombre',
                ],
                [
                    'attribute' => 'name_product',
                    'value' => function ($model) {
                        if (strpos(strtolower($model['name_product']), 'ftth')) {
                            return "FIBRA";
                        } else if (strpos(strtolower($model['name_product']), 'wifi')) {
                            return "WIRELESS";
                        } else {
                            return "Sin Identificar";
                        }
                    },
                    'format' => 'text',
                    'label' => 'Tecnología',
                ],
                [
                    'attribute' => 'speed',
                    'value' => function ($model) {
                        $speed = preg_match('/[0-9]/', $model['name_product'], $matches, PREG_OFFSET_CAPTURE);
                        $speed = substr($model['name_product'], $matches[0][1]);

                        return $speed;
                    },
                    'format' => 'text',
                    'label' => 'Velocidad',
                ],
                [
                    'attribute' => 'node',
                    'format' => 'text',
                    'label' => 'Nodo',
                ],
                [
                    'attribute'=>'date',
                    'format' => 'raw',
                    'header' => '<a class="prueba">Fecha <i class="glyphicon glyphicon-exclamation-sign" id="number-clients"></i></a>',
                    'value' =>'date',
                    'filter'=>DatePicker::widget([
                        'model' => $reportSearch,
                        'attribute' => 'date',
                        'value' => '2014-01-01',
                        'type' => DatePicker::TYPE_RANGE,
                        'attribute2' => 'date2',
                        'value2' => '2016-01-01',
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'yyyy-mm-dd'
                        ]
                    ]),  
                ],  
            ]
        ]) ?>
    </form>


</div>