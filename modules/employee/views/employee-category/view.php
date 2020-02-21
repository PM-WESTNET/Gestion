<?php

use app\components\helpers\UserA;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\employee\models\EmployeeCategory */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Employee Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-category-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= UserA::a('<span class="glyphicon glyphicon-pencil"></span> '.Yii::t('app', 'Update'), ['update', 'id' => $model->employee_category_id], ['class' => 'btn btn-primary']) ?>

        <?php if ($model->getDeletable()):?>
            <?= UserA::a('<span class="glyphicon glyphicon-trash"></span> '. Yii::t('app', 'Delete'), ['delete', 'id' => $model->employee_category_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif;?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'employee_category_id',
            'name',
            [
                'attribute' => 'statusLabel',
                'label' => Yii::t('app', 'Status')
            ],
        ],
    ]) ?>

</div>
