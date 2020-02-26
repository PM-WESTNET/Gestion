<?php

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\provider\models\ProviderBill;

/* @var $this yii\web\View */
/* @var $model app\modules\provider\models\ProviderBill */

$this->title = Yii::t('app','Bill') .' - '. $model->provider->name . " - " . $model->billType->name . " " . $model->number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Provider Bills'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provider-bill-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?php if ($model->getUpdatable()) {
                echo Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->provider_bill_id], ['class' => 'btn btn-primary']);
            } ?>
            <?php if ($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->provider_bill_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>

    <?php
    $attributes = [];
    if (Yii::$app->params['companies']['enabled']) {
        $attributes[] = [
            'label' => Yii::t('app', 'Company'),
            'value' => $model->company_id ? $model->company->name: ''
        ];
        $attributes[] = [
            'label' => Yii::t('partner', 'Partner Distribution Model'),
            'value' => $model->partnerDistributionModel ? $model->partnerDistributionModel->name: ''
        ];
    }

    echo DetailView::widget([
        'model' => $model,
        'attributes' => array_merge($attributes,[
            [
                'attribute' => 'billType.name',
                'label'     => Yii::t('app', 'Bill Type')
            ],
            'number',
            [
                'attribute'=>'date',
                'value'=>Yii::$app->formatter->asDate($model->date)
            ],
            [
                'attribute'=>'timestamp',
                'value'=> function($model) {
                    return (new \DateTime('now'))->setTimestamp($model->timestamp)->format('d-m-Y');
                }
            ],
            'net:currency',
            [
                'label'     => Yii::t('app', 'Taxes'),
                'value'     =>Yii::$app->formatter->asCurrency($model->taxes)
            ],
            'total:currency',
            'description',
            [
                'attribute' => 'status',
                'value' => function($model){
                    return Yii::t('app', $model->status);
                }

            ]
        ])
    ]) ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= Yii::t('app', 'Items') ?></h3>
        </div>
        <div class="panel-body">

            <?=GridView::widget([
                'id'=>'grid',
                'dataProvider' => new ActiveDataProvider([
                    'query' => $model->getProviderBillItems()
                ]),
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'description',
                    [
                        'label' => Yii::t("accounting", "Account"),
                        'value' => function($model){
                            return ($model->account ? $model->account->name : '' );
                        }
                    ],
                    'amount:currency'
                ],
                'options'=>[
                    'style'=>'margin-top:10px;'
                ]
            ]);
            ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= Yii::t('app', 'Taxes') ?></h3>
        </div>
        <div class="panel-body">

            <?=GridView::widget([
                'id'=>'grid',
                'dataProvider' => new ActiveDataProvider([
                    'query' => $model->getProviderBillHasTaxRates()
                ]),
                'options' => ['class' => 'table-responsive'],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'label' => Yii::t("app", "Tax"),
                        'value' => function($model){
                            return $model->taxRate->tax->name . " " . $model->taxRate->name;
                        }
                    ],
                    'net:currency',
                    'amount:currency'
                ],
                'options'=>[
                    'style'=>'margin-top:10px;'
                ]
            ]);
            ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= Yii::t('app', 'Payments') ?></h3>
        </div>
        <div class="panel-body">

            <?=GridView::widget([
                'id'=>'grid',
                'dataProvider' => new ActiveDataProvider([
                    'query' => $model->getProviderPayments()
                ]),
                'options' => ['class' => 'table-responsive'],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'label'     => Yii::t('app', 'Date'),
                        'attribute' => 'date'
                    ],
                    [
                        'label'     => Yii::t('app', 'amount'),
                        'value'     => function($model) {
                            return Yii::$app->formatter->asCurrency($model->amount);
                        }
                    ],
                    'description'
                ],
                'options'=>[
                    'style'=>'margin-top:10px;'
                ]
            ]);
            ?>
        </div>
    </div>
</div>
