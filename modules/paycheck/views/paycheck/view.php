<?php

use app\modules\paycheck\models\Paycheck;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\paycheck\models\Paycheck */

$this->title = Yii::t('paycheck', 'Paycheck') . " - " . Yii::t('paycheck', $model->is_own ? 'own' : 'no_own' ). " - " . ($model->is_own ?
        $model->checkbook->moneyBoxAccount->moneyBox->name . " - " . $model->checkbook->moneyBoxAccount->number
        :
        $model->business_name
    );


$this->params['breadcrumbs'][] = ['label' => Yii::t('paycheck', 'Paychecks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="paycheck-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?php
            if(count($model->getPossibleStates()) > 1 && $model->getUpdatable()) {
                echo Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->paycheck_id], ['class' => 'btn btn-primary']);
            }
            ?>
            <?php if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->paycheck_id], [
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

    if($model->is_own) {
        $attributes = [
            [
                'label' => Yii::t('paycheck', 'Money Box'),
                'attribute' => 'checkbook.moneyBoxAccount.moneyBox.name'
            ],
            [
                'label' => Yii::t('paycheck', 'Money Box Account'),
                'attribute' => 'checkbook.moneyBoxAccount.number'
            ],
            [
                'label' => Yii::t('paycheck', 'Checkbook'),
                'attribute' => 'checkbook.name'
            ],
            'date:date',
            'due_date:date',
            'number',
            'amount:currency',
            [
                'label' => Yii::t('app', 'Status'),
                'attribute' => function ($model) {
                    return Yii::t('paycheck', $model->status);
                }
            ],
            'description',
            [
                'label' => Yii::t('app', 'Movement date'),
                'attribute' => function($model) {
                    return (new \DateTime('now'))->setTimestamp($model->timestamp)->format('d/m/Y - H:i');
                }
            ]
        ];


    } else {
        $attributes = [
            'business_name',
            'document_number',
            [
                'label' => Yii::t('paycheck', 'Money Box'),
                'attribute' => 'moneyBox.name'
            ],
            'date:date',
            'due_date:date',
            'number',
            'amount:currency',
            [
                'label' => Yii::t('app', 'Status'),
                'attribute' => function ($model) {
                    return Yii::t('paycheck', $model->status);
                }
            ],
            'description',
            [
                'label' => Yii::t('app', 'Movement date'),
                'attribute' => function($model) {
                    return (new \DateTime('now'))->setTimestamp($model->timestamp)->format('d/m/Y - H:i');
                }
            ]
        ];
    }

    if ($model->status == Paycheck::STATE_DEPOSITED) {
        $attributes[] = [
            'label' => Yii::t('paycheck', 'Deposited in'),
            'attribute' => function($model) {
                return ($model->moneyBoxAccount ? $model->moneyBoxAccount->moneyBox->name . " - " . $model->moneyBoxAccount->number : '') ;
            }
        ];
    }

    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes,
    ]) ;?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= Yii::t('app', 'Movements') ?></h3>
        </div>
        <div class="panel-body">

            <?=GridView::widget([
                'id'=>'grid',
                'dataProvider' => new ActiveDataProvider([
                    'query' => $model->getPaycheckLogs()
                ]),
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'label' => Yii::t("app", "Movement date"),
                        'value' => function($model){
                            return (new \DateTime('now'))->setTimestamp($model->timestamp)->format('d/m/Y - H:i');
                        }
                    ],
                    [
                        'label' => Yii::t("app", "Description"),
                        'attribute' => 'description'
                    ],
                    [
                        'label' => Yii::t("app", "Status"),
                        'value' => function($model){
                            return Yii::t('paycheck', $model->status);
                        }
                    ],
                    [
                        'label' => Yii::t('paycheck', 'Deposited in'),
                        'value' => function($model) {
                            return ($model->moneyBoxAccount ? $model->moneyBoxAccount->moneyBox->name . " - " . $model->moneyBoxAccount->number : '') ;
                        }
                    ]
                ],
                'options'=>[
                    'style'=>'margin-top:10px;'
                ]
            ]);
            ?>
        </div>
    </div>


</div>
