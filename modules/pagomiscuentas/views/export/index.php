<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use app\components\companies\CompanySelector;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('pagomiscuentas', 'Exports Pagomiscuentas');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partner-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>
        <p>
            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.
                Yii::t('pagomiscuentas', 'Create Pagomiscuentas Export'),
                ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    </div>


    <div class="container-fluid">

        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
        ]); ?>

        <div class="row hidden-print">
            <div class="col-sm-4">
                <?= CompanySelector::widget(['model' => $searchModel]); ?>
            </div>

            <div class="col-sm-4">
                <?php
                echo $form->field($searchModel, 'from')->widget(DatePicker::class, [
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
            <div class="col-sm-4">
                <?php
                echo $form->field($searchModel, 'to')->widget(DatePicker::class, [
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
                <?= Html::submitButton('<span class="glyphicon glyphicon-search"></span> ' .Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
            </div>
            <div class="col-sm-6 text-right">
                <?= Html::a('<span class="glyphicon glyphicon-remove"></span> ' .Yii::t('app', 'Clear'), $form->action, ['class' => 'btn btn-warning']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => Yii::t('app', 'Company'),
                'attribute' => 'company.name',
            ],
            [
                'label' => Yii::t('pagomiscuentas', 'Bills From Date'),
                'attribute' => 'from_date',
                'format' => 'raw'
            ],
            [
                'label' => Yii::t('pagomiscuentas', 'Bills To Date'),
                'attribute' => 'date',
            ],
            [
                'label' => Yii::t('app', 'Status'),
                'value' => function($model) {
                    return Yii::t('pagomiscuentas', $model->status);
                }
            ],
            [
                'label' => Yii::t('app', 'Total'),
                'value' => function ($model) {
                    return $model->total ? $model->total : 0;
                },
                'format' => 'currency'
            ],
            [
                'class' => 'app\components\grid\ActionColumn',
                'template'=>' {view} {delete} {export}',
                'buttons'=> [
                    'export' => function ($url, $model, $key) {
                        return $model->status === 'closed' ? Html::a('<span class="glyphicon glyphicon-download" data-toggle="tooltip" title="'.Yii::t('pagomiscuentas', 'Download File').'"></span>', $url, ['target'=>'_blank', 'class' => 'btn btn-print']) : '';
                    },
                ]
            ]
        ],
    ]); ?>
</div>