<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use app\modules\pagomiscuentas\models\PagomiscuentasFile;
use yii\jui\DatePicker;
use app\components\companies\CompanySelector;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('pagomiscuentas', 'Importation of Pagomiscuentas');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partner-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.
                Yii::t('pagomiscuentas', 'Create Pagomiscuentas Import'),
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
                <?= CompanySelector::widget(['model'=>$searchModel]); ?>
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
                'label' => Yii::t('app', 'Date'),
                'attribute' => 'date',
            ],
            [
                'label' => Yii::t('app', 'File'),
                'attribute' => 'file',
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
                'template' => '{view}{delete}',
                'buttons' => [
                    'delete' => function($url, $model, $key){
                        if($model->deletable){
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                'title' => Yii::t('yii', 'Delete'),
                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                'data-method' => 'post',
                                'data-pjax' => '0',
                                'class' => 'btn btn-danger',
                            ]);
                        }
                    },
                ],
            ],
        ],
    ]); ?>
</div>