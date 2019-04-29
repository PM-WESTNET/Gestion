<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\MoneyBoxAccount */

$this->title = $model->moneyBox->name . " - " . $model->number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('accounting', 'Money Box Accounts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="money-box-account-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->money_box_account_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->money_box_account_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>

    <?php
    $columns = [];

    if (Yii::$app->params['companies']['enabled']) {
        $columns[] = [
            'attribute' => 'company.name',
            'label'     => Yii::t('app', 'Company')
        ];
    }

    $columns = array_merge($columns,[
            [
                'attribute' => 'moneyBox.name',
                'label'     =>  Yii::t('accounting', 'Money Box'),
            ],
            'number',
            [
                'attribute' => 'currency.name',
                'label'     =>  Yii::t('app', 'Currency'),
            ]
        ]
    );

    if (Yii::$app->getModule("accounting")) {
        $columns[] = [
            'attribute' => 'account.name',
            'label'     => Yii::t('accounting', 'Account')
        ];
    }

    $columns[] = 'enable:boolean';
    $columns[] = 'small_box:boolean';
    $columns[] = [
        'attribute' => 'type',
        'value' => function ($model) {
            return Yii::t('app', ucfirst($model->type));
        },
    ];

    echo DetailView::widget([
        'model' => $model,
        'attributes' => $columns,
    ]) ?>

</div>
