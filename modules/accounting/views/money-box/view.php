<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\MoneyBox */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('accounting', 'Money Boxes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="money-box-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->money_box_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->money_box_id], [
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
        [
            'attribute' => 'moneyBoxType.name',
            'label'     => Yii::t('accounting', 'Money Box Type')
        ],
        'name',

    ];
    if(Yii::$app->getModule("accounting")) {
        $attributes[] = [
            'label'     => Yii::t('accounting', 'Account'),
            'value' => ($model->account ? $model->account->name : "" )
        ];
    }
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes
    ]) ?>

</div>

<div class="panel panel-primary">
    <div class="panel-heading" data-toggle="collapse" data-target="#panel-body-items" aria-expanded="true" aria-controls="panel-body-items">
        <h3 class="panel-title"><?= Yii::t('accounting', 'Operations Type') ?></h3>
    </div>
    <div class="panel-body collapse in" id="panel-body-items" aria-expanded="true">
        <div class="row" id="form-operation_type-items">
            <?php
            $items = new  \yii\data\ActiveDataProvider(['query'=>$model->getMoneyBoxHasOperationTypes()]);
            echo GridView::widget([
                'id'=>'items',
                'dataProvider' => $items,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'label' => Yii::t("accounting", "Operation Type"),
                        'value' => function($model){
                            return ($model->operationType ? $model->operationType->name : '' );
                        },
                    ],
                    'account.name',
                    [
                        'label' => Yii::t("accounting", "Money Box Account"),
                        'attribute' => 'moneyBoxAccount.number'
                    ],
                    'operationType.is_debit:boolean',
                    'code'
                ]
            ]);
            ?>
        </div>
    </div>
</div>
