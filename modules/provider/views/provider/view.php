<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\provider\models\Provider */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Providers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provider-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('<span class="glyphicon glyphicon-eye-open"></span> '.Yii::t('app','Account'),
                        ['provider/current-account','id'=>$model->provider_id], ['class'=>'btn btn-default']) ?>
            <?= Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->provider_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->provider_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>

    <?php
    $attributes = [
        'provider_id',
        'name',
        'business_name',
        [

            'label'=> Yii::t('app', 'Tax Condition'),
            'attribute' => 'taxCondition.name',
        ],
        'tax_identification',
        'address',
        'bill_type',
        'phone',
        'phone2',
        'description:ntext'
    ];

    if (Yii::$app->getModule('accounting') ) {
        $attributes[] = [
            'label' => Yii::t('accounting', 'Account'),
            'attribute'=>'account.name'
        ];
    }

    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes
    ]) ?>

</div>
