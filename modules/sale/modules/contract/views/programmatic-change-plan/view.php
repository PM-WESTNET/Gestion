<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\sale\modules\contract\models\ProgrammaticChangePlan */

$this->title = $model->programmatic_change_plan_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Programmatic Change Plans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="programmatic-change-plan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->programmatic_change_plan_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->programmatic_change_plan_id], [
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
            'programmatic_change_plan_id',
            'date',
            'applied',
            'created_at',
            'updated_at',
            'contract_id',
            'product_id',
            'user_id',
        ],
    ]) ?>

</div>
