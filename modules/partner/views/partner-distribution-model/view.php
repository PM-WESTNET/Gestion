<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\partner\models\PartnerDistributionModel */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('partner', 'Partner Distribution Models'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partner-distribution-model-view">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->partner_distribution_model_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->partner_distribution_model_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            [
                'label' => Yii::t('app', 'Company'),
                'value' => $model->company->name
            ]
        ],
    ]) ?>

</div>
<div class="panel panel-default">
    <div class="panel-heading" data-toggle="collapse" data-target="#panel-body-items" aria-expanded="true" aria-controls="panel-body-items">
        <h3 class="panel-title"><?= Yii::t('partner', 'Partners') ?></h3>
    </div>
    <div class="panel-body collapse in" id="panel-body-items" aria-expanded="true">
        <div class="row" id="form-partner-items">
            <?php
            echo GridView::widget([
                'id'=>'items',
                'dataProvider' => new \yii\data\ActiveDataProvider(['query'=>$model->getPartnerDistributionModelHasPartner()]),
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'label' => Yii::t("partner", "Partner"),
                        'attribute' => 'partner.name'
                    ],
                    [
                        'label' => Yii::t("app", "Percentage"),
                        'attribute' => 'percentage'
                    ],

                ],
                'options'=>[
                    'style'=>'margin-top:10px;'
                ]
            ]);

            ?>

        </div>
    </div>
</div>