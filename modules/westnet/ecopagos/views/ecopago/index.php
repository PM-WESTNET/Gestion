<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Ecopagos');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ecopago-index">
    <div class="title">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a("<span class='glyphicon glyphicon-plus'></span> " . Yii::t('app', 'Create {modelClass}', [
        'modelClass' => 'Ecopago',
    ]), 
            ['create'], 
            ['class' => 'btn btn-success']) 
            ;?>
        </p>
    </div>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'ecopago_id',
            'name',
            'address_id',
            [
                'attribute' => 'status',
                'value' => function($model) {
                    if ($model->status) {
                        return Yii::t('app', $model->status->name);
                    }
                }
            ],
            'create_datetime:datetime',
            'update_datetime:datetime',
            // 'description:ntext',
            // 'limit',
            // 'number',
            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]);
    ?>

</div>
