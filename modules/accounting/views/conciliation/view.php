<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\accounting\models\Conciliation */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('accounting', 'Conciliations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="conciliation-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?php
                echo Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->conciliation_id], ['class' => 'btn btn-primary']);
            ?>
            <?php if($model->deletable) {
                    echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->conciliation_id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                            'method' => 'post',
                        ],
                    ]);
                }
            ?>
            <?php if ($model->status == 'draft'){
                    echo Html::a("<span class='glyphicon glyphicon-indent-right'></span> " .  Yii::t('accounting', 'Make'), ['conciliate', 'id' => $model->conciliation_id], [
                        'class' => 'btn btn-warning',
                    ]);
                }
            ?>
        </p>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => Yii::t('accounting', 'Money Box Account'),
                'attribute' => function ($model) {
                    return $model->moneyBoxAccount->moneyBox->name . " - " . $model->moneyBoxAccount->number;
                }
            ],
            'name',
            'date',
            'date_from',
            'date_to',
            [
                'label' => Yii::t('accounting', 'status'),
                'attribute' => function ($model) {
                    return Yii::t('accounting', ucfirst($model->status));
                }
            ],

        ],
    ]) ?>

</div>
