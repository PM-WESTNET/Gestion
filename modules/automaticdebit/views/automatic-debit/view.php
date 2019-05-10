<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\automaticdebit\models\AutomaticDebit */

$this->title = Yii::t('app','Automatic Debit'). ': '.$model->customer->fullName;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Automatic Debits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="automatic-debit-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->automatic_debit_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->automatic_debit_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'customer_id',
                'value' => function ($model) {
                    return $model->customer->fullName;
                }
            ],
            [
                'attribute' => 'bank_id',
                'value' => function ($model) {
                    return $model->bank->name;
                }
            ],
            'cbu',
            'beneficiario_number',
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return $model->getStatusLabel();
                }
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
