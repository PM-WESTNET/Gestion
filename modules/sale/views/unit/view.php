<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var app\modules\sale\models\Unit $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Units'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="unit-view">

    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-pencil'></span> " . Yii::t('app', 'Update'), ['update', 'id' => $model->unit_id], ['class' => 'btn btn-primary']) ?>
            <?php if($model->deletable) echo Html::a("<span class='glyphicon glyphicon-remove'></span> " . Yii::t('app', 'Delete'), ['delete', 'id' => $model->unit_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'unit_id',
            'name',
            [
                'label'=>$model->getAttributeLabel('type'),
                'value'=>Yii::t('app',$model->type)
            ],
            'symbol',
            [
                'label'=>$model->getAttributeLabel('symbol_position'),
                'value'=>Yii::t('app',$model->symbol_position)
            ],
        ],
    ]) ?>

</div>
