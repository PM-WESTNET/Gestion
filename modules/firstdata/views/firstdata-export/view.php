<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\grid\SerialColumn;
use yii\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $model app\modules\firstdata\models\FirstdataExport */

$this->title = Yii::t('app', 'Firstdata Export') . ': ' . $model->firstdataConfig->company->name . ' - ' .
     Yii::$app->formatter->asDate($model->created_at, 'dd-MM-yyyy');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Firstdata Exports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="firstdata-export-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (!$model->getBillHasFirstdataExports()->exists()):?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->firstdata_export_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        <?php endif;?>
        <?php if ($model->status === 'draft'):?>
            <?= Html::a('<span class="glyphicon glyphicon-export"></span> '. Yii::t('app', 'Generate File'),
             ['create-file', 'id' => $model->firstdata_export_id], ['class' => 'btn btn-success'])?>
        <?php endif;?>

        <?php if ($model->status === 'exported'):?>
            <?= Html::a('<span class="glyphicon glyphicon-download"></span> '. Yii::t('app', 'Download File'),
             ['download', 'id' => $model->firstdata_export_id], ['class' => 'btn btn-warning'])?>
        <?php endif;?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'firstdata_export_id',
            [
                'attribute' => 'firstdata_config_id',
                'value' => function($model) {
                    return $model->firstdataConfig->company->name;
                }
            ],
            'created_at:datetime',
            'file_url',
            'from_date',
            'to_date'
        ],
    ]) ?>

    <h3><?= Yii::t('app', 'Bills')?></h3>

    <hr>

    <?=
    
        GridView::widget([
            'dataProvider' => new ActiveDataProvider(['query' => $model->getBills()]),
            'columns' => [
                ['class' => SerialColumn::class],

                [
                    'label' => Yii::t('app', 'Customer'),
                    'value' => function($model) {
                        return $model->customer->fullName . ' (' . $model->customer->code . ')';
                    }
                ],
                [
                    'label' => Yii::t('app', 'Number'),
                    'value' => function($model) {
                        return $model->number;
                    }
                ],

                [
                    'class' => ActionColumn::class,
                    'buttons' => [
                        'view' => function($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 
                            ['/sale/bill/view', 'id' => $model->bill_id], 
                            ['class' => 'btn btn-default', 'target' => '_blank']);
                        }
                    ],
                    'template' => '{view}'
                ]
            ]
        ])
    
    ?>

</div>
