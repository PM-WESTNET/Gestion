<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\models\Plan */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Plans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="plan-view">
    

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->product_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->product_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>

    <?php
    //Para mostrar IVA
    $taxRates = '';
    foreach($model->taxRates as $rate){
        $taxRates .= $rate->tax->name.': '.$rate->name .'<br>';
    }
    ?>
    
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'product_id',
            [
                'attribute' => 'company.name',
                'label'     => Yii::t('app', 'Company'),
                'value'     => ($model->company_id ? $model->company->name : Yii::t('app', 'All') )
            ],

            'name',
            'system',
            [
                'attribute' => 'show_in_ads',
                'label'     => Yii::t('app', 'Show In Ads'),
                'value'     => ($model->show_in_ads ? Yii::t('app', 'Yes') : Yii::t('app', 'No') )
            ],
            [
                'attribute' => 'ads_name',
                'label'     => Yii::t('app', 'Ads Name'),
                'value'     => ($model->show_in_ads ? $model->ads_name : Yii::t('app', 'No apply') )
            ],
            'netPrice:currency',
            [
                'attribute' => 'taxRates',
                'label' => Yii::t('app', 'Taxes'),
                'value' => $taxRates,
                'format' => 'html'
            ],
            'finalPrice:currency',
            'futureFinalPrice:currency',
            'code',
            'description:ntext',
            [
                'attribute'=>'status',
                'value'=>Yii::t('app',  ucfirst($model->status))
            ],
            'create_timestamp:date',
            'update_timestamp:date',
            [
                'attribute'=>'unit_id',
                'value'=>$model->unit->name
            ],
            'quota'
        ],
    ]) ?>



<table class="table">
    <thead><th>Característica</th><th>Valor</th></thead>
    <?php
        echo ListView::widget([
            'dataProvider' => \app\modules\sale\modules\contract\models\search\PlanSearch::getdataProvider($model),
            'itemView' => '_feature_plan',
        ]);
    ?>
</table>
</div>
