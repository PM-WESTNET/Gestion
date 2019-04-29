<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\ticket\models\Schema */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Schemas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="schema-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->schema_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->schema_id], [
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
            'schema_id',
            'name',
            'class',
            [
                'attribute' => 'statuses',
                'value' => function($model) {
                    $statuses = '';
                    foreach ($model->statuses as $status) {
                        $statuses .= $status->name .', ';
                    }
                    return $statuses;
                }
            ],
        ],
    ]) ?>

</div>
