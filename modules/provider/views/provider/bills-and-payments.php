<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\ActiveForm;
use kartik\export\ExportMenu;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\sale\models\search\CustomerSearch $searchModel
 */

$this->title = Yii::t('app', 'Provider Bills And Payments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provider-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>


    <div class="debtors-search">

        <?php $form = ActiveForm::begin([
            'action' => ['bills-and-payments'],
            'method' => 'get',
        ]); ?>

        <div class="row hidden-print">
            <div class="col-sm-6">
                <?php
                echo $form->field($searchModel, 'fromDate')->widget(yii\jui\DatePicker::className(), [
                    'language' => Yii::$app->language,
                    'model' => $searchModel,
                    'attribute' => 'date',
                    'dateFormat' => 'dd-MM-yyyy',
                    'options'=>[
                        'class'=>'form-control dates',
                        'id' => 'from-date'
                    ]
                ]);
                ?>
            </div>
            <div class="col-sm-6">
                <?php
                echo $form->field($searchModel, 'toDate')->widget(yii\jui\DatePicker::className(), [
                    'language' => Yii::$app->language,
                    'model' => $searchModel,
                    'attribute' => 'date',
                    'dateFormat' => 'dd-MM-yyyy',
                    'options'=>[
                        'class'=>'form-control dates',
                        'id' => 'to-date'
                    ]
                ]);
                ?>
            </div>
        </div>
        <div class="row hidden-print">
            <div class="col-sm-6">

            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="control-label">&nbsp;</label>
                    <div class="pull-right">
                        <?= Html::submitButton('<span class="glyphicon glyphicon-search"></span> ' .Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('<span class="glyphicon glyphicon-remove"></span> ' .Yii::t('app', 'Clear'), $form->action, ['class' => 'btn btn-warning']) ?>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php ActiveForm::end(); ?>

    <?= ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => Yii::t('app', 'Provider'),
                'attribute'=>'name',
            ],
            [
                'format'=>'currency',
                'label'=>Yii::t('app', 'Amount billed'),
                'value' => function($model) {
                    return ($model['facturado'] ? $model['facturado'] : 0);
                },
                'contentOptions'=>['class'=>'text-right'],
            ],
            [
                'format'=>'currency',
                'label'=>Yii::t('app', 'Amount payed'),
                'value' => function($model) {
                    return ($model['pagos'] ? $model['pagos'] : 0);
                },
                'contentOptions'=>['class'=>'text-right'],
            ],
        ],
        'showConfirmAlert'=>false
    ]);
    ?>


    <?php
    $columns = [
        [
            'label' => Yii::t('app', 'Provider'),
            'attribute'=>'name',
        ],
        [
            'attribute'=>'facturado',
            'format'=>'currency',
            'label'=>Yii::t('app', 'Amount billed'),
            'value' => function($model) {
                return $model['facturado'] ? $model['facturado'] : 0;
            },
            'contentOptions'=>['class'=>'text-right'],
        ],
        [
            'attribute'=>'pagos',
            'format'=>'currency',
            'label'=>Yii::t('app', 'Amount payed'),
            'value' => function($model) {
                return $model['pagos'] ? $model['pagos'] : 0;
            },
            'contentOptions'=>['class'=>'text-right'],
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'content' => function($model, $key, $index, $column){
                return Html::a('<span class="glyphicon glyphicon-usd"></span> '.Yii::t('app','Account'), ['/provider/provider/current-account','id'=>$model['provider_id']], ['class'=>'btn btn-width btn-default']);
            },
            'format'=>'html',
        ]
    ];

    $grid = GridView::begin([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterPosition' => 'none',
        'id'=>'grid',
        'options' => ['class' => 'table-responsive'],
        'columns' => $columns,
    ]); ?>
    <?php $grid->end(); ?>

    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <?php echo Yii::t('app', 'Total Billed') ?>
            </div>
            <div class="col-md-3">
                <?php echo Yii::$app->formatter->asCurrency($totals['billed'] ? $totals['billed']: 0 ) ?>
            </div>
            <div class="col-md-3">
                <?php echo Yii::t('app', 'Total Payed') ?>
            </div>
            <div class="col-md-3">
                <?php echo Yii::$app->formatter->asCurrency($totals['payed'] ? $totals['payed'] : 0) ?>
            </div>
        </div>
    </div>
</div>
