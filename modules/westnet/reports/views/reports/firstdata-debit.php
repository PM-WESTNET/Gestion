<?php

use dosamigos\chartjs\ChartJs;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\widgets\ActiveField;
use yii\helpers\Html;
use kartik\grid\GridView;

$this->title = Yii::t('app', 'Firstdata Automatic Debit Report');
?>


<div class="firstdata-report">
    <h1 class="title">
        <?= $this->title ?>
    </h1>

    <?= ChartJs::widget([
            'type' => 'line',
            'options' => [
                'height' => 50,
                'width' => 100
            ],
            'data' => $data,
            'clientOptions' => [
                'scales' => [
                    'yAxes' => [
                        'ticks' => [
                            'max' => $max + 10,
                            'beginAtZero' => true
                        ]
                    ],
                    
                ]
            ]
        ]);
    ?>

    <br>
    <?php $form = ActiveForm::begin(['method' => 'GET'])?>
    <div class="row">
        <div class="col-xs-5">
            <?= $form->field($search, 'date_from')->widget(DatePicker::class, [
                'dateFormat' => 'dd-MM-yyyy',
                'options' => [
                    'class' => 'form-control'
                ]
            ])?>
        </div>
        <div class="col-xs-5">
            <?= $form->field($search, 'date_to')->widget(DatePicker::class, [
                'dateFormat' => 'dd-MM-yyyy',
                'options' => [
                    'class' => 'form-control'
                ]
            ])?>
        </div>
        <div class="col-xs-2">
            <br>
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-success'])?>
        </div>
    </div>

    <?php ActiveForm::end()?>

    <?= GridView::widget([
        'dataProvider' => $debits,
        'columns' => [
            [
                'label' => 'Nombre Completo',
                'value' => 'customer.fullName'
            ],
            'status',
            [
                'label' => 'Fecha de Creación',
                'value' => 'created_at',
                'format' => 'date'
            ],
        ]
    ])?>

</div>