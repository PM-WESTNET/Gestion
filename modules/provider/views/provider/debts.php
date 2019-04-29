<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use kartik\export\ExportMenu;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\modules\sale\models\search\CustomerSearch $searchModel
 */

$this->title = Yii::t('app', 'Provider Debts');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provider-index">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>        
    </div>


    <div class="debtors-search">

        <?php $form = ActiveForm::begin([
            'action' => ['debts'],
            'method' => 'get',
        ]); ?>

        <div class="row hidden-print">
            <div class="col-sm-12">
                <?php
                echo $this->render('@app/modules/provider/views/provider/_find-with-autocomplete', ['form'=> $form, 'model' => $searchModel, 'attribute' => 'provider_id', 'label'=>Yii::t('app', 'Provider')]);
                ?>
            </div>
        </div>
        <div class="row hidden-print">
            <div class="col-sm-12">
                <div class="form-group">
                    <?= $form->field($searchModel, 'toDate')->widget(yii\jui\DatePicker::className(),[
                        'language' => Yii::$app->language,
                        'dateFormat' => 'dd-MM-yyyy',
                        'options'=>[
                            'class'=>'form-control filter dates',
                            'placeholder'=>Yii::t('app','Date')
                        ]
                    ])->label(Yii::t('app', 'Debt To'));
                    ?>
                    <div class="help-block"></div>
                </div>
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
                'attribute'=>'debt',
                'format'=>'currency',
                'label'=>Yii::t('app', 'Amount due'),
                'contentOptions'=>['class'=>'text-right'],
            ],
            [
                'attribute'=>'payment',
                'format'=>'currency',
                'label'=>Yii::t('app', 'Amount payed'),
                'contentOptions'=>['class'=>'text-right'],
            ],
            [
                'attribute'=>'balance',
                'format'=>'currency',
                'label'=>Yii::t('app', 'Balance'),
                'contentOptions'=>['class'=>'text-right'],
            ],
        ],
        'showConfirmAlert'=>false
    ]);
    ?>


    <?php
    $columns = [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => Yii::t('app', 'Provider'),
            'attribute'=>'name',
        ],
        [
            'attribute'=>'debt',
            'format'=>'currency',
            'label'=>Yii::t('app', 'Amount due'),
            'contentOptions'=>['class'=>'text-right'],
        ],
        [
            'attribute'=>'payment',
            'format'=>'currency',
            'label'=>Yii::t('app', 'Amount payed'),
            'contentOptions'=>['class'=>'text-right'],
        ],
        [
            'attribute'=>'balance',
            'format'=>'currency',
            'label'=>Yii::t('app', 'Balance'),
            'contentOptions'=>['class'=>'text-right'],
        ],
        [
            'class' => '\yii\grid\DataColumn',
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
</div>
