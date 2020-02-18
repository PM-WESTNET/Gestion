<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\modules\contract\models\ProgrammedPlanChange */

$this->title = $model->programmed_plan_change_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Programmatic Change Plans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="programmatic-change-plan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->programmed_plan_change_id], [
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
            'programmed_plan_change_id',
            'date',
            'applied:boolean',
            'created_at',
            'updated_at',
            [
                'attribute' => 'contract_id',
                'value' => function($model) {
                    return $model->contract_id ? "[$model->contract_id] Contrato en ".$model->contract->address->shortAddress : '';
                }
            ],
            [
                'attribute' => 'product_id',
                'value' => function($model) {
                    return $model->product_id ? $model->product->name : '';
                }
            ],
            [
                'attribute' => 'user_id',
                'value' => function($model) {
                    return $model->user_id ? $model->user->username : '';
                }
            ],
        ],
    ]) ?>

</div>
