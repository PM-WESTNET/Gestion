<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\AccountingPeriod */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('accounting', 'Accounting Periods'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="accounting-period-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->accounting_period_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->accounting_period_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>

            <?php if($model->status == \app\modules\accounting\models\AccountingPeriod::STATE_OPEN) {
                echo Html::a("<span class='glyphicon glyphicon-lock'></span> " . Yii::t('app', 'Finish'), ['close', 'id' => $model->accounting_period_id], [
                    'class' => 'btn btn-warning',
                    'data' => [
                        'confirm' => Yii::t('accounting', 'Are you sure you want to finish this accounting period?'),
                        'method' => 'post',
                    ],
                ]);
            } ?>
        </p>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'number',
            'date_from',
            'date_to',
            [
                'label' => Yii::t('app', 'Status'),
                'attribute' => function ($model) {
                    return Yii::t('accounting', ucfirst($model->status));
                }
            ],
        ],
    ]) ?>

</div>
