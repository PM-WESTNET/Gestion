<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\automaticdebit\models\Bank */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Banks for Automatic Debit'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .title{
        margin-bottom: 10px;
    }
</style>
<div class="bank-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->bank_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->bank_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('<span class="glyphicon glyphicon-import"></span> '.Yii::t('app', 'Imports'), ['imports', 'bank_id' => $model->bank_id], ['class' => 'btn btn-warning']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-export"></span> '.Yii::t('app', 'Exports'), ['exports', 'bank_id' => $model->bank_id], ['class' => 'btn btn-info']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'bank_id',
            'name',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->getStatusLabel();
                },

            ],
            'class',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

    <div class="title">

    <h3><?php echo Yii::t('app','Companies')?></h3>
        <p>

            <?php echo Html::a(
                    '<span class="glyphicon glyphicon-plus"></span> '. Yii::t('app','Add Company'),
                    ['/automaticdebit/bank-company-config/create', 'bank_id' => $model->bank_id],
                    [
                        'class' => 'btn btn-success'
                    ]
            )?>
        </p>
    </div>

    <?php echo \yii\grid\GridView::widget([
        'dataProvider' => $configs,
        'columns' => [
            [
               'attribute' => 'company_id',
               'value' => function ($model) {
                    return $model->company->name;
               }
            ],
            'account_number',

            [
                'class' => 'app\components\grid\ActionColumn',
                'buttons' => [
                    'view' => function($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                            ['/automaticdebit/bank-company-config/view', 'id' => $model->bank_company_config_id],
                            [
                                'class' => 'btn btn-view'
                            ]);
                    },
                    'update' => function($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                            ['/automaticdebit/bank-company-config/update', 'id' => $model->bank_company_config_id],
                            [
                                'class' => 'btn btn-primary'
                            ]);
                    },
                    'delete' => function($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                            ['/automaticdebit/bank-company-config/delete', 'id' => $model->bank_company_config_id],
                            [
                                'class' => 'btn btn-danger'
                            ]);
                    }
                ]
            ]
        ]
    ])?>


</div>
